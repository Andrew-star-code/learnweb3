<?php
// Печатает все задания курса одним JSON — им пользуется tools/verify.js.
require dirname(__DIR__) . '/src/bootstrap.php';

$all = [];
foreach (App\Course::blocks() as $block) {
    foreach ($block['lessons'] as $slug) {
        $lesson = App\Course::lesson($block['id'], $slug);
        foreach ($lesson['tasks'] as $task) {
            $all[] = $task;
        }
        // заодно проверяем, что теория и тексты заданий рендерятся без ошибок
        App\Markdown::render($lesson['markdown']);
    }
}
echo json_encode($all, JSON_UNESCAPED_UNICODE);
