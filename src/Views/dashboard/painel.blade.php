@extends('layouts.app')

@section('title', 'Diagnóstico Socioeducacional 0-17 anos - Guapó-GO | Projeto Social')

@section('content')
<div class="min-h-screen">
    <header class="bg-slate-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-cyan-400">Projeto Social · Diagnóstico Socioeducacional</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Guapó – GO ({{ $municipio['codigo_ibge'] }})</h1>
                    <p class="mt-2 max-w-3xl text-slate-300">
                        Análise do déficit de vagas em creches, do cumprimento da Meta 1 do PNE (Lei 13.005/2014) e do
                        ecossistema educacional completo — da primeira infância ao Ensino Médio, com <strong class="text-white">5.107 crianças e jovens de 0 a 17 anos</strong>.
                    </p>
                </div>
                <div class="shrink-0 rounded-2xl border border-slate-700 bg-slate-800 px-5 py-4 text-right">
                    <p class="text-xs text-slate-400">Última atualização</p>
                    <p class="mt-1 text-sm font-semibold text-slate-100">{{ $atualizado_em }}</p>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Alternador de visão --}}
        <div role="tablist" aria-label="Visões do diagnóstico" class="inline-flex w-full max-w-2xl flex-wrap gap-1 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm sm:w-auto">
            <button type="button" id="tab-infancia" role="tab" aria-controls="panel-infancia" aria-selected="true" data-tab-target="panel-infancia" class="tab-btn tab-btn-ativo flex-1 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-bold transition">
                Foco Prioritário: Primeira Infância (0 a 5)
            </button>
            <button type="button" id="tab-ecossistema" role="tab" aria-controls="panel-ecossistema" aria-selected="false" data-tab-target="panel-ecossistema" class="tab-btn flex-1 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-bold transition">
                Ecossistema Completo (0 a 17)
            </button>
            <button type="button" id="tab-qualidade" role="tab" aria-controls="panel-qualidade" aria-selected="false" data-tab-target="panel-qualidade" class="tab-btn flex-1 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-bold transition">
                Qualidade & IDEB (MEC / SAEB)
            </button>
        </div>

        {{-- Grid de 5 KPIs --}}
        <section class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5" aria-label="Indicadores principais">
            @include('dashboard.components.kpi-card', [
                'titulo' => 'População Escolar Total (0 a 17)',
                'valor' => number_format($resumo_executivo['populacao_total_escolar_0a17'], 0, ',', '.'),
                'badge' => 'IBGE · Censo 2022',
                'contexto' => 'Crianças e jovens de Guapó em idade escolar obrigatória.',
                'classe' => 'bg-gradient-to-br from-slate-900 to-slate-700 border-slate-900 text-white',
                'cor_badge' => 'bg-cyan-100 text-cyan-800',
                'cor_valor' => 'text-white',
                'cor_titulo' => 'text-slate-300',
                'cor_contexto' => 'text-slate-300',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Primeira Infância (0 a 3 anos)',
                'valor' => number_format($resumo_executivo['populacao_0a3_anos'], 0, ',', '.'),
                'badge' => 'Demanda potencial de creche',
                'contexto' => 'Crianças residentes na faixa etária de referência para creche.',
                'cor_badge' => 'bg-sky-100 text-sky-700',
                'cor_valor' => 'text-sky-700',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Capacidade Ofertada (Creche 2025)',
                'valor' => number_format($resumo_executivo['vagas_creche_atual_2025'], 0, ',', '.'),
                'badge' => 'INEP · Censo Escolar 2025',
                'contexto' => 'Matrículas ativas na rede municipal de ensino de Guapó.',
                'cor_badge' => 'bg-indigo-100 text-indigo-700',
                'cor_valor' => 'text-indigo-700',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Déficit Estimado de Vagas',
                'valor' => number_format($resumo_executivo['deficit_vagas_creche'], 0, ',', '.'),
                'badge' => number_format($resumo_executivo['taxa_desatendimento_creche_pct'], 1, ',', '.') . '% desatendidas',
                'contexto' => 'Crianças de 0 a 3 anos fora de creche pública.',
                'cor_badge' => 'bg-red-100 text-red-700',
                'cor_valor' => 'text-red-600',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Público de Contraturno (6 a 14)',
                'valor' => number_format($resumo_executivo['populacao_contraturno_6a14'], 0, ',', '.'),
                'badge' => 'Fundamental I + II',
                'contexto' => 'Estudantes de meio período que podem ser atendidos pelo auditório multiuso.',
                'cor_badge' => 'bg-violet-100 text-violet-700',
                'cor_valor' => 'text-violet-700',
            ])
        </section>

        {{-- Painel: Primeira Infância --}}
        <section role="tabpanel" id="panel-infancia" aria-labelledby="tab-infancia" data-tab-panel>
            {{-- Gráficos da primeira infância --}}
            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                @include('dashboard.components.chart-card', [
                    'id' => 'chartDeficit',
                    'titulo' => 'Déficit Real da Primeira Infância',
                    'subtitulo' => 'Composição da população de 0 a 3 anos entre atendidas e desatendidas.',
                    'fonte' => 'IBGE 2022 · INEP 2025',
                    'altura' => 'h-80',
                    'legenda' => 'O déficit equivale a 72,9% das crianças de 0 a 3 anos fora da creche pública.',
                ])
                @include('dashboard.components.chart-card', [
                    'id' => 'chartMetaPNE',
                    'titulo' => 'Distância do Piso da Meta 1 do PNE',
                    'subtitulo' => 'Atendimento atual, piso legal (50%) e universalização da demanda por creche.',
                    'fonte' => 'Lei 13.005/2014',
                    'altura' => 'h-80',
                    'legenda' => 'A linha tracejada marca o piso legal: ainda faltam 245 vagas para chegar a 50%.',
                ])
            </div>

            {{-- Storytelling Bloco A --}}
            <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="storytelling-a-titulo">
                <h2 id="storytelling-a-titulo" class="text-xl font-bold text-slate-900">A Janela dos Primeiros Mil Dias (0 a 3 anos)</h2>
                <div class="mt-4 grid gap-6 md:grid-cols-2">
                    <div class="space-y-3 text-sm leading-relaxed text-slate-600">
                        <p>
                            Os primeiros mil dias de vida são a fase de maior plasticidade do cérebro humano. É nesse período
                            que se formam as bases da linguagem, do controle emocional e da capacidade de aprendizado.
                            Para cada <strong>{{ number_format($resumo_executivo['populacao_0a3_anos'], 0, ',', '.') }} criança de Guapó</strong>,
                            a creche pública não é um luxo: é a garantia de nutrição, acompanhamento pedagógico e estimulação
                            precoce que decidem o resto da vida escolar.
                        </p>
                        <p>
                            Hoje, <strong>{{ number_format($resumo_executivo['deficit_vagas_creche'], 0, ',', '.') }} bebês e crianças de 0 a 3 anos
                            não têm vaga</strong> ({{ number_format($resumo_executivo['taxa_desatendimento_creche_pct'], 1, ',', '.') }}%).
                            A cada ano perdido sem esse atendimento, o município acumula desigualdades que depois custarão muito
                            mais caro em reforço escolar, saúde e assistência social.
                        </p>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                            <p class="font-semibold text-rose-900">Persona representativa · Márcia, 26 anos, mãe solo</p>
                            <p class="mt-1 leading-relaxed text-rose-800">
                                Márcia deixou de aceitar um contrato numa fábrica da região porque não tinha com quem deixar
                                o filho de 2 anos. “Se tivesse creche, eu trabalhava e dava comida melhor em casa.” Persona
                                ilustrativa criada a partir do perfil socioeconômico de Guapó — não se trata de depoimento real.
                            </p>
                        </div>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="font-semibold text-amber-800">Conclusão para captação</p>
                            <p class="mt-1 leading-relaxed">
                                Faltam <strong>{{ number_format($resumo_executivo['vagas_faltantes_para_pne'], 0, ',', '.') }} vagas públicas</strong>
                                só para alcançar o piso legal da Meta 1 do PNE. Cada vaga criada libera uma mãe ou pai para o
                                trabalho, movimenta a economia local e protege o desenvolvimento infantil.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Painel: Ecossistema Completo --}}
        <section role="tabpanel" id="panel-ecossistema" aria-labelledby="tab-ecossistema" data-tab-panel hidden>
            {{-- Gráfico 1: Pirâmide 0-17 --}}
            <div class="mt-8">
                @include('dashboard.components.chart-card', [
                    'id' => 'chartPiramide',
                    'titulo' => 'Pirâmide Demográfica Escolar Completa (0 a 17 anos)',
                    'subtitulo' => 'População por idade simples, colorida por etapa escolar — total de 5.107 crianças e jovens.',
                    'fonte' => 'IBGE · Censo 2022',
                    'altura' => 'h-96',
                    'legenda' => 'Passar o cursor sobre cada barra para ver a idade, a população e a etapa escolar correspondente.',
                ])
            </div>

            {{-- Módulo de Contraturno & Auditório Multiuso --}}
            <div class="mt-10 overflow-hidden rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-6 shadow-sm sm:p-8" aria-labelledby="contraturno-titulo">
                <h2 id="contraturno-titulo" class="text-xl font-bold text-violet-900">Contraturno Escolar & Auditório Multiuso</h2>
                <div class="mt-4 grid gap-6 md:grid-cols-2">
                    <div class="space-y-3 text-sm leading-relaxed text-slate-600">
                        <p>
                            Em Guapó, <strong class="text-violet-900">{{ number_format($resumo_executivo['populacao_contraturno_6a14'], 0, ',', '.') }} crianças e
                            adolescentes de 6 a 14 anos</strong> cursam o Ensino Fundamental em turnos parciais.
                            Nas horas ociosas, sem acesso a esportes, cultura, tecnologia e reforço, o tempo livre se
                            transforma em vulnerabilidade: indisciplina escolar, exposição a telas e evasão futura.
                        </p>
                        <p>
                            A evolução histórica mostra a rede municipal crescendo (o 1º ano do Fundamental passou de
                            {{ number_format($series_historicas['fundamental_1_ano']['2008'] ?? 0, 0, ',', '.') }} para
                            {{ number_format($series_historicas['fundamental_1_ano']['2025'] ?? 0, 0, ',', '.') }} matrículas entre 2008 e 2025),
                            mas as crianças ainda estudam em um único turno — o contraturno segue 100% descoberto.
                        </p>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div class="rounded-xl border border-violet-100 bg-white p-4">
                            <p class="font-semibold text-violet-900">A Nossa Solução Integrada</p>
                            <ul class="mt-2 space-y-2 leading-relaxed text-slate-600">
                                <li><strong>Período diurno:</strong> a nova escola infantil acolhe em tempo integral a primeira infância, reduzindo o déficit de creches com infraestrutura segura e nutrição balanceada.</li>
                                <li><strong>Contraturno:</strong> o auditório multiuso vira polo comunitário — oficinas de reforço, robótica e artes para os {{ number_format($resumo_executivo['populacao_contraturno_6a14'], 0, ',', '.') }} alunos de 6 a 14 anos.</li>
                                <li><strong>Período noturno:</strong> capacitação e alfabetização de mães e pais, apresentações culturais e reuniões da comunidade. Um único equipamento atendendo à família inteira.</li>
                            </ul>
                        </div>
                        <div class="rounded-xl border border-violet-100 bg-white p-4">
                            <p class="font-semibold text-violet-900">Persona representativa · Larissa, 11 anos</p>
                            <p class="mt-1 leading-relaxed text-slate-600">
                                Larissa estuda pela manhã e passa a tarde sozinha em casa enquanto a mãe trabalha na
                                lavoura. Uma vaga no contraturno do auditório significaria reforço de matemática, aula
                                de robótica e um espaço seguro até o fim do expediente. Persona ilustrativa criada a
                                partir do perfil local — não se trata de depoimento real.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Painel: Qualidade Educacional --}}
        @include('dashboard.components.section-quality')

        {{-- Evolução histórica (visão compartilhada) --}}
        <div class="mt-10">
            <script type="application/json" id="guapo-data">
{!! json_encode([
    'municipio' => $municipio,
    'atualizado_em' => $atualizado_em,
    'resumo_executivo' => $resumo_executivo,
    'piramide_etaria' => $piramide_etaria,
    'series_historicas' => $series_historicas,
    'qualidade' => $qualidade ?? null,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
            </script>
            @include('dashboard.components.chart-card', [
                'id' => 'chartEvolucao',
                'titulo' => 'Evolução Histórica das Matrículas (2008–2025)',
                'subtitulo' => 'Trajetória das matrículas em creche, pré-escola e no Ensino Fundamental do município.',
                'fonte' => 'INEP · Censo Escolar',
                'altura' => 'h-72 sm:h-80 lg:h-96',
                'legenda' => 'O Ensino Fundamental reúne as matrículas de todas as redes; creche e pré-escola são municipais.',
            ])
        </div>

        {{-- Interpretação integrada --}}
        <section class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="interpretacao-titulo">
            <h2 id="interpretacao-titulo" class="text-xl font-bold text-slate-900">Interpretação e Diagnóstico</h2>
            <div class="mt-4 grid gap-6 md:grid-cols-2">
                <div class="space-y-3 text-sm leading-relaxed text-slate-600">
                    <p>
                        O <strong>Censo Demográfico 2022</strong> (IBGE) contabiliza
                        <strong>{{ number_format($resumo_executivo['populacao_total_escolar_0a17'], 0, ',', '.') }} crianças e jovens de 0 a 17 anos</strong> em Guapó-GO,
                        distribuídos entre creche (<strong>{{ number_format($resumo_executivo['populacao_0a3_anos'], 0, ',', '.') }}</strong>),
                        pré-escola (<strong>{{ number_format($resumo_executivo['populacao_4a5_anos'], 0, ',', '.') }}</strong>),
                        Fundamental I (<strong>{{ number_format($resumo_executivo['populacao_fundamental_1_6a10'], 0, ',', '.') }}</strong>),
                        Fundamental II (<strong>{{ number_format($resumo_executivo['populacao_fundamental_2_11a14'], 0, ',', '.') }}</strong>) e
                        Ensino Médio (<strong>{{ number_format($resumo_executivo['populacao_medio_15a17'], 0, ',', '.') }}</strong>).
                    </p>
                    <p>
                        Na Educação Infantil, apenas
                        <strong>{{ number_format($resumo_executivo['vagas_creche_atual_2025'], 0, ',', '.') }} crianças estão matriculadas em creche pública</strong>:
                        um déficit de <strong>{{ number_format($resumo_executivo['deficit_vagas_creche'], 0, ',', '.') }} vagas</strong>
                        ({{ number_format($resumo_executivo['taxa_desatendimento_creche_pct'], 1, ',', '.') }}%).
                        A pré-escola, por sua vez, cobre
                        <strong>{{ number_format($resumo_executivo['taxa_cobertura_pre_escola_pct'], 2, ',', '.') }}%</strong> das crianças de 4 e 5 anos —
                        outro passo rumo à universalização.
                    </p>
                </div>
                <div class="space-y-4 text-sm leading-relaxed text-slate-600">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="font-semibold text-slate-800">Transição pré-escola → 1º ano do Ensino Fundamental</p>
                        <p class="mt-1">
                            Das <strong>{{ number_format($resumo_executivo['populacao_4a5_anos'], 0, ',', '.') }} crianças de 4 e 5 anos</strong>,
                            <strong>{{ number_format($resumo_executivo['matriculas_1ano_fundamental_2025'], 0, ',', '.') }}</strong> ingressaram no 1º ano em 2025 —
                            fluxo equivalente a <strong>{{ number_format($resumo_executivo['taxa_transicao_pre_fundamental_pct'], 2, ',', '.') }}%</strong>.
                            Não há evasão estrutural na passagem para o Fundamental: o gargalo está, comprovadamente, na creche.
                        </p>
                    </div>
                    <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                        <p class="font-semibold text-violet-900">Conclusão para captação</p>
                        <p class="mt-1">
                            Duas frentes se complementam: <strong>ampliar a creche</strong> em
                            {{ number_format($resumo_executivo['vagas_faltantes_para_pne'], 0, ',', '.') }} vagas (piso do PNE) para a primeira infância e
                            <strong>ativar o contraturno</strong> para os
                            {{ number_format($resumo_executivo['populacao_contraturno_6a14'], 0, ',', '.') }} estudantes do Fundamental
                            por meio do auditório multiuso.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('dashboard.components.cta-footer')
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="/js/chartjs-helpers.js"></script>
<script src="/js/dashboard-tabs.js"></script>
<script src="/js/charts-guapo.js"></script>
@endsection