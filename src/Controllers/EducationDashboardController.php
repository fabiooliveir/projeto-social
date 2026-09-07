<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\GuapoDataSyncService;
use App\Services\IbgeApiClient;
use App\Services\QualityIndicatorsService;
use Jenssegers\Blade\Blade;

/**
 * Controller do painel educacional e do endpoint REST de indicadores.
 */
final class EducationDashboardController
{
    private readonly QualityIndicatorsService $qualityService;

    public function __construct(
        private readonly Blade $blade,
        private readonly string $cacheFile = __DIR__ . '/../../storage/data/guapo_education_cache.json',
        ?QualityIndicatorsService $qualityService = null,
    ) {
        $this->qualityService = $qualityService ?? new QualityIndicatorsService();
    }

    public function index(): void
    {
        $payload = $this->payload();

        try {
            $qualidade = $this->qualityService->obterIndicadoresCompletos();
        } catch (\RuntimeException) {
            $qualidade = null;
        }

        header('Content-Type: text/html; charset=utf-8');

        echo $this->blade->render('dashboard.painel', [
            'municipio'          => $payload['municipio'],
            'atualizado_em'      => $payload['atualizado_em'],
            'resumo_executivo'   => $payload['resumo_executivo'],
            'piramide_etaria'    => $payload['piramide_etaria'],
            'series_historicas'  => $payload['series_historicas'],
            'qualidade'          => $qualidade,
        ]);
    }

    public function apiIndicadores(): void
    {
        $payload = $this->payload();

        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Download do diagnóstico completo em JSON (CTA do painel).
     */
    public function baixarDiagnostico(): void
    {
        $payload = $this->payload();

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="diagnostico-educacional-guapo-0a17.json"');
        header('Cache-Control: public, max-age=3600');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * Carrega o cache consolidado; se ausente, dispara o pipeline de sync.
     *
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        $dados = $this->loadCache();

        if ($dados === null) {
            $sync = new GuapoDataSyncService(new IbgeApiClient());
            $dados = $sync->sync()->toArray();
        }

        return $dados;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadCache(): ?array
    {
        if (!is_file($this->cacheFile)) {
            return null;
        }

        $raw = file_get_contents($this->cacheFile);
        $decoded = $raw !== false ? json_decode($raw, true) : null;

        return is_array($decoded) ? $decoded : null;
    }
}