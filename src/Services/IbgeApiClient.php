<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * Cliente HTTP resiliente para as APIs do IBGE.
 *
 * Encapsula chamadas Guzzle com timeout curto, cache em disco e fallback
 * automático para o último payload válido quando a rede falha.
 */
final class IbgeApiClient
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

        $this->cacheFile = $cacheFile ?? dirname(__DIR__, 2) . '/storage/cache/ibge_api_cache.json';
    }

    /**
     * Busca um JSON de um endpoint oficial do IBGE.
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
     * Persiste o payload mais recente em cache.
     */
    private function store(string $key, array $data): void
    {
        $fillCache = $this->cacheFile;
        $cache = $this->load();
        $cache[$key] = [
            'saved_at' => date(DATE_ATOM),
            'data'     => $data,
        ];

        $dir = dirname($fillCache);
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return;
        }

        file_put_contents($fillCache, json_encode($cache, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->cache = $cache;
    }

    /**
     * Recupera payload em cache para determinada chave.
     */
    private function restore(string $key): ?array
    {
        $cache = $this->load();

        return $cache[$key]['data'] ?? null;
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