@extends('layouts.app')

@section('title', 'Diagnóstico da Educação Infantil - Guapó-GO | Projeto Social')

@section('content')
<div class="min-h-screen">
    <header class="bg-slate-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-cyan-400">Projeto Social · Diagnóstico da Educação Infantil</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">Guapó – GO ({{ $municipio['codigo_ibge'] }})</h1>
                    <p class="mt-2 max-w-2xl text-slate-300">
                        Análise do déficit de vagas em creches e o cumprimento da Meta 1 do PNE
                        (Lei 13.005/2014) para a primeira infância no município.
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
        {{-- KPIs --}}
        <section class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicadores principais">
            @include('dashboard.components.kpi-card', [
                'titulo' => 'População Infantil (0 a 3 anos)',
                'valor' => number_format($resumo_executivo['populacao_0a3_anos'], 0, ',', '.'),
                'badge' => 'IBGE · Censo 2022',
                'contexto' => 'Crianças residentes na faixa etária de referência para creche.',
                'cor_badge' => 'bg-sky-100 text-sky-700',
                'cor_valor' => 'text-sky-700',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Vagas Públicas em Creche',
                'valor' => number_format($resumo_executivo['vagas_creche_atual_2025'], 0, ',', '.'),
                'badge' => 'INEP · Censo Escolar 2025',
                'contexto' => 'Matrículas ativas na rede municipal de ensino de Guapó.',
                'cor_badge' => 'bg-indigo-100 text-indigo-700',
                'cor_valor' => 'text-indigo-700',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Déficit Estimado',
                'valor' => number_format($resumo_executivo['deficit_vagas_creche'], 0, ',', '.'),
                'badge' => 'Crianças fora da creche',
                'contexto' => number_format($resumo_executivo['taxa_desatendimento_creche_pct'], 1, ',', '.') . '% de desatendimento na faixa de 0 a 3 anos.',
                'cor_badge' => 'bg-red-100 text-red-700',
                'cor_valor' => 'text-red-600',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Meta 1 do PNE (Piso 50%)',
                'valor' => number_format($resumo_executivo['meta_pne_minima_50pct'], 0, ',', '.'),
                'badge' => 'Lei 13.005/2014',
                'contexto' => 'Faltam ' . number_format($resumo_executivo['vagas_faltantes_para_pne'], 0, ',', '.') . ' vagas para atingir o piso legal de atendimento.',
                'cor_badge' => 'bg-amber-100 text-amber-700',
                'cor_valor' => 'text-amber-600',
            ])
        </section>

        {{-- Gráficos --}}
        <div class="mt-10">
            @include('dashboard.components.charts-grid')
        </div>

        {{-- Interpretação --}}
        <section class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="interpretacao-titulo">
            <h2 id="interpretacao-titulo" class="text-xl font-bold text-slate-900">Interpretação e Diagnóstico</h2>
            <div class="mt-4 grid gap-6 md:grid-cols-2">
                <div class="space-y-3 text-sm leading-relaxed text-slate-600">
                    <p>
                        O <strong>Censo Demográfico 2022</strong> (IBGE) contabiliza
                        <strong>{{ number_format($resumo_executivo['populacao_0a3_anos'], 0, ',', '.') }} crianças de 0 a 3 anos</strong> em Guapó-GO.
                        Desse total, apenas <strong>{{ number_format($resumo_executivo['vagas_creche_atual_2025'], 0, ',', '.') }} estão matriculadas em creche municipal</strong>,
                        o que revela um déficit estimado de
                        <strong>{{ number_format($resumo_executivo['deficit_vagas_creche'], 0, ',', '.') }} vagas</strong>
                        ({{ number_format($resumo_executivo['taxa_desatendimento_creche_pct'], 1, ',', '.') }}% de desatendimento).
                    </p>
                    <p>
                        A <strong>Meta 1 do PNE</strong> exige o atendimento de ao menos
                        <strong>50% da demanda por creche</strong> ({{ number_format($resumo_executivo['meta_pne_minima_50pct'], 0, ',', '.') }} vagas).
                        A oferta atual fica <strong>{{ number_format($resumo_executivo['vagas_faltantes_para_pne'], 0, ',', '.') }} vagas aquém</strong> do piso legal,
                        configurando um cenário de escassez aguda do direito à educação infantil.
                    </p>
                </div>
                <div class="space-y-4 text-sm leading-relaxed text-slate-600">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="font-semibold text-slate-800">Pré-escola (4 e 5 anos)</p>
                        <p class="mt-1">
                            {{ number_format($resumo_executivo['vagas_pre_escola_atual_2025'], 0, ',', '.') }} matrículas para
                            {{ number_format($resumo_executivo['populacao_4a5_anos'], 0, ',', '.') }} crianças —
                            cobertura de <strong>{{ number_format($resumo_executivo['taxa_cobertura_pre_escola_pct'], 2, ',', '.') }}%</strong>,
                            próximo da universalização.
                        </p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="font-semibold text-amber-800">Conclusão para captação</p>
                        <p class="mt-1">
                            O gargalo está na <strong>creche (0 a 3 anos)</strong>. Ampliar a oferta pública em
                            {{ number_format($resumo_executivo['vagas_faltantes_para_pne'], 0, ',', '.') }} vagas é o piso mínimo
                            para cumprir a legislação e garantir às famílias de Guapó o direito à primeira infância.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-6 text-center text-xs text-slate-500 sm:px-6 lg:px-8">
            Fontes: IBGE · Censo Demográfico 2022 (Tabela 9514) e INEP · Censo Escolar (2008–2025).
            Cache consolidado em <code>storage/data/guapo_education_cache.json</code>.
        </div>
    </footer>
</div>
@endsection