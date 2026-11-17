<?php
return [
    'tasks' => [
        [
            'id' => 'min-version-quiz',
            'type' => 'quiz',
            'title' => 'С какой версии это работает',
            'text' => "```php\nfunction greet(?string \$name): void\n{\n    echo 'Привет, ' . (\$name ?? 'гость');\n}\n```\n\nКакая самая старая версия PHP сможет выполнить этот код?",
            'options' => ['7.0', '7.1', '7.4', '8.0'],
            'answer' => 1,
            'explain' => 'Оператор `??` и типы параметров есть уже в 7.0. А вот `?string` и `void` появились в 7.1 — на сервере с 7.0 файл не разберётся.',
        ],
        [
            'id' => 'parse-quiz',
            'type' => 'quiz',
            'title' => 'Функция, которую никто не вызывает',
            'text' => "Этот файл попал на сервер с PHP 7.0:\n\n```php\n<?php\n\necho \"Начало\\n\";\n\nfunction badge(int \$count): string\n{\n    return match (true) {\n        \$count > 99 => '99+',\n        default => (string) \$count,\n    };\n}\n\necho \"Конец\\n\";\n```\n\nФункция `badge` нигде не вызывается. Что произойдёт?",
            'options' => [
                'Выведется «Начало» и «Конец»: ошибка возникнет только при вызове badge',
                'Не выведется ничего: файл не пройдёт разбор',
                'Выведется «Начало», затем ошибка на строке с match',
                'PHP пропустит функцию и покажет предупреждение',
            ],
            'answer' => 1,
            'explain' => '`match` — новый синтаксис, а не функция. PHP сначала разбирает файл целиком и только потом выполняет. Одна незнакомая конструкция — и не выполнится ни одна строка, даже первая.',
        ],
        [
            'id' => 'polyfill',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'php' => '7',
            'title' => 'Две функции из будущего',
            'text' => "Проект написан под PHP 8, а сервер заказчика — на 7.0. Функций `str_starts_with` (PHP 8.0) и `array_key_last` (PHP 7.3) там нет.\n\nДопиши полифилы. Три пропуска: чем проверить, что функция ещё не объявлена; с чем сравнить результат `strncmp`, когда начала строк совпали; что вернуть для пустого массива.\n\nЗадание проверяется на PHP 7.0.",
            'starter' => <<<'PHP'
<?php

if (!___('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        // strncmp сравнивает первые N байт двух строк
        return strncmp($haystack, $needle, strlen($needle)) === ___;
    }
}

if (!function_exists('array_key_last')) {
    function array_key_last(array $array)
    {
        if (count($array) === 0) {
            return ___;
        }
        end($array);          // переставить внутренний указатель на последний элемент

        return key($array);   // ключ элемента под указателем
    }
}

var_dump(str_starts_with('латте', 'лат'));
var_dump(array_key_last(['эспрессо' => 150, 'латте' => 250]));
PHP,
            'tests' => <<<'PHP'
test('str_starts_with: начало совпало', function () { assert_eq(true, str_starts_with('латте', 'лат')); });
test('str_starts_with: не совпало', function () { assert_eq(false, str_starts_with('латте', 'тте')); });
test('str_starts_with: искомое длиннее строки', function () { assert_eq(false, str_starts_with('чай', 'чайник')); });
test('str_starts_with: пустое начало есть у любой строки', function () { assert_eq(true, str_starts_with('чай', '')); });
test('array_key_last: строковые ключи', function () { assert_eq('латте', array_key_last(['эспрессо' => 150, 'латте' => 250])); });
test('array_key_last: список', function () { assert_eq(2, array_key_last([10, 20, 30])); });
test('array_key_last: пустой массив → null', function () { assert_eq(null, array_key_last([])); });
PHP,
            'hints' => [
                'Первая проверка — `function_exists`. Вторую уже написали за тебя, она подсказывает первую.',
                '`strncmp` возвращает `0`, когда сравниваемые части равны.',
                'Настоящая `array_key_last` для пустого массива возвращает `null`.',
            ],
            'solution' => <<<'PHP'
<?php

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('array_key_last')) {
    function array_key_last(array $array)
    {
        if (count($array) === 0) {
            return null;
        }
        end($array);

        return key($array);
    }
}
PHP,
        ],
        [
            'id' => 'version-check',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Проверка версии',
            'text' => "Функция `isAtLeast` отвечает на вопрос «версия `\$current` не старее, чем `\$required`?». Автор превратил версии в дробные числа — и на простых примерах это даже работает.\n\nЗапусти код, посмотри на три вызова внизу и почини функцию.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function isAtLeast(string $current, string $required): bool
{
    return (float) $current >= (float) $required;
}

var_dump(isAtLeast('8.3.6', '7.4.0'));     // true — верно
var_dump(isAtLeast('7.0.4', '7.0.33'));    // должно быть false
var_dump(isAtLeast('7.10.0', '7.4.0'));    // должно быть true
PHP,
            'tests' => <<<'PHP'
test('8.3.6 не старее 7.4.0', function () { assert_eq(true, isAtLeast('8.3.6', '7.4.0')); });
test('7.0.33 старее 7.1.0', function () { assert_eq(false, isAtLeast('7.0.33', '7.1.0')); });
test('третье число: 7.0.4 старее 7.0.33', function () { assert_eq(false, isAtLeast('7.0.4', '7.0.33')); });
test('третье число: 7.0.33 не старее 7.0.4', function () { assert_eq(true, isAtLeast('7.0.33', '7.0.4')); });
test('двузначная минорная: 7.10.0 не старее 7.4.0', function () { assert_eq(true, isAtLeast('7.10.0', '7.4.0')); });
test('одинаковые версии', function () { assert_eq(true, isAtLeast('7.3.0', '7.3.0')); });
PHP,
            'hints' => [
                '`(float) \'7.0.33\'` — это `7.0`: всё после второй точки отбрасывается. А `(float) \'7.10.0\'` — это `7.1`.',
                'Версии сравнивает `version_compare($a, $b, \'>=\')` — по частям, как числа.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function isAtLeast(string $current, string $required): bool
{
    return version_compare($current, $required, '>=');
}
PHP,
        ],
        [
            'id' => 'min-version',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Минимальная версия для набора возможностей',
            'text' => "В константе `FEATURES` — кусочек таблицы из урока: возможность и версия, в которой она появилась.\n\nНапиши функцию `minVersion(array \$features): string`. Она получает список названий и возвращает самую старую версию PHP, на которой заработают они все.\n\n- Пустой список → `'7.0'`: старше в этом курсе версий нет.\n- Незнакомое название → `InvalidArgumentException` с текстом `Неизвестная возможность: имя`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

const FEATURES = [
    '??' => '7.0',
    'void' => '7.1',
    'nullable' => '7.1',
    'array_key_last' => '7.3',
    'fn' => '7.4',
    'typed-property' => '7.4',
    'match' => '8.0',
    'str_contains' => '8.0',
    'enum' => '8.1',
    'readonly' => '8.1',
];

function minVersion(array $features): string
{

}
PHP,
            'tests' => <<<'PHP'
test('одна возможность', function () { assert_eq('7.4', minVersion(['fn'])); });
test('побеждает самая новая', function () { assert_eq('8.0', minVersion(['void', 'match', 'fn'])); });
test('порядок в списке не важен', function () { assert_eq('8.1', minVersion(['enum', '??', 'nullable'])); });
test('всё из 7.0 → 7.0', function () { assert_eq('7.0', minVersion(['??'])); });
test('пустой список → 7.0', function () { assert_eq('7.0', minVersion([])); });
test('незнакомое название → исключение', function () {
    $e = assert_throws(function () { minVersion(['fn', 'pipe']); }, 'InvalidArgumentException');
    assert_eq('Неизвестная возможность: pipe', $e->getMessage());
});
PHP,
            'hints' => [
                'Заведи переменную с ответом `$min = \'7.0\'` и обойди список. Для каждого названия сначала проверь, что оно есть в таблице: `isset(FEATURES[$feature])`.',
                'Версию возможности сравни с текущим ответом: `if (version_compare(FEATURES[$feature], $min, \'>\')) { $min = FEATURES[$feature]; }`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

const FEATURES = [
    '??' => '7.0',
    'void' => '7.1',
    'nullable' => '7.1',
    'array_key_last' => '7.3',
    'fn' => '7.4',
    'typed-property' => '7.4',
    'match' => '8.0',
    'str_contains' => '8.0',
    'enum' => '8.1',
    'readonly' => '8.1',
];

function minVersion(array $features): string
{
    $min = '7.0';

    foreach ($features as $feature) {
        if (!isset(FEATURES[$feature])) {
            throw new InvalidArgumentException("Неизвестная возможность: $feature");
        }
        if (version_compare(FEATURES[$feature], $min, '>')) {
            $min = FEATURES[$feature];
        }
    }

    return $min;
}
PHP,
        ],
        [
            'id' => 'scanner-plan',
            'type' => 'plan',
            'title' => 'Сканер исходника: план',
            'text' => "Тебе прислали файл и спрашивают: «На нашем сервере это заработает?» Нужна функция `requiredVersion(string \$source): string`. Она получает **текст** PHP-файла и по характерным признакам определяет, какая версия ему нужна.\n\n| Признак в тексте | Версия |\n|---|---|\n| `??=` | 7.4 |\n| `fn(` | 7.4 |\n| `?->` | 8.0 |\n| `match (` | 8.0 |\n| `enum ` | 8.1 |\n| `readonly ` | 8.1 |\n\nЕсли признаков нет — `'7.0'`. Если их несколько — нужна самая новая из версий.\n\nЗапиши шаги. Подумай: в каком виде хранить таблицу и чем эта задача похожа на предыдущую.",
            'reference' => <<<'MD'
1. Записать таблицу признаков массивом: ключ — признак, значение — версия.
2. Завести ответ `$version = '7.0'`.
3. Обойти таблицу: для каждой пары «признак → версия»…
4. …проверить, встречается ли признак в тексте. — `strpos($source, $sign) !== false`
5. Если встречается и его версия новее текущего ответа — запомнить её. — `version_compare`
6. После цикла вернуть ответ.

На что обратить внимание:

- Это та же задача «найти максимум», что и `minVersion`: изменился только источник списка. Там возможности перечислял человек, здесь их находит поиск по тексту.
- Цикл идёт **по таблице**, а не по тексту. Перебирать символы исходника не нужно — искать умеет `strpos`.
- `strpos` возвращает `0`, если признак стоит в самом начале, поэтому сравнение строгое: `!== false`.
- Порядок строк в таблице не важен, раз мы каждый раз сравниваем версии. Не стоит полагаться на то, что «самые новые внизу».
- Сканер наивный: он найдёт `match (` и внутри строки или комментария. Для быстрой оценки этого хватает; настоящие инструменты разбирают код по правилам языка.
MD,
        ],
        [
            'id' => 'scanner',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Сканер исходника: код',
            'text' => "Напиши `requiredVersion(string \$source): string` по своему плану.\n\n| Признак в тексте | Версия |\n|---|---|\n| `??=` | 7.4 |\n| `fn(` | 7.4 |\n| `?->` | 8.0 |\n| `match (` | 8.0 |\n| `enum ` | 8.1 |\n| `readonly ` | 8.1 |\n\nБез признаков — `'7.0'`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
test('обычный код → 7.0', function () { assert_eq('7.0', requiredVersion('<?php $name = $user["name"] ?? "гость"; echo $user->name;')); });
test('стрелочная функция → 7.4', function () { assert_eq('7.4', requiredVersion('$prices = array_map(fn($p) => $p * 2, $prices);')); });
test('??= → 7.4', function () { assert_eq('7.4', requiredVersion('$count ??= 0;')); });
test('?-> → 8.0', function () { assert_eq('8.0', requiredVersion('echo $order?->customer?->name;')); });
test('match → 8.0', function () { assert_eq('8.0', requiredVersion("return match (\$code) {\n    200 => 'ok',\n};")); });
test('enum → 8.1', function () { assert_eq('8.1', requiredVersion("<?php\nenum Status: string { case New = 'new'; }")); });
test('readonly → 8.1', function () { assert_eq('8.1', requiredVersion('public function __construct(public readonly int $id) {}')); });
test('несколько признаков → самая новая версия', function () { assert_eq('8.0', requiredVersion('$f = fn($x) => $x?->id; $n ??= 1;')); });
test('порядок в тексте не важен', function () { assert_eq('8.1', requiredVersion("enum Size { case S; }\n\$f = fn(\$x) => \$x;")); });
test('признак в самом начале текста', function () { assert_eq('8.1', requiredVersion('readonly class Point {}')); });
test('пустой текст → 7.0', function () { assert_eq('7.0', requiredVersion('')); });
PHP,
            'hints' => [
                'Таблица: `$signs = [\'??=\' => \'7.4\', \'fn(\' => \'7.4\', \'?->\' => \'8.0\', \'match (\' => \'8.0\', \'enum \' => \'8.1\', \'readonly \' => \'8.1\'];`',
                'Цикл: `foreach ($signs as $sign => $since) { … }`. Внутри — проверка `strpos($source, $sign) !== false`.',
                'Обновление ответа — как в прошлом задании: `version_compare($since, $version, \'>\')`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function requiredVersion(string $source): string
{
    $signs = [
        '??=' => '7.4',
        'fn(' => '7.4',
        '?->' => '8.0',
        'match (' => '8.0',
        'enum ' => '8.1',
        'readonly ' => '8.1',
    ];

    $version = '7.0';
    foreach ($signs as $sign => $since) {
        if (strpos($source, $sign) !== false && version_compare($since, $version, '>')) {
            $version = $since;
        }
    }

    return $version;
}
PHP,
        ],
    ],
    'cards' => [
        ['Чем новый синтаксис отличается от новой функции с точки зрения старого PHP?', 'Синтаксис ломает весь файл при разборе (`Parse error`), ещё до выполнения. Вызов отсутствующей функции падает, только когда до него дошло выполнение (`Call to undefined function`).'],
        ['Как сравнить две версии PHP?', '`version_compare($a, $b, \'>=\')` или числом: `PHP_VERSION_ID >= 70400`. Обычное сравнение строк ошибается: `\'7.10\'` и `\'7.4\'`.'],
        ['Что такое полифил и зачем в нём `function_exists`?', 'Своя реализация функции, которой нет в старой версии. Проверка нужна, чтобы на новой версии не объявить функцию повторно — это фатальная ошибка.'],
        ['Чего из привычного нет в PHP 7.0? Назови хотя бы четыре вещи.', '`?int` и `void` (7.1), типы свойств, `fn` и `??=` (7.4), `match`, `?->`, продвижение свойств, `str_contains` (8.0), `enum` и `readonly` (8.1).'],
        ['Как проверить синтаксис файла, не выполняя его?', '`php -l файл.php` — интерпретатором той версии, которая стоит на сервере.'],
        ['Что нужно узнать до первой правки в чужом проекте?', 'Версию PHP на сервере. От неё зависит, какими конструкциями можно пользоваться.'],
    ],
];
