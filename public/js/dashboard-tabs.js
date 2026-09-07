(() => {
    'use strict';

    const botoes = Array.from(document.querySelectorAll('[role="tab"][data-tab-target]'));
    if (!botoes.length) {
        return;
    }

    const alternar = (alvo) => {
        botoes.forEach((botao) => {
            const ativo = botao.dataset.tabTarget === alvo;
            botao.setAttribute('aria-selected', ativo ? 'true' : 'false');
            botao.classList.toggle('tab-btn-ativo', ativo);
        });

        const paineis = Array.from(document.querySelectorAll('[data-tab-panel]'));
        paineis.forEach((painel) => {
            const ativo = painel.id === alvo;
            painel.hidden = !ativo;
        });

        window.dispatchEvent(new CustomEvent('guapo:tabschange'));
    };

    botoes.forEach((botao) => {
        botao.addEventListener('click', () => alternar(botao.dataset.tabTarget));
    });

    // Navegação por teclado (setas esquerda/direita) para acessibilidade.
    botoes.forEach((botao, indice) => {
        botao.addEventListener('keydown', (evento) => {
            let proximo = null;
            if (evento.key === 'ArrowRight') {
                proximo = botoes[(indice + 1) % botoes.length];
            } else if (evento.key === 'ArrowLeft') {
                proximo = botoes[(indice - 1 + botoes.length) % botoes.length];
            }
            if (proximo) {
                evento.preventDefault();
                proximo.focus();
                proximo.click();
            }
        });
    });
})();