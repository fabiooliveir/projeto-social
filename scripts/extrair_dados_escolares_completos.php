<?php

declare(strict_types=1);

/**
 * Extrai e consolida o diagnóstico demográfico (0-17 anos) e escolar
 * (Ensino Fundamental e Médio) de Guapó-GO via APIs oficiais.
 *
 * Fontes:
 *  1. IBGE SIDRA – Tabela 9514 (Censo 2022), códigos de idade 6557 a 6574.
 *  2. IBGE Pesquisa 13 / INEP Censo Escolar – indicadores de matrículas do
 *     Ensino Fundamental (1º ao 9º ano) e do Ensino Médio (2008-2025).
 *
 * Sub-issue 1.4 - Expansão do diagnóstico para 6 a 17 anos.
 */

const API_SIDRA_VALUES = 'https://apisidra.ibge.gov.br/values/t/9514/n6/5209200/v/allxp/p/last%201/c2/6794/c287/6557,6558,6559,6560,6561,6562,6563,6564,6565,6566,6567,6568,6569,6570,6571,6572,6573,6574';

const BASE_INEP = 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/%d/resultados/5209200';
const MUNICIPIO_IBGE = '5209200';

const DIR_RAW = __DIR__ . '/../data/raw';
const DIR_PROCESSED = __DIR__ . '/../data/processed';
const RAW_CENSO = DIR_RAW . '/ibge_censo2022_9514_0a17_guapo.json';
const RAW_INEP = DIR_RAW . '/ibge_pesquisa13_fundamental_medio.json';
const OUT_JSON = DIR_PROCESSED . '/demografia_escolar_0a17_guapo.json';
const OUT_CSV = DIR_PROCESSED . '/demografia_escolar_0a17_guapo.csv';

/**
 * Código SIDRA => rótulo da idade.
 */
const MAPPING = [
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

const CICLOS = [
    'creche'           => ['faixa' => '0-3',   'codigos' => [6557, 6558, 6559, 6560], 'etapa' => 'Creche'],
    'pre_escola'       => ['faixa' => '4-5',   'codigos' => [6561, 6562],              'etapa' => 'Pré-escola'],
    'fundamental_1'    => ['faixa' => '6-10',  'codigos' => [6563, 6564, 6565, 6566, 6567], 'etapa' => 'Ensino Fundamental (Anos Iniciais)'],
    'fundamental_2'    => ['faixa' => '11-14', 'codigos' => [6568, 6569, 6570, 6571],  'etapa' => 'Ensino Fundamental (Anos Finais)'],
    'medio'            => ['faixa' => '15-17', 'codigos' => [6572, 6573, 6574],        'etapa' => 'Ensino Médio'],
];

const INDICADORES_INEP = [
    'fundamental_total'          => 5908,
    'medio_total'                => 5913,
    'fundamental_1o_ano'         => 77899,
    'fundamental_2o_ano'         => 77900,
    'fundamental_3o_ano'         => 77901,
    'fundamental_4o_ano'         => 77902,
    'fundamental_5o_ano'         => 77903,
    'fundamental_6o_ano'         => 77904,
    'fundamental_7o_ano'         => 77905,
    'fundamental_8o_ano'         => 77906,
    'fundamental_9o_ano'         => 77907,
    'fundamental_1o_ano_municipal' => 77941,
    'fundamental_1o_ano_estadual'  => 77942,
    'fundamental_1o_ano_privado'   => 77944,
];

const ANOS = ['2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'];
const ANO_MATRICULA = '2025';

/**
 * Busca JSON de uma URL com timeout e Accept json.
 */
function fetchJson(string $url): array|false
{
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 30,
            'header'  => "Accept: application/json\r\n",
        ],
    ]);

    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        return false;
    }

    $data = json_decode($raw, true);

    return is_array($data) ? $data : false;
}

/**
 * Normaliza valor numérico ("-", vazio e null viram 0).
 */
function normalizeValue(mixed $value): int
{
    $value = trim((string) $value);
    if ($value === '' || $value === '-') {
        return 0;
    }
    return (int) round((float) str_replace(',', '.', $value));
}

/**
 * Busca um indicador da Pesquisa 13 do IBGE (matrículas).
 */
function fetchIndicador(int $id): array
{
    $data = fetchJson(sprintf(BASE_INEP, $id));
    if ($data === false) {
        fwrite(STDERR, "ERRO: Falha ao consultar indicador {$id}.\n");
        exit(1);
    }
    return $data;
}

/**
 * Série ano => valor de um indicador.
 *
 * @return array<string, int>
 */
function serieIndicador(array $payload, int $id): array
{
    $serie = [];
    foreach ($payload as $entry) {
        if ((int) $entry['id'] !== $id) {
            continue;
        }
        foreach ($entry['res'] ?? [] as $res) {
            foreach (ANOS as $ano) {
                $serie[$ano] = normalizeValue($res['res'][$ano] ?? 0);
            }
        }
    }
    return $serie;
}

/**
 * Preenche a lista de idades e os totais por ciclo a partir da resposta SIDRA.
 */
function processCenso(array $rows): array
{
    $populacao = [];
    $total = 0;

    foreach (array_slice($rows, 1) as $row) {
        $codigoIdade = (int) ($row['D5C'] ?? 0);
        if (!isset(MAPPING[$codigoIdade])) {
            continue;
        }

        $pop = (int) $row['V'];
        $total += $pop;

        $populacao[$codigoIdade] = [
            'codigo_sidra' => (string) $codigoIdade,
            'idade'        => MAPPING[$codigoIdade],
            'populacao'    => $pop,
        ];
    }

    ksort($populacao);

    $ciclos = [];
    foreach (CICLOS as $chave => $def) {
        $ciclos[$chave] = [
            'etapa'      => $def['etapa'],
            'faixa'      => $def['faixa'],
            'codigos'    => $def['codigos'],
            'total'      => 0,
            'percentual_0a17' => 0.0,
        ];
        foreach ($def['codigos'] as $codigo) {
            $ciclos[$chave]['total'] += $populacao[$codigo]['populacao'] ?? 0;
        }
        $ciclos[$chave]['percentual_0a17'] = $total > 0 ? round($ciclos[$chave]['total'] / $total * 100, 2) : 0.0;
    }

    $porIdade = [];
    foreach ($populacao as $item) {
        $faixa = idadeParaCiclo((int) $item['codigo_sidra']);
        $item['faixa_etaria'] = CICLOS[$faixa]['faixa'];
        $item['etapa']        = CICLOS[$faixa]['etapa'];
        $porIdade[] = $item;
    }

    $ciclos['total_0a17'] = [
        'etapa'   => 'População em idade escolar',
        'faixa'   => '0-17',
        'total'   => $total,
        'percentual_0a17' => 100.0,
    ];

    return [
        'populacao_por_idade' => $porIdade,
        'ciclos'              => $ciclos,
    ];
}

function idadeParaCiclo(int $codigo): string
{
    foreach (CICLOS as $chave => $def) {
        if (in_array($codigo, $def['codigos'], true)) {
            return $chave;
        }
    }
    return 'total_0a17';
}

/**
 * Consolida as séries de matrícula de EF/EM e calcula a transição.
 */
