<?php
declare(strict_types=1);

namespace App;

/**
 * Минимальный роутер: сопоставляет метод и путь запроса с обработчиком.
 * Шаблон пути может содержать параметры в фигурных скобках: /lesson/{block}/{lesson}
 */
final class Router
{
    /** @var array<int, array{method: string, regex: string, handler: callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function add(string $method, string $pattern, callable $handler): void
    {
        // {name} превращаем в именованную группу, которая не захватывает слэши
        $regex = preg_replace('#\{(\w+)\}#', '(?<$1>[a-z0-9-]+)', $pattern);
        $this->routes[] = ['method' => $method, 'regex' => '#^' . $regex . '$#', 'handler' => $handler];
    }

    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || !preg_match($route['regex'], $path, $m)) {
                continue;
            }
            // оставляем только именованные параметры
            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            ($route['handler'])($params);
            return;
        }
        http_response_code(404);
        echo View::render('error', ['title' => 'Страница не найдена', 'message' => 'Такой страницы нет.']);
    }
}
