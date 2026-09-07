(() => {
    'use strict';

    const H = window.GuapoChartHelpers;

    let dadosCache = null;

    const carregarDados = () => {
        const script = document.getElementById('guapo-data');
        if (script && script.textContent.trim()) {
            try {
                const dados = JSON.parse(script.textContent);
                return completarQualidade(dados);
            } catch (e) {
                console.warn('[charts-guapo] Dados injetados inválidos. Usando a API como fallback.', e);
            }
        }
        return Promise.all([
            fetch('/api/indicadores/guapo')
                .then((r) => {
                    if (!r.ok) {
                        throw new Error(`API respondeu HTTP ${r.status}`);
                    }
                    return r.json();
                }),
            fetch('/api/indicadores/qualidade')
                .then((r) => (r.ok ? r.json() : null)),
        ]).then(([edu, qualidade]) => (qualidade ? { ...edu, qualidade } : edu));
    };

    const completarQualidade = (dados) => {
        if (dados.qualidade) {
            return Promise.resolve(dados);
        }
        return fetch('/api/indicadores/qualidade')
            .then((r) => (r.ok ? r.json() : null))
            .then((qualidade) => (qualidade ? { ...dados, qualidade } : dados));
    };

    const construirEvolucao = (canvas, dados) => {
        const series = dados.series_historicas || {};
        const creche = series.creche_municipal || {};
        const pre = series.pre_escola_municipal || {};
        const ef = series.fundamental_total || {};
        const anos = [];
        for (let ano = 2008; ano <= 2025; ano++) {
            anos.push(String(ano));
        }
        const valores = (serie) => anos.map(
            (ano) => (serie[ano] === undefined || serie[ano] === null ? null : Number(serie[ano])),
        );

        return new window.Chart(canvas, {
            type: 'line',
            data: {
                labels: anos,
                datasets: [
                    {
                        label: 'Ensino Fundamental (total)',
                        data: valores(ef),
                        borderColor: H.PALETA.violeta,
                        backgroundColor: (ctx) => H.gradienteVertical(ctx, H.PALETA.violeta, 0.25),
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: H.PALETA.violeta,
                        pointBorderColor: '#fff',
                        spanGaps: true,
                    },
                    {
                        label: 'Creche Municipal (0-3)',
                        data: valores(creche),
                        borderColor: H.PALETA.indigo,
                        backgroundColor: (ctx) => H.gradienteVertical(ctx, H.PALETA.indigo, 0.35),
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: H.PALETA.indigo,
                        pointBorderColor: '#fff',
                        spanGaps: true,
                    },
                    {
                        label: 'Pré-escola Municipal (4-5)',
                        data: valores(pre),
                        borderColor: H.PALETA.turquesa,
                        backgroundColor: (ctx) => H.gradienteVertical(ctx, H.PALETA.turquesa, 0.35),
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: H.PALETA.turquesa,
                        pointBorderColor: '#fff',
                        spanGaps: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: H.legendaPadrao,
                    tooltip: {
                        ...H.tooltipPadrao,
                        callbacks: {
                            title: (items) => `Ano ${items[0]?.label ?? ''}`,
                            label: (ctx) => {
                                if (ctx.parsed.y === null) {
                                    return ` ${ctx.dataset.label}: Sem registro no Censo Escolar`;
                                }
                                return ` ${ctx.dataset.label}: ${H.formatarMilhar(ctx.parsed.y)} matrículas`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 3000,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: { callback: (v) => H.formatarMilhar(v) },
                        title: { display: true, text: 'Matrículas', color: '#64748B' },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: true, maxTicksLimit: 18 },
                    },
                },
            },
        });
    };

    const construirDeficit = (canvas, dados) => {
        const r = dados.resumo_executivo;
        const total = Number(r.populacao_0a3_anos);
        const atendidas = Number(r.vagas_creche_atual_2025);
        const desatendidas = Number(r.deficit_vagas_creche);

        const pluginCentral = {
            id: 'textoCentral',
            afterDraw(chart) {
                const meta = chart.getDatasetMeta(0);
                if (!meta.data.length) {
                    return;
                }
                const { x, y } = meta.data[0];
                const ctx = chart.ctx;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#0F172A';
                ctx.font = '800 28px Inter, sans-serif';
                ctx.fillText(`${H.formatarMilhar(total)} Crianças`, x, y - 14);
                ctx.fillStyle = '#64748B';
                ctx.font = '500 12px Inter, sans-serif';
                ctx.fillText('Total recenseado (IBGE 2022)', x, y + 14);
                ctx.restore();
            },
        };

        return new window.Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['Atendidas em creche pública', 'Desatendidas (déficit)'],
                datasets: [{
                    data: [atendidas, desatendidas],
                    backgroundColor: [H.PALETA.azul, H.PALETA.vermelho],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { position: 'bottom', ...H.legendaPadrao },
                    tooltip: {
                        ...H.tooltipPadrao,
                        callbacks: {
                            label: (ctx) => {
                                const pct = total > 0 ? (ctx.parsed / total) * 100 : 0;
                                return ` ${ctx.label}: ${H.formatarMilhar(ctx.parsed)} (${H.formatarDecimal(pct)}%)`;
                            },
                        },
                    },
                    textoCentral: {},
                },
            },
            plugins: [pluginCentral],
        });
    };

    const construirMetaPNE = (canvas, dados) => {
        const r = dados.resumo_executivo;
        const total = Number(r.populacao_0a3_anos);
        const atual = Number(r.vagas_creche_atual_2025);
        const piso = Number(r.meta_pne_minima_50pct);
        const faltantes = Number(r.vagas_faltantes_para_pne);
        const pctAtual = total > 0 ? (atual / total) * 100 : 0;

        const pluginLinhaMeta = {
            id: 'linhaMetaPNE',
            afterDatasetsDraw(chart) {
                const scale = chart.scales.x;
                if (!scale) {
                    return;
                }
                const ctx = chart.ctx;
                ctx.save();
                ctx.beginPath();
                ctx.setLineDash([6, 6]);
                ctx.lineWidth = 2;
                ctx.strokeStyle = H.PALETA.ambar;
                ctx.moveTo(scale.getPixelForValue(piso), chart.chartArea.top);
                ctx.lineTo(scale.getPixelForValue(piso), chart.chartArea.bottom);
                ctx.stroke();
                ctx.setLineDash([]);
                ctx.restore();
            },
        };

        return new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: [
                    `Atendimento atual (2025) · ${H.formatarDecimal(pctAtual)}%`,
                    'Piso legal Meta 1 do PNE (50%)',
                    'Universalização máxima (100%)',
                ],
                datasets: [{
                    data: [atual, piso, total],
                    backgroundColor: [H.PALETA.azul, H.PALETA.ambar, H.PALETA.turquesa],
                    borderRadius: 8,
                    barThickness: 28,
                    maxBarThickness: 32,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...H.tooltipPadrao,
                        callbacks: {
                            label: (ctx) => {
                                if (ctx.dataIndex === 0) {
                                    return ` ${H.formatarMilhar(atual)} vagas (${H.formatarDecimal(pctAtual)}%) · faltam +${H.formatarMilhar(faltantes)} vagas`;
                                }
                                if (ctx.dataIndex === 1) {
                                    return ` Piso legal: ${H.formatarMilhar(piso)} vagas (50%)`;
                                }
                                return ` População total: ${H.formatarMilhar(total)} crianças (100%)`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: total,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: { callback: (v) => H.formatarMilhar(v) },
                    },
                    y: { grid: { display: false } },
                },
            },
            plugins: [pluginLinhaMeta],
        });
    };

    const ETAPAS_PIRAMIDE = [
        { nomes: ['Menos de 1 ano', '1 ano', '2 anos', '3 anos'], curto: 'Creche', cor: H.PALETA.coral },
        { nomes: ['4 anos', '5 anos'], curto: 'Pré-escola', cor: H.PALETA.amarelo },
        { nomes: ['6 anos', '7 anos', '8 anos', '9 anos', '10 anos'], curto: 'Fundamental I', cor: H.PALETA.azulRoyal },
        { nomes: ['11 anos', '12 anos', '13 anos', '14 anos'], curto: 'Fundamental II', cor: H.PALETA.indigo },
        { nomes: ['15 anos', '16 anos', '17 anos'], curto: 'Ensino Médio', cor: H.PALETA.violeta },
    ];

    const construirPiramide = (canvas, dados) => {
        const piramide = dados.piramide_etaria || [];

        const porEtapa = {};
        ETAPAS_PIRAMIDE.forEach((etapa) => {
            porEtapa[etapa.curto] = etapa;
        });

        const nomesCompletos = piramide.map((e) => e.idade);
        const rotulos = piramide.map((e) => {
            const idade = e.idade.replace('Menos de 1 ano', '0');
            return idade.replace(' anos', '');
        });
        const populacoes = piramide.map((e) => Number(e.populacao));
        const cores = piramide.map((e) => {
            for (const etapa of ETAPAS_PIRAMIDE) {
                if (etapa.nomes.includes(e.idade)) {
                    return etapa.cor;
                }
            }
            return H.PALETA.eslate;
        });

        return new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: rotulos,
                datasets: [{
                    data: populacoes,
                    backgroundColor: cores,
                    borderRadius: 6,
                    barPercentage: 0.9,
                    categoryPercentage: 0.85,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            ...H.legendaPadrao.labels,
                            generateLabels: () => ETAPAS_PIRAMIDE.map((etapa) => ({
                                text: `${etapa.curto} (${etapa.nomes[0]}–${etapa.nomes[etapa.nomes.length - 1]})`,
                                fillStyle: etapa.cor,
                                strokeStyle: etapa.cor,
                                lineWidth: 0,
                                hidden: false,
                                index: 0,
                            })),
                        },
                    },
                    tooltip: {
                        ...H.tooltipPadrao,
                        callbacks: {
                            title: (items) => {
                                const idx = items[0]?.dataIndex;
                                return idx === undefined ? '' : `Idade: ${nomesCompletos[idx]}`;
                            },
                            label: (ctx) => ` População: ${H.formatarMilhar(ctx.parsed.y)}`,
                            afterLabel: (ctx) => {
                                const idx = ctx.dataIndex;
                                for (const etapa of ETAPAS_PIRAMIDE) {
                                    if (etapa.nomes.includes(nomesCompletos[idx])) {
                                        return ` Etapa: ${etapa.curto}`;
                                    }
                                }
                                return '';
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: { callback: (v) => H.formatarMilhar(v) },
                        title: { display: true, text: 'População (pessoas)', color: '#64748B' },
                    },
                    x: {
                        grid: { display: false },
                        title: { display: true, text: 'Idade (anos)', color: '#64748B' },
                        ticks: { autoSkip: false },
                    },
                },
            },
        });
    };

    const construirIdeb = (canvas, dados) => {
        const ideb = dados.qualidade && dados.qualidade.ideb;
        if (!ideb) {
            console.warn('[charts-guapo] Bloco qualidade.ideb ausente.');
            return null;
        }
        const serieAI = ideb.anos_iniciais?.serie_historica || [];
        const serieAF = ideb.anos_finais?.serie_historica || [];
        const anos = [...new Set([...serieAI, ...serieAF].map((e) => String(e.ano)))].sort();
        const extrair = (serie, campo) => anos.map((ano) => {
            const ponto = serie.find((e) => String(e.ano) === ano);
            return ponto ? ponto[campo] : null;
        });
        const linha = (rotulo, serie, campo, cor, tracejada = false) => ({
            label: rotulo,
            data: extrair(serie, campo),
            borderColor: cor,
            backgroundColor: cor,
            fill: false,
            tension: 0.4,
            borderWidth: tracejada ? 2 : 3,
            borderDash: tracejada ? [6, 6] : [],
            pointRadius: tracejada ? 3 : 5,
            pointBackgroundColor: cor,
            pointBorderColor: '#fff',
            spanGaps: true,
        });

        return new window.Chart(canvas, {
            type: 'line',
            data: {
                labels: anos,
                datasets: [
                    linha('Nota Anos Iniciais (5º ano)', serieAI, 'nota', H.PALETA.azulRoyal),
                    linha('Meta MEC Anos Iniciais', serieAI, 'meta', H.PALETA.azul, true),
                    linha('Nota Anos Finais (9º ano)', serieAF, 'nota', H.PALETA.violeta),
                    linha('Meta MEC Anos Finais', serieAF, 'meta', H.PALETA.eslate, true),
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: H.legendaPadrao,
                    tooltip: {
                        ...H.tooltipPadrao,
                        callbacks: {
                            title: (items) => `SAEB ${items[0]?.label ?? ''}`,
                            label: (ctx) => ` ${ctx.dataset.label}: ${H.formatarDecimal(ctx.parsed.y)}`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: 7,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        title: { display: true, text: 'IDEB (nota 0-10)', color: '#64748B' },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: false },
                    },
                },
            },
        });
    };

    const construirDistorcao = (canvas, dados) => {
        const qualidade = dados.qualidade;
        const fluxo = qualidade && qualidade.fluxo_e_docencia;
        const ideb = qualidade && qualidade.ideb;
        if (!fluxo || !ideb) {
            console.warn('[charts-guapo] Bloco qualidade.fluxo_e_docencia ausente.');
            return null;
        }
        const tdiIniciais = Number(fluxo.distorcao_idade_serie_anos_iniciais_pct ?? 0);
        const tdiFinais = Number(fluxo.distorcao_idade_serie_anos_finais_pct ?? 0);
        const reprovIniciais = 100 - Number(ideb.anos_iniciais?.taxa_aprovacao_pct ?? 100);
        const reprovFinais = 100 - Number(ideb.anos_finais?.taxa_aprovacao_pct ?? 100);

        return new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: ['Anos Iniciais (6 a 10)', 'Anos Finais (11 a 14)'],
                datasets: [
                    {
                        label: 'Distorção idade-série (%)',
                        data: [tdiIniciais, tdiFinais],
                        backgroundColor: [H.rgbaDe(H.PALETA.ambar, 0.85), H.PALETA.vermelho],
                        borderRadius: 8,
                        barPercentage: 0.55,
                    },
                    {
                        label: 'Reprovação anual (%)',
                        data: [reprovIniciais, reprovFinais],
                        backgroundColor: [H.rgbaDe(H.PALETA.eslate, 0.5), H.rgbaDe(H.PALETA.eslate, 0.85)],
                        borderRadius: 8,
                        barPercentage: 0.55,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: H.legendaPadrao,
                    tooltip: {
                        ...H.tooltipPadrao,
                        callbacks: {
                            label: (ctx) => ` ${ctx.dataset.label}: ${H.formatarDecimal(ctx.parsed.y)}%`,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: { callback: (v) => `${v}%` },
                        title: { display: true, text: 'Percentual de estudantes', color: '#64748B' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    };

    const ITENS_INFRAESTRUTURA = {
        com_refeitorio_alimentacao_pct: 'Refeitório com alimentação',
        com_internet_banda_larga_pct: 'Internet banda larga',
        com_biblioteca_sala_leitura_pct: 'Biblioteca / sala de leitura',
        com_acessibilidade_pcd_pct: 'Acessibilidade para PcD',
        com_parque_infantil_ludico_pct: 'Parque infantil / área lúdica',
        com_bercario_lactario_creche_pct: 'Berçário / lactário',
    };

    const ITENS_CRITICOS = ['Berçário / lactário', 'Parque infantil / área lúdica'];

    const construirInfraestrutura = (canvas, dados) => {
        const infra = dados.qualidade && dados.qualidade.infraestrutura_resumo;
        if (!infra) {
            console.warn('[charts-guapo] Bloco qualidade.infraestrutura_resumo ausente.');
            return null;
        }
        const itens = infra.itens || {};
        const entradas = Object.entries(ITENS_INFRAESTRUTURA)
            .map(([chave, rotulo]) => ({ rotulo, pct: Number(itens[chave] ?? 0) }))
            .sort((a, b) => a.pct - b.pct);
        const cores = entradas.map((e) => (ITENS_CRITICOS.includes(e.rotulo) ? H.PALETA.vermelho : H.PALETA.turquesa));

        return new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: entradas.map((e) => e.rotulo),
                datasets: [{
                    data: entradas.map((e) => e.pct),
                    backgroundColor: cores,
                    borderRadius: 6,
                    barPercentage: 0.72,
                    categoryPercentage: 0.8,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        ...H.tooltipPadrao,
                        callbacks: {
                            title: (items) => items[0]?.label ?? '',
                            label: (ctx) => {
                                const rotulo = ctx.label;
                                const ehCritico = ITENS_CRITICOS.includes(rotulo);
                                const unidades = Math.round((ctx.parsed.x / 100) * Number(infra.total_unidades_avaliadas ?? 0));
                                const nota = ehCritico
                                    ? ` Carência crítica: apenas ${H.formatarDecimal(ctx.parsed.x)}% das unidades (${unidades} de ${infra.total_unidades_avaliadas})`
                                    : ` ${H.formatarDecimal(ctx.parsed.x)}% das unidades (${unidades} de ${infra.total_unidades_avaliadas})`;
                                return nota;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(148,163,184,0.15)' },
                        ticks: { callback: (v) => `${v}%` },
                        title: { display: true, text: 'Unidades com o item (%)', color: '#64748B' },
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter, sans-serif', size: 12, weight: '600' } },
                    },
                },
            },
        });
    };

    const instancias = new Map();

    const renderizar = (dados) => {
        instancias.forEach((instancia) => instancia.destroy());
        instancias.clear();

        const alvos = [
            ['chartEvolucao', construirEvolucao],
            ['chartDeficit', construirDeficit],
            ['chartMetaPNE', construirMetaPNE],
            ['chartPiramide', construirPiramide],
            ['chartIdeb', construirIdeb],
            ['chartDistorcaoFluxo', construirDistorcao],
            ['chartInfraestrutura', construirInfraestrutura],
        ];

        alvos.forEach(([id, construtor]) => {
            const canvas = document.getElementById(id);
            if (canvas) {
                const instancia = construtor(canvas, dados);
                if (instancia) {
                    instancias.set(id, instancia);
                }
            }
        });
    };

    window.ChartsGuapo = { renderizar, carregarDados, instancias };

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Chart === 'undefined') {
            console.error('[charts-guapo] Chart.js não carregado.');
            return;
        }
        carregarDados()
            .then((dados) => {
                dadosCache = dados;
                renderizar(dados);
            })
            .catch((err) => console.error('[charts-guapo] Falha ao renderizar gráficos.', err));
    });

    // Abas escondem/gradem painéis; ao alternar, redimensiona os gráficos afetados.
    window.addEventListener('guapo:tabschange', () => {
        if (dadosCache && typeof window.Chart !== 'undefined') {
            renderizar(dadosCache);
        }
    });
})();