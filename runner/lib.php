<?php
// Запуск учебного PHP-кода в отдельном процессе с ограничением по времени.
//
// ВАЖНО: этот файл выполняется и на PHP 8, и на PHP 7.0 (контейнер php7),
// поэтому здесь только синтаксис, доступный в 7.0: без типов свойств,
// без стрелочных функций, без nullable-типов и void.

/**
 * @param string      $code    код ученика (начинается с <?php)
 * @param string|null $tests   PHP-код тестов (без <?php) или null, если просто запускаем
 * @param int         $timeout секунд до принудительной остановки
 * @return array stdout, exitCode, timedOut, results (если были тесты), version
 */
function runner_exec($code, $tests = null, $timeout = 5)
{
    $dir = sys_get_temp_dir() . '/learnweb_' . bin2hex(random_bytes(8));
    mkdir($dir, 0700);
    file_put_contents($dir . '/code.php', $code);
    $entry = $dir . '/code.php';

    $marker = null;
    if ($tests !== null) {
        $marker = '<<<' . bin2hex(random_bytes(8)) . '>>>';
        file_put_contents($dir . '/tests.php', "<?php\n" . $tests);
        file_put_contents($dir . '/main.php', runner_harness($marker));
        $entry = $dir . '/main.php';
    }

    // exec заменяет оболочку процессом php — иначе proc_terminate убил бы только sh
    $cmd = 'exec ' . escapeshellarg(PHP_BINARY)
        . ' -d display_errors=1 -d error_reporting=-1 -d html_errors=0 -d log_errors=0'
        . ' -d memory_limit=64M ' . escapeshellarg($entry);

    $proc = proc_open($cmd, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, $dir);
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $out = '';
    $timedOut = false;
    $deadline = microtime(true) + $timeout;
    $exitCode = -1;
    while (true) {
        $out .= stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
        if (strlen($out) > 200000) {            // не даём бесконечному echo съесть память
            $out = substr($out, 0, 200000) . "\n… вывод обрезан";
            proc_terminate($proc, 9);
            break;
        }
        $status = proc_get_status($proc);
        if (!$status['running']) {
            $exitCode = $status['exitcode'];
            $out .= stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
            break;
        }
        if (microtime(true) > $deadline) {
            $timedOut = true;
            proc_terminate($proc, 9);
            break;
        }
        usleep(20000);
    }
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($proc);

    array_map('unlink', glob($dir . '/*'));
    rmdir($dir);

    // в сообщениях об ошибках оставляем только имя файла, без временной папки
    $out = str_replace($dir . '/', '', $out);

    $results = null;
    if ($marker !== null) {
        $pos = strrpos($out, $marker);
        if ($pos !== false) {
            $results = json_decode(substr($out, $pos + strlen($marker)), true);
            $out = rtrim(substr($out, 0, $pos));
        }
    }

    return array(
        'stdout' => $out,
        'exitCode' => $exitCode,
        'timedOut' => $timedOut,
        'results' => $results,
        'version' => PHP_VERSION,
    );
}

/** Обвязка для тестов: подключает код ученика, затем тесты, в конце печатает результаты. */
function runner_harness($marker)
{
    return '<?php
$__results = array();
$__output = "";

function test($name, $fn) {
    global $__results;
    try {
        $fn();
        $__results[] = array("name" => $name, "ok" => true);
    } catch (\Throwable $e) {
        $__results[] = array("name" => $name, "ok" => false, "message" => $e->getMessage());
    }
}
/** Значение в коротком читаемом виде для сообщений тестов: [90,100] вместо array ( 0 => 90, ... ). */
function __show($value) {
    if (is_object($value)) {
        return "объект " . get_class($value);
    }
    if (is_float($value) && floor($value) == $value && abs($value) < 1e15) {
        return number_format($value, 1, ".", "");   // 4.0, а не 4 — чтобы было видно, что это float
    }
    $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $json === false ? var_export($value, true) : $json;
}
function assert_eq($expected, $actual, $message = "") {
    if ($expected !== $actual) {
        $types = gettype($expected) !== gettype($actual)
            ? " (типы: " . gettype($expected) . " и " . gettype($actual) . ")" : "";
        throw new \Exception(($message !== "" ? $message . ": " : "")
            . "ожидалось " . __show($expected) . ", получено " . __show($actual) . $types);
    }
}
function assert_true($value, $message = "Условие не выполнено") {
    if ($value !== true) { throw new \Exception($message); }
}
/** Проверяет, что функция бросает исключение (при желании — заданного класса). */
function assert_throws($fn, $class = "Throwable", $message = "") {
    try {
        $fn();
    } catch (\Throwable $e) {
        if (!($e instanceof $class)) {
            throw new \Exception(($message !== "" ? $message . ": " : "")
                . "ожидалось исключение " . $class . ", брошено " . get_class($e));
        }
        return $e;
    }
    throw new \Exception($message !== "" ? $message : "Ожидалось исключение, но код выполнился без него");
}
/** Всё, что код ученика напечатал через echo/print. */
function output() { global $__output; return $__output; }

ob_start();
require __DIR__ . "/code.php";
$__output = ob_get_clean();
echo $__output;

require __DIR__ . "/tests.php";
echo "\n' . $marker . '" . json_encode($__results);
';
}
