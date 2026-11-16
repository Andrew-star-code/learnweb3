<?php
return [
    'tasks' => [
        [
            'id' => 'name-quiz',
            'type' => 'quiz',
            'title' => 'Куда пропали данные',
            'text' => "Форма отправляется, но на сервер не приходит значение одного из полей:\n\n```html\n<label for=\"phone\">Телефон</label>\n<input type=\"tel\" id=\"phone\" required>\n```\n\nЧего не хватает?",
            'options' => ['Атрибута value', 'Атрибута name', 'Атрибута placeholder', 'Тега fieldset вокруг'],
            'answer' => 1,
            'explain' => 'Сервер получает пары «имя — значение», и имя берётся из `name`. Поле без `name` браузер в запрос не включает. `id` нужен только для связи с подписью и для скриптов.',
        ],
        [
            'id' => 'login',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Форма входа',
            'text' => 'Заполни пропуски в форме входа. Данные содержат пароль, поэтому метод отправки должен прятать их из адресной строки.',
            'starter' => <<<'HTML'
<form action="/login" method="___">
  <p>
    <label for="email">Почта</label>
    <input type="___" id="___" name="email" required>
  </p>
  <p>
    <label for="password">Пароль</label>
    <input type="___" id="password" name="___" required minlength="8">
  </p>
  <button type="___">Войти</button>
</form>
HTML,
            'tests' => <<<'JS'
test('форма отправляется методом post на /login', () => {
  assertEqual($('form').getAttribute('method').toLowerCase(), 'post');
  assertEqual($('form').getAttribute('action'), '/login');
});
test('поле почты имеет тип email', () => assertEqual($('[name=email]').type, 'email'));
test('подпись «Почта» связана со своим полем', () => assert($('label[for=email]').control === $('[name=email]'), 'for у подписи должен совпадать с id поля'));
test('поле пароля имеет тип password', () => assertEqual($('#password').type, 'password'));
test('пароль отправляется под именем password', () => assertEqual($('#password').name, 'password'));
test('кнопка отправляет форму', () => assertEqual($('form button').type, 'submit'));
JS,
            'hints' => [
                'Метод, который не показывает данные в адресе, — `post`.',
                'Типы полей: `email` и `password`. `id` поля почты должен совпасть с `for` у его подписи.',
                'Кнопка отправки: `type="submit"`.',
            ],
            'solution' => <<<'HTML'
<form action="/login" method="post">
  <p>
    <label for="email">Почта</label>
    <input type="email" id="email" name="email" required>
  </p>
  <p>
    <label for="password">Пароль</label>
    <input type="password" id="password" name="password" required minlength="8">
  </p>
  <button type="submit">Войти</button>
</form>
HTML,
        ],
        [
            'id' => 'fix-form',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Форма заказа с ошибками',
            'text' => "Форма заказа выглядит рабочей, но в ней четыре ошибки:\n\n- у полей «Имя» и «Телефон» вместо подписей только `placeholder`;\n- поле телефона не попадёт на сервер;\n- можно выбрать оба способа доставки одновременно;\n- кнопка не отправляет форму.\n\nИсправь. Значения способов доставки (`courier` и `pickup`) менять не нужно.",
            'starter' => <<<'HTML'
<form action="/order" method="post">
  <p>
    <input type="text" id="name" name="name" placeholder="Имя">
  </p>
  <p>
    <input type="tel" id="phone" placeholder="Телефон">
  </p>
  <p>
    <input type="radio" id="courier" name="courier" value="courier">
    <label for="courier">Курьер</label>
    <input type="radio" id="pickup" name="pickup" value="pickup">
    <label for="pickup">Самовывоз</label>
  </p>
  <button type="button">Заказать</button>
</form>
HTML,
            'tests' => <<<'JS'
test('у поля «Имя» есть связанная подпись', () => assert($('#name').labels.length > 0, 'Добавь <label for="name">'));
test('у поля «Телефон» есть связанная подпись', () => assert($('#phone').labels.length > 0, 'Добавь <label for="phone">'));
test('у телефона есть name', () => assert($('#phone').name.length > 0, 'Поле без name не отправится'));
test('переключатели доставки в одной группе', () => {
  assertEqual($('#courier').name, $('#pickup').name, 'У переключателей должен быть одинаковый name');
});
test('выбрать можно только один способ доставки', () => {
  $('#courier').checked = true;
  $('#pickup').checked = true;
  assertEqual($('#courier').checked, false);
});
test('значения способов доставки сохранены', () => assertEqual([$('#courier').value, $('#pickup').value], ['courier', 'pickup']));
test('кнопка отправляет форму', () => assertEqual($('form button').type, 'submit'));
JS,
            'hints' => [
                'Подпись: `<label for="name">Имя</label>` перед полем. `placeholder` можно оставить или убрать.',
                'Телефону нужен `name="phone"`.',
                'Обоим переключателям дай одинаковое имя, например `name="delivery"`. Значения остаются в `value`.',
            ],
            'solution' => <<<'HTML'
<form action="/order" method="post">
  <p>
    <label for="name">Имя</label>
    <input type="text" id="name" name="name">
  </p>
  <p>
    <label for="phone">Телефон</label>
    <input type="tel" id="phone" name="phone">
  </p>
  <p>
    <input type="radio" id="courier" name="delivery" value="courier">
    <label for="courier">Курьер</label>
    <input type="radio" id="pickup" name="delivery" value="pickup">
    <label for="pickup">Самовывоз</label>
  </p>
  <button type="submit">Заказать</button>
</form>
HTML,
        ],
        [
            'id' => 'feedback',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Обратная связь',
            'text' => "Допиши форму обратной связи. Нужны три поля, у каждого — связанная подпись:\n\n1. Выпадающий список «Тема» с именем `topic` и тремя вариантами: «Вопрос» (`question`), «Жалоба» (`complaint`), «Предложение» (`idea`).\n2. Многострочное поле «Сообщение» с именем `message`, обязательное.\n3. Флажок «Согласен на обработку данных» с именем `agree`, обязательный.",
            'starter' => <<<'HTML'
<form action="/feedback" method="post">

  <button type="submit">Отправить</button>
</form>
HTML,
            'tests' => <<<'JS'
test('выпадающий список с именем topic', () => assertEqual($('[name=topic]').tagName, 'SELECT'));
test('в списке три варианта с нужными значениями', () => assertEqual($$('[name=topic] option').map((o) => o.value), ['question', 'complaint', 'idea']));
test('тексты вариантов', () => assertEqual($$('[name=topic] option').map((o) => o.textContent.trim()), ['Вопрос', 'Жалоба', 'Предложение']));
test('многострочное обязательное поле message', () => {
  assertEqual($('[name=message]').tagName, 'TEXTAREA');
  assert($('[name=message]').required, 'Поле сообщения должно быть обязательным');
});
test('обязательный флажок agree', () => {
  assertEqual($('[name=agree]').type, 'checkbox');
  assert($('[name=agree]').required, 'Флажок должен быть обязательным');
});
test('у всех трёх полей есть связанные подписи', () => {
  for (const name of ['topic', 'message', 'agree']) {
    assert($(`[name=${name}]`).labels.length > 0, `Нет подписи у поля ${name}`);
  }
});
JS,
            'hints' => [
                'Список: `<select id="topic" name="topic"><option value="question">Вопрос</option>…</select>`.',
                'Многострочное поле — парный тег: `<textarea id="message" name="message" required></textarea>`.',
                'Флажок: `<input type="checkbox" id="agree" name="agree" required>` и подпись с `for="agree"`.',
            ],
            'solution' => <<<'HTML'
<form action="/feedback" method="post">
  <p>
    <label for="topic">Тема</label>
    <select id="topic" name="topic">
      <option value="question">Вопрос</option>
      <option value="complaint">Жалоба</option>
      <option value="idea">Предложение</option>
    </select>
  </p>
  <p>
    <label for="message">Сообщение</label>
    <textarea id="message" name="message" required></textarea>
  </p>
  <p>
    <input type="checkbox" id="agree" name="agree" required>
    <label for="agree">Согласен на обработку данных</label>
  </p>
  <button type="submit">Отправить</button>
</form>
HTML,
        ],
        [
            'id' => 'register',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Регистрация',
            'text' => "Сверстай форму регистрации с нуля. Образец — на соседней вкладке предпросмотра.\n\n- Форма отправляется методом `post` на `/register`.\n- Поля обёрнуты в группу с заголовком «Регистрация».\n- Имя: `name`, текст, обязательное, не короче 2 символов.\n- Почта: `email`, обязательное.\n- Возраст: `age`, число от 18 до 120.\n- Пароль: `password`, обязательное, не короче 8 символов.\n- Кнопка отправки «Зарегистрироваться».\n- У каждого поля — связанная подпись.",
            'starter' => "",
            'tests' => <<<'JS'
test('форма: post на /register', () => {
  assertEqual(($('form').getAttribute('method') || '').toLowerCase(), 'post');
  assertEqual($('form').getAttribute('action'), '/register');
});
test('группа полей с заголовком «Регистрация»', () => assertEqual(text('form fieldset legend'), 'Регистрация'));
test('имя: текст, обязательное, от 2 символов', () => {
  const field = $('[name=name]');
  assertEqual([field.type, field.required, field.minLength], ['text', true, 2]);
});
test('почта: тип email, обязательное', () => assertEqual([$('[name=email]').type, $('[name=email]').required], ['email', true]));
test('возраст: число от 18 до 120', () => {
  const field = $('[name=age]');
  assertEqual([field.type, field.min, field.max], ['number', '18', '120']);
});
test('пароль: тип password, обязательное, от 8 символов', () => {
  const field = $('[name=password]');
  assertEqual([field.type, field.required, field.minLength], ['password', true, 8]);
});
test('у каждого поля есть связанная подпись', () => {
  for (const name of ['name', 'email', 'age', 'password']) {
    assert($(`[name=${name}]`).labels.length > 0, `Нет подписи у поля ${name}`);
  }
});
test('кнопка отправки «Зарегистрироваться»', () => {
  assertEqual($('form button').type, 'submit');
  assertEqual(text('form button'), 'Зарегистрироваться');
});
JS,
            'hints' => [
                'Каркас: `<form ...><fieldset><legend>Регистрация</legend> …поля… </fieldset><button type="submit">…</button></form>`.',
                'Ограничения — атрибутами: `minlength="2"`, `min="18" max="120"`.',
            ],
            'solution' => <<<'HTML'
<form action="/register" method="post">
  <fieldset>
    <legend>Регистрация</legend>
    <p>
      <label for="name">Имя</label>
      <input type="text" id="name" name="name" required minlength="2">
    </p>
    <p>
      <label for="email">Почта</label>
      <input type="email" id="email" name="email" required>
    </p>
    <p>
      <label for="age">Возраст</label>
      <input type="number" id="age" name="age" min="18" max="120">
    </p>
    <p>
      <label for="password">Пароль</label>
      <input type="password" id="password" name="password" required minlength="8">
    </p>
  </fieldset>
  <button type="submit">Зарегистрироваться</button>
</form>
HTML,
        ],
    ],
    'cards' => [
        ['Что будет с полем формы без атрибута `name`?', 'Оно не отправится на сервер. `name` — это имя, под которым значение придёт в запросе.'],
        ['Когда `method="get"`, а когда `post`?', '`get` — данные в адресе, подходит для поиска и фильтров. `post` — данные в теле запроса, для всего, что меняет данные или содержит секреты.'],
        ['Как связать подпись с полем и зачем?', '`for` у `<label>` совпадает с `id` у поля. Щелчок по подписи активирует поле, программы экранного доступа зачитывают подпись.'],
        ['Как объединить переключатели в группу «один из нескольких»?', 'Одинаковый `name` у всех `type="radio"`. Значение каждого варианта — в `value`.'],
        ['Достаточно ли проверки `required` и `minlength` в браузере?', 'Нет. Это удобство для человека; запрос можно отправить в обход формы, поэтому сервер проверяет всё заново.'],
    ],
];
