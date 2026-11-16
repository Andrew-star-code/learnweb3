<?php
return [
    'tasks' => [
        [
            'id' => 'copy-quiz',
            'type' => 'quiz',
            'title' => 'Копия или ссылка',
            'text' => "```php\n\$a = [1, 2, 3];\n\$b = \$a;\n\$b[] = 4;\n\necho count(\$a);\n```\n\nЧто будет выведено?",
            'options' => ['3', '4', '0', 'Будет ошибка'],
            'answer' => 0,
            'explain' => 'Массив в PHP при присваивании копируется. `$b` — независимая копия, и изменения в ней не затрагивают `$a`. В JavaScript тот же код дал бы 4.',
        ],
        [
            'id' => 'active-names',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'title' => 'Отбор и преобразование',
            'text' => 'Допиши функцию `activeNames`: она возвращает имена активных пользователей обычным списком с ключами 0, 1, 2…',
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function activeNames(array $users): array
{
    $active = array_filter($users, fn($user) => ___);
    $names = array_map(fn($user) => ___, $active);

    return ___($names);
}

print_r(activeNames([
    ['name' => 'Аня', 'active' => true],
    ['name' => 'Борис', 'active' => false],
    ['name' => 'Вера', 'active' => true],
]));
PHP,
            'tests' => <<<'PHP'
$users = [
    ['name' => 'Аня', 'active' => true],
    ['name' => 'Борис', 'active' => false],
    ['name' => 'Вера', 'active' => true],
];
test('только активные, ключи по порядку', function () use ($users) { assert_eq(['Аня', 'Вера'], activeNames($users)); });
test('нет активных → пустой массив', function () { assert_eq([], activeNames([['name' => 'Гриша', 'active' => false]])); });
test('пустой массив', function () { assert_eq([], activeNames([])); });
test('первый неактивен — ключи всё равно с нуля', function () {
    assert_eq(['Вера'], activeNames([['name' => 'Борис', 'active' => false], ['name' => 'Вера', 'active' => true]]));
});
PHP,
            'hints' => [
                'Доступ к полю — в квадратных скобках: `$user[\'active\']` и `$user[\'name\']`.',
                '`array_filter` сохраняет исходные ключи, поэтому в результате может оказаться `[0 => \'Аня\', 2 => \'Вера\']`. Перенумеровать — `array_values`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function activeNames(array $users): array
{
    $active = array_filter($users, fn($user) => $user['active']);
    $names = array_map(fn($user) => $user['name'], $active);

    return array_values($names);
}
PHP,
        ],
        [
            'id' => 'sort-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Что вернула сортировка',
            'text' => "Функция `cheapSorted` должна вернуть цены не выше лимита, по возрастанию. Она падает — запусти и прочитай сообщение.\n\nВ PHP сообщение об ошибке устроено так же, как в JavaScript: тип, что произошло, файл и строка.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function cheapSorted(array $prices, int $limit): array
{
    $cheap = array_filter($prices, fn($price) => $price <= $limit);

    return sort($cheap);
}

print_r(cheapSorted([300, 100, 250, 90], 250));
PHP,
            'tests' => <<<'PHP'
test('[300, 100, 250, 90], лимит 250 → [90, 100, 250]', function () { assert_eq([90, 100, 250], cheapSorted([300, 100, 250, 90], 250)); });
test('ничего не подходит → []', function () { assert_eq([], cheapSorted([300, 400], 100)); });
test('всё подходит', function () { assert_eq([1, 2, 3], cheapSorted([3, 1, 2], 10)); });
test('числа сортируются как числа', function () { assert_eq([9, 25, 100], cheapSorted([100, 25, 9], 1000)); });
PHP,
            'hints' => [
                'Сообщение говорит: возвращаемое значение должно быть `array`, а вернулось `bool`. Функция `sort` возвращает не массив, а `true`.',
                '`sort` меняет сам массив. Вызови её отдельной строкой, а вернуть нужно `$cheap`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function cheapSorted(array $prices, int $limit): array
{
    $cheap = array_filter($prices, fn($price) => $price <= $limit);
    sort($cheap);

    return $cheap;
}
PHP,
        ],
        [
            'id' => 'cart-total',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Сумма корзины',
            'text' => "Напиши функцию `cartTotal(array \$items): int`. Каждый элемент — массив с ключами `price` и `qty`, результат — общая стоимость.\n\nМожно циклом `foreach` с накопителем, можно через `array_reduce` — как тебе удобнее.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function cartTotal(array $items): int
{

}

echo cartTotal([
    ['price' => 100, 'qty' => 2],
    ['price' => 50, 'qty' => 1],
]);
PHP,
            'tests' => <<<'PHP'
test('две позиции → 250', function () { assert_eq(250, cartTotal([['price' => 100, 'qty' => 2], ['price' => 50, 'qty' => 1]])); });
test('одна позиция', function () { assert_eq(297, cartTotal([['price' => 99, 'qty' => 3]])); });
test('пустая корзина → 0', function () { assert_eq(0, cartTotal([])); });
PHP,
            'hints' => [
                'Накопление: `$total = 0;`, в цикле `$total += $item[\'price\'] * $item[\'qty\'];`, в конце `return $total;`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function cartTotal(array $items): int
{
    $total = 0;

    foreach ($items as $item) {
        $total += $item['price'] * $item['qty'];
    }

    return $total;
}

// То же через array_reduce:
// return array_reduce($items, fn($sum, $item) => $sum + $item['price'] * $item['qty'], 0);
PHP,
        ],
        [
            'id' => 'count-words',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Массив как счётчик',
            'text' => "Напиши функцию `countWords(array \$words): array`, которая считает, сколько раз встретилось каждое слово.\n\n`['кот', 'пёс', 'кот']` → `['кот' => 2, 'пёс' => 1]`\n\nПорядок ключей — по первому появлению слова.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function countWords(array $words): array
{

}

print_r(countWords(['кот', 'пёс', 'кот']));
PHP,
            'tests' => <<<'PHP'
test("['кот', 'пёс', 'кот']", function () { assert_eq(['кот' => 2, 'пёс' => 1], countWords(['кот', 'пёс', 'кот'])); });
test('одно слово', function () { assert_eq(['да' => 1], countWords(['да'])); });
test('пустой массив → []', function () { assert_eq([], countWords([])); });
test('три одинаковых', function () { assert_eq(['a' => 3], countWords(['a', 'a', 'a'])); });
PHP,
            'hints' => [
                'Пустой массив до цикла, внутри — увеличение значения по ключу-слову.',
                'При первой встрече ключа ещё нет: `$counts[$word] = ($counts[$word] ?? 0) + 1;`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function countWords(array $words): array
{
    $counts = [];

    foreach ($words as $word) {
        $counts[$word] = ($counts[$word] ?? 0) + 1;
    }

    return $counts;
}

// В PHP для этого есть готовая функция: array_count_values($words)
PHP,
        ],
        [
            'id' => 'top-plan',
            'type' => 'plan',
            'title' => 'Лучшие результаты: план',
            'text' => "Задача знакома по блоку JavaScript. Есть результаты теста:\n\n```php\n[\n    ['name' => 'Аня', 'score' => 82],\n    ['name' => 'Борис', 'score' => 95],\n    ['name' => 'Вера', 'score' => 67],\n]\n```\n\nНужно вернуть имена `\$n` лучших участников, от лучшего к худшему.\n\nЗапиши шаги и рядом с каждым — функцию PHP, которая его выполняет. Чем решение будет отличаться от версии на JavaScript?",
            'reference' => <<<'MD'
1. Отсортировать результаты по убыванию баллов. — `usort($results, fn($a, $b) => $b['score'] <=> $a['score'])`
2. Взять первые `$n` элементов. — `array_slice($results, 0, $n)`
3. Достать из каждого имя. — `array_column($top, 'name')`

Отличия от JavaScript:

- **Копию делать не нужно.** Параметр `$results` — уже копия переданного массива, сортировка не затронет данные вызывающего кода.
- **Цепочкой не записать.** `usort` меняет массив на месте и возвращает `true`, поэтому это отдельная строка, а не звено цепочки.
- **Сравнение — оператором `<=>`**, а не вычитанием. Для убывания аргументы меняются местами: `$b <=> $a`.
- **Для «достать поле из каждого» есть готовая `array_column`** — короче, чем `array_map`.
MD,
        ],
        [
            'id' => 'top',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Лучшие результаты: код',
            'text' => 'Напиши функцию `topNames(array $results, int $n): array` по своему плану.',
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
$results = [
    ['name' => 'Аня', 'score' => 82],
    ['name' => 'Борис', 'score' => 95],
    ['name' => 'Вера', 'score' => 67],
    ['name' => 'Гриша', 'score' => 90],
];
test('двое лучших', function () use ($results) { assert_eq(['Борис', 'Гриша'], topNames($results, 2)); });
test('один лучший', function () use ($results) { assert_eq(['Борис'], topNames($results, 1)); });
test('n больше длины — все, по убыванию', function () use ($results) { assert_eq(['Борис', 'Гриша', 'Аня', 'Вера'], topNames($results, 10)); });
test('n = 0 → []', function () use ($results) { assert_eq([], topNames($results, 0)); });
test('исходный массив не меняется', function () use ($results) {
    $copy = $results;
    topNames($copy, 2);
    assert_eq('Аня', $copy[0]['name']);
});
PHP,
            'hints' => [
                'Сортировка отдельной строкой: `usort($results, fn($a, $b) => $b[\'score\'] <=> $a[\'score\']);`',
                'Дальше: `return array_column(array_slice($results, 0, $n), \'name\');`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function topNames(array $results, int $n): array
{
    usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

    return array_column(array_slice($results, 0, $n), 'name');
}
PHP,
        ],
    ],
    'cards' => [
        ['Что происходит при `$b = $a`, если `$a` — массив?', 'Создаётся независимая копия. Изменения в `$b` не затрагивают `$a` — в отличие от JavaScript.'],
        ['Как добавить элемент в конец массива и узнать длину?', '`$items[] = $value;` и `count($items)`.'],
        ['В чём ловушка `array_filter`?', 'Он сохраняет исходные ключи, в списке появляются «дырки». Чтобы получить обычный список — `array_values(array_filter(…))`.'],
        ['Что возвращают `sort` и `usort`?', '`true`, а не массив. Они меняют массив на месте, поэтому вызываются отдельной строкой.'],
        ['Что делает оператор `<=>`?', 'Сравнивает и возвращает -1, 0 или 1 — то, что нужно функции сравнения в `usort`. Для убывания аргументы меняют местами.'],
        ['Как достать одно поле из каждого элемента «таблицы»?', '`array_column($rows, \'name\')`.'],
    ],
];
