<?php
return [
    'tasks' => [
        [
            'id' => 'finally-quiz',
            'type' => 'quiz',
            'title' => 'Порядок выполнения',
            'text' => "```js\ntry {\n  console.log('A');\n  JSON.parse('не json');\n  console.log('B');\n} catch (error) {\n  console.log('C');\n} finally {\n  console.log('D');\n}\n```\n\nЧто будет выведено?",
            'options' => ['A B C D', 'A C D', 'A C', 'A B D'],
            'answer' => 1,
            'explain' => '`JSON.parse` бросает ошибку, и выполнение сразу переходит в `catch` — строка с `B` пропускается. `finally` выполняется всегда.',
        ],
        [
            'id' => 'safe-parse',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Разбор с подстраховкой',
            'text' => "Допиши функцию `safeParse(text, fallback)`: она разбирает JSON и возвращает результат, а если текст — не JSON, возвращает `fallback`.\n\nЭто тот случай, когда перехват уместен: мы точно знаем, что делать при ошибке.",
            'starter' => <<<'JS'
function safeParse(text, fallback) {
  try {
    return ___;
  } catch (error) {
    return ___;
  }
}

console.log(safeParse('{"a": 1}', {}));
console.log(safeParse('сломано', {}));
JS,
            'tests' => <<<'JS'
test('верный JSON', () => assertEqual(safeParse('{"a": 1}', {}), { a: 1 }));
test('массив', () => assertEqual(safeParse('[1, 2]', []), [1, 2]));
test('неверный JSON → fallback', () => assertEqual(safeParse('сломано', { empty: true }), { empty: true }));
test('пустая строка → fallback', () => assertEqual(safeParse('', null), null));
JS,
            'hints' => [
                'В `try` — `JSON.parse(text)`, в `catch` — `fallback`.',
            ],
            'solution' => <<<'JS'
function safeParse(text, fallback) {
  try {
    return JSON.parse(text);
  } catch (error) {
    return fallback;
  }
}
JS,
        ],
        [
            'id' => 'swallow',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Проглоченная ошибка',
            'text' => "Функция `pricePerItem(total, count)` делит сумму на количество. Автор «на всякий случай» обернул всё в `try/catch` и при нуле возвращает строку. В результате вызывающий код получает то число, то строку и ничего не знает о проблеме.\n\nПеределай: при `count`, равном нулю, функция должна бросать `Error` с текстом `Количество не может быть нулём`. Лишний перехват убери.",
            'starter' => <<<'JS'
function pricePerItem(total, count) {
  try {
    if (count === 0) {
      return 'ошибка';
    }
    return total / count;
  } catch (error) {
    return 0;
  }
}

console.log(pricePerItem(300, 3));
console.log(pricePerItem(300, 0));
JS,
            'tests' => <<<'JS'
test('300 / 3 → 100', () => assertEqual(pricePerItem(300, 3), 100));
test('0 / 5 → 0', () => assertEqual(pricePerItem(0, 5), 0));
test('count = 0 → бросает ошибку', () => assertThrows(() => pricePerItem(300, 0), 'Функция должна бросить ошибку, а не вернуть значение'));
test('у ошибки понятный текст', () => {
  let message = null;
  try { pricePerItem(300, 0); } catch (error) { message = error.message; }
  assertEqual(message, 'Количество не может быть нулём');
});
test('это объект Error, а не строка', () => {
  let caught = null;
  try { pricePerItem(1, 0); } catch (error) { caught = error; }
  assert(caught instanceof Error, 'Бросать нужно new Error(...), а не строку');
});
JS,
            'hints' => [
                'Бросить ошибку: `throw new Error(\'Количество не может быть нулём\');`',
                '`try/catch` здесь не нужен совсем: в функции нет ничего, что могло бы упасть неожиданно.',
            ],
            'solution' => <<<'JS'
function pricePerItem(total, count) {
  if (count === 0) {
    throw new Error('Количество не может быть нулём');
  }
  return total / count;
}
JS,
        ],
        [
            'id' => 'validation-error',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Свой тип ошибки',
            'text' => "Напиши:\n\n1. Класс `ValidationError`, расширяющий `Error`. Конструктор принимает `message` и `field`, сохраняет `field` в объекте, а в `name` записывает `'ValidationError'`.\n2. Функцию `validateAge(value)`. Она возвращает `value`, если это целое число от 0 до 150. Иначе бросает `ValidationError` с полем `'age'` и текстом:\n   - `Возраст должен быть целым числом` — если это не целое число;\n   - `Возраст вне допустимого диапазона` — если число меньше 0 или больше 150.\n\nПроверка на целое число: `Number.isInteger(value)`.",
            'starter' => <<<'JS'
class ValidationError extends Error {

}

function validateAge(value) {

}

console.log(validateAge(30));
JS,
            'tests' => <<<'JS'
function catchError(fn) { try { fn(); } catch (error) { return error; } return null; }
test('верный возраст возвращается', () => assertEqual([validateAge(30), validateAge(0), validateAge(150)], [30, 0, 150]));
test('ValidationError — это Error с name и field', () => {
  const e = new ValidationError('текст', 'email');
  assert(e instanceof Error, 'Нет наследования от Error');
  assertEqual([e.message, e.field, e.name], ['текст', 'email', 'ValidationError']);
});
test('строка → ошибка про целое число', () => {
  const e = catchError(() => validateAge('30'));
  assert(e instanceof ValidationError, 'Ожидалась ValidationError');
  assertEqual([e.message, e.field], ['Возраст должен быть целым числом', 'age']);
});
test('дробное число → ошибка про целое число', () => assertEqual(catchError(() => validateAge(30.5))?.message, 'Возраст должен быть целым числом'));
test('NaN → ошибка про целое число', () => assertEqual(catchError(() => validateAge(NaN))?.message, 'Возраст должен быть целым числом'));
test('отрицательное → ошибка про диапазон', () => assertEqual(catchError(() => validateAge(-1))?.message, 'Возраст вне допустимого диапазона'));
test('151 → ошибка про диапазон', () => assertEqual(catchError(() => validateAge(151))?.message, 'Возраст вне допустимого диапазона'));
JS,
            'hints' => [
                'Конструктор: `constructor(message, field) { super(message); this.name = \'ValidationError\'; this.field = field; }`',
                'В `validateAge` две проверки подряд, каждая со своим `throw new ValidationError(текст, \'age\')`, в конце `return value`.',
                '`Number.isInteger` сам отсекает строки, `NaN` и дробные числа.',
            ],
            'solution' => <<<'JS'
class ValidationError extends Error {
  constructor(message, field) {
    super(message);
    this.name = 'ValidationError';
    this.field = field;
  }
}

function validateAge(value) {
  if (!Number.isInteger(value)) {
    throw new ValidationError('Возраст должен быть целым числом', 'age');
  }
  if (value < 0 || value > 150) {
    throw new ValidationError('Возраст вне допустимого диапазона', 'age');
  }
  return value;
}
JS,
        ],
        [
            'id' => 'try-all',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Не останавливаться на первой ошибке',
            'text' => "Напиши функцию `runAll(tasks)`. Она получает массив функций, вызывает каждую и возвращает массив результатов той же длины:\n\n- если функция отработала — `{ ok: true, value: то, что она вернула }`;\n- если бросила ошибку — `{ ok: false, error: текст ошибки }`.\n\nОшибка в одной функции не должна мешать выполнению остальных. Так обрабатывают, например, импорт файла: показать все плохие строки сразу, а не по одной.",
            'starter' => <<<'JS'
function runAll(tasks) {

}

console.log(runAll([
  () => 1 + 1,
  () => { throw new Error('сломалось'); },
  () => 'готово',
]));
JS,
            'tests' => <<<'JS'
test('все успешны', () => assertEqual(runAll([() => 1, () => 'a']), [{ ok: true, value: 1 }, { ok: true, value: 'a' }]));
test('ошибка в середине не мешает остальным', () => assertEqual(
  runAll([() => 1, () => { throw new Error('сломалось'); }, () => 3]),
  [{ ok: true, value: 1 }, { ok: false, error: 'сломалось' }, { ok: true, value: 3 }],
));
test('пустой массив', () => assertEqual(runAll([]), []));
test('каждая функция вызывается один раз', () => {
  let calls = 0;
  runAll([() => calls++, () => calls++]);
  assertEqual(calls, 2);
});
JS,
            'hints' => [
                'Это преобразование: цикл по `tasks`, на каждом шаге кладём в результат один объект.',
                '`try/catch` должен стоять **внутри** цикла. Если обернуть цикл целиком, первая же ошибка его прервёт.',
                'В `try`: `result.push({ ok: true, value: task() });` В `catch`: `result.push({ ok: false, error: error.message });`',
            ],
            'solution' => <<<'JS'
function runAll(tasks) {
  const result = [];

  for (const task of tasks) {
    try {
      result.push({ ok: true, value: task() });
    } catch (error) {
      result.push({ ok: false, error: error.message });
    }
  }

  return result;
}
JS,
        ],
        [
            'id' => 'config-plan',
            'type' => 'plan',
            'title' => 'Файл настроек: план',
            'text' => "Задача: разобрать текст файла настроек в объект.\n\n```text\n# подключение к базе\nhost=localhost\nport=5432\n\nname=shop\n```\n\nРезультат: `{ host: 'localhost', port: '5432', name: 'shop' }`.\n\nПравила: пустые строки и строки, начинающиеся с `#`, пропускаются. Если в значимой строке нет знака `=`, нужно бросить ошибку с номером этой строки: `Строка 3: нет знака =`.\n\nЗапиши шаги. Откуда возьмётся номер строки?",
            'reference' => <<<'MD'
1. Завести пустой объект для результата.
2. Разбить текст на строки по символу перевода строки.
3. Пройти по строкам, зная номер каждой.
4. Убрать пробелы по краям строки.
5. Если строка пустая или начинается с `#` — перейти к следующей.
6. Если в строке нет `=` — бросить ошибку с номером строки.
7. Разделить строку на ключ и значение по первому знаку `=`.
8. Записать значение в объект под ключом.
9. Вернуть объект.

Номер строки — это индекс в массиве плюс один. Поэтому здесь нужен цикл, в котором виден индекс: `for` со счётчиком или `forEach((line, index) => ...)`. С `for...of` индекса под рукой не будет.

Шаг 5 — приём «пропустить неподходящее» (`continue`). Он идёт до проверки на `=`, иначе комментарии и пустые строки будут считаться ошибками.
MD,
        ],
        [
            'id' => 'config',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Файл настроек: код',
            'text' => "Напиши функцию `parseConfig(text)` по своему плану.\n\nСтроки в тексте разделены символом `\\n`. Пробелы вокруг ключа и значения убираются. В значении может встретиться свой знак `=` — делить нужно только по первому.",
            'starter' => "",
            'tests' => <<<'JS'
test('простые настройки', () => assertEqual(parseConfig('host=localhost\nport=5432'), { host: 'localhost', port: '5432' }));
test('комментарии и пустые строки пропускаются', () => assertEqual(parseConfig('# база\nhost=localhost\n\nname=shop\n'), { host: 'localhost', name: 'shop' }));
test('пробелы вокруг убираются', () => assertEqual(parseConfig('  host = localhost  '), { host: 'localhost' }));
test('знак = внутри значения', () => assertEqual(parseConfig('url=http://x.ru/?a=1'), { url: 'http://x.ru/?a=1' }));
test('пустой текст → {}', () => assertEqual(parseConfig(''), {}));
test('строка без = → ошибка с номером', () => {
  let message = null;
  try { parseConfig('host=localhost\n# комментарий\nсломано\nport=1'); } catch (error) { message = error.message; }
  assertEqual(message, 'Строка 3: нет знака =');
});
test('ошибка в первой строке', () => {
  let message = null;
  try { parseConfig('сломано'); } catch (error) { message = error.message; }
  assertEqual(message, 'Строка 1: нет знака =');
});
JS,
            'hints' => [
                'Строки: `const lines = text.split(\'\\n\');`, цикл `for (let i = 0; i < lines.length; i++)`, строка — `lines[i].trim()`.',
                'Пропуск: `if (line === \'\' || line.startsWith(\'#\')) continue;`',
                'Позиция первого `=`: `const pos = line.indexOf(\'=\');`. Если `pos === -1` — бросаем ошибку с текстом `` `Строка ${i + 1}: нет знака =` ``.',
                'Ключ — `line.slice(0, pos).trim()`, значение — `line.slice(pos + 1).trim()`.',
            ],
            'solution' => <<<'JS'
function parseConfig(text) {
  const result = {};
  const lines = text.split('\n');

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i].trim();

    if (line === '' || line.startsWith('#')) {
      continue;
    }

    const pos = line.indexOf('=');
    if (pos === -1) {
      throw new Error(`Строка ${i + 1}: нет знака =`);
    }

    const key = line.slice(0, pos).trim();
    const value = line.slice(pos + 1).trim();
    result[key] = value;
  }

  return result;
}
JS,
        ],
    ],
    'cards' => [
        ['Почему при недопустимых данных лучше бросить ошибку, чем вернуть `null` или строку?', 'Возвращённое значение вызывающий код может забыть проверить и продолжит работать с мусором. Ошибку проигнорировать нельзя.'],
        ['Где стоит перехватывать ошибку?', 'Там, где понятно, что с ней делать: показать сообщение, подставить запасное значение, повторить. Если непонятно — не перехватывать.'],
        ['Чем плох пустой `catch`?', 'Ошибка проглатывается: программа работает неправильно и молчит. Отлаживать такое очень трудно.'],
        ['Когда выполняется `finally`?', 'Всегда — и после успешного `try`, и после `catch`. Используется для уборки.'],
        ['Как отличить в `catch` ожидаемую ошибку от неожиданной?', 'Свой класс ошибки и проверка `error instanceof ValidationError`. Остальные ошибки пробросить дальше через `throw error`.'],
    ],
];
