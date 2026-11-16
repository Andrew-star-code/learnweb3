<?php
return [
    'tasks' => [
        [
            'id' => 'string-quiz',
            'type' => 'quiz',
            'title' => 'Счётчик визитов',
            'text' => "```js\nlocalStorage.setItem('visits', 5);\nconst visits = localStorage.getItem('visits') + 1;\n```\n\nЧто окажется в переменной `visits`?",
            'options' => ['6', "'51'", "'6'", 'NaN'],
            'answer' => 1,
            'explain' => 'Хранилище превращает всё в строки: число 5 записалось как `\'5\'`. Плюс со строкой склеивает, получается `\'51\'`. Правильно: `Number(localStorage.getItem(\'visits\')) + 1`.',
        ],
        [
            'id' => 'theme',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'storage' => true,
            'title' => 'Запомнить тему',
            'text' => "Кнопка переключает тёмную тему — класс `dark` у `body`. Допиши, чтобы выбор запоминался под ключом `theme` (значения `dark` и `light`) и восстанавливался при открытии страницы.\n\nПроверь в предпросмотре: включи тёмную тему, измени любой символ в коде — предпросмотр перезапустится, тема должна остаться.",
            'starter' => <<<'HTML'
<style>
  body { font-family: sans-serif; padding: 16px; }
  body.dark { background: rgb(30, 30, 30); color: rgb(230, 230, 230); }
</style>

<p>Читать в темноте удобнее с тёмной темой.</p>
<button type="button" id="toggle">Сменить тему</button>

<script>
  function applySavedTheme() {
    const saved = localStorage.___('theme');
    document.body.classList.toggle('dark', saved === 'dark');
  }

  document.querySelector('#toggle').addEventListener('click', () => {
    const dark = document.body.classList.toggle('dark');
    localStorage.___('theme', dark ? ___ : ___);
  });

  applySavedTheme();
</script>
HTML,
            'tests' => <<<'JS'
const dark = () => document.body.classList.contains('dark');
test('при первом визите тема светлая', () => assertEqual(dark(), false));
test('нажатие включает тёмную тему и запоминает её', () => { click('#toggle'); assertEqual([dark(), localStorage.getItem('theme')], [true, 'dark']); });
test('второе нажатие возвращает светлую', () => { click('#toggle'); assertEqual([dark(), localStorage.getItem('theme')], [false, 'light']); });
test('сохранённая тёмная тема восстанавливается', () => {
  localStorage.setItem('theme', 'dark');
  applySavedTheme();
  assertEqual(dark(), true);
});
test('сохранённая светлая тема восстанавливается', () => {
  localStorage.setItem('theme', 'light');
  applySavedTheme();
  assertEqual(dark(), false);
});
JS,
            'hints' => [
                'Чтение — `getItem`, запись — `setItem`.',
                'Значения — строки в кавычках: `dark ? \'dark\' : \'light\'`.',
            ],
            'solution' => <<<'HTML'
<style>
  body { font-family: sans-serif; padding: 16px; }
  body.dark { background: rgb(30, 30, 30); color: rgb(230, 230, 230); }
</style>

<p>Читать в темноте удобнее с тёмной темой.</p>
<button type="button" id="toggle">Сменить тему</button>

<script>
  function applySavedTheme() {
    const saved = localStorage.getItem('theme');
    document.body.classList.toggle('dark', saved === 'dark');
  }

  document.querySelector('#toggle').addEventListener('click', () => {
    const dark = document.body.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
  });

  applySavedTheme();
</script>
HTML,
        ],
        [
            'id' => 'favorites-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'storage' => true,
            'title' => 'Избранное теряет данные',
            'text' => "Страница хранит список избранных напитков. В чтении и записи три ошибки — все из этого урока:\n\n1. При первом визите скрипт падает (смотри «Консоль»).\n2. Массив сохраняется так, что прочитать его обратно нельзя.\n3. Если в хранилище окажется испорченное значение, страница перестанет работать.\n\nИсправь функции `loadFavorites` и `saveFavorites`. Остальной код менять не нужно.\n\nЕсли в процессе в хранилище запишется мусор, нажми «Очистить хранилище».",
            'starter' => <<<'HTML'
<p>
  <button type="button" class="add" data-name="Эспрессо">+ Эспрессо</button>
  <button type="button" class="add" data-name="Раф">+ Раф</button>
  <button type="button" class="add" data-name="Латте">+ Латте</button>
</p>
<h3>Избранное</h3>
<ul id="favorites"></ul>

<script>
  let favorites = [];

  function loadFavorites() {
    favorites = JSON.parse(localStorage.getItem('favorites'));
  }

  function saveFavorites() {
    localStorage.setItem('favorites', favorites);
  }

  function render() {
    const list = document.querySelector('#favorites');
    list.replaceChildren();
    for (const name of favorites) {
      const li = document.createElement('li');
      li.textContent = name;
      list.append(li);
    }
  }

  document.querySelectorAll('.add').forEach((button) => {
    button.addEventListener('click', () => {
      favorites.push(button.dataset.name);
      saveFavorites();
      render();
    });
  });

  loadFavorites();
  render();
</script>
HTML,
            'tests' => <<<'JS'
const shown = () => $$('#favorites li').map((li) => li.textContent);
test('при первом визите список пуст, а favorites — массив', () => {
  assert(Array.isArray(favorites), 'После loadFavorites() в favorites должен быть массив, а не null');
  assertEqual(shown(), []);
});
test('добавление показывает напиток', () => { click('[data-name="Раф"]'); assertEqual(shown(), ['Раф']); });
test('в хранилище лежит JSON-массив', () => {
  click('[data-name="Латте"]');
  assertEqual(localStorage.getItem('favorites'), '["Раф","Латте"]');
});
test('сохранённый список восстанавливается', () => {
  localStorage.setItem('favorites', '["Эспрессо","Раф","Латте"]');
  loadFavorites();
  render();
  assertEqual(shown(), ['Эспрессо', 'Раф', 'Латте']);
});
test('испорченное значение не роняет страницу', () => {
  localStorage.setItem('favorites', 'Раф,Латте');
  loadFavorites();
  render();
  assertEqual([Array.isArray(favorites), shown()], [true, []]);
});
JS,
            'hints' => [
                'Запись: `localStorage.setItem(\'favorites\', JSON.stringify(favorites));`',
                'Первый визит: `getItem` вернёт `null`, и `JSON.parse(null)` — тоже `null`. Подставь пустой массив: `?? []`.',
                'Испорченные данные: оберни разбор в `try`, а в `catch` присвой `favorites = [];`',
            ],
            'solution' => <<<'HTML'
<p>
  <button type="button" class="add" data-name="Эспрессо">+ Эспрессо</button>
  <button type="button" class="add" data-name="Раф">+ Раф</button>
  <button type="button" class="add" data-name="Латте">+ Латте</button>
</p>
<h3>Избранное</h3>
<ul id="favorites"></ul>

<script>
  let favorites = [];

  function loadFavorites() {
    try {
      favorites = JSON.parse(localStorage.getItem('favorites')) ?? [];
    } catch (error) {
      favorites = [];
    }
  }

  function saveFavorites() {
    localStorage.setItem('favorites', JSON.stringify(favorites));
  }

  function render() {
    const list = document.querySelector('#favorites');
    list.replaceChildren();
    for (const name of favorites) {
      const li = document.createElement('li');
      li.textContent = name;
      list.append(li);
    }
  }

  document.querySelectorAll('.add').forEach((button) => {
    button.addEventListener('click', () => {
      favorites.push(button.dataset.name);
      saveFavorites();
      render();
    });
  });

  loadFavorites();
  render();
</script>
HTML,
        ],
        [
            'id' => 'draft',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'storage' => true,
            'title' => 'Черновик, который не теряется',
            'text' => "Человек пишет длинный отзыв, случайно закрывает вкладку — и текст пропал. Сделай автосохранение черновика.\n\n- При каждом изменении текста он записывается в хранилище под ключом `draft`.\n- Функция `restoreDraft()` подставляет сохранённый текст в поле. Если черновика нет, поле остаётся пустым. Вызови её в конце скрипта.\n- Кнопка `#clear` очищает поле и удаляет ключ из хранилища.",
            'starter' => <<<'HTML'
<p><label for="note">Отзыв</label></p>
<textarea id="note" rows="4" cols="40"></textarea>
<p><button type="button" id="clear">Очистить</button></p>

<script>
  const note = document.querySelector('#note');

</script>
HTML,
            'tests' => <<<'JS'
test('при первом визите поле пустое', () => assertEqual($('#note').value, ''));
test('ввод сохраняется в хранилище', () => { type('#note', 'Отличный кофе'); assertEqual(localStorage.getItem('draft'), 'Отличный кофе'); });
test('каждое изменение перезаписывает черновик', () => { type('#note', 'Отличный кофе!'); assertEqual(localStorage.getItem('draft'), 'Отличный кофе!'); });
test('restoreDraft подставляет сохранённый текст', () => {
  localStorage.setItem('draft', 'Вчерашний черновик');
  $('#note').value = '';
  restoreDraft();
  assertEqual($('#note').value, 'Вчерашний черновик');
});
test('кнопка очищает поле и удаляет ключ', () => {
  click('#clear');
  assertEqual([$('#note').value, localStorage.getItem('draft')], ['', null]);
});
test('restoreDraft без черновика оставляет поле пустым', () => {
  restoreDraft();
  assertEqual($('#note').value, '');
});
JS,
            'hints' => [
                'Сохранение: на событии `input` — `localStorage.setItem(\'draft\', note.value);`',
                'Восстановление: `note.value = localStorage.getItem(\'draft\') ?? \'\';` — без `??` при отсутствии черновика в поле попадёт `null`.',
                'Удалить ключ: `localStorage.removeItem(\'draft\');`',
            ],
            'solution' => <<<'HTML'
<p><label for="note">Отзыв</label></p>
<textarea id="note" rows="4" cols="40"></textarea>
<p><button type="button" id="clear">Очистить</button></p>

<script>
  const note = document.querySelector('#note');

  function restoreDraft() {
    note.value = localStorage.getItem('draft') ?? '';
  }

  note.addEventListener('input', () => {
    localStorage.setItem('draft', note.value);
  });

  document.querySelector('#clear').addEventListener('click', () => {
    note.value = '';
    localStorage.removeItem('draft');
  });

  restoreDraft();
</script>
HTML,
        ],
    ],
    'cards' => [
        ['Какого типа значения хранит `localStorage`?', 'Только строки. Числа нужно превращать обратно через `Number`, массивы и объекты — хранить через `JSON.stringify` и читать через `JSON.parse`.'],
        ['Что вернёт `localStorage.getItem` для несуществующего ключа?', '`null`. При первом визите это обычная ситуация, её нужно обрабатывать: `?? []`, `?? \'\'`.'],
        ['Как надёжно прочитать массив из `localStorage`?', '`try { return JSON.parse(localStorage.getItem(key)) ?? []; } catch { return []; }` — учтены и первый визит, и испорченные данные.'],
        ['В чём идея схемы «состояние — сохранение — отрисовка»?', 'Данные лежат в одной переменной. Обработчики меняют её, затем вызывают `save()` и `render()`. Страница — отражение данных и не может с ними разойтись.'],
        ['Что нельзя хранить в `localStorage`?', 'Пароли, токены, платёжные данные: хранилище читается любым скриптом на странице.'],
    ],
];
