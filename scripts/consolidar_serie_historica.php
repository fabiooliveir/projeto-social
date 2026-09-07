<?php

declare(strict_types=1);

/**
 * Consolida a série histórica (2008-2025) do Censo Escolar INEP em Guapó-GO
 * via API IBGE Pesquisa 13.
 *
 * Endpoint base: https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/{id}/resultados/5209200
 */

define('BASE_URL', 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/%d/resultados/5209200');
define('MUNICIPIO_IBGE', '5209200');

define('INDICADORES', [
    'matriculas_creche_municipal'    => 77883,
    'matriculas_creche_privada'      => 77886,
    'matriculas_pre_escola_municipal'=> 5904,
    'matriculas_pre_escola_privada'  => 5907,
    'escolas_creche_municipal'       => 77895,
    'escolas_pre_escola_municipal'   => 5946,
]);

const ANOS = ['2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'];

// Base do Censo Demográfico 2022 (Issue #7)
const POP_CRECHE_0A3 = 1068;
const POP_PRE_ESCOLA_4A5 = 553;

const DIR_RAW = __DIR__ . '/../data/raw';
const DIR_PROCESSED = __DIR__ . '/../data/processed';
const RAW_FILE = DIR_RAW . '/ibge_pesquisa13_censo_escolar_guapo.json';

define('CMEIS', [
    [
        'nome'      => 'CMEI Geralda Rodrigues Novantino',
        'descricao' => 'Centro Municipal de Educação Infantil',
        'etapas'    => ['Creche', 'Pré-escola'],
    ],
    [
        'nome'      => 'CMEI Floripes Maria de Jesus',
        'descricao' => 'Centro Municipal de Educação Infantil',
        'etapas'    => ['Creche', 'Pré-escola'],
    ],
]);

/**
 * Busca um indicador na Pesquisa 13 do IBGE.
 */
function fetchIndicador(int $id): array
{
    $url = sprintf(BASE_URL, $id);
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 30,
            'header'  => "Accept: application/json\r\n",
        ],
    ]);

    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        fwrite(STDERR, "ERRO: Falha ao consultar indicador {$id}.\n");
        exit(1);
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        fwrite(STDERR, "ERRO: Resposta inválida para indicador {$id}.\n");
        exit(1);
    }

    return $data;
}

/**
 * Normaliza valor numérico, tratando hífen ("-") como zero.
 */
function normalizeValue($value): float
{
    $value = trim((string) $value);
    if ($value === '' || $value === '-') {
        return 0;
    }
    return (float) str_replace(',', '.', $value);
}

/**
 * Salva payload bruto consolidado.
 */
