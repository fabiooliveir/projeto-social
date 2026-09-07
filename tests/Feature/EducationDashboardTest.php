<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use PHPUnit\Framework\TestCase;

final class EducationDashboardTest extends TestCase
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

    public function testPainelEducacaoRenderizaKpisComStatus200(): void
    {
        [$status, $body] = $this->request('GET', '/painel-educacao');

        $this->assertSame(200, $status);
        $this->assertStringContainsString('Diagnóstico da Educação Infantil', $body);
        foreach (['1.068', '289', '779', '534'] as $valorEsperado) {
            $this->assertStringContainsString($valorEsperado, $body);
        }
    }

    public function testApiIndicadoresRetornaJsonValido(): void
    {
        [$status, $body] = $this->request('GET', '/api/indicadores/guapo');

        $this->assertSame(200, $status);
        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertSame('5209200', $json['municipio']['codigo_ibge']);
        $this->assertSame(1068, $json['resumo_executivo']['populacao_0a3_anos']);
        $this->assertSame(779, $json['resumo_executivo']['deficit_vagas_creche']);
        $this->assertSame(534, $json['resumo_executivo']['meta_pne_minima_50pct']);
    }

    public function testRotaInexistenteRetorna404(): void
    {
        [$status, $body] = $this->request('GET', '/rota/inexistente');

        $this->assertSame(404, $status);
        $this->assertStringContainsString('Página não encontrada', $body);
    }

    /**
     * Executa o front controller real com um request HTTP simulado.
     *
     * @return array{0: int, 1: string}
     */
    private function request(string $method, string $uri): array
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        putenv('GUAPO_VIEWS_CACHE_DIR=' . $this->viewsCacheDir);
        http_response_code(200);

        ob_start();
        try {
            include dirname(__DIR__, 2) . '/public/index.php';
        } finally {
            $body = (string) ob_get_clean();
        }

        return [http_response_code(), $body];
    }
}