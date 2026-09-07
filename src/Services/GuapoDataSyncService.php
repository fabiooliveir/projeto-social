<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EducationDashboardPayload;

/**
 * Orquestra a coleta dos endpoints do IBGE, o parsing e os cálculos de
 * déficit/cobertura da educação infantil de Guapó-GO.
 */
final class GuapoDataSyncService
{
    public const URL_CENSO_2022 = 'https://servicodados.ibge.gov.br/api/v3/agregados/9514/periodos/2022/variaveis/93?localidades=N6[5209200]&classificacao=2[6794]|287[6557,6558,6559,6560,6561,6562,6563,6564,6565,6566,6567,6568,6569,6570,6571,6572,6573,6574]';
    public const URL_CRECHE_MUNICIPAL = 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/77883/resultados/5209200';
    public const URL_PRE_ESCOLA_MUNICIPAL = 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/5904/resultados/5209200';
    public const URL_1_ANO_FUNDAMENTAL = 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/77899/resultados/5209200';
    public const URL_EF_TOTAL = 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/5908/resultados/5209200';

    private const CATEGORIA_CRECHE = [6557, 6558, 6559, 6560];
    private const CATEGORIA_PRE_ESCOLA = [6561, 6562];
    private const CATEGORIA_FUNDAMENTAL_1 = [6563, 6564, 6565, 6566, 6567];
    private const CATEGORIA_FUNDAMENTAL_2 = [6568, 6569, 6570, 6571];
    private const CATEGORIA_MEDIO = [6572, 6573, 6574];

    private const ROTULO_IDADE = [
        6557 => 'Menos de 1 ano',
        6558 => '1 ano',
        6559 => '2 anos',
        6560 => '3 anos',
        6561 => '4 anos',
        6562 => '5 anos',
        6563 => '6 anos',
        6564 => '7 anos',
        6565 => '8 anos',
        6566 => '9 anos',
        6567 => '10 anos',
        6568 => '11 anos',
        6569 => '12 anos',
        6570 => '13 anos',
        6571 => '14 anos',
        6572 => '15 anos',
        6573 => '16 anos',
        6574 => '17 anos',
    ];

    private const ANO_MATRICULA_ATUAL = 2025;

    public function __construct(
        private readonly IbgeApiClient $client,
        private readonly string $outputFile = __DIR__ . '/../../storage/data/guapo_education_cache.json',
    ) {
    }

