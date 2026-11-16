<?php
// Единая точка входа (front controller): сюда приходит каждый запрос.

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Встроенный сервер PHP сам отдаст существующий статический файл, если вернуть false
if (PHP_SAPI === 'cli-server' && $path !== '/' && is_file(__DIR__ . $path)) {
    return false;
}

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Controllers\ApiController;
use App\Controllers\DemoApiController;
use App\Controllers\PageController;
use App\Router;
use App\Security;

Security::checkHost();

$router = new Router();

$router->get('/', fn() => (new PageController())->home());
$router->get('/lesson/{block}/{lesson}', fn(array $p) => (new PageController())->lesson($p));
$router->get('/review', fn() => (new PageController())->review());
$router->get('/sandbox', fn() => (new PageController())->sandbox());

$router->post('/api/progress', fn() => (new ApiController())->progress());
$router->post('/api/solution', fn() => (new ApiController())->solution());
$router->post('/api/run-php', fn() => (new ApiController())->runPhp());
$router->post('/api/card', fn() => (new ApiController())->card());

// Учебное API для заданий про fetch. Запросы к нему приходят из изолированной
// рамки предпросмотра, поэтому оно разрешает любые источники и не требует токена.
if (strncmp($path, '/api/demo/', 10) === 0) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { // предварительный запрос браузера (preflight)
        http_response_code(204);
        exit;
    }

    $demo = new DemoApiController();
    $router->post('/api/demo/reset', fn() => $demo->reset());
    $router->post('/api/demo/fail-next', fn() => $demo->failNext());
    if (!in_array($path, ['/api/demo/reset', '/api/demo/fail-next'], true)) {
        $demo->before();
    }
    $router->get('/api/demo/products', fn() => $demo->products());
    $router->get('/api/demo/products/{id}', fn(array $p) => $demo->product($p));
    $router->get('/api/demo/todos', fn() => $demo->todos());
    $router->post('/api/demo/todos', fn() => $demo->createTodo());
    $router->add('DELETE', '/api/demo/todos/{id}', fn(array $p) => $demo->deleteTodo($p));
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::checkToken();
}

$router->dispatch($_SERVER['REQUEST_METHOD'], rtrim($path, '/') ?: '/');
