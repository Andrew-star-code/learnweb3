<?php
return [
    'tasks' => [
        [
            'id' => 'width-quiz',
            'type' => 'quiz',
            'title' => 'Сколько места займёт блок',
            'text' => "```css\n.box {\n  width: 200px;\n  padding: 20px;\n  border: 5px solid black;\n}\n```\n\nСвойство `box-sizing` не задано. Какой ширины блок окажется на странице?",
            'options' => ['200px', '225px', '240px', '250px'],
            'answer' => 3,
            'explain' => 'По умолчанию `width` — это ширина содержимого. К ней прибавляются `padding` слева и справа (40) и рамка слева и справа (10): 200 + 40 + 10 = 250. С `box-sizing: border-box` было бы ровно 200.',
        ],
        [
            'id' => 'card',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Отступы и рамка',
            'text' => 'Заполни пропуски значениями из комментариев.',
            'starter' => <<<'HTML'
<style>
  .card {
    padding: ___;          /* 16px со всех сторон */
    border: ___;           /* 1px, сплошная, цвет rgb(200, 200, 200) */
    border-radius: ___;    /* 8px */
    margin-bottom: ___;    /* 24px */
  }
</style>

<div class="card">Первая карточка</div>
<div class="card">Вторая карточка</div>
HTML,
            'tests' => <<<'JS'
test('внутренний отступ 16px со всех сторон', () => assertEqual([css('.card', 'padding-top'), css('.card', 'padding-right'), css('.card', 'padding-bottom'), css('.card', 'padding-left')], ['16px', '16px', '16px', '16px']));
test('рамка 1px сплошная серая', () => assertEqual([css('.card', 'border-top-width'), css('.card', 'border-top-style'), css('.card', 'border-top-color')], ['1px', 'solid', 'rgb(200, 200, 200)']));
test('углы скруглены на 8px', () => assertEqual(css('.card', 'border-top-left-radius'), '8px'));
test('между карточками 24px', () => {
  const cards = $$('.card');
  assertEqual(Math.round(box(cards[1]).top - box(cards[0]).bottom), 24);
});
JS,
            'hints' => [
                'Рамка записывается тремя значениями через пробел: толщина, стиль, цвет — `1px solid rgb(200, 200, 200)`.',
            ],
            'solution' => <<<'HTML'
<style>
  .card {
    padding: 16px;
    border: 1px solid rgb(200, 200, 200);
    border-radius: 8px;
    margin-bottom: 24px;
  }
</style>

<div class="card">Первая карточка</div>
<div class="card">Вторая карточка</div>
HTML,
        ],
        [
            'id' => 'sizing-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Размеры не сходятся',
            'text' => "В макете две проблемы.\n\n1. Блок `.box` по макету должен занимать ровно 300 пикселей вместе с отступами и рамкой, а занимает больше.\n2. Колонка `.page` должна быть не шире 600 пикселей, стоять по центру окна и сжиматься на телефоне. Сейчас она прижата к левому краю, а на ширине 360 появляется горизонтальная прокрутка — переключи ширину предпросмотра и посмотри.\n\nЧисла `300px`, `20px` и `5px` в стилях `.box` не меняй.",
            'starter' => <<<'HTML'
<style>
  .page {
    width: 600px;
    margin: 0;
    background: rgb(245, 245, 245);
  }

  .box {
    width: 300px;
    padding: 20px;
    border: 5px solid rgb(47, 111, 79);
  }
</style>

<div class="page">
  <div class="box">Блок шириной ровно 300 пикселей</div>
</div>
HTML,
            'tests' => <<<'JS'
test('.box занимает ровно 300px', async () => { await setWidth(1000); assertEqual(Math.round(box('.box').width), 300); });
test('отступ и рамка у .box сохранены', () => assertEqual([css('.box', 'padding-left'), css('.box', 'border-left-width')], ['20px', '5px']));
test('на широком экране .page шириной 600px', async () => { await setWidth(1000); assertEqual(Math.round(box('.page').width), 600); });
test('.page стоит по центру', async () => {
  await setWidth(1000);
  const b = box('.page');
  assert(Math.abs(b.left - (1000 - b.right)) <= 1, 'Отступы слева и справа от .page должны быть равны');
});
test('на ширине 360 колонка сжимается', async () => { await setWidth(360); assert(box('.page').width <= 360, '.page шире окна'); });
test('на ширине 360 нет горизонтальной прокрутки', async () => { await setWidth(360); assert(document.documentElement.scrollWidth <= 360, 'Страница шире окна'); });
JS,
            'hints' => [
                'Чтобы `width` включала отступы и рамку, блоку нужен `box-sizing: border-box`.',
                'Жёсткая `width: 600px` не даёт колонке сжаться. Замени её на `max-width: 600px`.',
                'По центру: `margin: 0 auto`.',
            ],
            'solution' => <<<'HTML'
<style>
  .page {
    max-width: 600px;
    margin: 0 auto;
    background: rgb(245, 245, 245);
  }

  .box {
    box-sizing: border-box;
    width: 300px;
    padding: 20px;
    border: 5px solid rgb(47, 111, 79);
  }
</style>

<div class="page">
  <div class="box">Блок шириной ровно 300 пикселей</div>
</div>
HTML,
        ],
        [
            'id' => 'button',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Ссылка в виде кнопки',
            'text' => "Оформи ссылку с классом `btn` как кнопку:\n\n- внутренние отступы: 10px сверху и снизу, 20px слева и справа;\n- отступ сверху от абзаца — 12px;\n- фон `rgb(47, 111, 79)`, текст белый `rgb(255, 255, 255)`, без подчёркивания;\n- скругление 6px.\n\nСсылка — строчный элемент, поэтому вертикальные отступы у неё сами не заработают. Подбери подходящий `display`.",
            'starter' => <<<'HTML'
<style>
  .btn {

  }
</style>

<p>Свежая обжарка каждую неделю.</p>
<a class="btn" href="/order">Заказать</a>
HTML,
            'tests' => <<<'JS'
test('display: inline-block', () => assertEqual(css('.btn', 'display'), 'inline-block'));
test('отступы 10px и 20px', () => assertEqual([css('.btn', 'padding-top'), css('.btn', 'padding-bottom'), css('.btn', 'padding-left'), css('.btn', 'padding-right')], ['10px', '10px', '20px', '20px']));
test('отступ сверху 12px действительно отодвигает кнопку', () => {
  assertEqual(css('.btn', 'margin-top'), '12px');
  assert(box('.btn').top - box('p').bottom >= 12, 'Кнопка не отодвинулась от абзаца');
});
test('цвета', () => assertEqual([css('.btn', 'background-color'), css('.btn', 'color')], ['rgb(47, 111, 79)', 'rgb(255, 255, 255)']));
test('без подчёркивания', () => assertEqual(css('.btn', 'text-decoration-line'), 'none'));
test('скругление 6px', () => assertEqual(css('.btn', 'border-top-left-radius'), '6px'));
JS,
            'hints' => [
                '`display: inline-block` оставляет элемент в строке, но включает ему размеры и вертикальные отступы.',
                'Два значения в `padding` — это «вертикаль горизонталь»: `padding: 10px 20px`.',
                'Убрать подчёркивание: `text-decoration: none`.',
            ],
            'solution' => <<<'HTML'
<style>
  .btn {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 12px;
    background: rgb(47, 111, 79);
    color: rgb(255, 255, 255);
    text-decoration: none;
    border-radius: 6px;
  }
</style>

<p>Свежая обжарка каждую неделю.</p>
<a class="btn" href="/order">Заказать</a>
HTML,
        ],
        [
            'id' => 'profile',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Карточка профиля',
            'text' => "Разметка готова. Напиши стили с нуля — образец на соседней вкладке.\n\n- Для всех элементов включён `box-sizing: border-box`.\n- `.profile`: не шире 320px, по центру окна, внутренний отступ 24px, скругление 12px, тень (любая), текст по центру.\n- `.avatar`: круг 80 на 80 пикселей с фоном `rgb(47, 111, 79)`, стоит по центру карточки.",
            'starter' => <<<'HTML'
<style>

</style>

<div class="profile">
  <div class="avatar"></div>
  <h2>Анна Кофейникова</h2>
  <p>Бариста, обжарщик</p>
</div>
HTML,
            'tests' => <<<'JS'
test('box-sizing: border-box у всех элементов', () => assertEqual([css('.profile', 'box-sizing'), css('.avatar', 'box-sizing'), css('p', 'box-sizing')], ['border-box', 'border-box', 'border-box']));
test('карточка шириной 320px вместе с отступами', async () => { await setWidth(800); assertEqual(Math.round(box('.profile').width), 320); });
test('карточка по центру окна', async () => {
  await setWidth(800);
  const b = box('.profile');
  assert(Math.abs(b.left - (800 - b.right)) <= 1, 'Отступы слева и справа должны быть равны');
});
test('на узком экране карточка сжимается', async () => { await setWidth(280); assert(box('.profile').right <= 280, 'Карточка вылезла за окно'); });
test('отступ 24px и скругление 12px', () => assertEqual([css('.profile', 'padding-top'), css('.profile', 'padding-left'), css('.profile', 'border-top-left-radius')], ['24px', '24px', '12px']));
test('есть тень', () => assert(css('.profile', 'box-shadow') !== 'none', 'Добавь box-shadow'));
test('текст по центру', () => assertEqual(css('.profile', 'text-align'), 'center'));
test('аватар — круг 80×80 нужного цвета', () => {
  const b = box('.avatar');
  assertEqual([Math.round(b.width), Math.round(b.height)], [80, 80]);
  assertEqual(css('.avatar', 'border-top-left-radius'), '50%');
  assertEqual(css('.avatar', 'background-color'), 'rgb(47, 111, 79)');
});
test('аватар по центру карточки', async () => {
  await setWidth(800);
  const a = box('.avatar');
  const p = box('.profile');
  assert(Math.abs((a.left + a.right) / 2 - (p.left + p.right) / 2) <= 1, 'Центр аватара не совпадает с центром карточки');
});
JS,
            'hints' => [
                'Начни с `* { box-sizing: border-box; }`.',
                'Карточка: `max-width: 320px; margin: 0 auto;`. Тень, например: `box-shadow: 0 2px 8px rgb(0 0 0 / 15%);`',
                'Аватар — блочный элемент, `text-align` его не двигает. Блок центрируется через `margin: 0 auto` при заданной ширине.',
            ],
            'solution' => <<<'HTML'
<style>
  * { box-sizing: border-box; }

  .profile {
    max-width: 320px;
    margin: 0 auto;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgb(0 0 0 / 15%);
    text-align: center;
  }

  .avatar {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    border-radius: 50%;
    background: rgb(47, 111, 79);
  }
</style>

<div class="profile">
  <div class="avatar"></div>
  <h2>Анна Кофейникова</h2>
  <p>Бариста, обжарщик</p>
</div>
HTML,
        ],
    ],
    'cards' => [
        ['Из каких слоёв состоит блок, изнутри наружу?', 'Содержимое → `padding` (внутренний отступ) → `border` (рамка) → `margin` (внешний отступ).'],
        ['Что делает `box-sizing: border-box`?', '`width` и `height` начинают включать `padding` и `border`. Написал 300px — блок займёт ровно 300px.'],
        ['Как сделать центральную колонку, которая сжимается на телефоне?', '`max-width: 800px; margin: 0 auto;`. Именно `max-width`, а не `width`.'],
        ['Почему у ссылки не работает `margin-top` или `width`?', 'Она строчная (`display: inline`). Нужен `display: inline-block` или `block`.'],
        ['Что такое схлопывание отступов?', 'Вертикальные `margin` соседних блоков не складываются, берётся больший. Внутри flex и grid этого нет.'],
    ],
];
