<?php
return [
    'tasks' => [
        [
            'id' => 'compare-quiz',
            'type' => 'quiz',
            'title' => 'Остаток на складе',
            'text' => "```php\n\$stock = ['эспрессо' => 0, 'латте' => 5];\n\nif (\$stock['эспрессо'] == 'нет в наличии') {\n    echo 'A';\n} else {\n    echo 'B';\n}\n```\n\nЧто выведет этот код?",
            'options' => [
                'A на обеих версиях',
                'B на обеих версиях',
                'A в PHP 7, B в PHP 8',
                'B в PHP 7, A в PHP 8',
            ],
            'answer' => 2,
            'explain' => 'Слева число `0`, справа строка без цифр. PHP 7 превращает строку в число — получается `0 == 0`. PHP 8 видит, что строка не числовая, и сравнивает как строки: `\'0\'` и `\'нет в наличии\'` не равны. Со строгим `===` ответ был бы B везде.',
        ],
        [
            'id' => 'precedence-quiz',
            'type' => 'quiz',
            'title' => 'Баллы на экране',
            'text' => "```php\necho 'Баллов: ' . 10 + 5;\n```\n\nЧто выведет этот код на **PHP 7.0**?",
            'options' => ['Баллов: 15', 'Баллов: 105', '5', 'Ошибку разбора'],
            'answer' => 2,
            'explain' => 'В PHP 7 склейка и сложение выполняются слева направо. Сначала получается строка `\'Баллов: 10\'`, затем к ней прибавляют 5. Строка начинается не с цифры, поэтому превращается в ноль: `0 + 5`. PHP 8 сначала складывает и выводит «Баллов: 15». Скобки вокруг `(10 + 5)` убирают разницу.',
        ],
        [
            'id' => 'same-everywhere',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'php' => 'both',
            'title' => 'Одинаково на обеих версиях',
            'text' => "Три маленькие функции из корзины кофейни. Заполни пропуски так, чтобы результат не зависел от версии PHP:\n\n1. `hasSize` — искать размер строго, без приведения типов;\n2. `lineTotal` — количество из формы считать нулём, если это не число;\n3. `perCup` — не делить на ноль.\n\nЗадание проверяется на двух версиях сразу. Кнопка «Запустить» тоже покажет обе колонки.",
            'starter' => <<<'PHP'
<?php

function hasSize(array $sizes, $size): bool
{
    return in_array($size, $sizes, ___);
}

function lineTotal(array $row): int
{
    $qty = ___($row['qty']) ? (int) $row['qty'] : 0;

    return $row['price'] * $qty;
}

function perCup(int $total, int $cups): float
{
    if ($cups ___ 0) {
        return 0.0;
    }

    return $total / $cups;
}

var_dump(hasSize([0, 1, 2], 'большой'));
var_dump(lineTotal(['price' => 250, 'qty' => '']));
var_dump(perCup(900, 0));
PHP,
            'tests' => <<<'PHP'
test('hasSize: текста нет среди чисел', function () { assert_eq(false, hasSize([0, 1, 2], 'большой')); });
test('hasSize: размер есть в списке', function () { assert_eq(true, hasSize(['S', 'M', 'L'], 'M')); });
test('hasSize: строка "1" — не число 1', function () { assert_eq(false, hasSize([1, 2, 3], '1')); });
test('lineTotal: количество строкой из формы', function () { assert_eq(500, lineTotal(['price' => 250, 'qty' => '2'])); });
test('lineTotal: пустое поле → 0', function () { assert_eq(0, lineTotal(['price' => 250, 'qty' => ''])); });
test('lineTotal: текст вместо числа → 0', function () { assert_eq(0, lineTotal(['price' => 250, 'qty' => 'две'])); });
test('perCup: обычное деление', function () { assert_eq(112.5, perCup(900, 8)); });
test('perCup: ноль чашек → 0.0', function () { assert_eq(0.0, perCup(900, 0)); });
PHP,
            'hints' => [
                'Третий аргумент `in_array` — `true`: сравнивать строго.',
                'Проверить, что значение — число или числовая строка: `is_numeric`.',
                'Сравнение с нулём — строгое: `===`. Тип параметра `int` гарантирует, что внутри функции это действительно целое число.',
            ],
            'solution' => <<<'PHP'
<?php

function hasSize(array $sizes, $size): bool
{
    return in_array($size, $sizes, true);
}

function lineTotal(array $row): int
{
    $qty = is_numeric($row['qty']) ? (int) $row['qty'] : 0;

    return $row['price'] * $qty;
}

function perCup(int $total, int $cups): float
{
    if ($cups === 0) {
        return 0.0;
    }

    return $total / $cups;
}
PHP,
        ],
        [
            'id' => 'after-upgrade',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'php' => 'both',
            'title' => 'После переезда',
            'text' => "Эти функции десять лет работали на PHP 7.0. Сервер обновили до PHP 8 — страница заказа стала падать, а в письмах клиентам изменился текст.\n\nЗапусти код на обеих версиях и сравни колонки. Затем почини функции так, чтобы на **обеих** версиях:\n\n- `orderTotal` считала нечисловое количество (пустое поле, текст) нулём и не падала;\n- `averageCheck` для нуля заказов возвращала `0.0`;\n- `totalLabel` возвращала строку вида `Итого с доставкой: 350 ₽`.\n\nОдна из трёх функций все эти годы работала неправильно — как раз на старой версии.",
            'starter' => <<<'PHP'
<?php

function orderTotal(array $rows)
{
    $total = 0;
    foreach ($rows as $row) {
        $total += $row['price'] * $row['qty'];
    }

    return $total;
}

function averageCheck($total, $orders)
{
    return round($total / $orders);
}

function totalLabel($total, $delivery)
{
    return 'Итого с доставкой: ' . $total + $delivery . ' ₽';
}

$rows = [
    ['price' => 250, 'qty' => '1'],
    ['price' => 150, 'qty' => ''],       // количество в форме не заполнили
];

echo totalLabel(250, 100), "\n";
echo orderTotal($rows), "\n";
echo averageCheck(0, 0), "\n";
PHP,
            'tests' => <<<'PHP'
test('orderTotal: обычный заказ', function () {
    assert_eq(650, orderTotal([['price' => 250, 'qty' => '2'], ['price' => 150, 'qty' => 1]]));
});
test('orderTotal: пустое количество считается нулём', function () {
    assert_eq(250, orderTotal([['price' => 250, 'qty' => '1'], ['price' => 150, 'qty' => '']]));
});
test('orderTotal: текст вместо количества считается нулём', function () {
    assert_eq(0, orderTotal([['price' => 250, 'qty' => 'две']]));
});
test('orderTotal: пустой заказ → 0', function () { assert_eq(0, orderTotal([])); });
test('averageCheck: обычный случай', function () { assert_eq(333.0, (float) averageCheck(1000, 3)); });
test('averageCheck: ноль заказов → 0.0', function () { assert_eq(0.0, (float) averageCheck(0, 0)); });
test('totalLabel: сумма посчитана и подпись на месте', function () {
    assert_eq('Итого с доставкой: 350 ₽', totalLabel(250, 100));
});
test('totalLabel: бесплатная доставка', function () {
    assert_eq('Итого с доставкой: 990 ₽', totalLabel(990, 0));
});
PHP,
            'hints' => [
                'В `orderTotal` перед умножением проверь количество: `$qty = is_numeric($row[\'qty\']) ? $row[\'qty\'] : 0;`',
                'В `averageCheck` — проверка до деления: `if ($orders == 0) { return 0.0; }`. Здесь нестрогое сравнение уместно: количество может прийти и числом, и строкой `\'0\'`, а обе стороны числовые.',
                'В `totalLabel` не хватает скобок вокруг сложения. Именно она была сломана на PHP 7: посмотри на левую и правую колонку.',
            ],
            'solution' => <<<'PHP'
<?php

function orderTotal(array $rows)
{
    $total = 0;
    foreach ($rows as $row) {
        $qty = is_numeric($row['qty']) ? $row['qty'] : 0;
        $total += $row['price'] * $qty;
    }

    return $total;
}

function averageCheck($total, $orders)
{
    if ($orders == 0) {
        return 0.0;
    }

    return round($total / $orders);
}

function totalLabel($total, $delivery)
{
    return 'Итого с доставкой: ' . ($total + $delivery) . ' ₽';
}
PHP,
        ],
        [
            'id' => 'average-rating',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'php' => 'both',
            'title' => 'Средняя оценка',
            'text' => "Оценки кофейни приходят из формы отзывов вперемешку: числа, строки, пустые значения, иногда слова.\n\nНапиши `averageRating(array \$ratings): float` — среднюю оценку, округлённую до одного знака.\n\n- Учитываются числа и числовые строки. Пробелы по краям строки не мешают: `' 5 '` — это оценка 5.\n- Всё остальное (`null`, `''`, `'отлично'`, `'5 звёзд'`) пропускается.\n- Если учитывать нечего — `0.0`.\n\nЗадание проверяется на двух версиях. Здесь спрятана ловушка: `is_numeric('5 ')` — с пробелом **в конце** — отвечает по-разному в PHP 7 и PHP 8.",
            'starter' => <<<'PHP'
<?php

function averageRating(array $ratings): float
{

}
PHP,
            'tests' => <<<'PHP'
test('целые оценки', function () { assert_eq(4.0, averageRating([5, 4, 3])); });
test('числовые строки учитываются', function () { assert_eq(4.5, averageRating([5, '4'])); });
test('дробные оценки', function () { assert_eq(4.0, averageRating([4.5, '3.5'])); });
test('округление до одного знака', function () { assert_eq(4.3, averageRating([5, 4, 4])); });
test('слова и пустые значения пропускаются', function () { assert_eq(4.0, averageRating([5, 'отлично', null, '', 3, '5 звёзд'])); });
test('пробелы по краям не мешают', function () { assert_eq(4.0, averageRating([' 5 ', '3 '])); });
test('нет ни одной оценки → 0.0', function () { assert_eq(0.0, averageRating([])); });
test('только мусор → 0.0', function () { assert_eq(0.0, averageRating(['отлично', null, ''])); });
PHP,
            'hints' => [
                'Нужны сумма и счётчик. В цикле: если значение — строка, убрать пробелы (`trim`); если после этого оно не `is_numeric` — `continue`.',
                '`trim` вызывай только для строк: `if (is_string($value)) { $value = trim($value); }`. Тогда `null` и числа в него не попадут.',
                'После цикла: если счётчик равен нулю — вернуть `0.0`, иначе `round($sum / $count, 1)`.',
            ],
            'solution' => <<<'PHP'
<?php

function averageRating(array $ratings): float
{
    $sum = 0;
    $count = 0;

    foreach ($ratings as $value) {
        if (is_string($value)) {
            $value = trim($value);
        }
        if (!is_numeric($value)) {
            continue;
        }
        $sum += $value;
        $count++;
    }

    if ($count === 0) {
        return 0.0;
    }

    return round($sum / $count, 1);
}
PHP,
        ],
        [
            'id' => 'risks-plan',
            'type' => 'plan',
            'title' => 'Журнал перед переездом: план',
            'text' => "Сервер переводят с PHP 7.3 на 8. Тебе дали журнал ошибок за неделю и просят список мест, где новая версия **упадёт**. Строки журнала выглядят так:\n\n```text\n[12-Mar-2024 10:15:02 UTC] PHP Warning:  Division by zero in /var/www/shop/cart.php on line 48\n[12-Mar-2024 10:15:07 UTC] PHP Notice:  Undefined index: city in /var/www/shop/profile.php on line 12\n[12-Mar-2024 10:16:40 UTC] PHP Warning:  A non-numeric value encountered in /var/www/shop/cart.php on line 31\n```\n\nНужна функция `migrationRisks(array \$lines): array`. Опасной считается строка уровня `Warning`, текст которой начинается с одного из трёх сообщений:\n\n- `Division by zero`\n- `A non-numeric value encountered`\n- `count(): Parameter must be an array`\n\nРезультат — список мест в виде `/var/www/shop/cart.php:48`, без повторов, в порядке первого появления. Строки, не похожие на запись журнала, пропускаются.\n\nЗапиши шаги.",
            'reference' => <<<'MD'
1. Завести список опасных сообщений и пустой массив для результата.
2. Обойти строки журнала.
3. Разобрать строку: достать уровень, текст сообщения, файл и номер строки. — `preg_match` с группами
4. Если строка не разобралась — пропустить.
5. Если уровень не `Warning` — пропустить.
6. Проверить, начинается ли текст с одного из опасных сообщений. Если нет — пропустить.
7. Собрать место: файл, двоеточие, номер строки.
8. Если такого места в результате ещё нет — добавить.
9. Вернуть результат.

На что обратить внимание:

- Запись журнала читается **с конца**: хвост ` in ФАЙЛ on line НОМЕР` всегда одинаков, а текст сообщения в середине бывает любым. Шаблон: `/PHP (\w+):\s+(.*) in (\S+) on line (\d+)$/`.
- После двоеточия в журнале два пробела, а не один — поэтому `\s+`.
- «Начинается с» в PHP 8 — `str_starts_with`, а в коде для старого сервера — `strpos($message, $prefix) === 0`.
- Шаг 6 — это цикл в цикле. Его удобно вынести в отдельную маленькую функцию `isDangerous($message)`, которая отвечает `true` или `false`.
- Повторы: одно и то же место за неделю встретится тысячи раз. Проверка `in_array($place, $result, true)` перед добавлением — или ключи массива и `array_keys` в конце.
MD,
        ],
        [
            'id' => 'risks',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Журнал перед переездом: код',
            'text' => "Напиши `migrationRisks(array \$lines): array` по своему плану.\n\nОпасна строка уровня `Warning`, текст которой начинается с `Division by zero`, `A non-numeric value encountered` или `count(): Parameter must be an array`. Результат — места вида `файл:строка`, без повторов, в порядке первого появления.\n\nОбразец строки журнала:\n\n```text\n[12-Mar-2024 10:15:02 UTC] PHP Warning:  Division by zero in /var/www/shop/cart.php on line 48\n```",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
function logLine($level, $message, $file, $line) {
    return "[12-Mar-2024 10:15:02 UTC] PHP $level:  $message in /var/www/shop/$file on line $line";
}
test('деление на ноль', function () {
    assert_eq(['/var/www/shop/cart.php:48'], migrationRisks([logLine('Warning', 'Division by zero', 'cart.php', 48)]));
});
test('нечисловое значение', function () {
    assert_eq(['/var/www/shop/cart.php:31'], migrationRisks([logLine('Warning', 'A non-numeric value encountered', 'cart.php', 31)]));
});
test('count() от не-массива: у сообщения длинный хвост', function () {
    assert_eq(['/var/www/shop/list.php:7'], migrationRisks([
        logLine('Warning', 'count(): Parameter must be an array or an object that implements Countable', 'list.php', 7),
    ]));
});
test('замечания пропускаются', function () {
    assert_eq([], migrationRisks([logLine('Notice', 'Undefined index: city', 'profile.php', 12)]));
});
test('прочие предупреждения пропускаются', function () {
    assert_eq([], migrationRisks([logLine('Warning', 'file_get_contents(prices.csv): failed to open stream: No such file or directory', 'import.php', 5)]));
});
test('уровень важен: Notice с опасным текстом не считается', function () {
    assert_eq([], migrationRisks([logLine('Notice', 'Division by zero', 'cart.php', 48)]));
});
test('повторы убираются, порядок — по первому появлению', function () {
    assert_eq(['/var/www/shop/cart.php:48', '/var/www/shop/report.php:101'], migrationRisks([
        logLine('Warning', 'Division by zero', 'cart.php', 48),
        logLine('Notice', 'Undefined variable: total', 'cart.php', 50),
        logLine('Warning', 'A non-numeric value encountered', 'report.php', 101),
        logLine('Warning', 'Division by zero', 'cart.php', 48),
    ]));
});
test('одно и то же сообщение в разных строках файла — разные места', function () {
    assert_eq(['/var/www/shop/cart.php:48', '/var/www/shop/cart.php:90'], migrationRisks([
        logLine('Warning', 'Division by zero', 'cart.php', 48),
        logLine('Warning', 'Division by zero', 'cart.php', 90),
    ]));
});
test('посторонние строки пропускаются', function () {
    assert_eq([], migrationRisks(['', 'Stack trace:', '#0 {main}', 'PHP Warning без подробностей']));
});
test('пустой журнал → []', function () { assert_eq([], migrationRisks([])); });
PHP,
            'hints' => [
                'Разбор строки: `preg_match(\'/PHP (\w+):\s+(.*) in (\S+) on line (\d+)$/\', $line, $m)`. Тогда `$m[1]` — уровень, `$m[2]` — сообщение, `$m[3]` — файл, `$m[4]` — номер.',
                'Проверка сообщения: цикл по списку опасных начал и `str_starts_with($m[2], $prefix)`.',
                'Место: `$place = $m[3] . \':\' . $m[4];` Добавляй его, только если `!in_array($place, $result, true)`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function isDangerous(string $message): bool
{
    $prefixes = [
        'Division by zero',
        'A non-numeric value encountered',
        'count(): Parameter must be an array',
    ];

    foreach ($prefixes as $prefix) {
        if (str_starts_with($message, $prefix)) {
            return true;
        }
    }

    return false;
}

function migrationRisks(array $lines): array
{
    $result = [];

    foreach ($lines as $line) {
        if (!preg_match('/PHP (\w+):\s+(.*) in (\S+) on line (\d+)$/', $line, $m)) {
            continue;
        }
        if ($m[1] !== 'Warning' || !isDangerous($m[2])) {
            continue;
        }

        $place = $m[3] . ':' . $m[4];
        if (!in_array($place, $result, true)) {
            $result[] = $place;
        }
    }

    return $result;
}
PHP,
        ],
    ],
    'cards' => [
        ['Чему равно `0 == \'кофе\'` в PHP 7 и в PHP 8 и почему?', 'В 7 — `true`: строка превращается в число 0. В 8 — `false`: нечисловая строка сравнивается с числом как строка.'],
        ['Где нестрогое сравнение спрятано так, что его не видно?', 'В `in_array` и `array_search` без третьего аргумента `true` и в `switch`.'],
        ['Что делает `100 * \'\'` в PHP 7.0 и в PHP 8?', 'В 7.0 молча даёт `0`. В 8 бросает `TypeError`. Типичный источник — пустое поле формы.'],
        ['Что происходит при делении на ноль в PHP 7 и в PHP 8?', 'В 7 — предупреждение, скрипт идёт дальше с бессмысленным результатом. В 8 — исключение `DivisionByZeroError`.'],
        ['Как меняется `echo \'Итого: \' . $a + $b;` между версиями?', 'В PHP 7 склейка и сложение идут слева направо — подпись теряется. В PHP 8 сложение выполняется раньше. Скобки вокруг суммы убирают разницу.'],
        ['С чего начинать подготовку старого кода к PHP 8?', 'Включить на старой версии показ всех ошибок и разобрать журнал: почти каждое замечание и предупреждение — будущая поломка или смена поведения.'],
    ],
];
