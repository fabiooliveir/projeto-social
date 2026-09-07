# Nota Técnica – Extração de Dados Demográficos: Pirâmide Etária 0-5 Anos, Guapó-GO

## Fonte de Dados

- **Instituto**: IBGE – Instituto Brasileiro de Geografia e Estatística
- **Produto**: Censo Demográfico 2022
- **Agregado**: 9514 – População residente, por idade e sexo

## Nível Territorial

- **Unidade**: Município
- **Código IBGE**: 5209200
- **Município**: Guapó (GO)

## Variável Utilizada

- **Código**: V93
- **Nome**: População residente
- **Unidade**: Pessoas

## Classificações

| Classificação | Código | Valor |
|---|---|---|
| Sexo | 2 | Total (6794) – ambos os sexos |
| Idade (anos completos) | 287 | 6557 a 6562 |

### Categorias de Idade (Código SIDRA)

| Código SIDRA | Faixa Etária |
|:---:|---|
| 6557 | Menos de 1 ano |
| 6558 | 1 ano |
| 6559 | 2 anos |
| 6560 | 3 anos |
| 6561 | 4 anos |
| 6562 | 5 anos |

## Ano de Referência

2022

## Notas do IBGE

- **População residente**: Pessoa que tem seu domicílio habitual no território brasileiro na data de referência do Censo, independentemente de estar ou não presente no domicílio.
- A população residente difere da população presente, que inclui pessoas que estavam no domicílio na data de referência mas não residem nele habitualmente.

## API Utilizada

**API SIDRA/IBGE** (formato tabular):

```
https://apisidra.ibge.gov.br/values/t/9514/n6/5209200/v/allxp/p/last%201/c2/6794/c287/6557,6558,6559,6560,6561,6562
```

- **Endpoint**: `apisidra.ibge.gov.br`
- **Método**: GET
- **Formato de resposta**: JSON (array de arrays)
- **Primeira linha**: Cabeçalho com nomes das variáveis
- **Linhas seguintes**: Dados

## Referências Legais

- **Meta 1 do PNE** (Lei 13.005/2014): Universalizar, para crianças a partir de 4 anos de idade, até o final da primeira década deste Programa, a educação infantil em creches e pré-escolas, e, até o final do sexto ano, a ampliação da cobertura para, pelo menos, 50% das crianças de até 3 anos de idade.
- **CF/88, art. 208, inciso IV**: O dever do Estado com a educação será efetivado mediante a garantia de: educação infantil, em creches e pré-escolas, às crianças até cinco anos de idade.
