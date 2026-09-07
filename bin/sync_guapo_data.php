#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Services\GuapoDataSyncService;
use App\Services\IbgeApiClient;

require dirname(__DIR__) . '/vendor/autoload.php';

$start = microtime(true);

echo "[INFO] Iniciando sincronização dos dados de Guapó-GO (5209200)...\n";

try {
    $sync = new GuapoDataSyncService(new IbgeApiClient());

    /** @var \App\Models\EducationDashboardPayload $payload */
    $payload = $sync->sync();

    $elapsed = microtime(true) - $start;

    $resumo = $payload->resumoExecutivo;
    echo "\n=== Resumo Executivo ===\n";
    echo "População 0-3:      {$resumo['populacao_0a3_anos']}\n";
    echo "Vagas creche 2025:  {$resumo['vagas_creche_atual_2025']}\n";
    echo "Déficit:            {$resumo['deficit_vagas_creche']} ({$resumo['taxa_desatendimento_creche_pct']}%)\n";
    echo "Meta PNE (50%):     {$resumo['meta_pne_minima_50pct']} | Faltantes: {$resumo['vagas_faltantes_para_pne']}\n";
    echo "Pré-escola:         {$resumo['vagas_pre_escola_atual_2025']}/{$resumo['populacao_4a5_anos']} ({$resumo['taxa_cobertura_pre_escola_pct']}%)\n";
    printf("[DONE] Concluído com sucesso em %.2fs!\n", $elapsed);

    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "\n[ERRO] " . $e->getMessage() . "\n");
    exit(1);
}