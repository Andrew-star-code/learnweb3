<?php
return [
    'tasks' => [
        [
            'id' => 'state-quiz',
            'type' => 'quiz',
            'title' => 'Один раз и навсегда',
            'text' => "```js\nconst promise = new Promise((resolve, reject) => {\n  resolve('первый');\n  resolve('второй');\n  reject(new Error('ошибка'));\n});\n\npromise.then((value) => console.log(value));\n```\n\nЧто будет выведено?",
            'options' => ['первый', 'второй', 'первый, затем второй', 'Ошибка: промис отклонён'],
            'answer' => 0,
            'explain' => 'Промис меняет состояние один раз. После первого `resolve` остальные вызовы `resolve` и `reject` ничего не делают.',
        ],
        [
            'id' => 'delay',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Пауза как промис',
            'text' => "Допиши функцию `delay(ms)`: она возвращает промис, который выполняется через `ms` миллисекунд. Это одна из самых полезных маленьких функций в асинхронном коде.\n\n```js\ndelay(1000).then(() => console.log('прошла секунда'));\n```",
            'starter' => <<<'JS'
function delay(ms) {
  return new ___((resolve) => {
    setTimeout(___, ms);
  });
}

delay(100).then(() => console.log('прошло 100 мс'));
JS,
            'tests' => <<<'JS'
test('возвращает промис', () => assert(delay(1) instanceof Promise, 'delay должна вернуть промис'));
test('промис выполняется не раньше указанного времени', async () => {
  const start = Date.now();
  await delay(60);
  const elapsed = Date.now() - start;
  assert(elapsed >= 50, `Прошло всего ${elapsed} мс`);
});
test('и не намного позже', async () => {
  const start = Date.now();
  await delay(20);
  assert(Date.now() - start < 200, 'Промис выполнился слишком поздно');
});
JS,
            'hints' => [
                'Промис создаётся через `new Promise(…)`.',
                'Таймер должен вызвать `resolve`: `setTimeout(resolve, ms)`.',
            ],
            'solution' => <<<'JS'
function delay(ms) {
  return new Promise((resolve) => {
    setTimeout(resolve, ms);
  });
}
JS,
        ],
        [
            'id' => 'lost-return',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Потерянный return',
            'text' => "Функция `loadUserName(loadUser)` должна вернуть промис с именем пользователя в верхнем регистре. Сейчас она возвращает `undefined` — в цепочке потеряны два `return`.\n\n`loadUser` — функция, которая возвращает промис с объектом `{ name: '…' }`.",
            'starter' => <<<'JS'
function loadUserName(loadUser) {
  loadUser().then((user) => {
    user.name.toUpperCase();
  });
}

const fakeLoad = () => Promise.resolve({ name: 'аня' });
console.log(loadUserName(fakeLoad));
JS,
            'tests' => <<<'JS'
test('функция возвращает промис', () => assert(loadUserName(() => Promise.resolve({ name: 'x' })) instanceof Promise, 'Функция должна вернуть промис, а вернула не его'));
test('в промисе — имя в верхнем регистре', async () => assertEqual(await loadUserName(() => Promise.resolve({ name: 'аня' })), 'АНЯ'));
test('ошибка загрузки доходит до вызывающего кода', async () => {
  let message = 'ошибки не было';
  try {
    await loadUserName(() => Promise.reject(new Error('нет сети')));
  } catch (error) {
    message = error.message;
  }
  assertEqual(message, 'нет сети');
});
JS,
            'hints' => [
                'Первый `return` — перед всей цепочкой: `return loadUser().then(…)`. Без него функция ничего не отдаёт наружу.',
                'Второй — внутри обработчика: `return user.name.toUpperCase();`',
            ],
            'solution' => <<<'JS'
function loadUserName(loadUser) {
  return loadUser().then((user) => {
    return user.name.toUpperCase();
  });
}
JS,
        ],
        [
            'id' => 'promisify',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Обернуть колбэк в промис',
            'text' => "В легаси-коде есть функция `readFile(name, callback)` в старом стиле: колбэк получает два аргумента — ошибку (или `null`) и данные.\n\nНапиши обёртку `readFileAsync(name)`, которая возвращает промис: он выполняется с данными или отклоняется с ошибкой. Саму `readFile` не меняй.",
            'starter' => <<<'JS'
// Старое API — не менять
function readFile(name, callback) {
  setTimeout(() => {
    if (name === 'menu.txt') {
      callback(null, 'Эспрессо, Капучино, Раф');
    } else {
      callback(new Error(`Файл ${name} не найден`));
    }
  }, 20);
}

function readFileAsync(name) {

}

readFileAsync('menu.txt').then((data) => console.log(data));
JS,
            'tests' => <<<'JS'
test('возвращает промис', () => {
  const result = readFileAsync('menu.txt');
  assert(result instanceof Promise, 'readFileAsync должна вернуть промис');
});
test('выполняется с содержимым файла', async () => assertEqual(await readFileAsync('menu.txt'), 'Эспрессо, Капучино, Раф'));
test('отклоняется, если файла нет', async () => {
  let caught = null;
  try { await readFileAsync('nope.txt'); } catch (error) { caught = error; }
  assert(caught instanceof Error, 'Промис должен быть отклонён объектом Error');
  assertEqual(caught.message, 'Файл nope.txt не найден');
});
JS,
            'hints' => [
                'Каркас: `return new Promise((resolve, reject) => { readFile(name, (error, data) => { … }); });`',
                'В колбэке: если `error` — `reject(error)`, иначе `resolve(data)`.',
            ],
            'solution' => <<<'JS'
// Старое API — не менять
function readFile(name, callback) {
  setTimeout(() => {
    if (name === 'menu.txt') {
      callback(null, 'Эспрессо, Капучино, Раф');
    } else {
      callback(new Error(`Файл ${name} не найден`));
    }
  }, 20);
}

function readFileAsync(name) {
  return new Promise((resolve, reject) => {
    readFile(name, (error, data) => {
      if (error) {
        reject(error);
      } else {
        resolve(data);
      }
    });
  });
}
JS,
        ],
        [
            'id' => 'dashboard',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Два запроса одновременно',
            'text' => "Для главной страницы кабинета нужны пользователь и его заказы. Запросы друг от друга не зависят.\n\nНапиши функцию `loadDashboard(loadUser, loadOrders)`. Оба аргумента — функции, возвращающие промис. Результат — промис с объектом `{ user, orders }`.\n\nЗапросы должны идти **одновременно**: тест проверяет время.",
            'starter' => <<<'JS'
function loadDashboard(loadUser, loadOrders) {

}

const later = (value, ms) => () => new Promise((resolve) => setTimeout(() => resolve(value), ms));

loadDashboard(later({ name: 'Аня' }, 100), later([1, 2], 100))
  .then((data) => console.log(data));
JS,
            'tests' => <<<'JS'
const after = (value, ms) => () => new Promise((resolve) => setTimeout(() => resolve(value), ms));
test('результат — объект с user и orders', async () => {
  assertEqual(await loadDashboard(after({ name: 'Аня' }, 5), after([1, 2], 5)), { user: { name: 'Аня' }, orders: [1, 2] });
});
test('порядок полей не зависит от того, кто ответил первым', async () => {
  assertEqual(await loadDashboard(after('пользователь', 40), after('заказы', 5)), { user: 'пользователь', orders: 'заказы' });
});
test('запросы идут одновременно', async () => {
  const start = Date.now();
  await loadDashboard(after(1, 80), after(2, 80));
  const elapsed = Date.now() - start;
  assert(elapsed < 140, `Заняло ${elapsed} мс — похоже, запросы шли по очереди`);
});
test('ошибка любого запроса отклоняет результат', async () => {
  let message = 'ошибки не было';
  try {
    await loadDashboard(after(1, 5), () => Promise.reject(new Error('заказы недоступны')));
  } catch (error) { message = error.message; }
  assertEqual(message, 'заказы недоступны');
});
JS,
            'hints' => [
                '`Promise.all([loadUser(), loadOrders()])` запускает оба запроса и даёт промис с массивом из двух результатов.',
                'Массив нужно превратить в объект: `.then(([user, orders]) => ({ user, orders }))`. Круглые скобки вокруг фигурных нужны, чтобы стрелка вернула объект.',
            ],
            'solution' => <<<'JS'
function loadDashboard(loadUser, loadOrders) {
  return Promise.all([loadUser(), loadOrders()])
    .then(([user, orders]) => ({ user, orders }));
}
JS,
        ],
        [
            'id' => 'timeout-plan',
            'type' => 'plan',
            'title' => 'Ограничение по времени: план',
            'text' => "Задача: функция `withTimeout(promise, ms)` возвращает промис, который:\n\n- выполняется или отклоняется так же, как исходный, если тот успел за `ms` миллисекунд;\n- отклоняется с ошибкой `Превышено время ожидания`, если не успел.\n\nТак защищаются от запросов, которые «висят» бесконечно.\n\nЗапиши шаги. Подсказка: подумай, какой из методов `Promise` выбирает того, кто завершился первым.",
            'reference' => <<<'MD'
1. Создать второй промис — «будильник»: он ничего не делает `ms` миллисекунд, а затем отклоняется с ошибкой «Превышено время ожидания».
2. Устроить гонку между исходным промисом и будильником.
3. Вернуть результат гонки.

Гонка — это `Promise.race`: её результат определяется тем, кто завершился первым, с любым исходом. Успел исходный промис — получим его значение или его ошибку. Не успел — сработает будильник.

Будильник — это почти `delay` из задания выше, только в конце не `resolve`, а `reject`.

Важная деталь: проигравший гонку промис никто не отменяет. Исходное действие продолжит выполняться, просто его результат уже никому не нужен.
MD,
        ],
        [
            'id' => 'timeout',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Ограничение по времени: код',
            'text' => 'Напиши `withTimeout(promise, ms)` по своему плану.',
            'starter' => "",
            'tests' => <<<'JS'
const after = (value, ms) => new Promise((resolve) => setTimeout(() => resolve(value), ms));
const failAfter = (message, ms) => new Promise((resolve, reject) => setTimeout(() => reject(new Error(message)), ms));
const outcome = async (promise) => { try { return ['значение', await promise]; } catch (error) { return ['ошибка', error.message]; } };
test('быстрый промис отдаёт своё значение', async () => assertEqual(await outcome(withTimeout(after('готово', 10), 100)), ['значение', 'готово']));
test('медленный промис — ошибка ожидания', async () => assertEqual(await outcome(withTimeout(after('поздно', 150), 30)), ['ошибка', 'Превышено время ожидания']));
test('ошибка быстрого промиса проходит как есть', async () => assertEqual(await outcome(withTimeout(failAfter('сервер упал', 10), 100)), ['ошибка', 'сервер упал']));
test('ошибка ожидания — объект Error', async () => {
  let caught = null;
  try { await withTimeout(after(1, 150), 20); } catch (error) { caught = error; }
  assert(caught instanceof Error, 'Отклонять нужно объектом Error');
});
test('ошибка приходит вовремя, а не когда закончится медленный промис', async () => {
  const start = Date.now();
  await outcome(withTimeout(after(1, 300), 30));
  assert(Date.now() - start < 150, 'Функция ждала исходный промис до конца');
});
JS,
            'hints' => [
                'Будильник: `new Promise((resolve, reject) => { setTimeout(() => reject(new Error(\'Превышено время ожидания\')), ms); })`.',
                'Гонка: `return Promise.race([promise, alarm]);`',
            ],
            'solution' => <<<'JS'
function withTimeout(promise, ms) {
  const alarm = new Promise((resolve, reject) => {
    setTimeout(() => reject(new Error('Превышено время ожидания')), ms);
  });

  return Promise.race([promise, alarm]);
}
JS,
        ],
    ],
    'cards' => [
        ['Что такое промис?', 'Объект, представляющий результат, которого ещё нет. Состояния: ожидание → выполнен или отклонён, один раз и навсегда.'],
        ['Что возвращает `then` и что из этого следует?', 'Новый промис со значением, которое вернул обработчик. Поэтому шаги записываются цепочкой; если обработчик вернул промис, цепочка его дождётся.'],
        ['Что будет, если в обработчике `then` забыть `return`?', 'Дальше по цепочке уйдёт `undefined`, а запущенное внутри действие никто не дождётся.'],
        ['Чем `Promise.all` отличается от `Promise.race`?', '`all` ждёт все промисы и даёт массив результатов в исходном порядке (или первую ошибку). `race` завершается вместе с первым завершившимся, с любым исходом.'],
        ['Как превратить функцию с колбэком `(error, data)` в промис?', '`new Promise((resolve, reject) => fn(args, (error, data) => error ? reject(error) : resolve(data)))`'],
    ],
];
