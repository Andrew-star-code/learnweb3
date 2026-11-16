<?php
return [
    'tasks' => [
        [
            'id' => 'delegation-quiz',
            'type' => 'quiz',
            'title' => 'Новые кнопки не работают',
            'text' => "В списке дел у каждого пункта есть кнопка «Удалить». Обработчики повешены так:\n\n```js\ndocument.querySelectorAll('.delete').forEach((button) => {\n  button.addEventListener('click', removeItem);\n});\n```\n\nУ пунктов, которые были на странице изначально, удаление работает. У пунктов, добавленных позже через форму, — нет. Почему?",
            'options' => [
                'У новых кнопок другой класс',
                'Обработчики повешены один раз при загрузке — на кнопки, которые тогда существовали',
                'События click не работают на элементах, созданных через createElement',
                'Нужно вызвать preventDefault',
            ],
            'answer' => 1,
            'explain' => '`querySelectorAll` находит элементы, которые есть в момент вызова. Кнопки, созданные позже, обработчика не получили. Решение — делегирование: один обработчик на списке, который проверяет `event.target.closest(\'.delete\')`.',
        ],
        [
            'id' => 'counter',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Счётчик',
            'text' => 'Допиши скрипт: каждое нажатие кнопки «+» увеличивает число на странице на единицу.',
            'starter' => <<<'HTML'
<p>Порций: <output id="count">0</output></p>
<button type="button" id="plus">+</button>

<script>
  const button = document.querySelector('#plus');
  const output = document.querySelector('#count');
  let count = 0;

  button.___('___', () => {
    count++;
    output.___ = count;
  });
</script>
HTML,
            'tests' => <<<'JS'
test('в начале на странице 0', () => assertEqual(text('#count'), '0'));
test('после одного нажатия — 1', () => { click('#plus'); assertEqual(text('#count'), '1'); });
test('после ещё двух — 3', () => { click('#plus'); click('#plus'); assertEqual(text('#count'), '3'); });
JS,
            'hints' => [
                'Подписка на событие: `addEventListener(\'click\', функция)`.',
                'Показать число: `output.textContent = count;`',
            ],
            'solution' => <<<'HTML'
<p>Порций: <output id="count">0</output></p>
<button type="button" id="plus">+</button>

<script>
  const button = document.querySelector('#plus');
  const output = document.querySelector('#count');
  let count = 0;

  button.addEventListener('click', () => {
    count++;
    output.textContent = count;
  });
</script>
HTML,
        ],
        [
            'id' => 'handler-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Сработало само и увело со страницы',
            'text' => "На странице две проблемы.\n\n1. Сообщение «Заказ принят» появляется сразу при загрузке, а не по нажатию кнопки.\n2. Ссылка «Показать состав» должна раскрывать скрытый абзац на этой же странице. Вместо этого она уводит по адресу `/details`.\n\nИсправь обе.",
            'starter' => <<<'HTML'
<button type="button" id="order">Заказать</button>
<p id="message"></p>

<a href="/details" id="more">Показать состав</a>
<p id="details" hidden>Арабика 100%, светлая обжарка.</p>

<script>
  const message = document.querySelector('#message');
  const details = document.querySelector('#details');

  function showMessage() {
    message.textContent = 'Заказ принят';
  }

  document.querySelector('#order').addEventListener('click', showMessage());

  document.querySelector('#more').addEventListener('click', () => {
    details.hidden = false;
  });
</script>
HTML,
            'tests' => <<<'JS'
test('при загрузке сообщения нет', () => assertEqual(text('#message'), ''));
test('после нажатия кнопки сообщение появляется', () => { click('#order'); assertEqual(text('#message'), 'Заказ принят'); });
test('состав изначально скрыт', () => {
  // проверяем по состоянию до щелчка, поэтому этот тест стоит перед следующим
  assert($('#details').hidden || getComputedStyle($('#details')).display === 'none', 'Абзац должен быть скрыт');
});
test('щелчок по ссылке не уводит со страницы', () => {
  const event = new MouseEvent('click', { bubbles: true, cancelable: true });
  $('#more').dispatchEvent(event);
  assert(event.defaultPrevented, 'Стандартное действие ссылки не отменено');
});
test('после щелчка состав виден', () => assertEqual($('#details').hidden, false));
JS,
            'hints' => [
                '`showMessage()` со скобками — это вызов: функция выполняется сразу, а в обработчик попадает её результат, `undefined`. Нужно передать саму функцию: `showMessage`.',
                'Чтобы ссылка не переходила по адресу, обработчик должен принять объект события и вызвать `event.preventDefault()`.',
            ],
            'solution' => <<<'HTML'
<button type="button" id="order">Заказать</button>
<p id="message"></p>

<a href="/details" id="more">Показать состав</a>
<p id="details" hidden>Арабика 100%, светлая обжарка.</p>

<script>
  const message = document.querySelector('#message');
  const details = document.querySelector('#details');

  function showMessage() {
    message.textContent = 'Заказ принят';
  }

  document.querySelector('#order').addEventListener('click', showMessage);

  document.querySelector('#more').addEventListener('click', (event) => {
    event.preventDefault();
    details.hidden = false;
  });
</script>
HTML,
        ],
        [
            'id' => 'menu-toggle',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Меню по кнопке',
            'text' => "В блоке про вёрстку ты делал шапку с кнопкой «Меню», которая ничего не открывала. Теперь оживи её.\n\nПо нажатию кнопки:\n\n- у меню `#menu` переключается класс `open` (стили для него уже написаны);\n- у кнопки атрибут `aria-expanded` становится `\"true\"`, когда меню открыто, и `\"false\"`, когда закрыто.",
            'starter' => <<<'HTML'
<style>
  #menu { display: none; }
  #menu.open { display: block; }
</style>

<button type="button" class="menu-toggle" aria-expanded="false" aria-controls="menu">Меню</button>
<nav id="menu">
  <a href="/catalog">Каталог</a>
  <a href="/delivery">Доставка</a>
</nav>

<script>

</script>
HTML,
            'tests' => <<<'JS'
const state = () => [css('#menu', 'display'), $('.menu-toggle').getAttribute('aria-expanded')];
test('изначально меню закрыто', () => assertEqual(state(), ['none', 'false']));
test('первое нажатие открывает', () => { click('.menu-toggle'); assertEqual(state(), ['block', 'true']); });
test('второе нажатие закрывает', () => { click('.menu-toggle'); assertEqual(state(), ['none', 'false']); });
test('третье снова открывает', () => { click('.menu-toggle'); assertEqual(state(), ['block', 'true']); });
JS,
            'hints' => [
                '`classList.toggle(\'open\')` переключает класс и возвращает `true`, если класс теперь есть.',
                'Атрибут задаётся строкой: `toggle.setAttribute(\'aria-expanded\', String(open));`',
            ],
            'solution' => <<<'HTML'
<style>
  #menu { display: none; }
  #menu.open { display: block; }
</style>

<button type="button" class="menu-toggle" aria-expanded="false" aria-controls="menu">Меню</button>
<nav id="menu">
  <a href="/catalog">Каталог</a>
  <a href="/delivery">Доставка</a>
</nav>

<script>
  const toggle = document.querySelector('.menu-toggle');
  const menu = document.querySelector('#menu');

  toggle.addEventListener('click', () => {
    const open = menu.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
  });
</script>
HTML,
        ],
        [
            'id' => 'delegation',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Удаление через делегирование',
            'text' => "Кнопка «Добавить» уже работает: она создаёт новый пункт с кнопкой удаления. Допиши удаление.\n\nПо нажатию кнопки `.delete` её пункт `<li>` должен исчезать — и для двух исходных пунктов, и для добавленных позже. Сделай это **одним обработчиком на списке**.",
            'starter' => <<<'HTML'
<ul id="list">
  <li>Эспрессо <button type="button" class="delete">Удалить</button></li>
  <li>Капучино <button type="button" class="delete">Удалить</button></li>
</ul>
<button type="button" id="add">Добавить</button>

<script>
  const list = document.querySelector('#list');
  let number = 0;

  document.querySelector('#add').addEventListener('click', () => {
    number++;
    const li = document.createElement('li');
    li.textContent = `Новый напиток ${number} `;

    const button = document.createElement('button');
    button.type = 'button';
    button.classList.add('delete');
    button.textContent = 'Удалить';

    li.append(button);
    list.append(li);
  });

  // обработчик удаления

</script>
HTML,
            'tests' => <<<'JS'
const count = () => $$('#list > li').length;
test('изначально два пункта', () => assertEqual(count(), 2));
test('удаление исходного пункта', () => {
  click('#list li:first-child .delete');
  assertEqual(count(), 1);
  assert(text('#list li').startsWith('Капучино'), 'Удалён не тот пункт');
});
test('добавленный пункт тоже удаляется', () => {
  click('#add');
  click('#add');
  assertEqual(count(), 3);
  click('#list li:last-child .delete');
  assertEqual(count(), 2);
});
test('щелчок по тексту пункта ничего не удаляет', () => { click('#list li'); assertEqual(count(), 2); });
test('можно удалить всё', () => { $$('.delete').forEach((b) => b.click()); assertEqual(count(), 0); });
JS,
            'hints' => [
                'Обработчик вешается на `list` и получает `event`.',
                'Проверь, что щёлкнули по кнопке удаления: `const button = event.target.closest(\'.delete\'); if (!button) return;`',
                'Пункт, в котором лежит кнопка: `button.closest(\'li\')`. Удалить — `.remove()`.',
            ],
            'solution' => <<<'HTML'
<ul id="list">
  <li>Эспрессо <button type="button" class="delete">Удалить</button></li>
  <li>Капучино <button type="button" class="delete">Удалить</button></li>
</ul>
<button type="button" id="add">Добавить</button>

<script>
  const list = document.querySelector('#list');
  let number = 0;

  document.querySelector('#add').addEventListener('click', () => {
    number++;
    const li = document.createElement('li');
    li.textContent = `Новый напиток ${number} `;

    const button = document.createElement('button');
    button.type = 'button';
    button.classList.add('delete');
    button.textContent = 'Удалить';

    li.append(button);
    list.append(li);
  });

  list.addEventListener('click', (event) => {
    const button = event.target.closest('.delete');
    if (!button) return;

    button.closest('li').remove();
  });
</script>
HTML,
        ],
        [
            'id' => 'tabs',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Вкладки своими руками',
            'text' => "В Bootstrap вкладки — готовый компонент. Напиши такой же сам: разметка и стили готовы, скрипт — с нуля.\n\nУ каждой кнопки-вкладки есть атрибут `data-tab`, у каждой панели — `data-panel` с тем же значением.\n\nПо нажатию на вкладку:\n\n- она получает класс `active`, остальные вкладки его теряют;\n- панель с тем же значением показывается, остальные скрываются (свойство `hidden`).\n\nИзначально активна первая вкладка — это уже задано в разметке. Используй делегирование: один обработчик на блоке `.tabs`.",
            'starter' => <<<'HTML'
<style>
  .tabs { display: flex; gap: 4px; border-bottom: 2px solid rgb(220, 220, 220); font-family: sans-serif; }
  .tab { padding: 8px 14px; border: 0; background: none; font: inherit; cursor: pointer; }
  .tab.active { color: rgb(47, 111, 79); box-shadow: 0 2px 0 rgb(47, 111, 79); font-weight: 700; }
  .panel { padding: 12px 0; font-family: sans-serif; }
</style>

<div class="tabs">
  <button type="button" class="tab active" data-tab="about">Описание</button>
  <button type="button" class="tab" data-tab="specs">Характеристики</button>
  <button type="button" class="tab" data-tab="reviews">Отзывы</button>
</div>

<div class="panel" data-panel="about">Светлая обжарка, ноты жасмина.</div>
<div class="panel" data-panel="specs" hidden>Арабика, 250 г, Эфиопия.</div>
<div class="panel" data-panel="reviews" hidden>Пока нет отзывов.</div>

<script>

</script>
HTML,
            'tests' => <<<'JS'
const active = () => $$('.tab.active').map((t) => t.dataset.tab);
const visible = () => $$('.panel').filter((p) => !p.hidden).map((p) => p.dataset.panel);
test('изначально активна первая вкладка', () => assertEqual([active(), visible()], [['about'], ['about']]));
test('нажатие на «Характеристики»', () => { click('[data-tab=specs]'); assertEqual([active(), visible()], [['specs'], ['specs']]); });
test('нажатие на «Отзывы»', () => { click('[data-tab=reviews]'); assertEqual([active(), visible()], [['reviews'], ['reviews']]); });
test('возврат на первую', () => { click('[data-tab=about]'); assertEqual([active(), visible()], [['about'], ['about']]); });
test('повторное нажатие на активную ничего не ломает', () => { click('[data-tab=about]'); assertEqual([active(), visible()], [['about'], ['about']]); });
test('щелчок мимо вкладок ничего не меняет', () => { click('.tabs'); assertEqual([active(), visible()], [['about'], ['about']]); });
JS,
            'hints' => [
                'В обработчике на `.tabs`: `const tab = event.target.closest(\'.tab\'); if (!tab) return;`',
                'Пройди по всем вкладкам и поставь класс только нужной: `t.classList.toggle(\'active\', t === tab)`. Второй аргумент `toggle` говорит, добавить класс или убрать.',
                'С панелями так же: `panel.hidden = panel.dataset.panel !== tab.dataset.tab;`',
            ],
            'solution' => <<<'HTML'
<style>
  .tabs { display: flex; gap: 4px; border-bottom: 2px solid rgb(220, 220, 220); font-family: sans-serif; }
  .tab { padding: 8px 14px; border: 0; background: none; font: inherit; cursor: pointer; }
  .tab.active { color: rgb(47, 111, 79); box-shadow: 0 2px 0 rgb(47, 111, 79); font-weight: 700; }
  .panel { padding: 12px 0; font-family: sans-serif; }
</style>

<div class="tabs">
  <button type="button" class="tab active" data-tab="about">Описание</button>
  <button type="button" class="tab" data-tab="specs">Характеристики</button>
  <button type="button" class="tab" data-tab="reviews">Отзывы</button>
</div>

<div class="panel" data-panel="about">Светлая обжарка, ноты жасмина.</div>
<div class="panel" data-panel="specs" hidden>Арабика, 250 г, Эфиопия.</div>
<div class="panel" data-panel="reviews" hidden>Пока нет отзывов.</div>

<script>
  const tabs = document.querySelectorAll('.tab');
  const panels = document.querySelectorAll('.panel');

  document.querySelector('.tabs').addEventListener('click', (event) => {
    const tab = event.target.closest('.tab');
    if (!tab) return;

    tabs.forEach((t) => t.classList.toggle('active', t === tab));
    panels.forEach((panel) => {
      panel.hidden = panel.dataset.panel !== tab.dataset.tab;
    });
  });
</script>
HTML,
        ],
    ],
    'cards' => [
        ['Что не так в `button.addEventListener(\'click\', handle())`?', 'Скобки вызывают функцию сразу, при загрузке. Передавать нужно саму функцию: `handle`.'],
        ['Чем `event.target` отличается от `event.currentTarget`?', '`target` — элемент, на котором событие произошло. `currentTarget` — элемент, на котором висит обработчик. Из-за всплытия они могут различаться.'],
        ['Что такое делегирование событий и зачем оно?', 'Один обработчик на общем родителе, внутри проверка `event.target.closest(селектор)`. Работает и для элементов, добавленных позже.'],
        ['Когда нужен `event.preventDefault()`?', 'Когда у события есть действие браузера по умолчанию, а нужно своё: ссылка не должна уводить со страницы, форма — перезагружать её.'],
        ['Чем событие `input` отличается от `change`?', '`input` срабатывает на каждое изменение значения, `change` — когда изменение завершено (поле потеряло фокус; для флажков и списков — сразу).'],
    ],
];
