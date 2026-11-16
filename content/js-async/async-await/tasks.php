<?php
return [
    'tasks' => [
        [
            'id' => 'forgot-quiz',
            'type' => 'quiz',
            'title' => 'Забытый await',
            'text' => "```js\nasync function getName() {\n  const user = loadUser();      // loadUser возвращает промис с { name: 'Аня' }\n  return user.name;\n}\n\nconsole.log(await getName());\n```\n\nЧто будет выведено?",
            'options' => ["'Аня'", 'undefined', 'Promise', 'TypeError'],
            'answer' => 1,
            'explain' => 'Без `await` в `user` лежит сам промис, а не объект пользователя. У промиса нет свойства `name`, поэтому получается `undefined`. Ошибки при этом нет — и это делает баг трудным для поиска.',
        ],
        [
            'id' => 'rewrite',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'От цепочки к await',
            'text' => "Функция `getUserName(loadUser)` возвращает имя пользователя. Если загрузка не удалась, она возвращает строку `'гость'`.\n\nЗаполни пропуски: функция написана через `async/await` и `try/catch`.",
            'starter' => <<<'JS'
___ function getUserName(loadUser) {
  try {
    const user = ___ loadUser();
    return user.name;
  } ___ (error) {
    return 'гость';
  }
}

getUserName(() => Promise.resolve({ name: 'Аня' })).then((name) => console.log(name));
JS,
            'tests' => <<<'JS'
test('возвращает промис', () => assert(getUserName(() => Promise.resolve({ name: 'x' })) instanceof Promise, 'async-функция должна возвращать промис'));
test('имя пользователя', async () => assertEqual(await getUserName(() => Promise.resolve({ name: 'Аня' })), 'Аня'));
test('при ошибке загрузки — «гость»', async () => assertEqual(await getUserName(() => Promise.reject(new Error('нет сети'))), 'гость'));
test('дожидается медленной загрузки', async () => {
  const slow = () => new Promise((resolve) => setTimeout(() => resolve({ name: 'Борис' }), 40));
  assertEqual(await getUserName(slow), 'Борис');
});
JS,
            'hints' => [
                'Слова по порядку: `async`, `await`, `catch`.',
            ],
            'solution' => <<<'JS'
async function getUserName(loadUser) {
  try {
    const user = await loadUser();
    return user.name;
  } catch (error) {
    return 'гость';
  }
}
JS,
        ],
        [
            'id' => 'foreach-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'forEach не ждёт',
            'text' => "Функция `saveAll(items, save)` сохраняет элементы по одному и должна вернуть массив того, что вернул `save` для каждого. Сейчас она возвращает пустой массив: посмотри на вывод.\n\nСохранять нужно **по очереди**, в исходном порядке.",
            'starter' => <<<'JS'
async function saveAll(items, save) {
  const saved = [];

  items.forEach(async (item) => {
    saved.push(await save(item));
  });

  return saved;
}

const fakeSave = (item) => new Promise((resolve) => setTimeout(() => resolve(`сохранено: ${item}`), 20));

saveAll(['а', 'б'], fakeSave).then((result) => console.log(result));
JS,
            'tests' => <<<'JS'
const save = (item) => new Promise((resolve) => setTimeout(() => resolve(item.toUpperCase()), 15));
test('возвращает результаты сохранения', async () => assertEqual(await saveAll(['а', 'б', 'в'], save), ['А', 'Б', 'В']));
test('порядок сохраняется, даже если первый элемент самый медленный', async () => {
  const uneven = (ms) => new Promise((resolve) => setTimeout(() => resolve(ms), ms));
  assertEqual(await saveAll([40, 5, 20], uneven), [40, 5, 20]);
});
test('сохранение идёт по очереди, а не одновременно', async () => {
  let active = 0;
  let maxActive = 0;
  const tracked = async (item) => {
    active++;
    maxActive = Math.max(maxActive, active);
    await new Promise((resolve) => setTimeout(resolve, 15));
    active--;
    return item;
  };
  await saveAll([1, 2, 3], tracked);
  assertEqual(maxActive, 1, 'Одновременно выполнялось несколько сохранений');
});
test('пустой массив', async () => assertEqual(await saveAll([], save), []));
JS,
            'hints' => [
                '`forEach` запускает три асинхронные функции и сразу идёт дальше — `return saved` срабатывает, пока массив ещё пуст.',
                'Замени на `for (const item of items) { saved.push(await save(item)); }`. В `for...of` `await` действительно приостанавливает цикл.',
            ],
            'solution' => <<<'JS'
async function saveAll(items, save) {
  const saved = [];

  for (const item of items) {
    saved.push(await save(item));
  }

  return saved;
}
JS,
        ],
        [
            'id' => 'parallel',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Ускорить загрузку',
            'text' => "Функция `loadPrices(ids, loadPrice)` получает цены нескольких товаров. Сейчас она ждёт каждый запрос по очереди: четыре товара по 50 мс — это 200 мс.\n\nПерепиши так, чтобы запросы шли одновременно. Порядок цен в результате должен совпадать с порядком `ids`.",
            'starter' => <<<'JS'
async function loadPrices(ids, loadPrice) {
  const prices = [];

  for (const id of ids) {
    prices.push(await loadPrice(id));
  }

  return prices;
}

const fakePrice = (id) => new Promise((resolve) => setTimeout(() => resolve(id * 100), 50));
const start = Date.now();
loadPrices([1, 2, 3, 4], fakePrice).then((prices) => console.log(prices, `${Date.now() - start} мс`));
JS,
            'tests' => <<<'JS'
const price = (id) => new Promise((resolve) => setTimeout(() => resolve(id * 100), 50));
test('цены в порядке ids', async () => assertEqual(await loadPrices([3, 1, 2], price), [300, 100, 200]));
test('порядок не зависит от скорости ответов', async () => {
  const uneven = (ms) => new Promise((resolve) => setTimeout(() => resolve(ms), ms));
  assertEqual(await loadPrices([60, 5, 30], uneven), [60, 5, 30]);
});
test('четыре запроса по 50 мс укладываются в 130 мс', async () => {
  const start = Date.now();
  await loadPrices([1, 2, 3, 4], price);
  const elapsed = Date.now() - start;
  assert(elapsed < 130, `Заняло ${elapsed} мс — запросы идут по очереди`);
});
test('пустой список', async () => assertEqual(await loadPrices([], price), []));
JS,
            'hints' => [
                '`ids.map((id) => loadPrice(id))` запускает все запросы сразу и даёт массив промисов.',
                '`Promise.all` ждёт их все и сохраняет порядок: `return Promise.all(ids.map((id) => loadPrice(id)));`',
            ],
            'solution' => <<<'JS'
async function loadPrices(ids, loadPrice) {
  return Promise.all(ids.map((id) => loadPrice(id)));
}
JS,
        ],
        [
            'id' => 'retry-plan',
            'type' => 'plan',
            'title' => 'Повтор попытки: план',
            'text' => "Сеть ненадёжна: запрос может не пройти с первого раза и пройти со второго.\n\nЗадача: функция `retry(action, attempts, delayMs)`.\n\n- `action` — функция без аргументов, возвращает промис.\n- Если `action` выполнилась — вернуть её результат.\n- Если отклонилась — подождать `delayMs` миллисекунд и попробовать снова.\n- Всего не больше `attempts` попыток. Если все неудачны — отклониться с ошибкой **последней** попытки.\n- После последней неудачной попытки ждать уже не нужно.\n\nЗапиши шаги.",
            'reference' => <<<'MD'
1. Завести переменную для последней ошибки.
2. Повторять от первой попытки до `attempts`:
3. Вызвать `action` и дождаться результата. Получилось — сразу вернуть его.
4. Не получилось — запомнить ошибку.
5. Если эта попытка не последняя — подождать `delayMs`.
6. Если цикл закончился, а результата нет, — бросить запомненную ошибку.

С `async/await` это обычный цикл `for` с `try/catch` внутри: `return` в `try` выходит из функции при первом успехе, `catch` запоминает ошибку и даёт циклу пойти на следующий круг.

На цепочках `then` то же самое пришлось бы писать рекурсией — хороший пример того, зачем нужен `await`.
MD,
        ],
        [
            'id' => 'retry',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Повтор попытки: код',
            'text' => "Напиши `retry(action, attempts, delayMs)` по своему плану. Для паузы пригодится функция `delay` — она уже есть в заготовке.",
            'starter' => <<<'JS'
const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

JS,
            'tests' => <<<'JS'
// действие, которое отклоняется failures раз подряд, а потом выполняется
const flaky = (failures) => {
  let calls = 0;
  const action = () => {
    calls++;
    return calls <= failures ? Promise.reject(new Error(`сбой ${calls}`)) : Promise.resolve(`успех с попытки ${calls}`);
  };
  action.calls = () => calls;
  return action;
};
const outcome = async (promise) => { try { return await promise; } catch (error) { return 'ошибка: ' + error.message; } };

test('успех с первой попытки — без повторов', async () => {
  const action = flaky(0);
  assertEqual([await retry(action, 3, 10), action.calls()], ['успех с попытки 1', 1]);
});
test('успех с третьей попытки', async () => {
  const action = flaky(2);
  assertEqual([await retry(action, 3, 10), action.calls()], ['успех с попытки 3', 3]);
});
test('все попытки неудачны — ошибка последней', async () => {
  const action = flaky(10);
  assertEqual([await outcome(retry(action, 3, 10)), action.calls()], ['ошибка: сбой 3', 3]);
});
test('между попытками есть пауза', async () => {
  const start = Date.now();
  await retry(flaky(2), 3, 40);
  const elapsed = Date.now() - start;
  assert(elapsed >= 70, `Две паузы по 40 мс заняли всего ${elapsed} мс`);
});
test('после последней попытки паузы нет', async () => {
  const start = Date.now();
  await outcome(retry(flaky(10), 2, 60));
  const elapsed = Date.now() - start;
  assert(elapsed < 110, `Заняло ${elapsed} мс — похоже, была лишняя пауза в конце`);
});
test('одна попытка', async () => assertEqual(await outcome(retry(flaky(1), 1, 10)), 'ошибка: сбой 1'));
JS,
            'hints' => [
                'Каркас: `async function retry(action, attempts, delayMs) { let lastError; for (let i = 1; i <= attempts; i++) { … } throw lastError; }`',
                'Внутри цикла: `try { return await action(); } catch (error) { lastError = error; }`',
                'Пауза — после `catch`, но только если попытка не последняя: `if (i < attempts) await delay(delayMs);`',
            ],
            'solution' => <<<'JS'
const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function retry(action, attempts, delayMs) {
  let lastError;

  for (let i = 1; i <= attempts; i++) {
    try {
      return await action();
    } catch (error) {
      lastError = error;
    }

    if (i < attempts) {
      await delay(delayMs);
    }
  }

  throw lastError;
}
JS,
        ],
    ],
    'cards' => [
        ['Что делает `async` перед функцией и `await` перед промисом?', '`async` — функция всегда возвращает промис. `await` — приостановить эту функцию до завершения промиса и подставить его значение; остальная программа не ждёт.'],
        ['Как узнать, что забыт `await`?', 'В переменной лежит промис: `undefined` там, где ждал данные, или `[object Promise]` на странице. Ошибки при этом нет.'],
        ['Как запустить независимые запросы одновременно?', '`const [a, b] = await Promise.all([loadA(), loadB()]);` Для списка — `await Promise.all(ids.map((id) => load(id)))`.'],
        ['Почему нельзя `await` внутри `forEach`?', '`forEach` не ждёт: вызывает функции и идёт дальше. Нужен `for...of` (по очереди) или `Promise.all` с `map` (параллельно).'],
        ['Как обрабатываются ошибки с `await`?', 'Обычным `try/catch`: отклонённый промис под `await` превращается в исключение.'],
    ],
];
