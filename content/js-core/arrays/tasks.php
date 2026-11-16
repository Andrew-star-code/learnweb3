<?php
return [
    'tasks' => [
        [
            'id' => 'sort-quiz',
            'type' => 'quiz',
            'title' => 'Сортировка чисел',
            'text' => 'Что вернёт `[10, 9, 1].sort()`?',
            'options' => ['[1, 9, 10]', '[1, 10, 9]', '[10, 9, 1]', 'Будет ошибка'],
            'answer' => 1,
            'explain' => 'Без функции сравнения `sort` сравнивает элементы как строки: `\'1\'` < `\'10\'` < `\'9\'`. Для чисел нужна функция: `sort((a, b) => a - b)`.',
        ],
        [
            'id' => 'active-names',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Цепочка filter и map',
            'text' => 'Допиши функцию `activeNames(users)`: она возвращает имена только активных пользователей (у них `active: true`).',
            'starter' => <<<'JS'
function activeNames(users) {
  return users
    .filter((user) => ___)
    .map((user) => ___);
}

console.log(activeNames([
  { name: 'Аня', active: true },
  { name: 'Борис', active: false },
]));
JS,
            'tests' => <<<'JS'
const users = [{ name: 'Аня', active: true }, { name: 'Борис', active: false }, { name: 'Вера', active: true }];
test('только активные', () => assertEqual(activeNames(users), ['Аня', 'Вера']));
test('нет активных → []', () => assertEqual(activeNames([{ name: 'Гриша', active: false }]), []));
test('пустой массив', () => assertEqual(activeNames([]), []));
JS,
            'hints' => [
                'Функция в `filter` должна вернуть `true` для тех, кого оставляем: `user.active`.',
                'Функция в `map` возвращает то, чем заменить элемент: `user.name`.',
            ],
            'solution' => <<<'JS'
function activeNames(users) {
  return users
    .filter((user) => user.active)
    .map((user) => user.name);
}
JS,
        ],
        [
            'id' => 'sort-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Сортировка, которая портит данные',
            'text' => "Функция `sortedPrices(prices)` должна возвращать **новый** массив цен по возрастанию, не трогая исходный. В ней две ошибки.",
            'starter' => <<<'JS'
function sortedPrices(prices) {
  return prices.sort();
}

const prices = [100, 25, 9];
console.log(sortedPrices(prices));
console.log(prices);
JS,
            'tests' => <<<'JS'
test('[100, 25, 9] → [9, 25, 100]', () => assertEqual(sortedPrices([100, 25, 9]), [9, 25, 100]));
test('отрицательные и дробные', () => assertEqual(sortedPrices([1.5, -3, 0]), [-3, 0, 1.5]));
test('исходный массив не меняется', () => {
  const source = [3, 1, 2];
  sortedPrices(source);
  assertEqual(source, [3, 1, 2]);
});
test('возвращается другой массив', () => {
  const source = [1, 2];
  assert(sortedPrices(source) !== source, 'Функция вернула тот же самый массив, а не копию');
});
JS,
            'hints' => [
                'Первая ошибка: `sort` без аргумента сравнивает числа как строки. Нужна функция сравнения `(a, b) => a - b`.',
                'Вторая: `sort` меняет массив, у которого вызван. Сначала сделай копию: `[...prices]`.',
            ],
            'solution' => <<<'JS'
function sortedPrices(prices) {
  return [...prices].sort((a, b) => a - b);
}
JS,
        ],
        [
            'id' => 'cart-total',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Накопление через reduce',
            'text' => "Напиши функцию `cartTotal(items)` с помощью `reduce`. Каждый элемент — объект с полями `price` и `qty`, результат — общая стоимость.\n\nЭто то же накопление, что ты писал циклом: накопитель, прибавление на каждом шаге, начальное значение `0`.",
            'starter' => <<<'JS'
function cartTotal(items) {

}

console.log(cartTotal([{ price: 100, qty: 2 }, { price: 50, qty: 1 }]));
JS,
            'tests' => <<<'JS'
test('две позиции → 250', () => assertEqual(cartTotal([{ price: 100, qty: 2 }, { price: 50, qty: 1 }]), 250));
test('одна позиция', () => assertEqual(cartTotal([{ price: 99, qty: 3 }]), 297));
test('пустая корзина → 0', () => assertEqual(cartTotal([]), 0));
JS,
            'hints' => [
                'Каркас: `items.reduce((sum, item) => ..., 0)`.',
                'На каждом шаге возвращаем новое значение накопителя: `sum + item.price * item.qty`.',
            ],
            'solution' => <<<'JS'
function cartTotal(items) {
  return items.reduce((sum, item) => sum + item.price * item.qty, 0);
}
JS,
        ],
        [
            'id' => 'unique',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Без повторов',
            'text' => "Напиши функцию `unique(items)`, которая возвращает новый массив без повторяющихся значений. Порядок — по первому появлению.\n\n`unique([3, 1, 3, 2, 1])` → `[3, 1, 2]`",
            'starter' => <<<'JS'
function unique(items) {

}

console.log(unique([3, 1, 3, 2, 1]));
JS,
            'tests' => <<<'JS'
test('[3, 1, 3, 2, 1] → [3, 1, 2]', () => assertEqual(unique([3, 1, 3, 2, 1]), [3, 1, 2]));
test('строки', () => assertEqual(unique(['а', 'б', 'а']), ['а', 'б']));
test('без повторов — без изменений', () => assertEqual(unique([1, 2, 3]), [1, 2, 3]));
test('пустой массив', () => assertEqual(unique([]), []));
test("1 и '1' — разные значения", () => assertEqual(unique([1, '1', 1]), [1, '1']));
JS,
            'hints' => [
                'Это отбор: кладём элемент в результат, только если его там ещё нет.',
                'Проверка «ещё нет»: `!result.includes(item)`.',
            ],
            'solution' => <<<'JS'
function unique(items) {
  const result = [];

  for (const item of items) {
    if (!result.includes(item)) {
      result.push(item);
    }
  }

  return result;
}

// Короче — через Set, коллекцию без повторов:
// return [...new Set(items)];
JS,
        ],
        [
            'id' => 'top-plan',
            'type' => 'plan',
            'title' => 'Лучшие результаты: план',
            'text' => "Есть результаты теста:\n\n```js\n[{ name: 'Аня', score: 82 }, { name: 'Борис', score: 95 }, { name: 'Вера', score: 67 }]\n```\n\nЗадача: вернуть имена `n` участников с лучшими результатами, от лучшего к худшему. Для `n = 2` ответ `['Борис', 'Аня']`. Исходный массив менять нельзя.\n\nЗапиши шаги и рядом с каждым — метод массива, который его выполняет.",
            'reference' => <<<'MD'
1. Сделать копию массива, чтобы не испортить исходный. — `[...results]`
2. Отсортировать копию по убыванию баллов. — `sort((a, b) => b.score - a.score)`
3. Взять первые n элементов. — `slice(0, n)`
4. Заменить каждый объект на имя. — `map((r) => r.name)`

Каждый шаг — один метод, и вся функция записывается одной цепочкой. Порядок шагов важен: если сначала взять первые n, а потом сортировать, получатся не лучшие, а просто первые по списку.
MD,
        ],
        [
            'id' => 'top',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Лучшие результаты: код',
            'text' => 'Напиши функцию `topNames(results, n)` по своему плану.',
            'starter' => "",
            'tests' => <<<'JS'
const results = [{ name: 'Аня', score: 82 }, { name: 'Борис', score: 95 }, { name: 'Вера', score: 67 }, { name: 'Гриша', score: 90 }];
test('двое лучших', () => assertEqual(topNames(results, 2), ['Борис', 'Гриша']));
test('один лучший', () => assertEqual(topNames(results, 1), ['Борис']));
test('n больше длины — все, по убыванию', () => assertEqual(topNames(results, 10), ['Борис', 'Гриша', 'Аня', 'Вера']));
test('n = 0 → []', () => assertEqual(topNames(results, 0), []));
test('исходный массив не меняется', () => {
  topNames(results, 2);
  assertEqual(results.map((r) => r.name), ['Аня', 'Борис', 'Вера', 'Гриша']);
});
JS,
            'hints' => [
                'Начни с копии и сортировки, выведи результат и убедись, что порядок верный: `[...results].sort((a, b) => b.score - a.score)`.',
                'Дальше по цепочке: `.slice(0, n).map((r) => r.name)`.',
            ],
            'solution' => <<<'JS'
function topNames(results, n) {
  return [...results]
    .sort((a, b) => b.score - a.score)
    .slice(0, n)
    .map((r) => r.name);
}
JS,
        ],
    ],
    'cards' => [
        ['Какой метод массива соответствует какому приёму?', 'Преобразование — `map`, отбор — `filter`, накопление — `reduce`, поиск одного элемента — `find`.'],
        ['Какие методы меняют исходный массив?', '`push`, `pop`, `shift`, `unshift`, `sort`, `reverse`, `splice`. Не меняют: `map`, `filter`, `slice`, `concat`, `toSorted`.'],
        ['Как правильно отсортировать числа по возрастанию, не испортив исходный массив?', '`[...numbers].sort((a, b) => a - b)`. Копия — потому что `sort` меняет массив, функция сравнения — потому что без неё сравнение строковое.'],
        ['Чем `slice` отличается от `splice`?', '`slice(from, to)` возвращает кусок и не трогает массив. `splice` удаляет или вставляет элементы прямо в исходном массиве.'],
        ['Зачем `reduce` второй аргумент?', 'Это начальное значение накопителя. Без него `reduce` на пустом массиве бросает ошибку.'],
    ],
];
