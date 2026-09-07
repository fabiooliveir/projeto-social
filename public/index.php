<?php

declare(strict_types=1);

use App\Controllers\EducationDashboardController;
use Bramus\Router\Router;
use Jenssegers\Blade\Blade;
use Jenssegers\Blade\Container;

require dirname(__DIR__) . '/vendor/autoload.php';

$viewsDir = dirname(__DIR__) . '/src/Views';
$viewsCache = getenv('GUAPO_VIEWS_CACHE_DIR') ?: dirname(__DIR__) . '/storage/views';

if (!is_dir($viewsCache) && !mkdir($viewsCache, 0755, true) && !is_dir($viewsCache)) {
    throw new RuntimeException("Não foi possível criar o diretório de cache de views: {$viewsCache}");
}

// Compartilha o mesmo container com o Blade e o engine resolver do Illuminate
$container = new Container();
Container::setInstance($container);

$blade = new Blade($viewsDir, $viewsCache, $container);

$router = new Router();

$router->setBasePath('');

$router->get('/', function () {
    header('Location: /painel-educacao');
    exit;
});

$router->get('/painel-educacao', [new EducationDashboardController($blade), 'index']);
$router->get('/api/indicadores/guapo', [new EducationDashboardController($blade), 'apiIndicadores']);
$router->get('/api/indicadores/guapo/download', [new EducationDashboardController($blade), 'baixarDiagnostico']);

$router->set404(function () {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Página não encontrada.';
});

$router->run();