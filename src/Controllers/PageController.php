<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Course;
use App\Markdown;
use App\Progress;
use App\View;

final class PageController
{
    /** Главная: вся программа, прогресс и кнопка «Продолжить». */
    public function home(): void
    {
        $progress = Progress::all();
        $blocks = [];
        $continue = null;
        $doneTasks = 0;
        $totalTasks = 0;

        foreach (Course::blocks() as $block) {
            $lessons = [];
            foreach ($block['lessons'] as $slug) {
                $ids = Course::taskIds($block['id'], $slug);
                $done = count(array_filter($ids, function ($id) use ($progress) {
                    return ($progress[$id]['status'] ?? '') === 'done';
                }));
                $lesson = [
                    'url' => "/lesson/{$block['id']}/$slug",
                    'title' => Course::lessonTitle($block['id'], $slug),
                    'done' => $done,
                    'total' => count($ids),
                ];
                if ($continue === null && $done < count($ids)) {
                    $continue = $lesson;
                }
                $doneTasks += $done;
                $totalTasks += count($ids);
                $lessons[] = $lesson;
            }
            $blocks[] = ['lessons' => $lessons] + $block;
        }

        echo View::render('home', [
            'title' => 'Программа',
            'blocks' => $blocks,
            'continue' => $continue,
            'doneTasks' => $doneTasks,
            'totalTasks' => $totalTasks,
        ]);
    }

    public function lesson(array $params): void
    {
        $lesson = Course::lesson($params['block'], $params['lesson']);
        if ($lesson === null) {
            http_response_code(404);
            echo View::render('error', ['title' => 'Урок не найден', 'message' => 'Такого урока нет.']);
            return;
        }
        Progress::addCards($lesson['cards']);
        $progress = Progress::all();

        // Клиенту отдаём всё, кроме решений: их он запрашивает отдельно, по кнопке.
        $tasks = [];
        foreach ($lesson['tasks'] as $task) {
            $saved = $progress[$task['id']] ?? null;
            $task['html'] = Markdown::render($task['text'] ?? '');
            $task['status'] = $saved['status'] ?? 'new';
            $task['attempts'] = (int) ($saved['attempts'] ?? 0);
            $task['saved'] = $saved['code'] ?? null;
            if (isset($task['explain'])) {
                $task['explain'] = Markdown::render($task['explain']);
            }
            if (isset($task['hints'])) {
                $task['hints'] = array_map([Markdown::class, 'render'], $task['hints']);
            }
            if (($task['lang'] ?? '') === 'html') {
                $task['sample'] = $task['solution']; // для вкладки «Образец» в предпросмотре
            }
            unset($task['solution'], $task['reference'], $task['text']);
            if (($task['lang'] ?? '') === 'php') {
                unset($task['tests']); // PHP-тесты выполняет сервер, клиенту они не нужны
            }
            $tasks[] = $task;
        }

        echo View::render('lesson', [
            'title' => $lesson['title'],
            'lesson' => $lesson,
            'theory' => Markdown::render($lesson['markdown']),
            'tasks' => $tasks,
            'nav' => Course::neighbours($lesson['id']),
            'scripts' => ['preview.js', 'lesson.js'],
        ]);
    }

    public function review(): void
    {
        $cards = [];
        foreach (Progress::dueCards() as $id) {
            $card = Course::card($id);
            if ($card !== null) {
                $cards[] = [
                    'id' => $card['id'],
                    'q' => Markdown::render($card['q']),
                    'a' => Markdown::render($card['a']),
                    'lessonTitle' => $card['lessonTitle'],
                    'lessonUrl' => $card['lessonUrl'],
                ];
            }
        }
        echo View::render('review', ['title' => 'Повторение', 'cards' => $cards, 'scripts' => ['review.js']]);
    }

    public function sandbox(): void
    {
        echo View::render('sandbox', [
            'title' => 'Песочница',
            'php7' => (bool) getenv('PHP7_RUNNER_URL'),
            'scripts' => ['sandbox.js'],
        ]);
    }
}
