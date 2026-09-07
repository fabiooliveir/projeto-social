<section class="space-y-6" aria-label="Gráficos interativos">
    @include('dashboard.components.chart-card', [
        'id' => 'chartEvolucao',
        'titulo' => 'Evolução Histórica das Matrículas',
        'subtitulo' => 'Série temporal 2008–2025 das matrículas em creche e pré-escola municipais.',
        'fonte' => 'INEP · Censo Escolar',
        'altura' => 'h-72 sm:h-80 lg:h-96',
    ])

    <div class="grid gap-6 lg:grid-cols-2">
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
</section>