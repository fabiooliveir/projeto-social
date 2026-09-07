# Nota Técnica — Diagnóstico Demográfico e Escolar Ampliado (0 a 17 anos), Guapó-GO

**Sub-issue 1.4** · Épico #1 · Issue #25

## Objetivo

Expandir o diagnóstico de dados de Guapó-GO da primeira infância (0-5 anos) para toda a
população em idade escolar obrigatória (**0 a 17 anos**), cobrindo o **Ensino Fundamental**
(6 a 14 anos) e o **Ensino Médio** (15 a 17 anos), com vistas ao planejamento pedagógico,
ao fluxo escolar e às ações comunitárias de **contraturno escolar** apoiadas pelo
**auditório multiuso** da nova escola.

## Fontes de Dados

| Fonte | Produto | Agregado/Indicadores | Período |
|---|---|---|---|
| IBGE SIDRA | Censo Demográfico 2022 | Tabela 9514 – idade simples (códigos 6557 a 6574) | 2022 |
| IBGE Pesquisa 13 / INEP | Censo Escolar | Matrículas EF total (5908), EF 1º-9º ano (77899–77907), dependência 1º ano (77941/42/44), EM total (5913) | 2008–2025 |

## Panorama Demográfico (Censo 2022)

| Etapa / Ciclo | Faixa | Total | % de 0-17 |
|---|:---:|:---:|:---:|
| Creche | 0–3 | 1.068 | 20,9% |
| Pré-escola | 4–5 | 553 | 10,8% |
| Ensino Fundamental I (Anos Iniciais) | 6–10 | 1.482 | 29,0% |
| Ensino Fundamental II (Anos Finais) | 11–14 | 1.187 | 23,2% |
| Ensino Médio | 15–17 | 817 | 16,0% |
| **Total em idade escolar** | **0–17** | **5.107** | **100%** |

Indicadores atualizados no pipeline PHP (`GuapoDataSyncService`):

- `populacao_fundamental_1_6a10 = 1.482`
- `populacao_fundamental_2_11a14 = 1.187`
- `populacao_medio_15a17 = 817`
- `populacao_total_escolar_0a17 = 5.107`
- Matrículas no 1º ano do EF (2025) = **306**

## Fluxo de Transição Pré-escola → Ensino Fundamental

| Indicador | Valor |
|---|---|
| População de 5 anos (Censo 2022) | 275 |
| Matrículas no 1º ano do EF (Censo Escolar 2025) | 306 |
| **Taxa de transição** | **111,27%** |

Uma taxa acima de 100% indica que há matrículas no 1º ano em volume superior à coorte de
crianças de 5 anos recenseadas, o que aponta para **ausência de evasão na passagem da
educação infantil para a escola regular** em Guapó — possivelmente com absorção de
crianças de outras idades ou matrículas de faixas adjacentes. O fluxo escolar entre
pré-escola e anos iniciais não apresenta perda estrutural de cobertura.

## A Demanda Oculta por Contraturno Escolar

- **2.669 crianças e adolescentes de 6 a 14 anos** (1.482 + 1.187) estão em idade de
  Ensino Fundamental.
- Grande parte cursa em **meio período** e permanece sem atividades estruturadas no
  contraturno (reforço escolar, oficinas de artes, robótica, música e esporte).
- A API IBGE **Pesquisa 13 / Censo Escolar não disponibiliza indicador individualizado de
  matrícula em tempo integral** para o município — a oferta efetiva de tempo integral
  público segue como lacuna a ser investigada junto à rede municipal.

### O Papel do Auditório Multiuso

O auditório da nova escola atenderá não apenas a Educação Infantil (manhã/tarde), mas se
tornará um **polo de atividades no contraturno** para crianças e adolescentes de 6 a 14
anos: reforço escolar, oficinas de artes, robótica, música e esporte. Isso amplia o
impacto social do investimento ao transformar a infraestrutura escolar em **equipamento
público de tempo integral comunitário**.

## Arquivos Gerados

| Arquivo | Conteúdo |
|---|---|
| `data/raw/ibge_censo2022_9514_0a17_guapo.json` | Resposta bruta SIDRA (0-17 anos) |
| `data/raw/ibge_pesquisa13_fundamental_medio.json` | Resposta bruta INEP (EF/EM) |
| `data/processed/demografia_escolar_0a17_guapo.json` | Dataset consolidado e tipado |
| `data/processed/demografia_escolar_0a17_guapo.csv` | Versão tabular completa |
| `scripts/extrair_dados_escolares_completos.php` | Extração via Docker |

## Execução

```powershell
docker compose run --rm app scripts/extrair_dados_escolares_completos.php
docker compose run --rm app bin/sync_guapo_data.php
docker compose run --rm tests
```

## Validação dos Somatórios

- 0 a 5 anos = **1.621** (1.068 + 553)
- 6 a 10 anos = **1.482**
- 11 a 14 anos = **1.187**
- 15 a 17 anos = **817**
- Total 0 a 17 anos = **5.107**