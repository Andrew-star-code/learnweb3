<?php
return [
    'tasks' => [
        [
            'id' => 'independent-quiz',
            'type' => 'quiz',
            'title' => 'Два счётчика',
            'text' => "```js\nfunction makeCounter() {\n  let count = 0;\n  return () => ++count;\n}\n\nconst a = makeCounter();\nconst b = makeCounter();\na();\na();\nconsole.log(b());\n```\n\nЧто будет выведено?",
            'options' => ['1', '2', '3', 'undefined'],
            'answer' => 0,
            'explain' => 'Каждый вызов `makeCounter` создаёт новую переменную `count`. У `a` и `b` счётчики разные, вызовы `a()` на `b` не влияют.',
        ],
        [
            'id' => 'counter-fill',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Счётчик с шагом',
            'text' => "Допиши `makeCounter(step)`: возвращённая функция при каждом вызове увеличивает счётчик на `step` и возвращает новое значение. Если шаг не передан, он равен `1`.",
            'starter' => <<<'JS'
function makeCounter(step ___) {
  let count = 0;

  return function () {
    ___
    return ___;
  };
}

const next = makeCounter(5);
console.log(next(), next(), next());
JS,
            'tests' => <<<'JS'
test('шаг 5: 5, 10, 15', () => { const c = makeCounter(5); assertEqual([c(), c(), c()], [5, 10, 15]); });
test('шаг по умолчанию — 1', () => { const c = makeCounter(); assertEqual([c(), c()], [1, 2]); });
test('счётчики независимы', () => { const a = makeCounter(); const b = makeCounter(10); a(); a(); assertEqual(b(), 10); });
JS,
            'hints' => [
                'Значение по умолчанию: `step = 1`.',
                'Внутри: `count += step;`, затем `return count;`.',
            ],
            'solution' => <<<'JS'
function makeCounter(step = 1) {
  let count = 0;

  return function () {
    count += step;
    return count;
  };
}
JS,
        ],
        [
            'id' => 'var-loop',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Ловушка из легаси',
            'text' => "Функция `makeButtons(labels)` создаёт для каждой подписи обработчик, который возвращает строку вида `Нажата кнопка 0: Сохранить`. Код написан в старом стиле, и все обработчики ведут себя одинаково неправильно. Запусти и посмотри на вывод.",
            'starter' => <<<'JS'
function makeButtons(labels) {
  var handlers = [];

  for (var i = 0; i < labels.length; i++) {
    handlers.push(function () {
      return 'Нажата кнопка ' + i + ': ' + labels[i];
    });
  }

  return handlers;
}

const buttons = makeButtons(['Сохранить', 'Отмена']);
console.log(buttons[0]());
console.log(buttons[1]());
JS,
            'tests' => <<<'JS'
const handlers = makeButtons(['Сохранить', 'Отмена', 'Удалить']);
test('первая кнопка', () => assertEqual(handlers[0](), 'Нажата кнопка 0: Сохранить'));
test('вторая кнопка', () => assertEqual(handlers[1](), 'Нажата кнопка 1: Отмена'));
test('третья кнопка', () => assertEqual(handlers[2](), 'Нажата кнопка 2: Удалить'));
test('обработчиков столько же, сколько подписей', () => assertEqual(handlers.length, 3));
JS,
            'hints' => [
                'Все обработчики выводят номер, равный длине массива, и `undefined` вместо подписи. Значит, все они видят одну и ту же `i` — уже после окончания цикла.',
                'Замени `var i` на `let i`: тогда у каждого шага цикла будет своя переменная.',
            ],
            'solution' => <<<'JS'
function makeButtons(labels) {
  const handlers = [];

  for (let i = 0; i < labels.length; i++) {
    handlers.push(function () {
      return 'Нажата кнопка ' + i + ': ' + labels[i];
    });
  }

  return handlers;
}
JS,
        ],
        [
            'id' => 'multiplier',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Функция с настройкой',
            'text' => "Напиши функцию `makeDiscount(percent)`. Она возвращает функцию, которая принимает цену и возвращает цену со скидкой.\n\n```js\nconst sale = makeDiscount(20);\nsale(1000);   // 800\n```",
            'starter' => <<<'JS'
function makeDiscount(percent) {

}

const sale = makeDiscount(20);
console.log(sale(1000));
JS,
            'tests' => <<<'JS'
test('скидка 20% с 1000 → 800', () => assertEqual(makeDiscount(20)(1000), 800));
test('скидка 0% — цена не меняется', () => assertEqual(makeDiscount(0)(500), 500));
test('скидка 100% → 0', () => assertEqual(makeDiscount(100)(500), 0));
test('две скидки независимы', () => {
  const small = makeDiscount(10);
  const big = makeDiscount(50);
  assertEqual([small(200), big(200)], [180, 100]);
});
test('возвращается функция', () => assertEqual(typeof makeDiscount(5), 'function'));
JS,
            'hints' => [
                'Внешняя функция только возвращает внутреннюю: `return (price) => ...;`',
                'Внутренняя помнит `percent`: `price - price * percent / 100`.',
            ],
            'solution' => <<<'JS'
function makeDiscount(percent) {
  return (price) => price - price * percent / 100;
}
JS,
        ],
        [
            'id' => 'once',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Только один раз',
            'text' => "Напиши функцию `once(fn)`. Она возвращает новую функцию, которая вызывает `fn` только при первом вызове, а при всех следующих просто возвращает результат первого.\n\nТак защищают действия, которые нельзя повторять: отправку заказа при двойном щелчке, инициализацию.",
            'starter' => <<<'JS'
function once(fn) {

}

const init = once(() => {
  console.log('инициализация');
  return 42;
});
console.log(init(), init(), init());
JS,
            'tests' => <<<'JS'
test('fn вызывается один раз', () => {
  let calls = 0;
  const f = once(() => { calls++; });
  f(); f(); f();
  assertEqual(calls, 1);
});
test('возвращается результат первого вызова', () => {
  let n = 0;
  const f = once(() => ++n);
  assertEqual([f(), f(), f()], [1, 1, 1]);
});
test('аргументы первого вызова передаются в fn', () => {
  const f = once((a, b) => a + b);
  assertEqual(f(2, 3), 5);
  assertEqual(f(10, 10), 5);
});
test('результат undefined тоже запоминается', () => {
  let calls = 0;
  const f = once(() => { calls++; return undefined; });
  f(); f();
  assertEqual(calls, 1);
});
JS,
            'hints' => [
                'В замыкании нужны две переменные: флаг «уже вызывали» и сохранённый результат.',
                'Чтобы передать в `fn` любые аргументы: `return function (...args) { ... fn(...args) ... }`.',
                'Проверять нужно именно флаг, а не результат: `fn` может законно вернуть `undefined`.',
            ],
            'solution' => <<<'JS'
function once(fn) {
  let called = false;
  let result;

  return function (...args) {
    if (!called) {
      called = true;
      result = fn(...args);
    }
    return result;
  };
}
JS,
        ],
        [
            'id' => 'cart-plan',
            'type' => 'plan',
            'title' => 'Корзина: план',
            'text' => "Задача: функция `createCart()` возвращает объект-корзину с тремя методами:\n\n- `add(name, price)` — добавить товар;\n- `count()` — сколько товаров в корзине;\n- `total()` — общая стоимость.\n\nСписок товаров должен быть недоступен снаружи: изменить корзину можно только через `add`.\n\nЗапиши, какие данные хранит корзина, где они лежат и что делает каждый метод.",
            'reference' => <<<'MD'
**Данные:** массив товаров, каждый товар — объект с именем и ценой. Массив объявлен внутри `createCart` обычной переменной — так он не попадает в возвращаемый объект и снаружи не виден.

**Шаги `createCart`:**

1. Завести пустой массив товаров.
2. Вернуть объект с тремя методами.

**Методы:**

- `add` — положить в массив объект с именем и ценой.
- `count` — вернуть длину массива.
- `total` — накопление: сложить цены всех товаров.

Все три метода видят один и тот же массив, потому что созданы в одном вызове `createCart`. У другой корзины будет свой массив.
MD,
        ],
        [
            'id' => 'cart',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Корзина: код',
            'text' => 'Напиши `createCart()` по своему плану.',
            'starter' => "",
            'tests' => <<<'JS'
test('новая корзина пуста', () => { const c = createCart(); assertEqual([c.count(), c.total()], [0, 0]); });
test('add увеличивает count и total', () => {
  const c = createCart();
  c.add('чай', 150);
  c.add('сахар', 80);
  assertEqual([c.count(), c.total()], [2, 230]);
});
test('корзины независимы', () => {
  const a = createCart();
  const b = createCart();
  a.add('чай', 150);
  assertEqual(b.count(), 0);
});
test('список товаров недоступен снаружи', () => {
  const c = createCart();
  c.add('чай', 150);
  const exposed = Object.values(c).filter((value) => typeof value !== 'function');
  assertEqual(exposed, [], 'В объекте корзины должны быть только методы');
});
test('один товар можно добавить дважды', () => {
  const c = createCart();
  c.add('чай', 150);
  c.add('чай', 150);
  assertEqual([c.count(), c.total()], [2, 300]);
});
JS,
            'hints' => [
                'Каркас: `function createCart() { const items = []; return { add(name, price) {...}, count() {...}, total() {...} }; }`',
                'В `add`: `items.push({ name, price });`',
                'В `total`: `items.reduce((sum, item) => sum + item.price, 0)`.',
            ],
            'solution' => <<<'JS'
function createCart() {
  const items = [];

  return {
    add(name, price) {
      items.push({ name, price });
    },
    count() {
      return items.length;
    },
    total() {
      return items.reduce((sum, item) => sum + item.price, 0);
    },
  };
}
JS,
        ],
    ],
    'cards' => [
        ['Что такое замыкание — одним предложением?', 'Функция вместе с переменными того места, где она была создана: она помнит их и может использовать даже после того, как внешняя функция завершилась.'],
        ['Для чего замыкания нужны на практике?', 'Приватное состояние (счётчик, корзина), функции с настройкой (`makeDiscount(20)`), колбэки, которые видят переменные вокруг.'],
        ['Почему в цикле с `var i` все созданные функции видят одно значение?', '`var` создаёт одну переменную на всю функцию, а не на каждый шаг цикла. К моменту вызова цикл уже закончился. С `let` у каждого шага своя переменная.'],
        ['Два вызова `makeCounter()` — счётчики общие или разные?', 'Разные. Каждый вызов внешней функции создаёт новый набор переменных.'],
    ],
];
