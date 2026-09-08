@extends('layouts.app')

@section('title', 'Diagnóstico da Educação Infantil & Transparência - Guapó-GO | Projeto Social')

@section('content')
<div class="min-h-screen">
    {{-- Header Técnico e Institucional --}}
    <header class="bg-gradient-to-r from-slate-950 via-slate-900 to-brand-950 text-white border-b border-slate-800">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4 mb-6">
                <a href="/" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-brand-400 hover:text-brand-300 transition-colors">
                    <span>←</span> Voltar para o Site da Escola
                </a>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/20 text-brand-300 border border-brand-500/30">
                        <span>🏛️</span> Escola com Auditório Multiuso · MROSC
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white text-base">📊</span>
                        <p class="text-xs font-bold uppercase tracking-widest text-brand-400 font-display">
                            Projeto Social · Diagnóstico da Educação Infantil & Contraturno Escolar
                        </p>
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white font-display">
                        Guapó – GO <span class="text-slate-400 text-2xl font-normal">(IBGE {{ $municipio['codigo_ibge'] }})</span>
                    </h1>
                    <p class="mt-2 max-w-3xl text-sm sm:text-base text-slate-300 leading-relaxed">
                        Painel de monitoramento educacional: combate à distorção idade-série e ativação do
                        <strong class="text-white">Programa de Educação Integral & Contraturno</strong> para os estudantes de 6 a 14 anos,
                        acompanhando o ecossistema total de <strong class="text-white">{{ number_format($resumo_executivo['populacao_total_escolar_0a17'], 0, ',', '.') }} jovens</strong>.
                    </p>
                </div>
                <div class="shrink-0 rounded-2xl border border-slate-700/80 bg-slate-800/80 backdrop-blur-sm px-5 py-4 text-left sm:text-right">
                    <p class="text-xs text-slate-400">Última atualização dos dados</p>
                    <p class="mt-1 text-sm font-semibold text-slate-100 flex items-center gap-1.5 justify-start sm:justify-end">
                        <span class="w-2 h-2 rounded-full bg-hope-400 animate-pulse"></span>
                        {{ $atualizado_em }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Alternador de Visão (Tabs) - Solução da Escola em Primeiro Lugar --}}
        <div role="tablist" aria-label="Visões do diagnóstico" class="inline-flex w-full flex-wrap gap-1.5 rounded-2xl border border-slate-200/90 bg-white p-1.5 shadow-sm sm:w-auto">
            <button type="button" id="tab-contraturno" role="tab" aria-controls="panel-contraturno" aria-selected="true" data-tab-target="panel-contraturno" class="tab-btn tab-btn-ativo flex-1 sm:flex-none whitespace-nowrap rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition">
                🎯 Solução da Escola: Educação Integral & Contraturno (6 a 14)
            </button>
            <button type="button" id="tab-ecossistema" role="tab" aria-controls="panel-ecossistema" aria-selected="false" data-tab-target="panel-ecossistema" class="tab-btn flex-1 sm:flex-none whitespace-nowrap rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition">
                👥 Ecossistema Completo (0 a 17)
            </button>
            <button type="button" id="tab-qualidade" role="tab" aria-controls="panel-qualidade" aria-selected="false" data-tab-target="panel-qualidade" class="tab-btn flex-1 sm:flex-none whitespace-nowrap rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition">
                📈 Qualidade & IDEB (MEC / SAEB)
            </button>
            <button type="button" id="tab-infancia" role="tab" aria-controls="panel-infancia" aria-selected="false" data-tab-target="panel-infancia" class="tab-btn flex-1 sm:flex-none whitespace-nowrap rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition">
                🏛️ Contexto Municipal: Primeira Infância (Dados IBGE)
            </button>
            <button type="button" id="tab-transparencia" role="tab" aria-controls="panel-transparencia" aria-selected="false" data-tab-target="panel-transparencia" class="tab-btn flex-1 sm:flex-none whitespace-nowrap rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition">
                📋 Transparência & Finanças (MROSC)
            </button>
        </div>

        {{-- Grid de 5 KPIs Focados no Impacto Central da Escola --}}
        <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5" aria-label="Indicadores principais">
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Público Alvo Contraturno',
                'valor' => '2.502',
                'badge' => 'Ensino Fundamental (6 a 14 anos)',
                'contexto' => 'Estudantes da rede pública em turno único sem atividades extracurriculares.',
                'classe' => 'bg-white border-brand-300 ring-2 ring-brand-500/20 shadow-sm',
                'cor_badge' => 'bg-brand-100 text-brand-800 font-bold',
                'cor_valor' => 'text-brand-600 font-display',
                'cor_titulo' => 'text-slate-600 font-semibold',
                'cor_contexto' => 'text-slate-600',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Distorção Idade-Série',
                'valor' => '16,4%',
                'badge' => 'Salto nos Anos Finais (INEP)',
                'contexto' => 'Estudantes com 2+ anos de atraso escolar: risco direto de reprovação e abandono.',
                'classe' => 'bg-white border-amber-200',
                'cor_badge' => 'bg-amber-100 text-amber-800',
                'cor_valor' => 'text-amber-600 font-display',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'População Escolar (0-17)',
                'valor' => number_format($resumo_executivo['populacao_total_escolar_0a17'], 0, ',', '.'),
                'badge' => 'IBGE · Censo 2022',
                'contexto' => 'Crianças e jovens em idade escolar obrigatória em Guapó.',
                'classe' => 'bg-white border-slate-200/80',
                'cor_badge' => 'bg-slate-100 text-slate-700',
                'cor_valor' => 'text-slate-900 font-display',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Carência Estrutural Local',
                'valor' => '58,3%',
                'badge' => 'Rede Pública Municipal',
                'contexto' => 'Escolas sem pátio coberto, espaço de cultura ou parque infantil adaptado.',
                'classe' => 'bg-white border-slate-200/80',
                'cor_badge' => 'bg-rose-100 text-rose-700',
                'cor_valor' => 'text-rose-600 font-display',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Obra do Auditório Multiuso',
                'valor' => '68,5%',
                'badge' => 'Fase 3 em Andamento',
                'contexto' => 'R$ 342.500 arrecadados para acabamentos, acústica e laboratório maker.',
                'classe' => 'bg-white border-hope-200',
                'cor_badge' => 'bg-hope-100 text-hope-800',
                'cor_valor' => 'text-hope-600 font-display',
            ])
        </section>

        {{-- Aba 1 (Padrão Ativa): Solução da Escola - Educação Integral & Contraturno --}}
        <section role="tabpanel" id="panel-contraturno" aria-labelledby="tab-contraturno" data-tab-panel>
            {{-- Bloco Explicativo: O que é Distorção Idade-Série --}}
            <div class="mt-8 rounded-3xl border border-amber-200 bg-gradient-to-br from-amber-50/80 via-white to-white p-6 sm:p-8 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="px-3.5 py-1 rounded-full text-xs font-bold bg-amber-200 text-amber-900">Conceito Pedagógico Chave</span>
                    <span class="px-3.5 py-1 rounded-full text-xs font-bold bg-brand-100 text-brand-800">Causa Central do Projeto</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                    O que é a Distorção Idade-Série e por que ela é o maior alerta de Guapó?
                </h2>
                <div class="mt-4 grid gap-6 md:grid-cols-2 text-sm leading-relaxed text-slate-700">
                    <div class="space-y-3">
                        <p>
                            A <strong>Distorção Idade-Série</strong> é o indicador oficial do MEC/INEP que mede a proporção de alunos que estão com <strong>2 anos ou mais de atraso</strong> em relação à idade esperada para o ano escolar que estão cursando.
                        </p>
                        <p>
                            Por exemplo: aos <strong>11 anos</strong>, o estudante deveria ingressar no 6º ano do Ensino Fundamental. Se ele tem 13, 14 ou 15 anos nessa etapa (devido a repetências ou interrupções), ele é classificado tecnicamente em distorção idade-série.
                        </p>
                        <div class="p-4 rounded-2xl bg-amber-100/60 border border-amber-200 text-amber-900 font-medium">
                            ⚠️ <strong>O Fenômeno em Guapó:</strong> Nos anos iniciais (1º ao 5º ano), a distorção é de apenas <strong>7,8%</strong>. Porém, nos anos finais (6º ao 9º ano), ela mais que dobra e atinge <strong>16,4%</strong>. Quase 1 em cada 6 adolescentes está defasado!
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                            <h3 class="font-bold text-slate-900 font-display text-base mb-2">Por que o Contraturno Escolar é a Resposta?</h3>
                            <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-3">
                                As <strong>2.502 crianças de Guapó</strong> estudam em meio período. No contraturno, sem reforço ou supervisão, os déficits de alfabetização e matemática básica vão se acumulando silenciosamente até explodirem em reprovação e evasão no 6º ao 9º ano.
                            </p>
                            <ul class="space-y-2 text-xs text-slate-700">
                                <li class="flex items-start gap-2">
                                    <span class="text-hope-600 font-bold">✓</span>
                                    <span><strong>Reforço Escolar Diário:</strong> Alfabetização sólida e nivelamento em matemática.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-hope-600 font-bold">✓</span>
                                    <span><strong>Laboratório Maker & Robótica:</strong> Atividades práticas que despertam a paixão pelo estudo.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-hope-600 font-bold">✓</span>
                                    <span><strong>Auditório Multiuso:</strong> Teatro, música, xadrez e assembleias comunitárias.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detalhes da Solução da Escola e Auditório --}}
            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-700 bg-brand-50 px-3 py-1 rounded-full">
                        Ativo de Alta Utilidade Comunitária
                    </span>
                    <h3 class="font-display font-bold text-xl text-slate-900 mt-3 mb-2">
                        O Papel do Auditório Multiuso
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        O auditório não é um espaço ocioso: ele opera nos 3 turnos do dia para maximizar o retorno social:
                    </p>
                    <div class="space-y-3 text-sm">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-start gap-3">
                            <span class="text-xl">☀️</span>
                            <div>
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">Tarde: Contraturno Escolar (6 a 14 anos)</p>
                                <p class="text-xs text-slate-500">Aulas de música, palestras educativas, reforço coletivo e apresentações de robótica.</p>
                            </div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-start gap-3">
                            <span class="text-xl">🌙</span>
                            <div>
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">Noite e Sábados: Fortalecimento das Famílias</p>
                                <p class="text-xs text-slate-500">Cursos profissionalizantes para pais, assembleias comunitárias e eventos culturais.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-hope-700 bg-hope-50 px-3 py-1 rounded-full">
                        Metodologia & Teoria da Mudança
                    </span>
                    <h3 class="font-display font-bold text-xl text-slate-900 mt-3 mb-2">
                        Do Diagnóstico à Transformação de Vidas
                    </h3>
                    <p class="text-slate-600 text-sm leading-relaxed mb-4">
                        O projeto não dispersa recursos: concentra energia onde o impacto preventivo é comprovadamente maior.
                    </p>
                    <div class="p-4 rounded-2xl bg-brand-50/60 border border-brand-200/70 text-xs sm:text-sm text-brand-900 space-y-2">
                        <p><strong>ODS 4 da ONU (Educação de Qualidade):</strong> Assegurar a educação inclusiva, equitativa e de qualidade, garantindo que nenhum aluno fique para trás.</p>
                        <p><strong>Prevenção da Evasão:</strong> O contraturno ataca a vulnerabilidade social e a evasão no momento em que ela mais se intensifica (11 a 14 anos).</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Aba 2: Ecossistema Completo (0 a 17 anos) --}}
        <section role="tabpanel" id="panel-ecossistema" aria-labelledby="tab-ecossistema" data-tab-panel hidden>
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
        </section>

        {{-- Aba 3: Qualidade Educacional --}}
        @include('dashboard.components.section-quality')

        {{-- Aba 4: Contexto Municipal - Primeira Infância (Dados IBGE) --}}
        <section role="tabpanel" id="panel-infancia" aria-labelledby="tab-infancia" data-tab-panel hidden>
            {{-- Disclaimer explícito: Indicador Municipal vs Solução da Escola --}}
            <div class="mt-8 p-5 rounded-3xl bg-blue-50 border border-blue-200 text-blue-900 flex flex-col sm:flex-row items-start gap-4">
                <span class="text-3xl shrink-0">ℹ️</span>
                <div>
                    <h3 class="font-bold text-base sm:text-lg font-display text-blue-950 mb-1">
                        Nota Técnica e Institucional sobre estes Dados
                    </h3>
                    <p class="text-xs sm:text-sm text-blue-800 leading-relaxed">
                        Os dados abaixo representam o <strong>diagnóstico de carência da rede pública municipal de Guapó</strong> segundo o Censo 2022 do IBGE e Censo Escolar INEP.
                        A <strong>Escola Social NÃO é creche e NÃO oferta berçário</strong>. Apresentamos este diagnóstico para subsidiar o debate público e demonstrar a vulnerabilidade histórica do município, enquanto nossa infraestrutura física concentra foco no <strong>Programa de Educação Integral, Contraturno Escolar (6 a 14 anos)</strong> e no <strong>Auditório Multiuso</strong>.
                    </p>
                </div>
            </div>

            {{-- Gráficos do Diagnóstico Municipal da Primeira Infância --}}
            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                @include('dashboard.components.chart-card', [
                    'id' => 'chartDeficit',
                    'titulo' => 'Déficit Municipal de Vagas em Creche (0 a 3 anos)',
                    'subtitulo' => 'Composição da população municipal: 1.068 crianças residentes vs 289 vagas atendidas.',
                    'fonte' => 'IBGE 2022 · INEP 2025',
                    'altura' => 'h-80',
                    'legenda' => 'Déficit público municipal de 779 vagas (72,9% desatendidas na rede pública de Guapó).',
                ])
                @include('dashboard.components.chart-card', [
                    'id' => 'chartMetaPNE',
                    'titulo' => 'Meta 1 do PNE no Município de Guapó',
                    'subtitulo' => 'Piso legal de 50% exige 534 vagas no município; rede pública oferta 289.',
                    'fonte' => 'Lei 13.005/2014',
                    'altura' => 'h-80',
                    'legenda' => 'Faltam 245 vagas para a rede pública municipal atingir o piso legal de 534 vagas (Meta 1).',
                ])
            </div>

            {{-- Storytelling Bloco A --}}
            <div class="mt-8 rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="storytelling-a-titulo">
                <h2 id="storytelling-a-titulo" class="text-xl font-bold text-slate-900 font-display">A Realidade Municipal da Primeira Infância (0 a 3 anos)</h2>
                <div class="mt-4 grid gap-6 md:grid-cols-2 text-sm leading-relaxed text-slate-600">
                    <div>
                        <p>
                            Guapó possui <strong>1.068 crianças de 0 a 3 anos</strong> segundo o Censo IBGE 2022. Na rede municipal pública, apenas <strong>289 crianças</strong> estão atendidas em creches, gerando um déficit de <strong>779 vagas</strong> (72,9% desatendidas).
                        </p>
                    </div>
                    <div>
                        <p>
                            Para cumprir a Meta 1 do PNE (piso de 50%), o município necessitaria de <strong>534 vagas</strong>. A ausência dessas vagas públicas pressiona as famílias e sobrecarrega mães solo na cidade.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Aba 5: Transparência & Finanças (MROSC) --}}
        <section role="tabpanel" id="panel-transparencia" aria-labelledby="tab-transparencia" data-tab-panel hidden>
            <div class="mt-8 rounded-3xl border border-brand-200/80 bg-gradient-to-br from-brand-50/70 via-white to-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="inline-flex items-center rounded-full bg-brand-100 px-3.5 py-1 text-xs font-bold text-brand-800">
                        Lei Federal 13.019/2014 (MROSC)
                    </span>
                    <span class="inline-flex items-center rounded-full bg-hope-100 px-3.5 py-1 text-xs font-bold text-hope-800">
                        Conta Corrente Vinculada Exclusiva
                    </span>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3.5 py-1 text-xs font-semibold text-slate-700">
                        Transparência Ativa
                    </span>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 font-display">
                    Transparência Contábil & Prestação de Contas da Obra
                </h2>
                <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600">
                    Em conformidade com os princípios do Marco Regulatório das Organizações da Sociedade Civil (MROSC),
                    todos os recursos captados para a Fase 3 da obra são movimentados em conta bancária exclusiva,
                    com segregação patrimonial estrita e publicação periódica de comprovantes fiscais.
                </p>

                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Meta Orçamentária Fase 3</p>
                        <p class="text-2xl font-extrabold text-slate-900 mt-1 font-display">R$ 500.000</p>
                        <p class="text-xs text-slate-400 mt-1">Acabamentos e instalações</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                        <p class="text-xs font-bold text-hope-700 uppercase tracking-wider">Arrecadado até o momento</p>
                        <p class="text-2xl font-extrabold text-hope-600 mt-1 font-display">R$ 342.500</p>
                        <p class="text-xs text-slate-400 mt-1">68,5% do total da etapa</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                        <p class="text-xs font-bold text-brand-700 uppercase tracking-wider">Cotas Apadrinhadas</p>
                        <p class="text-2xl font-extrabold text-brand-600 mt-1 font-display">184 cotas</p>
                        <p class="text-xs text-slate-400 mt-1">Pessoas físicas e jurídicas</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Saldo para Conclusão</p>
                        <p class="text-2xl font-extrabold text-amber-600 mt-1 font-display">R$ 157.500</p>
                        <p class="text-xs text-slate-400 mt-1">Necessário para entrega final</p>
                    </div>
                </div>

                {{-- Tabela de Destinação de Recursos da Fase 3 --}}
                <div class="mt-10">
                    <h3 class="font-display font-bold text-lg text-slate-900 mb-4">Destinação Orçamentária da Obra (Fase 3)</h3>
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-6 py-3.5 font-bold uppercase tracking-wider">Rubrica de Despesa</th>
                                    <th class="px-6 py-3.5 font-bold uppercase tracking-wider">Participação</th>
                                    <th class="px-6 py-3.5 font-bold uppercase tracking-wider">Valor Previsto</th>
                                    <th class="px-6 py-3.5 font-bold uppercase tracking-wider">Status Físico</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-slate-900">Materiais de Acabamento (Pisos, tintas, forro)</td>
                                    <td class="px-6 py-4">40%</td>
                                    <td class="px-6 py-4">R$ 200.000</td>
                                    <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-hope-100 text-hope-800">85% Entregue</span></td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-slate-900">Mão de Obra Técnica Especializada</td>
                                    <td class="px-6 py-4">35%</td>
                                    <td class="px-6 py-4">R$ 175.000</td>
                                    <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-100 text-brand-800">70% Executado</span></td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-slate-900">Instalações Elétricas, LED e Climatização</td>
                                    <td class="px-6 py-4">15%</td>
                                    <td class="px-6 py-4">R$ 75.000</td>
                                    <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">55% Executado</span></td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-slate-900">Mobiliário Pedagógico & Equipamentos Maker</td>
                                    <td class="px-6 py-4">10%</td>
                                    <td class="px-6 py-4">R$ 50.000</td>
                                    <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Em Cotação</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Download e Selos --}}
                <div class="mt-8 flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-200">
                    <div class="flex items-center gap-3">
                        <a href="/api/indicadores/guapo/download" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white text-xs sm:text-sm font-bold rounded-xl hover:bg-brand-700 transition-colors shadow-sm">
                            <span>📥</span> Baixar Diagnóstico Consolidado (JSON)
                        </a>
                        <a href="https://github.com/fabiooliveir/projeto-social" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white text-xs sm:text-sm font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                            <span>💻</span> Código & Dados no GitHub
                        </a>
                    </div>
                    <p class="text-xs text-slate-500">
                        Registro sob CNPJ associativo específico com escrituração digital ECD/ECF.
                    </p>
                </div>
            </div>
        </section>

        {{-- Evolução Histórica (Visão Compartilhada) --}}
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

        {{-- Diagnóstico Integrado --}}
        <section class="mt-10 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="interpretacao-titulo">
            <h2 id="interpretacao-titulo" class="text-xl font-bold text-slate-900 font-display">Interpretação e Diagnóstico Consolidado</h2>
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
                        O grande contingente populacional está concentrado nos <strong>{{ number_format($resumo_executivo['populacao_contraturno_6a14'], 0, ',', '.') }} estudantes do Ensino Fundamental</strong>, que estudam em meio período e representam o público imediato de transformação da Escola Social.
                    </p>
                </div>
                <div class="space-y-4 text-sm leading-relaxed text-slate-600">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="font-bold text-slate-900 font-display">Transição Pré-Escola → 1º Ano do Ensino Fundamental</p>
                        <p class="mt-1 text-xs sm:text-sm">
                            Das <strong>{{ number_format($resumo_executivo['populacao_4a5_anos'], 0, ',', '.') }} crianças de 4 e 5 anos</strong>,
                            <strong>{{ number_format($resumo_executivo['matriculas_1ano_fundamental_2025'], 0, ',', '.') }}</strong> ingressaram no 1º ano em 2025 —
                            fluxo equivalente a <strong>{{ number_format($resumo_executivo['taxa_transicao_pre_fundamental_pct'], 2, ',', '.') }}%</strong>.
                            O gargalo comprovadamente não é a evasão pré-escolar, e sim a falta de contraturno de qualidade ao longo de todo o Ensino Fundamental.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-brand-200 bg-brand-50/70 p-4">
                        <p class="font-bold text-brand-900 font-display">Conclusão para Apoio & Apadrinhamento</p>
                        <p class="mt-1 text-xs sm:text-sm text-brand-800">
                            <strong>Ativar o contraturno integral</strong> para os
                            {{ number_format($resumo_executivo['populacao_contraturno_6a14'], 0, ',', '.') }} estudantes do Fundamental
                            por meio do novo centro comunitário e salas pedagógicas, blindando a infância e juventude contra o atraso escolar e a vulnerabilidade social.
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