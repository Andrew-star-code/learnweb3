<?php $percent = $totalTasks > 0 ? (int) round($doneTasks / $totalTasks * 100) : 0; ?>
<section class="hero">
    <div>
        <h1>Программа</h1>
        <p class="muted">Выполнено заданий: <?= $doneTasks ?> из <?= $totalTasks ?></p>
        <div class="bar"><div class="bar-fill" style="width: <?= $percent ?>%"></div></div>
    </div>
    <?php if ($continue): ?>
        <a class="btn btn-primary" href="<?= e($continue['url']) ?>">
            <?= $doneTasks > 0 ? 'Продолжить' : 'Начать' ?>: <?= e($continue['title']) ?> →
        </a>
    <?php endif ?>
</section>

<?php foreach ($blocks as $n => $block): ?>
<section class="block <?= $block['lessons'] ? '' : 'block-planned' ?>">
    <header class="block-head">
        <span class="block-num"><?= $n ?></span>
        <div>
            <h2><?= e($block['title']) ?></h2>
            <p class="muted"><?= e($block['summary']) ?></p>
        </div>
    </header>
    <?php if ($block['lessons']): ?>
    <ol class="lessons">
        <?php foreach ($block['lessons'] as $lesson): ?>
        <?php $state = $lesson['done'] >= $lesson['total'] ? 'done' : ($lesson['done'] > 0 ? 'partial' : 'new'); ?>
        <li>
            <a href="<?= e($lesson['url']) ?>">
                <span class="mark mark-<?= $state ?>"><?= $state === 'done' ? '✓' : '' ?></span>
                <span class="lesson-title"><?= e($lesson['title']) ?></span>
                <span class="muted"><?= $lesson['done'] ?>/<?= $lesson['total'] ?></span>
            </a>
        </li>
        <?php endforeach ?>
    </ol>
    <?php endif ?>
    <?php if ($block['planned']): ?>
    <p class="planned">
        <span class="tag">Скоро</span>
        <?= e(implode(' · ', $block['planned'])) ?>
    </p>
    <?php endif ?>
</section>
<?php endforeach ?>
