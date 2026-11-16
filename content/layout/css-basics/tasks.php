<?php
return [
    'tasks' => [
        [
            'id' => 'specificity-quiz',
            'type' => 'quiz',
            'title' => 'Кто победит',
            'text' => "```css\n#title { color: red; }\n.title.big { color: blue; }\nh1 { color: green; }\n```\n\n```html\n<h1 id=\"title\" class=\"title big\">Заголовок</h1>\n```\n\nКакого цвета будет заголовок?",
            'options' => ['Красного', 'Синего', 'Зелёного', 'Зелёного — это правило написано последним'],
            'answer' => 0,
            'explain' => 'Один `id` сильнее любого количества классов, а класс сильнее тега. Порядок правил важен только при равной специфичности. Именно поэтому `id` в селекторах избегают: такое правило потом трудно перебить.',
        ],
        [
            'id' => 'selectors',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Подобрать селекторы',
            'text' => 'Объявления уже написаны. Замени каждый `___` на селектор, который подходит под комментарий справа.',
            'starter' => <<<'HTML'
<style>
  ___ { color: rgb(0, 100, 0); }            /* все заголовки h2 */
  ___ { background: rgb(255, 240, 200); }   /* элементы с классом note */
  ___ { font-weight: 700; }                 /* ссылки внутри nav */
  ___ { color: rgb(120, 120, 120); }        /* абзацы, лежащие прямо в footer */
</style>

<nav><a href="/">Главная</a> <a href="/blog">Блог</a></nav>

<h2>Новости</h2>
<p class="note">Важное объявление.</p>
<p>Обычный абзац со <a href="/more">ссылкой</a>.</p>

<footer>
  <p>Контакты</p>
  <div><p>Вложенный абзац</p></div>
</footer>
HTML,
            'tests' => <<<'JS'
test('h2 тёмно-зелёный', () => assertEqual(css('h2', 'color'), 'rgb(0, 100, 0)'));
test('у .note жёлтый фон', () => assertEqual(css('.note', 'background-color'), 'rgb(255, 240, 200)'));
test('у обычного абзаца фона нет', () => assertEqual(css('p:not(.note)', 'background-color'), 'rgba(0, 0, 0, 0)'));
test('ссылки в nav жирные', () => assertEqual(css('nav a', 'font-weight'), '700'));
test('ссылка вне nav обычная', () => assertEqual(css('p a', 'font-weight'), '400'));
test('абзац прямо в footer серый', () => assertEqual(css('footer > p', 'color'), 'rgb(120, 120, 120)'));
test('вложенный глубже абзац в footer не серый', () => assert(css('footer div p', 'color') !== 'rgb(120, 120, 120)', 'Нужен селектор прямого потомка'));
JS,
            'hints' => [
                'По тегу — `h2`, по классу — `.note`.',
                'Вложенность записывается через пробел: `nav a`.',
                '«Прямо в» — это знак `>`: `footer > p`.',
            ],
            'solution' => <<<'HTML'
<style>
  h2 { color: rgb(0, 100, 0); }
  .note { background: rgb(255, 240, 200); }
  nav a { font-weight: 700; }
  footer > p { color: rgb(120, 120, 120); }
</style>

<nav><a href="/">Главная</a> <a href="/blog">Блог</a></nav>

<h2>Новости</h2>
<p class="note">Важное объявление.</p>
<p>Обычный абзац со <a href="/more">ссылкой</a>.</p>

<footer>
  <p>Контакты</p>
  <div><p>Вложенный абзац</p></div>
</footer>
HTML,
        ],
        [
            'id' => 'specificity-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Война приоритетов',
            'text' => "Кнопка «Удалить» должна быть красной, но остаётся серой: правило `.danger` проигрывает. Предыдущий разработчик «починил» это через `!important`.\n\nСделай по-человечески: убери `!important` и селектор по `id`, чтобы оба правила были одинаковой силы и побеждало нижнее. Разметку менять не нужно.",
            'starter' => <<<'HTML'
<style>
  #actions button { background: rgb(230, 230, 230); border: 0; padding: 8px 16px; }
  .danger { background: rgb(200, 30, 30) !important; color: white; }
</style>

<div id="actions">
  <button type="button" class="btn">Отмена</button>
  <button type="button" class="btn danger">Удалить</button>
</div>
HTML,
            'tests' => <<<'JS'
test('«Отмена» серая', () => assertEqual(css('.btn:not(.danger)', 'background-color'), 'rgb(230, 230, 230)'));
test('«Удалить» красная', () => assertEqual(css('.danger', 'background-color'), 'rgb(200, 30, 30)'));
test('у кнопок остались отступы', () => assertEqual(css('.btn', 'padding-left'), '16px'));
test('в стилях нет !important', () => assert(!$('style').textContent.includes('!important'), 'Убери !important'));
test('в стилях нет селектора по id', () => assert(!$('style').textContent.includes('#actions'), 'Замени #actions button на селектор по классу'));
JS,
            'hints' => [
                'У обеих кнопок есть класс `btn`. Общие стили можно повесить на него: `.btn { ... }`.',
                'Теперь `.btn` и `.danger` — по одному классу, силы равны. Побеждает то, что ниже, — `.danger`. `!important` больше не нужен.',
            ],
            'solution' => <<<'HTML'
<style>
  .btn { background: rgb(230, 230, 230); border: 0; padding: 8px 16px; }
  .danger { background: rgb(200, 30, 30); color: white; }
</style>

<div id="actions">
  <button type="button" class="btn">Отмена</button>
  <button type="button" class="btn danger">Удалить</button>
</div>
HTML,
        ],
        [
            'id' => 'pseudo',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Псевдоклассы',
            'text' => "Оформи список дел, не меняя разметку и не добавляя классов:\n\n- чётные по счёту пункты — фон `rgb(240, 240, 240)`;\n- первый пункт — жирный (`font-weight: 700`);\n- выполненные (класс `done`) — зачёркнуты: `text-decoration: line-through`;\n- невыполненные — цвет текста `rgb(0, 0, 0)`, выполненные — `rgb(150, 150, 150)`.",
            'starter' => <<<'HTML'
<style>

</style>

<ul class="todo">
  <li>Купить зёрна</li>
  <li class="done">Помыть кофемашину</li>
  <li>Заказать фильтры</li>
  <li class="done">Оплатить аренду</li>
</ul>
HTML,
            'tests' => <<<'JS'
const items = () => $$('.todo li');
test('разметка не изменена', () => {
  assertEqual(items().length, 4);
  assertEqual(items().map((li) => li.className), ['', 'done', '', 'done']);
});
test('чётные пункты с серым фоном', () => assertEqual([css(items()[1], 'background-color'), css(items()[3], 'background-color')], ['rgb(240, 240, 240)', 'rgb(240, 240, 240)']));
test('у нечётных фона нет', () => assertEqual(css(items()[0], 'background-color'), 'rgba(0, 0, 0, 0)'));
test('первый пункт жирный, остальные нет', () => assertEqual([css(items()[0], 'font-weight'), css(items()[2], 'font-weight')], ['700', '400']));
test('выполненные зачёркнуты', () => assert(css(items()[1], 'text-decoration').includes('line-through'), 'Нет зачёркивания у .done'));
test('невыполненные не зачёркнуты', () => assert(!css(items()[0], 'text-decoration').includes('line-through'), 'Зачёркнут лишний пункт'));
test('цвета текста', () => assertEqual([css(items()[0], 'color'), css(items()[1], 'color')], ['rgb(0, 0, 0)', 'rgb(150, 150, 150)']));
JS,
            'hints' => [
                'Чётные: `.todo li:nth-child(even)`. Первый: `.todo li:first-child`.',
                'Выполненные: `.todo .done`. Невыполненные: `.todo li:not(.done)`.',
            ],
            'solution' => <<<'HTML'
<style>
  .todo li:nth-child(even) { background: rgb(240, 240, 240); }
  .todo li:first-child { font-weight: 700; }
  .todo li:not(.done) { color: rgb(0, 0, 0); }
  .todo .done { text-decoration: line-through; color: rgb(150, 150, 150); }
</style>

<ul class="todo">
  <li>Купить зёрна</li>
  <li class="done">Помыть кофемашину</li>
  <li>Заказать фильтры</li>
  <li class="done">Оплатить аренду</li>
</ul>
HTML,
        ],
        [
            'id' => 'variables',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Переменные и наследование',
            'text' => "Разметка готова, стилей нет. Напиши их с нуля:\n\n- в `:root` объяви переменную `--accent` со значением `rgb(47, 111, 79)`;\n- для `body`: шрифт без засечек (`sans-serif`) и размер `18px` — абзац и кнопка должны получить их по наследству, а не отдельными правилами;\n- цвет заголовка `.title` и фон кнопки `.button` берутся из переменной;\n- у кнопки белый текст (`rgb(255, 255, 255)`), а шрифт наследуется: `font: inherit`.\n\nКнопки по умолчанию шрифт не наследуют — браузер задаёт им свой. Поэтому `font: inherit` для них пишут почти всегда.",
            'starter' => <<<'HTML'
<style>

</style>

<h2 class="title">Подписка на кофе</h2>
<p>Свежие зёрна каждую неделю.</p>
<button type="button" class="button">Оформить</button>
HTML,
            'tests' => <<<'JS'
test('переменная --accent объявлена в :root', () => assertEqual(css(':root', '--accent').replace(/\s/g, ''), 'rgb(47,111,79)'));
test('в правилах используется var(--accent)', () => assert(($('style').textContent.match(/var\(\s*--accent\s*\)/g) || []).length >= 2, 'Цвет заголовка и фон кнопки должны браться из переменной'));
test('шрифт страницы без засечек, 18px', () => {
  assert(css('body', 'font-family').includes('sans-serif'), 'У body должен быть шрифт sans-serif');
  assertEqual(css('body', 'font-size'), '18px');
});
test('абзац наследует размер шрифта', () => assertEqual(css('p', 'font-size'), '18px'));
test('заголовок цвета --accent', () => assertEqual(css('.title', 'color'), 'rgb(47, 111, 79)'));
test('кнопка: фон --accent, белый текст', () => assertEqual([css('.button', 'background-color'), css('.button', 'color')], ['rgb(47, 111, 79)', 'rgb(255, 255, 255)']));
test('кнопка наследует шрифт', () => {
  assertEqual(css('.button', 'font-size'), '18px');
  assert(css('.button', 'font-family').includes('sans-serif'), 'Кнопке нужен font: inherit');
});
JS,
            'hints' => [
                'Объявление: `:root { --accent: rgb(47, 111, 79); }`. Использование: `color: var(--accent);`',
                'Шрифт задаётся один раз: `body { font-family: sans-serif; font-size: 18px; }`.',
            ],
            'solution' => <<<'HTML'
<style>
  :root { --accent: rgb(47, 111, 79); }
  body { font-family: sans-serif; font-size: 18px; }
  .title { color: var(--accent); }
  .button { font: inherit; background: var(--accent); color: rgb(255, 255, 255); }
</style>

<h2 class="title">Подписка на кофе</h2>
<p>Свежие зёрна каждую неделю.</p>
<button type="button" class="button">Оформить</button>
HTML,
        ],
    ],
    'cards' => [
        ['В каком порядке браузер выбирает победившее правило CSS?', 'Сначала специфичность (id > класс > тег), при равной — порядок (ниже побеждает). `!important` перебивает всё.'],
        ['Почему в селекторах избегают `#id` и `!important`?', 'Их почти невозможно перебить обычным правилом — приходится наращивать селекторы или добавлять новые `!important`.'],
        ['Чем `.card p` отличается от `.card > p`?', 'Первый выбирает `p` на любой глубине внутри `.card`, второй — только прямых потомков.'],
        ['Какие свойства наследуются от родителя?', 'Свойства текста: `color`, `font-family`, `font-size`, `line-height`. Отступы, рамки, фон и размеры — нет.'],
        ['Как объявить и использовать переменную CSS?', 'Объявить в `:root { --accent: #2f6f4f; }`, использовать через `var(--accent)`.'],
    ],
];
