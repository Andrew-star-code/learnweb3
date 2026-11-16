<?php
return [
    'tasks' => [
        [
            'id' => 'plus-quiz',
            'type' => 'quiz',
            'title' => 'Плюс и точка',
            'text' => "Чему равны выражения `'5' + 3` и `'5' . 3` в PHP?",
            'options' => ["'53' и '53'", "8 и '53'", "'53' и 8", '8 и 8'],
            'answer' => 1,
            'explain' => 'В PHP у каждого действия свой оператор: плюс только складывает числа (строка `\'5\'` превращается в число), точка только склеивает строки. Той путаницы, что в JavaScript с плюсом, здесь нет.',
        ],
        [
            'id' => 'falsy-quiz',
            'type' => 'quiz',
            'title' => 'Ложные значения',
            'text' => "Пользователь ввёл в поле «Количество» число ноль. Значение пришло строкой `'0'`. Что выведет код?\n\n```php\n\$qty = '0';\n\nif (\$qty) {\n    echo 'задано';\n} else {\n    echo 'пусто';\n}\n```",
            'options' => ['задано', 'пусто', 'Ничего', 'Будет ошибка'],
            'answer' => 1,
            'explain' => 'Строка `\'0\'` в PHP считается ложной — в отличие от JavaScript. Поэтому «задано ли значение» проверяют явно: `$qty !== \'\'`.',
        ],
        [
            'id' => 'greeting',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'title' => 'Подстановка и склейка',
            'text' => "Допиши функцию `greeting`: она возвращает строку вида `Привет, Аня! Через год тебе будет 31.`\n\nИмя подставляется прямо в строку в двойных кавычках, а выражение `\$age + 1` приклеивается точками.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function greeting(string $name, int $age): string
{
    return "Привет, ___! Через год тебе будет " ___ ($age + 1) ___ ".";
}

echo greeting('Аня', 30);
PHP,
            'tests' => <<<'PHP'
test("greeting('Аня', 30)", function () { assert_eq('Привет, Аня! Через год тебе будет 31.', greeting('Аня', 30)); });
test("greeting('Борис', 0)", function () { assert_eq('Привет, Борис! Через год тебе будет 1.', greeting('Борис', 0)); });
PHP,
            'hints' => [
                'Переменная в двойных кавычках подставляется по имени: `"Привет, $name!"`.',
                'Оператор склейки строк — точка.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function greeting(string $name, int $age): string
{
    return "Привет, $name! Через год тебе будет " . ($age + 1) . ".";
}

echo greeting('Аня', 30);
PHP,
        ],
        [
            'id' => 'is-filled',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Ноль — это тоже ответ',
            'text' => "Функция `isFilled` проверяет, заполнено ли поле формы. Сейчас она считает пустым поле, в которое ввели `0`, а поле из одних пробелов — заполненным.\n\nИсправь: поле заполнено, если после удаления пробелов по краям в нём остался хотя бы один символ. Убрать пробелы — функция `trim()`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function isFilled(string $value): bool
{
    if ($value) {
        return true;
    }

    return false;
}

var_dump(isFilled('0'));
var_dump(isFilled('   '));
PHP,
            'tests' => <<<'PHP'
test("'Аня' — заполнено", function () { assert_eq(true, isFilled('Аня')); });
test("пустая строка — не заполнено", function () { assert_eq(false, isFilled('')); });
test("'0' — заполнено", function () { assert_eq(true, isFilled('0')); });
test("одни пробелы — не заполнено", function () { assert_eq(false, isFilled('   ')); });
test("' 0 ' — заполнено", function () { assert_eq(true, isFilled(' 0 ')); });
PHP,
            'hints' => [
                '`if ($value)` приводит строку к логическому типу, а строка `\'0\'` ложна. Нужно явное сравнение.',
                'Результат сравнения уже имеет тип `bool`: `return trim($value) !== \'\';`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function isFilled(string $value): bool
{
    return trim($value) !== '';
}
PHP,
        ],
        [
            'id' => 'to-int',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Число из формы',
            'text' => "Напиши функцию `toInt(\$value)`. Она получает то, что пришло из формы, и возвращает целое число, а если значение числом не является — `null`.\n\n- `'42'` → `42`\n- `'3.9'` → `3` (дробная часть отбрасывается)\n- `7` → `7`\n- `'42 рубля'`, `''`, `'много'` → `null`\n\nПроверить, похоже ли значение на число, — `is_numeric()`.\n\nТип результата `?int` означает «целое или null».",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function toInt($value): ?int
{

}

var_dump(toInt('42'), toInt('много'));
PHP,
            'tests' => <<<'PHP'
test("'42' → 42", function () { assert_eq(42, toInt('42')); });
test("7 → 7", function () { assert_eq(7, toInt(7)); });
test("'3.9' → 3", function () { assert_eq(3, toInt('3.9')); });
test("'-5' → -5", function () { assert_eq(-5, toInt('-5')); });
test("'0' → 0, а не null", function () { assert_eq(0, toInt('0')); });
test("'42 рубля' → null", function () { assert_eq(null, toInt('42 рубля')); });
test("пустая строка → null", function () { assert_eq(null, toInt('')); });
test("'много' → null", function () { assert_eq(null, toInt('много')); });
PHP,
            'hints' => [
                'Ранний выход: `if (!is_numeric($value)) { return null; }`',
                'Приведение к целому: `(int) $value`. Строку с дробью `(int)` обрезает не всегда так, как ждёшь, поэтому надёжнее через `float`: `(int) (float) $value`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function toInt($value): ?int
{
    if (!is_numeric($value)) {
        return null;
    }

    return (int) (float) $value;
}
PHP,
        ],
        [
            'id' => 'format-price',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Цена для вывода',
            'text' => "Ту же задачу ты решал на JavaScript. Напиши функцию `formatPrice(\$value)` на PHP:\n\n- `1234.5` → `'1234.50 ₽'`\n- цена может прийти строкой: `'99'` → `'99.00 ₽'`\n- если значение не число — вернуть `'—'`\n\nДва знака после точки даёт `number_format(\$number, 2, '.', '')`: число, количество знаков, разделитель дробной части, разделитель тысяч.\n\nСначала запиши шаги комментариями.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
test("1234.5 → '1234.50 ₽'", function () { assert_eq('1234.50 ₽', formatPrice(1234.5)); });
test("10 → '10.00 ₽'", function () { assert_eq('10.00 ₽', formatPrice(10)); });
test("строка '99' → '99.00 ₽'", function () { assert_eq('99.00 ₽', formatPrice('99')); });
test("округление: 7.129 → '7.13 ₽'", function () { assert_eq('7.13 ₽', formatPrice(7.129)); });
test("не число → '—'", function () { assert_eq('—', formatPrice('бесплатно')); });
test("0 → '0.00 ₽'", function () { assert_eq('0.00 ₽', formatPrice(0)); });
test("пустая строка → '—'", function () { assert_eq('—', formatPrice('')); });
PHP,
            'hints' => [
                'Шаги: проверить `is_numeric` → если нет, вернуть прочерк → привести к `float` → собрать строку.',
                'Сборка: `number_format((float) $value, 2, \'.\', \'\') . \' ₽\'`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function formatPrice($value): string
{
    // 1. Не число — прочерк
    if (!is_numeric($value)) {
        return '—';
    }

    // 2. Два знака после точки и знак рубля
    return number_format((float) $value, 2, '.', '') . ' ₽';
}
PHP,
        ],
    ],
    'cards' => [
        ['Чем PHP отличается от JavaScript по времени жизни программы?', 'PHP выполняется на сервере на каждый запрос заново: получил запрос, отработал сверху вниз, отдал ответ и завершился. Между запросами ничего не сохраняется.'],
        ['Какой оператор склеивает строки в PHP?', 'Точка. Плюс только складывает числа.'],
        ['Чем отличаются одинарные и двойные кавычки?', 'В двойных подставляются переменные и работают `\n`, `\t`. В одинарных всё буквально.'],
        ['Какие значения ложны в PHP, но истинны в JavaScript?', 'Пустой массив `[]` и строка `\'0\'`.'],
        ['Что делает `declare(strict_types=1)`?', 'Запрещает молчаливое приведение аргументов к объявленному типу: строка `\'5\'` в параметр `int` даст `TypeError`.'],
        ['Как безопасно прочитать ключ массива, которого может не быть?', '`$user[\'city\'] ?? \'не указан\'` — оператор `??` подставит значение, если ключа нет или там `null`.'],
    ],
];
