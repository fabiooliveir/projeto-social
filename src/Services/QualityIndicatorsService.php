<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service para consolidação, cache e regras de negócio dos indicadores
 * de qualidade educacional de Guapó-GO.
 *
 * Alimenta o controller REST e gerencia o cache consolidado em
 * storage/data/guapo_quality_cache.json.
 */
final class QualityIndicatorsService
{
    public function __construct(
        private readonly string $processedFile = __DIR__ . '/../../data/processed/indicadores_qualidade_guapo.json',
        private readonly string $cacheFile = __DIR__ . '/../../storage/data/guapo_quality_cache.json',
    ) {
    }

    /**
     * Retorna o payload completo consolidado (IDEB, TDI, AFD, ATU, Infraestrutura).
     *
     * Tenta carregar do cache da aplicação primeiro; se ausente, lê do dataset
     * processado. Lança exceção se nenhum estiver disponível.
     *
     * @return array<string, mixed>
     */
    public function obterIndicadoresCompletos(): array
    {
        $cache = $this->loadCache();
        if ($cache !== null) {
            return $cache;
        }

        $data = $this->loadProcessed();
        if ($data === null) {
            throw new \RuntimeException(
                'Dados de indicadores de qualidade não disponíveis. '
                . 'Execute o script de extração: php scripts/extrair_indicadores_qualidade.php'
            );
        }

        $this->persist($data);

        return $data;
    }

    /**
     * Retorna apenas o bloco IDEB (Anos Iniciais e Finais).
     *
     * @return array<string, mixed>
     */
    public function obterIdeb(): array
    {
        $completo = $this->obterIndicadoresCompletos();

        return [
            'municipio'     => $completo['municipio'],
            'atualizado_em' => $completo['atualizado_em'],
            'anos_iniciais' => $completo['ideb']['anos_iniciais'],
            'anos_finais'   => $completo['ideb']['anos_finais'],
        ];
    }

    /**
     * Retorna o bloco de infraestrutura das unidades escolares.
     *
     * @return array<string, mixed>
     */
    public function obterInfraestrutura(): array
    {
        $completo = $this->obterIndicadoresCompletos();

        return [
            'municipio'                => $completo['municipio'],
            'atualizado_em'            => $completo['atualizado_em'],
            'total_unidades_avaliadas' => $completo['infraestrutura_resumo']['total_unidades_avaliadas'],
            'indicadores_infraestrutura' => $this->mapInfraestruturaItems($completo),
            'conclusao_censo'          => $completo['diagnostico_qualitativo']['impacto_projeto_social'],
        ];
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

    /**
     * @return array<string, mixed>|null
     */
    private function loadProcessed(): ?array
    {
        if (!is_file($this->processedFile)) {
            return null;
        }

        $raw = file_get_contents($this->processedFile);
        $decoded = $raw !== false ? json_decode($raw, true) : null;

        return is_array($decoded) ? $decoded : null;
    }

    private function persist(array $data): void
    {
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new \RuntimeException("Não foi possível criar o diretório {$dir}");
        }

        file_put_contents(
            $this->cacheFile,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Mapeia os itens de infraestrutura do payload completo para o formato
     * do endpoint /infraestrutura.
     *
     * @param array<string, mixed> $completo
     * @return array<int, array{item: string, unidades_atendidas: int, percentual: float}>
     */
    private function mapInfraestruturaItems(array $completo): array
    {
        $total = $completo['infraestrutura_resumo']['total_unidades_avaliadas'] ?? 0;
        $itens = $completo['infraestrutura_resumo']['itens'] ?? [];

        $mapping = [
            'com_refeitorio_alimentacao_pct'      => 'Refeitório com alimentação escolar',
            'com_internet_banda_larga_pct'         => 'Internet banda larga pedagógica',
            'com_biblioteca_sala_leitura_pct'      => 'Biblioteca ou sala de leitura',
            'com_acessibilidade_pcd_pct'           => 'Acessibilidade para pessoas com deficiência',
            'com_parque_infantil_ludico_pct'       => 'Parque infantil e área lúdica',
            'com_bercario_lactario_creche_pct'     => 'Berçário e lactário para creche',
        ];

        $resultado = [];
        foreach ($mapping as $chave => $rotulo) {
            $pct = $itens[$chave] ?? 0.0;
            $atendidas = $total > 0 ? (int) round($total * $pct / 100) : 0;
            $resultado[] = [
                'item'               => $rotulo,
                'unidades_atendidas' => $atendidas,
                'percentual'         => (float) $pct,
            ];
        }

        return $resultado;
    }
}
