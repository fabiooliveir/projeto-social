<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Escola Social de Guapó - Educação Infantil e Centro Comunitário com Auditório Multiuso')</title>

    <meta name="description" content="@yield('description', 'Projeto social de educação infantil (a partir de 2 anos), contraturno escolar e centro comunitário multiuso em Guapó-GO. Apadrinhe uma cota e transforme vidas.')">
    <meta name="theme-color" content="#0284c7">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏫</text></svg>">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Escola Social de Guapó">
    <meta property="og:title" content="@yield('og_title', 'Escola Social de Guapó - Educação Infantil e Centro Comunitário')">
    <meta property="og:description" content="@yield('og_description', 'Projeto social de educação infantil (a partir de 2 anos), contraturno escolar e centro comunitário multiuso em Guapó-GO.')">
    <meta property="og:url" content="@yield('og_url', 'https://escolasocialguapo.org.br')">
    <meta name="twitter:card" content="summary_large_image">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NGO",
        "name": "Escola Social de Guapó",
        "description": "Projeto social de educação infantil, contraturno escolar e centro comunitário em Guapó-GO",
        "areaServed": {
            "@type": "City",
            "name": "Guapó",
            "containedInPlace": {
                "@type": "State",
                "name": "Goiás"
            }
        },
        "knowsAbout": ["Educação Infantil", "Contraturno Escolar", "Auditório Multiuso", "MROSC"]
    }
    </script>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        hope: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        h1, h2, h3, h4, .font-display { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased selection:bg-brand-500 selection:text-white">

    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-600 to-hope-600 flex items-center justify-center text-white shadow-md shadow-brand-600/20 group-hover:scale-105 transition-transform">
                        <span class="text-xl">🏫</span>
                    </div>
                    <div>
                        <span class="font-display font-bold text-slate-900 text-lg leading-tight block">Escola Social de Guapó</span>
                        <span class="text-xs font-semibold text-brand-600 uppercase tracking-wider block">Educação Infantil & Contraturno</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
                    <a href="#projeto" class="hover:text-brand-600 transition-colors">O Projeto</a>
                    <a href="#dados" class="hover:text-brand-600 transition-colors">Diagnóstico Social</a>
                    <a href="#turnos" class="hover:text-brand-600 transition-colors">Espaços & 3 Turnos</a>
                    <a href="#obra" class="hover:text-brand-600 transition-colors">Fases da Obra</a>
                    <a href="#cotas" class="hover:text-brand-600 transition-colors">Cotas de Apoio</a>
                    <a href="/painel-educacao" class="hover:text-brand-600 transition-colors flex items-center gap-1.5 font-semibold text-slate-700">
                        <span class="inline-block w-2 h-2 rounded-full bg-brand-600"></span>
                        Painel de Dados
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="#doar-pix" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-hope-600 to-emerald-600 text-white text-sm font-bold rounded-xl hover:from-hope-700 hover:to-emerald-700 transition-all shadow-md shadow-hope-600/25 hover:shadow-lg hover:-translate-y-0.5">
                        <span>⚡</span> Doar via PIX
                    </a>
                    <button id="menu-toggle" class="md:hidden p-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100" aria-label="Abrir menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <div id="mobile-menu" class="hidden md:hidden pb-5 pt-2 border-t border-slate-100 space-y-2">
                <a href="#projeto" class="block px-3.5 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl">O Projeto</a>
                <a href="#dados" class="block px-3.5 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl">Diagnóstico Social</a>
                <a href="#turnos" class="block px-3.5 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl">Espaços & 3 Turnos</a>
                <a href="#obra" class="block px-3.5 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl">Fases da Obra</a>
                <a href="#cotas" class="block px-3.5 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl">Cotas de Apoio</a>
                <a href="/painel-educacao" class="block px-3.5 py-2.5 text-sm font-semibold text-brand-700 bg-brand-50 rounded-xl">Painel de Dados Públicos</a>
                <a href="#contato" class="block px-3.5 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-700 rounded-xl">Contato & Parcerias</a>
                <a href="#doar-pix" class="block px-4 py-3 bg-hope-600 text-white text-sm font-bold rounded-xl text-center shadow-md">Doar via PIX Instantâneo</a>
            </div>
        </nav>
    </header>

    <main class="pt-20">
        @yield('content')
    </main>

    <footer class="bg-slate-950 text-slate-300 border-t border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-600 to-hope-600 flex items-center justify-center text-white">
                            <span class="text-lg">🏫</span>
                        </div>
                        <div>
                            <span class="font-display font-bold text-white text-lg block">Projeto Social Guapó</span>
                            <span class="text-xs text-brand-400 font-semibold tracking-wider block">Educação Infantil & Centro Comunitário</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed mb-6">
                        Associação sem fins lucrativos dedicada a combater o déficit educacional na infância (a partir de 2 anos), ofertar contraturno escolar de excelência e promover o acolhimento comunitário em Guapó-GO.
                    </p>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="px-3 py-1 bg-slate-900 rounded-full border border-slate-800 text-slate-300">Lei 13.019/2014 (MROSC)</span>
                        <span class="px-3 py-1 bg-slate-900 rounded-full border border-slate-800 text-slate-300">CMDCA Guapó</span>
                        <span class="px-3 py-1 bg-slate-900 rounded-full border border-slate-800 text-slate-300">100% Auditável</span>
                    </div>
                </div>

                <div>
                    <h3 class="font-display font-bold text-white text-sm tracking-wider uppercase mb-4">Navegação Rápida</h3>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="/painel-educacao" class="hover:text-white transition-colors flex items-center gap-1.5"><span class="text-brand-400">→</span> Painel de Dados</a></li>
                        <li><a href="#dados" class="hover:text-white transition-colors flex items-center gap-1.5"><span class="text-brand-400">→</span> Diagnóstico Social</a></li>
                        <li><a href="#cotas" class="hover:text-white transition-colors flex items-center gap-1.5"><span class="text-brand-400">→</span> Cotas de Apadrinhamento</a></li>
                        <li><a href="/api/indicadores/guapo/download" class="hover:text-white transition-colors flex items-center gap-1.5"><span class="text-brand-400">→</span> Download do Diagnóstico (JSON)</a></li>
                        <li><a href="https://github.com/fabiooliveir/projeto-social" class="hover:text-white transition-colors flex items-center gap-1.5" target="_blank" rel="noopener"><span class="text-brand-400">→</span> Repositório GitHub</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-display font-bold text-white text-sm tracking-wider uppercase mb-4">Sede & Contato</h3>
                    <ul class="space-y-2.5 text-sm text-slate-400 mb-6">
                        <li class="flex items-start gap-2">
                            <span>📍</span> <span>Guapó - GO, Brasil</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span>✉️</span> <a href="mailto:contato@escolasocialguapo.org.br" class="hover:text-white transition-colors">contato@escolasocialguapo.org.br</a>
                        </li>
                    </ul>
                    <div class="p-3.5 bg-slate-900/90 rounded-xl border border-slate-800 text-xs text-slate-400">
                        <p class="font-semibold text-slate-200 mb-1">Transparência & Governança</p>
                        <p>Prestação de contas contínua com segregação financeira exclusiva para obras e atividades pedagógicas.</p>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-800 text-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Projeto Social Guapó. Todos os direitos reservados. Plataforma de Captação e Transparência Pública.</p>
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
            if (window.scrollY > 15) {
                navbar.classList.add('shadow-md');
            } else {
                navbar.classList.remove('shadow-md');
            }
        });
    })();
    </script>

    @yield('scripts')
</body>
</html>
