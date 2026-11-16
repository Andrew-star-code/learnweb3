<?php
return [
    'tasks' => [
        [
            'id' => 'choice-quiz',
            'type' => 'quiz',
            'title' => 'Grid или Flexbox',
            'text' => 'Нужно сверстать каталог: двенадцать карточек товаров в четыре столбца, все столбцы одной ширины, карточки ровно друг под другом. Что подойдёт лучше?',
            'options' => ['Flexbox с flex-wrap', 'Grid', 'Таблица <table>', 'display: inline-block у карточек'],
            'answer' => 1,
            'explain' => 'Выравнивание и по строкам, и по столбцам — двумерная задача, это Grid. Flexbox с переносом тоже справится, но последняя неполная строка в нём растянется или потребует ухищрений. Таблицы — для табличных данных, а не для раскладки.',
        ],
        [
            'id' => 'columns',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Три равных столбца',
            'text' => 'Разложи шесть карточек в три равных столбца с промежутком 20px.',
            'starter' => <<<'HTML'
<style>
  .catalog {
    display: ___;
    grid-template-columns: ___;
    gap: ___;
  }

  .item { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }
</style>

<div class="catalog">
  <div class="item">Эспрессо</div>
  <div class="item">Американо</div>
  <div class="item">Капучино</div>
  <div class="item">Латте</div>
  <div class="item">Раф</div>
  <div class="item">Флэт уайт</div>
</div>
HTML,
            'tests' => <<<'JS'
const items = () => $$('.item').map((i) => box(i));
const columns = () => new Set(items().map((b) => Math.round(b.left))).size;
test('три столбца', async () => { await setWidth(800); assertEqual(columns(), 3); });
test('две строки', async () => { await setWidth(800); assertEqual(new Set(items().map((b) => Math.round(b.top))).size, 2); });
test('столбцы одинаковой ширины', async () => {
  await setWidth(800);
  const b = items();
  assert(Math.abs(b[0].width - b[1].width) <= 1 && Math.abs(b[1].width - b[2].width) <= 1, 'Ширины столбцов разные');
});
test('промежуток 20px между столбцами и между строками', async () => {
  await setWidth(800);
  const b = items();
  assertEqual([Math.round(b[1].left - b[0].right), Math.round(b[3].top - b[0].bottom)], [20, 20]);
});
JS,
            'hints' => [
                'Три равных столбца: `1fr 1fr 1fr` или короче `repeat(3, 1fr)`.',
            ],
            'solution' => <<<'HTML'
<style>
  .catalog {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }

  .item { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }
</style>

<div class="catalog">
  <div class="item">Эспрессо</div>
  <div class="item">Американо</div>
  <div class="item">Капучино</div>
  <div class="item">Латте</div>
  <div class="item">Раф</div>
  <div class="item">Флэт уайт</div>
</div>
HTML,
        ],
        [
            'id' => 'gallery-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Галерея с жёсткими столбцами',
            'text' => "Галерея задана тремя столбцами по 300px. На экране уже 950px она вылезает за край — переключи ширину предпросмотра и убедись.\n\nПеределай сетку так, чтобы столбцов помещалось столько, сколько влезает: каждый не уже 200px, свободное место делится поровну. Медиазапросы не нужны.",
            'starter' => <<<'HTML'
<style>
  .gallery {
    display: grid;
    grid-template-columns: 300px 300px 300px;
    gap: 16px;
  }

  .photo { height: 100px; background: rgb(227, 240, 232); border-radius: 8px; }
</style>

<div class="gallery">
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
</div>
HTML,
            'tests' => <<<'JS'
const columns = () => new Set($$('.photo').map((p) => Math.round(box(p).left))).size;
const fits = (width) => document.documentElement.scrollWidth <= width;
test('на ширине 360 — один столбец, без прокрутки', async () => { await setWidth(360); assertEqual(columns(), 1); assert(fits(360), 'Галерея шире окна'); });
test('на ширине 800 — три столбца, без прокрутки', async () => { await setWidth(800); assertEqual(columns(), 3); assert(fits(800), 'Галерея шире окна'); });
test('на ширине 1100 — пять столбцов', async () => { await setWidth(1100); assertEqual(columns(), 5); });
test('столбцы заполняют всю ширину', async () => {
  await setWidth(800);
  const photos = $$('.photo');
  assert(Math.abs(box(photos[2]).right - box('.gallery').right) <= 1, 'Справа осталось пустое место');
});
test('промежуток 16px', async () => { await setWidth(800); const p = $$('.photo'); assertEqual(Math.round(box(p[1]).left - box(p[0]).right), 16); });
JS,
            'hints' => [
                'Нужна формула из урока: `repeat(auto-fit, minmax(…, …))`.',
                '`grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));`',
            ],
            'solution' => <<<'HTML'
<style>
  .gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }

  .photo { height: 100px; background: rgb(227, 240, 232); border-radius: 8px; }
</style>

<div class="gallery">
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
  <div class="photo"></div>
</div>
HTML,
        ],
        [
            'id' => 'page-areas',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Раскладка страницы',
            'text' => "Разложи страницу сеткой:\n\n- шапка — на всю ширину сверху;\n- под ней слева боковая панель шириной 200px, справа — основное содержимое на всё оставшееся место;\n- подвал — на всю ширину снизу;\n- промежутки между областями — 16px.\n\nУдобнее всего через именованные области.",
            'starter' => <<<'HTML'
<style>
  .page {

  }

  .page > * { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }
</style>

<div class="page">
  <header>Шапка</header>
  <aside>Разделы</aside>
  <main>Содержимое</main>
  <footer>Подвал</footer>
</div>
HTML,
            'tests' => <<<'JS'
const b = (selector) => box('.page > ' + selector);
test('.page — grid-контейнер', () => assertEqual(css('.page', 'display'), 'grid'));
test('шапка на всю ширину', async () => { await setWidth(800); assert(Math.abs(b('header').width - box('.page').width) <= 1, 'Шапка уже контейнера'); });
test('боковая панель шириной 200px', async () => { await setWidth(800); assertEqual(Math.round(b('aside').width), 200); });
test('содержимое справа от панели, на одной линии с ней', async () => {
  await setWidth(800);
  assertEqual(Math.round(b('main').left - b('aside').right), 16);
  assert(Math.abs(b('main').top - b('aside').top) <= 1, 'main и aside начинаются на разной высоте');
});
test('содержимое доходит до правого края', async () => { await setWidth(800); assert(Math.abs(b('main').right - box('.page').right) <= 1, 'main не дотянулся до края'); });
test('панель под шапкой с промежутком 16px', async () => { await setWidth(800); assertEqual(Math.round(b('aside').top - b('header').bottom), 16); });
test('подвал внизу на всю ширину', async () => {
  await setWidth(800);
  assert(b('footer').top >= b('aside').bottom, 'Подвал не ниже боковой панели');
  assert(Math.abs(b('footer').width - box('.page').width) <= 1, 'Подвал уже контейнера');
});
JS,
            'hints' => [
                'Столбцы: `grid-template-columns: 200px 1fr;`',
                "Области рисуются строками: `grid-template-areas: \"header header\" \"sidebar main\" \"footer footer\";`",
                'Каждому элементу назначь свою область: `.page header { grid-area: header; }` и так далее.',
            ],
            'solution' => <<<'HTML'
<style>
  .page {
    display: grid;
    grid-template-columns: 200px 1fr;
    grid-template-areas:
      "header  header"
      "sidebar main"
      "footer  footer";
    gap: 16px;
  }

  .page header { grid-area: header; }
  .page aside  { grid-area: sidebar; }
  .page main   { grid-area: main; }
  .page footer { grid-area: footer; }

  .page > * { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }
</style>

<div class="page">
  <header>Шапка</header>
  <aside>Разделы</aside>
  <main>Содержимое</main>
  <footer>Подвал</footer>
</div>
HTML,
        ],
        [
            'id' => 'stats',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Плитки статистики',
            'text' => "Разметка готова, раскладку напиши с нуля — образец на соседней вкладке.\n\n- `.stats` — сетка из двух равных столбцов с промежутком 12px.\n- Главная плитка `.tile-main` занимает оба столбца.\n- Остальные три плитки встают по порядку: две во второй строке, одна в третьей.",
            'starter' => <<<'HTML'
<style>
  .tile { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }
  .tile-main { background: rgb(47, 111, 79); color: white; }

</style>

<div class="stats">
  <div class="tile tile-main">Выручка за месяц: 412 000 ₽</div>
  <div class="tile">Заказов: 318</div>
  <div class="tile">Средний чек: 1 295 ₽</div>
  <div class="tile">Новых клиентов: 47</div>
</div>
HTML,
            'tests' => <<<'JS'
const tiles = () => $$('.tile').map((t) => box(t));
test('.stats — grid-контейнер', () => assertEqual(css('.stats', 'display'), 'grid'));
test('главная плитка на всю ширину', async () => { await setWidth(600); assert(Math.abs(tiles()[0].width - box('.stats').width) <= 1, 'Главная плитка не занимает оба столбца'); });
test('вторая и третья плитки стоят рядом', async () => { await setWidth(600); const t = tiles(); assert(Math.abs(t[1].top - t[2].top) <= 1 && t[2].left > t[1].right, 'Плитки не в одной строке'); });
test('столбцы равной ширины', async () => { await setWidth(600); const t = tiles(); assert(Math.abs(t[1].width - t[2].width) <= 1, 'Столбцы разной ширины'); });
test('промежутки 12px', async () => { await setWidth(600); const t = tiles(); assertEqual([Math.round(t[2].left - t[1].right), Math.round(t[1].top - t[0].bottom)], [12, 12]); });
test('четвёртая плитка в третьей строке, в первом столбце', async () => {
  await setWidth(600);
  const t = tiles();
  assert(t[3].top > t[1].bottom && Math.abs(t[3].left - t[1].left) <= 1, 'Четвёртая плитка не на своём месте');
  assert(Math.abs(t[3].width - t[1].width) <= 1, 'Четвёртая плитка должна занимать один столбец');
});
JS,
            'hints' => [
                '`.stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }`',
                'Растянуть плитку на два столбца: `grid-column: span 2;`',
            ],
            'solution' => <<<'HTML'
<style>
  .tile { padding: 16px; background: rgb(240, 240, 240); border-radius: 8px; }
  .tile-main { background: rgb(47, 111, 79); color: white; }

  .stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .tile-main { grid-column: span 2; }
</style>

<div class="stats">
  <div class="tile tile-main">Выручка за месяц: 412 000 ₽</div>
  <div class="tile">Заказов: 318</div>
  <div class="tile">Средний чек: 1 295 ₽</div>
  <div class="tile">Новых клиентов: 47</div>
</div>
HTML,
        ],
    ],
    'cards' => [
        ['Что означает `1fr` в grid?', 'Одну долю свободного места, оставшегося после фиксированных столбцов и промежутков. `1fr 1fr` — два равных столбца.'],
        ['Как сделать адаптивную сетку без медиазапросов?', '`grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));` — столько столбцов, сколько влезет, каждый не уже 200px.'],
        ['Как растянуть элемент сетки на два столбца? На всю ширину?', '`grid-column: span 2;` и `grid-column: 1 / -1;`'],
        ['Когда брать Grid, а когда Flexbox?', 'Flexbox — один ряд или колонка: меню, кнопки, шапка. Grid — сетка со строками и столбцами: каталог, раскладка страницы. Часто используются вместе.'],
        ['Как нарисовать раскладку страницы именованными областями?', '`grid-template-areas: "header header" "sidebar main" "footer footer";` у контейнера и `grid-area: имя` у каждого элемента.'],
    ],
];
