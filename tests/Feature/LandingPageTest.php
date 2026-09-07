<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use PHPUnit\Framework\TestCase;

final class LandingPageTest extends TestCase
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

    public function testRotaRaizRenderizaLandingComStatus200(): void
    {
        [$status, $body] = $this->request('GET', '/');

        $this->assertSame(200, $status);
        $this->assertStringContainsString('Escola Social de Guapó', $body);
        $this->assertStringContainsString('Auditório Multiuso', $body);
        $this->assertStringContainsString('2.502', $body);
        $this->assertStringContainsString('PIX', $body);
    }

    public function testLandingExibeIndicadoresCentrais(): void
    {
        [, $body] = $this->request('GET', '/');

        $this->assertStringContainsString('2.502', $body);
        $this->assertStringContainsString('16,4%', $body);
        $this->assertStringContainsString('Educação Infantil', $body);
        $this->assertStringContainsString('Auditório Multiuso', $body);
        $this->assertStringContainsString('Contraturno', $body);
        $this->assertStringContainsString('5', $body);
    }

    public function testSchoolDoesNotOfferBercario(): void
    {
        [, $body] = $this->request('GET', '/');

        $this->assertStringContainsString('não inclui berçário', $body);
        $this->assertStringContainsString('berçário (0 a 2 anos)', $body);
    }

    public function testCotasDeApadrinhamentoRenderizam(): void
    {
        [, $body] = $this->request('GET', '/');

        $this->assertStringContainsString('Cota Salas Pedagógicas', $body);
        $this->assertStringContainsString('Cota Auditório Acústico', $body);
        $this->assertStringContainsString('R$ 250', $body);
        $this->assertStringContainsString('R$ 500', $body);
    }

    public function testContatoInvalidoRetornaErros(): void
    {
        $_POST = [];

        [$status, $body] = $this->request('POST', '/contato', true);

        $this->assertSame(422, $status);
        $json = json_decode($body, true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('erros', $json);
        $this->assertArrayHasKey('nome', $json['erros']);
        $this->assertArrayHasKey('contato', $json['erros']);
        $this->assertArrayHasKey('tipo_apoio', $json['erros']);
    }

    public function testPainelEducacaoContinuaAcessivel(): void
    {
        [$status, $body] = $this->request('GET', '/painel-educacao');

        $this->assertSame(200, $status);
        $this->assertStringContainsString('Diagnóstico da Educação Infantil', $body);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function request(string $method, string $uri, bool $ajax = false): array
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        if ($ajax) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        } else {
            unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        }

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
