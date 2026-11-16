<?php
$markup = <<<'HTML'
<style>
  body { font-family: sans-serif; max-width: 420px; margin: 0 auto; padding: 16px; }
  #new-todo { display: flex; gap: 8px; }
  #todo-text { flex: 1; padding: 8px; font: inherit; }
  button { font: inherit; cursor: pointer; }
  button:disabled { opacity: 0.5; cursor: default; }
  #status { color: rgb(100, 100, 100); min-height: 1.4em; margin: 8px 0; }
  #error { color: rgb(179, 55, 44); margin: 8px 0; }
  #todos { list-style: none; padding: 0; }
  .todo { display: flex; align-items: center; gap: 8px; padding: 8px 0; border-bottom: 1px solid rgb(230, 230, 230); }
  .todo .text { flex: 1; }
  .delete { border: 0; background: none; font-size: 20px; color: rgb(179, 55, 44); }
  #empty { color: rgb(100, 100, 100); }
</style>

<h1>Список дел</h1>

<form id="new-todo">
  <input type="text" id="todo-text" placeholder="Что нужно сделать?" aria-label="Новое дело">
  <button type="submit">Добавить</button>
</form>

<p id="status"></p>
<p id="error" hidden></p>
<ul id="todos"></ul>
<p id="empty" hidden>Дел пока нет.</p>

HTML;

