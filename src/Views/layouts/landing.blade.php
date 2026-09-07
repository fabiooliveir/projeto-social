<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Escola Social de Guapó - Educação Infantil e Contraturno com Auditório Multiuso')</title>

    <meta name="description" content="@yield('description', 'Projeto social de educação infantil (a partir de 2 anos), contraturno escolar e auditório multiuso em Guapó-GO. Apadrinhe uma cota e transforme vidas.')">
    <meta name="theme-color" content="#0f172a">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏫</text></svg>">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Escola Social de Guapó">
    <meta property="og:title" content="@yield('og_title', 'Escola Social de Guapó - Educação Infantil e Contraturno')">
    <meta property="og:description" content="@yield('og_description', 'Projeto social de educação infantil (a partir de 2 anos), contraturno escolar e auditório multiuso em Guapó-GO.')">
    <meta property="og:url" content="@yield('og_url', 'https://escolasocialguapo.org.br')">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Escola Social de Guapó - Educação Infantil e Contraturno')">
    <meta name="twitter:description" content="@yield('og_description', 'Projeto social de educação infantil (a partir de 2 anos), contraturno escolar e auditório multiuso em Guapó-GO.')">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NGO",
        "name": "Escola Social de Guapó",
        "description": "Projeto social de educação infantil e contraturno escolar em Guapó-GO",
        "areaServed": {
            "@type": "City",
            "name": "Guapó",
            "containedInPlace": {
                "@type": "State",
                "name": "Goiás"
            }
        },
        "knowsAbout": ["Educação Infantil", "Contraturno Escolar", "Auditório Multiuso"]
    }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">

    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200 transition-all duration-300">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🏫</span>
                    <span class="font-bold text-slate-900 hidden sm:inline">Escola Social de Guapó</span>
                    <span class="font-bold text-slate-900 sm:hidden">ESG</span>
                </div>

                <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                    <a href="#projeto" class="hover:text-blue-600 transition-colors">O Projeto</a>
                    <a href="#dados" class="hover:text-blue-600 transition-colors">Dados de Guapó</a>
                    <a href="#turnos" class="hover:text-blue-600 transition-colors">3 Turnos</a>
                    <a href="#cotas" class="hover:text-blue-600 transition-colors">Cotas</a>
                    <a href="/painel-educacao" class="hover:text-blue-600 transition-colors">Painel de Dados</a>
                    <a href="#contato" class="hover:text-blue-600 transition-colors">Contato</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="#cotas" class="hidden sm:inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                        Doar via PIX
                    </a>
                    <button id="menu-toggle" class="md:hidden p-2 text-slate-600 hover:text-slate-900" aria-label="Abrir menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <div id="mobile-menu" class="hidden md:hidden pb-4 space-y-2">
                <a href="#projeto" class="block px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">O Projeto</a>
                <a href="#dados" class="block px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Dados de Guapó</a>
                <a href="#turnos" class="block px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">3 Turnos</a>
                <a href="#cotas" class="block px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Cotas</a>
                <a href="/painel-educacao" class="block px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Painel de Dados</a>
                <a href="#contato" class="block px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg">Contato</a>
                <a href="#cotas" class="block px-3 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg text-center">Doar via PIX</a>
            </div>
        </nav>
    </header>

    <main class="pt-16">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl">🏫</span>
                        <span class="font-bold text-white">Escola Social de Guapó</span>
                    </div>
                    <p class="text-sm text-slate-400">
                        Associação sem fins lucrativos dedicada à educação infantil, contraturno escolar e ao fortalecimento da comunidade de Guapó-GO.
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-3">Links Úteis</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/painel-educacao" class="hover:text-white transition-colors">Painel de Dados</a></li>
                        <li><a href="/api/indicadores/guapo/download" class="hover:text-white transition-colors">Diagnóstico Completo (JSON)</a></li>
                        <li><a href="https://github.com/fabiooliveir/projeto-social" class="hover:text-white transition-colors" target="_blank" rel="noopener">Repositório GitHub</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-white mb-3">Contato</h3>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li>📍 Guapó - GO, Brasil</li>
                        <li><a href="mailto:contato@escolasocialguapo.org.br" class="hover:text-white transition-colors">contato@escolasocialguapo.org.br</a></li>
                    </ul>
                    <div class="mt-4 p-3 bg-slate-800 rounded-lg text-xs text-slate-400">
                        <p class="font-medium text-slate-300 mb-1">Transparência</p>
                        <p>Registro associativo ativo. Dados públicos disponíveis no Painel de Dados e repositório GitHub.</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-slate-800 text-center text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} Escola Social de Guapó. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
    (function() {
        var toggle = document.getElementById('menu-toggle');
        var menu = document.getElementById('mobile-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });
            menu.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    menu.classList.add('hidden');
                });
            });
        }

        var navbar = document.getElementById('navbar');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                navbar.classList.add('shadow-sm');
            } else {
                navbar.classList.remove('shadow-sm');
            }
        });
    })();
    </script>

    @yield('scripts')
</body>
</html>
