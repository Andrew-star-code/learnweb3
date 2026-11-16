<?php
// Точка сборки приложения: автозагрузка классов и общие константы.
// Composer здесь намеренно не используется — автозагрузчик написан руками,
// чтобы было видно, что в нём нет магии (см. блок «Ядро PHP»).

declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('DATA_DIR', ROOT . '/data');
define('CONTENT_DIR', ROOT . '/content');

spl_autoload_register(function (string $class): void {
    // App\Controllers\PageController -> src/Controllers/PageController.php
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $path = ROOT . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($path)) {
        require $path;
    }
});

if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

/** Короткий помощник для шаблонов: экранирует строку перед выводом в HTML. */
function e(?string $s): string
{
    return App\View::e($s);
}
