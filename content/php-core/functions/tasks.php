<?php
return [
    'tasks' => [
        [
            'id' => 'scope-quiz',
            'type' => 'quiz',
            'title' => 'Что видит функция',
            'text' => "```php\n\$discount = 10;\n\nfunction finalPrice(int \$price): int\n{\n    return \$price - \$discount;\n}\n\necho finalPrice(100);\n```\n\nЧто произойдёт?",
            'options' => [
                'Выведется 90',
                'Предупреждение «Undefined variable $discount», выведется 100',
                'Выведется 10',
                'Синтаксическая ошибка',
            ],
            'answer' => 1,
            'explain' => 'Функция в PHP не видит переменные, объявленные снаружи. Внутри `$discount` не существует: PHP выдаст предупреждение и подставит `null`, который в арифметике превратится в ноль. В JavaScript тот же код вернул бы 90. Значение нужно передать параметром.',
        ],
        [
            'id' => 'with-tax',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'title' => 'Типы и значение по умолчанию',
            'text' => "Допиши объявление функции `withTax`:\n\n- первый параметр — цена, дробное число;\n- второй — ставка налога, дробное число, по умолчанию `0.2`;\n- результат — дробное число.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function withTax(___ $price, ___ $rate ___): ___
{
    return $price * (1 + $rate);
}

var_dump(withTax(100.0));
PHP,
            'tests' => <<<'PHP'
test('ставка по умолчанию — 20%', function () { assert_eq(120.0, withTax(100.0)); });
test('своя ставка', function () { assert_eq(110.0, round(withTax(100.0, 0.1), 2)); });
test('результат — float', function () { assert_eq('double', gettype(withTax(50.0, 0.0))); });
test('строка вместо числа — TypeError', function () {
    assert_throws(function () { withTax('100'); }, 'TypeError', 'Параметр $price должен иметь тип float');
});
PHP,
            'hints' => [
                'Тип дробного числа — `float`. Он пишется перед именем параметра.',
                'Значение по умолчанию: `float $rate = 0.2`. Тип результата — после двоеточия.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function withTax(float $price, float $rate = 0.2): float
{
    return $price * (1 + $rate);
}
PHP,
        ],
        [
            'id' => 'global-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Функция из легаси',
            'text' => "Функция `expensive` в стиле старого кода берёт порог цены из глобальной переменной. Из-за этого её нельзя вызвать с другим порогом, а результат зависит от того, что лежит в `\$limit` в момент вызова.\n\nПеределай:\n\n- порог передаётся вторым параметром `int \$limit`;\n- `global` из функции убран;\n- анонимная функция внутри получает порог через `use` — или замени её стрелочной.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

$limit = 500;

function expensive(array $prices): array
{
    global $limit;

    return array_values(array_filter($prices, function ($price) {
        return $price > $limit;
    }));
}

print_r(expensive([300, 700, 900]));
PHP,
            'tests' => <<<'PHP'
test('порог 500', function () { assert_eq([700, 900], expensive([300, 700, 900], 500)); });
test('порог 800', function () { assert_eq([900], expensive([300, 700, 900], 800)); });
test('порог 0 — проходят все', function () { assert_eq([300, 700, 900], expensive([300, 700, 900], 0)); });
test('ровно порог — не проходит', function () { assert_eq([], expensive([500], 500)); });
test('результат не зависит от глобальной переменной', function () {
    $GLOBALS['limit'] = 0;
    assert_eq([900], expensive([300, 700, 900], 800));
});
PHP,
            'hints' => [
                'В исходном коде целых две проблемы с видимостью: `global` в функции и анонимная функция, которая `$limit` не видит вообще — в ней он не объявлен.',
                'Объявление: `function expensive(array $prices, int $limit): array`. Строку `global $limit;` убрать.',
                'Замыкание: `function ($price) use ($limit) { … }` или короче `fn($price) => $price > $limit`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function expensive(array $prices, int $limit): array
{
    return array_values(array_filter($prices, fn($price) => $price > $limit));
}

print_r(expensive([300, 700, 900], 500));
PHP,
        ],
        [
            'id' => 'clamp',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Ограничить значение',
            'text' => "Напиши функцию `clamp(int \$value, int \$min, int \$max): int`. Она удерживает число в границах: меньше `\$min` — возвращает `\$min`, больше `\$max` — возвращает `\$max`, иначе само число.\n\nЕсли `\$min` больше `\$max`, функция бросает `InvalidArgumentException`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function clamp(int $value, int $min, int $max): int
{

}

echo clamp(150, 0, 100);
PHP,
            'tests' => <<<'PHP'
test('clamp(150, 0, 100) → 100', function () { assert_eq(100, clamp(150, 0, 100)); });
test('clamp(-5, 0, 100) → 0', function () { assert_eq(0, clamp(-5, 0, 100)); });
test('clamp(42, 0, 100) → 42', function () { assert_eq(42, clamp(42, 0, 100)); });
test('значение на границе', function () { assert_eq([0, 100], [clamp(0, 0, 100), clamp(100, 0, 100)]); });
test('min больше max — исключение', function () {
    assert_throws(function () { clamp(5, 10, 1); }, 'InvalidArgumentException');
});
test('min равен max — допустимо', function () { assert_eq(7, clamp(100, 7, 7)); });
PHP,
            'hints' => [
                'Сначала проверка аргументов: `if ($min > $max) { throw new InvalidArgumentException(\'min больше max\'); }`',
                'Дальше два ранних выхода и общий случай — как в версии на JavaScript.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function clamp(int $value, int $min, int $max): int
{
    if ($min > $max) {
        throw new InvalidArgumentException('min больше max');
    }
    if ($value < $min) {
        return $min;
    }
    if ($value > $max) {
        return $max;
    }

    return $value;
}

// Короче: return max($min, min($max, $value));
PHP,
        ],
        [
            'id' => 'make-discount',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Функция, которая возвращает функцию',
            'text' => "Напиши функцию `makeDiscount(int \$percent): callable`. Она возвращает функцию, которая принимает цену и возвращает цену со скидкой.\n\n```php\n\$sale = makeDiscount(20);\necho \$sale(1000);   // 800\n```\n\nЭто замыкание — такое же, как в JavaScript.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function makeDiscount(int $percent): callable
{

}

$sale = makeDiscount(20);
var_dump($sale(1000));
PHP,
            'tests' => <<<'PHP'
test('скидка 20% с 1000 → 800', function () { $sale = makeDiscount(20); assert_eq(800.0, (float) $sale(1000)); });
test('скидка 0% — цена не меняется', function () { $none = makeDiscount(0); assert_eq(500.0, (float) $none(500)); });
test('скидка 100% → 0', function () { $free = makeDiscount(100); assert_eq(0.0, (float) $free(500)); });
test('две скидки независимы', function () {
    $small = makeDiscount(10);
    $big = makeDiscount(50);
    assert_eq([180.0, 100.0], [(float) $small(200), (float) $big(200)]);
});
test('работает как колбэк в array_map', function () {
    assert_eq([90.0, 180.0], array_map('floatval', array_map(makeDiscount(10), [100, 200])));
});
PHP,
            'hints' => [
                'Стрелочная функция сама видит `$percent`: `return fn($price) => $price - $price * $percent / 100;`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function makeDiscount(int $percent): callable
{
    return fn($price) => $price - $price * $percent / 100;
}
PHP,
        ],
        [
            'id' => 'password-plan',
            'type' => 'plan',
            'title' => 'Надёжность пароля: план',
            'text' => "Задача из блока JavaScript, теперь на PHP. Три правила:\n\n- длина не меньше 8 символов;\n- есть хотя бы одна цифра;\n- есть хотя бы одна заглавная буква.\n\nВыполнены все три — `'сильный'`, два — `'средний'`, иначе — `'слабый'`.\n\nЗапиши шаги. Отдельно подумай: какая проверка в PHP потребует внимания из-за русских букв?",
            'reference' => <<<'MD'
Главная функция `passwordStrength`:

1. Завести счётчик выполненных правил.
2. Если длина пароля **в символах** не меньше 8 — увеличить счётчик.
3. Если есть цифра — увеличить.
4. Если есть заглавная буква — увеличить.
5. Вернуть оценку по значению счётчика.

Вспомогательные `hasDigit` и `hasUpperCase` — по одной проверке каждая.

**Где нужно внимание.** Обычные строковые функции PHP считают байты, а русская буква занимает два. Поэтому:

- длина — `mb_strlen`, а не `strlen`: иначе пароль из четырёх русских букв окажется «длиной 8»;
- заглавная буква — сравнение строки с её вариантом в нижнем регистре через `mb_strtolower`: обычная `strtolower` русские буквы не трогает.

Цифры — латинские символы, для них годится и регулярное выражение `/\d/`, и обычный перебор.

Сама структура решения та же, что на JavaScript. Отличаются только имена функций — и то, что в PHP приходится помнить о кодировке.
MD,
        ],
        [
            'id' => 'password',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Надёжность пароля: код',
            'text' => "Напиши три функции: `hasDigit(string \$password): bool`, `hasUpperCase(string \$password): bool` и `passwordStrength(string \$password): string`.\n\nПодсказки по проверкам:\n\n- есть цифра: `preg_match('/\\d/', \$password) === 1`;\n- есть заглавная: строка отличается от себя же в нижнем регистре — `\$password !== mb_strtolower(\$password)`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
test("hasDigit('abc1') → true", function () { assert_eq(true, hasDigit('abc1')); });
test("hasDigit('abc') → false", function () { assert_eq(false, hasDigit('abc')); });
test("hasUpperCase('abC') → true", function () { assert_eq(true, hasUpperCase('abC')); });
test("hasUpperCase('пароль') → false", function () { assert_eq(false, hasUpperCase('пароль')); });
test("hasUpperCase('Пароль') → true (кириллица)", function () { assert_eq(true, hasUpperCase('Пароль')); });
test("hasUpperCase('123!') → false", function () { assert_eq(false, hasUpperCase('123!')); });
test("'Secret123' → сильный", function () { assert_eq('сильный', passwordStrength('Secret123')); });
test("'secret123' → средний", function () { assert_eq('средний', passwordStrength('secret123')); });
test("'secret' → слабый", function () { assert_eq('слабый', passwordStrength('secret')); });
test("'пароль' — 6 символов, а не 12: слабый", function () { assert_eq('слабый', passwordStrength('пароль')); });
test("'Пароль1' — 7 символов: средний", function () { assert_eq('средний', passwordStrength('Пароль1')); });
test("'Парольчик1' → сильный", function () { assert_eq('сильный', passwordStrength('Парольчик1')); });
test('пустая строка → слабый', function () { assert_eq('слабый', passwordStrength('')); });
PHP,
            'hints' => [
                '`hasDigit` — одна строка: `return preg_match(\'/\d/\', $password) === 1;`',
                'В главной функции: `$score = 0;`, три `if`, каждый делает `$score++`. Длина — `mb_strlen($password) >= 8`.',
                'Оценка: `if ($score === 3) return \'сильный\'; if ($score === 2) return \'средний\'; return \'слабый\';`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function hasDigit(string $password): bool
{
    return preg_match('/\d/', $password) === 1;
}

function hasUpperCase(string $password): bool
{
    return $password !== mb_strtolower($password);
}

function passwordStrength(string $password): string
{
    $score = 0;

    if (mb_strlen($password) >= 8) {
        $score++;
    }
    if (hasDigit($password)) {
        $score++;
    }
    if (hasUpperCase($password)) {
        $score++;
    }

    if ($score === 3) {
        return 'сильный';
    }
    if ($score === 2) {
        return 'средний';
    }

    return 'слабый';
}
PHP,
        ],
    ],
    'cards' => [
        ['Видит ли функция PHP переменные, объявленные снаружи?', 'Нет. Внутри доступны только параметры и собственные переменные. Всё нужное передаётся аргументами.'],
        ['Как анонимной функции получить внешнюю переменную?', 'Перечислить её в `use`: `function ($x) use ($limit) { … }`. Стрелочная функция `fn` видит внешние переменные сама.'],
        ['Что означают типы `?string` и `void`?', '`?string` — строка или `null`. `void` — функция ничего не возвращает.'],
        ['Что такое `global $x` и как к нему относиться?', 'Подключение глобальной переменной внутрь функции. Встречается в легаси; в новом коде значение передают параметром.'],
        ['Что делает амперсанд перед параметром: `array &$items`?', 'Передаёт переменную по ссылке: функция меняет массив вызывающего кода, а не копию. Так работают `sort` и `shuffle`.'],
    ],
];
