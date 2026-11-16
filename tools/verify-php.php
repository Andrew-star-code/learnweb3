<?php
// Проверка PHP-заданий: эталонное решение должно проходить тесты, заготовка — нет.
// Запуск из корня проекта: php tools/verify-php.php
//
// Задания с 'php' => '7' выполняются в контейнере php7. Если платформа запущена не
// через docker compose (нет переменной PHP7_RUNNER_URL), они пропускаются с пометкой.

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use App\Course;
use App\PhpRunner;

$passed = function (array $run): bool {
    if (isset($run['error']) || !empty($run['timedOut']) || empty($run['results'])) {
        return false;
    }
    foreach ($run['results'] as $result) {
        if (!$result['ok']) {
            return false;
        }
    }
    return true;
};

$problems = 0;
$checked = 0;
$skipped = 0;

foreach (Course::blocks() as $block) {
    foreach ($block['lessons'] as $slug) {
        foreach (Course::lesson($block['id'], $slug)['tasks'] as $task) {
            if ($task['type'] !== 'code' || $task['lang'] !== 'php') {
                continue;
            }
            $version = (string) ($task['php'] ?? '8');
            if ($version === '7' && !getenv('PHP7_RUNNER_URL')) {
                $skipped++;
                continue;
            }
            $checked++;

            $solved = PhpRunner::run($task['solution'], $version, $task['tests']);
            if (!$passed($solved)) {
                $problems++;
                echo "✗ {$task['id']}: решение не проходит тесты\n";
                if (isset($solved['error'])) {
                    echo "    {$solved['error']}\n";
                }
                if (trim($solved['stdout'] ?? '') !== '') {
                    echo '    ' . str_replace("\n", "\n    ", trim($solved['stdout'])) . "\n";
                }
                foreach ($solved['results'] ?? [] as $result) {
                    if (!$result['ok']) {
                        echo "    {$result['name']}: {$result['message']}\n";
                    }
                }
            }

            // пропуски ___ сами по себе — синтаксическая ошибка, поэтому заменяем их на null
            $starter = str_replace('___', 'null', $task['starter']);
            if (empty($task['hangs']) && $passed(PhpRunner::run($starter, $version, $task['tests']))) {
                $problems++;
                echo "✗ {$task['id']}: заготовка уже проходит все тесты\n";
            }
        }
    }
}

if ($skipped > 0) {
    echo "Пропущено заданий для PHP 7 (нет контейнера): $skipped\n";
}
echo $problems > 0 ? "\nПроблем: $problems\n" : "Всё в порядке: заданий на PHP $checked.\n";
exit($problems > 0 ? 1 : 0);
