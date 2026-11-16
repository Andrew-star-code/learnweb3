<?php
return [
    'tasks' => [
        [
            'id' => 'ok-quiz',
            'type' => 'quiz',
            'title' => 'Когда fetch считает, что всё плохо',
            'text' => "```js\ntry {\n  const response = await fetch('/api/demo/products/999');   // сервер ответит 404\n  console.log('A');\n} catch (error) {\n  console.log('B');\n}\n```\n\nЧто будет выведено?",
            'options' => ['A', 'B', 'Сначала A, потом B', 'Ничего'],
            'answer' => 0,
            'explain' => 'Ответ `404` — это полученный ответ, и `fetch` считает запрос успешным. В `catch` попадают только сбои сети. Код состояния нужно проверять самому: `if (!response.ok) throw new Error(…)`.',
        ],
        [
            'id' => 'load-list',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Первый запрос',
            'text' => 'Допиши функцию `loadProducts`: она запрашивает товары у учебного API и выводит их списком в виде `Эфиопия Иргачеффе — 890 ₽`.',
            'starter' => <<<'HTML'
<h2>Каталог</h2>
<ul id="products"></ul>

<script>
  async function loadProducts() {
    const response = await ___('/api/demo/products');
    const products = await response.___();

    const list = document.querySelector('#products');
    list.replaceChildren();

    for (const product of products) {
      const li = document.createElement('li');
      li.textContent = `${product.name} — ${product.___} ₽`;
      list.append(li);
    }
  }

  loadProducts();
</script>
HTML,
            'tests' => <<<'JS'
test('в списке появились все восемь товаров', async () => {
  await until(() => $$('#products li').length === 8, 'Товары не появились на странице');
});
test('первый товар выведен с ценой', async () => {
  await until(() => $$('#products li').length > 0);
  assertEqual($('#products li').textContent, 'Эфиопия Иргачеффе — 890 ₽');
});
test('последний товар', async () => {
  await until(() => $$('#products li').length === 8);
  assertEqual($$('#products li')[7].textContent, 'Колумбия Декаф — 790 ₽');
});
JS,
            'hints' => [
                'Функция запроса — `fetch`. Тело ответа разбирает метод `json()`.',
                'Цена лежит в поле `price`.',
            ],
            'solution' => <<<'HTML'
<h2>Каталог</h2>
<ul id="products"></ul>

<script>
  async function loadProducts() {
    const response = await fetch('/api/demo/products');
    const products = await response.json();

    const list = document.querySelector('#products');
    list.replaceChildren();

    for (const product of products) {
      const li = document.createElement('li');
      li.textContent = `${product.name} — ${product.price} ₽`;
      list.append(li);
    }
  }

  loadProducts();
</script>
HTML,
        ],
        [
            'id' => 'errors-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Сервер ответил ошибкой',
            'text' => "Функция `load(url)` загружает товары и рисует список. Пока сервер отвечает нормально, всё работает. Но адрес во втором вызове заставляет сервер вернуть ошибку `500` — и скрипт падает (смотри «Консоль»), а человек видит пустую страницу без объяснений.\n\nИсправь `load` так, чтобы она никогда не бросала ошибку наружу:\n\n- если сервер ответил ошибкой или запрос не дошёл — список очищается, а в `#error` появляется текст `Не удалось загрузить товары` (элемент становится видимым);\n- при успешной загрузке `#error` скрыт.",
            'starter' => <<<'HTML'
<p id="error" hidden style="color: rgb(179, 55, 44)"></p>
<ul id="products"></ul>

<script>
  const list = document.querySelector('#products');
  const errorBox = document.querySelector('#error');

  function render(products) {
    list.replaceChildren();
    for (const product of products) {
      const li = document.createElement('li');
      li.textContent = product.name;
      list.append(li);
    }
  }

  async function load(url) {
    const response = await fetch(url);
    const products = await response.json();
    render(products);
  }

  load('/api/demo/products?fail=1');
</script>
HTML,
            'tests' => <<<'JS'
const state = () => [$('#error').hidden, text('#error'), $$('#products li').length];
test('ошибка сервера: сообщение показано, список пуст', async () => {
  await load('/api/demo/products?fail=1');
  assertEqual(state(), [false, 'Не удалось загрузить товары', 0]);
});
test('успешная загрузка: сообщение скрыто, товары на месте', async () => {
  await load('/api/demo/products');
  assertEqual([$('#error').hidden, $$('#products li').length], [true, 8]);
});
test('код 404 — тоже ошибка', async () => {
  await load('/api/demo/products?fail=404');
  assertEqual(state(), [false, 'Не удалось загрузить товары', 0]);
});
test('сбой сети: запрос не дошёл', async () => {
  await load('/api/demo/products');
  await load('http://localhost:9/недоступный-сервер');
  assertEqual(state(), [false, 'Не удалось загрузить товары', 0]);
});
JS,
            'hints' => [
                'После `fetch` проверь ответ: `` if (!response.ok) { throw new Error(`Сервер ответил ${response.status}`); } ``',
                'Оберни тело функции в `try/catch`. В `catch` окажутся и твоя ошибка, и сбой сети.',
                'В `try` после отрисовки: `errorBox.hidden = true;`. В `catch`: очистить список, записать текст, `errorBox.hidden = false;`',
            ],
            'solution' => <<<'HTML'
<p id="error" hidden style="color: rgb(179, 55, 44)"></p>
<ul id="products"></ul>

<script>
  const list = document.querySelector('#products');
  const errorBox = document.querySelector('#error');

  function render(products) {
    list.replaceChildren();
    for (const product of products) {
      const li = document.createElement('li');
      li.textContent = product.name;
      list.append(li);
    }
  }

  async function load(url) {
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`Сервер ответил ${response.status}`);
      }

      render(await response.json());
      errorBox.hidden = true;
    } catch (error) {
      list.replaceChildren();
      errorBox.textContent = 'Не удалось загрузить товары';
      errorBox.hidden = false;
    }
  }

  load('/api/demo/products?fail=1');
