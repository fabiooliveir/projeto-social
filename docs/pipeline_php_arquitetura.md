# Pipeline PHP – Arquitetura de Automação de Coleta e Cache de Dados

## Visão Geral

Pipeline automatizado em **PHP 8.2+ orientado a objetos**, 100% executado via **Docker**. Coleta dados demográficos e educacionais de Guapó-GO (`5209200`) de APIs oficiais do IBGE, calcula indicadores e gera um arquivo de cache consolidado para consumo por dashboards.

## Arquitetura

```
Cliente HTTP (Guzzle)
      │
      ▼
IbgeApiClient ──► Cache em disco (storage/cache/)
      │
      ▼
GuapoDataSyncService  ──► 3 endpoints oficiais
      │
      ├─ parseCenso2022()       (Tabela 9514 / API v3)
      ├─ parseSeriePesquisa13() (INEP - Pesquisa 13)
      ├─ calcularResumo()       (déficit, cobertura, PNE)
      ▼
EducationDashboardPayload
      │
      ▼
storage/data/guapo_education_cache.json
```

## Endpoints Integrados

| # | Finalidade | Endpoint |
|---|---|---|
| 1 | Censo 2022 - Pirâmide Etária 0-5 anos (Tab. 9514) | `https://servicodados.ibge.gov.br/api/v3/agregados/9514/periodos/2022/variaveis/93?localidades=N6[5209200]&classificacao=2[6794]|287[6557,6558,6559,6560,6561,6562]` |
| 2 | Matrículas em Creche Municipal (Pesquisa 13 - 77883) | `https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/77883/resultados/5209200` |
| 3 | Matrículas em Pré-escola Municipal (Pesquisa 13 - 5904) | `https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/5904/resultados/5209200` |

## Componentes

### IbgeApiClient

- Wrapper do `GuzzleHttp\Client` com `connect_timeout: 3.0s` e `timeout: 5.0s`.
- User-Agent padronizado e `Accept: application/json`.
- **Cache em disco**: cada resposta bem-sucedida é persistida em `storage/cache/ibge_api_cache.json`, indexada por chave.
- **Fallback resiliente**: em caso de `RequestException`/`ConnectException` (rede fora do ar, HTTP 5xx), reutiliza o último payload válido em cache. Só lança exceção se não houver cache.

### GuapoDataSyncService

Orquestrador responsável por:

1. Disparar as 3 requisições (sequenciais nesta versão; prontas para evoluir para Guzzle Pool/Promises).
2. Fazer o parsing das respostas:
   - **Censo 2022 (9514):** categorias `6557–6560` → creche; `6561–6562` → pré-escola.
   - **Censo Escolar (77883):** série histórica e matrícula de 2025 (289).
   - **Censo Escolar (5904):** série histórica e matrícula de 2025 (514).
3. Calcular indicadores:
   - Déficit absoluto de creche: `1.068 − 289 = 779`.
   - Taxa de desatendimento: `779 / 1.068 = 72,94%`.
   - Meta 1 PNE: `1.068 × 0,50 = 534 vagas`.
   - Gap legal: `534 − 289 = 245 vagas adicionais`.
   - Cobertura pré-escola: `514 / 553 = 92,95%`.
4. Persistir o payload em `storage/data/guapo_education_cache.json`.

### EducationDashboardPayload

Modelo de valor (value object) com `toArray()`, pensado para serialização direta em JSON consumível por Chart.js / ApexCharts.

### bin/sync_guapo_data.php

Ponto de entrada CLI com cronômetro e saída formatada. Código de saída `0` em sucesso, `1` em falha.

## Resiliência e Falhas

| Cenário | Comportamento |
|---|---|
| Rede indisponível / timeout | Fallback para cache local; execução continua |
| HTTP 5xx do IBGE | Fallback para cache local |
| Sem cache e sem rede | Exceção `RuntimeException` com mensagem clara; saída `1` |
| Dado ausente (`"-"` no INEP) | Normalizado para `0` |

## Como Executar

```powershell
# Instalar dependências
docker compose run --rm composer install

# Executar o pipeline (coleta + cache)
docker compose run --rm app

# Rodar os testes unitários
docker compose run --rm tests
```

## Tempo de Execução

O pipeline executa em menos de 5 segundos (alvo: <2s), garantido pelos timeouts curtos e uso de cache. O tempo real é registrado no cache em `tempo_execucao_ms`.