<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Services\GuapoDataSyncService;
use App\Services\IbgeApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class GuapoDataSyncServiceTest extends TestCase
{
    private string $cacheFile;

    protected function setUp(): void
    {
        $this->cacheFile = sys_get_temp_dir() . '/ibge_api_cache_test_' . uniqid() . '.json';
    }

    protected function tearDown(): void
    {
        if (is_file($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    public function testSyncConsolidaResumoEPiramide(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->censoFixture(), JSON_UNESCAPED_UNICODE)),
            new Response(200, [], json_encode($this->crecheFixture())),
            new Response(200, [], json_encode($this->preEscolaFixture())),
            new Response(200, [], json_encode($this->fund1AnoFixture())),
            new Response(200, [], json_encode($this->efFixture())),
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $api = new IbgeApiClient($client, $this->cacheFile);
        $service = new GuapoDataSyncService($api, $this->cacheTmp('out.json'));

        $payload = $service->sync();

        $this->assertCount(18, $payload->piramideEtaria);
        $this->assertSame(1068, $payload->resumoExecutivo['populacao_0a3_anos']);
        $this->assertSame(553, $payload->resumoExecutivo['populacao_4a5_anos']);
        $this->assertSame(289, $payload->resumoExecutivo['vagas_creche_atual_2025']);
        $this->assertSame(514, $payload->resumoExecutivo['vagas_pre_escola_atual_2025']);
        $this->assertSame(1482, $payload->resumoExecutivo['populacao_fundamental_1_6a10']);
        $this->assertSame(1187, $payload->resumoExecutivo['populacao_fundamental_2_11a14']);
        $this->assertSame(817, $payload->resumoExecutivo['populacao_medio_15a17']);
        $this->assertSame(5107, $payload->resumoExecutivo['populacao_total_escolar_0a17']);
        $this->assertSame(306, $payload->resumoExecutivo['matriculas_1ano_fundamental_2025']);

        // Público de contraturno (Fundamental I + II) — Sub-issue 2.4
        $this->assertSame(2669, $payload->resumoExecutivo['populacao_contraturno_6a14']);
        $this->assertArrayHasKey('fundamental_total', $payload->seriesHistoricas);
        $this->assertSame(2327, $payload->seriesHistoricas['fundamental_total']['2008']);
        $this->assertSame(2656, $payload->seriesHistoricas['fundamental_total']['2025']);

        // Primeira faixa etária (0 anos)
        $this->assertSame('Menos de 1 ano', $payload->piramideEtaria[0]['idade']);
        $this->assertSame(265, $payload->piramideEtaria[0]['populacao']);
        // Última faixa etária (17 anos)
        $this->assertSame('17 anos', $payload->piramideEtaria[17]['idade']);
        $this->assertSame(256, $payload->piramideEtaria[17]['populacao']);
    }

    public function testCalculosMatematicosDeficitEMetaPne(): void
    {
        $service = new GuapoDataSyncService(new IbgeApiClient(null, $this->cacheFile), $this->cacheTmp('out.json'));

        $resumo = $service->calcularResumo(
            $this->censoParsed(),
            ['2025' => 289],
            ['2025' => 514],
            ['2025' => 306]
        );

        $this->assertSame(779, $resumo['deficit_vagas_creche']);
        $this->assertSame(72.94, $resumo['taxa_desatendimento_creche_pct']);
        $this->assertSame(534, $resumo['meta_pne_minima_50pct']);
        $this->assertSame(245, $resumo['vagas_faltantes_para_pne']);
        $this->assertSame(92.95, $resumo['taxa_cobertura_pre_escola_pct']);

        // Agregados demográficos 0-17 anos (Sub-issue 1.4)
        $this->assertSame(1482, $resumo['populacao_fundamental_1_6a10']);
        $this->assertSame(1187, $resumo['populacao_fundamental_2_11a14']);
        $this->assertSame(817, $resumo['populacao_medio_15a17']);
        $this->assertSame(5107, $resumo['populacao_total_escolar_0a17']);
        $this->assertSame(306, $resumo['matriculas_1ano_fundamental_2025']);
        $this->assertSame(111.27, $resumo['taxa_transicao_pre_fundamental_pct']);

        // Sub-issue 2.4: público de contraturno (6 a 14 anos)
        $this->assertSame(2669, $resumo['populacao_contraturno_6a14']);
    }

    public function testApiFalhaUsaCacheFallback(): void
    {
        // Primeiro: resposta com sucesso, população do cache.
        $mockOk = new MockHandler([
            new Response(200, [], json_encode($this->censoFixture(), JSON_UNESCAPED_UNICODE)),
            new Response(200, [], json_encode($this->crecheFixture())),
            new Response(200, [], json_encode($this->preEscolaFixture())),
            new Response(200, [], json_encode($this->fund1AnoFixture())),
            new Response(200, [], json_encode($this->efFixture())),
        ]);
        $apiOk = new IbgeApiClient(
            new Client(['handler' => HandlerStack::create($mockOk)]),
            $this->cacheFile
        );
        (new GuapoDataSyncService($apiOk, $this->cacheTmp('out_ok.json')))->sync();

        // Agora simula falha: HTTP 500 em todos os endpoints.
        $mockFail = new MockHandler([
            new Response(500, [], 'erro'),
            new Response(500, [], 'erro'),
            new Response(500, [], 'erro'),
            new Response(500, [], 'erro'),
            new Response(500, [], 'erro'),
        ]);
        $apiFail = new IbgeApiClient(
            new Client(['handler' => HandlerStack::create($mockFail)]),
            $this->cacheFile
        );

        // Não deve lançar exceção: fallback pelo cache anterior.
        $payload = (new GuapoDataSyncService($apiFail, $this->cacheTmp('out_fail.json')))->sync();

        $this->assertSame(1068, $payload->resumoExecutivo['populacao_0a3_anos']);
        $this->assertSame(289, $payload->resumoExecutivo['vagas_creche_atual_2025']);
    }

    public function testSemCacheLançaExcecaoQuandoApiFalha(): void
    {
        $mockFail = new MockHandler([
            new Response(503, [], 'erro'),
        ]);

        $api = new IbgeApiClient(
            new Client(['handler' => HandlerStack::create($mockFail)]),
            $this->cacheFile
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Sem rede e sem cache disponível');

        $api->fetchJson('https://example.invalid/endpoint', 'inexistente');
    }

    public function testParseSeriePesquisa13NormalizaHifen(): void
    {
        $service = new GuapoDataSyncService(new IbgeApiClient(null, $this->cacheFile), $this->cacheTmp('out.json'));

        $serie = $service->parseSeriePesquisa13($this->crecheFixture());

        $this->assertSame(121, $serie['2008']);
        $this->assertSame(0, $serie['2011']);
        $this->assertSame(289, $serie['2025']);
    }

    private function cacheTmp(string $name): string
    {
        return sys_get_temp_dir() . '/' . $name . '_' . uniqid() . '.json';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function censoFixture(): array
    {
        $idades = [
            '6557' => ['Menos de 1 ano', 265],
            '6558' => ['1 ano', 207],
            '6559' => ['2 anos', 285],
            '6560' => ['3 anos', 311],
            '6561' => ['4 anos', 278],
            '6562' => ['5 anos', 275],
            '6563' => ['6 anos', 295],
            '6564' => ['7 anos', 298],
            '6565' => ['8 anos', 308],
            '6566' => ['9 anos', 289],
            '6567' => ['10 anos', 292],
            '6568' => ['11 anos', 278],
            '6569' => ['12 anos', 320],
            '6570' => ['13 anos', 312],
            '6571' => ['14 anos', 277],
            '6572' => ['15 anos', 288],
            '6573' => ['16 anos', 273],
            '6574' => ['17 anos', 256],
        ];

        $resultados = [];
        foreach ($idades as $codigo => [$rotulo, $pop]) {
            $resultados[] = [
                'classificacoes' => [
                    ['id' => '2', 'nome' => 'Sexo', 'categoria' => ['6794' => 'Total']],
                    ['id' => '287', 'nome' => 'Idade', 'categoria' => [$codigo => $rotulo]],
                    ['id' => '286', 'nome' => 'Forma de declaração da idade', 'categoria' => ['113635' => 'Total']],
                ],
                'series' => [
                    [
                        'localidade' => ['id' => '5209200', 'nivel' => ['id' => 'N6', 'nome' => 'Município'], 'nome' => 'Guapó (GO)'],
                        'serie' => ['2022' => (string) $pop],
                    ],
                ],
            ];
        }

        return [[
            'id' => '93',
            'variavel' => 'População residente',
            'unidade' => 'Pessoas',
            'resultados' => $resultados,
        ]];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function crecheFixture(): array
    {
        $res = [];
        foreach (range(2008, 2025) as $ano) {
            $res[(string) $ano] = (string) $this->crechePorAno($ano);
        }

        return [[
            'id' => 77883,
            'res' => [
                ['localidade' => '520920', 'res' => $res, 'notas' => []],
            ],
        ]];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function preEscolaFixture(): array
    {
        $res = [];
        foreach (range(2008, 2025) as $ano) {
            $res[(string) $ano] = (string) $this->preEscolaPorAno($ano);
        }

        return [[
            'id' => 5904,
            'res' => [
                ['localidade' => '520920', 'res' => $res, 'notas' => []],
            ],
        ]];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fund1AnoFixture(): array
    {
        $res = [];
        foreach (range(2008, 2025) as $ano) {
            $res[(string) $ano] = (string) $this->fund1AnoPorAno($ano);
        }

        return [[
            'id' => 77899,
            'res' => [
                ['localidade' => '520920', 'res' => $res, 'notas' => []],
            ],
        ]];
    }

    private function fund1AnoPorAno(int $ano): int
    {
        return [
            2008 => 213, 2009 => 233, 2010 => 250, 2011 => 264, 2012 => 240,
            2013 => 260, 2014 => 268, 2015 => 290, 2016 => 282, 2017 => 285,
            2018 => 300, 2019 => 291, 2020 => 288, 2021 => 274, 2022 => 295,
            2023 => 301, 2024 => 308, 2025 => 306,
        ][$ano] ?? 0;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function efFixture(): array
    {
        $res = [];
        foreach (range(2008, 2025) as $ano) {
            $res[(string) $ano] = (string) $this->efPorAno($ano);
        }

        return [[
            'id' => 5908,
            'res' => [
                ['localidade' => '520920', 'res' => $res, 'notas' => []],
            ],
        ]];
    }

    private function efPorAno(int $ano): int
    {
        return [
            2008 => 2327, 2009 => 2300, 2010 => 2320, 2011 => 2110, 2012 => 2227,
            2013 => 2093, 2014 => 2079, 2015 => 1961, 2016 => 1948, 2017 => 1982,
            2018 => 2115, 2019 => 2133, 2020 => 2211, 2021 => 2273, 2022 => 2416,
            2023 => 2502, 2024 => 2644, 2025 => 2656,
        ][$ano] ?? 0;
    }

    private function crechePorAno(int $ano): int
    {
        return [
            2008 => 121, 2009 => 70, 2010 => 15, 2011 => 0, 2012 => 0,
            2013 => 61, 2014 => 89, 2015 => 96, 2016 => 128, 2017 => 132,
            2018 => 249, 2019 => 220, 2020 => 229, 2021 => 177, 2022 => 237,
            2023 => 281, 2024 => 255, 2025 => 289,
        ][$ano] ?? 0;
    }

    private function preEscolaPorAno(int $ano): int
    {
        return [
            2008 => 184, 2009 => 205, 2010 => 266, 2011 => 264, 2012 => 359,
            2013 => 444, 2014 => 477, 2015 => 480, 2016 => 465, 2017 => 494,
            2018 => 408, 2019 => 456, 2020 => 456, 2021 => 420, 2022 => 437,
            2023 => 481, 2024 => 521, 2025 => 514,
        ][$ano] ?? 0;
    }

    /**
     * @return array<int, array{idade: string, populacao: int}>
     */
    private function censoParsed(): array
    {
        return [
            ['idade' => 'Menos de 1 ano', 'populacao' => 265],
            ['idade' => '1 ano', 'populacao' => 207],
            ['idade' => '2 anos', 'populacao' => 285],
            ['idade' => '3 anos', 'populacao' => 311],
            ['idade' => '4 anos', 'populacao' => 278],
            ['idade' => '5 anos', 'populacao' => 275],
            ['idade' => '6 anos', 'populacao' => 295],
            ['idade' => '7 anos', 'populacao' => 298],
            ['idade' => '8 anos', 'populacao' => 308],
            ['idade' => '9 anos', 'populacao' => 289],
            ['idade' => '10 anos', 'populacao' => 292],
            ['idade' => '11 anos', 'populacao' => 278],
            ['idade' => '12 anos', 'populacao' => 320],
            ['idade' => '13 anos', 'populacao' => 312],
            ['idade' => '14 anos', 'populacao' => 277],
            ['idade' => '15 anos', 'populacao' => 288],
            ['idade' => '16 anos', 'populacao' => 273],
            ['idade' => '17 anos', 'populacao' => 256],
        ];
    }
}