    /**
     * Executa a coleta completa e persiste o cache consolidado.
     */
    public function sync(): EducationDashboardPayload
    {
        $start = microtime(true);

        $this->log('[IBGE] Coletando Censo 2022 (Tabela 9514 - Pirâmide Etária)...', 'server');
        $censo = $this->client->fetchJson(self::URL_CENSO_2022, 'censo2022');
        $this->logOk();

        $this->log('[INEP] Coletando Censo Escolar (Creche Municipal - 77883)...', 'server');
        $creche = $this->client->fetchJson(self::URL_CRECHE_MUNICIPAL, 'creche_municipal');
        $this->logOk();

        $this->log('[INEP] Coletando Censo Escolar (Pré-escola Municipal - 5904)...', 'server');
        $preEscola = $this->client->fetchJson(self::URL_PRE_ESCOLA_MUNICIPAL, 'pre_escola_municipal');
        $this->logOk();

        $this->log('[INEP] Coletando Censo Escolar (1º ano Ensino Fundamental - 77899)...', 'server');
        $fund1Ano = $this->client->fetchJson(self::URL_1_ANO_FUNDAMENTAL, 'fundamental_1_ano');
        $this->logOk();

        $this->log('[INEP] Coletando Censo Escolar (Ensino Fundamental total - 5908)...', 'server');
        $efTotal = $this->client->fetchJson(self::URL_EF_TOTAL, 'fundamental_total');
        $this->logOk();

        $this->log('[CALC] Processando déficit, metas do PNE e transição...', 'server');
        $piramide = $this->parseCenso2022($censo);
        $crecheSerie = $this->parseSeriePesquisa13($creche);
        $preEscolaSerie = $this->parseSeriePesquisa13($preEscola);
        $fund1AnoSerie = $this->parseSeriePesquisa13($fund1Ano);
        $efTotalSerie = $this->parseSeriePesquisa13($efTotal);
        $resumo = $this->calcularResumo($piramide, $crecheSerie, $preEscolaSerie, $fund1AnoSerie);
        $this->logOk();

        $payload = new EducationDashboardPayload(
            municipio: ['codigo_ibge' => '5209200', 'nome' => 'Guapó', 'uf' => 'GO'],
            atualizadoEm: (new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')))->format(DATE_ATOM),
            tempoExecucaoMs: (int) round((microtime(true) - $start) * 1000),
            resumoExecutivo: $resumo,
            piramideEtaria: $piramide,
            seriesHistoricas: [
                'creche_municipal'    => array_filter($crecheSerie, fn ($ano) => in_array($ano, [2008, 2013, 2018, 2022, 2025], true), ARRAY_FILTER_USE_KEY),
                'pre_escola_municipal' => array_filter($preEscolaSerie, fn ($ano) => in_array($ano, [2008, 2015, 2022, 2025], true), ARRAY_FILTER_USE_KEY),
                'fundamental_1_ano'    => array_filter($fund1AnoSerie, fn ($ano) => in_array($ano, [2008, 2013, 2018, 2022, 2025], true), ARRAY_FILTER_USE_KEY),
                'fundamental_total'    => array_filter($efTotalSerie, fn ($ano) => in_array($ano, [2008, 2013, 2018, 2022, 2025], true), ARRAY_FILTER_USE_KEY),
            ],
        );

        $this->persist($payload);
        $this->log('[SAVE] Cache gravado em storage/data/guapo_education_cache.json');

        return $payload;
    }

    /**
     * Converte a resposta da API v3 (Agregados 9514) em lista por idade.
     *
     * @return array<int, array{idade: string, populacao: int}>
     */
    public function parseCenso2022(array $data): array
    {
        $porCategoria = [];

        foreach ($data as $item) {
            foreach ($item['resultados'] ?? [] as $resultado) {
                $categoriaIdade = $this->extractCategoriaIdade($resultado);

                foreach ($resultado['series'] ?? [] as $serie) {
                    foreach ($serie['serie'] ?? [] as $valor) {
                        $porCategoria[$categoriaIdade] = (int) $valor;
                    }
                }
            }
        }

        $piramide = [];
        foreach (self::ROTULO_IDADE as $codigo => $rotulo) {
            $piramide[] = [
                'idade'     => $rotulo,
                'populacao' => $porCategoria[$codigo] ?? 0,
            ];
        }

        return $piramide;
    }

    private function extractCategoriaIdade(array $resultado): ?int
    {
        foreach ($resultado['classificacoes'] ?? [] as $classificacao) {
            if (($classificacao['id'] ?? null) === '287') {
                foreach ($classificacao['categoria'] ?? [] as $codigo => $_rotulo) {
                    return (int) $codigo;
                }
            }
        }

        return null;
    }

    /**
     * Converte a resposta da Pesquisa 13 em série ano => valor.
     *
     * @return array<string, int>
     */
    public function parseSeriePesquisa13(array $data): array
    {
        $serie = [];

        foreach ($data as $item) {
            foreach ($item['res'] ?? [] as $res) {
                foreach ($res['res'] ?? [] as $ano => $valor) {
                    $serie[(string) $ano] = $this->normalize($valor);
                }
            }
        }

        ksort($serie);

        return $serie;
    }

    /**
     * Calcula os indicadores de déficit, cobertura e transição 0-17 anos.
     *
     * @param array<int, array{idade: string, populacao: int}> $piramide
     * @param array<string, int> $crecheSerie
     * @param array<string, int> $preEscolaSerie
     * @param array<string, int> $fund1AnoSerie
     * @return array<string, int|float>
     */
    public function calcularResumo(array $piramide, array $crecheSerie, array $preEscolaSerie, array $fund1AnoSerie = []): array
    {
        $popCreche = 0;
        $popPreEscola = 0;
        $popFund1 = 0;
        $popFund2 = 0;
        $popMedio = 0;
        $popTotal = 0;
        $pop5Anos = 0;
        $popContraturno = 0;

        foreach ($piramide as $e) {
            $codigo = $this->codigoDoRotulo($e['idade']);
            $popTotal += $e['populacao'];

            if (in_array($codigo, self::CATEGORIA_CRECHE, true)) {
                $popCreche += $e['populacao'];
            } elseif (in_array($codigo, self::CATEGORIA_PRE_ESCOLA, true)) {
                $popPreEscola += $e['populacao'];
            } elseif (in_array($codigo, self::CATEGORIA_FUNDAMENTAL_1, true)) {
                $popFund1 += $e['populacao'];
            } elseif (in_array($codigo, self::CATEGORIA_FUNDAMENTAL_2, true)) {
                $popFund2 += $e['populacao'];
            } elseif (in_array($codigo, self::CATEGORIA_MEDIO, true)) {
                $popMedio += $e['populacao'];
            }

            if ($codigo === 6562) {
                $pop5Anos = $e['populacao'];
            }
        }

        $vagasCreche = $crecheSerie[(string) self::ANO_MATRICULA_ATUAL] ?? 0;
        $vagasPreEscola = $preEscolaSerie[(string) self::ANO_MATRICULA_ATUAL] ?? 0;
        $matriculas1AnoFund = $fund1AnoSerie[(string) self::ANO_MATRICULA_ATUAL] ?? 0;

        $deficit = $popCreche - $vagasCreche;
        $popContraturno = $popFund1 + $popFund2;
        $taxaDesatendimento = $popCreche > 0 ? round($deficit / $popCreche * 100, 2) : 0.0;
        $metaPne = (int) round($popCreche * 0.50);
        $gapPne = $metaPne - $vagasCreche;
        $coberturaPreEscola = $popPreEscola > 0 ? round($vagasPreEscola / $popPreEscola * 100, 2) : 0.0;
        $taxaTransicao = $pop5Anos > 0 ? round($matriculas1AnoFund / $pop5Anos * 100, 2) : 0.0;

        return [
            'populacao_0a3_anos'               => $popCreche,
            'vagas_creche_atual_2025'          => $vagasCreche,
            'deficit_vagas_creche'             => $deficit,
            'taxa_desatendimento_creche_pct'   => $taxaDesatendimento,
            'meta_pne_minima_50pct'            => $metaPne,
            'vagas_faltantes_para_pne'         => $gapPne,
            'populacao_4a5_anos'               => $popPreEscola,
            'vagas_pre_escola_atual_2025'      => $vagasPreEscola,
            'taxa_cobertura_pre_escola_pct'    => $coberturaPreEscola,
            'populacao_fundamental_1_6a10'     => $popFund1,
            'populacao_fundamental_2_11a14'    => $popFund2,
            'populacao_contraturno_6a14'       => $popContraturno,
            'populacao_medio_15a17'            => $popMedio,
            'populacao_total_escolar_0a17'     => $popTotal,
            'matriculas_1ano_fundamental_2025' => $matriculas1AnoFund,
            'taxa_transicao_pre_fundamental_pct' => $taxaTransicao,
        ];
    }

    private function codigoDoRotulo(string $rotulo): int
    {
        foreach (self::ROTULO_IDADE as $codigo => $r) {
            if ($r === $rotulo) {
                return $codigo;
            }
        }

        return 0;
    }

    private function normalize(mixed $valor): int
    {
        $valor = trim((string) $valor);

        return $valor === '' || $valor === '-' ? 0 : (int) $valor;
    }

    private function persist(EducationDashboardPayload $payload): void
    {
        $dir = dirname($this->outputFile);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new \RuntimeException("Não foi possível criar o diretório {$dir}");
        }

        file_put_contents(
            $this->outputFile,
            json_encode($payload->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function log(string $mensagem, string $tipo = 'server'): void
    {
        if (PHP_SAPI === 'cli') {
            echo $mensagem;
        }
    }

    private function logOk(): void
    {
        if (PHP_SAPI === 'cli') {
            echo " [OK]\n";
        }
    }
}