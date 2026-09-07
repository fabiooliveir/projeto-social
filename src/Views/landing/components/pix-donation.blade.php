<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-4">Doação Instantânea via PIX</h2>
            <p class="text-slate-600 max-w-2xl mx-auto">
                Contribua agora mesmo. Todo valor faz a diferença na vida de uma criança.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-2xl p-6 sm:p-8 border border-emerald-200/50">
                <h3 class="font-bold text-slate-900 text-lg mb-4">Chave PIX Institucional</h3>
                <div class="flex items-center gap-2 mb-6">
                    <code id="pix-key-display" class="flex-1 bg-white px-4 py-3 rounded-xl border border-emerald-200 text-base font-mono text-slate-700 select-all">
                        contato@escolasocialguapo.org.br
                    </code>
                    <button id="copy-pix-btn" class="px-4 py-3 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors whitespace-nowrap">
                        Copiar
                    </button>
                </div>
                <div id="copy-feedback" class="hidden text-sm text-emerald-700 font-medium mb-4">✓ Chave PIX copiada com sucesso!</div>

                <div class="bg-white rounded-xl p-4 border border-emerald-200/50">
                    <p class="text-sm text-slate-600 mb-3 text-center">Leia o QR Code abaixo:</p>
                    <div class="flex justify-center">
                        <div class="w-48 h-48 bg-slate-100 rounded-xl flex items-center justify-center border border-slate-200">
                            <div class="text-center text-slate-500">
                                <div class="text-4xl mb-2">📱</div>
                                <p class="text-xs">QR Code será gerado<br>pelo app do banco</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 text-center mt-3">
                        Use a chave PIX acima no app do seu banco para gerar o QR Code.
                    </p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                    <h3 class="font-bold text-slate-900 mb-4">Simulador de Impacto Mensal</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-slate-200 hover:border-emerald-300 transition-colors">
                            <span class="text-2xl">🎒</span>
                            <div>
                                <div class="font-bold text-slate-900">R$ 30/mês</div>
                                <p class="text-sm text-slate-600">Fornecimento de material escolar para 2 crianças do contraturno por 1 mês.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-slate-200 hover:border-emerald-300 transition-colors">
                            <span class="text-2xl">📚</span>
                            <div>
                                <div class="font-bold text-slate-900">R$ 50/mês</div>
                                <p class="text-sm text-slate-600">1 hora semanal de reforço escolar personalizado para 5 crianças por 1 mês.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-slate-200 hover:border-emerald-300 transition-colors">
                            <span class="text-2xl">🎨</span>
                            <div>
                                <div class="font-bold text-slate-900">R$ 100/mês</div>
                                <p class="text-sm text-slate-600">Oficina de arte e robótica completa para 10 crianças por 1 mês, incluindo materiais.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="contato" class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                    <h3 class="font-bold text-slate-900 mb-4">Parceria / Voluntariado / Grandes Doadores</h3>
                    <form id="contact-form" action="/contato" method="POST" class="space-y-4">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-slate-700 mb-1">Nome *</label>
                            <input type="text" id="nome" name="nome" required
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                                   placeholder="Seu nome completo">
                            <p class="text-xs text-red-500 mt-1 hidden" id="erro-nome"></p>
                        </div>
                        <div>
                            <label for="contato-campo" class="block text-sm font-medium text-slate-700 mb-1">E-mail ou Telefone *</label>
                            <input type="text" id="contato-campo" name="contato" required
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                                   placeholder="email@exemplo.com ou (00) 00000-0000">
                            <p class="text-xs text-red-500 mt-1 hidden" id="erro-contato"></p>
                        </div>
                        <div>
                            <label for="tipo_apoio" class="block text-sm font-medium text-slate-700 mb-1">Tipo de Apoio *</label>
                            <select id="tipo_apoio" name="tipo_apoio" required
                                    class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                                <option value="">Selecione...</option>
                                <option value="parceria">Parceria Empresarial</option>
                                <option value="voluntariado">Voluntariado</option>
                                <option value="doacao_grande">Grande Doador</option>
                                <option value="apadrinhamento">Apadrinhamento de Cota</option>
                                <option value="outro">Outro</option>
                            </select>
                            <p class="text-xs text-red-500 mt-1 hidden" id="erro-tipo_apoio"></p>
                        </div>
                        <div>
                            <label for="mensagem" class="block text-sm font-medium text-slate-700 mb-1">Mensagem (opcional)</label>
                            <textarea id="mensagem" name="mensagem" rows="3"
                                      class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all resize-none"
                                      placeholder="Conte-nos como gostaria de contribuir..."></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                            Enviar Mensagem
                        </button>
                        <div id="form-success" class="hidden p-3 bg-emerald-50 text-emerald-700 text-sm rounded-xl border border-emerald-200"></div>
                        <div id="form-error" class="hidden p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
