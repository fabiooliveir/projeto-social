(() => {
    'use strict';

    const H = window.GuapoChartHelpers;

    let dadosCache = null;

    const carregarDados = () => {
        const script = document.getElementById('guapo-data');
        if (script && script.textContent.trim()) {
            try {
                return Promise.resolve(JSON.parse(script.textContent));
            } catch (e) {
                console.warn('[charts-guapo] Dados injetados inválidos. Usando a API como fallback.', e);
            }
        }
        return fetch('/api/indicadores/guapo')
            .then((r) => {
                if (!r.ok) {
                    throw new Error(`API respondeu HTTP ${r.status}`);
                }
                return r.json();
            });
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

    const instancias = new Map();

    const renderizar = (dados) => {
        instancias.forEach((instancia) => instancia.destroy());
        instancias.clear();

        const alvos = [
            ['chartEvolucao', construirEvolucao],
            ['chartDeficit', construirDeficit],
            ['chartMetaPNE', construirMetaPNE],
            ['chartPiramide', construirPiramide],
        ];

        alvos.forEach(([id, construtor]) => {
            const canvas = document.getElementById(id);
            if (canvas) {
                instancias.set(id, construtor(canvas, dados));
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