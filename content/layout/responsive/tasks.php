<?php
return [
    'tasks' => [
        [
            'id' => 'mobile-first-quiz',
            'type' => 'quiz',
            'title' => 'Сначала мобильные',
            'text' => 'Что означает подход «mobile first» в коде стилей?',
            'options' => [
                'Для телефонов делается отдельный сайт на отдельном адресе',
                'Базовые стили написаны для узкого экрана, медиазапросы с min-width добавляют изменения для широких',
                'Базовые стили написаны для монитора, медиазапросы с max-width исправляют их для телефонов',
                'Стили для телефона подключаются отдельным файлом раньше остальных',
            ],
            'answer' => 1,
            'explain' => 'Мобильная раскладка проще, и к ней удобно добавлять. При обратном подходе большая часть мобильных правил — это отмена десктопных. На `min-width` построен и Bootstrap: `col-md-6` значит «от средней ширины и выше».',
        ],
        [
            'id' => 'media-fill',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Первый медиазапрос',
            'text' => 'На телефоне карточки должны идти друг под другом, а начиная с ширины 768px — в ряд. Заполни пропуски.',
            'starter' => <<<'HTML'
<style>
  .cards {
    display: flex;
    flex-direction: ___;
    gap: 16px;
  }

  .card { flex: 1; padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }

  @media (min-width: ___) {
    .cards {
      flex-direction: ___;
    }
  }
</style>

<div class="cards">
  <div class="card">Зёрна</div>
  <div class="card">Молотый</div>
  <div class="card">Дрип-пакеты</div>
</div>
HTML,
            'tests' => <<<'JS'
const cards = () => $$('.card').map((c) => box(c));
const stacked = () => { const c = cards(); return Math.abs(c[0].left - c[1].left) <= 1 && c[1].top > c[0].bottom; };
const inRow = () => { const c = cards(); return Math.abs(c[0].top - c[1].top) <= 1 && c[1].left > c[0].right; };
test('на ширине 400 карточки друг под другом', async () => { await setWidth(400); assert(stacked(), 'Карточки не в колонку'); });
test('на ширине 760 — всё ещё друг под другом', async () => { await setWidth(760); assert(stacked(), 'Раскладка переключилась раньше 768px'); });
test('на ширине 780 — в ряд', async () => { await setWidth(780); assert(inRow(), 'Раскладка не переключилась на 768px'); });
test('на ширине 1100 — в ряд', async () => { await setWidth(1100); assert(inRow(), 'Карточки не в ряд'); });
JS,
            'hints' => [
                'Базовое значение — для телефона: `column`.',
                'В медиазапросе: `min-width: 768px` и `flex-direction: row`.',
            ],
            'solution' => <<<'HTML'
<style>
  .cards {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .card { flex: 1; padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }

  @media (min-width: 768px) {
    .cards {
      flex-direction: row;
    }
  }
</style>

<div class="cards">
  <div class="card">Зёрна</div>
  <div class="card">Молотый</div>
  <div class="card">Дрип-пакеты</div>
</div>
HTML,
        ],
        [
            'id' => 'overflow-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Горизонтальная прокрутка',
            'text' => "Статья свёрстана под монитор. Переключи предпросмотр на 360 — появится горизонтальная прокрутка: и колонка, и картинка шире экрана.\n\nИсправь так, чтобы на широком экране всё осталось как было (колонка 700px по центру, картинка 640px), а на узком — сжималось. Пропорции картинки должны сохраняться.",
            'starter' => <<<'HTML'
<style>
  .article {
    width: 700px;
    margin: 0 auto;
  }

  .article img {
    width: 640px;
  }
</style>

<div class="article">
  <h2>Как мы обжариваем кофе</h2>
  <img src="/assets/sample-wide.svg" alt="Обжарочный цех" width="640" height="240">
  <p>Каждая партия обжаривается вручную небольшими порциями.</p>
</div>
HTML,
            'tests' => <<<'JS'
test('на ширине 360 нет горизонтальной прокрутки', async () => { await setWidth(360); assert(document.documentElement.scrollWidth <= 360, 'Страница шире окна'); });
test('на ширине 360 картинка помещается в колонку', async () => { await setWidth(360); assert(box('img').right <= box('.article').right + 1, 'Картинка шире колонки'); });
test('на ширине 360 пропорции картинки сохранены', async () => {
  await setWidth(360);
  const b = box('img');
  assert(Math.abs(b.height / b.width - 240 / 640) < 0.02, `Картинка искажена: ${Math.round(b.width)}×${Math.round(b.height)}`);
});
test('на ширине 1000 колонка 700px по центру', async () => {
  await setWidth(1000);
  const b = box('.article');
  assertEqual(Math.round(b.width), 700);
  assert(Math.abs(b.left - (1000 - b.right)) <= 1, 'Колонка не по центру');
});
test('на ширине 1000 картинка 640×240', async () => { await setWidth(1000); const b = box('img'); assertEqual([Math.round(b.width), Math.round(b.height)], [640, 240]); });
JS,
            'hints' => [
                'Колонке вместо `width` нужна `max-width`.',
                'Картинке: `max-width: 100%;` вместо жёсткой ширины.',
                'Чтобы высота уменьшалась вместе с шириной: `height: auto;`',
            ],
            'solution' => <<<'HTML'
<style>
  .article {
    max-width: 700px;
    margin: 0 auto;
  }

  .article img {
    max-width: 100%;
    height: auto;
  }
</style>

<div class="article">
  <h2>Как мы обжариваем кофе</h2>
  <img src="/assets/sample-wide.svg" alt="Обжарочный цех" width="640" height="240">
  <p>Каждая партия обжаривается вручную небольшими порциями.</p>
</div>
HTML,
        ],
        [
            'id' => 'nav',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Меню для телефона и для монитора',
            'text' => "Допиши стили шапки по принципу «сначала мобильные», контрольная точка — 600px.\n\n**Уже 600px:** кнопка «Меню» видна, ссылки идут друг под другом.\n\n**От 600px:** кнопка скрыта, ссылки стоят в ряд с промежутком 16px.\n\nОткрывать и закрывать меню по кнопке мы научимся в блоке про JavaScript в браузере — здесь только раскладка.",
            'starter' => <<<'HTML'
<style>
  .top { padding: 12px 16px; background: rgb(240, 240, 240); }

</style>

<header class="top">
  <button type="button" class="menu-button">Меню</button>
  <nav class="links">
    <a href="/menu">Меню</a>
    <a href="/delivery">Доставка</a>
    <a href="/contacts">Контакты</a>
  </nav>
</header>
HTML,
            'tests' => <<<'JS'
const links = () => $$('.links a').map((a) => box(a));
test('на ширине 400 кнопка «Меню» видна', async () => { await setWidth(400); assert(css('.menu-button', 'display') !== 'none', 'Кнопка скрыта'); });
test('на ширине 400 ссылки друг под другом', async () => {
  await setWidth(400);
  const l = links();
  assert(l[1].top >= l[0].bottom && l[2].top >= l[1].bottom, 'Ссылки не в колонку');
  assert(Math.abs(l[0].left - l[1].left) <= 1, 'Ссылки не выровнены по левому краю');
});
test('на ширине 800 кнопка скрыта', async () => { await setWidth(800); assertEqual(css('.menu-button', 'display'), 'none'); });
test('на ширине 800 ссылки в ряд', async () => { await setWidth(800); const l = links(); assert(Math.abs(l[0].top - l[1].top) <= 1 && Math.abs(l[1].top - l[2].top) <= 1, 'Ссылки не в одну строку'); });
test('на ширине 800 между ссылками 16px', async () => { await setWidth(800); const l = links(); assertEqual([Math.round(l[1].left - l[0].right), Math.round(l[2].left - l[1].right)], [16, 16]); });
test('контрольная точка — 600px', async () => {
  await setWidth(590);
  assert(css('.menu-button', 'display') !== 'none', 'На 590px кнопка уже скрыта');
  await setWidth(610);
  assertEqual(css('.menu-button', 'display'), 'none');
});
JS,
            'hints' => [
                'Базовые стили: `.links { display: flex; flex-direction: column; }`',
                'Внутри `@media (min-width: 600px)`: `.menu-button { display: none; }` и `.links { flex-direction: row; gap: 16px; }`',
            ],
            'solution' => <<<'HTML'
<style>
  .top { padding: 12px 16px; background: rgb(240, 240, 240); }

  .links {
    display: flex;
    flex-direction: column;
  }

  @media (min-width: 600px) {
    .menu-button { display: none; }

    .links {
      flex-direction: row;
      gap: 16px;
    }
  }
</style>

<header class="top">
  <button type="button" class="menu-button">Меню</button>
  <nav class="links">
    <a href="/menu">Меню</a>
    <a href="/delivery">Доставка</a>
    <a href="/contacts">Контакты</a>
  </nav>
</header>
HTML,
        ],
        [
            'id' => 'products',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Каталог на трёх экранах',
            'text' => "Разметка готова, раскладку напиши с нуля. Количество столбцов зависит от ширины окна:\n\n| Ширина окна | Столбцов |\n|---|---|\n| меньше 576px | 1 |\n| от 576px | 2 |\n| от 992px | 4 |\n\nСтолбцы равной ширины, промежуток 16px. Это те же контрольные точки, что у Bootstrap, — в следующем уроке ты увидишь, как он делает то же самое готовыми классами.",
            'starter' => <<<'HTML'
<style>
  .product { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }

</style>

<div class="products">
  <div class="product">Эфиопия</div>
  <div class="product">Колумбия</div>
  <div class="product">Бразилия</div>
  <div class="product">Кения</div>
  <div class="product">Гватемала</div>
  <div class="product">Перу</div>
  <div class="product">Руанда</div>
  <div class="product">Индия</div>
</div>
HTML,
            'tests' => <<<'JS'
const columns = () => new Set($$('.product').map((p) => Math.round(box(p).left))).size;
test('на ширине 360 — один столбец', async () => { await setWidth(360); assertEqual(columns(), 1); });
test('на ширине 560 — всё ещё один', async () => { await setWidth(560); assertEqual(columns(), 1); });
test('на ширине 600 — два столбца', async () => { await setWidth(600); assertEqual(columns(), 2); });
test('на ширине 980 — всё ещё два', async () => { await setWidth(980); assertEqual(columns(), 2); });
test('на ширине 1000 — четыре столбца', async () => { await setWidth(1000); assertEqual(columns(), 4); });
test('столбцы равной ширины с промежутком 16px', async () => {
  await setWidth(1000);
  const p = $$('.product').map((x) => box(x));
  assert(Math.abs(p[0].width - p[3].width) <= 1, 'Столбцы разной ширины');
  assertEqual(Math.round(p[1].left - p[0].right), 16);
});
test('на ширине 360 нет горизонтальной прокрутки', async () => { await setWidth(360); assert(document.documentElement.scrollWidth <= 360, 'Страница шире окна'); });
JS,
            'hints' => [
                'Базовый вариант: `.products { display: grid; grid-template-columns: 1fr; gap: 16px; }`',
                'Два медиазапроса с `min-width: 576px` и `min-width: 992px`, в каждом меняется только `grid-template-columns`.',
            ],
            'solution' => <<<'HTML'
<style>
  .product { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }

  .products {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
  }

  @media (min-width: 576px) {
    .products { grid-template-columns: repeat(2, 1fr); }
  }

  @media (min-width: 992px) {
    .products { grid-template-columns: repeat(4, 1fr); }
  }
</style>

<div class="products">
  <div class="product">Эфиопия</div>
  <div class="product">Колумбия</div>
  <div class="product">Бразилия</div>
  <div class="product">Кения</div>
  <div class="product">Гватемала</div>
  <div class="product">Перу</div>
  <div class="product">Руанда</div>
  <div class="product">Индия</div>
</div>
HTML,
        ],
    ],
    'cards' => [
        ['Какие две строчки не дают картинке вылезти за экран?', '`img { max-width: 100%; height: auto; }`'],
        ['Что такое «mobile first»?', 'Базовые стили — для узкого экрана, медиазапросы с `min-width` добавляют изменения для более широких.'],
        ['Зачем нужен `<meta name="viewport" content="width=device-width, initial-scale=1">`?', 'Без него телефон показывает страницу уменьшенной, как на широком экране, и медиазапросы не срабатывают как задумано.'],
        ['Назови контрольные точки Bootstrap 5.', '`sm` — 576px, `md` — 768px, `lg` — 992px, `xl` — 1200px.'],
        ['Какой главный признак ошибки адаптивной вёрстки?', 'Горизонтальная полоса прокрутки на узком экране: какой-то элемент шире окна (жёсткая `width`, картинка без `max-width`).'],
    ],
];
