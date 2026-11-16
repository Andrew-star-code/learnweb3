<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e($csrf) ?>">
    <meta name="php7" content="<?= getenv('PHP7_RUNNER_URL') ? '1' : '0' ?>">
    <title><?= e($title) ?> — learnweb</title>
    <link rel="stylesheet" href="/vendor/codemirror/codemirror.css">
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a class="logo" href="/">learn<span>web</span></a>
        <nav>
            <a href="/">Программа</a>
            <a href="/review">Повторение<?php if ($dueCount > 0): ?> <span class="badge"><?= $dueCount ?></span><?php endif ?></a>
            <a href="/sandbox">Песочница</a>
        </nav>
    </div>
</header>
<main class="page">
<?= $content ?>
</main>
<script src="/vendor/codemirror/codemirror.js"></script>
<script src="/vendor/codemirror/addon/runmode.js"></script>
<script src="/vendor/codemirror/addon/matchbrackets.js"></script>
<script src="/vendor/codemirror/addon/closebrackets.js"></script>
<script src="/vendor/codemirror/addon/comment.js"></script>
<script src="/vendor/codemirror/mode/xml.js"></script>
<script src="/vendor/codemirror/mode/javascript.js"></script>
<script src="/vendor/codemirror/mode/css.js"></script>
<script src="/vendor/codemirror/mode/htmlmixed.js"></script>
<script src="/vendor/codemirror/mode/clike.js"></script>
<script src="/vendor/codemirror/mode/php.js"></script>
<script src="/vendor/codemirror/mode/sql.js"></script>
<script src="/assets/core.js"></script>
<?php foreach ($scripts ?? [] as $script): ?>
<script src="/assets/<?= e($script) ?>"></script>
<?php endforeach ?>
</body>
</html>
