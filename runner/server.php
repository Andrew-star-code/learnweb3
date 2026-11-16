<?php
// Крошечный HTTP-сервис внутри контейнера php7: принимает код, выполняет его
// на «своей» версии PHP и возвращает результат. Снаружи Docker-сети не виден.
// Синтаксис — только PHP 7.0.

require __DIR__ . '/lib.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || !isset($input['code'])) {
    echo json_encode(array('version' => PHP_VERSION));   // проверка «жив ли сервис»
    return;
}

$tests = isset($input['tests']) ? $input['tests'] : null;
echo json_encode(runner_exec((string) $input['code'], $tests, 5));
