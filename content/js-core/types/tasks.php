<?php
return [
    'tasks' => [
        [
            'id' => 'typeof-null',
            'type' => 'quiz',
            'title' => 'typeof',
            'text' => 'Что вернёт `typeof null`?',
            'options' => ["'null'", "'undefined'", "'object'", "'boolean'"],
            'answer' => 2,
            'explain' => 'Это ошибка первых версий языка, которую уже нельзя исправить, не сломав старые сайты. Проверять на `null` нужно сравнением: `value === null`.',
        ],
        [
            'id' => 'plus',
            'type' => 'quiz',
            'title' => 'Плюс и минус',
            'text' => "Чему равны выражения `'5' + 3` и `'5' - 3`?",
            'options' => ["8 и 2", "'53' и 2", "'53' и NaN", "8 и '2'"],
            'answer' => 1,
            'explain' => 'Плюс при наличии строки склеивает: `\'53\'`. Минус бывает только числовым, поэтому строка `\'5\'` превращается в число: `2`.',
        ],
        [
            'id' => 'greeting',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Шаблонная строка',
            'text' => "Допиши функцию `greeting(name, age)`, чтобы она возвращала строку вида `Привет, Аня! Через год тебе будет 31.`\n\nИспользуй обратные кавычки и `\${}`.",
            'starter' => <<<'JS'
function greeting(name, age) {
  return ___;
}

console.log(greeting('Аня', 30));
JS,
            'tests' => <<<'JS'
test("greeting('Аня', 30)", () => assertEqual(greeting('Аня', 30), 'Привет, Аня! Через год тебе будет 31.'));
test("greeting('Борис', 0)", () => assertEqual(greeting('Борис', 0), 'Привет, Борис! Через год тебе будет 1.'));
JS,
            'hints' => [
                'Каркас: `` `Привет, ${name}! ...` ``. Обрати внимание на знаки препинания и точку в конце.',
                'Внутри `${}` можно считать: `${age + 1}`.',
            ],
            'solution' => <<<'JS'
function greeting(name, age) {
  return `Привет, ${name}! Через год тебе будет ${age + 1}.`;
}
JS,
        ],
        [
            'id' => 'to-number',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Проверка на NaN',
            'text' => "Функция `toNumberOrZero(value)` превращает значение в число, а если не получилось — возвращает `0`. Сейчас для нечисловых строк она возвращает `NaN`. Найди, почему проверка не срабатывает.",
            'starter' => <<<'JS'
function toNumberOrZero(value) {
  const number = Number(value);

  if (number === NaN) {
    return 0;
  }

  return number;
}

console.log(toNumberOrZero('42'));
console.log(toNumberOrZero('сорок два'));
JS,
            'tests' => <<<'JS'
test("'42' → 42", () => assertEqual(toNumberOrZero('42'), 42));
test("'3.5' → 3.5", () => assertEqual(toNumberOrZero('3.5'), 3.5));
test("'сорок два' → 0", () => assertEqual(toNumberOrZero('сорок два'), 0));
test('undefined → 0', () => assertEqual(toNumberOrZero(undefined), 0));
test('число остаётся числом', () => assertEqual(toNumberOrZero(7), 7));
JS,
            'hints' => [
                '`NaN` не равен ничему, даже самому себе. Сравнение `number === NaN` всегда даёт `false`.',
                'Для проверки есть функция `Number.isNaN(number)`.',
            ],
            'solution' => <<<'JS'
function toNumberOrZero(value) {
  const number = Number(value);

  if (Number.isNaN(number)) {
    return 0;
  }

  return number;
}
JS,
        ],
        [
            'id' => 'describe-type',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Честный typeof',
            'text' => "Напиши функцию `describeType(value)`, которая исправляет две странности `typeof`:\n\n- для `null` возвращает `'null'`;\n- для массива возвращает `'array'`;\n- во всех остальных случаях возвращает то же, что `typeof`.",
            'starter' => <<<'JS'
function describeType(value) {

}

console.log(describeType(null), describeType([1, 2]), describeType('текст'));
JS,
            'tests' => <<<'JS'
test("null → 'null'", () => assertEqual(describeType(null), 'null'));
test("[1, 2] → 'array'", () => assertEqual(describeType([1, 2]), 'array'));
test("'текст' → 'string'", () => assertEqual(describeType('текст'), 'string'));
test("42 → 'number'", () => assertEqual(describeType(42), 'number'));
test("undefined → 'undefined'", () => assertEqual(describeType(undefined), 'undefined'));
test("{} → 'object'", () => assertEqual(describeType({}), 'object'));
test("true → 'boolean'", () => assertEqual(describeType(true), 'boolean'));
JS,
            'hints' => [
                'Это приём «ранний выход»: сначала два особых случая с `return`, в конце — общий.',
                'Проверка на null: `value === null`. Проверка на массив: `Array.isArray(value)`.',
            ],
            'solution' => <<<'JS'
function describeType(value) {
  if (value === null) {
    return 'null';
  }
  if (Array.isArray(value)) {
    return 'array';
  }
  return typeof value;
}
JS,
        ],
        [
            'id' => 'format-price',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Цена для вывода',
            'text' => "Напиши функцию `formatPrice(value)`, которая готовит цену к показу на странице: два знака после запятой и знак рубля через пробел.\n\n- `formatPrice(1234.5)` → `'1234.50 ₽'`\n- цена может прийти строкой: `formatPrice('99')` → `'99.00 ₽'`\n- если значение не превращается в число, вернуть `'—'`\n\nПригодится метод чисел `toFixed(2)` — он возвращает строку с двумя знаками после точки.\n\nСначала запиши шаги комментариями.",
            'starter' => "",
            'tests' => <<<'JS'
test("1234.5 → '1234.50 ₽'", () => assertEqual(formatPrice(1234.5), '1234.50 ₽'));
test("10 → '10.00 ₽'", () => assertEqual(formatPrice(10), '10.00 ₽'));
test("строка '99' → '99.00 ₽'", () => assertEqual(formatPrice('99'), '99.00 ₽'));
test("округление: 7.129 → '7.13 ₽'", () => assertEqual(formatPrice(7.129), '7.13 ₽'));
test("не число → '—'", () => assertEqual(formatPrice('бесплатно'), '—'));
test("0 → '0.00 ₽'", () => assertEqual(formatPrice(0), '0.00 ₽'));
JS,
            'hints' => [
                'Шаги: превратить значение в число → если получился NaN, вернуть прочерк → иначе собрать строку из `toFixed(2)` и знака рубля.',
                'Проверка: `Number.isNaN(number)`. Сборка: `` `${number.toFixed(2)} ₽` ``.',
            ],
            'solution' => <<<'JS'
function formatPrice(value) {
  // 1. Превратить в число
  const number = Number(value);

  // 2. Не получилось — прочерк
  if (Number.isNaN(number)) {
    return '—';
  }

  // 3. Два знака после запятой и знак рубля
  return `${number.toFixed(2)} ₽`;
}
JS,
        ],
    ],
    'cards' => [
        ['Когда писать `const`, а когда `let`?', '`const` по умолчанию. `let` — только если переменной понадобится присвоить новое значение.'],
        ['Назови шесть ложных значений JavaScript.', '`false`, `0`, `\'\'`, `null`, `undefined`, `NaN`. Всё остальное истинно, включая `\'0\'`, `[]` и `{}`.'],
        ['Почему `==` не используют?', 'Он молча приводит типы перед сравнением (`0 == \'\'` истинно). `===` сравнивает без преобразований и делает ровно то, что написано.'],
        ['Как проверить, что значение — `NaN`? А что оно массив?', '`Number.isNaN(value)` — потому что `NaN` не равен даже себе. `Array.isArray(value)` — потому что `typeof []` даёт `\'object\'`.'],
        ['Что даст `Number(\'\')`?', '`0`, а не `NaN`. Пустая строка из формы превратится в ноль — это стоит проверять отдельно.'],
    ],
];
