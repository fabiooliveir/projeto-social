<div class="chart-card rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">{{ $titulo }}</h3>
            @if (!empty($subtitulo))
                <p class="mt-1 text-sm text-slate-500">{{ $subtitulo }}</p>
            @endif
        </div>
        @if (!empty($fonte))
            <span class="shrink-0 inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $fonte }}</span>
        @endif
    </div>
    <div class="relative mt-6 {{ $altura ?? 'h-72 sm:h-80' }} w-full">
        <canvas id="{{ $id }}" role="img" aria-label="{{ $titulo }}"></canvas>
    </div>
    @if (!empty($legenda))
        <p class="mt-4 text-sm text-slate-500">{{ $legenda }}</p>
    @endif
</div>