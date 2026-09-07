<?php

declare(strict_types=1);

/**
 * Extrai a pirâmide etária (0-5 anos) de Guapó-GO via API SIDRA/IBGE.
 *
 * Tabela 9514 – População residente, por idade e sexo.
 * https://apisidra.ibge.gov.br/values/t/9514/n6/5209200/v/allxp/p/last%201/c2/6794/c287/6557,6558,6559,6560,6561,6562
 */

define('API_URL', 'https://apisidra.ibge.gov.br/values/t/9514/n6/5209200/v/allxp/p/last%201/c2/6794/c287/6557,6558,6559,6560,6561,6562');

define('DIR_RAW', __DIR__ . '/../data/raw');
define('DIR_PROCESSED', __DIR__ . '/../data/processed');

define('MAPPING', [
    6557 => 'Menos de 1 ano',
    6558 => '1 ano',
    6559 => '2 anos',
    6560 => '3 anos',
    6561 => '4 anos',
    6562 => '5 anos',
]);

/**
 * Busca dados da API SIDRA.
 */
function fetchSidra(): array
{
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 30,
            'header' => "Accept: application/json\r\n",
        ],
    ]);

    $raw = @file_get_contents(API_URL, false, $ctx);
    if ($raw === false) {
        fwrite(STDERR, "ERRO: Falha ao consumir API SIDRA.\n");
        exit(1);
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        fwrite(STDERR, "ERRO: Resposta da API não é JSON válido.\n");
        exit(1);
    }

    return $data;
}

/**
 * Salva resposta bruta em data/raw/.
 */
function saveRaw(array $data): string
{
    if (!is_dir(DIR_RAW)) {
        mkdir(DIR_RAW, 0755, true);
    }

    $path = DIR_RAW . '/ibge_censo2022_9514_guapo.json';
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return $path;
}

/**
 * Processa e tabula os dados.
 */
function processData(array $raw): array
{
    // A primeira linha é o cabeçalho; linhas de dados começam no índice 1
    $rows = array_slice($raw, 1);

    $populacao = [];
    $total = 0;

    foreach ($rows as $row) {
        $codigoIdade = (int) ($row['D5C'] ?? 0);
        if (!isset(MAPPING[$codigoIdade])) {
            continue;
        }

        $pop = (int) $row['V'];
        $total += $pop;

        $populacao[$codigoIdade] = [
            'idade'        => MAPPING[$codigoIdade],
            'codigo_sidra' => (string) $codigoIdade,
            'populacao'    => $pop,
        ];
    }

    // Calcula percentuais
    ksort($populacao);
    $result = [];
    foreach ($populacao as $item) {
        $item['percentual_0a5'] = $total > 0
            ? round($item['populacao'] / $total * 100, 2)
            : 0.0;
        $result[] = $item;
    }

    // Totais por faixa
    $creche = 0;
    $preEscola = 0;
    foreach ($result as $item) {
        $cod = (int) $item['codigo_sidra'];
        if ($cod >= 6557 && $cod <= 6560) {
            $creche += $item['populacao'];
        } elseif ($cod >= 6561 && $cod <= 6562) {
            $preEscola += $item['populacao'];
        }
    }

    $now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format(DateTimeInterface::ISO8601);

    return [
        'metadata' => [
            'fonte'        => 'IBGE - Censo Demográfico 2022',
            'tabela'       => '9514 - População residente, por idade e sexo',
            'municipio'    => 'Guapó (GO)',
            'codigo_ibge'  => '5209200',
            'variavel'     => 'População residente (V93)',
            'data_extracao' => $now,
            'api_url'      => API_URL,
        ],
        'populacao_por_idade' => $result,
        'totais' => [
            'creche_0a3' => [
                'faixa'      => '0 a 3 anos',
                'total'      => $creche,
                'descricao'  => 'Demanda potencial para Creche',
            ],
            'pre_escola_4a5' => [
                'faixa'      => '4 a 5 anos',
                'total'      => $preEscola,
                'descricao'  => 'Demanda potencial para Pré-escola',
            ],
            'total_0a5' => [
                'faixa'      => '0 a 5 anos',
                'total'      => $total,
                'descricao'  => 'Total primeira infância',
            ],
        ],
        'analise_demanda' => [
            'creche' => [
                'demanda_potencial'   => $creche,
                'meta_pne_50_porcento' => (int) ceil($creche * 0.5),
                'observacao'          => 'Meta 1 do PNE exige mínimo 50% de atendimento em creche',
            ],
            'pre_escola' => [
                'demanda_potencial'   => $preEscola,
                'meta_pne_100_porcento' => $preEscola,
                'observacao'          => 'CF/88 e PNE exigem universalização (100%) da pré-escola',
            ],
        ],
    ];
}

/**
 * Salva JSON processado.
 */
function saveProcessed(array $data): string
{
    if (!is_dir(DIR_PROCESSED)) {
        mkdir(DIR_PROCESSED, 0755, true);
    }

    $path = DIR_PROCESSED . '/piramide_etaria_guapo_0a5.json';
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return $path;
}

// --- Main ---
echo "Extraindo pirâmide etária de Guapó-GO (IBGE Censo 2022)...\n";

$raw = fetchSidra();
$rawPath = saveRaw($raw);
echo "✓ Dados brutos salvos em: {$rawPath}\n";

$processed = processData($raw);
$procPath = saveProcessed($processed);
echo "✓ Dados processados salvos em: {$procPath}\n";

$t = $processed['totais'];
echo "\n=== Resumo ===\n";
echo "Creche (0-3):      {$t['creche_0a3']['total']}\n";
echo "Pré-escola (4-5):  {$t['pre_escola_4a5']['total']}\n";
echo "Total (0-5):       {$t['total_0a5']['total']}\n";
