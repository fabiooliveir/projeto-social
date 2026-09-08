<section id="cotas" class="py-20 bg-slate-950 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(#0284c7_1px,transparent_1px)] [background-size:32px_32px] opacity-10"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-brand-500/20 text-brand-300 border border-brand-500/30 mb-3">
                Investimento Social Estratégico · ESG
            </span>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight mb-4">
                Catálogo de Cotas de Apadrinhamento
            </h2>
            <p class="text-slate-400 text-base sm:text-lg leading-relaxed">
                Escolha uma cota de acabamento da Fase 3 e acompanhe de perto o impacto direto da sua doação na vida das crianças de Guapó.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($cotas as $cota)
            <div class="cota-card relative bg-slate-900/80 rounded-3xl p-7 border border-slate-800 hover:border-brand-500/50 transition-all duration-300 hover:-translate-y-1.5 cursor-pointer flex flex-col justify-between group shadow-lg shadow-black/40"
                 data-cota-id="{{ $cota['id'] }}"
                 data-cota-nome="{{ $cota['nome'] }}"
                 data-cota-valor="{{ $cota['valor'] }}">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <span class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            {{ $cota['icone'] }}
                        </span>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                            Cota Fase 3
                        </span>
                    </div>

                    <h3 class="font-display font-bold text-xl text-white group-hover:text-brand-300 transition-colors mb-2">
                        {{ $cota['nome'] }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed mb-6">
                        {{ $cota['desc'] }}
                    </p>
                </div>

                <div>
                    <div class="flex items-baseline gap-1.5 mb-4">
                        <span class="text-2xl sm:text-3xl font-extrabold text-hope-400 font-display">
                            R$ {{ number_format($cota['valor'], 0, ',', '.') }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">/ contribuição</span>
                    </div>

                    <div class="mb-5">
                        <div class="flex justify-between text-xs text-slate-400 mb-1.5">
                            <span>{{ $cota['arrecadado'] }} de {{ $cota['meta'] }} cotas</span>
                            <span class="font-semibold text-brand-400">{{ $cota['meta'] > 0 ? round(($cota['arrecadado'] / $cota['meta']) * 100) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-brand-500 to-hope-500 h-full rounded-full transition-all duration-700"
                                 style="width: {{ $cota['meta'] > 0 ? round(($cota['arrecadado'] / $cota['meta']) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <button class="abrir-modal-cota w-full py-3 bg-gradient-to-r from-brand-600 to-brand-700 text-white text-sm font-bold rounded-xl hover:from-brand-500 hover:to-brand-600 transition-all shadow-md group-hover:shadow-brand-900/50"
                            data-cota-id="{{ $cota['id'] }}">
                        Apadrinhar esta Cota
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <p class="text-xs sm:text-sm text-slate-400">
                Pessoas Jurídicas e Doadores Institucionais: emitimos termo de doação e relatório de destinação para compliance ESG.
            </p>
        </div>
    </div>
</section>

{{-- Modal de Apadrinhamento de Cota --}}
<div id="modal-cota" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" id="modal-cota-overlay"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 text-slate-900 z-10 border border-slate-100">
        <button id="modal-cota-close" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 text-2xl leading-none transition-colors">&times;</button>
        
        <div class="text-center mb-6">
            <div id="modal-cota-icon" class="text-5xl mb-3"></div>
            <h3 id="modal-cota-title" class="font-display font-bold text-2xl text-slate-900"></h3>
            <p id="modal-cota-value" class="text-3xl font-extrabold text-hope-600 font-display mt-1"></p>
            <p class="text-xs text-slate-500 mt-1">Contribuição direta para os acabamentos da Fase 3</p>
        </div>

        <div class="bg-slate-50 rounded-2xl p-4 mb-6 border border-slate-200">
            <p class="text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wider">Chave PIX Oficial (E-mail):</p>
            <div class="flex items-center gap-2">
                <code id="modal-pix-key" class="flex-1 bg-white px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-mono text-slate-800 select-all truncate">
                    contato@escolasocialguapo.org.br
                </code>
                <button id="modal-copy-pix" class="px-4 py-2.5 bg-brand-600 text-white text-xs sm:text-sm font-bold rounded-xl hover:bg-brand-700 transition-colors whitespace-nowrap shadow-sm">
                    Copiar
                </button>
            </div>
            <p id="modal-copy-feedback" class="text-xs font-semibold text-hope-600 mt-2 hidden">✓ Chave PIX copiada para a área de transferência!</p>
        </div>

        <button id="modal-confirm-cota" class="w-full py-3.5 bg-gradient-to-r from-hope-600 to-emerald-600 text-white font-bold rounded-xl hover:from-hope-700 hover:to-emerald-700 transition-all shadow-md">
            Informar Pagamento & Finalizar
        </button>
        <p class="text-xs text-slate-400 text-center mt-3">
            Após a transferência, você será redirecionado para enviar o comprovante.
        </p>
    </div>
</div>
