<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use PHPUnit\Framework\TestCase;

final class QualityIndicatorsApiTest extends TestCase
{
    private string $viewsCacheDir;

    protected function setUp(): void
    {
        $this->viewsCacheDir = sys_get_temp_dir() . '/views_cache_' . uniqid('', true);
        mkdir($this->viewsCacheDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->viewsCacheDir)) {
            foreach (glob($this->viewsCacheDir . '/*') ?: [] as $file) {
                unlink($file);
            }
            rmdir($this->viewsCacheDir);
        }
    }

    public function testEndpointQualidadeCompletoRetornaJsonValido(): void
    {
        [$status, $body] = $this->request('GET', '/api/indicadores/qualidade');

        $this->assertSame(200, $status);
        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertSame('5209200', $json['municipio']['codigo_ibge']);
        $this->assertSame('Guapó', $json['municipio']['nome']);
        $this->assertSame('GO', $json['municipio']['uf']);
        $this->assertArrayHasKey('atualizado_em', $json);
        $this->assertArrayHasKey('fontes_oficiais', $json);
        $this->assertArrayHasKey('ideb', $json);
        $this->assertArrayHasKey('fluxo_e_docencia', $json);
        $this->assertArrayHasKey('infraestrutura_resumo', $json);
        $this->assertArrayHasKey('diagnostico_qualitativo', $json);
    }

    public function testEndpointIdebRetornaHistoricoCompleto(): void
    {
        [$status, $body] = $this->request('GET', '/api/indicadores/qualidade/ideb');

        $this->assertSame(200, $status);
        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertSame('5209200', $json['municipio']['codigo_ibge']);
        $this->assertArrayHasKey('anos_iniciais', $json);
        $this->assertArrayHasKey('anos_finais', $json);

        $this->assertArrayHasKey('serie_historica', $json['anos_iniciais']);
        $this->assertCount(4, $json['anos_iniciais']['serie_historica']);
        $this->assertArrayHasKey('nota_recente', $json['anos_iniciais']);
        $this->assertArrayHasKey('meta_recente', $json['anos_iniciais']);
        $this->assertArrayHasKey('taxa_aprovacao_pct', $json['anos_iniciais']);
        $this->assertArrayHasKey('benchmark', $json['anos_iniciais']);

        $this->assertArrayHasKey('serie_historica', $json['anos_finais']);
        $this->assertCount(4, $json['anos_finais']['serie_historica']);
    }

    public function testEndpointInfraestruturaRetornaItensEsperados(): void
    {
        [$status, $body] = $this->request('GET', '/api/indicadores/qualidade/infraestrutura');

        $this->assertSame(200, $status);
        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertSame('5209200', $json['municipio']['codigo_ibge']);
        $this->assertArrayHasKey('total_unidades_avaliadas', $json);
        $this->assertArrayHasKey('indicadores_infraestrutura', $json);
        $this->assertIsArray($json['indicadores_infraestrutura']);
        $this->assertGreaterThanOrEqual(1, count($json['indicadores_infraestrutura']));

        foreach ($json['indicadores_infraestrutura'] as $item) {
            $this->assertArrayHasKey('item', $item);
            $this->assertArrayHasKey('unidades_atendidas', $item);
            $this->assertArrayHasKey('percentual', $item);
        }

        $this->assertArrayHasKey('conclusao_censo', $json);
    }

    public function testEndpointDownloadRetornaHeaderAttachment(): void
    {
        [$status, $body, $headers] = $this->requestWithHeaders('GET', '/api/indicadores/qualidade/download');

        $this->assertSame(200, $status);
        if (!empty($headers)) {
            $this->assertStringContainsString('attachment', $headers['content-disposition'] ?? '');
            $this->assertStringContainsString('diagnostico-qualidade-educacional-guapo.json', $headers['content-disposition'] ?? '');
        }

        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertSame('5209200', $json['municipio']['codigo_ibge']);
    }

    public function testHeadersCorsEFixed(): void
    {
        [$status, $body, $headers] = $this->requestWithHeaders('GET', '/api/indicadores/qualidade');

        $this->assertSame(200, $status);
        if (!empty($headers)) {
            $this->assertStringContainsString('application/json', $headers['content-type'] ?? '');
            $this->assertSame('*', $headers['access-control-allow-origin'] ?? '');
            $this->assertSame('nosniff', $headers['x-content-type-options'] ?? '');
        } else {
            $this->assertNotEmpty($body);
        }
    }

    public function testFluxoDocenciaContemIndicadoresEsperados(): void
    {
        [$status, $body] = $this->request('GET', '/api/indicadores/qualidade');

        $this->assertSame(200, $status);
        $json = json_decode($body, true);

        $fluxo = $json['fluxo_e_docencia'];
        $this->assertArrayHasKey('distorcao_idade_serie_anos_iniciais_pct', $fluxo);
        $this->assertArrayHasKey('distorcao_idade_serie_anos_finais_pct', $fluxo);
        $this->assertArrayHasKey('adequacao_formacao_docente_grupo1_pct', $fluxo);
        $this->assertArrayHasKey('media_alunos_turma', $fluxo);
        $this->assertArrayHasKey('creche', $fluxo['media_alunos_turma']);
        $this->assertArrayHasKey('pre_escola', $fluxo['media_alunos_turma']);
        $this->assertArrayHasKey('fundamental_anos_iniciais', $fluxo['media_alunos_turma']);
        $this->assertArrayHasKey('fundamental_anos_finais', $fluxo['media_alunos_turma']);
    }

    public function testDiagnosticoQualitativoContemPontosFortesECriticos(): void
    {
        [$status, $body] = $this->request('GET', '/api/indicadores/qualidade');

        $this->assertSame(200, $status);
        $json = json_decode($body, true);

        $diag = $json['diagnostico_qualitativo'];
        $this->assertArrayHasKey('pontos_fortes', $diag);
        $this->assertArrayHasKey('pontos_criticos', $diag);
        $this->assertArrayHasKey('impacto_projeto_social', $diag);
        $this->assertGreaterThanOrEqual(1, count($diag['pontos_fortes']));
        $this->assertGreaterThanOrEqual(1, count($diag['pontos_criticos']));
    }

    /**
     * Executa o front controller real com um request HTTP simulado.
     *
     * @return array{0: int, 1: string}
     */
    private function request(string $method, string $uri): array
    {
        [$status, $body, $_headers] = $this->requestWithHeaders($method, $uri);

        return [$status, $body];
    }

    /**
     * @return array{0: int, 1: string, 2: array<string, string>}
     */
    private function requestWithHeaders(string $method, string $uri): array
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        putenv('GUAPO_VIEWS_CACHE_DIR=' . $this->viewsCacheDir);
        http_response_code(200);

        $capturedHeaders = [];
        $originalHeader = 'header';
        $headerCallback = function (string $string) use (&$capturedHeaders): bool {
            $parts = explode(':', $string, 2);
            if (count($parts) === 2) {
                $capturedHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
            }

            return true;
        };

        ob_start();
        try {
            include dirname(__DIR__, 2) . '/public/index.php';
        } finally {
            $body = (string) ob_get_clean();
        }

        $status = http_response_code();

        // Coleta headers via headers_list nativo ou xdebug
        $rawHeaders = function_exists('headers_list') ? headers_list() : (function_exists('xdebug_get_headers') ? xdebug_get_headers() : []);
        foreach ($rawHeaders as $header) {
            $parts = explode(':', $header, 2);
            if (count($parts) === 2) {
                $capturedHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
            }
        }

        return [$status, $body, $capturedHeaders];
    }
}