return [
    'tasks' => [
        [
            'id' => 'plan',
            'type' => 'plan',
            'title' => 'План приложения',
            'text' => "Перечитай требования и запиши по шагам, что происходит при добавлении дела — от нажатия кнопки до появления пункта в списке. Отдельно отметь, что должно случиться в каждой точке, если запрос не удался.\n\nЧем этот порядок отличается от добавления в прошлом проекте, где данные лежали в `localStorage`?",
            'reference' => <<<'MD'
**Добавление по шагам:**

1. Отменить обычную отправку формы.
2. Взять текст без пробелов по краям. Пустой — выйти, ничего не отправляя.
3. Отключить кнопку.
4. Отправить `POST` с текстом и дождаться ответа.
5. Если сервер ответил ошибкой — считать это неудачей.
6. Добавить в состояние запись **из ответа сервера** — с настоящим `id`.
7. Очистить поле, скрыть сообщение об ошибке, перерисовать список.
8. При неудаче на шагах 4–5: показать сообщение об ошибке. Состояние и поле не трогать.
9. В любом случае включить кнопку обратно.

Шаги 4–7 — блок `try`, шаг 8 — `catch`, шаг 9 — `finally`.

**Отличия от прошлого проекта:**

- **Изменение состояния идёт после ответа, а не до него.** В `localStorage` запись не могла не удаться, поэтому массив меняли сразу. Здесь сначала спрашиваем сервер и только при успехе меняем свою копию.
- **Идентификатор выдаёт сервер.** Раньше дело опознавалось по номеру в массиве. Теперь у каждого есть `id`, и именно он нужен для удаления, — поэтому в состояние кладётся запись из ответа, а не собранная на месте.
- **Появились промежуточные состояния**: «запрос идёт» и «запрос не удался». Раньше их не существовало.
- **`save()` больше нет**: каждое действие само является сохранением.
MD,
        ],
        [
            'id' => 'todo-api',
            'type' => 'code',
            'kind' => 'project',
            'lang' => 'html',
            'noSolution' => true,
            'title' => 'Список дел с сервером',
            'text' => 'Напиши скрипт приложения по требованиям выше. Разметку и стили менять не нужно.',
            'starter' => $markup . <<<'HTML'
<script>
  const API = '/api/demo/todos';
  let todos = [];

</script>
HTML,
            'tests' => <<<'JS'
let calls = 0;
const realFetch = window.fetch;
window.fetch = (...args) => { calls++; return realFetch(...args); };

const api = (path, method = 'GET') => realFetch('/api/demo/' + path, { method });
const serverTodos = async () => (await api('todos')).json();
const items = () => $$('#todos > li.todo');
const texts = () => items().map((li) => li.querySelector('.text').textContent);
const add = (value) => { type('#todo-text', value); submit('#new-todo'); };
const errorShown = () => !$('#error').hidden && text('#error') === 'Не удалось выполнить запрос';

test('Загрузка: loadTodos() рисует список с сервера', async () => {
  await until(() => text('#status') !== 'Загрузка…', 'Первая загрузка не завершилась');
  await api('reset', 'POST');
  await loadTodos();
  assertEqual(texts(), ['Заказать зёрна', 'Проверить кофемолку', 'Обновить меню']);
  assertEqual(items().map((li) => li.dataset.id), ['1', '2', '3']);
});
test('Загрузка: во время запроса «Загрузка…», после — пусто', async () => {
  const promise = loadTodos();
  assertEqual(text('#status'), 'Загрузка…');
  await promise;
  assertEqual(text('#status'), '');
});
test('Список: у дела текст и кнопка удаления, #empty скрыт', () => {
  const li = items()[0];
  assert(li.querySelector('.text'), 'Нет элемента .text');
  assertEqual(li.querySelector('button.delete').type, 'button');
  assertEqual($('#empty').hidden, true);
});

test('Добавление: на время запроса кнопка отключена', async () => {
  add('Купить фильтры');
  assertEqual($('#new-todo button').disabled, true, 'Кнопка должна отключаться сразу после отправки');
  await until(() => items().length === 4, 'Новое дело не появилось');
  assertEqual($('#new-todo button').disabled, false, 'После ответа кнопка должна включиться');
});
test('Добавление: дело получает id от сервера, поле очищается', async () => {
  assertEqual([texts()[3], items()[3].dataset.id, $('#todo-text').value], ['Купить фильтры', '4', '']);
  assertEqual((await serverTodos()).length, 4);
});
test('Добавление: пустой текст запрос не отправляет', async () => {
  calls = 0;
  add('   ');
  add('');
  await wait(150);
  assertEqual([calls, items().length], [0, 4]);
});
test('Добавление: текст вставляется как текст', async () => {
  add('<b>жирное</b>');
  await until(() => items().length === 5, 'Дело не появилось');
  assertEqual([items()[4].querySelector('.text').children.length, texts()[4]], [0, '<b>жирное</b>']);
});

test('Удаление: дело исчезает после ответа сервера, а не сразу', async () => {
  items()[4].querySelector('.delete').click();
  assertEqual(items().length, 5, 'Пункт исчез до ответа сервера');
  await until(() => items().length === 4, 'Пункт не исчез');
  assertEqual((await serverTodos()).length, 4);
});
test('Удаление: удаляется именно выбранное дело', async () => {
  items()[0].querySelector('.delete').click();
  await until(() => items().length === 3, 'Пункт не исчез');
  assertEqual(texts(), ['Проверить кофемолку', 'Обновить меню', 'Купить фильтры']);
  assertEqual((await serverTodos()).map((todo) => todo.id), [2, 3, 4]);
});

test('Ошибки: неудачное добавление показывает ошибку и ничего не добавляет', async () => {
  await api('fail-next', 'POST');
  add('Не добавится');
  await until(errorShown, 'Сообщение об ошибке не появилось');
  assertEqual(items().length, 3);
  assertEqual($('#todo-text').value, 'Не добавится', 'Текст в поле должен сохраниться');
  assertEqual($('#new-todo button').disabled, false, 'После ошибки кнопка осталась отключённой');
});
test('Ошибки: успешный запрос скрывает сообщение', async () => {
  submit('#new-todo');
  await until(() => items().length === 4, 'Повторная отправка не сработала');
  assertEqual([$('#error').hidden, texts()[3]], [true, 'Не добавится']);
});
test('Ошибки: неудачное удаление ничего не удаляет', async () => {
  await api('fail-next', 'POST');
  items()[0].querySelector('.delete').click();
  await until(errorShown, 'Сообщение об ошибке не появилось');
  assertEqual([items().length, (await serverTodos()).length], [4, 4]);
});
test('Ошибки: неудачная загрузка не бросает ошибку наружу', async () => {
  await api('fail-next', 'POST');
  await loadTodos();
  assert(errorShown(), 'Сообщение об ошибке не показано');
  assert(text('#status') !== 'Загрузка…', 'Статус «Загрузка…» остался после ошибки');
});

test('Пустой список: показан #empty', async () => {
  await loadTodos();
  assertEqual($('#error').hidden, true);
  while (items().length > 0) {
    const before = items().length;
    items()[0].querySelector('.delete').click();
    await until(() => items().length === before - 1, 'Пункт не исчез');
  }
  assertEqual($('#empty').hidden, false);
  assertEqual(await serverTodos(), []);
});
JS,
            'hints' => [
                '**Общая функция запроса.** `` async function request(url, options) { const response = await fetch(url, options); if (!response.ok) throw new Error(`Сервер ответил ${response.status}`); return response.status === 204 ? null : response.json(); } `` — у ответа `204` тела нет, разбирать его как JSON нельзя.',
                '**Показ ошибки.** Две маленькие функции: `showError()` записывает текст и снимает `hidden`, `hideError()` — ставит `hidden`. Первая вызывается в каждом `catch`, вторая — после каждого успешного запроса.',
                '**Загрузка.** `status.textContent = \'Загрузка…\';` до `try`. В `try` — запрос, присваивание `todos`, `hideError()`. В `finally` — очистить статус и вызвать `render()`.',
                '**Удаление.** Обработчик на списке: `const li = event.target.closest(\'.todo\')`, `const id = Number(li.dataset.id)`. После успешного `DELETE`: `todos = todos.filter((todo) => todo.id !== id);`',
                '**Пустой список.** В конце `render()`: `empty.hidden = todos.length > 0;`',
            ],
            'solution' => $markup . <<<'HTML'
<script>
  const API = '/api/demo/todos';
  let todos = [];

  const form = document.querySelector('#new-todo');
  const input = document.querySelector('#todo-text');
  const submitButton = form.querySelector('button');
  const list = document.querySelector('#todos');
  const status = document.querySelector('#status');
  const errorBox = document.querySelector('#error');
  const empty = document.querySelector('#empty');

  async function request(url, options) {
    const response = await fetch(url, options);
    if (!response.ok) {
      throw new Error(`Сервер ответил ${response.status}`);
    }
    return response.status === 204 ? null : response.json();
  }

  function showError() {
    errorBox.textContent = 'Не удалось выполнить запрос';
    errorBox.hidden = false;
  }

  function hideError() {
    errorBox.hidden = true;
  }

  function render() {
    list.replaceChildren();

    for (const todo of todos) {
      const li = document.createElement('li');
      li.classList.add('todo');
      li.dataset.id = todo.id;

      const text = document.createElement('span');
      text.classList.add('text');
      text.textContent = todo.text;

      const remove = document.createElement('button');
      remove.type = 'button';
      remove.classList.add('delete');
      remove.textContent = '×';
      remove.setAttribute('aria-label', 'Удалить');

      li.append(text, remove);
      list.append(li);
    }

    empty.hidden = todos.length > 0;
  }

  async function loadTodos() {
    status.textContent = 'Загрузка…';

    try {
      todos = await request(API);
      hideError();
    } catch (error) {
      showError();
    } finally {
      status.textContent = '';
      render();
    }
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const text = input.value.trim();
    if (text === '') return;

    submitButton.disabled = true;

    try {
      const todo = await request(API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text }),
      });

      todos.push(todo);
      input.value = '';
      hideError();
      render();
    } catch (error) {
      showError();
    } finally {
      submitButton.disabled = false;
    }
  });

  list.addEventListener('click', async (event) => {
    if (!event.target.closest('.delete')) return;

    const id = Number(event.target.closest('.todo').dataset.id);

    try {
      await request(`${API}/${id}`, { method: 'DELETE' });

      todos = todos.filter((todo) => todo.id !== id);
      hideError();
      render();
    } catch (error) {
      showError();
    }
  });

  loadTodos();
</script>
HTML,
        ],
    ],
    'cards' => [
        ['В каком порядке менять состояние и отправлять запрос?', 'Сначала запрос, потом изменение состояния. Иначе при ошибке страница покажет то, чего на сервере нет.'],
        ['Откуда брать `id` новой записи в приложении с сервером?', 'Из ответа сервера. В состояние кладётся запись, которую он вернул, а не собранная на месте.'],
        ['Зачем делать одну функцию `request` для всех запросов?', 'Проверка `response.ok` и разбор ответа пишутся один раз. Каждый обработчик — это `try` с запросом и `catch` с показом ошибки.'],
        ['Что нужно учесть при разборе ответа с кодом `204`?', 'У него нет тела: `response.json()` бросит ошибку. Тело разбирают, только если оно есть.'],
    ],
];
