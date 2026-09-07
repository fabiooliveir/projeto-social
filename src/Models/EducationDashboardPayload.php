<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Payload final consumível por bibliotecas de gráficos (Chart.js / ApexCharts).
 */
final class EducationDashboardPayload
{
    /**
     * @param array<string, string>       $municipio
     * @param array<string, int|float>    $resumoExecutivo
     * @param array<int, array<string, mixed>> $piramideEtaria
     * @param array<string, array<string, int>> $seriesHistoricas
     */
    public function __construct(
        public readonly array $municipio,
        public readonly string $atualizadoEm,
        public readonly int $tempoExecucaoMs,
        public readonly array $resumoExecutivo,
        public readonly array $piramideEtaria,
        public readonly array $seriesHistoricas,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'municipio' => $this->municipio,
            'atualizado_em' => $this->atualizadoEm,
            'tempo_execucao_ms' => $this->tempoExecucaoMs,
            'resumo_executivo' => $this->resumoExecutivo,
            'piramide_etaria' => $this->piramideEtaria,
            'series_historicas' => $this->seriesHistoricas,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}