<?php
return [
    'tasks' => [
        [
            'id' => 'bytes-quiz',
            'type' => 'quiz',
            'title' => 'Сколько букв в слове',
            'text' => "Что выведет `echo strlen('кот');` при кодировке UTF-8?",
            'options' => ['3', '6', '4', 'Будет ошибка'],
            'answer' => 1,
            'explain' => '`strlen` считает байты, а русская буква в UTF-8 занимает два. Длину в символах даёт `mb_strlen`. Проверка «имя не длиннее 20 символов» через `strlen` отрежет русские имена вдвое раньше.',
        ],
        [
            'id' => 'strpos-quiz',
            'type' => 'quiz',
            'title' => 'Ловушка strpos',
            'text' => "```php\n\$path = '/admin/users';\n\nif (strpos(\$path, '/admin')) {\n    echo 'админка';\n} else {\n    echo 'обычная страница';\n}\n```\n\nЧто будет выведено?",
            'options' => ['админка', 'обычная страница', 'Ничего', 'Будет ошибка'],
            'answer' => 1,
            'explain' => 'Подстрока найдена в самом начале, `strpos` вернула `0` — а ноль в условии ложен. Правильно: `strpos($path, \'/admin\') === 0` или, в PHP 8, `str_starts_with($path, \'/admin\')`. В легаси эта ошибка встречается постоянно, в том числе в проверках доступа.',
        ],
        [
            'id' => 'initials',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'title' => 'Инициалы',
            'text' => "Допиши функцию `initials`: `'Иван Петров'` → `'И. П.'`\n\nПервая буква слова — `mb_substr(\$word, 0, 1)`: начало и длина.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function initials(string $fullName): string
{
    $words = ___(' ', $fullName);
    $letters = array_map(fn($word) => ___($word, 0, 1) . '.', $words);

    return ___(' ', $letters);
}

echo initials('Иван Петров');
PHP,
            'tests' => <<<'PHP'
test("'Иван Петров' → 'И. П.'", function () { assert_eq('И. П.', initials('Иван Петров')); });
test('три слова', function () { assert_eq('А. М. В.', initials('Анна Мария Вольф')); });
test('одно слово', function () { assert_eq('П.', initials('Платон')); });
test('латиница', function () { assert_eq('J. S.', initials('John Smith')); });
PHP,
            'hints' => [
                'Разбить строку — `explode`, склеить массив — `implode`. У обеих разделитель идёт первым аргументом.',
                'Первая буква с учётом кириллицы — `mb_substr`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function initials(string $fullName): string
{
    $words = explode(' ', $fullName);
    $letters = array_map(fn($word) => mb_substr($word, 0, 1) . '.', $words);

    return implode(' ', $letters);
}
PHP,
        ],
        [
            'id' => 'truncate',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Обрезанные буквы',
            'text' => "Функция `truncate` обрезает длинный текст для карточки товара и добавляет многоточие. На английском тексте она работает, на русском — режет раньше времени и иногда выдаёт испорченный символ. Запусти и посмотри.\n\nИсправь так, чтобы длина считалась в символах.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function truncate(string $text, int $max): string
{
    if (strlen($text) <= $max) {
        return $text;
    }

    return substr($text, 0, $max) . '…';
}

echo truncate('привет, мир', 6) . "\n";
echo truncate('кот', 6) . "\n";
PHP,
            'tests' => <<<'PHP'
test("('привет, мир', 6) → 'привет…'", function () { assert_eq('привет…', truncate('привет, мир', 6)); });
test('короткий русский текст не меняется', function () { assert_eq('кот', truncate('кот', 3)); });
test('длина ровно max — не меняется', function () { assert_eq('привет', truncate('привет', 6)); });
test('латиница по-прежнему работает', function () { assert_eq('hel…', truncate('hello world', 3)); });
test('max = 1', function () { assert_eq('д…', truncate('да или нет', 1)); });
PHP,
            'hints' => [
                '`strlen` и `substr` считают байты. Нужны их варианты для любого текста.',
                'Замены: `mb_strlen` и `mb_substr`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function truncate(string $text, int $max): string
{
    if (mb_strlen($text) <= $max) {
        return $text;
    }

    return mb_substr($text, 0, $max) . '…';
}
PHP,
        ],
        [
            'id' => 'ucfirst',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Заглавная буква для любого языка',
            'text' => "Встроенная `ucfirst` делает первую букву заглавной только для латиницы. Напиши свою `mbUcfirst(string \$text): string`, которая работает и с кириллицей.\n\nОстальные буквы не меняются. Пустая строка остаётся пустой.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function mbUcfirst(string $text): string
{

}

echo mbUcfirst('привет');
PHP,
            'tests' => <<<'PHP'
test("'привет' → 'Привет'", function () { assert_eq('Привет', mbUcfirst('привет')); });
test("'hello' → 'Hello'", function () { assert_eq('Hello', mbUcfirst('hello')); });
test('остальные буквы не меняются', function () { assert_eq('ЁЛКА и Сосна', mbUcfirst('ёЛКА и Сосна')); });
test('уже заглавная', function () { assert_eq('Аня', mbUcfirst('Аня')); });
test('одна буква', function () { assert_eq('Я', mbUcfirst('я')); });
test('пустая строка', function () { assert_eq('', mbUcfirst('')); });
PHP,
            'hints' => [
                'Раздели строку на две части: первая буква `mb_substr($text, 0, 1)` и остаток `mb_substr($text, 1)`.',
                'Первую часть — в верхний регистр через `mb_strtoupper`, и склеить точкой.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function mbUcfirst(string $text): string
{
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}
PHP,
        ],
        [
            'id' => 'receipt-line',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Строка чека',
            'text' => "Напиши функцию `receiptLine(string \$name, int \$qty, float \$price): string` для печати чека.\n\n```php\nreceiptLine('Эфиопия', 2, 890.5)   // 'Эфиопия × 2 = 1781.00 ₽'\n```\n\nСумма — количество на цену, с двумя знаками после точки. Удобнее всего через `sprintf`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function receiptLine(string $name, int $qty, float $price): string
{

}

echo receiptLine('Эфиопия', 2, 890.5);
PHP,
            'tests' => <<<'PHP'
test('две позиции', function () { assert_eq('Эфиопия × 2 = 1781.00 ₽', receiptLine('Эфиопия', 2, 890.5)); });
test('одна позиция', function () { assert_eq('Раф × 1 = 250.00 ₽', receiptLine('Раф', 1, 250.0)); });
test('копейки', function () { assert_eq('Сироп × 3 = 149.97 ₽', receiptLine('Сироп', 3, 49.99)); });
test('ноль штук', function () { assert_eq('Чай × 0 = 0.00 ₽', receiptLine('Чай', 0, 100.0)); });
PHP,
            'hints' => [
                'Шаблон: `\'%s × %d = %.2f ₽\'`. `%s` — строка, `%d` — целое, `%.2f` — число с двумя знаками.',
                '`return sprintf(\'%s × %d = %.2f ₽\', $name, $qty, $qty * $price);`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function receiptLine(string $name, int $qty, float $price): string
{
    return sprintf('%s × %d = %.2f ₽', $name, $qty, $qty * $price);
}
PHP,
        ],
        [
            'id' => 'query-plan',
            'type' => 'plan',
            'title' => 'Параметры из адреса: план',
            'text' => "Строку параметров `name=anna&page=2&sort=price` нужно превратить в массив `['name' => 'anna', 'page' => '2', 'sort' => 'price']`. Для пустой строки — пустой массив.\n\nТы уже писал это на JavaScript. Запиши шаги для PHP и назови функции, которые понадобятся.",
            'reference' => <<<'MD'
1. Если строка пустая — вернуть пустой массив.
2. Завести пустой массив для результата.
3. Разбить строку по `&`. — `explode('&', $query)`
4. Пройти по парам. — `foreach`
5. Каждую пару разбить по `=` на ключ и значение. — `explode('=', $pair, 2)`
6. Записать значение под ключом. — `$result[$key] = $value`
7. Вернуть массив.

Третий аргумент `explode` — ограничение числа частей. С ним значение, внутри которого есть свой знак `=`, не развалится.

Шаг 1 нужен по той же причине, что и в JS: `explode('&', '')` возвращает не пустой массив, а массив с одной пустой строкой.

В настоящем PHP-приложении писать это не нужно: параметры адреса уже лежат в массиве `$_GET`, а для произвольной строки есть `parse_str`. Но теперь ты знаешь, что они делают внутри.
MD,
        ],
        [
            'id' => 'query',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Параметры из адреса: код',
            'text' => 'Напиши функцию `parseQuery(string $query): array` по своему плану. Значения остаются строками. Параметр без знака `=` получает пустую строку.',
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
test('три параметра', function () { assert_eq(['name' => 'anna', 'page' => '2', 'sort' => 'price'], parseQuery('name=anna&page=2&sort=price')); });
test('один параметр', function () { assert_eq(['q' => 'php'], parseQuery('q=php')); });
test('пустая строка → []', function () { assert_eq([], parseQuery('')); });
test('пустое значение', function () { assert_eq(['q' => '', 'page' => '1'], parseQuery('q=&page=1')); });
test('знак = внутри значения', function () { assert_eq(['filter' => 'price=100'], parseQuery('filter=price=100')); });
test('параметр без знака =', function () { assert_eq(['debug' => '', 'page' => '2'], parseQuery('debug&page=2')); });
test('кириллица', function () { assert_eq(['q' => 'кофе'], parseQuery('q=кофе')); });
PHP,
            'hints' => [
                'Каркас: ранний выход для пустой строки, пустой массив, `foreach (explode(\'&\', $query) as $pair)`.',
                'Разбор пары: `$parts = explode(\'=\', $pair, 2);` Ключ — `$parts[0]`, значение — `$parts[1] ?? \'\'`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function parseQuery(string $query): array
{
    if ($query === '') {
        return [];
    }

    $result = [];

    foreach (explode('&', $query) as $pair) {
        $parts = explode('=', $pair, 2);
        $result[$parts[0]] = $parts[1] ?? '';
    }

    return $result;
}
PHP,
        ],
    ],
    'cards' => [
        ['Почему `strlen(\'кот\')` возвращает 6?', 'Обычные строковые функции считают байты, а русская буква в UTF-8 занимает два. Для символов — `mb_strlen`, `mb_substr`, `mb_strtoupper`.'],
        ['В чём ловушка `if (strpos($text, $part))`?', 'Позиция `0` — «найдено в начале» — в условии ложна. Правильно: `strpos(…) !== false`, а в PHP 8 — `str_contains`.'],
        ['Какие функции PHP соответствуют `split` и `join`?', '`explode(разделитель, строка)` и `implode(разделитель, массив)`.'],
        ['Как собрать строку из значений по шаблону?', '`sprintf(\'%s: %d шт. по %.2f ₽\', $name, $qty, $price)` — `%s` строка, `%d` целое, `%.2f` два знака после точки.'],
        ['Что нужно сделать со строкой перед выводом в HTML?', 'Экранировать: `htmlspecialchars($text)`. Это защита от XSS, серверный аналог `textContent`.'],
    ],
];
