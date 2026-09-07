<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\QualityIndicatorsService;

/**
 * Controller REST dos endpoints de indicadores de qualidade educacional.
 *
 * Endpoints:
 *   GET /api/indicadores/qualidade           → payload completo
 *   GET /api/indicadores/qualidade/ideb      → apenas IDEB
 *   GET /api/indicadores/qualidade/infraestrutura → apenas infraestrutura
 *   GET /api/indicadores/qualidade/download  → download do diagnóstico
 */
final class QualityIndicatorsController
{
    public function __construct(
        private readonly QualityIndicatorsService $service,
    ) {
    }

    /**
     * GET /api/indicadores/qualidade
     *
     * Retorna o payload completo consolidado.
     */
    public function index(): void
    {
        $payload = $this->service->obterIndicadoresCompletos();
        $this->jsonResponse($payload);
    }

    /**
     * GET /api/indicadores/qualidade/ideb
     *
     * Retorna o histórico de notas e metas do IDEB.
     */
    public function ideb(): void
    {
        $payload = $this->service->obterIdeb();
        $this->jsonResponse($payload);
    }

    /**
     * GET /api/indicadores/qualidade/infraestrutura
     *
     * Retorna o levantamento de infraestrutura das escolas.
     */
    public function infraestrutura(): void
    {
        $payload = $this->service->obterInfraestrutura();
        $this->jsonResponse($payload);
    }

    /**
     * GET /api/indicadores/qualidade/download
     *
     * Download do diagnóstico consolidado em JSON.
     */
    public function download(): void
    {
        $payload = $this->service->obterIndicadoresCompletos();

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="diagnostico-qualidade-educacional-guapo.json"');
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    private function jsonResponse(array $payload): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
