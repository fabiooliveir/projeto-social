# Projeto Social - Diagnóstico Educacional de Guapó-GO

Projeto de diagnóstico de dados educacionais e demográficos do município de Guapó-GO, com foco na primeira infância (0-5 anos) e educação infantil (creche e pré-escola).

## Estrutura

```
projeto-social/
├── README.md
├── docs/metodologia/         # Notas técnicas e metodologia
├── data/raw/                 # Respostas brutas das APIs (IBGE, INEP)
├── data/processed/           # Dados tabulados e padronizados
└── scripts/                  # Scripts de extração e processamento
```

## Fontes de Dados

| Fonte | API | Uso |
|-------|-----|-----|
| IBGE Censo 2022 | SIDRA / Agregados | Dados demográficos por idade |
| INEP | Censo Escolar | Dados educacionais |

## Uso

```bash
php scripts/extrair_piramide_etaria.php
```

## Referências

- [API SIDRA/IBGE](https://apisidra.ibge.gov.br/)
- [Censo Demográfico 2022](https://censo2022.ibge.gov.br/)
- Meta 1 do PNE (Lei 13.005/2014) – universalização da educação infantil
