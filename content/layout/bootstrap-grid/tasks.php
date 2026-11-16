<?php
return [
    'tasks' => [
        [
            'id' => 'breakpoint-quiz',
            'type' => 'quiz',
            'title' => 'Что будет на телефоне',
            'text' => 'У колонки единственный класс `col-md-4`. Какую ширину она займёт на телефоне с экраном 360px?',
            'options' => ['Треть ряда', 'Всю ширину ряда', 'Она будет скрыта', 'Ширину своего содержимого'],
            'answer' => 1,
            'explain' => '`md` означает «от 768px и шире». Ниже этой точки правило не действует, и колонка — обычный блок на всю ширину. Чтобы на телефоне тоже была треть, нужен класс без контрольной точки: `col-4`.',
        ],
        [
            'id' => 'two-columns',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Контейнер, ряд, колонки',
            'text' => 'Заполни классы: боковая панель занимает треть ширины, содержимое — две трети. Начиная с 768px они стоят рядом, на телефоне — друг под другом.',
            'starter' => <<<'HTML'
<div class="___">
  <div class="___">
    <aside class="___">
      <div class="p-3 bg-light">Разделы</div>
    </aside>
    <main class="___">
      <div class="p-3 bg-light">Содержимое</div>
    </main>
  </div>
</div>
HTML,
            'tests' => <<<'JS'
test('колонки лежат в .row, а .row — в .container', () => {
  assert($('aside').parentElement.classList.contains('row'), 'Родитель колонок должен иметь класс row');
  assert($('aside').parentElement.parentElement.classList.contains('container'), 'Ряд должен лежать в .container');
});
test('на ширине 1000 колонки стоят рядом', async () => { await setWidth(1000); assert(Math.abs(box('aside').top - box('main').top) <= 1 && box('main').left >= box('aside').right - 1, 'Колонки не в одном ряду'); });
test('на ширине 1000 содержимое вдвое шире панели', async () => { await setWidth(1000); assert(Math.abs(box('main').width - 2 * box('aside').width) <= 2, `Ширины: ${Math.round(box('aside').width)} и ${Math.round(box('main').width)}`); });
test('на ширине 700 колонки друг под другом', async () => { await setWidth(700); assert(box('main').top >= box('aside').bottom - 1, 'Колонки стоят рядом раньше 768px'); });
test('на ширине 700 каждая колонка на всю ширину ряда', async () => { await setWidth(700); assert(Math.abs(box('aside').width - box('main').width) <= 1, 'Ширины колонок различаются'); });
JS,
            'hints' => [
                'Снаружи внутрь: `container`, `row`, затем колонки.',
                'Треть от 12 частей — это 4, две трети — 8. Контрольная точка 768px называется `md`.',
            ],
            'solution' => <<<'HTML'
<div class="container">
  <div class="row">
    <aside class="col-md-4">
      <div class="p-3 bg-light">Разделы</div>
    </aside>
    <main class="col-md-8">
      <div class="p-3 bg-light">Содержимое</div>
    </main>
  </div>
</div>
HTML,
        ],
        [
            'id' => 'nesting-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Перепутанная вложенность',
            'text' => "Две колонки должны стоять рядом, но идут друг под другом, а на узком экране появляется горизонтальная прокрутка. Классы правильные — неправильный порядок вложенности. Исправь.",
            'starter' => <<<'HTML'
<div class="row">
  <div class="container">
    <div class="col-6">
      <div class="p-3 bg-light">Левая</div>
    </div>
    <div class="col-6">
      <div class="p-3 bg-light">Правая</div>
    </div>
  </div>
</div>
HTML,
            'tests' => <<<'JS'
test('структура: .container > .row > две колонки', () => assertEqual($$('.container > .row > .col-6').length, 2));
test('.row не лежит снаружи контейнера', () => assert(!$('.container').parentElement.classList.contains('row'), 'Контейнер не должен быть внутри ряда'));
test('колонки стоят рядом', async () => {
  await setWidth(800);
  const c = $$('.col-6').map((x) => box(x));
  assert(Math.abs(c[0].top - c[1].top) <= 1 && c[1].left >= c[0].right - 1, 'Колонки не в одном ряду');
});
test('колонки равной ширины', async () => { await setWidth(800); const c = $$('.col-6').map((x) => box(x)); assert(Math.abs(c[0].width - c[1].width) <= 1, 'Ширины разные'); });
test('на ширине 360 нет горизонтальной прокрутки', async () => { await setWidth(360); assert(document.documentElement.scrollWidth <= 360, 'Страница шире окна'); });
JS,
            'hints' => [
                'Порядок всегда один: `container` снаружи, в нём `row`, в нём колонки.',
            ],
            'solution' => <<<'HTML'
<div class="container">
  <div class="row">
    <div class="col-6">
      <div class="p-3 bg-light">Левая</div>
    </div>
    <div class="col-6">
      <div class="p-3 bg-light">Правая</div>
    </div>
  </div>
</div>
HTML,
        ],
        [
            'id' => 'product-grid',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Каталог на классах',
            'text' => "В прошлом уроке ты писал каталог с тремя раскладками на медиазапросах. Теперь сделай то же самое одними классами Bootstrap, не написав ни строчки CSS.\n\n| Ширина окна | Столбцов |\n|---|---|\n| меньше 576px | 1 |\n| от 576px | 2 |\n| от 992px | 4 |\n\nПромежутки между карточками — класс `g-3` на ряду. Добавь нужные классы элементам с классом `item` и ряду.",
            'starter' => <<<'HTML'
<div class="container">
  <div class="row">
    <div class="item"><div class="p-3 bg-light rounded">Эфиопия</div></div>
    <div class="item"><div class="p-3 bg-light rounded">Колумбия</div></div>
    <div class="item"><div class="p-3 bg-light rounded">Бразилия</div></div>
    <div class="item"><div class="p-3 bg-light rounded">Кения</div></div>
  </div>
</div>
HTML,
            'tests' => <<<'JS'
const columns = () => new Set($$('.item').map((i) => Math.round(box(i).left))).size;
test('на странице нет своих стилей', () => assertEqual($$('style').length + $$('[style]').length, 0, 'Задание решается только классами'));
test('на ширине 360 — один столбец', async () => { await setWidth(360); assertEqual(columns(), 1); });
test('на ширине 600 — два столбца', async () => { await setWidth(600); assertEqual(columns(), 2); });
test('на ширине 900 — всё ещё два', async () => { await setWidth(900); assertEqual(columns(), 2); });
test('на ширине 1000 — четыре столбца', async () => { await setWidth(1000); assertEqual(columns(), 4); });
test('у ряда есть промежутки g-3', () => assert($('.row').classList.contains('g-3'), 'Добавь ряду класс g-3'));
test('между строками на телефоне есть промежуток', async () => {
  await setWidth(360);
  const items = $$('.item > div').map((i) => box(i));
  assertEqual(Math.round(items[1].top - items[0].bottom), 16);
});
JS,
            'hints' => [
                'Каждой колонке нужны три класса: для телефона, от `sm` и от `lg`.',
                'Один столбец — 12 частей, два — по 6, четыре — по 3: `col-12 col-sm-6 col-lg-3`.',
            ],
            'solution' => <<<'HTML'
<div class="container">
  <div class="row g-3">
    <div class="item col-12 col-sm-6 col-lg-3"><div class="p-3 bg-light rounded">Эфиопия</div></div>
    <div class="item col-12 col-sm-6 col-lg-3"><div class="p-3 bg-light rounded">Колумбия</div></div>
    <div class="item col-12 col-sm-6 col-lg-3"><div class="p-3 bg-light rounded">Бразилия</div></div>
    <div class="item col-12 col-sm-6 col-lg-3"><div class="p-3 bg-light rounded">Кения</div></div>
  </div>
</div>
HTML,
        ],
        [
            'id' => 'utilities',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'bootstrap' => true,
            'title' => 'Шапка на утилитах',
            'text' => "Оформи шапку одними классами-утилитами, без своего CSS:\n\n- шапка — flex-контейнер: элементы разнесены по краям и выровнены по вертикали по центру;\n- внутренний отступ 16px, светлый фон (`bg-light`);\n- часы работы скрыты на телефоне и видны от 768px;\n- ссылка «Заказать» выглядит как основная кнопка.",
            'starter' => <<<'HTML'
<header>
  <strong>Зерно</strong>
  <span class="hours">Ежедневно 8:00–22:00</span>
  <a href="/order">Заказать</a>
</header>
HTML,
            'tests' => <<<'JS'
test('на странице нет своих стилей', () => assertEqual($$('style').length + $$('[style]').length, 0, 'Задание решается только классами'));
test('шапка — flex с разнесением по краям', () => assertEqual([css('header', 'display'), css('header', 'justify-content'), css('header', 'align-items')], ['flex', 'space-between', 'center']));
test('внутренний отступ 16px', () => assertEqual([css('header', 'padding-top'), css('header', 'padding-left')], ['16px', '16px']));
test('светлый фон', () => assert($('header').classList.contains('bg-light'), 'Добавь шапке класс bg-light'));
test('часы работы скрыты на ширине 500', async () => { await setWidth(500); assertEqual(css('.hours', 'display'), 'none'); });
test('часы работы видны на ширине 800', async () => { await setWidth(800); assert(css('.hours', 'display') !== 'none', 'От 768px часы должны быть видны'); });
test('ссылка оформлена как основная кнопка', () => {
  assert($('header a').classList.contains('btn') && $('header a').classList.contains('btn-primary'), 'Нужны классы btn и btn-primary');
});
JS,
            'hints' => [
                'Шапке: `d-flex justify-content-between align-items-center p-3 bg-light`.',
                'Скрыть по умолчанию и показать от `md`: `d-none d-md-block`.',
            ],
            'solution' => <<<'HTML'
<header class="d-flex justify-content-between align-items-center p-3 bg-light">
  <strong>Зерно</strong>
  <span class="hours d-none d-md-block">Ежедневно 8:00–22:00</span>
  <a href="/order" class="btn btn-primary">Заказать</a>
</header>
HTML,
        ],
        [
            'id' => 'own-grid',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Своя сетка без Bootstrap',
            'text' => "Чтобы убедиться, что в сетке нет магии, напиши её упрощённую версию сам. В этом задании Bootstrap **не подключён**.\n\nНужны два класса:\n\n- `.my-row` — ряд: flex-контейнер с переносом;\n- `.my-col-md-6` — колонка: на телефоне занимает всю ширину ряда, от 768px — половину.\n\nДля всех элементов включи `box-sizing: border-box`, а колонкам дай внутренний отступ 12px слева и справа — так в Bootstrap сделаны промежутки.",
            'starter' => <<<'HTML'
<style>

</style>

<div class="my-row">
  <div class="my-col-md-6"><div style="background: rgb(240, 240, 240); padding: 16px">Левая</div></div>
  <div class="my-col-md-6"><div style="background: rgb(240, 240, 240); padding: 16px">Правая</div></div>
</div>
HTML,
            'tests' => <<<'JS'
const cols = () => $$('.my-col-md-6').map((c) => box(c));
test('.my-row — flex-контейнер с переносом', () => assertEqual([css('.my-row', 'display'), css('.my-row', 'flex-wrap')], ['flex', 'wrap']));
test('box-sizing: border-box у колонок', () => assertEqual(css('.my-col-md-6', 'box-sizing'), 'border-box'));
test('у колонок отступы 12px слева и справа', () => assertEqual([css('.my-col-md-6', 'padding-left'), css('.my-col-md-6', 'padding-right')], ['12px', '12px']));
test('на ширине 500 колонки на всю ширину, друг под другом', async () => {
  await setWidth(500);
  const c = cols();
  assert(Math.abs(c[0].width - box('.my-row').width) <= 1, 'Колонка не на всю ширину ряда');
  assert(c[1].top >= c[0].bottom - 1, 'Колонки стоят рядом');
});
test('на ширине 800 колонки по половине ряда, рядом', async () => {
  await setWidth(800);
  const c = cols();
  assert(Math.abs(c[0].width - box('.my-row').width / 2) <= 1, `Ширина колонки ${Math.round(c[0].width)}, а половина ряда — ${Math.round(box('.my-row').width / 2)}`);
  assert(Math.abs(c[0].top - c[1].top) <= 1, 'Колонки не в одном ряду');
});
test('на ширине 800 нет горизонтальной прокрутки', async () => { await setWidth(800); assert(document.documentElement.scrollWidth <= 800, 'Страница шире окна'); });
JS,
            'hints' => [
                '`.my-row { display: flex; flex-wrap: wrap; }`',
                'Базовое правило колонки: `width: 100%; padding: 0 12px;`. В `@media (min-width: 768px)` — `width: 50%;`',
                'Без `box-sizing: border-box` отступы прибавятся к 50%, и две колонки в ряд не поместятся.',
            ],
            'solution' => <<<'HTML'
<style>
  * { box-sizing: border-box; }

  .my-row {
    display: flex;
    flex-wrap: wrap;
  }

  .my-col-md-6 {
    width: 100%;
    padding: 0 12px;
  }

  @media (min-width: 768px) {
    .my-col-md-6 { width: 50%; }
  }
</style>

<div class="my-row">
  <div class="my-col-md-6"><div style="background: rgb(240, 240, 240); padding: 16px">Левая</div></div>
  <div class="my-col-md-6"><div style="background: rgb(240, 240, 240); padding: 16px">Правая</div></div>
</div>
HTML,
        ],
    ],
    'cards' => [
        ['Какие три уровня у сетки Bootstrap и в каком порядке?', '`.container` → `.row` → `.col-*`. Колонки — прямые потомки ряда, ряд лежит в контейнере.'],
        ['Что означает `col-md-4`?', 'От 768px и шире колонка занимает 4 части из 12 (треть). Уже 768px — всю ширину.'],
        ['Как записать «на телефоне один столбец, от 576px два, от 992px четыре»?', '`col-12 col-sm-6 col-lg-3`'],
        ['Как скрыть элемент на телефоне и показать от планшета?', '`d-none d-md-block`'],
        ['Чем отличаются утилиты отступов Bootstrap 5 и 4?', 'В пятой версии «лево/право» — это `s`/`e`: `ms-2`, `pe-3`, `text-end`. В четвёртой — `ml-2`, `pr-3`, `text-right`.'],
        ['Как устроена сетка Bootstrap внутри?', '`.row` — `display: flex; flex-wrap: wrap`. `.col-md-6` — `width: 50%` внутри `@media (min-width: 768px)`.'],
    ],
];