function saveRaw(array $payload): void
{
    if (!is_dir(DIR_RAW)) {
        mkdir(DIR_RAW, 0755, true);
    }
    file_put_contents(RAW_FILE, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

/**
 * Extrai feijão de valores por ano de um indicador.
 */
function seriesDoIndicador(array $payload, int $id): array
{
    $series = [];
    foreach ($payload as $entry) {
        if ((int) $entry['id'] !== $id) {
            continue;
        }
        foreach ($entry['res'] ?? [] as $res) {
            foreach (ANOS as $ano) {
                $series[$ano] = normalizeValue($res['res'][$ano] ?? 0);
            }
        }
    }
    return $series;
}

/**
 * Monta a série anual consolidada.
 */
function buildSerieAnual(array $payload): array
{
    $indicadores = [];
    foreach (INDICADORES as $chave => $id) {
        $indicadores[$chave] = seriesDoIndicador($payload, $id);
    }

    $serie = [];
    foreach (ANOS as $ano) {
        $creche = $indicadores['matriculas_creche_municipal'][$ano]
                + $indicadores['matriculas_creche_privada'][$ano];
        $preEscola = $indicadores['matriculas_pre_escola_municipal'][$ano]
                   + $indicadores['matriculas_pre_escola_privada'][$ano];

        $serie[] = [
            'ano'                          => (int) $ano,
            'creche_municipal'             => $indicadores['matriculas_creche_municipal'][$ano],
            'creche_privada'               => $indicadores['matriculas_creche_privada'][$ano],
            'creche_total'                 => $creche,
            'pre_escola_municipal'         => $indicadores['matriculas_pre_escola_municipal'][$ano],
            'pre_escola_privada'           => $indicadores['matriculas_pre_escola_privada'][$ano],
            'pre_escola_total'             => $preEscola,
            'escolas_creche_municipal'     => $indicadores['escolas_creche_municipal'][$ano],
            'escolas_pre_escola_municipal' => $indicadores['escolas_pre_escola_municipal'][$ano],
        ];
    }

    return $serie;
}

/**
 * Gera o diagnóstico de déficit 2025 frente ao Censo 2022.
 */
function diagnosticoDeficit(array $serie): array
{
    $anoAtual = (int) MAX_ANO;
    $latest = $serie[count($serie) - 1];

    // Cobertura pré-escola: 553 (pop) x 514 (matrículas) ≈ 92.95%
    $coberturaPreEscola = round($latest['pre_escola_total'] / POP_PRE_ESCOLA_4A5 * 100, 1);

    $vagasCreche = $latest['creche_municipal']; // rede pública municipal

    return [
        'ano_referencia'            => $anoAtual,
        'populacao_creche_0a3'      => POP_CRECHE_0A3,
        'populacao_pre_escola_4a5'  => POP_PRE_ESCOLA_4A5,
        'vagas_creche_publicas'     => $vagasCreche,
        'deficit_absoluto_creche'   => POP_CRECHE_0A3 - $vagasCreche,
        'taxa_desassistencia'       => round((POP_CRECHE_0A3 - $vagasCreche) / POP_CRECHE_0A3 * 100, 2),
        'meta_pne_1_minimo_vagas'   => (int) ceil(POP_CRECHE_0A3 * 0.50),
        'deficit_legal_meta_1'      => max(0, (int) ceil(POP_CRECHE_0A3 * 0.50) - $vagasCreche),
        'cobertura_pre_escola_pct'  => $coberturaPreEscola,
        'pre_escola_matriculas'     => $latest['pre_escola_total'],
        'observacao'                => 'Meta 1 do PNE (Lei 13.005/2014): ampliação do atendimento em creche para, no mínimo, 50% da população de 0 a 3 anos.',
    ];
}

/**
 * Gera CSV tabular.
 */
function exportCsv(array $serie): void
{
    $caminho = DIR_PROCESSED . '/serie_historica_censo_escolar_guapo.csv';

    $handle = fopen($caminho, 'w');
    if ($handle === false) {
        fwrite(STDERR, "ERRO: Não foi possível criar o CSV.\n");
        exit(1);
    }

    fputcsv($handle, [
        'ano',
        'creche_municipal',
        'creche_privada',
        'creche_total',
        'pre_escola_municipal',
        'pre_escola_privada',
        'pre_escola_total',
        'escolas_creche_municipal',
        'escolas_pre_escola_municipal',
    ], separator: ';', escape: '');

    foreach ($serie as $linha) {
        fputcsv($handle, $linha, separator: ';', escape: '');
    }

    fclose($handle);
}

// --- Main ---
echo "Consolidando série histórica do Censo Escolar INEP (Guapó-GO)...\n";

$payload = [];
foreach (INDICADORES as $chave => $id) {
    echo "  → indicador {$id} ({$chave})\n";
    $payload = array_merge($payload, fetchIndicador($id));
}

define('MAX_ANO', '2025');
if (!is_dir(DIR_PROCESSED)) {
    mkdir(DIR_PROCESSED, 0755, true);
}

saveRaw($payload);
echo "✓ Payload bruto salvo em: " . RAW_FILE . "\n";

$serie = buildSerieAnual($payload);
$diagnostico = diagnosticoDeficit($serie);

$resultado = [
    'metadata' => [
        'fonte'           => 'INEP - Censo Escolar / IBGE - Pesquisa 13',
        'municipio'       => 'Guapó (GO)',
        'codigo_ibge'     => MUNICIPIO_IBGE,
        'periodo'         => '2008-2025',
        'data_extracao'   => (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format(DateTimeInterface::ISO8601),
        'api'             => 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores',
        'indicadores_uso' => INDICADORES,
    ],
    'serie_anual'              => $serie,
    'escolas_mapeadas'         => CMEIS,
    'diagnostico_deficit_2025' => $diagnostico,
];

$jsonPath = DIR_PROCESSED . '/serie_historica_censo_escolar_guapo.json';
file_put_contents($jsonPath, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "✓ JSON processado salvo em: {$jsonPath}\n";

exportCsv($serie);
echo "✓ CSV exportado em: " . DIR_PROCESSED . "/serie_historica_censo_escolar_guapo.csv\n";

echo "\n=== Diagnóstico 2025 ===\n";
echo "Matrículas creche pública:  {$diagnostico['vagas_creche_publicas']}\n";
echo "Déficit absoluto:           {$diagnostico['deficit_absoluto_creche']} crianças ({$diagnostico['taxa_desassistencia']}%)\n";
echo "Meta 1 PNE (50%):           {$diagnostico['meta_pne_1_minimo_vagas']} vagas mínimas\n";
echo "Déficit legal Meta 1:       {$diagnostico['deficit_legal_meta_1']} novas vagas necessárias\n";
echo "Pré-escola:                 {$diagnostico['pre_escola_matriculas']} matrículas ({$diagnostico['cobertura_pre_escola_pct']}%)\n";