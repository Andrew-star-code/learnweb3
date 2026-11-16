<?php
return [
    'tasks' => [
        [
            'id' => 'version-3',
            'type' => 'quiz',
            'title' => 'Определи версию: первый проект',
            'text' => "В шаблоне старого проекта ты видишь:\n\n```html\n<div class=\"col-xs-6\">\n  <div class=\"panel panel-default\">\n    <div class=\"panel-body\">\n      <button class=\"btn btn-default\" data-toggle=\"modal\" data-target=\"#edit\">\n        <span class=\"glyphicon glyphicon-pencil\"></span> Изменить\n      </button>\n    </div>\n  </div>\n</div>\n```\n\nКакая это версия Bootstrap?",
            'options' => ['Bootstrap 3', 'Bootstrap 4', 'Bootstrap 5', 'По разметке определить нельзя'],
            'answer' => 0,
            'explain' => 'Признаки третьей версии: `col-xs-`, `panel`, `btn-default` и иконки `glyphicon`. Всё это убрали в четвёртой. `data-toggle` без `bs` говорит только о том, что версия не пятая.',
        ],
        [
            'id' => 'version-4',
            'type' => 'quiz',
            'title' => 'Определи версию: второй проект',
            'text' => "```html\n<div class=\"card\">\n  <div class=\"card-body d-flex\">\n    <span class=\"badge badge-primary mr-2\">Новинка</span>\n    <button class=\"btn btn-secondary ml-auto\" data-toggle=\"collapse\" data-target=\"#more\">Ещё</button>\n  </div>\n</div>\n```\n\nА это?",
            'options' => ['Bootstrap 3', 'Bootstrap 4', 'Bootstrap 5', 'Смесь версий, работать не будет'],
            'answer' => 1,
            'explain' => '`card` и `d-flex` появились в четвёртой версии. А `mr-2`, `ml-auto`, `badge-primary` и `data-toggle` в пятой заменены на `me-2`, `ms-auto`, `text-bg-primary` и `data-bs-toggle`. Значит, это Bootstrap 4.',
        ],
        [
            'id' => 'jquery-set',
            'type' => 'quiz',
            'title' => 'Читаем jQuery: набор',
            'text' => "На странице пять строк таблицы с классом `row-item`. Что сделает эта строка?\n\n```js\n\$('.row-item').addClass('highlight');\n```",
            'options' => [
                'Добавит класс первой найденной строке',
                'Добавит класс всем пяти строкам',
                'Ничего: для нескольких элементов нужен цикл',
                'Вызовет ошибку: найдено больше одного элемента',
            ],
            'answer' => 1,
            'explain' => '`$(…)` возвращает набор всех найденных элементов, и метод применяется к каждому. Цикл не нужен. В современном JavaScript то же самое пишется через `querySelectorAll` и `forEach`.',
        ],
        [
            'id' => 'jquery-silent',
            'type' => 'quiz',
            'title' => 'Читаем jQuery: опечатка',
            'text' => "Итоговая сумма на странице лежит в элементе с `id=\"total\"`. В коде написано:\n\n```js\n\$('#totl').text('1500 ₽');\n```\n\nЧто произойдёт?",
            'options' => [
                'ReferenceError: totl is not defined',
                'TypeError: Cannot read properties of null',
                'Ничего: ни изменений на странице, ни ошибки',
                'Сумма обновится: jQuery исправляет опечатки',
            ],
            'answer' => 2,
            'explain' => 'По селектору с опечаткой jQuery найдёт пустой набор и «применит» метод к нулю элементов. Ошибки нет, и это главная ловушка при отладке старого кода: если что-то не обновляется, первым делом проверь, что селектор вообще что-то находит — `console.log($(\'#totl\').length)`.',
        ],
        [
            'id' => 'jquery-translate',
            'type' => 'quiz',
            'title' => 'jQuery и современный JavaScript',
            'text' => "Какая запись без jQuery делает то же самое, что эта строка?\n\n```js\n\$('#save').on('click', handleSave);\n```",
            'options' => [
                "document.querySelector('#save').addEventListener('click', handleSave);",
                "document.querySelector('#save').onclick(handleSave);",
                "document.getElementById('#save').click(handleSave);",
                "window.on('#save', 'click', handleSave);",
            ],
            'answer' => 0,
            'explain' => '`$(\'#save\')` — это поиск элемента, `.on(\'click\', …)` — подписка на событие. В браузере им соответствуют `querySelector` и `addEventListener`. Подробно — в блоке «JavaScript в браузере».',
        ],
        [
            'id' => 'migrate',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Перенос блока с Bootstrap 3 на 5',
            'text' => "Блок скопирован из старого проекта на Bootstrap 3 в новый, где подключён Bootstrap 5. Половина классов здесь ничего не значит: раскладка развалилась, кнопка «Подробнее» не работает.\n\nПереведи разметку на Bootstrap 5, пользуясь таблицей из урока. Что должно получиться:\n\n- две колонки по половине ряда на любой ширине;\n- блок с рамкой — карточка с телом;\n- метка «Новинка» основного цвета;\n- картинка, которая сжимается вместе с колонкой;\n- серая кнопка, прижатая вправо, раскрывает блок с описанием;\n- абзац «Только на широком экране» скрыт на ширине меньше 576px;\n- цена выровнена по правому краю.",
            'starter' => <<<'HTML'
<div class="container">
  <div class="row">
    <div class="col-xs-6">
      <div class="panel panel-default">
        <div class="panel-body">
          <span class="label label-primary">Новинка</span>
          <img class="img-responsive" src="/assets/sample-wide.svg" alt="Кофе Эфиопия">
          <button type="button" class="btn btn-default pull-right" data-toggle="collapse" data-target="#details">Подробнее</button>
          <p class="hidden-xs wide-only">Только на широком экране.</p>
          <div class="collapse" id="details">Светлая обжарка, ноты жасмина.</div>
        </div>
      </div>
    </div>
    <div class="col-xs-6">
      <p class="text-right price">890 ₽</p>
    </div>
  </div>
</div>
HTML,
            'tests' => <<<'JS'
const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
test('не осталось классов и атрибутов Bootstrap 3', () => {
  const html = document.querySelector('.container').outerHTML;
  const old = ['col-xs-', 'panel', 'label-primary', 'img-responsive', 'btn-default', 'pull-right', 'hidden-xs', 'text-right', 'data-toggle', 'data-target'];
  const found = old.filter((name) => html.includes(name));
  assertEqual(found, [], 'Остались старые имена');
});
test('две колонки по половине на ширине 360', async () => {
  await setWidth(360);
  const cols = $$('.row > div').map((c) => box(c));
  assert(Math.abs(cols[0].top - cols[1].top) <= 1 && Math.abs(cols[0].width - cols[1].width) <= 1, 'Колонки не стоят рядом по половине ряда');
});
test('карточка с телом', () => $('.card > .card-body'));
test('метка основного цвета', () => assert($('.card-body span').classList.contains('badge') && $('.card-body span').classList.contains('text-bg-primary'), 'Нужны классы badge и text-bg-primary'));
test('картинка сжимается вместе с колонкой', async () => {
  await setWidth(360);
  assert($('img').classList.contains('img-fluid'), 'Нужен класс img-fluid');
  assert(box('img').right <= box('.card-body').right + 1, 'Картинка вылезает из карточки');
});
test('кнопка серая и прижата вправо', () => assert($('button').classList.contains('btn-secondary') && $('button').classList.contains('float-end'), 'Нужны классы btn-secondary и float-end'));
test('кнопка раскрывает описание', async () => {
  $('button').click();
  await wait(700);
  assert($('#details').classList.contains('show'), 'Описание не раскрылось');
});
test('абзац скрыт на ширине 400 и виден на 700', async () => {
  await setWidth(400);
  assertEqual(css('.wide-only', 'display'), 'none');
  await setWidth(700);
  assert(css('.wide-only', 'display') !== 'none', 'От 576px абзац должен быть виден');
});
test('цена выровнена по правому краю', () => assertEqual(css('.price', 'text-align'), 'right'));
JS,
            'hints' => [
                'Колонки: `col-xs-6` → `col-6`. Блок: `panel panel-default` → `card`, `panel-body` → `card-body`.',
                'Метка: `badge text-bg-primary`. Картинка: `img-fluid`. Кнопка: `btn btn-secondary float-end`.',
                'Атрибуты: `data-bs-toggle="collapse" data-bs-target="#details"`.',
                'Скрыть на самых узких экранах: `d-none d-sm-block`. Текст вправо: `text-end`.',
            ],
            'solution' => <<<'HTML'
<div class="container">
  <div class="row">
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          <span class="badge text-bg-primary">Новинка</span>
          <img class="img-fluid" src="/assets/sample-wide.svg" alt="Кофе Эфиопия">
          <button type="button" class="btn btn-secondary float-end" data-bs-toggle="collapse" data-bs-target="#details">Подробнее</button>
          <p class="d-none d-sm-block wide-only">Только на широком экране.</p>
          <div class="collapse" id="details">Светлая обжарка, ноты жасмина.</div>
        </div>
      </div>
    </div>
    <div class="col-6">
      <p class="text-end price">890 ₽</p>
    </div>
  </div>
</div>
HTML,
        ],
    ],
    'cards' => [
        ['По каким признакам узнать Bootstrap 3?', '`col-xs-`, `panel`, `well`, `btn-default`, `glyphicon`, `pull-right`, `hidden-xs`, `img-responsive`; сетка на `float`, обязателен jQuery.'],
        ['Чем разметка Bootstrap 4 отличается от пятой версии?', 'В четвёртой `ml-`/`mr-`, `float-right`, `text-right`, `badge-primary`, `form-group`, `data-toggle` и jQuery. В пятой — `ms-`/`me-`, `float-end`, `text-end`, `text-bg-primary`, `mb-3`, `data-bs-toggle`, jQuery не нужен.'],
        ['Что возвращает `$(\'.item\')` в jQuery?', 'Набор всех найденных элементов. Метод применяется ко всем сразу; если ничего не найдено — молча ничего не делает.'],
        ['Чему в современном JavaScript соответствуют `$(sel)`, `.on()`, `.addClass()`, `.val()`?', '`document.querySelectorAll(sel)`, `.addEventListener()`, `.classList.add()`, `.value`.'],
        ['Как дорабатывать страницу на старом Bootstrap?', 'Определить версию, открыть документацию именно этой версии, делать по образцу соседних блоков. Версии не смешивать и не переписывать всё «заодно».'],
    ],
];