</script>
HTML,
        ],
        [
            'id' => 'loading-state',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Состояние загрузки',
            'text' => "Сервер в этом задании отвечает с задержкой в полсекунды. Сейчас после нажатия кнопки ничего не происходит, и её хочется нажать ещё раз.\n\nДопиши обработчик:\n\n- сразу после нажатия в `#status` появляется `Загрузка…`, а кнопка отключается;\n- после успешного ответа товары выводятся в список, `#status` очищается;\n- при ошибке в `#status` пишется `Ошибка загрузки`;\n- кнопка включается обратно при любом исходе.\n\nФункции `loadProducts` и `render` уже написаны.",
            'starter' => <<<'HTML'
<button type="button" id="load">Показать каталог</button>
<p id="status"></p>
<ul id="products"></ul>

<script>
  const button = document.querySelector('#load');
  const status = document.querySelector('#status');
  const list = document.querySelector('#products');

  async function loadProducts() {
    const response = await fetch('/api/demo/products?delay=500');
    if (!response.ok) {
      throw new Error(`Сервер ответил ${response.status}`);
    }
    return response.json();
  }

  function render(products) {
    list.replaceChildren();
    for (const product of products) {
      const li = document.createElement('li');
      li.textContent = product.name;
      list.append(li);
    }
  }

  button.addEventListener('click', async () => {

  });
</script>
HTML,
            'tests' => <<<'JS'
const state = () => [text('#status'), $('#load').disabled];
test('сразу после нажатия: «Загрузка…», кнопка отключена', async () => {
  await fetch('/api/demo/reset', { method: 'POST' });
  click('#load');
  assertEqual(state(), ['Загрузка…', true]);
});
test('после ответа: товары показаны, статус пуст, кнопка включена', async () => {
  await until(() => $$('#products li').length === 8, 'Товары не появились');
  assertEqual(state(), ['', false]);
});
test('при ошибке: «Ошибка загрузки», кнопка снова включена', async () => {
  await fetch('/api/demo/fail-next', { method: 'POST' });
  click('#load');
  assertEqual(state(), ['Загрузка…', true]);
  await until(() => text('#status') === 'Ошибка загрузки', 'Сообщение об ошибке не появилось');
  assertEqual($('#load').disabled, false, 'После ошибки кнопка осталась отключённой');
});
test('после ошибки можно загрузить снова', async () => {
  click('#load');
  await until(() => text('#status') === '' && $$('#products li').length === 8, 'Повторная загрузка не сработала');
});
JS,
            'hints' => [
                'До запроса: `status.textContent = \'Загрузка…\'; button.disabled = true;`',
                'Запрос и отрисовка — в `try`, сообщение об ошибке — в `catch`.',
                'Включение кнопки — в `finally`: он выполнится и после успеха, и после ошибки.',
            ],
            'solution' => <<<'HTML'
<button type="button" id="load">Показать каталог</button>
<p id="status"></p>
<ul id="products"></ul>

<script>
  const button = document.querySelector('#load');
  const status = document.querySelector('#status');
  const list = document.querySelector('#products');

  async function loadProducts() {
    const response = await fetch('/api/demo/products?delay=500');
    if (!response.ok) {
      throw new Error(`Сервер ответил ${response.status}`);
    }
    return response.json();
  }

  function render(products) {
    list.replaceChildren();
    for (const product of products) {
      const li = document.createElement('li');
      li.textContent = product.name;
      list.append(li);
    }
  }

  button.addEventListener('click', async () => {
    status.textContent = 'Загрузка…';
    button.disabled = true;

    try {
      render(await loadProducts());
      status.textContent = '';
    } catch (error) {
      status.textContent = 'Ошибка загрузки';
    } finally {
      button.disabled = false;
    }
  });
</script>
HTML,
        ],
        [
            'id' => 'post',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Отправить данные',
            'text' => "Допиши обработчик формы. При отправке он:\n\n- отправляет на `/api/demo/todos` запрос `POST` с телом в формате JSON: `{ \"text\": \"…\" }`;\n- получает в ответ созданную запись с полями `id` и `text`;\n- добавляет в список `#list` пункт с текстом из ответа и атрибутом `data-id`, равным `id` из ответа;\n- очищает поле.\n\nЕсли текст пустой или состоит из пробелов, запрос не отправляется.",
            'starter' => <<<'HTML'
<form id="new">
  <input type="text" id="text" aria-label="Новое дело">
  <button type="submit">Добавить</button>
</form>
<ul id="list"></ul>

<script>
  const form = document.querySelector('#new');
  const input = document.querySelector('#text');
  const list = document.querySelector('#list');

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

  });
