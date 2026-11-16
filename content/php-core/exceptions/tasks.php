<?php
return [
    'tasks' => [
        [
            'id' => 'order-quiz',
            'type' => 'quiz',
            'title' => 'Какой catch сработает',
            'text' => "```php\ntry {\n    throw new InvalidArgumentException('плохой аргумент');\n} catch (RuntimeException \$e) {\n    echo 'A';\n} catch (Exception \$e) {\n    echo 'B';\n} finally {\n    echo 'C';\n}\n```\n\n`InvalidArgumentException` наследуется от `LogicException`, а тот — от `Exception`. Что будет выведено?",
            'options' => ['AC', 'BC', 'ABC', 'C'],
            'answer' => 1,
            'explain' => 'Блоки `catch` проверяются сверху вниз по классу. `InvalidArgumentException` не является `RuntimeException`, поэтому первый блок пропускается. Второй подходит: любое исключение — это `Exception`. `finally` выполняется всегда.',
        ],
        [
            'id' => 'silent-quiz',
            'type' => 'quiz',
            'title' => 'Молчаливая ошибка',
            'text' => "```php\n\$settings = json_decode('{\"host\": \"localhost\"');   // нет закрывающей скобки\n\necho \$settings['host'];\n```\n\nЧто произойдёт?",
            'options' => [
                'json_decode бросит JsonException',
                'json_decode молча вернёт null, проблема проявится на следующей строке',
                'Выведется localhost: PHP исправит JSON сам',
                'Синтаксическая ошибка PHP',
            ],
            'answer' => 1,
            'explain' => 'По умолчанию `json_decode` не сообщает об ошибке, а возвращает `null`. Код падает позже и в другом месте, и причину приходится искать. С флагом `JSON_THROW_ON_ERROR` ошибка возникает сразу и там, где случилась.',
        ],
        [
            'id' => 'read-config',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'title' => 'Разбор с подстраховкой',
            'text' => "Допиши функцию `readConfig`: она разбирает JSON-текст настроек в массив, а если текст повреждён — возвращает пустой массив.\n\nЗаполни два пропуска: флаг, который заставляет `json_decode` бросать исключение, и класс этого исключения.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function readConfig(string $json): array
{
    try {
        return json_decode($json, true, 512, ___);
    } catch (___ $e) {
        return [];
    }
}

print_r(readConfig('{"host": "localhost"}'));
print_r(readConfig('сломано'));
PHP,
            'tests' => <<<'PHP'
test('верный JSON', function () { assert_eq(['host' => 'localhost', 'port' => 5432], readConfig('{"host": "localhost", "port": 5432}')); });
test('повреждённый JSON → []', function () { assert_eq([], readConfig('{"host": "localhost"')); });
test('не JSON → []', function () { assert_eq([], readConfig('сломано')); });
test('пустая строка → []', function () { assert_eq([], readConfig('')); });
PHP,
            'hints' => [
                'Флаг — константа `JSON_THROW_ON_ERROR`.',
                'Класс исключения — `JsonException`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function readConfig(string $json): array
{
    try {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        return [];
    }
}
PHP,
        ],
        [
            'id' => 'swallow-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Заглушённая ошибка',
            'text' => "Функция `pricePerItem` написана в духе «лишь бы не упало»: оператор `@`, перехват всего подряд и строка вместо результата. Вызывающий код получает то число, то текст и не знает, что что-то пошло не так.\n\nПеределай:\n\n- при количестве, равном нулю, функция бросает `InvalidArgumentException` с текстом `Количество не может быть нулём`;\n- в остальных случаях возвращает `float`;\n- `@` и `try/catch` убрать.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function pricePerItem(int $total, int $count)
{
    try {
        return @($total / $count);
    } catch (Throwable $e) {
        return 'ошибка';
    }
}

var_dump(pricePerItem(300, 3));
var_dump(pricePerItem(300, 0));
PHP,
            'tests' => <<<'PHP'
test('300 / 3 → 100.0', function () { assert_eq(100.0, (float) pricePerItem(300, 3)); });
test('0 / 5 → 0.0', function () { assert_eq(0.0, (float) pricePerItem(0, 5)); });
test('1 / 4 → 0.25', function () { assert_eq(0.25, pricePerItem(1, 4)); });
test('count = 0 → InvalidArgumentException', function () {
    $e = assert_throws(function () { pricePerItem(300, 0); }, 'InvalidArgumentException');
    assert_eq('Количество не может быть нулём', $e->getMessage());
});
test('в коде нет оператора @ и перехвата', function () {
    $source = (new ReflectionFunction('pricePerItem'));
    $lines = array_slice(file($source->getFileName()), $source->getStartLine() - 1, $source->getEndLine() - $source->getStartLine() + 1);
    $body = implode('', $lines);
    assert_true(strpos($body, '@') === false && strpos($body, 'catch') === false, 'Убери @ и try/catch из функции');
});
PHP,
            'hints' => [
                'Проверка в начале: `if ($count === 0) { throw new InvalidArgumentException(\'Количество не может быть нулём\'); }`',
                'Дальше просто `return $total / $count;` — и тип результата `float` в объявлении функции.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function pricePerItem(int $total, int $count): float
{
    if ($count === 0) {
        throw new InvalidArgumentException('Количество не может быть нулём');
    }

    return $total / $count;
}
PHP,
        ],
        [
            'id' => 'validation',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Своё исключение',
            'text' => "Напиши:\n\n1. Класс `ValidationException`, наследник `RuntimeException`. Конструктор принимает сообщение и имя поля; имя поля возвращает метод `getField(): string`.\n2. Функцию `validateAge(\$value): int`. Она возвращает значение, если это целое число от 0 до 150. Иначе бросает `ValidationException` с полем `'age'` и текстом:\n   - `Возраст должен быть целым числом` — если это не `int`;\n   - `Возраст вне допустимого диапазона` — если число меньше 0 или больше 150.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

class ValidationException extends RuntimeException
{

}

function validateAge($value): int
{

}
PHP,
            'tests' => <<<'PHP'
test('верный возраст возвращается', function () { assert_eq([30, 0, 150], [validateAge(30), validateAge(0), validateAge(150)]); });
test('ValidationException — это RuntimeException с полем', function () {
    $e = new ValidationException('текст', 'email');
    assert_true($e instanceof RuntimeException, 'Нет наследования от RuntimeException');
    assert_eq(['текст', 'email'], [$e->getMessage(), $e->getField()]);
});
test('строка → ошибка про целое число', function () {
    $e = assert_throws(function () { validateAge('30'); }, 'ValidationException');
    assert_eq(['Возраст должен быть целым числом', 'age'], [$e->getMessage(), $e->getField()]);
});
test('дробное число → ошибка про целое число', function () {
    assert_eq('Возраст должен быть целым числом', assert_throws(function () { validateAge(30.5); }, 'ValidationException')->getMessage());
});
test('отрицательное → ошибка про диапазон', function () {
    assert_eq('Возраст вне допустимого диапазона', assert_throws(function () { validateAge(-1); }, 'ValidationException')->getMessage());
});
test('151 → ошибка про диапазон', function () {
    assert_eq('Возраст вне допустимого диапазона', assert_throws(function () { validateAge(151); }, 'ValidationException')->getMessage());
});
PHP,
            'hints' => [
                'Конструктор: `public function __construct(string $message, private string $field) { parent::__construct($message); }`',
                'Проверка типа: `if (!is_int($value)) { throw new ValidationException(\'Возраст должен быть целым числом\', \'age\'); }`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class ValidationException extends RuntimeException
{
    public function __construct(string $message, private string $field)
    {
        parent::__construct($message);
    }

    public function getField(): string
    {
        return $this->field;
    }
}

function validateAge($value): int
{
    if (!is_int($value)) {
        throw new ValidationException('Возраст должен быть целым числом', 'age');
    }
    if ($value < 0 || $value > 150) {
        throw new ValidationException('Возраст вне допустимого диапазона', 'age');
    }

    return $value;
}
PHP,
        ],
        [
            'id' => 'chain',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Понятная ошибка с причиной',
            'text' => "Напиши функцию `loadSettings(string \$json): array`. Она разбирает текст настроек и проверяет, что в них есть ключ `host`.\n\n- Если JSON повреждён, бросает `RuntimeException` с текстом `Файл настроек повреждён`. Исходное `JsonException` сохраняется как предыдущее исключение.\n- Если ключа `host` нет, бросает `RuntimeException` с текстом `Не задан параметр host`.\n- Иначе возвращает массив настроек.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function loadSettings(string $json): array
{

}

print_r(loadSettings('{"host": "localhost"}'));
PHP,
            'tests' => <<<'PHP'
test('верные настройки возвращаются', function () { assert_eq(['host' => 'localhost', 'port' => 5432], loadSettings('{"host": "localhost", "port": 5432}')); });
test('повреждённый JSON → RuntimeException с понятным текстом', function () {
    $e = assert_throws(function () { loadSettings('{"host": '); }, 'RuntimeException');
    assert_eq('Файл настроек повреждён', $e->getMessage());
});
test('исходная причина сохранена', function () {
    $e = assert_throws(function () { loadSettings('сломано'); }, 'RuntimeException');
    assert_true($e->getPrevious() instanceof JsonException, 'В getPrevious() должно лежать исходное JsonException');
});
test('нет ключа host → своя ошибка', function () {
    $e = assert_throws(function () { loadSettings('{"port": 5432}'); }, 'RuntimeException');
    assert_eq('Не задан параметр host', $e->getMessage());
});
test('у ошибки про host нет предыдущего исключения', function () {
    $e = assert_throws(function () { loadSettings('{}'); }, 'RuntimeException');
    assert_eq(null, $e->getPrevious());
});
PHP,
            'hints' => [
                'Разбор — в `try` с флагом `JSON_THROW_ON_ERROR`. В `catch (JsonException $e)` — `throw new RuntimeException(\'Файл настроек повреждён\', 0, $e);`',
                'Проверка ключа стоит после `try/catch`, а не внутри него: `if (!isset($settings[\'host\'])) { throw new RuntimeException(\'Не задан параметр host\'); }`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function loadSettings(string $json): array
{
    try {
        $settings = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        throw new RuntimeException('Файл настроек повреждён', 0, $e);
    }

    if (!isset($settings['host'])) {
        throw new RuntimeException('Не задан параметр host');
    }

    return $settings;
}
PHP,
        ],
        [
            'id' => 'config-plan',
            'type' => 'plan',
            'title' => 'Файл настроек: план',
            'text' => "Задача из блока JavaScript: разобрать текст вида\n\n```text\n# подключение к базе\nhost=localhost\nport=5432\n\nname=shop\n```\n\nв массив `['host' => 'localhost', 'port' => '5432', 'name' => 'shop']`.\n\nПустые строки и строки с `#` в начале пропускаются. Если в значимой строке нет знака `=`, бросается исключение `ConfigException` с текстом `Строка 3: нет знака =`, а номер строки доступен через метод `getLineNumber()`.\n\nЗапиши шаги для PHP. Какие функции заменят `split`, `trim`, `startsWith`, `indexOf`, `slice`?",
            'reference' => <<<'MD'
1. Завести пустой массив для результата.
2. Разбить текст на строки. — `explode("\n", $text)`
3. Пройти по строкам вместе с индексом. — `foreach ($lines as $index => $line)`
4. Убрать пробелы по краям. — `trim`
5. Пустую строку и комментарий пропустить. — `$line === ''`, `str_starts_with($line, '#')`, `continue`
6. Найти позицию первого `=`. — `strpos($line, '=')`
7. Если знака нет — бросить `ConfigException` с номером строки `$index + 1`.
8. Ключ — часть до знака, значение — часть после; у обеих убрать пробелы. — `substr`
9. Записать в массив и после цикла вернуть его.

На что обратить внимание:

- `"\n"` обязательно в **двойных** кавычках. В одинарных это два символа — косая черта и буква.
- `strpos` возвращает `false`, если знака нет, и `0`, если он первый. Сравнивать нужно строго: `=== false`.
- `foreach` с ключом сразу даёт индекс — отдельный счётчик не нужен.
- Здесь `substr` и `strpos` без `mb_` допустимы: позиция и срез считаются в одних и тех же единицах — байтах, поэтому кириллица в значениях не пострадает.
MD,
        ],
        [
            'id' => 'config',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Файл настроек: код',
            'text' => "Напиши класс `ConfigException` (наследник `RuntimeException` с методом `getLineNumber(): int`) и функцию `parseConfig(string \$text): array` по своему плану.\n\nВ значении может встретиться свой знак `=` — делить нужно по первому.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
test('простые настройки', function () { assert_eq(['host' => 'localhost', 'port' => '5432'], parseConfig("host=localhost\nport=5432")); });
test('комментарии и пустые строки пропускаются', function () { assert_eq(['host' => 'localhost', 'name' => 'shop'], parseConfig("# база\nhost=localhost\n\nname=shop\n")); });
test('пробелы вокруг убираются', function () { assert_eq(['host' => 'localhost'], parseConfig("  host = localhost  ")); });
test('знак = внутри значения', function () { assert_eq(['url' => 'http://x.ru/?a=1'], parseConfig('url=http://x.ru/?a=1')); });
test('кириллица в значении', function () { assert_eq(['title' => 'Кофейня «Зерно»'], parseConfig('title = Кофейня «Зерно»')); });
test('пустой текст → []', function () { assert_eq([], parseConfig('')); });
test('строка без = → ConfigException с номером', function () {
    $e = assert_throws(function () { parseConfig("host=localhost\n# комментарий\nсломано\nport=1"); }, 'ConfigException');
    assert_eq(['Строка 3: нет знака =', 3], [$e->getMessage(), $e->getLineNumber()]);
});
test('ConfigException — наследник RuntimeException', function () {
    assert_true(assert_throws(function () { parseConfig('сломано'); }, 'ConfigException') instanceof RuntimeException, 'Нет наследования от RuntimeException');
});
PHP,
            'hints' => [
                'Исключение: конструктор принимает сообщение и номер строки, сохраняет номер в закрытое свойство и вызывает `parent::__construct($message)`.',
                'Цикл: `foreach (explode("\n", $text) as $index => $line) { $line = trim($line); … }`',
                'Поиск знака: `$pos = strpos($line, \'=\'); if ($pos === false) { throw new ConfigException(\'Строка \' . ($index + 1) . \': нет знака =\', $index + 1); }`',
                'Части: `trim(substr($line, 0, $pos))` и `trim(substr($line, $pos + 1))`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class ConfigException extends RuntimeException
{
    public function __construct(string $message, private int $lineNumber)
    {
        parent::__construct($message);
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}

function parseConfig(string $text): array
{
    $result = [];

    foreach (explode("\n", $text) as $index => $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            throw new ConfigException('Строка ' . ($index + 1) . ': нет знака =', $index + 1);
        }

        $result[trim(substr($line, 0, $pos))] = trim(substr($line, $pos + 1));
    }

    return $result;
}
PHP,
        ],
    ],
    'cards' => [
        ['Как `catch` в PHP выбирает, какую ошибку ловить?', 'По классу, указанному в скобках. Блоки проверяются сверху вниз, срабатывает первый подходящий; неподходящее исключение летит дальше.'],
        ['Чем `Exception` отличается от `Error`?', '`Exception` — ожидаемые ситуации, которые бросает код. `Error` — ошибки в самом коде, их бросает PHP (`TypeError` и другие). Общий предок — `Throwable`.'],
        ['Как заставить `json_decode` сообщать об ошибке?', 'Флагом `JSON_THROW_ON_ERROR` — тогда он бросает `JsonException`. Без флага молча возвращает `null`.'],
        ['Как бросить понятную ошибку, не потеряв техническую причину?', 'Передать исходное исключение третьим аргументом: `throw new RuntimeException(\'текст\', 0, $e);` Оно доступно через `getPrevious()`.'],
        ['Что делает оператор `@` и почему его избегают?', 'Глушит предупреждение у вызова. Ошибка остаётся, её просто не видно — аналог пустого `catch`.'],
        ['Как проверять результат старых функций вроде `file_get_contents`?', 'Явно: они возвращают `false` при неудаче. `if ($text === false) { throw new RuntimeException(…); }`'],
    ],
];
