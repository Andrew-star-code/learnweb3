<?php
return [
    'tasks' => [
        [
            'id' => 'axis-quiz',
            'type' => 'quiz',
            'title' => 'Какая ось',
            'text' => 'У контейнера `display: flex` и направление по умолчанию (в ряд). Каким свойством выровнять элементы по вертикали по центру?',
            'options' => ['justify-content: center', 'align-items: center', 'vertical-align: middle', 'text-align: center'],
            'answer' => 1,
            'explain' => 'В ряду главная ось горизонтальна, поперечная — вертикальна. По поперечной оси выравнивает `align-items`. Если поменять направление на `column`, оси поменяются местами, и по вертикали начнёт двигать `justify-content`.',
        ],
        [
            'id' => 'header',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Шапка сайта',
            'text' => 'Сделай шапку: логотип слева, меню справа, оба выровнены по вертикали по центру. Ссылки меню стоят в ряд с промежутком 16px.',
            'starter' => <<<'HTML'
<style>
  .header {
    display: ___;
    justify-content: ___;
    align-items: ___;
    padding: 12px 16px;
    background: rgb(240, 240, 240);
  }

  .logo { font-size: 28px; }

  .nav {
    display: ___;
    gap: ___;
  }
</style>

<header class="header">
  <strong class="logo">Зерно</strong>
  <nav class="nav">
    <a href="/menu">Меню</a>
    <a href="/delivery">Доставка</a>
    <a href="/contacts">Контакты</a>
  </nav>
</header>
HTML,
            'tests' => <<<'JS'
test('логотип у левого края шапки', async () => { await setWidth(800); assertEqual(Math.round(box('.logo').left - box('.header').left), 16); });
test('меню у правого края шапки', async () => { await setWidth(800); assertEqual(Math.round(box('.header').right - box('.nav').right), 16); });
test('логотип и меню на одной линии по центру', async () => {
  await setWidth(800);
  const a = box('.logo');
  const b = box('.nav');
  assert(Math.abs((a.top + a.bottom) / 2 - (b.top + b.bottom) / 2) <= 1, 'Центры логотипа и меню не совпадают по вертикали');
});
test('между ссылками меню 16px', () => {
  const links = $$('.nav a');
  assertEqual([Math.round(box(links[1]).left - box(links[0]).right), Math.round(box(links[2]).left - box(links[1]).right)], [16, 16]);
});
JS,
            'hints' => [
                'Разнести элементы по краям — `justify-content: space-between`.',
                'По вертикали по центру — `align-items: center`.',
                'Меню — тоже flex-контейнер: `display: flex; gap: 16px;`',
            ],
            'solution' => <<<'HTML'
<style>
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: rgb(240, 240, 240);
  }

  .logo { font-size: 28px; }

  .nav {
    display: flex;
    gap: 16px;
  }
</style>

<header class="header">
  <strong class="logo">Зерно</strong>
  <nav class="nav">
    <a href="/menu">Меню</a>
    <a href="/delivery">Доставка</a>
    <a href="/contacts">Контакты</a>
  </nav>
</header>
HTML,
        ],
        [
            'id' => 'center-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Центрирование, которое не работает',
            'text' => "Зелёный блок должен стоять точно по центру серой области — и по горизонтали, и по вертикали. Автор попробовал `text-align` и `vertical-align`, но они двигают текст и строчные элементы, а не блоки.\n\nЗамени их на то, что работает. Размеры блоков не меняй.",
            'starter' => <<<'HTML'
<style>
  .stage {
    height: 200px;
    background: rgb(235, 235, 235);
    text-align: center;
    vertical-align: middle;
  }

  .box {
    width: 100px;
    height: 60px;
    background: rgb(47, 111, 79);
  }
</style>

<div class="stage">
  <div class="box"></div>
</div>
HTML,
            'tests' => <<<'JS'
test('блок по центру по горизонтали', async () => {
  await setWidth(600);
  const s = box('.stage');
  const b = box('.box');
  assert(Math.abs((s.left + s.right) / 2 - (b.left + b.right) / 2) <= 1, 'Центры не совпадают по горизонтали');
});
test('блок по центру по вертикали', async () => {
  await setWidth(600);
  const s = box('.stage');
  const b = box('.box');
  assert(Math.abs((s.top + s.bottom) / 2 - (b.top + b.bottom) / 2) <= 1, 'Центры не совпадают по вертикали');
});
test('размеры не изменены', () => {
  assertEqual([Math.round(box('.box').width), Math.round(box('.box').height), Math.round(box('.stage').height)], [100, 60, 200]);
});
JS,
            'hints' => [
                'Сделай `.stage` flex-контейнером.',
                'По главной оси центрирует `justify-content: center`, по поперечной — `align-items: center`.',
            ],
            'solution' => <<<'HTML'
<style>
  .stage {
    height: 200px;
    background: rgb(235, 235, 235);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .box {
    width: 100px;
    height: 60px;
    background: rgb(47, 111, 79);
  }
</style>

<div class="stage">
  <div class="box"></div>
</div>
HTML,
        ],
        [
            'id' => 'equal-cards',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Равные колонки',
            'text' => "Поставь три карточки в ряд: одинаковой ширины независимо от длины текста, с промежутком 16px, на всю ширину контейнера.",
            'starter' => <<<'HTML'
<style>
  .cards {

  }

  .card {
    padding: 16px;
    background: rgb(240, 240, 240);
    border-radius: 8px;
  }
</style>

<div class="cards">
  <div class="card">Эспрессо</div>
  <div class="card">Капучино на овсяном молоке с корицей</div>
  <div class="card">Раф</div>
</div>
HTML,
            'tests' => <<<'JS'
const cards = () => $$('.card').map((c) => box(c));
test('карточки стоят в один ряд', async () => { await setWidth(800); const c = cards(); assert(Math.abs(c[0].top - c[1].top) <= 1 && Math.abs(c[1].top - c[2].top) <= 1, 'Карточки не на одной линии'); });
test('ширина карточек одинакова', async () => {
  await setWidth(800);
  const c = cards();
  assert(Math.abs(c[0].width - c[1].width) <= 1 && Math.abs(c[1].width - c[2].width) <= 1, `Ширины разные: ${c.map((x) => Math.round(x.width)).join(', ')}`);
});
test('между карточками 16px', async () => { await setWidth(800); const c = cards(); assertEqual([Math.round(c[1].left - c[0].right), Math.round(c[2].left - c[1].right)], [16, 16]); });
test('ряд занимает всю ширину контейнера', async () => {
  await setWidth(800);
  assert(Math.abs(cards()[2].right - box('.cards').right) <= 1, 'Последняя карточка не доходит до правого края');
});
test('высота карточек одинакова', async () => { await setWidth(800); const c = cards(); assert(Math.abs(c[0].height - c[1].height) <= 1, 'Высоты разные'); });
JS,
            'hints' => [
                'Контейнеру: `display: flex; gap: 16px;`',
                'Каждой карточке: `flex: 1` — они поделят ширину поровну.',
            ],
            'solution' => <<<'HTML'
<style>
  .cards {
    display: flex;
    gap: 16px;
  }

  .card {
    flex: 1;
    padding: 16px;
    background: rgb(240, 240, 240);
    border-radius: 8px;
  }
</style>

<div class="cards">
  <div class="card">Эспрессо</div>
  <div class="card">Капучино на овсяном молоке с корицей</div>
  <div class="card">Раф</div>
</div>
HTML,
        ],
        [
            'id' => 'tags',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Теги не помещаются',
            'text' => "Список тегов свёрстан в ряд. На широком экране всё хорошо, но переключи предпросмотр на 360 — теги уезжают за край. Сделай так, чтобы они переносились на следующую строку, а между ними и по горизонтали, и по вертикали было 8px.",
            'starter' => <<<'HTML'
<style>
  .tags {
    display: flex;
  }

  .tag {
    padding: 4px 12px;
    background: rgb(227, 240, 232);
    border-radius: 12px;
    white-space: nowrap;
  }
</style>

<div class="tags">
  <span class="tag">арабика</span>
  <span class="tag">светлая обжарка</span>
  <span class="tag">Эфиопия</span>
  <span class="tag">фильтр</span>
  <span class="tag">моносорт</span>
  <span class="tag">ягодные ноты</span>
  <span class="tag">новинка</span>
</div>
HTML,
            'tests' => <<<'JS'
const rows = () => new Set($$('.tag').map((t) => Math.round(box(t).top))).size;
test('на ширине 360 теги занимают несколько строк', async () => { await setWidth(360); assert(rows() > 1, 'Теги по-прежнему в одну строку'); });
test('на ширине 360 нет горизонтальной прокрутки', async () => { await setWidth(360); assert(document.documentElement.scrollWidth <= 360, 'Теги вылезают за окно'); });
test('ни один тег не выходит за контейнер', async () => {
  await setWidth(360);
  const right = box('.tags').right;
  assert($$('.tag').every((t) => box(t).right <= right + 1), 'Есть тег за правым краем');
});
test('между тегами в строке 8px', async () => {
  await setWidth(360);
  const tags = $$('.tag');
  assertEqual(Math.round(box(tags[1]).left - box(tags[0]).right), 8);
});
test('между строками 8px', async () => {
  await setWidth(360);
  const tags = $$('.tag').map((t) => box(t));
  const second = tags.find((t) => t.top > tags[0].top + 1);
  assertEqual(Math.round(second.top - tags[0].bottom), 8);
});
JS,
            'hints' => [
                'Перенос включается свойством `flex-wrap: wrap`.',
                '`gap: 8px` задаёт промежутки сразу и между элементами в строке, и между строками.',
            ],
            'solution' => <<<'HTML'
<style>
  .tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .tag {
    padding: 4px 12px;
    background: rgb(227, 240, 232);
    border-radius: 12px;
    white-space: nowrap;
  }
</style>

<div class="tags">
  <span class="tag">арабика</span>
  <span class="tag">светлая обжарка</span>
  <span class="tag">Эфиопия</span>
  <span class="tag">фильтр</span>
  <span class="tag">моносорт</span>
  <span class="tag">ягодные ноты</span>
  <span class="tag">новинка</span>
</div>
HTML,
        ],
        [
            'id' => 'sticky-footer',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Подвал у нижнего края',
            'text' => "Классическая задача: на странице мало содержимого, и подвал «всплывает» к середине окна. Нужно прижать его к нижнему краю.\n\nРазметка готова, стили раскладки напиши с нуля:\n\n- у `body` нет внешних отступов;\n- `.page` — колонка высотой не меньше окна;\n- `main` растягивается и занимает всё свободное место;\n- шапка остаётся вверху, подвал оказывается внизу окна.",
            'starter' => <<<'HTML'
<style>
  header, footer { padding: 12px 16px; background: rgb(47, 111, 79); color: white; }
  main { padding: 16px; }

</style>

<div class="page">
  <header>Кофейня «Зерно»</header>
  <main>Короткая страница.</main>
  <footer>© 2024</footer>
</div>
HTML,
            'tests' => <<<'JS'
test('у body нет внешних отступов', () => assertEqual(css('body', 'margin-top'), '0px'));
test('.page — flex-колонка', () => assertEqual([css('.page', 'display'), css('.page', 'flex-direction')], ['flex', 'column']));
test('шапка у верхнего края окна', () => assertEqual(Math.round(box('header').top), 0));
test('подвал у нижнего края окна', () => assert(Math.abs(box('footer').bottom - window.innerHeight) <= 1, `Низ подвала на ${Math.round(box('footer').bottom)}px, а высота окна ${window.innerHeight}px`));
test('main занимает свободное место', () => assert(box('main').height > 100, 'main не растянулся'));
test('вертикальной прокрутки нет', () => assert(document.documentElement.scrollHeight <= window.innerHeight + 1, 'Страница выше окна — проверь отступы у body'));
JS,
            'hints' => [
                '`body { margin: 0; }` — иначе стандартные 8px добавятся к высоте окна и появится прокрутка.',
                '`.page { display: flex; flex-direction: column; min-height: 100vh; }`',
                'Растянуть содержимое: `main { flex: 1; }`',
            ],
            'solution' => <<<'HTML'
<style>
  header, footer { padding: 12px 16px; background: rgb(47, 111, 79); color: white; }
  main { padding: 16px; }

  body { margin: 0; }

  .page {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  main { flex: 1; }
</style>

<div class="page">
  <header>Кофейня «Зерно»</header>
  <main>Короткая страница.</main>
  <footer>© 2024</footer>
</div>
HTML,
        ],
    ],
    'cards' => [
        ['У какого элемента пишется `display: flex`?', 'У родителя. Его прямые потомки становятся flex-элементами.'],
        ['Чем `justify-content` отличается от `align-items`?', '`justify-content` распределяет по главной оси (в ряду — горизонталь), `align-items` выравнивает по поперечной (в ряду — вертикаль). При `flex-direction: column` оси меняются.'],
        ['Как отцентрировать блок по обеим осям?', 'Родителю: `display: flex; justify-content: center; align-items: center;`'],
        ['Как сделать колонки равной ширины?', 'Контейнеру `display: flex; gap: …`, каждому элементу `flex: 1`.'],
        ['Как прижать подвал к низу окна?', 'Обёртке: `display: flex; flex-direction: column; min-height: 100vh;`, основному содержимому: `flex: 1`.'],
        ['Что делает `flex-wrap: wrap`?', 'Разрешает элементам переноситься на следующую строку, когда они не помещаются.'],
    ],
];