function processInep(array $payload, array $censo): array
{
    $series = [];
    foreach (INDICADORES_INEP as $chave => $id) {
        $series[$chave] = serieIndicador($payload, $id);
    }

    $matriculas2025 = [
        'fundamental_total'    => $series['fundamental_total'][ANO_MATRICULA] ?? 0,
        'fundamental_1o_5o'    => array_sum(array_map(fn ($k) => $series[$k][ANO_MATRICULA] ?? 0, ['fundamental_1o_ano', 'fundamental_2o_ano', 'fundamental_3o_ano', 'fundamental_4o_ano', 'fundamental_5o_ano'])),
        'fundamental_6o_9o'    => array_sum(array_map(fn ($k) => $series[$k][ANO_MATRICULA] ?? 0, ['fundamental_6o_ano', 'fundamental_7o_ano', 'fundamental_8o_ano', 'fundamental_9o_ano'])),
        'medio_total'          => $series['medio_total'][ANO_MATRICULA] ?? 0,
        'fundamental_1o_ano'   => $series['fundamental_1o_ano'][ANO_MATRICULA] ?? 0,
        'dependencia_1o_ano'   => [
            'municipal' => $series['fundamental_1o_ano_municipal'][ANO_MATRICULA] ?? 0,
            'estadual'  => $series['fundamental_1o_ano_estadual'][ANO_MATRICULA] ?? 0,
            'privado'   => $series['fundamental_1o_ano_privado'][ANO_MATRICULA] ?? 0,
        ],
    ];

    $pop5 = idadePopulacao($censo, 6562);

    $transicao = [
        'populacao_5_anos_censo_2022'          => $pop5,
        'matriculas_1o_ano_fundamental_2025'   => $matriculas2025['fundamental_1o_ano'],
        'taxa_transicao_pct'                   => $pop5 > 0 ? round($matriculas2025['fundamental_1o_ano'] / $pop5 * 100, 2) : 0.0,
        'observacao'                           => 'Relação entre as crianças de 5 anos (Censo 2022) e as matrículas no 1º ano do Ensino Fundamental (Censo Escolar 2025) – acima de 100% indica absorção também de crianças de outras idades ou entrada antecipada.',
    ];

    return [
        'series_historicas' => $series,
        'matriculas_2025'   => $matriculas2025,
        'transicao'         => $transicao,
    ];
}

function idadePopulacao(array $censo, int $codigo): int
{
    foreach ($censo['populacao_por_idade'] as $item) {
        if ((int) $item['codigo_sidra'] === $codigo) {
            return (int) $item['populacao'];
        }
    }
    return 0;
}

/**
 * Monta avaliação de contraturno e tempo integral (análise qualitativa).
 */
function contraturno(array $censo): array
{
    $criancas6a14 = $censo['ciclos']['fundamental_1']['total'] + $censo['ciclos']['fundamental_2']['total'];

    return [
        'criancas_adolescentes_6a14' => $criancas6a14,
        'jovens_15a17'               => $censo['ciclos']['medio']['total'],
        'oferta_tempo_integral_publica' => 'Sem indicador específico de matrícula em tempo integral para o município na API IBGE Pesquisa 13 (Censo Escolar)',
        'demanda_estimada_contraturno'  => $criancas6a14,
        'observacao' => 'Grande parte das matrículas de Guapó ocorre em regime de meio período. O auditório multiuso da nova escola é planejado como polo de reforço escolar, oficinas de artes, robótica, música e esporte no contraturno para crianças e adolescentes de 6 a 14 anos, ampliando o impacto social do investimento público.',
    ];
}

/**
 * Exporta CSV com uma linha por idade (Censo 2022 × matrículas Censo Escolar 2025).
 */
function exportCsv(array $censo, array $inep): void
{
    $handle = fopen(OUT_CSV, 'w');
    if ($handle === false) {
        fwrite(STDERR, "ERRO: Não foi possível criar o CSV.\n");
        exit(1);
    }

    fputcsv($handle, [
        'codigo_sidra',
        'idade',
        'etapa',
        'faixa_etaria',
        'populacao_censo_2022',
        'matriculas_censo_escolar_2025',
        'observacao',
    ], separator: ';', escape: '');

    foreach ($censo['populacao_por_idade'] as $item) {
        $faixa = $item['faixa_etaria'];
        $matriculas = match ($faixa) {
            '0-3'    => $inep['matriculas_2025']['creche_referencia'],
            '4-5'    => $inep['matriculas_2025']['pre_escola_referencia'],
            '6-10'   => $inep['matriculas_2025']['fundamental_1o_5o'],
            '11-14'  => $inep['matriculas_2025']['fundamental_6o_9o'],
            '15-17'  => $inep['matriculas_2025']['medio_total'],
            default  => 0,
        };

        fputcsv($handle, [
            $item['codigo_sidra'],
            $item['idade'],
            $item['etapa'],
            $faixa,
            $item['populacao'],
            $matriculas,
            $matriculas > 0 ? 'matrículas agregadas da faixa (etapa) no Censo Escolar 2025' : '',
        ], separator: ';', escape: '');
    }

    fclose($handle);
}

// --- Main ---
echo "Extraindo diagnóstico demográfico e escolar completo (0-17 anos) de Guapó-GO...\n";

