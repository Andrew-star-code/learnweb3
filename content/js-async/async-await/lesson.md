# async/await

`async` и `await` — это другая запись для промисов. Под ней те же промисы, но код читается сверху вниз, как обычный.

```js
// Цепочка
function countOrders(userId) {
  return loadUser(userId)
    .then((user) => loadOrders(user.id))
    .then((orders) => orders.length);
}

// То же через async/await
async function countOrders(userId) {
  const user = await loadUser(userId);
  const orders = await loadOrders(user.id);
  return orders.length;
}
```

## Два слова

**`async`** перед функцией означает: она всегда возвращает промис. То, что стоит после `return`, становится его значением. Брошенная ошибка отклоняет его.

**`await`** перед промисом означает: приостановить **эту функцию**, пока промис не завершится, и подставить его значение. Остальная программа при этом не ждёт — страница остаётся живой.

`await` работает только внутри `async`-функции (и на верхнем уровне модуля).

## Ошибки — обычным try/catch

```js
async function showUser(id) {
  try {
    const user = await loadUser(id);
    render(user);
  } catch (error) {
    showError('Не удалось загрузить пользователя');
  } finally {
    hideSpinner();
  }
}
```

Отклонённый промис под `await` превращается в исключение. Все правила из урока про ошибки действуют: перехватывай там, где знаешь, что делать.

## Три типичные ошибки

### Забытый await

```js
async function getName(id) {
  const user = loadUser(id);      // await забыт: в user лежит промис
  return user.name;               // undefined
}
```

Ошибки нет, просто в переменной не то. Признаки: `undefined` там, где ждал данные, или `[object Promise]` на странице.

### Последовательно вместо параллельно

```js
// Медленно: второй запрос ждёт первого, хотя не зависит от него
const user = await loadUser(id);
const settings = await loadSettings(id);

// Быстро: оба запроса идут одновременно
const [user, settings] = await Promise.all([loadUser(id), loadSettings(id)]);
```

Ждать по очереди нужно, только если следующий шаг использует результат предыдущего.

Для списка — `map` плюс `Promise.all`:

```js
const prices = await Promise.all(ids.map((id) => loadPrice(id)));
```

`map` запускает все запросы сразу и возвращает массив промисов, `Promise.all` ждёт их и сохраняет порядок.

### await внутри forEach

```js
async function saveAll(items) {
  items.forEach(async (item) => {
    await save(item);
  });
  console.log('готово');          // выполнится ДО сохранения
}
```

`forEach` не умеет ждать: он вызывает функции и сразу идёт дальше. Нужен либо `for...of` (по очереди), либо `Promise.all` с `map` (параллельно):

```js
for (const item of items) {
  await save(item);
}
```

## По очереди или параллельно

| Ситуация | Как писать |
|---|---|
| Шаги зависят друг от друга | `await` один за другим |
| Независимые запросы | `Promise.all` |
| Много запросов, сервер просит не частить | `for...of` с `await` |
| Нужны все результаты, даже если часть упала | `Promise.allSettled` |

## Повтор с задержкой

Приём, который встречается в любой работе с сетью: если не получилось — подождать и попробовать ещё раз.

```js
const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function waitAndLoad() {
  await delay(500);               // пауза без блокировки страницы
  return loadData();
}
```

## Глубже

- [Async/await](https://learn.javascript.ru/async-await) — learn.javascript.ru
- [async function](https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Statements/async_function) — MDN
