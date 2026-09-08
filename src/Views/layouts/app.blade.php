<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Diagnóstico Socioeducacional - Guapó-GO | Projeto Social')</title>

    <meta name="description" content="Painel analítico sobre o déficit de vagas em creches, cumprimento da meta do PNE e transparência pública do Projeto Social em Guapó - GO.">
    <meta name="theme-color" content="#0284c7">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Projeto Social Guapó">
    <meta property="og:title" content="Diagnóstico Socioeducacional & Transparência - Guapó-GO">
    <meta property="og:description" content="Painel analítico sobre o déficit de vagas em creches, cumprimento da meta do PNE e transparência pública do Projeto Social em Guapó - GO.">
    <meta name="twitter:card" content="summary_large_image">

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
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        h1, h2, h3, h4, .font-display { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased selection:bg-brand-500 selection:text-white">
    @yield('content')
</body>
</html>