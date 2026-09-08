<section id="doar-pix" class="py-20 bg-white border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-hope-50 text-hope-700 border border-hope-200 mb-3">
                Doação Segura & Instantânea
            </span>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                Faça Sua Doação Direta via PIX
            </h2>
            <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                Cada real é investido integralmente na infraestrutura das salas de aula e do centro comunitário. Prestação de contas transparente e auditável.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            {{-- Card de Doação PIX Instantâneo --}}
            <div class="bg-gradient-to-br from-brand-50/70 via-white to-hope-50/50 rounded-3xl p-8 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-700">Chave PIX Oficial</span>
                        <h3 class="font-display font-bold text-2xl text-slate-900 mt-1">Transferência Direta</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-hope-100 text-hope-700 flex items-center justify-center text-2xl">
                        ⚡
                    </div>
                </div>

                {{-- Seletor de Valores Sugeridos --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2.5">
                        Valores Sugeridos de Apoio:
                    </label>
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" class="btn-valor-sugerido py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-800 hover:border-brand-500 hover:bg-brand-50 transition-all text-center">
                            R$ 30
                        </button>
                        <button type="button" class="btn-valor-sugerido py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-800 hover:border-brand-500 hover:bg-brand-50 transition-all text-center">
                            R$ 50
                        </button>
                        <button type="button" class="btn-valor-sugerido py-2.5 px-3 bg-brand-600 border border-brand-600 rounded-xl text-sm font-bold text-white shadow-sm transition-all text-center">
                            R$ 100
                        </button>
                        <button type="button" class="btn-valor-sugerido py-2.5 px-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-800 hover:border-brand-500 hover:bg-brand-50 transition-all text-center">
                            R$ 250
                        </button>
                    </div>
                </div>

                {{-- Campo Copia e Cola --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">
                        Chave PIX (E-mail):
                    </label>
                    <div class="flex items-center gap-2">
                        <code id="pix-key-display" class="flex-1 bg-white px-4 py-3 rounded-2xl border border-slate-300 text-sm font-mono text-slate-800 select-all truncate shadow-inner">
                            contato@escolasocialguapo.org.br
                        </code>
                        <button id="copy-pix-btn" class="px-5 py-3 bg-gradient-to-r from-hope-600 to-emerald-600 text-white text-sm font-bold rounded-2xl hover:from-hope-700 hover:to-emerald-700 transition-all shadow-md shrink-0 flex items-center gap-1.5">
                            <span>📋</span> Copiar
                        </button>
                    </div>
                    <div id="copy-feedback" class="hidden text-xs font-semibold text-hope-700 mt-2 flex items-center gap-1">
                        <span>✓</span> Chave PIX copiada com sucesso! Cole no aplicativo do seu banco.
                    </div>
                </div>

                {{-- QR Code Visual --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Escaneie com a câmera do seu banco</p>
                    <div class="inline-block p-3 rounded-2xl bg-slate-50 border border-slate-200 shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=contato@escolasocialguapo.org.br" 
                             alt="QR Code PIX - Projeto Social Guapó" 
                             width="180" 
                             height="180" 
                             class="rounded-xl mx-auto">
                    </div>
                    <p class="text-xs text-slate-500 mt-3">
                        Favorecido: <strong>Projeto Social de Educação Guapó</strong> · Banco do Brasil
                    </p>
                </div>
            </div>

            {{-- Coluna Direita: Simulador de Impacto e Formulário de Parceria --}}
            <div class="space-y-8">
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200/80">
                    <h3 class="font-display font-bold text-slate-900 text-xl mb-4">Como sua doação transforma realidades:</h3>
                    <div class="space-y-3.5">
                        <div class="flex items-start gap-4 p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                            <span class="text-2xl p-2 rounded-xl bg-brand-50 text-brand-600">🎒</span>
                            <div>
                                <div class="font-display font-bold text-slate-900 text-base">R$ 30 / mês</div>
                                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">Garante material pedagógico e lanche diário para 1 criança da primeira infância.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                            <span class="text-2xl p-2 rounded-xl bg-hope-50 text-hope-600">📚</span>
                            <div>
                                <div class="font-display font-bold text-slate-900 text-base">R$ 50 / mês</div>
                                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">Custeia tutoria individual de reforço em Língua Portuguesa e Matemática para 2 alunos do contraturno.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                            <span class="text-2xl p-2 rounded-xl bg-amber-50 text-amber-600">💡</span>
                            <div>
                                <div class="font-display font-bold text-slate-900 text-base">R$ 100 / mês</div>
                                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">Mantém a oficina de robótica educativa e tecnologia com kits práticos para uma turma inteira.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulário de Contato e Parcerias --}}
                <div id="contato" class="bg-slate-50 rounded-3xl p-8 border border-slate-200/80">
                    <div class="mb-5">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-700">Canal Institucional</span>
                        <h3 class="font-display font-bold text-slate-900 text-xl mt-1">Parcerias Corporativas (ESG) & Grandes Doadores</h3>
                    </div>

                    <form id="contact-form" action="/contato" method="POST" class="space-y-4">
                        <div>
                            <label for="nome" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nome / Empresa *</label>
                            <input type="text" id="nome" name="nome" required
                                   class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all"
                                   placeholder="Nome completo ou Razão Social">
                            <p class="text-xs text-red-500 mt-1 hidden" id="erro-nome"></p>
                        </div>

                        <div>
                            <label for="contato-campo" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">E-mail ou WhatsApp *</label>
                            <input type="text" id="contato-campo" name="contato" required
                                   class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all"
                                   placeholder="exemplo@empresa.com ou (62) 99999-9999">
                            <p class="text-xs text-red-500 mt-1 hidden" id="erro-contato"></p>
                        </div>

                        <div>
                            <label for="tipo_apoio" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tipo de Apoio *</label>
                            <select id="tipo_apoio" name="tipo_apoio" required
                                    class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
                                <option value="">Selecione a modalidade...</option>
                                <option value="parceria">Parceria Corporativa / ESG</option>
                                <option value="apadrinhamento">Apadrinhamento de Cota de Obra</option>
                                <option value="doacao_grande">Grande Doador / Fundação</option>
                                <option value="voluntariado">Voluntariado Técnico ou Pedagógico</option>
                                <option value="outro">Outro tipo de contribuição</option>
                            </select>
                            <p class="text-xs text-red-500 mt-1 hidden" id="erro-tipo_apoio"></p>
                        </div>

                        <div>
                            <label for="mensagem" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mensagem (opcional)</label>
                            <textarea id="mensagem" name="mensagem" rows="3"
                                      class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all resize-none"
                                      placeholder="Como você ou sua empresa gostaria de colaborar com a Escola Social de Guapó?"></textarea>
                        </div>

                        <div id="form-success" class="hidden p-4 rounded-xl text-sm font-semibold bg-hope-100 text-hope-800 border border-hope-200"></div>
                        <div id="form-error" class="hidden p-4 rounded-xl text-sm font-semibold bg-red-100 text-red-800 border border-red-200"></div>

                        <button type="submit" class="w-full py-3.5 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-md flex items-center justify-center gap-2">
                            <span>✉️</span> Enviar Solicitação de Parceria
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
