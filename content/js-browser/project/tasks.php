<?php
$markup = <<<'HTML'
<style>
  body { font-family: sans-serif; max-width: 420px; margin: 0 auto; padding: 16px; }
  #new-todo { display: flex; gap: 8px; }
  #todo-text { flex: 1; padding: 8px; font: inherit; }
  button { font: inherit; cursor: pointer; }
  #todos { list-style: none; padding: 0; }
  .todo { display: flex; align-items: center; gap: 8px; padding: 8px 0; border-bottom: 1px solid rgb(230, 230, 230); }
  .todo .text { flex: 1; }
  .todo.done .text { text-decoration: line-through; color: rgb(150, 150, 150); }
  .delete { border: 0; background: none; font-size: 20px; color: rgb(179, 55, 44); }
  .filters { display: flex; gap: 8px; align-items: center; }
  .filter { border: 1px solid rgb(200, 200, 200); background: white; border-radius: 6px; padding: 4px 10px; }
  .filter.active { background: rgb(47, 111, 79); border-color: rgb(47, 111, 79); color: white; }
  #left { margin-left: auto; color: rgb(100, 100, 100); }
</style>

<h1>Список дел</h1>

<form id="new-todo">
  <input type="text" id="todo-text" placeholder="Что нужно сделать?" aria-label="Новое дело">
  <button type="submit">Добавить</button>
</form>

<ul id="todos"></ul>

<div class="filters">
  <button type="button" class="filter active" data-filter="all">Все</button>
  <button type="button" class="filter" data-filter="active">Активные</button>
  <button type="button" class="filter" data-filter="done">Выполненные</button>
  <span id="left">Осталось: 0</span>
</div>

HTML;

