<?php

declare(strict_types=1);

error_reporting(E_ALL & ~E_DEPRECATED);

use App\Controllers\EducationDashboardController;
use App\Controllers\LandingPageController;
use App\Controllers\QualityIndicatorsController;
use App\Services\QualityIndicatorsService;
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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router->get('/', [new LandingPageController($blade), 'index']);
$router->post('/contato', [new LandingPageController($blade), 'enviarContato']);

$router->get('/painel-educacao', [new EducationDashboardController($blade), 'index']);
$router->get('/api/indicadores/guapo', [new EducationDashboardController($blade), 'apiIndicadores']);
$router->get('/api/indicadores/guapo/download', [new EducationDashboardController($blade), 'baixarDiagnostico']);

$qualityController = new QualityIndicatorsController(new QualityIndicatorsService());
$router->get('/api/indicadores/qualidade', [$qualityController, 'index']);
$router->get('/api/indicadores/qualidade/ideb', [$qualityController, 'ideb']);
$router->get('/api/indicadores/qualidade/infraestrutura', [$qualityController, 'infraestrutura']);
$router->get('/api/indicadores/qualidade/download', [$qualityController, 'download']);

$router->set404(function () {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Página não encontrada.';
});

$router->run();