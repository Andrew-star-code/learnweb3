<?php
$kinds = [
    'fill' => 'Дописать', 'fix' => 'Починить', 'tests' => 'По тестам', 'scratch' => 'С нуля',
    'quiz' => 'Вопрос', 'plan' => 'План',
];
?>
<p class="crumbs"><a href="/">Программа</a> / <?= e($lesson['block']['title']) ?></p>
<h1><?= e($lesson['title']) ?></h1>

<article class="prose"><?= $theory ?></article>

<h2 class="tasks-title">Практика</h2>
<?php foreach ($tasks as $n => $task): ?>
<?php $kind = $task['type'] === 'code' ? $task['kind'] : $task['type']; ?>
<section class="task <?= $task['status'] === 'done' ? 'is-done' : '' ?>" data-task="<?= $n ?>" id="task-<?= $n + 1 ?>">
    <header class="task-head">
        <span class="tag tag-<?= e($kind) ?>"><?= e($kinds[$kind]) ?></span>
        <h3><?= $n + 1 ?>. <?= e($task['title']) ?></h3>
        <span class="task-status" title="Задание выполнено">✓</span>
    </header>
    <div class="prose"><?= $task['html'] ?></div>
    <div class="task-body"></div>
</section>
<?php endforeach ?>

<nav class="lesson-nav">
    <?php if ($nav['prev']): ?><a class="btn" href="/lesson/<?= e($nav['prev']) ?>">← Назад</a><?php else: ?><span></span><?php endif ?>
    <?php if ($nav['next']): ?><a class="btn btn-primary" href="/lesson/<?= e($nav['next']) ?>">Следующий урок →</a>
    <?php else: ?><a class="btn" href="/">К программе</a><?php endif ?>
</nav>

<script type="application/json" id="tasks-data"><?= App\View::json($tasks) ?></script>
