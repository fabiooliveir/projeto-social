<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Diagnóstico da Educação Infantil - Guapó-GO | Projeto Social')</title>

    <meta name="description" content="Painel analítico sobre o déficit de vagas em creches e a meta do PNE no município de Guapó - GO.">
    <meta name="theme-color" content="#0f172a">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🧒</text></svg>">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Projeto Social">
    <meta property="og:title" content="Diagnóstico da Educação Infantil - Guapó-GO">
    <meta property="og:description" content="Painel analítico sobre o déficit de vagas em creches e a meta do PNE no município de Guapó - GO.">
    <meta name="twitter:card" content="summary_large_image">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/dashboard.css">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    @yield('content')
</body>
</html>