# fetch и JSON

`fetch` — встроенная функция браузера для запросов к серверу. Она возвращает промис, поэтому всё из двух прошлых уроков применяется напрямую.

## Простой запрос

```js
async function loadProducts() {
  const response = await fetch('/api/demo/products');
  const products = await response.json();
  return products;
}
```

Здесь **два `await`**, и оба нужны:

1. `fetch` выполняется, когда пришли заголовки ответа. Получаем объект `response`.
2. `response.json()` дочитывает тело и разбирает его. Это тоже промис.

## Ошибка сервера — не ошибка fetch

Это главная ловушка. `fetch` отклоняет промис **только при сбое сети**: нет соединения, сервер недоступен. Ответ с кодом `404` или `500` для него — успешно полученный ответ.

Поэтому код состояния проверяют вручную:

```js
async function loadProducts() {
  const response = await fetch('/api/demo/products');

  if (!response.ok) {                                    // ok — это коды 200–299
    throw new Error(`Сервер ответил ${response.status}`);
  }

  return response.json();
}
```

Без этой проверки код попытается разобрать страницу ошибки как данные и упадёт позже, в непонятном месте.

Полезные свойства ответа: `response.ok`, `response.status`, `response.headers`. Методы чтения тела: `json()`, `text()`, `blob()`.

## Отправка данных

```js
const response = await fetch('/api/demo/todos', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ text: 'Заказать зёрна' }),
});

const created = await response.json();       // сервер вернул созданную запись с id
```

Три вещи, которые забывают по отдельности:

- `method` — без него уйдёт `GET`;
- заголовок `Content-Type` — без него сервер не поймёт, что в теле JSON;
- `JSON.stringify` — объект, переданный в `body` как есть, превратится в строку `[object Object]`.

Удаление: `fetch('/api/demo/todos/5', { method: 'DELETE' })`.

Параметры адреса собирай через `URLSearchParams` — он сам закодирует пробелы и кириллицу:

```js
const params = new URLSearchParams({ q: 'кофе', page: 2 });
fetch(`/api/demo/products?${params}`);       // ?q=%D0%BA%D0%BE%D1%84%D0%B5&page=2
```

## Три состояния интерфейса

Запрос занимает время и может не удаться. У любого блока, который загружает данные, есть минимум три состояния, и показывать нужно каждое:

| Состояние | Что видит человек |
|---|---|
| Загрузка | Индикатор, кнопка отключена |
| Ошибка | Понятное сообщение и возможность повторить |
| Готово | Данные — или сообщение «пока пусто» |

```js
async function showProducts() {
  status.textContent = 'Загрузка…';
  button.disabled = true;

  try {
    const products = await loadProducts();
    render(products);
    status.textContent = '';
  } catch (error) {
    status.textContent = 'Не удалось загрузить. Попробуйте ещё раз.';
  } finally {
    button.disabled = false;      // в любом случае вернуть кнопку
  }
}
```

`finally` здесь не украшение. Если включать кнопку только после успеха, первая же ошибка оставит её отключённой навсегда.

Отключение кнопки на время запроса заодно защищает от двойной отправки.

## Не чаще, чем нужно: задержка ввода

Поиск «на лету» не должен отправлять запрос на каждую букву. Приём называется debounce: действие откладывается и выполняется, только когда ввод остановился.

```js
let timer;

input.addEventListener('input', () => {
  clearTimeout(timer);                       // отменить прошлый отложенный запуск
  timer = setTimeout(() => {
    search(input.value);                     // запустить, если 300 мс не было ввода
  }, 300);
});
```

Каждое нажатие отменяет предыдущий таймер и ставит новый. До запроса доживает только последний.

## Учебное API

В заданиях используется API, встроенное в платформу.

| Запрос | Что делает |
|---|---|
| `GET /api/demo/products` | Список товаров. Параметр `q` — поиск по названию |
| `GET /api/demo/products/4` | Один товар; `404`, если такого нет |
| `GET /api/demo/todos` | Список дел |
| `POST /api/demo/todos` | Создать дело. Тело — JSON `{ "text": "…" }`. Ответ `201` с созданной записью |
| `DELETE /api/demo/todos/5` | Удалить дело. Ответ `204` без тела |

К любому адресу можно добавить `?delay=1000`, чтобы ответ пришёл через секунду, и `?fail=1`, чтобы сервер вернул ошибку `500`. Так удобно проверять состояния загрузки и ошибки.

> Запросы из предпросмотра видны и в настоящих инструментах разработчика: открой вкладку Network (F12) и посмотри, что уходит и что возвращается.

## Глубже

- [Fetch](https://learn.javascript.ru/fetch) — learn.javascript.ru
- [Использование Fetch](https://developer.mozilla.org/ru/docs/Web/API/Fetch_API/Using_Fetch) — MDN
- [URLSearchParams](https://learn.javascript.ru/url) — learn.javascript.ru
