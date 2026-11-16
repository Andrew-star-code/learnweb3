<?php
return [
    'tasks' => [
        [
            'id' => 'plan',
            'type' => 'plan',
            'title' => 'План вёрстки',
            'text' => "Прежде чем писать код, разбери макет. Посмотри образец в задании ниже и запиши:\n\n- из каких крупных блоков состоит страница, сверху вниз;\n- какой инструмент раскладки нужен каждому блоку (обычный поток, flexbox, grid);\n- что меняется при смене ширины окна.",
            'reference' => <<<'MD'
**Блоки сверху вниз:**

1. Шапка: логотип и навигация.
2. Основное содержимое: заголовок и каталог из шести карточек.
3. Подвал.

**Раскладка:**

| Блок | Инструмент | Почему |
|---|---|---|
| Страница целиком | обычный поток | Блоки просто идут друг под другом |
| Шапка | flexbox | Один ряд: два элемента разнесены по краям |
| Навигация | flexbox | Ссылки в ряд с промежутком |
| Контейнер | `max-width` + `margin: 0 auto` | Центральная колонка |
| Каталог | grid | Карточки ровно по строкам и столбцам |
| Карточка | обычный поток | Заголовок, цена и кнопка друг под другом |

**Что меняется с шириной:** только число столбцов каталога — 1, 2 или 3. Всё остальное резиновое и не требует медиазапросов.

Такой разбор стоит делать для любого макета: когда для каждого блока выбран инструмент, сама вёрстка превращается в набор уже знакомых приёмов.
MD,
        ],
        [
            'id' => 'catalog',
            'type' => 'code',
            'kind' => 'project',
            'lang' => 'html',
            'noSolution' => true,
            'title' => 'Страница каталога',
            'text' => 'Сверстай страницу по требованиям выше. Тесты — чек-лист приёмки.',
            'starter' => <<<'HTML'
<style>

</style>

HTML,
            'tests' => <<<'JS'
const columns = () => new Set($$('.catalog .card').map((c) => Math.round(box(c).left))).size;

test('Разметка: шапка с логотипом «Зерно» и навигацией из трёх ссылок', () => {
  assertEqual(text('header.site-header .logo'), 'Зерно');
  assertEqual($$('header.site-header nav a').map((a) => a.textContent.trim()), ['Каталог', 'Доставка', 'Контакты']);
});
test('Разметка: main.container с единственным h1 «Каталог»', () => {
  assertEqual($$('h1').length, 1);
  assertEqual(text('main.container h1'), 'Каталог');
});
test('Разметка: шесть карточек article.card в .catalog', () => assertEqual($$('main .catalog > article.card').length, 6));
test('Разметка: в каждой карточке h2, .price и кнопка «В корзину»', () => {
  for (const card of $$('.card')) {
    assert(card.querySelector('h2') && card.querySelector('p.price'), 'В карточке нет h2 или p.price');
    const button = card.querySelector('button.btn');
    assert(button && button.type === 'button' && button.textContent.trim() === 'В корзину', 'В карточке нет кнопки «В корзину» с type="button"');
  }
});
test('Разметка: подвал footer.site-footer с копирайтом', () => assert(text('footer.site-footer').includes('©'), 'В подвале нет копирайта'));

test('Общие стили: border-box, body без отступов, шрифт без засечек', () => {
  assertEqual([css('.card', 'box-sizing'), css('.btn', 'box-sizing')], ['border-box', 'border-box']);
  assertEqual(css('body', 'margin-left'), '0px');
  assert(css('body', 'font-family').includes('sans-serif'), 'У body должен быть шрифт sans-serif');
});
test('Общие стили: переменная --accent', () => assertEqual(css(':root', '--accent').replace(/\s/g, ''), 'rgb(47,111,79)'));

test('Шапка: логотип слева, навигация справа, по центру по вертикали', async () => {
  await setWidth(800);
  const h = box('.site-header');
  const logo = box('.site-header .logo');
  const nav = box('.site-header nav');
  assertEqual([Math.round(logo.left - h.left), Math.round(h.right - nav.right)], [16, 16], 'Отступы от краёв шапки');
  assert(Math.abs((logo.top + logo.bottom) / 2 - (nav.top + nav.bottom) / 2) <= 1, 'Логотип и навигация не на одной линии');
});
test('Шапка: между ссылками 16px', async () => {
  await setWidth(800);
  const links = $$('.site-header nav a').map((a) => box(a));
  assertEqual([Math.round(links[1].left - links[0].right), Math.round(links[2].left - links[1].right)], [16, 16]);
});

test('Содержимое: контейнер 960px по центру с отступами 16px', async () => {
  await setWidth(1200);
  const c = box('.container');
  assertEqual(Math.round(c.width), 960);
  assert(Math.abs(c.left - (1200 - c.right)) <= 1, 'Контейнер не по центру');
  assertEqual([css('.container', 'padding-left'), css('.container', 'padding-right')], ['16px', '16px']);
});
test('Содержимое: столбцов 1, 2 и 3 на ширинах 400, 700 и 1100', async () => {
  const result = [];
  for (const width of [400, 700, 1100]) { await setWidth(width); result.push(columns()); }
  assertEqual(result, [1, 2, 3]);
});
test('Содержимое: промежуток в каталоге 16px', async () => {
  await setWidth(1100);
  const cards = $$('.card').map((c) => box(c));
  assertEqual([Math.round(cards[1].left - cards[0].right), Math.round(cards[3].top - cards[0].bottom)], [16, 16]);
});

test('Карточка: отступ 16px, рамка 1px, скругление 8px', () => assertEqual([css('.card', 'padding-top'), css('.card', 'border-top-width'), css('.card', 'border-top-style'), css('.card', 'border-top-left-radius')], ['16px', '1px', 'solid', '8px']));
test('Карточка: кнопка на всю ширину, цвета --accent', async () => {
  await setWidth(700);
  const card = $('.card');
  const inner = box(card).width - 2 * 16 - 2 * 1;
  assert(Math.abs(box(card.querySelector('.btn')).width - inner) <= 1, 'Кнопка не занимает всю ширину карточки');
  assertEqual([css('.btn', 'background-color'), css('.btn', 'color')], ['rgb(47, 111, 79)', 'rgb(255, 255, 255)']);
});
test('Карточка: у кнопки нет рамки, скругление 6px, отступы 10px', () => assertEqual([css('.btn', 'border-top-width'), css('.btn', 'border-top-left-radius'), css('.btn', 'padding-top')], ['0px', '6px', '10px']));

test('Подвал: текст по центру, отступы 16px', () => assertEqual([css('.site-footer', 'text-align'), css('.site-footer', 'padding-top')], ['center', '16px']));
test('Адаптивность: на ширине 360 нет горизонтальной прокрутки', async () => { await setWidth(360); assert(document.documentElement.scrollWidth <= 360, 'Страница шире окна'); });
JS,
            'hints' => [
                '**Каркас разметки:** `<header class="site-header">…</header><main class="container"><h1>Каталог</h1><div class="catalog">…шесть article.card…</div></main><footer class="site-footer">…</footer>`',
                '**Шапка:** `.site-header { display: flex; justify-content: space-between; align-items: center; padding: 16px; }` и `.site-header nav { display: flex; gap: 16px; }`',
                '**Контейнер:** `max-width: 960px; margin: 0 auto; padding: 0 16px;`',
                '**Каталог:** `display: grid; grid-template-columns: 1fr; gap: 16px;` и два медиазапроса с `repeat(2, 1fr)` от 576px и `repeat(3, 1fr)` от 992px.',
                '**Кнопка:** `width: 100%; border: 0; padding: 10px; border-radius: 6px; background: var(--accent); color: rgb(255, 255, 255); font: inherit;`',
            ],
            'solution' => <<<'HTML'
<style>
  * { box-sizing: border-box; }

  :root { --accent: rgb(47, 111, 79); }

  body { margin: 0; font-family: sans-serif; }

  .site-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: rgb(245, 245, 245);
  }

  .logo { font-size: 22px; color: var(--accent); }

  .site-header nav { display: flex; gap: 16px; }

  .container {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 16px;
  }

  .catalog {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
  }

  @media (min-width: 576px) {
    .catalog { grid-template-columns: repeat(2, 1fr); }
  }

  @media (min-width: 992px) {
    .catalog { grid-template-columns: repeat(3, 1fr); }
  }

  .card {
    padding: 16px;
    border: 1px solid rgb(220, 220, 220);
    border-radius: 8px;
  }

  .card h2 { margin: 0 0 8px; font-size: 18px; }

  .price { margin: 0 0 12px; color: rgb(100, 100, 100); }

  .btn {
    width: 100%;
    padding: 10px;
    border: 0;
    border-radius: 6px;
    background: var(--accent);
    color: rgb(255, 255, 255);
    font: inherit;
    cursor: pointer;
  }

  .site-footer {
    margin-top: 24px;
    padding: 16px;
    text-align: center;
    color: rgb(100, 100, 100);
  }
</style>

<header class="site-header">
  <strong class="logo">Зерно</strong>
  <nav>
    <a href="/catalog">Каталог</a>
    <a href="/delivery">Доставка</a>
    <a href="/contacts">Контакты</a>
  </nav>
</header>

<main class="container">
  <h1>Каталог</h1>
  <div class="catalog">
    <article class="card">
      <h2>Эфиопия Иргачеффе</h2>
      <p class="price">890 ₽</p>
      <button type="button" class="btn">В корзину</button>
    </article>
    <article class="card">
      <h2>Колумбия Супремо</h2>
      <p class="price">750 ₽</p>
      <button type="button" class="btn">В корзину</button>
    </article>
    <article class="card">
      <h2>Бразилия Сантос</h2>
      <p class="price">620 ₽</p>
      <button type="button" class="btn">В корзину</button>
    </article>
    <article class="card">
      <h2>Кения АА</h2>
      <p class="price">980 ₽</p>
      <button type="button" class="btn">В корзину</button>
    </article>
    <article class="card">
      <h2>Гватемала Антигуа</h2>
      <p class="price">840 ₽</p>
      <button type="button" class="btn">В корзину</button>
    </article>
    <article class="card">
      <h2>Руанда Бурбон</h2>
      <p class="price">910 ₽</p>
      <button type="button" class="btn">В корзину</button>
    </article>
  </div>
</main>

<footer class="site-footer">© Кофейня «Зерно»</footer>
HTML,
        ],
    ],
    'cards' => [
        ['С чего начинать вёрстку макета?', 'С разбора: блоки сверху вниз, инструмент раскладки для каждого, что меняется с шириной. Затем вся разметка без стилей, затем мобильная раскладка, затем медиазапросы.'],
        ['Какой инструмент раскладки для чего в типичной странице?', 'Шапка и меню — flexbox. Каталог карточек — grid. Центральная колонка — `max-width` и `margin: 0 auto`. Содержимое карточки — обычный поток.'],
    ],
];
