<?php
return [
    'tasks' => [
        [
            'id' => 'immutable-quiz',
            'type' => 'quiz',
            'title' => 'Потерянный результат',
            'text' => "```js\nlet city = '  казань ';\ncity.trim();\ncity.toUpperCase();\nconsole.log(city);\n```\n\nЧто будет выведено?",
            'options' => ["'КАЗАНЬ'", "'казань'", "'  казань '", "'  КАЗАНЬ '"],
            'answer' => 2,
            'explain' => 'Методы строк не меняют строку, а возвращают новую. Результаты `trim()` и `toUpperCase()` здесь никуда не сохранены. Правильно: `city = city.trim().toUpperCase();`',
        ],
        [
            'id' => 'initials',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Инициалы',
            'text' => "Допиши функцию `initials(fullName)`: `'Иван Петров'` → `'И. П.'`",
            'starter' => <<<'JS'
function initials(fullName) {
  return fullName
    .split(___)
    .map((word) => ___)
    .join(___);
}

console.log(initials('Иван Петров'));
JS,
            'tests' => <<<'JS'
test("'Иван Петров' → 'И. П.'", () => assertEqual(initials('Иван Петров'), 'И. П.'));
test('три слова', () => assertEqual(initials('Анна Мария Вольф'), 'А. М. В.'));
test('одно слово', () => assertEqual(initials('Платон'), 'П.'));
JS,
            'hints' => [
                'Разбиваем по пробелу и склеиваем тоже через пробел.',
                'Каждое слово заменяем на первую букву с точкой: `word[0] + \'.\'`.',
            ],
            'solution' => <<<'JS'
function initials(fullName) {
  return fullName
    .split(' ')
    .map((word) => word[0] + '.')
    .join(' ');
}
JS,
        ],
        [
            'id' => 'truncate',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Обрезать длинный текст',
            'text' => "Функция `truncate(text, max)` готовит текст для карточки товара: если он длиннее `max` символов, оставляет первые `max` и добавляет многоточие `…`. Короткий текст должен оставаться как есть. В функции две ошибки.",
            'starter' => <<<'JS'
function truncate(text, max) {
  return text.slice(1, max) + '…';
}

console.log(truncate('привет, мир', 6));
console.log(truncate('кот', 6));
JS,
            'tests' => <<<'JS'
test("('привет, мир', 6) → 'привет…'", () => assertEqual(truncate('привет, мир', 6), 'привет…'));
test('короткий текст не меняется', () => assertEqual(truncate('кот', 6), 'кот'));
test('длина ровно max — не меняется', () => assertEqual(truncate('привет', 6), 'привет'));
test('max = 1', () => assertEqual(truncate('да или нет', 1), 'д…'));
JS,
            'hints' => [
                'Символы нумеруются с нуля. `slice(1, max)` теряет первую букву.',
                'Многоточие добавляется всегда, а нужно только когда `text.length > max`. Используй ранний выход.',
            ],
            'solution' => <<<'JS'
function truncate(text, max) {
  if (text.length <= max) {
    return text;
  }
  return text.slice(0, max) + '…';
}
JS,
        ],
        [
            'id' => 'slug',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Адрес из заголовка',
            'text' => "Напиши функцию `slugify(title)`, которая превращает заголовок в часть адреса страницы: нижний регистр, слова через дефис, без пробелов по краям.\n\n`'  Hello Big World '` → `'hello-big-world'`\n\nМежду словами может быть несколько пробелов подряд.",
            'starter' => <<<'JS'
function slugify(title) {

}

console.log(slugify('  Hello Big World '));
JS,
            'tests' => <<<'JS'
test("'Hello World' → 'hello-world'", () => assertEqual(slugify('Hello World'), 'hello-world'));
test('пробелы по краям', () => assertEqual(slugify('  Hello Big World '), 'hello-big-world'));
test('несколько пробелов подряд', () => assertEqual(slugify('a   b'), 'a-b'));
test('одно слово', () => assertEqual(slugify('JavaScript'), 'javascript'));
test('кириллица', () => assertEqual(slugify('Привет Мир'), 'привет-мир'));
JS,
            'hints' => [
                'Шаги: убрать пробелы по краям → нижний регистр → разбить на слова → склеить через дефис.',
                'После `split(\' \')` при двойных пробелах в массиве появятся пустые строки. Их можно отфильтровать: `.filter((word) => word !== \'\')`.',
                'Другой путь — разбивать сразу по любому количеству пробелов: `split(/\s+/)`.',
            ],
            'solution' => <<<'JS'
function slugify(title) {
  return title
    .trim()
    .toLowerCase()
    .split(' ')
    .filter((word) => word !== '')
    .join('-');
}
JS,
        ],
        [
            'id' => 'mask',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Скрыть номер карты',
            'text' => "Напиши функцию `maskCard(number)`, которая оставляет видимыми только последние четыре символа, а остальные заменяет звёздочками. Длина строки сохраняется.\n\n`'1234567812345678'` → `'************5678'`",
            'starter' => <<<'JS'
function maskCard(number) {

}

console.log(maskCard('1234567812345678'));
JS,
            'tests' => <<<'JS'
test('16 цифр', () => assertEqual(maskCard('1234567812345678'), '************5678'));
test('8 цифр', () => assertEqual(maskCard('12345678'), '****5678'));
test('ровно 4 цифры — без звёздочек', () => assertEqual(maskCard('5678'), '5678'));
test('меньше 4 — без изменений', () => assertEqual(maskCard('12'), '12'));
JS,
            'hints' => [
                'Последние четыре символа: `number.slice(-4)`.',
                'Дальше два пути. Первый: дополнить хвост звёздочками слева до исходной длины — `tail.padStart(number.length, \'*\')`. Второй: `\'*\'.repeat(сколько нужно) + tail`.',
            ],
            'solution' => <<<'JS'
function maskCard(number) {
  return number.slice(-4).padStart(number.length, '*');
}
JS,
        ],
        [
            'id' => 'query-plan',
            'type' => 'plan',
            'title' => 'Параметры из адреса: план',
            'text' => "В адресе страницы после знака вопроса идут параметры: `name=anna&page=2&sort=price`.\n\nЗадача: превратить такую строку в объект `{ name: 'anna', page: '2', sort: 'price' }`. Для пустой строки вернуть пустой объект.\n\nЗапиши шаги. Попробуй сначала разобрать пример руками: на какие части ты делишь строку и в каком порядке?",
            'reference' => <<<'MD'
1. Если строка пустая — вернуть пустой объект.
2. Завести пустой объект для результата.
3. Разбить строку по `&` — получатся пары вида `name=anna`.
4. Пройти по парам.
5. Каждую пару разбить по `=` — получатся ключ и значение.
6. Записать значение в объект под этим ключом.
7. Вернуть объект.

Разбор идёт «снаружи внутрь»: сначала крупный разделитель, потом мелкий. Так разбирается почти любой текстовый формат — строки файла, затем поля в строке.

Шаг 1 нужен потому, что `''.split('&')` возвращает не пустой массив, а массив с одной пустой строкой: `['']`.
MD,
        ],
        [
            'id' => 'query',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Параметры из адреса: код',
            'text' => "Напиши функцию `parseQuery(query)` по своему плану. Значения остаются строками.\n\nВ обычной работе для этого есть готовый класс `URLSearchParams`. Но написать разбор один раз самому полезно: формат «ключ=значение» с разделителями встречается повсюду.",
            'starter' => "",
            'tests' => <<<'JS'
test('три параметра', () => assertEqual(parseQuery('name=anna&page=2&sort=price'), { name: 'anna', page: '2', sort: 'price' }));
test('один параметр', () => assertEqual(parseQuery('q=js'), { q: 'js' }));
test('пустая строка → {}', () => assertEqual(parseQuery(''), {}));
test('пустое значение', () => assertEqual(parseQuery('q=&page=1'), { q: '', page: '1' }));
test('значения остаются строками', () => assertEqual(typeof parseQuery('page=2').page, 'string'));
JS,
            'hints' => [
                'Разбей и сразу выведи, что получилось: `console.log(query.split(\'&\'));`',
                'Пару удобно разобрать сразу в две переменные: `const [key, value] = pair.split(\'=\');`',
                'Запись в объект по ключу из переменной: `result[key] = value;`',
            ],
            'solution' => <<<'JS'
function parseQuery(query) {
  if (query === '') {
    return {};
  }

  const result = {};

  for (const pair of query.split('&')) {
    const [key, value] = pair.split('=');
    result[key] = value;
  }

  return result;
}
JS,
        ],
    ],
    'cards' => [
        ['Почему `name.trim();` отдельной строкой ничего не делает?', 'Строки неизменяемы: метод возвращает новую строку. Результат нужно сохранить: `name = name.trim();`'],
        ['Какой парой методов разбирают и собирают строку?', '`split(разделитель)` превращает строку в массив, `join(разделитель)` — массив обратно в строку. Между ними работают методы массивов.'],
        ['Как взять последние 4 символа строки?', '`text.slice(-4)`. Отрицательные числа в `slice` отсчитываются с конца.'],
        ['Чем `replace` отличается от `replaceAll`?', '`replace` со строкой заменяет только первое вхождение, `replaceAll` — все.'],
        ['Что вернёт `\'\'.split(\'&\')`?', 'Массив с одной пустой строкой `[\'\']`, а не пустой массив. Пустой вход нужно обрабатывать отдельно.'],
    ],
];