</script>
HTML,
            'tests' => <<<'JS'
let calls = [];
const realFetch = window.fetch;
window.fetch = (url, options) => { calls.push({ url: String(url), options: options || {} }); return realFetch(url, options); };

test('дело появляется в списке с id от сервера', async () => {
  await realFetch('/api/demo/reset', { method: 'POST' });
  type('#text', 'Купить фильтры');
  submit('#new');
  await until(() => $$('#list li').length === 1, 'Пункт не появился в списке');
  assertEqual([$('#list li').textContent, $('#list li').dataset.id], ['Купить фильтры', '4']);
});
test('запрос — POST с заголовком и телом в JSON', () => {
  const request = calls[0];
  assertEqual((request.options.method || 'GET').toUpperCase(), 'POST');
  assertEqual(new Headers(request.options.headers).get('Content-Type'), 'application/json');
  assertEqual(JSON.parse(request.options.body), { text: 'Купить фильтры' });
});
test('дело действительно сохранено на сервере', async () => {
  const todos = await (await realFetch('/api/demo/todos')).json();
  assertEqual(todos[todos.length - 1], { id: 4, text: 'Купить фильтры', done: false });
});
test('поле очищено', () => assertEqual($('#text').value, ''));
test('пустой текст не отправляется', async () => {
  calls = [];
  type('#text', '   ');
  submit('#new');
  await wait(150);
  assertEqual([calls.length, $$('#list li').length], [0, 1]);
});
test('второе дело добавляется следом', async () => {
  type('#text', 'Позвонить поставщику');
  submit('#new');
  await until(() => $$('#list li').length === 2, 'Второй пункт не появился');
  assertEqual($$('#list li').map((li) => li.dataset.id), ['4', '5']);
});
JS,
            'hints' => [
                'Сначала проверка: `const text = input.value.trim(); if (text === \'\') return;`',
                'Запрос: `fetch(\'/api/demo/todos\', { method: \'POST\', headers: { \'Content-Type\': \'application/json\' }, body: JSON.stringify({ text }) })`.',
                'Ответ: `const todo = await response.json();` Дальше — `li.textContent = todo.text; li.dataset.id = todo.id;`',
            ],
            'solution' => <<<'HTML'
<form id="new">
  <input type="text" id="text" aria-label="Новое дело">
  <button type="submit">Добавить</button>
</form>
<ul id="list"></ul>

<script>
  const form = document.querySelector('#new');
  const input = document.querySelector('#text');
  const list = document.querySelector('#list');

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const text = input.value.trim();
    if (text === '') return;

    const response = await fetch('/api/demo/todos', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ text }),
    });
    const todo = await response.json();

    const li = document.createElement('li');
    li.textContent = todo.text;
    li.dataset.id = todo.id;
    list.append(li);

    input.value = '';
  });
