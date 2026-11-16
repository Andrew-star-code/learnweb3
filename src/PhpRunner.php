<?php
declare(strict_types=1);

namespace App;

/**
 * Выполняет PHP-код ученика. Версия «8» запускается тем же PHP, на котором
 * работает платформа; версия «7» отправляется в соседний контейнер php7.
 */
final class PhpRunner
{
    public static function run(string $code, string $version, ?string $tests = null): array
    {
        if ($version === '7') {
            return self::runRemote($code, $tests);
        }
        require_once ROOT . '/runner/lib.php';
        return runner_exec($code, $tests, 5);
    }

    private static function runRemote(string $code, ?string $tests): array
    {
        $url = getenv('PHP7_RUNNER_URL');
        if (!$url) {
            return ['error' => 'PHP 7 доступен только при запуске через docker compose (см. README).'];
        }
        $context = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode(['code' => $code, 'tests' => $tests]),
            'timeout' => 10,
            'ignore_errors' => true,
        ]]);
        $response = @file_get_contents($url, false, $context);
        $result = $response === false ? null : json_decode($response, true);
        if (!is_array($result)) {
            return ['error' => 'Контейнер php7 не отвечает. Проверь, что он запущен: docker compose ps'];
        }
        return $result;
    }
}
