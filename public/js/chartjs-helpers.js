(() => {
    'use strict';

    const PALETA = {
        azul: '#3B82F6',
        azulRoyal: '#2563EB',
        vermelho: '#EF4444',
        ambar: '#F59E0B',
        amarelo: '#FBBF24',
        turquesa: '#10B981',
        indigo: '#6366F1',
        violeta: '#8B5CF6',
        coral: '#FB7185',
        eslate: '#64748B',
    };

    const formatarMilhar = (valor) => {
        if (valor === null || valor === undefined || Number.isNaN(Number(valor))) {
            return '0';
        }
        return Number(valor).toLocaleString('pt-BR');
    };

    const formatarDecimal = (valor, casas = 1) => {
        return Number(valor).toLocaleString('pt-BR', {
            minimumFractionDigits: casas,
            maximumFractionDigits: casas,
        });
    };

    const rgbaDe = (hex, alpha) => {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    };

    const gradienteVertical = (context, cor, opacidadePico = 1) => {
        const { chartArea, ctx } = context.chart;
        if (!chartArea) {
            return rgbaDe(cor, opacidadePico);
        }
        const gradiente = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
        gradiente.addColorStop(0, rgbaDe(cor, opacidadePico));
        gradiente.addColorStop(1, rgbaDe(cor, 0));
        return gradiente;
    };

    const tooltipPadrao = {
        backgroundColor: 'rgba(15, 23, 42, 0.92)',
        titleColor: '#F8FAFC',
        bodyColor: '#E2E8F0',
        borderColor: 'rgba(148, 163, 184, 0.25)',
        borderWidth: 1,
        padding: 12,
        cornerRadius: 10,
        titleFont: { family: 'Inter, sans-serif', weight: '700' },
        bodyFont: { family: 'Inter, sans-serif' },
        displayColors: true,
        boxPadding: 6,
        caretSize: 6,
    };

    const legendaPadrao = {
        labels: {
            color: '#334155',
            usePointStyle: true,
            pointStyle: 'circle',
            padding: 16,
            font: { family: 'Inter, sans-serif', size: 12, weight: '500' },
        },
    };

    window.GuapoChartHelpers = {
        PALETA,
        formatarMilhar,
        formatarDecimal,
        rgbaDe,
        gradienteVertical,
        tooltipPadrao,
        legendaPadrao,
    };
})();