</script>
HTML,
        ],
        [
            'id' => 'search-plan',
            'type' => 'plan',
            'title' => 'Поиск с задержкой: план',
            'text' => "Задача: поле поиска по каталогу. Пока человек печатает, запросы на сервер не уходят. Когда он остановился на 300 мс — уходит один запрос с тем, что введено, и результаты выводятся списком.\n\nПри пустом поле список очищается, а запрос не отправляется.\n\nЗапиши шаги обработчика события `input`. Что должно происходить при каждом нажатии клавиши?",
            'reference' => <<<'MD'
**При каждом изменении поля:**

1. Отменить отложенный запуск, оставшийся от прошлого нажатия.
2. Запланировать новый запуск через 300 мс и запомнить его идентификатор.

**Когда отложенный запуск всё-таки сработал:**

3. Взять текущее значение поля без пробелов по краям.
4. Если оно пустое — очистить список и выйти.
5. Отправить запрос с этим значением в параметре `q`.
6. Вывести полученные товары.

Идентификатор таймера хранится в переменной **снаружи** обработчика: каждое следующее нажатие должно видеть таймер предыдущего, чтобы его отменить. Это замыкание в деле.

Значение поля читается в момент срабатывания таймера, а не в момент нажатия — так в запрос попадает то, что введено к концу паузы.

Параметр нужно закодировать: в запросе может быть кириллица и пробелы. `URLSearchParams` делает это сам.
MD,
        ],
        [
            'id' => 'search',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Поиск с задержкой: код',
            'text' => "Напиши скрипт поиска по своему плану. Адрес запроса: `/api/demo/products?q=…`. Названия найденных товаров выводятся пунктами списка `#results`.",
            'starter' => <<<'HTML'
<p><label for="search">Поиск по каталогу</label> <input type="search" id="search"></p>
<ul id="results"></ul>

<script>

</script>
HTML,
            'tests' => <<<'JS'
let calls = [];
const realFetch = window.fetch;
window.fetch = (url, options) => { calls.push(String(url)); return realFetch(url, options); };
const names = () => $$('#results li').map((li) => li.textContent);

test('пока человек печатает, запросы не уходят', async () => {
  type('#search', 'э');
  await wait(80);
  type('#search', 'эф');
  await wait(80);
  type('#search', 'эфи');
  await wait(120);
  assertEqual(calls.length, 0, 'Запрос ушёл раньше, чем закончилась пауза');
});
test('после паузы уходит один запрос с последним значением', async () => {
  await until(() => names().length === 2, 'Результаты не появились');
  assertEqual(calls.length, 1, 'Запросов должно быть ровно один');
  assert(decodeURIComponent(calls[0]).includes('q=эфи'), `Запрос ушёл по адресу ${decodeURIComponent(calls[0])}`);
});
test('результаты выведены', () => assertEqual(names(), ['Эфиопия Иргачеффе', 'Эфиопия Сидамо']));
test('новый запрос заменяет результаты', async () => {
  type('#search', 'колумбия');
  await until(() => names()[0] === 'Колумбия Супремо', 'Список не обновился');
  assertEqual(names(), ['Колумбия Супремо', 'Колумбия Декаф']);
});
test('пустое поле очищает список без запроса', async () => {
  calls = [];
  type('#search', '');
  await wait(450);
  assertEqual([names(), calls.length], [[], 0]);
});
test('запрос из пробелов тоже не отправляется', async () => {
  type('#search', '   ');
  await wait(450);
  assertEqual(calls.length, 0);
});
JS,
            'hints' => [
                'Снаружи обработчика: `let timer;`. Внутри: `clearTimeout(timer); timer = setTimeout(runSearch, 300);`',
                'Адрес: `` `/api/demo/products?${new URLSearchParams({ q: query })}` ``.',
                'Отрисовка — тот же приём, что и раньше: очистить список, создать `li` на каждый товар.',
            ],
            'solution' => <<<'HTML'
<p><label for="search">Поиск по каталогу</label> <input type="search" id="search"></p>
<ul id="results"></ul>

<script>
  const input = document.querySelector('#search');
  const results = document.querySelector('#results');
  let timer;

  async function runSearch() {
    const query = input.value.trim();

    if (query === '') {
      results.replaceChildren();
      return;
    }

    const response = await fetch(`/api/demo/products?${new URLSearchParams({ q: query })}`);
    const products = await response.json();

    results.replaceChildren();
    for (const product of products) {
      const li = document.createElement('li');
      li.textContent = product.name;
      results.append(li);
    }
  }

  input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(runSearch, 300);
  });
</script>
HTML,
        ],
    ],
    'cards' => [
        ['Почему после `fetch` нужно два `await`?', 'Первый ждёт заголовки ответа и даёт объект `response`. Второй (`response.json()`) дочитывает и разбирает тело.'],
        ['Отклоняет ли `fetch` промис при ответе `404` или `500`?', 'Нет, только при сбое сети. Код состояния проверяют сами: `if (!response.ok) throw new Error(…)`.'],
        ['Какие три вещи нужны, чтобы отправить JSON через `fetch`?', '`method: \'POST\'`, заголовок `Content-Type: application/json` и `body: JSON.stringify(данные)`.'],
        ['Какие состояния есть у блока, который загружает данные?', 'Загрузка, ошибка, готово (включая «пусто»). Показывать нужно каждое; кнопку возвращать в `finally`.'],
        ['Как устроена задержка ввода (debounce)?', 'При каждом событии — `clearTimeout(timer)` и новый `setTimeout`. До выполнения доживает только последний запуск.'],
    ],
];
