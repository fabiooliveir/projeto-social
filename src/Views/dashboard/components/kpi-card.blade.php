<div class="{{ $classe ?? 'bg-white border-slate-200' }} relative overflow-hidden rounded-2xl border p-6 shadow-sm">
    <p class="text-xs font-semibold uppercase tracking-wider {{ $cor_titulo ?? 'text-slate-500' }}">{{ $titulo }}</p>
    <p class="mt-3 text-4xl font-extrabold tracking-tight {{ $cor_valor ?? 'text-slate-900' }}">{{ $valor }}</p>
    <div class="mt-4 flex items-center gap-2">
        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $cor_badge ?? 'bg-slate-100 text-slate-700' }}">{{ $badge }}</span>
    </div>
    @if (!empty($contexto))
        <p class="mt-4 text-sm leading-relaxed {{ $cor_contexto ?? 'text-slate-600' }}">{{ $contexto }}</p>
    @endif
</div>