return [
    'tasks' => [
        [
            'id' => 'plan',
            'type' => 'plan',
            'title' => 'План приложения',
            'text' => "Перечитай требования и запиши:\n\n- какие данные составляют состояние приложения;\n- что по шагам делает `render()`;\n- что делает обработчик каждого из четырёх действий: добавить, переключить, удалить, выбрать фильтр.",
            'reference' => <<<'MD'
**Состояние:** массив дел `todos` (у каждого текст и признак «выполнено») и выбранный фильтр `filter`. Больше ничего: счётчик и видимый список вычисляются из них.

**`render()`:**

1. Очистить список.
2. Пройти по всем делам с номером.
3. Если дело не подходит под выбранный фильтр — пропустить.
4. Создать `li` с классом `todo`, записать номер дела в `data-index`; если дело выполнено — добавить класс `done`.
5. Создать флажок (отмеченный, если выполнено), текст и кнопку удаления, вставить их в `li`, а `li` — в список.
6. Посчитать невыполненные дела по всему массиву и вывести в счётчик.
7. Проставить класс `active` кнопке выбранного фильтра.

**Обработчики** — все по одной схеме «изменить состояние → сохранить → отрисовать»:

- **Добавить:** отменить отправку формы, взять текст без пробелов по краям, пустой — выйти; добавить объект в массив; очистить поле.
- **Переключить:** по `data-index` найти дело, поменять `done` на противоположное.
- **Удалить:** по `data-index` убрать дело из массива.
- **Фильтр:** записать значение `data-filter` в переменную `filter`. Сохранять не нужно — данные не менялись, только отрисовать.

Переключение и удаление — один обработчик на списке с делегированием. Номер берётся в исходном массиве, а не среди видимых пунктов: при включённом фильтре это разные числа, поэтому он и записывается в `data-index` при отрисовке.
MD,
        ],
        [
            'id' => 'todo',
            'type' => 'code',
            'kind' => 'project',
            'lang' => 'html',
            'noSolution' => true,
            'storage' => true,
            'title' => 'Список дел',
            'text' => 'Напиши скрипт приложения по требованиям выше. Разметку и стили менять не нужно.',
            'starter' => $markup . <<<'HTML'
<script>
  let todos = [];
  let filter = 'all';

</script>
HTML,
            'tests' => <<<'JS'
const add = (value) => { type('#todo-text', value); submit('#new-todo'); };
const items = () => $$('#todos > li.todo');
const texts = () => items().map((li) => li.querySelector('.text').textContent);
const stored = () => JSON.parse(localStorage.getItem('todos'));

test('Добавление: отправка формы добавляет дело', () => {
  add('Купить зёрна');
  assertEqual(texts(), ['Купить зёрна']);
});
test('Добавление: страница не перезагружается', () => {
  const event = new Event('submit', { bubbles: true, cancelable: true });
  $('#new-todo').dispatchEvent(event);
  assert(event.defaultPrevented, 'Нет preventDefault');
});
test('Добавление: пробелы убираются, пустой текст игнорируется', () => {
  add('   Помыть кофемашину  ');
  add('    ');
  add('');
  assertEqual(texts(), ['Купить зёрна', 'Помыть кофемашину']);
});
test('Добавление: поле очищается', () => assertEqual($('#todo-text').value, ''));

test('Список: у дела флажок, текст и кнопка удаления', () => {
  const li = items()[0];
  assert(li.querySelector('input[type=checkbox]') && li.querySelector('.text'), 'Нет флажка или .text');
  assertEqual(li.querySelector('button.delete').type, 'button');
});
test('Список: щелчок по флажку отмечает дело выполненным', () => {
  items()[0].querySelector('input').click();
  const li = items()[0];
  assertEqual([li.classList.contains('done'), li.querySelector('input').checked], [true, true]);
  assertEqual(items()[1].classList.contains('done'), false);
});
test('Список: повторный щелчок снимает отметку', () => {
  items()[0].querySelector('input').click();
  assertEqual([items()[0].classList.contains('done'), items()[0].querySelector('input').checked], [false, false]);
});
test('Список: текст вставляется как текст', () => {
  add('<b>жирное</b> дело');
  assertEqual(items()[2].querySelector('.text').children.length, 0, 'Используй textContent');
  assertEqual(texts()[2], '<b>жирное</b> дело');
});
test('Список: удаление убирает нужное дело', () => {
  items()[2].querySelector('.delete').click();
  assertEqual(texts(), ['Купить зёрна', 'Помыть кофемашину']);
});

test('Счётчик: «Осталось: N» считает невыполненные', () => {
  assertEqual(text('#left'), 'Осталось: 2');
  items()[1].querySelector('input').click();
  assertEqual(text('#left'), 'Осталось: 1');
});

test('Фильтр: «Активные» показывает только невыполненные', () => {
  click('[data-filter=active]');
  assertEqual(texts(), ['Купить зёрна']);
  assertEqual($$('.filter.active').map((b) => b.dataset.filter), ['active']);
});
test('Фильтр: «Выполненные» показывает только выполненные', () => {
  click('[data-filter=done]');
  assertEqual(texts(), ['Помыть кофемашину']);
});
test('Фильтр: действия при включённом фильтре относятся к нужному делу', () => {
  items()[0].querySelector('input').click();   // снимаем отметку с «Помыть кофемашину»
  assertEqual(texts(), []);
  click('[data-filter=all]');
  assertEqual(items().map((li) => li.classList.contains('done')), [false, false]);
});
test('Фильтр: счётчик от фильтра не зависит', () => {
  click('[data-filter=done]');
  assertEqual(text('#left'), 'Осталось: 2');
  click('[data-filter=all]');
});

test('Хранение: список лежит в localStorage под ключом todos', () => {
  items()[0].querySelector('input').click();
  assertEqual(stored(), [{ text: 'Купить зёрна', done: true }, { text: 'Помыть кофемашину', done: false }]);
});
test('Хранение: удаление тоже сохраняется', () => {
  items()[1].querySelector('.delete').click();
  assertEqual(stored(), [{ text: 'Купить зёрна', done: true }]);
});
test('Хранение: load() и render() восстанавливают список', () => {
  localStorage.setItem('todos', JSON.stringify([{ text: 'Из хранилища', done: true }, { text: 'Второе', done: false }]));
  load();
  render();
  assertEqual(texts(), ['Из хранилища', 'Второе']);
  assertEqual(items().map((li) => li.classList.contains('done')), [true, false]);
  assertEqual(text('#left'), 'Осталось: 1');
});
test('Хранение: мусор в хранилище даёт пустой список', () => {
  localStorage.setItem('todos', '{сломано');
  load();
  render();
  assertEqual([texts(), text('#left')], [[], 'Осталось: 0']);
});
test('Хранение: при пустом хранилище список пуст', () => {
  localStorage.removeItem('todos');
  load();
  render();
  assertEqual(texts(), []);
});
JS,
            'hints' => [
                '**Отрисовка.** `todos.forEach((todo, index) => { … })` даёт и дело, и его номер. Номер запиши в элемент: `li.dataset.index = index;`',
                '**Фильтр в отрисовке.** В начале шага цикла: `if (filter === \'active\' && todo.done) return;` и `if (filter === \'done\' && !todo.done) return;`',
                '**Обработчик на списке.** `const li = event.target.closest(\'.todo\'); if (!li) return; const index = Number(li.dataset.index);` Дальше: если `event.target.closest(\'.delete\')` — `todos.splice(index, 1)`; если щёлкнули по флажку (`event.target.matches(\'input\')`) — `todos[index].done = !todos[index].done`.',
                '**Счётчик.** `todos.filter((todo) => !todo.done).length` — по всему массиву, а не по видимым пунктам.',
                '**Чтение.** `try { todos = JSON.parse(localStorage.getItem(\'todos\')) ?? []; } catch (error) { todos = []; }`',
            ],
            'solution' => $markup . <<<'HTML'
<script>
  let todos = [];
  let filter = 'all';

  const list = document.querySelector('#todos');
  const form = document.querySelector('#new-todo');
  const input = document.querySelector('#todo-text');
  const left = document.querySelector('#left');
  const filters = document.querySelectorAll('.filter');

  function load() {
    try {
      todos = JSON.parse(localStorage.getItem('todos')) ?? [];
    } catch (error) {
      todos = [];
    }
  }

  function save() {
    localStorage.setItem('todos', JSON.stringify(todos));
  }

  function render() {
    list.replaceChildren();

    todos.forEach((todo, index) => {
      if (filter === 'active' && todo.done) return;
      if (filter === 'done' && !todo.done) return;

      const li = document.createElement('li');
      li.classList.add('todo');
      li.classList.toggle('done', todo.done);
      li.dataset.index = index;

      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.checked = todo.done;

      const text = document.createElement('span');
      text.classList.add('text');
      text.textContent = todo.text;

      const remove = document.createElement('button');
      remove.type = 'button';
      remove.classList.add('delete');
      remove.textContent = '×';
      remove.setAttribute('aria-label', 'Удалить');

      li.append(checkbox, text, remove);
      list.append(li);
    });

    left.textContent = `Осталось: ${todos.filter((todo) => !todo.done).length}`;
    filters.forEach((button) => button.classList.toggle('active', button.dataset.filter === filter));
  }

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    const text = input.value.trim();
    if (text === '') return;

    todos.push({ text, done: false });
    input.value = '';
    save();
    render();
  });

  list.addEventListener('click', (event) => {
    const li = event.target.closest('.todo');
    if (!li) return;
    const index = Number(li.dataset.index);

    if (event.target.closest('.delete')) {
      todos.splice(index, 1);
    } else if (event.target.matches('input')) {
      todos[index].done = !todos[index].done;
    } else {
      return;
    }

    save();
    render();
  });

  document.querySelector('.filters').addEventListener('click', (event) => {
    const button = event.target.closest('.filter');
    if (!button) return;

    filter = button.dataset.filter;
    render();
  });

  load();
  render();
</script>
HTML,
        ],
    ],
    'cards' => [
        ['По какой схеме работает каждый обработчик в приложении со состоянием?', 'Изменить состояние → сохранить → перерисовать. Элементы страницы напрямую обработчик не правит.'],
        ['Как при делегировании понять, какому элементу массива соответствует нажатый пункт списка?', 'При отрисовке записать номер в `data-index`, в обработчике прочитать: `Number(li.dataset.index)`.'],
        ['Что входит в состояние, а что вычисляется?', 'В состоянии — минимум, который нельзя получить из другого: список дел и выбранный фильтр. Счётчик и видимые пункты вычисляются при отрисовке.'],
    ],
];
