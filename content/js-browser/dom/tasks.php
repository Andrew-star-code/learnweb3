<?php
return [
    'tasks' => [
        [
            'id' => 'xss-quiz',
            'type' => 'quiz',
            'title' => 'Комментарий пользователя',
            'text' => "Нужно показать на странице комментарий, который человек ввёл в форму. Текст лежит в переменной `comment`. Какая строка безопасна?",
            'options' => [
                'box.innerHTML = comment;',
                'box.textContent = comment;',
                "box.innerHTML = '<p>' + comment + '</p>';",
                'Все три одинаково безопасны',
            ],
            'answer' => 1,
            'explain' => '`textContent` вставляет строку как обычный текст. `innerHTML` разбирает её как разметку: если в комментарии окажется тег со скриптом, он выполнится у каждого, кто откроет страницу. Обёртка в `<p>` ничего не меняет.',
        ],
        [
            'id' => 'change',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Найти и изменить',
            'text' => 'Допиши скрипт: он меняет текст заголовка на «Каталог кофе», а абзацу со статусом ставит текст «Готово» и добавляет класс `ok`.',
            'starter' => <<<'HTML'
<style>
  .ok { color: rgb(35, 128, 74); font-weight: 700; }
</style>

<h1 class="title">Старый заголовок</h1>
<p class="status">Ожидание…</p>

<script>
  const title = document.___('.title');
  title.___ = 'Каталог кофе';

  const status = document.querySelector('.status');
  status.textContent = ___;
  status.classList.___('ok');
</script>
HTML,
            'tests' => <<<'JS'
test('текст заголовка изменён', () => assertEqual(text('.title'), 'Каталог кофе'));
test('текст статуса — «Готово»', () => assertEqual(text('.status'), 'Готово'));
test('у статуса появился класс ok', () => assert($('.status').classList.contains('ok'), 'Нет класса ok'));
test('статус стал зелёным', () => assertEqual(css('.status', 'color'), 'rgb(35, 128, 74)'));
JS,
            'hints' => [
                'Найти один элемент — `querySelector`. Текст элемента — свойство `textContent`.',
                'Добавить класс: `classList.add(\'ok\')`.',
            ],
            'solution' => <<<'HTML'
<style>
  .ok { color: rgb(35, 128, 74); font-weight: 700; }
</style>

<h1 class="title">Старый заголовок</h1>
<p class="status">Ожидание…</p>

<script>
  const title = document.querySelector('.title');
  title.textContent = 'Каталог кофе';

  const status = document.querySelector('.status');
  status.textContent = 'Готово';
  status.classList.add('ok');
</script>
HTML,
        ],
        [
            'id' => 'null-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Скрипт ничего не находит',
            'text' => "Скрипт должен отметить все пункты списка классом `done` и вывести их количество. Он падает — посмотри на панель «Консоль» под предпросмотром.\n\nВ коде две ошибки из урока: одна связана с тем, **когда** выполняется скрипт, другая — с тем, **что** возвращает `querySelectorAll`.",
            'starter' => <<<'HTML'
<style>
  .done { text-decoration: line-through; color: rgb(150, 150, 150); }
</style>

<script>
  const items = document.querySelectorAll('.item');
  items.classList.add('done');
  document.querySelector('.count').textContent = items.length;
</script>

<ul>
  <li class="item">Смолоть зёрна</li>
  <li class="item">Нагреть воду</li>
  <li class="item">Заварить</li>
</ul>
<p>Всего шагов: <span class="count">?</span></p>
HTML,
            'tests' => <<<'JS'
test('все три пункта отмечены классом done', () => assertEqual($$('.item.done').length, 3));
test('количество выведено', () => assertEqual(text('.count'), '3'));
test('разметка списка не изменена', () => assertEqual($$('ul > li.item').map((li) => li.textContent.trim()), ['Смолоть зёрна', 'Нагреть воду', 'Заварить']));
JS,
            'hints' => [
                'Скрипт стоит выше списка. В момент его выполнения элементов ещё нет: коллекция пустая, а `.count` — `null`. Перенеси скрипт в конец.',
                'У коллекции нет `classList`. Класс нужно добавить каждому элементу: `items.forEach((item) => item.classList.add(\'done\'));`',
            ],
            'solution' => <<<'HTML'
<style>
  .done { text-decoration: line-through; color: rgb(150, 150, 150); }
</style>

<ul>
  <li class="item">Смолоть зёрна</li>
  <li class="item">Нагреть воду</li>
  <li class="item">Заварить</li>
</ul>
<p>Всего шагов: <span class="count">?</span></p>

<script>
  const items = document.querySelectorAll('.item');
  items.forEach((item) => item.classList.add('done'));
  document.querySelector('.count').textContent = items.length;
</script>
HTML,
        ],
        [
            'id' => 'render-list',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Список из массива',
            'text' => "Напиши функцию `renderList(list, names)`. Она очищает элемент `list` и добавляет в него по одному пункту `<li class=\"drink\">` на каждое название из массива `names`.\n\nВызов функции уже написан — после твоей правки в списке должны появиться три напитка.",
            'starter' => <<<'HTML'
<h2>Меню</h2>
<ul id="menu"></ul>

<script>
  const drinks = ['Эспрессо', 'Капучино', 'Раф'];

  function renderList(list, names) {

  }

  renderList(document.querySelector('#menu'), drinks);
</script>
HTML,
            'tests' => <<<'JS'
test('в меню три пункта с классом drink', () => assertEqual($$('#menu > li.drink').map((li) => li.textContent), ['Эспрессо', 'Капучино', 'Раф']));
test('функция работает с любым массивом', () => {
  renderList($('#menu'), ['Чай', 'Какао']);
  assertEqual($$('#menu > li').map((li) => li.textContent), ['Чай', 'Какао']);
});
test('повторный вызов не дублирует пункты', () => {
  renderList($('#menu'), ['Латте']);
  renderList($('#menu'), ['Латте']);
  assertEqual($$('#menu > li').length, 1);
});
test('пустой массив очищает список', () => { renderList($('#menu'), []); assertEqual($$('#menu > li').length, 0); });
test('название вставляется как текст, а не как разметка', () => {
  renderList($('#menu'), ['<b>Раф</b>']);
  assertEqual($('#menu > li').children.length, 0, 'Используй textContent, а не innerHTML');
});
JS,
            'hints' => [
                'Сначала очисти список: `list.replaceChildren();`',
                'В цикле по `names`: создать `li` через `document.createElement(\'li\')`, задать `textContent` и класс, вставить через `list.append(li)`.',
            ],
            'solution' => <<<'HTML'
<h2>Меню</h2>
<ul id="menu"></ul>

<script>
  const drinks = ['Эспрессо', 'Капучино', 'Раф'];

  function renderList(list, names) {
    list.replaceChildren();

    for (const name of names) {
      const li = document.createElement('li');
      li.textContent = name;
      li.classList.add('drink');
      list.append(li);
    }
  }

  renderList(document.querySelector('#menu'), drinks);
</script>
HTML,
        ],
        [
            'id' => 'catalog-plan',
            'type' => 'plan',
            'title' => 'Каталог из данных: план',
            'text' => "С сервера пришёл массив товаров:\n\n```js\n[\n  { name: 'Эфиопия', price: 890, inStock: true },\n  { name: 'Кения', price: 980, inStock: false },\n]\n```\n\nДля каждого товара нужно построить карточку:\n\n```html\n<article class=\"card\">\n  <h3>Эфиопия</h3>\n  <p class=\"price\">890 ₽</p>\n  <button type=\"button\">В корзину</button>\n</article>\n```\n\nЕсли товара нет в наличии, у карточки добавляется класс `sold-out`, а кнопка становится неактивной и получает текст «Нет в наличии».\n\nЗапиши шаги функции `renderCatalog(container, products)`.",
            'reference' => <<<'MD'
1. Очистить контейнер.
2. Пройти по всем товарам.
3. Создать элемент карточки и дать ему класс `card`.
4. Создать заголовок, записать в него название, вставить в карточку.
5. Создать абзац цены с классом `price`, записать цену со знаком рубля, вставить в карточку.
6. Создать кнопку с `type="button"` и текстом «В корзину», вставить в карточку.
7. Если товара нет в наличии — добавить карточке класс `sold-out`, кнопку сделать неактивной и поменять её текст.
8. Вставить готовую карточку в контейнер.

Шаги 3–7 — это «построить одну карточку по одному товару». Их удобно вынести в отдельную функцию `createCard(product)`, которая возвращает элемент. Тогда `renderCatalog` останется циклом из трёх строк.

Карточка вставляется в документ в самом конце, уже собранной: так браузеру не приходится перерисовывать страницу после каждого шага.
MD,
        ],
        [
            'id' => 'catalog',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Каталог из данных: код',
            'text' => "Напиши `renderCatalog(container, products)` по своему плану. Разметка карточки — как в задании выше, цена выводится в виде `890 ₽`.\n\nСтили и данные уже есть. Вызов функции раскомментируй, когда она будет готова.",
            'starter' => <<<'HTML'
<style>
  .catalog { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; font-family: sans-serif; }
  .card { padding: 12px; border: 1px solid rgb(220, 220, 220); border-radius: 8px; }
  .card h3 { margin: 0 0 4px; }
  .sold-out { opacity: 0.5; }
</style>

<div class="catalog"></div>

<script>
  const products = [
    { name: 'Эфиопия', price: 890, inStock: true },
    { name: 'Кения', price: 980, inStock: false },
    { name: 'Колумбия', price: 750, inStock: true },
  ];

  // function renderCatalog(container, products) { ... }

  // renderCatalog(document.querySelector('.catalog'), products);
</script>
HTML,
            'tests' => <<<'JS'
test('на странице три карточки article.card', () => assertEqual($$('.catalog > article.card').length, 3));
test('в карточке заголовок h3 с названием', () => assertEqual($$('.card h3').map((h) => h.textContent), ['Эфиопия', 'Кения', 'Колумбия']));
test('цена в p.price со знаком рубля', () => assertEqual($$('.card p.price').map((p) => p.textContent), ['890 ₽', '980 ₽', '750 ₽']));
test('в каждой карточке кнопка type="button"', () => assertEqual($$('.card button').map((b) => b.type), ['button', 'button', 'button']));
test('товар в наличии: кнопка «В корзину», активна', () => {
  const button = $$('.card')[0].querySelector('button');
  assertEqual([button.textContent, button.disabled], ['В корзину', false]);
});
test('товара нет: класс sold-out, кнопка неактивна, текст «Нет в наличии»', () => {
  const card = $$('.card')[1];
  const button = card.querySelector('button');
  assertEqual([card.classList.contains('sold-out'), button.disabled, button.textContent], [true, true, 'Нет в наличии']);
});
test('у товаров в наличии класса sold-out нет', () => assertEqual($$('.card.sold-out').length, 1));
test('функция работает с другими данными и не дублирует карточки', () => {
  renderCatalog($('.catalog'), [{ name: 'Тест', price: 1, inStock: true }]);
  assertEqual($$('.card h3').map((h) => h.textContent), ['Тест']);
});
test('название вставляется как текст', () => {
  renderCatalog($('.catalog'), [{ name: '<i>Кофе</i>', price: 1, inStock: true }]);
  assertEqual($('.card h3').children.length, 0, 'Используй textContent, а не innerHTML');
});
JS,
            'hints' => [
                'Начни с функции `createCard(product)`: `const card = document.createElement(\'article\'); card.classList.add(\'card\');` — и в конце `return card;`.',
                'Вложенные элементы создаются так же и вставляются в карточку: `card.append(title, price, button);`',
                'Нет в наличии: `card.classList.add(\'sold-out\'); button.disabled = true; button.textContent = \'Нет в наличии\';`',
            ],
            'solution' => <<<'HTML'
<style>
  .catalog { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; font-family: sans-serif; }
  .card { padding: 12px; border: 1px solid rgb(220, 220, 220); border-radius: 8px; }
  .card h3 { margin: 0 0 4px; }
  .sold-out { opacity: 0.5; }
</style>

<div class="catalog"></div>

<script>
  const products = [
    { name: 'Эфиопия', price: 890, inStock: true },
    { name: 'Кения', price: 980, inStock: false },
    { name: 'Колумбия', price: 750, inStock: true },
  ];

  function createCard(product) {
    const card = document.createElement('article');
    card.classList.add('card');

    const title = document.createElement('h3');
    title.textContent = product.name;

    const price = document.createElement('p');
    price.classList.add('price');
    price.textContent = `${product.price} ₽`;

    const button = document.createElement('button');
    button.type = 'button';
    button.textContent = 'В корзину';

    if (!product.inStock) {
      card.classList.add('sold-out');
      button.disabled = true;
      button.textContent = 'Нет в наличии';
    }

    card.append(title, price, button);
    return card;
  }

  function renderCatalog(container, products) {
    container.replaceChildren();
    for (const product of products) {
      container.append(createCard(product));
    }
  }

  renderCatalog(document.querySelector('.catalog'), products);
</script>
HTML,
        ],
    ],
    'cards' => [
        ['Чем `querySelector` отличается от `querySelectorAll`?', '`querySelector` возвращает один элемент или `null`. `querySelectorAll` — коллекцию, которую нужно перебирать; у неё нет `textContent` и `classList`.'],
        ['Почему `textContent` безопаснее `innerHTML`?', '`innerHTML` разбирает строку как разметку, и в данных пользователя может оказаться чужой скрипт (XSS). `textContent` всегда вставляет текст.'],
        ['Как добавить на страницу новый элемент?', '`document.createElement(тег)` → наполнить (`textContent`, классы) → вставить: `parent.append(element)`.'],
        ['Почему `querySelector` в скрипте вернул `null`, хотя элемент есть в разметке?', 'Скрипт выполнился раньше, чем браузер дошёл до элемента. Решения: скрипт в конце `body`, атрибут `defer` или событие `DOMContentLoaded`.'],
        ['Как лучше менять внешний вид элемента из скрипта?', 'Переключать классы через `classList`, а оформление держать в CSS. Скрипт сообщает состояние, а не рисует.'],
    ],
];