if (!is_dir(DIR_RAW)) {
    mkdir(DIR_RAW, 0755, true);
}
if (!is_dir(DIR_PROCESSED)) {
    mkdir(DIR_PROCESSED, 0755, true);
}

echo "  [1/4] Censo Demográfico 2022 (SIDRA 9514 - 0 a 17 anos)...\n";
$rawCenso = fetchJson(API_SIDRA_VALUES);
if ($rawCenso === false) {
    fwrite(STDERR, "ERRO: Falha ao consumir API SIDRA (Tabela 9514).\n");
    exit(1);
}
file_put_contents(RAW_CENSO, json_encode($rawCenso, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  ✓ Bruto salvo em: " . RAW_CENSO . "\n";

echo "  [2/4] Censo Escolar (Pesquisa 13 - EF e EM)...\n";
$rawInep = [];
foreach (INDICADORES_INEP as $chave => $id) {
    echo "      → indicador {$id} ({$chave})\n";
    $rawInep = array_merge($rawInep, fetchIndicador($id));
}
file_put_contents(RAW_INEP, json_encode($rawInep, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  ✓ Bruto salvo em: " . RAW_INEP . "\n";

echo "  [3/4] Consolidando ciclos, séries e transição...\n";
$censo = processCenso($rawCenso);

$refCreche = 289;    // Issue #8 - creche municipal 2025
$refPre = 514;       // Issue #8 - pré-escola municipal 2025
$inep = processInep($rawInep, $censo);
$inep['matriculas_2025']['creche_referencia'] = $refCreche;
$inep['matriculas_2025']['pre_escola_referencia'] = $refPre;

$resultado = [
    'metadata' => [
        'fonte'          => 'IBGE - Censo Demográfico 2022 (Tabela 9514) e INEP - Censo Escolar / IBGE Pesquisa 13',
        'municipio'      => 'Guapó (GO)',
        'codigo_ibge'    => MUNICIPIO_IBGE,
        'periodo'        => '2008-2025',
        'data_extracao'  => (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format(DateTimeInterface::ISO8601),
        'api_censo'      => API_SIDRA_VALUES,
        'api_inep'       => 'https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores',
        'indicadores_inep' => INDICADORES_INEP,
    ],
    'populacao_por_idade' => $censo['populacao_por_idade'],
    'ciclos'              => $censo['ciclos'],
    'series_historicas'   => $inep['series_historicas'],
    'matriculas_2025'     => $inep['matriculas_2025'],
    'transicao_pre_escola_fundamental' => $inep['transicao'],
    'contraturno_tempo_integral'       => contraturno($censo),
];

file_put_contents(OUT_JSON, json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "  ✓ JSON processado salvo em: " . OUT_JSON . "\n";

exportCsv($censo, $inep);
echo "  ✓ CSV exportado em: " . OUT_CSV . "\n";

echo "  [4/4] Validação de somatórios...\n";
$ciclos = $resultado['ciclos'];
$total = $ciclos['total_0a17']['total'];
echo "      Creche (0-3):           {$ciclos['creche']['total']}\n";
echo "      Pré-escola (4-5):       {$ciclos['pre_escola']['total']}\n";
echo "      Fundamental I (6-10):   {$ciclos['fundamental_1']['total']}\n";
echo "      Fundamental II (11-14): {$ciclos['fundamental_2']['total']}\n";
echo "      Ensino Médio (15-17):   {$ciclos['medio']['total']}\n";
echo "      TOTAL 0-17:             {$total}\n";
echo "  ✓ " . (($ciclos['creche']['total'] + $ciclos['pre_escola']['total'] === 1621) && $total === 5107 ? "Somatórios conferem (1.621 e 5.107)." : "Somatórios DIVERGENTES - revisar dados!") . "\n";

echo "\n=== Transição Pré-escola → 1º ano do Fundamental ===\n";
printf("População de 5 anos (Censo 2022): %d\n", $inep['transicao']['populacao_5_anos_censo_2022']);
printf("Matrículas no 1º ano EF (2025):   %d\n", $inep['transicao']['matriculas_1o_ano_fundamental_2025']);
printf("Taxa de transição:                %.2f%%\n", $inep['transicao']['taxa_transicao_pct']);