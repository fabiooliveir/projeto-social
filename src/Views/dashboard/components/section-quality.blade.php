@php
    $num = static fn (float|int $v, int $c = 1): string => number_format((float) $v, $c, ',', '.');

    $idebAI = $qualidade['ideb']['anos_iniciais'] ?? null;
    $idebAF = $qualidade['ideb']['anos_finais'] ?? null;
    $fluxo  = $qualidade['fluxo_e_docencia'] ?? null;
    $infra  = $qualidade['infraestrutura_resumo'] ?? null;
    $diag   = $qualidade['diagnostico_qualitativo'] ?? null;
@endphp

@if (!empty($qualidade))
<section role="tabpanel" id="panel-qualidade" aria-labelledby="tab-qualidade" data-tab-panel hidden>
    <div class="mt-8 rounded-2xl border border-teal-100 bg-gradient-to-br from-teal-50 via-white to-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-teal-100 px-3 py-1 text-xs font-bold text-teal-800">INEP · SAEB</span>
            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Censo Escolar 2023</span>
            <span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Aprendizado, fluxo e infraestrutura</span>
        </div>
        <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-900">Além das Vagas: O Desafio da Qualidade</h2>
        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600">
            As vagas resolvem o <em>acesso</em>; a qualidade decide o <em>resultado</em>. Esta seção revela o que o
            IDEB, o fluxo escolar e o Censo de Infraestrutura do INEP mostram sobre a rede pública de Guapó — e como a
            <strong>Nossa Escola Social</strong>, com seu auditório multiuso, ataca diretamente cada fragilidade.
        </p>
    </div>

    <section class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicadores de qualidade">
        @if ($idebAI)
            @include('dashboard.components.kpi-card', [
                'titulo' => 'IDEB Anos Iniciais (5º ano)',
                'valor' => $num($idebAI['nota_recente'] ?? 0),
                'badge' => 'Meta MEC ' . $num($idebAI['meta_recente'] ?? 0),
                'contexto' => 'Goiás ' . $num($idebAI['benchmark']['goias'] ?? 0) . ' · Brasil ' . $num($idebAI['benchmark']['brasil'] ?? 0) . ' — SAEB 2023.',
                'classe' => 'bg-white border-slate-200',
                'cor_badge' => 'bg-blue-100 text-blue-700',
                'cor_valor' => 'text-blue-700',
            ])
        @endif
        @if ($idebAF)
            @include('dashboard.components.kpi-card', [
                'titulo' => 'IDEB Anos Finais (9º ano)',
                'valor' => $num($idebAF['nota_recente'] ?? 0),
                'badge' => 'Meta MEC ' . $num($idebAF['meta_recente'] ?? 0),
                'contexto' => 'Goiás ' . $num($idebAF['benchmark']['goias'] ?? 0) . ' · Brasil ' . $num($idebAF['benchmark']['brasil'] ?? 0) . ' — SAEB 2023.',
                'classe' => 'bg-white border-slate-200',
                'cor_badge' => 'bg-indigo-100 text-indigo-700',
                'cor_valor' => 'text-indigo-600',
            ])
        @endif
        @if ($fluxo)
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Distorção Idade-Série (Anos Finais)',
                'valor' => $num($fluxo['distorcao_idade_serie_anos_finais_pct'] ?? 0) . '%',
                'badge' => 'Salto de ' . $num($fluxo['distorcao_idade_serie_anos_iniciais_pct'] ?? 0) . '% → ' . $num($fluxo['distorcao_idade_serie_anos_finais_pct'] ?? 0) . '%',
                'contexto' => 'Estudantes com 2+ anos de atraso escolar: o gargalo que empurra à evasão nos anos finais.',
                'classe' => 'bg-white border-rose-200',
                'cor_badge' => 'bg-rose-100 text-rose-700',
                'cor_valor' => 'text-rose-600',
            ])
            @include('dashboard.components.kpi-card', [
                'titulo' => 'Adequação da Formação Docente',
                'valor' => $num($fluxo['adequacao_formacao_docente_grupo1_pct'] ?? 0) . '%',
                'badge' => 'AFD grupo 1 · INEP',
                'contexto' => 'Professores com formação superior compatível com a turma e a disciplina que lecionam.',
                'classe' => 'bg-white border-slate-200',
                'cor_badge' => 'bg-teal-100 text-teal-700',
                'cor_valor' => 'text-teal-700',
            ])
        @endif
    </section>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        @include('dashboard.components.chart-card', [
            'id' => 'chartIdeb',
            'titulo' => 'Trajetória do IDEB de Guapó vs Metas do MEC',
            'subtitulo' => 'Notas do SAEB e metas projetadas para os anos iniciais e finais do Ensino Fundamental.',
            'fonte' => 'INEP · SAEB 2017-2023',
            'altura' => 'h-80',
            'legenda' => 'Linhas tracejadas são as metas oficiais do MEC; as contínuas, o desempenho real de Guapó.',
        ])
        @include('dashboard.components.chart-card', [
            'id' => 'chartDistorcaoFluxo',
            'titulo' => 'Gargalo do Fluxo Escolar: Distorção e Reprovação',
            'subtitulo' => 'O atraso escolar dobra na passagem dos anos iniciais para os finais.',
            'fonte' => 'INEP · Indicadores Educacionais',
            'altura' => 'h-80',
            'legenda' => 'O contraturno do auditório multiuso é a resposta para reter, recuperar e acelerar estes estudantes.',
        ])
    </div>

    <div class="mt-10">
        @include('dashboard.components.chart-card', [
            'id' => 'chartInfraestrutura',
            'titulo' => 'Infraestrutura das Unidades Municipais',
            'subtitulo' => 'Cobertura dos itens essenciais nas ' . ($infra['total_unidades_avaliadas'] ?? 0) . ' unidades do Censo Escolar.',
            'fonte' => 'INEP · Censo Escolar',
            'altura' => 'h-80 sm:h-96',
            'legenda' => 'Em vermelho, as maiores carências: berçário/lactário e parque infantil.',
        ])
    </div>

    <section class="mt-10 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="qualidade-comparativo-titulo">
        <div class="border-b border-slate-200 bg-slate-900 px-6 py-5 sm:px-8">
            <h2 id="qualidade-comparativo-titulo" class="text-lg font-bold text-white">Padrões de Qualidade MEC vs Nossa Escola Social</h2>
            <p class="mt-1 text-sm text-slate-300">Parâmetros Nacionais de Qualidade da Educação Infantil (MEC) confrontados com as fragilidades reais da rede de Guapó.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold text-slate-500">Dimensão</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-rose-600">Rede convencional em Guapó</th>
                        <th scope="col" class="px-6 py-3 font-semibold text-teal-700">Nossa Escola Social</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <th scope="row" class="px-6 py-4 align-top font-semibold text-slate-800">Relação Adulto/Criança</th>
                        <td class="px-6 py-4 align-top text-slate-600">Pré-escola com média de {{ $num($fluxo['media_alunos_turma']['pre_escola'] ?? 21.8) }} alunos por turma — cuidado diluído e sem atenção individualizada.</td>
                        <td class="px-6 py-4 align-top text-teal-800">Turmas reduzidas com atenção personalizada e monitoramento individual do desenvolvimento de cada criança.</td>
                    </tr>
                    <tr>
                        <th scope="row" class="px-6 py-4 align-top font-semibold text-slate-800">Espaços Lúdicos & Primeira Infância</th>
                        <td class="px-6 py-4 align-top text-slate-600">Somente {{ $num($infra['itens']['com_bercario_lactario_creche_pct'] ?? 0) }}% das unidades têm berçário/lactário e {{ $num($infra['itens']['com_parque_infantil_ludico_pct'] ?? 0) }}% têm parque infantil.</td>
                        <td class="px-6 py-4 align-top text-teal-800">Berçário climatizado, lactário e parque sensorial adaptado para o desenvolvimento psicomotor da primeira infância.</td>
                    </tr>
                    <tr>
                        <th scope="row" class="px-6 py-4 align-top font-semibold text-slate-800">Auditório Multiuso</th>
                        <td class="px-6 py-4 align-top text-slate-600">Ensino restrito à sala de aula, sem espaço próprio para artes, música e convivência comunitária.</td>
                        <td class="px-6 py-4 align-top text-teal-800">Auditório multiuso: artes, música, contação de histórias, estímulo socioemocional e eventos comunitários no contraturno.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-10 rounded-2xl border border-teal-100 bg-gradient-to-br from-teal-50 via-white to-white p-6 shadow-sm sm:p-8" aria-labelledby="auditorio-qualidade-titulo">
        <h2 id="auditorio-qualidade-titulo" class="text-xl font-bold text-teal-900">Auditório Multiuso: o Diferencial que Transforma Qualidade em Resultado</h2>
        <div class="mt-4 grid gap-6 md:grid-cols-2">
            <div class="space-y-3 text-sm leading-relaxed text-slate-600">
                <p>
                    A neurociência da primeira infância é categórica: habilidades socioemocionais — autoconhecimento,
                    colaboração e autorregulação — são o alicerce do aprendizado. A OECD e a BNCC apontam as artes, a
                    música e o brincar estruturado como os caminhos mais eficazes para desenvolvê-las. Sem espaço
                    adequado para isso, a criança deixa de construir o repertório que a protege da reprovação e da evasão.
                </p>
                <p>
                    Em Guapó, a distorção idade-série salta de {{ $num($fluxo['distorcao_idade_serie_anos_iniciais_pct'] ?? 0) }}% nos
                    anos iniciais para {{ $num($fluxo['distorcao_idade_serie_anos_finais_pct'] ?? 0) }}% nos anos finais: o aluno que reprova
                    é o mesmo que perde o vínculo com a escola. O <strong class="text-teal-800">auditório multiuso</strong> ataca a
                    causa — acolhimento, estímulo e contraturno — não apenas o sintoma.
                </p>
            </div>
            <div class="space-y-4 text-sm">
                <div class="rounded-xl border border-teal-100 bg-white p-4">
                    <p class="font-semibold text-teal-900">Diferencial para doadores e editais</p>
                    <ul class="mt-2 space-y-2 leading-relaxed text-slate-600">
                        <li><strong>Conteúdo de qualidade comprovada:</strong> IDEB {{ $num($idebAI['nota_recente'] ?? 0) }} a {{ $num($idebAF['nota_recente'] ?? 0) }} com meta MEC de {{ $num($idebAI['meta_recente'] ?? 0) }} a {{ $num($idebAF['meta_recente'] ?? 0) }} — a escola nova mira o padrão que a rede ainda não alcança.</li>
                        <li><strong>Combate à evasão:</strong> cada ponto de distorção recuperado pelo contraturno é uma vida escolar resgatada.</li>
                        <li><strong>Infraestrutura que o Censo revela:</strong> berçário, lactário e parque sensorial — exatamente os itens mais escassos ({{ $num($infra['itens']['com_bercario_lactario_creche_pct'] ?? 0) }}% e {{ $num($infra['itens']['com_parque_infantil_ludico_pct'] ?? 0) }}%).</li>
                    </ul>
                </div>
                @if ($diag)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="font-semibold text-amber-800">Leitura técnica do INEP</p>
                    <p class="mt-1 leading-relaxed text-slate-600">{{ $diag['impacto_projeto_social'] ?? '' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif