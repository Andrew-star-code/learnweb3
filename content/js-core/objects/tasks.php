<?php
return [
    'tasks' => [
        [
            'id' => 'reference-quiz',
            'type' => 'quiz',
            'title' => 'Копия или ссылка',
            'text' => "```js\nconst a = { x: 1 };\nconst b = a;\nb.x = 2;\n```\n\nЧему равно `a.x`?",
            'options' => ['1', '2', 'undefined', 'Будет ошибка: a объявлена через const'],
            'answer' => 1,
            'explain' => '`b = a` копирует не объект, а ссылку на него. Обе переменные указывают на один объект. `const` запрещает присвоить переменной другой объект, но менять содержимое не мешает.',
        ],
        [
            'id' => 'make-user',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Собрать и разобрать объект',
            'text' => "Допиши две функции:\n\n- `makeUser(name, age)` возвращает объект с полями `name`, `age` и `isAdult` (истина, если возраст от 18);\n- `describe(user)` возвращает строку вида `Аня, 30` — поля достаются разбором объекта прямо в параметрах.",
            'starter' => <<<'JS'
function makeUser(name, age) {
  const isAdult = ___;
  return { ___ };
}

function describe({ ___ }) {
  return `${name}, ${age}`;
}

console.log(describe(makeUser('Аня', 30)));
JS,
            'tests' => <<<'JS'
test("makeUser('Аня', 30)", () => assertEqual(makeUser('Аня', 30), { name: 'Аня', age: 30, isAdult: true }));
test('17 лет — не взрослый', () => assertEqual(makeUser('Петя', 17).isAdult, false));
test('18 лет — взрослый', () => assertEqual(makeUser('Оля', 18).isAdult, true));
test('describe', () => assertEqual(describe({ name: 'Аня', age: 30 }), 'Аня, 30'));
JS,
            'hints' => [
                '`isAdult` — результат сравнения: `age >= 18`.',
                'Если имя переменной совпадает с именем поля, достаточно перечислить их: `{ name, age, isAdult }`.',
                'Разбор в параметрах выглядит так же: `function describe({ name, age })`.',
            ],
            'solution' => <<<'JS'
function makeUser(name, age) {
  const isAdult = age >= 18;
  return { name, age, isAdult };
}

function describe({ name, age }) {
  return `${name}, ${age}`;
}
JS,
        ],
        [
            'id' => 'update-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Функция, которая портит аргумент',
            'text' => "Функция `withEmail(user, email)` должна возвращать **нового** пользователя с изменённой почтой. Сейчас она меняет исходный объект — и тот, кто его передал, получает сюрприз.",
            'starter' => <<<'JS'
function withEmail(user, email) {
  user.email = email;
  return user;
}

const anna = { name: 'Аня', email: 'old@mail.ru' };
const updated = withEmail(anna, 'new@mail.ru');
console.log(anna.email, updated.email);
JS,
            'tests' => <<<'JS'
test('у нового объекта новая почта', () => {
  assertEqual(withEmail({ name: 'Аня', email: 'old@mail.ru' }, 'new@mail.ru'), { name: 'Аня', email: 'new@mail.ru' });
});
test('исходный объект не меняется', () => {
  const anna = { name: 'Аня', email: 'old@mail.ru' };
  withEmail(anna, 'new@mail.ru');
  assertEqual(anna.email, 'old@mail.ru');
});
test('остальные поля сохраняются', () => {
  assertEqual(withEmail({ name: 'Борис', age: 40 }, 'b@mail.ru'), { name: 'Борис', age: 40, email: 'b@mail.ru' });
});
JS,
            'hints' => [
                'Нужно вернуть копию объекта, в которой заменено одно поле.',
                'Копия с изменением: `{ ...user, email: email }`, или короче `{ ...user, email }`.',
            ],
            'solution' => <<<'JS'
function withEmail(user, email) {
  return { ...user, email };
}
JS,
        ],
        [
            'id' => 'count-words',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Объект как счётчик',
            'text' => "Напиши функцию `countWords(words)`, которая считает, сколько раз встретилось каждое слово.\n\n`countWords(['кот', 'пёс', 'кот'])` → `{ кот: 2, пёс: 1 }`",
            'starter' => <<<'JS'
function countWords(words) {

}

console.log(countWords(['кот', 'пёс', 'кот']));
JS,
            'tests' => <<<'JS'
test("['кот', 'пёс', 'кот']", () => assertEqual(countWords(['кот', 'пёс', 'кот']), { кот: 2, пёс: 1 }));
test('одно слово', () => assertEqual(countWords(['да']), { да: 1 }));
test('пустой массив → {}', () => assertEqual(countWords([]), {}));
test('три одинаковых', () => assertEqual(countWords(['a', 'a', 'a']), { a: 3 }));
JS,
            'hints' => [
                'Пустой объект до цикла, в цикле увеличиваем значение по ключу-слову, после цикла возвращаем объект.',
                'Слово лежит в переменной, поэтому нужны квадратные скобки: `counts[word]`.',
                'При первой встрече слова значения ещё нет: `counts[word] = (counts[word] ?? 0) + 1;`',
            ],
            'solution' => <<<'JS'
function countWords(words) {
  const counts = {};

  for (const word of words) {
    counts[word] = (counts[word] ?? 0) + 1;
  }

  return counts;
}
JS,
        ],
        [
            'id' => 'pick',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Только нужные поля',
            'text' => "Напиши функцию `pick(object, keys)`, которая возвращает новый объект только с перечисленными полями. Полей, которых в исходном объекте нет, в результате быть не должно.\n\n`pick({ id: 1, name: 'Аня', password: '123' }, ['id', 'name'])` → `{ id: 1, name: 'Аня' }`\n\nТак убирают лишнее перед отправкой данных на страницу — например, пароль.",
            'starter' => <<<'JS'
function pick(object, keys) {

}

console.log(pick({ id: 1, name: 'Аня', password: '123' }, ['id', 'name']));
JS,
            'tests' => <<<'JS'
const user = { id: 1, name: 'Аня', password: '123' };
test('два поля', () => assertEqual(pick(user, ['id', 'name']), { id: 1, name: 'Аня' }));
test('несуществующее поле не добавляется', () => assertEqual(pick(user, ['id', 'email']), { id: 1 }));
test('пустой список полей → {}', () => assertEqual(pick(user, []), {}));
test('исходный объект не меняется', () => { pick(user, ['id']); assertEqual(Object.keys(user).length, 3); });
test('поле со значением 0 сохраняется', () => assertEqual(pick({ count: 0 }, ['count']), { count: 0 }));
JS,
            'hints' => [
                'Перебираем не объект, а массив `keys`. Для каждого ключа копируем значение: `result[key] = object[key]`.',
                'Перед копированием проверь, что поле есть: `if (key in object)`. Проверка `if (object[key])` не годится — она отбросит ноль и пустую строку.',
            ],
            'solution' => <<<'JS'
function pick(object, keys) {
  const result = {};

  for (const key of keys) {
    if (key in object) {
      result[key] = object[key];
    }
  }

  return result;
}
JS,
        ],
        [
            'id' => 'group-plan',
            'type' => 'plan',
            'title' => 'Группировка: план',
            'text' => "Есть список товаров:\n\n```js\n[\n  { name: 'яблоко', type: 'фрукты' },\n  { name: 'морковь', type: 'овощи' },\n  { name: 'груша', type: 'фрукты' },\n]\n```\n\nЗадача: сгруппировать их по значению заданного поля. Для поля `'type'` результат:\n\n```js\n{\n  фрукты: [{ name: 'яблоко', ... }, { name: 'груша', ... }],\n  овощи: [{ name: 'морковь', ... }],\n}\n```\n\nЗапиши шаги. Чем это похоже на подсчёт слов и чем отличается?",
            'reference' => <<<'MD'
1. Завести пустой объект для групп.
2. Пройти по всем элементам.
3. Достать из элемента значение поля, по которому группируем, — это ключ группы.
4. Если группы с таким ключом ещё нет — создать её: положить в объект пустой массив.
5. Добавить элемент в массив своей группы.
6. Вернуть объект.

Это тот же приём, что и счётчик слов: объект, в котором ключ — признак, а значение накапливается. Разница только в том, что накапливается: там число (начинаем с нуля и прибавляем единицу), здесь массив (начинаем с пустого и добавляем элемент).
MD,
        ],
        [
            'id' => 'group',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Группировка: код',
            'text' => "Напиши функцию `groupBy(items, key)` по своему плану. `key` — имя поля в виде строки.",
            'starter' => "",
            'tests' => <<<'JS'
const goods = [{ name: 'яблоко', type: 'фрукты' }, { name: 'морковь', type: 'овощи' }, { name: 'груша', type: 'фрукты' }];
test('группировка по type', () => assertEqual(groupBy(goods, 'type'), {
  фрукты: [{ name: 'яблоко', type: 'фрукты' }, { name: 'груша', type: 'фрукты' }],
  овощи: [{ name: 'морковь', type: 'овощи' }],
}));
test('группировка по другому полю', () => assertEqual(
  groupBy([{ n: 1, even: false }, { n: 2, even: true }, { n: 3, even: false }], 'even'),
  { false: [{ n: 1, even: false }, { n: 3, even: false }], true: [{ n: 2, even: true }] },
));
test('пустой массив → {}', () => assertEqual(groupBy([], 'type'), {}));
test('порядок внутри группы сохраняется', () => assertEqual(groupBy(goods, 'type')['фрукты'].map((g) => g.name), ['яблоко', 'груша']));
JS,
            'hints' => [
                'Ключ группы для текущего элемента: `const group = item[key];`',
                'Создать группу, если её нет: `if (!(group in groups)) { groups[group] = []; }`',
                'Добавить элемент: `groups[group].push(item);`',
            ],
            'solution' => <<<'JS'
function groupBy(items, key) {
  const groups = {};

  for (const item of items) {
    const group = item[key];
    if (!(group in groups)) {
      groups[group] = [];
    }
    groups[group].push(item);
  }

  return groups;
}
JS,
        ],
    ],
    'cards' => [
        ['Когда нужны квадратные скобки для доступа к свойству объекта?', 'Когда имя свойства лежит в переменной: `user[field]`. Запись `user.field` ищет свойство с буквальным именем «field».'],
        ['Что происходит при `const b = a`, если `a` — объект?', 'Копируется ссылка, а не объект. Обе переменные указывают на один объект, изменение через `b` видно через `a`.'],
        ['Как вернуть копию объекта с одним изменённым полем?', '`{ ...user, age: 31 }`. Копия поверхностная: вложенные объекты остаются общими.'],
        ['Как посчитать, сколько раз встретилось каждое значение?', 'Объект-счётчик: `counts[x] = (counts[x] ?? 0) + 1` в цикле.'],
        ['Как перебрать объект?', '`Object.keys(obj)` — ключи, `Object.values(obj)` — значения, `Object.entries(obj)` — пары. Результат — массив, дальше работают методы массивов.'],
    ],
];
