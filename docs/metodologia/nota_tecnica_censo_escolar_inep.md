# Nota Técnica – Série Histórica do Censo Escolar INEP (2008 a 2025) – Guapó-GO

## 1. Objetivo

Consolidar a série histórica de matrículas na Educação Infantil (Creche e Pré-escola) do município de **Guapó-GO** (código IBGE `5209200`), entre **2008 e 2025**, e comparar a oferta à demanda potencial extraída do Censo Demográfico 2022 (Issue #7).

## 2. Fonte de Dados

- **Produtor dos dados:** INEP – Instituto Nacional de Estudos e Pesquisas Educacionais Anísio Teixeira (Censo Escolar).
- **Consolidação e disponibilização:** IBGE – Pesquisa 13 (Pesquisa Anual de Matrículas do Ensino Regular, por Dependência Administrativa e Localização).
- **API oficial:** `https://servicodados.ibge.gov.br/api/v1/pesquisas/13/indicadores/{indicador}/resultados/5209200`

## 3. Indicadores Utilizados

| Indicador ID | Descrição |
|:---:|---|
| `77883` | Matrículas em Creche – Rede Municipal |
| `77886` | Matrículas em Creche – Rede Privada |
| `5904` | Matrículas em Pré-escola – Rede Municipal |
| `5907` | Matrículas em Pré-escola – Rede Privada |
| `77895` | Unidades Escolares com Creche – Rede Municipal |
| `5946` | Unidades Escolares com Pré-escola – Rede Municipal |

> **Nota:** Neste município as redes estadual e federal não possuem matrículas de educação infantil significativas; a oferta é concentrada na rede municipal, com participação residual/inexistente da rede privada em creche a partir de 2025.

## 4. Conceitos e Notas Técnicas

- **Matrículas de ensino regular:** Excluem turmas de Educação de Jovens e Adultos (EJA) e Educação Especial em classes separadas, quando aplicável.
- **Rede Municipal:** Unidades administradas pelo poder público municipal.
- **Rede Privada:** Instituições particulares de ensino.
- **Valor `"-"`:** Indicador não divulgado/rede inexistente no ano; normalizado para **0** numérico no dataset consolidado.

## 5. Fórmulas de Cálculo

### 5.1 Taxa de desassistência em creche (2025)

```
Déficit absoluto   = População 0-3 anos (2022) − Vagas públicas de creche (2025)
Taxa desassistência = (Déficit absoluto ÷ População 0-3 anos) × 100
```

```
1.068 − 289 = 779   →   779 ÷ 1.068 = 72,94% de desassistência
```

### 5.2 Meta 1 do PNE (Lei nº 13.005/2014)

```
Vagas mínimas Meta 1 = População 0-3 anos × 50%
Déficit legal        = Vagas mínimas − Vagas públicas ofertadas
```

```
1.068 × 0,50 = 534 vagas mínimas
534 − 289    = 245 novas vagas públicas necessárias
```

### 5.3 Taxa de cobertura em pré-escola (2025)

```
Cobertura pré-escola = (Matrículas pré-escola ÷ População 4-5 anos) × 100
```

```
514 ÷ 553 = 92,95%
```

> **Atenção:** A taxa de cobertura é aproximada, pois considera matrículas totais (municipal e privada) sobre a população residente do Censo 2022. Serve como indicador de tendência, não como medida de atendimento individualizado.

## 6. Base Legal

- **Meta 1 do PNE (Lei nº 13.005/2014):** Universalizar, até o final da década, a educação infantil na pré-escola para crianças de 4 a 5 anos e ampliar a oferta de creches para, no mínimo, 50% das crianças de até 3 anos.
- **CF/88, art. 208, inciso IV:** Educação infantil em creches e pré-escolas às crianças até cinco anos como dever do Estado.
- **Lei nº 9.394/1996 (LDB):** Organização da educação infantil em creches (0-3) e pré-escolas (4-5).

## 7. Limitações

- A série histórica do IBGE (Pesquisa 13) possui lacunas de divulgação para algumas redes em determinados anos (representadas por `"-"`).
- A população base utilizada é estática (Censo 2022). Para estimativas anuais de demanda, recomenda-se interpolar com as projeções populacionais do IBGE.