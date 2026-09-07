# Projeto Social - Diagnóstico Educacional de Guapó-GO

Projeto de diagnóstico de dados educacionais e demográficos do município de Guapó-GO, com foco na primeira infância (0-5 anos) e educação infantil (creche e pré-escola).

## Estrutura

```
projeto-social/
├── README.md
├── docs/metodologia/         # Notas técnicas e metodologia
├── data/raw/                 # Respostas brutas das APIs (IBGE, INEP)
├── data/processed/           # Dados tabulados e padronizados
├── public/                   # Front controller (index.php) e .htaccess
├── src/Controllers/          # Controllers HTTP (Dashboard)
├── src/Views/                # Templates Blade (Tailwind CSS)
├── scripts/                  # Scripts de extração e processamento
└── storage/data/             # Cache consolidado do pipeline
```

## Fontes de Dados

| Fonte | API | Uso |
|-------|-----|-----|
| IBGE Censo 2022 | SIDRA / Agregados | Dados demográficos por idade |
| INEP | Censo Escolar | Dados educacionais |

## Uso (CLI)

```bash
php scripts/extrair_piramide_etaria.php
```

## Painel Web (Dashboard)

```bash
docker compose up -d app
```

- Dashboard: http://localhost:8000/painel-educacao
- API JSON:  http://localhost:8000/api/indicadores/guapo

## Testes

```bash
docker compose run --rm tests
```

## Referências

- [API SIDRA/IBGE](https://apisidra.ibge.gov.br/)
- [Censo Demográfico 2022](https://censo2022.ibge.gov.br/)
- Meta 1 do PNE (Lei 13.005/2014) – universalização da educação infantil
