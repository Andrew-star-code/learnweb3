<?php
declare(strict_types=1);

namespace App;

/**
 * Платформа умеет выполнять произвольный PHP-код, поэтому важно, чтобы
 * команду «выполни» мог отдать только её собственный интерфейс, а не
 * посторонний сайт, открытый в соседней вкладке.
 *
 * Защита из двух частей:
 *  1. Заголовок Host должен быть localhost — отсекает DNS rebinding.
 *  2. Каждый POST несёт секретный токен, который знает только наша страница (CSRF).
 */
final class Security
{
    public static function token(): string
    {
        $file = DATA_DIR . '/csrf-token';
        if (!is_file($file)) {
            file_put_contents($file, bin2hex(random_bytes(32)));
        }
        return trim(file_get_contents($file));
    }

    public static function checkHost(): void
    {
        $host = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
        if (!in_array($host, ['localhost', '127.0.0.1', '[::1]'], true)) {
            http_response_code(403);
            exit('Платформа доступна только по адресу localhost.');
        }
    }

    public static function checkToken(): void
    {
        $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals(self::token(), $sent)) {
            http_response_code(403);
            header('Content-Type: application/json');
            exit(json_encode(['error' => 'Неверный токен. Обнови страницу.']));
        }
    }
}
