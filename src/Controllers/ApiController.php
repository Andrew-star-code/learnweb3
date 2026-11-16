<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Course;
use App\Markdown;
use App\PhpRunner;
use App\Progress;

/**
 * JSON-API, которым пользуется интерфейс. Все методы принимают и возвращают JSON.
 */
final class ApiController
{
    private array $input;

    public function __construct()
    {
        $this->input = json_decode(file_get_contents('php://input'), true) ?: [];
    }

    /** Сохранить черновик кода, попытку или успешное решение. */
    public function progress(): void
    {
        $task = Course::task((string) ($this->input['taskId'] ?? ''));
        if ($task === null) {
            $this->fail('Задание не найдено', 404);
        }
        $code = isset($this->input['code']) ? (string) $this->input['code'] : null;
        $this->json(Progress::save(
            $task['id'],
            $code,
            !empty($this->input['attempt']),
            !empty($this->input['passed'])
        ));
    }

    /** Решение задания или эталонный разбор для задания на декомпозицию. */
    public function solution(): void
    {
        $task = Course::task((string) ($this->input['taskId'] ?? ''));
        if ($task === null) {
            $this->fail('Задание не найдено', 404);
        }
        if ($task['type'] === 'plan') {
            $this->json(['html' => Markdown::render($task['reference'])]);
        }
        $this->json(['solution' => $task['solution'] ?? '']);
    }

    /** Выполнить PHP-код: просто запустить (песочница) или проверить тестами задания. */
    public function runPhp(): void
    {
        $code = (string) ($this->input['code'] ?? '');
        $version = ($this->input['version'] ?? '8') === '7' ? '7' : '8';
        $tests = null;

        if (!empty($this->input['taskId'])) {
            $task = Course::task((string) $this->input['taskId']);
            if ($task === null || ($task['lang'] ?? '') !== 'php') {
                $this->fail('Задание не найдено', 404);
            }
            $tests = $task['tests'] ?? null;
            $version = (string) ($task['php'] ?? $version);
        }
        $this->json(PhpRunner::run($code, $version, $tests));
    }

    public function card(): void
    {
        Progress::reviewCard((string) ($this->input['cardId'] ?? ''), !empty($this->input['remembered']));
        $this->json(['ok' => true]);
    }

    /** @return never */
    private function json(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** @return never */
    private function fail(string $message, int $status): void
    {
        http_response_code($status);
        $this->json(['error' => $message]);
    }
}
