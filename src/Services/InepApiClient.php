<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * Cliente HTTP resiliente para APIs do INEP, dados.gov.br (CKAN) e QEdu.
 *
 * Encapsula chamadas Guzzle com timeout curto, cache em disco e fallback
 * automático para o último payload válido quando a rede falha.
 */
final class InepApiClient
{
    private const USER_AGENT = 'projeto-social/guapo-diagnostico/1.0';

    private ClientInterface $http;
    private string $cacheFile;
    private ?array $cache = null;

    public function __construct(?ClientInterface $http = null, ?string $cacheFile = null)
    {
        $this->http = $http ?? new Client([
            'connect_timeout' => 3.0,
            'timeout'         => 5.0,
            'headers'         => [
                'User-Agent' => self::USER_AGENT,
                'Accept'     => 'application/json',
            ],
        ]);

        $this->cacheFile = $cacheFile ?? dirname(__DIR__, 2) . '/storage/cache/inep_api_cache.json';
    }

    /**
     * Busca JSON de um endpoint externo (INEP, CKAN, QEdu).
     *
     * Em caso de falha de rede/HTTP, tenta devolver a versão em cache.
     * Retorna o payload em cache como último recurso; lança exceção apenas
     * se não houver cache disponível.
     */
    public function fetchJson(string $url, string $cacheKey): array
    {
        try {
            $response = $this->http->request('GET', $url);
            $body = (string) $response->getBody();
            $data = json_decode($body, true);
            if (!is_array($data)) {
                throw new \RuntimeException("Resposta inválida de {$url}");
            }
            $this->store($cacheKey, $data);

            return $data;
        } catch (RequestException | ConnectException $e) {
            $cached = $this->restore($cacheKey);
            if ($cached !== null) {
                fwrite(STDERR, "  [!] Fallback para cache '{$cacheKey}': {$e->getMessage()}\n");

                return $cached;
            }

            throw new \RuntimeException(
                "Sem rede e sem cache disponível para '{$cacheKey}': {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    /**
     * Faz download de um arquivo binário (XLSX, CSV, etc.) e retorna o conteúdo bruto.
     *
     * Em caso de falha, tenta retornar a versão em cache.
     */
    public function fetchBinary(string $url, string $cacheKey): string
    {
        try {
            $response = $this->http->request('GET', $url);
            $body = (string) $response->getBody();
            $this->storeBinary($cacheKey, $body);

            return $body;
        } catch (RequestException | ConnectException $e) {
            $cached = $this->restoreBinary($cacheKey);
            if ($cached !== null) {
                fwrite(STDERR, "  [!] Fallback para cache binário '{$cacheKey}': {$e->getMessage()}\n");

                return $cached;
            }

            throw new \RuntimeException(
                "Sem rede e sem cache disponível para binário '{$cacheKey}': {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    /**
     * Consulta a CKAN DataStore API do dados.gov.br.
     */
    public function ckanDatastoreSearch(string $resourceId, array $filters = [], int $limit = 100): array
    {
        $params = http_build_query(array_merge([
            'resource_id' => $resourceId,
            'limit'       => $limit,
        ], $filters !== [] ? ['filters' => json_encode($filters)] : []));

        $url = "https://dados.gov.br/api/3/action/datastore_search?{$params}";
        $cacheKey = "ckan_ds_{$resourceId}_" . md5(json_encode($filters));

        return $this->fetchJson($url, $cacheKey);
    }

    /**
     * Consulta a CKAN package_show para obter metadados de um dataset.
     */
    public function ckanPackageShow(string $packageId): array
    {
        $url = "https://dados.gov.br/api/3/action/package_show?id={$packageId}";
        $cacheKey = "ckan_pkg_{$packageId}";

        return $this->fetchJson($url, $cacheKey);
    }

    private function store(string $key, array $data): void
    {
        $cache = $this->load();
        $cache[$key] = [
            'saved_at' => date(DATE_ATOM),
            'data'     => $data,
        ];

        $this->writeCache($cache);
    }

    private function storeBinary(string $key, string $data): void
    {
        $cache = $this->load();
        $cache[$key] = [
            'saved_at' => date(DATE_ATOM),
            'binary'   => base64_encode($data),
        ];

        $this->writeCache($cache);
    }

    private function restore(string $key): ?array
    {
        $cache = $this->load();

        return $cache[$key]['data'] ?? null;
    }

    private function restoreBinary(string $key): ?string
    {
        $cache = $this->load();
        $encoded = $cache[$key]['binary'] ?? null;

        return $encoded !== null ? base64_decode($encoded, true) : null;
    }

    private function writeCache(array $cache): void
    {
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return;
        }

        file_put_contents(
            $this->cacheFile,
            json_encode($cache, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
        $this->cache = $cache;
    }

    private function load(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        if (!is_file($this->cacheFile)) {
            return $this->cache = [];
        }

        $raw = file_get_contents($this->cacheFile);
        $decoded = json_decode($raw ?: '[]', true);

        return $this->cache = is_array($decoded) ? $decoded : [];
    }
}
