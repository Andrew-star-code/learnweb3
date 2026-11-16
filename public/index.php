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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::checkToken();
}

$router->dispatch($_SERVER['REQUEST_METHOD'], rtrim($path, '/') ?: '/');
