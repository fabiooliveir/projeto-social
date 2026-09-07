<section id="cotas" class="py-16 sm:py-20 bg-gradient-to-b from-slate-900 to-slate-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-4">Catálogo de Cotas de Apadrinhamento da Obra</h2>
            <p class="text-slate-400 max-w-2xl mx-auto">
                Escolha uma cota, apadrinhe e receba atualizações sobre o impacto direto da sua contribuição na obra.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($cotas as $cota)
            <div class="cota-card relative bg-slate-800/50 rounded-2xl p-6 border border-slate-700/50 hover:border-slate-600 transition-all hover:-translate-y-1 cursor-pointer"
                 data-cota-id="{{ $cota['id'] }}"
                 data-cota-nome="{{ $cota['nome'] }}"
                 data-cota-valor="{{ $cota['valor'] }}">
                <div class="absolute top-4 right-4 text-3xl">{{ $cota['icone'] }}</div>
                <div class="mb-4 pr-10">
                    <h3 class="font-bold text-lg text-white">{{ $cota['nome'] }}</h3>
                    <p class="text-sm text-slate-400 mt-1">{{ $cota['desc'] }}</p>
                </div>
                <div class="flex items-baseline gap-2 mb-3">
                    <span class="text-3xl font-extrabold text-emerald-400">R$ {{ number_format($cota['valor'], 0, ',', '.') }}</span>
                    <span class="text-sm text-slate-500">/ cota</span>
                </div>
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-500">{{ $cota['arrecadado'] }} de {{ $cota['meta'] }} cotas</span>
                        <span class="text-slate-500">{{ $cota['meta'] > 0 ? round(($cota['arrecadado'] / $cota['meta']) * 100) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all"
                             style="width: {{ $cota['meta'] > 0 ? round(($cota['arrecadado'] / $cota['meta']) * 100) : 0 }}%"></div>
                    </div>
                </div>
                <button class="abrir-modal-cota w-full py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors"
                        data-cota-id="{{ $cota['id'] }}">
                    Apadrinhar esta cota
                </button>
            </div>
            @endforeach
        </div>

        <p class="text-center text-sm text-slate-500 mt-8">
            Cotas sujeitas a disponibilidade. Após confirmação, você receberá os dados para pagamento via PIX.
        </p>
    </div>
</section>

<div id="modal-cota" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="modal-cota-overlay"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8 text-slate-900">
        <button id="modal-cota-close" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
        <div class="text-center mb-6">
            <div id="modal-cota-icon" class="text-4xl mb-2"></div>
            <h3 id="modal-cota-title" class="font-bold text-xl text-slate-900"></h3>
            <p id="modal-cota-value" class="text-2xl font-extrabold text-emerald-600 mt-1"></p>
        </div>

        <div class="bg-slate-50 rounded-xl p-4 mb-6">
            <p class="text-sm text-slate-600 mb-3">Chave PIX para pagamento:</p>
            <div class="flex items-center gap-2">
                <code id="modal-pix-key" class="flex-1 bg-white px-3 py-2 rounded-lg border text-sm font-mono text-slate-700 select-all">
                    contato@escolasocialguapo.org.br
                </code>
                <button id="modal-copy-pix" class="px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                    Copiar
                </button>
            </div>
            <p id="modal-copy-feedback" class="text-xs text-emerald-600 mt-2 hidden">✓ Chave copiada!</p>
        </div>

        <button id="modal-confirm-cota" class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
            Confirmar Apadrinhamento
        </button>
        <p class="text-xs text-slate-500 text-center mt-3">
            Após confirmar, enviaremos os detalhes para seu e-mail.
        </p>
    </div>
</div>
