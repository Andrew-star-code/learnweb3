<?php
return [
    'tasks' => [
        [
            'id' => 'modal-quiz',
            'type' => 'quiz',
            'title' => 'Окно не открывается',
            'text' => 'Ты скопировал из документации Bootstrap 5 разметку модального окна и кнопку к нему. Стили применились, но по нажатию ничего не происходит. Что стоит проверить в первую очередь?',
            'options' => [
                'Подключён ли bootstrap.bundle.min.js и совпадает ли data-bs-target с id окна',
                'Написан ли свой обработчик клика на JavaScript',
                'Есть ли у кнопки класс btn-primary',
                'Обёрнуто ли окно в .container',
            ],
            'answer' => 0,
            'explain' => 'Компоненты с поведением работают через скрипт Bootstrap, который находит элементы по data-атрибутам. Свой JavaScript писать не нужно. Если скрипта нет или `data-bs-target` указывает на несуществующий id, нажатие ничего не делает — и ошибки при этом тоже нет.',
        ],
        [
            'id' => 'card',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Карточка товара',
            'text' => 'Заполни классы, чтобы получилась карточка Bootstrap с основной кнопкой.',
            'starter' => <<<'HTML'
<div class="___" style="max-width: 320px">
  <div class="___">
    <h5 class="___">Эфиопия Иргачеффе</h5>
    <p class="___">Светлая обжарка, ноты жасмина и цитрусов.</p>
    <a href="/buy" class="___ ___">Купить</a>
  </div>
</div>
HTML,
            'tests' => <<<'JS'
test('внешний блок — .card', () => assertEqual($('body > div').className.trim(), 'card'));
test('внутри — .card-body', () => $('.card > .card-body'));
test('заголовок — .card-title', () => assertEqual($('.card-body h5').className.trim(), 'card-title'));
test('абзац — .card-text', () => assertEqual($('.card-body p').className.trim(), 'card-text'));
test('ссылка — основная кнопка', () => assertEqual([...$('.card-body a').classList].sort(), ['btn', 'btn-primary']));
test('у карточки появилась рамка, у кнопки — синий фон', () => {
  assertEqual(css('.card', 'border-top-width'), '1px');
  assertEqual(css('.card-body a', 'background-color'), 'rgb(13, 110, 253)');
});
JS,
            'hints' => [
                'Сверху вниз: `card`, `card-body`, `card-title`, `card-text`.',
                'Кнопке нужны два класса: базовый `btn` и вариант `btn-primary`.',
            ],
            'solution' => <<<'HTML'
<div class="card" style="max-width: 320px">
  <div class="card-body">
    <h5 class="card-title">Эфиопия Иргачеффе</h5>
    <p class="card-text">Светлая обжарка, ноты жасмина и цитрусов.</p>
    <a href="/buy" class="btn btn-primary">Купить</a>
  </div>
</div>
HTML,
        ],
        [
            'id' => 'form',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Форма без оформления',
            'text' => "Разметка формы правильная, но Bootstrap её не оформил: ни у одного элемента нет его классов. Добавь их:\n\n- каждой группе (`div` вокруг подписи и поля) — отступ снизу `mb-3`;\n- подписям текстовых полей — `form-label`;\n- текстовым полям и `textarea` — `form-control`, выпадающему списку — `form-select`;\n- флажку — обёртка `form-check`, сам флажок `form-check-input`, его подпись `form-check-label`;\n- кнопке — вид основной кнопки.\n\nАтрибуты `name`, `id`, `for` и `type` не трогай.",
            'starter' => <<<'HTML'
<form action="/order" method="post" style="max-width: 420px">
  <div>
    <label for="name">Имя</label>
    <input type="text" id="name" name="name" required>
  </div>
  <div>
    <label for="grind">Помол</label>
    <select id="grind" name="grind">
      <option value="beans">В зёрнах</option>
      <option value="filter">Под фильтр</option>
    </select>
  </div>
  <div>
    <label for="comment">Комментарий</label>
    <textarea id="comment" name="comment"></textarea>
  </div>
  <div>
    <input type="checkbox" id="gift" name="gift">
    <label for="gift">Подарочная упаковка</label>
  </div>
  <button type="submit">Заказать</button>
</form>
HTML,
            'tests' => <<<'JS'
const has = (selector, ...classes) => classes.every((c) => $(selector).classList.contains(c));
test('группы имеют отступ mb-3', () => assertEqual($$('form > div.mb-3').length, 4));
test('подписи текстовых полей — form-label', () => assert(has('[for=name]', 'form-label') && has('[for=grind]', 'form-label') && has('[for=comment]', 'form-label'), 'Не у всех подписей есть form-label'));
test('поле и textarea — form-control', () => assert(has('#name', 'form-control') && has('#comment', 'form-control'), 'Нет form-control'));
test('список — form-select', () => assert(has('#grind', 'form-select'), 'Нет form-select'));
test('флажок оформлен как form-check', () => {
  assert($('#gift').parentElement.classList.contains('form-check'), 'Обёртке флажка нужен класс form-check');
  assert(has('#gift', 'form-check-input') && has('[for=gift]', 'form-check-label'), 'Нет form-check-input или form-check-label');
});
test('кнопка — btn btn-primary', () => assert(has('form button', 'btn', 'btn-primary'), 'Нет классов кнопки'));
test('поля растянулись на ширину формы', () => assert(Math.abs(box('#name').width - box('form').width) <= 1, 'Поле не на всю ширину'));
test('атрибуты формы не тронуты', () => {
  assertEqual($$('form [name]').map((f) => f.name), ['name', 'grind', 'comment', 'gift']);
  assertEqual($('form button').type, 'submit');
});
JS,
            'hints' => [
                'Группа текстового поля: `<div class="mb-3"><label class="form-label" …><input class="form-control" …></div>`.',
                'Группа флажка: `<div class="mb-3 form-check">` — два класса сразу.',
            ],
            'solution' => <<<'HTML'
<form action="/order" method="post" style="max-width: 420px">
  <div class="mb-3">
    <label for="name" class="form-label">Имя</label>
    <input type="text" class="form-control" id="name" name="name" required>
  </div>
  <div class="mb-3">
    <label for="grind" class="form-label">Помол</label>
    <select class="form-select" id="grind" name="grind">
      <option value="beans">В зёрнах</option>
      <option value="filter">Под фильтр</option>
    </select>
  </div>
  <div class="mb-3">
    <label for="comment" class="form-label">Комментарий</label>
    <textarea class="form-control" id="comment" name="comment"></textarea>
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="gift" name="gift">
    <label class="form-check-label" for="gift">Подарочная упаковка</label>
  </div>
  <button type="submit" class="btn btn-primary">Заказать</button>
</form>
HTML,
        ],
        [
            'id' => 'table',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Таблица остатков',
            'text' => "Сверстай таблицу остатков на складе — типичный экран админки.\n\n| Товар | Остаток | Статус |\n|---|---|---|\n| Эфиопия | 24 | В наличии |\n| Колумбия | 3 | Заканчивается |\n| Кения | 0 | Нет |\n\nТребования:\n\n- таблица Bootstrap с чередованием строк (`table-striped`);\n- заголовки столбцов — в `thead`, данные — в `tbody`;\n- таблица обёрнута в блок `table-responsive`;\n- статусы оформлены метками `badge`: «В наличии» — `text-bg-success`, «Заканчивается» — `text-bg-warning`, «Нет» — `text-bg-danger`.",
            'starter' => "",
            'tests' => <<<'JS'
test('таблица с классами table и table-striped', () => $('table.table.table-striped'));
test('таблица обёрнута в .table-responsive', () => $('.table-responsive > table'));
test('три заголовка в thead', () => assertEqual($$('thead th').map((th) => th.textContent.trim()), ['Товар', 'Остаток', 'Статус']));
test('три строки данных в tbody', () => assertEqual($$('tbody tr').length, 3));
test('товары и остатки', () => assertEqual($$('tbody tr').map((tr) => [tr.cells[0].textContent.trim(), tr.cells[1].textContent.trim()]), [['Эфиопия', '24'], ['Колумбия', '3'], ['Кения', '0']]));
test('статусы — метки badge с нужными цветами', () => {
  const badges = $$('tbody .badge');
  assertEqual(badges.map((b) => b.textContent.trim()), ['В наличии', 'Заканчивается', 'Нет']);
  assertEqual(badges.map((b) => ['success', 'warning', 'danger'].find((c) => b.classList.contains('text-bg-' + c))), ['success', 'warning', 'danger']);
});
JS,
            'hints' => [
                'Каркас: `<div class="table-responsive"><table class="table table-striped"><thead><tr><th>…</th></tr></thead><tbody>…</tbody></table></div>`.',
                'Метка в ячейке: `<td><span class="badge text-bg-success">В наличии</span></td>`.',
            ],
            'solution' => <<<'HTML'
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr><th>Товар</th><th>Остаток</th><th>Статус</th></tr>
    </thead>
    <tbody>
      <tr><td>Эфиопия</td><td>24</td><td><span class="badge text-bg-success">В наличии</span></td></tr>
      <tr><td>Колумбия</td><td>3</td><td><span class="badge text-bg-warning">Заканчивается</span></td></tr>
      <tr><td>Кения</td><td>0</td><td><span class="badge text-bg-danger">Нет</span></td></tr>
    </tbody>
  </table>
</div>
HTML,
        ],
        [
            'id' => 'modal',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Оживить модальное окно',
            'text' => "Разметка окна подтверждения готова, но оно не открывается и не закрывается: не хватает атрибутов, которые связывают кнопки с окном.\n\nДобавь:\n\n- окну — `id=\"confirm\"`;\n- кнопке «Удалить запись» — атрибуты, которые открывают это окно;\n- крестику и кнопке «Отмена» — атрибут, который его закрывает.\n\nПроверь в предпросмотре: окно должно открываться и закрываться.",
            'starter' => <<<'HTML'
<div class="p-3">
  <button type="button" class="btn btn-danger open">Удалить запись</button>
</div>

<div class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Удалить запись?</h5>
        <button type="button" class="btn-close" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">Это действие нельзя отменить.</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary cancel">Отмена</button>
        <button type="button" class="btn btn-danger">Удалить</button>
      </div>
    </div>
  </div>
</div>
HTML,
            'tests' => <<<'JS'
const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
test('у окна id="confirm"', () => assertEqual($('.modal').id, 'confirm'));
test('у кнопки открытия есть data-bs-toggle и data-bs-target', () => {
  assertEqual([$('.open').getAttribute('data-bs-toggle'), $('.open').getAttribute('data-bs-target')], ['modal', '#confirm']);
});
test('по нажатию окно открывается', async () => {
  $('.open').click();
  await wait(700);
  assert($('.modal').classList.contains('show'), 'Окно не открылось');
});
test('кнопка «Отмена» закрывает окно', async () => {
  $('.cancel').click();
  await wait(700);
  assert(!$('.modal').classList.contains('show'), 'Окно не закрылось');
});
test('крестик тоже закрывает окно', () => assertEqual($('.btn-close').getAttribute('data-bs-dismiss'), 'modal'));
JS,
            'hints' => [
                'Открытие: `data-bs-toggle="modal" data-bs-target="#confirm"` на кнопке.',
                'Закрытие: `data-bs-dismiss="modal"` на крестике и на кнопке «Отмена».',
            ],
            'solution' => <<<'HTML'
<div class="p-3">
  <button type="button" class="btn btn-danger open" data-bs-toggle="modal" data-bs-target="#confirm">Удалить запись</button>
</div>

<div class="modal fade" id="confirm" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Удалить запись?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">Это действие нельзя отменить.</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary cancel" data-bs-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-danger">Удалить</button>
      </div>
    </div>
  </div>
</div>
HTML,
        ],
        [
            'id' => 'navbar',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Навбар',
            'text' => "Сверстай навигационную панель с нуля. Разметку можно взять из примера в уроке и доработать — в обычной работе именно так и делают.\n\n- Панель: `navbar`, разворачивается от 768px (`navbar-expand-md`), светлый фон.\n- Название сайта «Зерно» — ссылка `navbar-brand` на `/`.\n- Кнопка-гамбургер `navbar-toggler`, которая открывает и закрывает меню.\n- Меню — блок `collapse navbar-collapse` с `id=\"menu\"`, в нём список `navbar-nav` из трёх ссылок `nav-link`: «Меню», «Доставка», «Контакты».\n\nПроверь на ширине 360: меню должно быть спрятано и открываться по кнопке.",
            'starter' => "",
            'tests' => <<<'JS'
const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
test('панель с классами navbar и navbar-expand-md', () => $('nav.navbar.navbar-expand-md'));
test('название сайта — ссылка navbar-brand', () => { assertEqual(text('.navbar-brand'), 'Зерно'); assertEqual($('.navbar-brand').getAttribute('href'), '/'); });
test('три ссылки nav-link в списке navbar-nav', () => assertEqual($$('#menu .navbar-nav .nav-link').map((a) => a.textContent.trim()), ['Меню', 'Доставка', 'Контакты']));
test('на ширине 1000 меню развёрнуто в ряд, кнопка скрыта', async () => {
  await setWidth(1000);
  const links = $$('.nav-link').map((a) => box(a));
  assert(links[0].width > 0 && Math.abs(links[0].top - links[2].top) <= 1, 'Ссылки не в одну строку');
  assertEqual(css('.navbar-toggler', 'display'), 'none');
});
test('на ширине 400 меню спрятано, кнопка видна', async () => {
  await setWidth(400);
  assertEqual(css('#menu', 'display'), 'none');
  assert(css('.navbar-toggler', 'display') !== 'none', 'Кнопка меню не видна');
});
test('кнопка открывает меню', async () => {
  await setWidth(400);
  $('.navbar-toggler').click();
  await wait(700);
  assert($('#menu').classList.contains('show'), 'Меню не открылось — проверь data-bs-toggle и data-bs-target');
});
JS,
            'hints' => [
                'Каркас: `<nav class="navbar navbar-expand-md bg-light"><div class="container"> бренд, кнопка, меню </div></nav>`.',
                'Кнопка: `<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Открыть меню"><span class="navbar-toggler-icon"></span></button>`.',
                'Меню: `<div class="collapse navbar-collapse" id="menu"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="/menu">Меню</a></li>…</ul></div>`.',
            ],
            'solution' => <<<'HTML'
<nav class="navbar navbar-expand-md bg-light">
  <div class="container">
    <a class="navbar-brand" href="/">Зерно</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
            aria-controls="menu" aria-expanded="false" aria-label="Открыть меню">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="/menu">Меню</a></li>
        <li class="nav-item"><a class="nav-link" href="/delivery">Доставка</a></li>
        <li class="nav-item"><a class="nav-link" href="/contacts">Контакты</a></li>
      </ul>
    </div>
  </div>
</nav>
HTML,
        ],
    ],
    'cards' => [
        ['По какой схеме строятся классы компонентов Bootstrap?', 'Базовый класс плюс модификатор: `btn btn-primary`, `alert alert-warning`, `table table-striped`.'],
        ['Какие классы нужны полям формы в Bootstrap 5?', '`form-control` — текстовые поля и `textarea`, `form-select` — список, `form-check-input` — флажки и переключатели, `form-label` — подписи, `mb-3` — отступ группы.'],
        ['Как кнопка открывает модальное окно без своего JavaScript?', 'Атрибутами `data-bs-toggle="modal"` и `data-bs-target="#id-окна"`. Нужен подключённый `bootstrap.bundle.min.js`.'],
        ['Что делает класс `navbar-expand-md`?', 'От 768px меню показывается в ряд, уже — прячется под кнопку `navbar-toggler`.'],
        ['Как поменять цвета Bootstrap 5 под свой проект?', 'Переопределить переменные CSS (`--bs-primary`, `--bs-btn-bg` и другие) в своих стилях, подключённых после Bootstrap.'],
    ],
];
