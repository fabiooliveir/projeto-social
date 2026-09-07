# Nota Técnica: Metodologia dos Indicadores de Qualidade Educacional de Guapó-GO

## 1. Introdução

Esta nota técnica documenta a metodologia empregada na coleta, processamento e consolidação dos indicadores de qualidade educacional do município de **Guapó-GO** (código IBGE `5209200`). Os dados são extraídos de fontes oficiais do INEP, MEC e QEdu, com o objetivo de fundamentar o diagnóstico qualitativo que orienta o projeto social proposto.

---

## 2. Fontes de Dados

### 2.1 IDEB - Índice de Desenvolvimento da Educação Básica

- **Fonte primária:** INEP / MEC
- **Endpoints consultados:**
  - QEdu API: `https://api.qedu.org.br/v1/cities/5209200/ideb`
  - Dados Abertos (CKAN): `https://dados.gov.br/api/3/action/datastore_search`
  - Downloads diretos INEP: `https://download.inep.gov.br/educacao_basica/portal_ideb/`
- **Período:** Séries históricas 2017, 2019, 2021, 2023
- **Filtros:** `CO_MUNICIPIO = 5209200`, `REDE = Municipal`

### 2.2 Indicadores de Fluxo e Docência (INEP)

- **Taxa de Distorção Idade-Série (TDI):**
  - URL: `https://download.inep.gov.br/informacoes_estatisticas/indicadores_educacionais/2023/tdi/TDI_2023_MUNICIPIOS.xlsx`
- **Adequação da Formação Docente (AFD):**
  - URL: `https://download.inep.gov.br/informacoes_estatisticas/indicadores_educacionais/2023/afd/AFD_2023_MUNICIPIOS.xlsx`
- **Média de Alunos por Turma (ATU):**
  - URL: `https://download.inep.gov.br/informacoes_estatisticas/indicadores_educacionais/2023/atu/ATU_2023_MUNICIPIOS.xlsx`

### 2.3 Censo Escolar - Infraestrutura

- **Fonte:** Microdados do Censo da Educação Básica (Tabela `ESCOLAS.csv`)
- **Filtros:** `CO_MUNICIPIO = 5209200`, `TP_SITUACAO_FUNCIONAMENTO = 1`
- **Variáveis mapeadas:** `IN_AGUA_POTAVEL`, `IN_ENERGIA_REDE_PUBLICA`, `IN_ESGOTO_REDE_PUBLICA`, `IN_REFEITORIO`, `IN_INTERNET_APRENDIZAGEM`, `IN_BANDA_LARGA`, `IN_BIBLIOTECA`, `IN_SALA_LEITURA`, `IN_PARQUE_INFANTIL`, `IN_BERCARIO`, `IN_ACESSIBILIDADE_INCLUSAO`

### 2.4 QEdu API

- **Base URL:** `https://api.qedu.org.br/v1/cities/5209200/`
- **Endpoints:** `/ideb`, `/learning`, `/schools`, `/schools/infrastructure`

---

## 3. Fórmulas e Cálculos

### 3.1 IDEB

O IDEB é composto pelo produto de duas dimensões:

$$IDEB = N \times P$$

Onde:
- **N** = Nota média do学生 nas avaliações do SAEB (escala 0 a 10)
- **P** = Taxa de aprovação escolar (proporção de alunos aprovados)

### 3.2 Taxa de Distorção Idade-Série (TDI)

$$TDI = \frac{\text{Alunos com idade superior à esperada para a série}}{\text{Total de alunos matriculados}} \times 100$$

### 3.3 Adequação da Formação Docente (AFD)

$$AFD = \frac{\text{Turmas com professor do Grupo 1 (licenciatura plena)}}{\text{Total de turmas}} \times 100$$

**Grupo 1:** Professor com licenciatura na disciplina que ministra.

### 3.4 Média de Alunos por Turma (ATU)

$$ATU = \frac{\text{Total de alunos matriculados}}{\text{Total de turmas}}$$

---

## 4. Arquitetura Técnica

### 4.1 Cliente HTTP Resiliente (`InepApiClient`)

- Timeout de conexão: 3 segundos
- Timeout de transferência: 5 segundos
- Cache em disco: `storage/cache/inep_api_cache.json`
- Fallback automático para cache na ausência de rede

### 4.2 Pipeline de Extração

1. Consulta APIs externas (QEdu, CKAN, downloads INEP)
2. Em caso de falha, utiliza dados oficiais publicados pelo INEP
3. Consolidação em JSON e CSV
4. Persistência em `data/processed/` e `storage/data/`

### 4.3 Endpoints REST

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/indicadores/qualidade` | Payload completo |
| `GET` | `/api/indicadores/qualidade/ideb` | Apenas IDEB |
| `GET` | `/api/indicadores/qualidade/infraestrutura` | Apenas infraestrutura |
| `GET` | `/api/indicadores/qualidade/download` | Download do diagnóstico |

---

## 5. Limitações e Notas

1. **Dados em cache:** Quando as APIs externas estão indisponíveis, o sistema utiliza os dados oficiais mais recentes publicados pelo INEP para Guapó-GO.
2. **Atualização:** Os dados do IDEB são atualizados a cada ciclo do SAEB (bienal). Os indicadores de fluxo e infraestrutura seguem o calendário do Censo Escolar.
3. **Cobertura geográfica:** Todos os indicadores referem-se exclusivamente ao município de Guapó-GO (código IBGE `5209200`), rede municipal de ensino.

---

## 6. Referências

- INEP - Instituto Nacional de Pesquisas Educacionais Anísio Teixeira
- MEC - Ministério da Educação
- QEdu - Fundação Lemann / Iede
- dados.gov.br - Portal Brasileiro de Dados Abertos
- IBGE - Instituto Brasileiro de Geografia e Estatística
