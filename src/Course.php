<?php
declare(strict_types=1);

namespace App;

/**
 * Читает содержимое курса с диска.
 *
 * content/course.php                — список блоков и уроков
 * content/<блок>/<урок>/lesson.md   — теория
 * content/<блок>/<урок>/tasks.php   — задания и карточки для повторения
 */
final class Course
{
    private static ?array $blocks = null;

    /** @return array<int, array> */
    public static function blocks(): array
    {
        if (self::$blocks === null) {
            self::$blocks = require CONTENT_DIR . '/course.php';
            foreach (self::$blocks as &$block) {
                $block['lessons'] = $block['lessons'] ?? [];
                $block['planned'] = $block['planned'] ?? [];
            }
        }
        return self::$blocks;
    }

    /** Урок целиком: заголовок, HTML теории, задания, карточки. */
    public static function lesson(string $blockId, string $lessonId): ?array
    {
        foreach (self::blocks() as $block) {
            if ($block['id'] !== $blockId || !in_array($lessonId, $block['lessons'], true)) {
                continue;
            }
            $dir = CONTENT_DIR . "/$blockId/$lessonId";
            $markdown = file_get_contents("$dir/lesson.md");
            $data = is_file("$dir/tasks.php") ? require "$dir/tasks.php" : [];

            // первая строка "# Заголовок" — название урока
            $title = $lessonId;
            if (preg_match('/^#\s+(.+)\n/', $markdown, $m)) {
                $title = trim($m[1]);
                $markdown = substr($markdown, strlen($m[0]));
            }

            $prefix = "$blockId/$lessonId";
            $tasks = [];
            foreach ($data['tasks'] ?? [] as $task) {
                $task['id'] = "$prefix/" . $task['id'];
                $tasks[] = $task;
            }
            $cards = [];
            foreach ($data['cards'] ?? [] as $n => $card) {
                $cards[] = ['id' => "$prefix/card-" . ($n + 1), 'q' => $card[0], 'a' => $card[1]];
            }

            return [
                'id' => $prefix,
                'block' => $block,
                'slug' => $lessonId,
                'title' => $title,
                'markdown' => $markdown,
                'tasks' => $tasks,
                'cards' => $cards,
            ];
        }
        return null;
    }

    public static function lessonTitle(string $blockId, string $lessonId): string
    {
        $file = CONTENT_DIR . "/$blockId/$lessonId/lesson.md";
        $first = is_file($file) ? (string) fgets(fopen($file, 'r')) : '';
        return preg_match('/^#\s+(.+)/', $first, $m) ? trim($m[1]) : $lessonId;
    }

    /** id заданий урока без загрузки теории — для подсчёта прогресса. */
    public static function taskIds(string $blockId, string $lessonId): array
    {
        $file = CONTENT_DIR . "/$blockId/$lessonId/tasks.php";
        $data = is_file($file) ? require $file : [];
        return array_map(function ($t) use ($blockId, $lessonId) {
            return "$blockId/$lessonId/" . $t['id'];
        }, $data['tasks'] ?? []);
    }

    public static function task(string $taskId): ?array
    {
        $parts = explode('/', $taskId);
        if (count($parts) !== 3) {
            return null;
        }
        $lesson = self::lesson($parts[0], $parts[1]);
        foreach ($lesson['tasks'] ?? [] as $task) {
            if ($task['id'] === $taskId) {
                return $task;
            }
        }
        return null;
    }

    public static function card(string $cardId): ?array
    {
        $parts = explode('/', $cardId);
        if (count($parts) !== 3) {
            return null;
        }
        $lesson = self::lesson($parts[0], $parts[1]);
        foreach ($lesson['cards'] ?? [] as $card) {
            if ($card['id'] === $cardId) {
                return $card + ['lessonTitle' => $lesson['title'], 'lessonUrl' => "/lesson/$parts[0]/$parts[1]"];
            }
        }
        return null;
    }

    /** Соседние уроки для кнопок «назад / дальше». */
    public static function neighbours(string $lessonKey): array
    {
        $all = [];
        foreach (self::blocks() as $block) {
            foreach ($block['lessons'] as $slug) {
                $all[] = $block['id'] . '/' . $slug;
            }
        }
        $pos = array_search($lessonKey, $all, true);
        return [
            'prev' => $pos > 0 ? $all[$pos - 1] : null,
            'next' => $pos !== false && $pos < count($all) - 1 ? $all[$pos + 1] : null,
        ];
    }
}
