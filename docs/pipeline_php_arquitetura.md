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
GuapoDataSyncService  ──► 4 endpoints oficiais
      │
      ├─ parseCenso2022()       (Tabela 9514 / API v3 - 0 a 17 anos)
      ├─ parseSeriePesquisa13() (INEP - Pesquisa 13)
      ├─ calcularResumo()       (déficit, cobertura, PNE, agregação 0-17, transição)
      ▼
EducationDashboardPayload
      │
      ▼
storage/data/guapo_education_cache.json
```

## Endpoints Integrados

| # | Finalidade | Endpoint |
|---|---|---|
| 1 | Censo 2022 - Pirâmide Etária 0-17 anos (Tab. 9514) | `https://servicodados.ibge.gov.br/api/v3/agregados/9514/periodos/2022/variaveis/93?localidades=N6[5209200]&classificacao=2[6794]|287[6557,...,6574]` |
| 2 | Matrículas em Creche Municipal (Pesquisa 13 - 77883) | `https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/77883/resultados/5209200` |
| 3 | Matrículas em Pré-escola Municipal (Pesquisa 13 - 5904) | `https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/5904/resultados/5209200` |
| 4 | Matrículas no 1º ano do EF (Pesquisa 13 - 77899) | `https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/77899/resultados/5209200` |

## Componentes

### IbgeApiClient

- Wrapper do `GuzzleHttp\Client` com `connect_timeout: 3.0s` e `timeout: 5.0s`.
- User-Agent padronizado e `Accept: application/json`.
- **Cache em disco**: cada resposta bem-sucedida é persistida em `storage/cache/ibge_api_cache.json`, indexada por chave.
- **Fallback resiliente**: em caso de `RequestException`/`ConnectException` (rede fora do ar, HTTP 5xx), reutiliza o último payload válido em cache. Só lança exceção se não houver cache.

### GuapoDataSyncService

Orquestrador responsável por:

1. Disparar as 4 requisições (sequenciais nesta versão; prontas para evoluir para Guzzle Pool/Promises).
2. Fazer o parsing das respostas:
   - **Censo 2022 (9514):** categorias `6557–6560` → creche; `6561–6562` → pré-escola; `6563–6567` → Fundamental I (6-10 anos); `6568–6571` → Fundamental II (11-14 anos); `6572–6574` → Ensino Médio (15-17 anos). Total 0-17 = 5.107.
   - **Censo Escolar (77883):** série histórica e matrícula de 2025 (289).
   - **Censo Escolar (5904):** série histórica e matrícula de 2025 (514).
   - **Censo Escolar (77899):** matrículas no 1º ano do EF (2025 = 306) para a taxa de transição.
3. Calcular indicadores:
   - Déficit absoluto de creche: `1.068 − 289 = 779`.
   - Taxa de desatendimento: `779 / 1.068 = 72,94%`.
   - Meta 1 PNE: `1.068 × 0,50 = 534 vagas`.
   - Gap legal: `534 − 289 = 245 vagas adicionais`.
   - Cobertura pré-escola: `514 / 553 = 92,95%`.
   - População Fundamental I/II e Médio: `1.482 / 1.187 / 817`; total 0-17 = `5.107`.
   - Transição pré-escola → 1º ano EF: `306 / 275 = 111,27%` (sem evasão estrutural no fluxo).
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
docker compose run --rm app bin/sync_guapo_data.php

# Rodar os testes unitários e de integração
docker compose run --rm tests

# Subir o painel web (http://localhost:8000/painel-educacao)
docker compose up -d app
```

## Tempo de Execução

O pipeline executa em menos de 5 segundos (alvo: <2s), garantido pelos timeouts curtos e uso de cache. O tempo real é registrado no cache em `tempo_execucao_ms`.