@extends('layouts.landing')

@section('content')
    @include('landing.components.hero')
    @include('landing.components.context-kpis')
    @include('landing.components.spaces-auditorium')
    @include('landing.components.project-phases')
    @include('landing.components.sponsorship-catalog')
    @include('landing.components.pix-donation')
@endsection

@section('scripts')
<script>
(function() {
    var modalCota = document.getElementById('modal-cota');
    var modalOverlay = document.getElementById('modal-cota-overlay');
    var modalClose = document.getElementById('modal-cota-close');
    var modalTitle = document.getElementById('modal-cota-title');
    var modalValue = document.getElementById('modal-cota-value');
    var modalIcon = document.getElementById('modal-cota-icon');
    var copyBtn = document.getElementById('copy-pix-btn');
    var copyFeedback = document.getElementById('copy-feedback');
    var pixKeyDisplay = document.getElementById('pix-key-display');

    function openModalCota(id, nome, valor) {
        if (!modalCota) return;
        var cotas = @json($cotas);
        var cota = cotas.find(function(c) { return c.id === id; });
        if (cota) {
            modalIcon.textContent = cota.icone;
            modalTitle.textContent = cota.nome;
            modalValue.textContent = 'R$ ' + cota.valor.toLocaleString('pt-BR');
        } else {
            modalIcon.textContent = '🤝';
            modalTitle.textContent = nome || 'Apadrinhar Cota';
            modalValue.textContent = valor ? 'R$ ' + Number(valor).toLocaleString('pt-BR') : '';
        }
        modalCota.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModalCota() {
        if (!modalCota) return;
        modalCota.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.abrir-modal-cota').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var id = this.getAttribute('data-cota-id');
            var card = this.closest('.cota-card');
            var nome = card ? card.getAttribute('data-cota-nome') : '';
            var valor = card ? card.getAttribute('data-cota-valor') : '';
            openModalCota(id, nome, valor);
        });
    });

    document.querySelectorAll('.cota-card').forEach(function(card) {
        card.addEventListener('click', function() {
            var id = this.getAttribute('data-cota-id');
            var nome = this.getAttribute('data-cota-nome');
            var valor = this.getAttribute('data-cota-valor');
            openModalCota(id, nome, valor);
        });
    });

    if (modalClose) modalClose.addEventListener('click', closeModalCota);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModalCota);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModalCota();
    });

    var confirmBtn = document.getElementById('modal-confirm-cota');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            window.location.href = '#contato';
            closeModalCota();
        });
    }

    if (copyBtn && pixKeyDisplay && copyFeedback) {
        copyBtn.addEventListener('click', function() {
            var key = pixKeyDisplay.textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(key).then(function() {
                    copyFeedback.classList.remove('hidden');
                    setTimeout(function() { copyFeedback.classList.add('hidden'); }, 3000);
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = key;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                copyFeedback.classList.remove('hidden');
                setTimeout(function() { copyFeedback.classList.add('hidden'); }, 3000);
            }
        });
    }

    var contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var dados = {};
            formData.forEach(function(value, key) { dados[key] = value; });

            var submitBtn = contactForm.querySelector('button[type="submit"]');
            var originalText = submitBtn.textContent;
            submitBtn.textContent = 'Enviando...';
            submitBtn.disabled = true;

            fetch('/contato', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(dados)
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;

                var successEl = document.getElementById('form-success');
                var errorEl = document.getElementById('form-error');
                successEl.classList.add('hidden');
                errorEl.classList.add('hidden');

                if (data.erros) {
                    Object.keys(data.erros).forEach(function(campo) {
                        var erroEl = document.getElementById('erro-' + campo);
                        if (erroEl) {
                            erroEl.textContent = data.erros[campo];
                            erroEl.classList.remove('hidden');
                        }
                    });
                } else if (data.sucesso) {
                    successEl.textContent = data.mensagem;
                    successEl.classList.remove('hidden');
                    contactForm.reset();
                    setTimeout(function() { successEl.classList.add('hidden'); }, 5000);
                }
            })
            .catch(function() {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                var errorEl = document.getElementById('form-error');
                errorEl.textContent = 'Erro ao enviar. Tente novamente.';
                errorEl.classList.remove('hidden');
            });
        });
    }
})();
</script>
@endsection
