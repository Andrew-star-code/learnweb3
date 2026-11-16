<?php
return [
    'tasks' => [
        [
            'id' => 'no-return',
            'type' => 'quiz',
            'title' => 'Вывести или вернуть',
            'text' => "```js\nfunction add(a, b) {\n  console.log(a + b);\n}\n\nconst result = add(2, 3);\n```\n\nЧто окажется в переменной `result`?",
            'options' => ['5', "'5'", 'undefined', 'Будет ошибка'],
            'answer' => 2,
            'explain' => 'Функция вывела `5` в консоль, но ничего не вернула. Функция без `return` возвращает `undefined`. Чтобы результатом можно было пользоваться, нужен `return a + b;`.',
        ],
        [
            'id' => 'arrows',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Стрелки и значения по умолчанию',
            'text' => "Допиши три функции:\n\n- `double(x)` — возвращает число, умноженное на два;\n- `isEven(n)` — возвращает `true` для чётного числа;\n- `greet(name)` — возвращает `'Привет, Аня!'`, а если имя не передано — `'Привет, гость!'`.",
            'starter' => <<<'JS'
const double = (x) => ___;

const isEven = (n) => ___;

function greet(name ___) {
  return `Привет, ${name}!`;
}

console.log(double(4), isEven(4), greet());
JS,
            'tests' => <<<'JS'
test('double(4) → 8', () => assertEqual(double(4), 8));
test('isEven(4) → true', () => assertEqual(isEven(4), true));
test('isEven(7) → false', () => assertEqual(isEven(7), false));
test("greet('Аня')", () => assertEqual(greet('Аня'), 'Привет, Аня!'));
test('greet() без аргумента', () => assertEqual(greet(), 'Привет, гость!'));
JS,
            'hints' => [
                'Стрелочная функция без фигурных скобок сама возвращает результат выражения: `(x) => x * 2`.',
                'Сравнение уже даёт `true` или `false`: `(n) => n % 2 === 0`.',
                "Значение по умолчанию пишется прямо в списке параметров: `name = 'гость'`.",
            ],
            'solution' => <<<'JS'
const double = (x) => x * 2;

const isEven = (n) => n % 2 === 0;

function greet(name = 'гость') {
  return `Привет, ${name}!`;
}
JS,
        ],
        [
            'id' => 'missing-return',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Потерянный return',
            'text' => "Обе функции возвращают `undefined` — в каждой потерян `return`, но по-разному.\n\n- `square(x)` возвращает квадрат числа.\n- `discount(total)` возвращает размер скидки: 10% от суммы, если сумма от 5000, иначе `0`.",
            'starter' => <<<'JS'
const square = (x) => {
  x * x;
};

function discount(total) {
  if (total >= 5000) {
    total * 0.1;
  } else {
    return 0;
  }
}

console.log(square(5), discount(6000));
JS,
            'tests' => <<<'JS'
test('square(5) → 25', () => assertEqual(square(5), 25));
test('square(-3) → 9', () => assertEqual(square(-3), 9));
test('discount(6000) → 600', () => assertEqual(discount(6000), 600));
test('discount(5000) → 500 (граница)', () => assertEqual(discount(5000), 500));
test('discount(4999) → 0', () => assertEqual(discount(4999), 0));
JS,
            'hints' => [
                'У стрелочной функции с фигурными скобками автоматического возврата нет. Либо добавь `return`, либо убери скобки.',
                'В `discount` одна ветка возвращает значение, а вторая только вычисляет его и выбрасывает.',
            ],
            'solution' => <<<'JS'
const square = (x) => x * x;

function discount(total) {
  if (total >= 5000) {
    return total * 0.1;
  }
  return 0;
}
JS,
        ],
        [
            'id' => 'clamp',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Ограничить значение',
            'text' => "Напиши функцию `clamp(value, min, max)`, которая удерживает число в заданных границах:\n\n- если `value` меньше `min` — возвращает `min`;\n- если больше `max` — возвращает `max`;\n- иначе возвращает само `value`.\n\nТакая функция нужна постоянно: громкость от 0 до 100, номер страницы от 1 до последней, количество товара не больше остатка на складе.",
            'starter' => <<<'JS'
function clamp(value, min, max) {

}

console.log(clamp(150, 0, 100));
JS,
            'tests' => <<<'JS'
test('clamp(150, 0, 100) → 100', () => assertEqual(clamp(150, 0, 100), 100));
test('clamp(-5, 0, 100) → 0', () => assertEqual(clamp(-5, 0, 100), 0));
test('clamp(42, 0, 100) → 42', () => assertEqual(clamp(42, 0, 100), 42));
test('значение на границе', () => { assertEqual(clamp(0, 0, 100), 0); assertEqual(clamp(100, 0, 100), 100); });
test('отрицательные границы', () => assertEqual(clamp(-50, -10, -1), -10));
JS,
            'hints' => [
                'Два ранних выхода и общий случай в конце — как в `describeType` из первого урока блока.',
            ],
            'solution' => <<<'JS'
function clamp(value, min, max) {
  if (value < min) {
    return min;
  }
  if (value > max) {
    return max;
  }
  return value;
}
JS,
        ],
        [
            'id' => 'apply-to-all',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Функция как аргумент',
            'text' => "Напиши функцию `applyToAll(items, fn)`, которая возвращает новый массив: каждый элемент исходного массива пропущен через функцию `fn`.\n\n```js\napplyToAll([1, 2, 3], (x) => x * 10)   // [10, 20, 30]\n```\n\nЭто приём «преобразование», только само преобразование приходит снаружи. Именно так устроен внутри метод массивов `map`.",
            'starter' => <<<'JS'
function applyToAll(items, fn) {

}

console.log(applyToAll([1, 2, 3], (x) => x * 10));
JS,
            'tests' => <<<'JS'
test('умножение на 10', () => assertEqual(applyToAll([1, 2, 3], (x) => x * 10), [10, 20, 30]));
test('строки в верхний регистр', () => assertEqual(applyToAll(['а', 'б'], (s) => s.toUpperCase()), ['А', 'Б']));
test('пустой массив', () => assertEqual(applyToAll([], (x) => x), []));
test('исходный массив не меняется', () => {
  const source = [1, 2];
  applyToAll(source, (x) => x + 1);
  assertEqual(source, [1, 2]);
});
test('fn вызывается по одному разу на элемент', () => {
  let calls = 0;
  applyToAll([5, 6, 7], (x) => { calls++; return x; });
  assertEqual(calls, 3);
});
JS,
            'hints' => [
                'Каркас преобразования: пустой массив `result`, цикл по `items`, `result.push(...)`, `return result`.',
                'Переданную функцию вызывают как обычную: `fn(item)`. То, что она вернула, и кладём в результат.',
            ],
            'solution' => <<<'JS'
function applyToAll(items, fn) {
  const result = [];

  for (const item of items) {
    result.push(fn(item));
  }

  return result;
}
JS,
        ],
        [
            'id' => 'password-plan',
            'type' => 'plan',
            'title' => 'Надёжность пароля: план',
            'text' => "Задача: оценить надёжность пароля. Есть три правила:\n\n- длина не меньше 8 символов;\n- есть хотя бы одна цифра;\n- есть хотя бы одна заглавная буква.\n\nВыполнены все три — пароль `'сильный'`, два — `'средний'`, одно или ни одного — `'слабый'`.\n\nЗапиши шаги. Подумай, какие из них стоит оформить отдельными функциями.",
            'reference' => <<<'MD'
Главная функция:

1. Завести счётчик выполненных правил, равный нулю.
2. Если длина пароля не меньше 8 — увеличить счётчик.
3. Если в пароле есть цифра — увеличить счётчик.
4. Если в пароле есть заглавная буква — увеличить счётчик.
5. Если счётчик равен 3 — вернуть «сильный», если 2 — «средний», иначе — «слабый».

Шаги 3 и 4 не записываются одной строкой, поэтому это отдельные функции — `hasDigit(password)` и `hasUpperCase(password)`. Каждая устроена как поиск:

1. Пройти по символам пароля.
2. Если символ подходит — сразу вернуть `true`.
3. Если цикл закончился — вернуть `false`.

Так большая задача превращается в три маленькие функции, каждую из которых можно написать и проверить отдельно. Это и есть главная польза функций.
MD,
        ],
        [
            'id' => 'password',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Надёжность пароля: код',
            'text' => "Напиши функцию `passwordStrength(password)` и вспомогательные функции `hasDigit(password)` и `hasUpperCase(password)`. Тесты проверяют все три.\n\nПодсказки по символам:\n\n- символ — цифра, если `ch >= '0' && ch <= '9'`;\n- буква заглавная, если она меняется при переводе в нижний регистр: `ch !== ch.toLowerCase()`. Этот способ работает и для кириллицы, и для латиницы.\n\nПиши и проверяй по одной функции: сначала `hasDigit`, запусти, потом следующую.",
            'starter' => "",
            'tests' => <<<'JS'
test("hasDigit('abc1') → true", () => assertEqual(hasDigit('abc1'), true));
test("hasDigit('abc') → false", () => assertEqual(hasDigit('abc'), false));
test("hasUpperCase('abC') → true", () => assertEqual(hasUpperCase('abC'), true));
test("hasUpperCase('пароль') → false", () => assertEqual(hasUpperCase('пароль'), false));
test("hasUpperCase('Пароль') → true (кириллица)", () => assertEqual(hasUpperCase('Пароль'), true));
test("hasUpperCase('123!') → false", () => assertEqual(hasUpperCase('123!'), false));
test("'Secret123' → сильный", () => assertEqual(passwordStrength('Secret123'), 'сильный'));
test("'secret123' → средний", () => assertEqual(passwordStrength('secret123'), 'средний'));
test("'Sec1' → средний", () => assertEqual(passwordStrength('Sec1'), 'средний'));
test("'secret' → слабый", () => assertEqual(passwordStrength('secret'), 'слабый'));
test("'12345678' → средний", () => assertEqual(passwordStrength('12345678'), 'средний'));
test("пустая строка → слабый", () => assertEqual(passwordStrength(''), 'слабый'));
JS,
            'hints' => [
                '`hasDigit`: цикл `for (const ch of password)`, внутри — если символ цифра, `return true`. После цикла — `return false`.',
                '`hasUpperCase` устроена точно так же, отличается только условие.',
                'В главной функции: `let score = 0;`, затем три `if`, каждый из которых делает `score++`. Вспомогательные функции вызываются прямо в условии: `if (hasDigit(password))`.',
            ],
            'solution' => <<<'JS'
function hasDigit(password) {
  for (const ch of password) {
    if (ch >= '0' && ch <= '9') {
      return true;
    }
  }
  return false;
}

function hasUpperCase(password) {
  for (const ch of password) {
    if (ch !== ch.toLowerCase()) {
      return true;
    }
  }
  return false;
}

function passwordStrength(password) {
  let score = 0;

  if (password.length >= 8) score++;
  if (hasDigit(password)) score++;
  if (hasUpperCase(password)) score++;

  if (score === 3) return 'сильный';
  if (score === 2) return 'средний';
  return 'слабый';
}
JS,
        ],
    ],
    'cards' => [
        ['Чем `return` отличается от `console.log`?', '`console.log` показывает значение человеку. `return` отдаёт его вызвавшему коду — только так результат можно сохранить, сложить или проверить.'],
        ['Что вернёт `(x) => { x * 2 }` и почему?', '`undefined`. С фигурными скобками стрелочная функция ничего не возвращает сама — нужен `return`. Без скобок: `(x) => x * 2`.'],
        ['В чём разница между `action` и `action()`?', '`action` — сама функция, её можно передать как значение. `action()` — вызов, на этом месте окажется результат.'],
        ['Что такое чистая функция?', 'Функция, результат которой зависит только от аргументов и которая ничего не меняет снаружи. Её легко проверять и переиспользовать.'],
        ['Как понять, что шаг плана пора выносить в отдельную функцию?', 'Шаг не записывается одной строкой, или повторяется, или ему легко дать имя-глагол (`hasDigit`, `formatPrice`).'],
    ],
];
