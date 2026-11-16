<?php
return [
    'tasks' => [
        [
            'id' => 'submit-quiz',
            'type' => 'quiz',
            'title' => 'На что подписываться',
            'text' => 'Нужно проверить данные формы входа перед отправкой. Где правильнее повесить обработчик?',
            'options' => [
                'На событие click кнопки «Войти»',
                'На событие submit формы',
                'На событие input каждого поля',
                'На событие keydown, чтобы поймать Enter',
            ],
            'answer' => 1,
            'explain' => 'Форму можно отправить и щелчком по кнопке, и клавишей Enter в поле. Событие `submit` формы случается в обоих случаях, поэтому проверка на нём не пропустит ни один способ.',
        ],
        [
            'id' => 'read-values',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'html',
            'title' => 'Перехватить и прочитать',
            'text' => 'Допиши обработчик: при отправке формы страница не должна перезагружаться, а в абзаце `#result` появляется строка вида `Аня: 2 шт.`',
            'starter' => <<<'HTML'
<form id="order">
  <p><label>Имя <input type="text" name="customer"></label></p>
  <p><label>Порций <input type="number" name="qty" value="1" min="1"></label></p>
  <button type="submit">Заказать</button>
</form>
<p id="result"></p>

<script>
  const form = document.querySelector('#order');
  const result = document.querySelector('#result');

  form.addEventListener('___', (event) => {
    event.___();

    const customer = form.elements.customer.___;
    const qty = Number(form.elements.qty.value);

    result.textContent = `${customer}: ${qty} шт.`;
  });
</script>
HTML,
            'tests' => <<<'JS'
test('отправка формы выводит результат', () => {
  type('[name=customer]', 'Аня');
  type('[name=qty]', '2');
  submit('#order');
  assertEqual(text('#result'), 'Аня: 2 шт.');
});
test('повторная отправка с другими данными', () => {
  type('[name=customer]', 'Борис');
  type('[name=qty]', '5');
  submit('#order');
  assertEqual(text('#result'), 'Борис: 5 шт.');
});
test('стандартная отправка отменена', () => {
  const event = new Event('submit', { bubbles: true, cancelable: true });
  $('#order').dispatchEvent(event);
  assert(event.defaultPrevented, 'Нет preventDefault');
});
JS,
            'hints' => [
                'Событие формы — `submit`. Отмена стандартного действия — `preventDefault`.',
                'Значение поля — свойство `value`.',
            ],
            'solution' => <<<'HTML'
<form id="order">
  <p><label>Имя <input type="text" name="customer"></label></p>
  <p><label>Порций <input type="number" name="qty" value="1" min="1"></label></p>
  <button type="submit">Заказать</button>
</form>
<p id="result"></p>

<script>
  const form = document.querySelector('#order');
  const result = document.querySelector('#result');

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    const customer = form.elements.customer.value;
    const qty = Number(form.elements.qty.value);

    result.textContent = `${customer}: ${qty} шт.`;
  });
</script>
HTML,
        ],
        [
            'id' => 'calc-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'html',
            'title' => 'Калькулятор считает странно',
            'text' => "Калькулятор должен складывать стоимость заказа и доставки. В нём две ошибки.\n\n1. Для 500 и 200 получается не 700. Попробуй в предпросмотре.\n2. Расчёт срабатывает только по щелчку на кнопке. Если нажать Enter в поле, страница перезагружается и ничего не считает.",
            'starter' => <<<'HTML'
<form id="calc">
  <p><label>Заказ, ₽ <input type="number" name="order" value="500"></label></p>
  <p><label>Доставка, ₽ <input type="number" name="delivery" value="200"></label></p>
  <button type="submit">Посчитать</button>
</form>
<p>Итого: <output id="total">—</output></p>

<script>
  const form = document.querySelector('#calc');
  const total = document.querySelector('#total');

  form.querySelector('button').addEventListener('click', () => {
    total.textContent = form.elements.order.value + form.elements.delivery.value;
  });
</script>
HTML,
            'tests' => <<<'JS'
test('500 + 200 = 700', () => { submit('#calc'); assertEqual(text('#total'), '700'); });
test('другие числа: 1200 + 0', () => {
  type('[name=order]', '1200');
  type('[name=delivery]', '0');
  submit('#calc');
  assertEqual(text('#total'), '1200');
});
test('пустое поле доставки считается нулём', () => {
  type('[name=order]', '350');
  type('[name=delivery]', '');
  submit('#calc');
  assertEqual(text('#total'), '350');
});
test('отправка формы не перезагружает страницу', () => {
  const event = new Event('submit', { bubbles: true, cancelable: true });
  $('#calc').dispatchEvent(event);
  assert(event.defaultPrevented, 'Нет preventDefault в обработчике submit');
});
JS,
            'hints' => [
                'Значения полей — строки, и плюс их склеивает. Нужны числа: `Number(form.elements.order.value)`.',
                'Обработчик должен висеть на событии `submit` самой формы и вызывать `event.preventDefault()`.',
            ],
            'solution' => <<<'HTML'
<form id="calc">
  <p><label>Заказ, ₽ <input type="number" name="order" value="500"></label></p>
  <p><label>Доставка, ₽ <input type="number" name="delivery" value="200"></label></p>
  <button type="submit">Посчитать</button>
</form>
<p>Итого: <output id="total">—</output></p>

<script>
  const form = document.querySelector('#calc');
  const total = document.querySelector('#total');

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    total.textContent = Number(form.elements.order.value) + Number(form.elements.delivery.value);
  });
</script>
HTML,
        ],
        [
            'id' => 'counter',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Счётчик символов',
            'text' => "У отзыва лимит — 50 символов. Напиши скрипт, который при каждом изменении текста:\n\n- показывает в `#left`, сколько символов осталось (число может стать отрицательным);\n- если лимит превышен, добавляет элементу `#left` класс `over` и отключает кнопку отправки;\n- если текст снова уложился в лимит — убирает класс и включает кнопку.",
            'starter' => <<<'HTML'
<style>
  .over { color: rgb(179, 55, 44); font-weight: 700; }
</style>

<form id="review">
  <p><label for="text">Отзыв</label></p>
  <textarea id="text" name="text" rows="3" cols="40"></textarea>
  <p>Осталось символов: <span id="left">50</span></p>
  <button type="submit">Отправить</button>
</form>

<script>
  const LIMIT = 50;

</script>
HTML,
            'tests' => <<<'JS'
const state = () => [text('#left'), $('#left').classList.contains('over'), $('#review button').disabled];
test('изначально осталось 50', () => assertEqual(state(), ['50', false, false]));
test('10 символов → осталось 40', () => { type('#text', 'а'.repeat(10)); assertEqual(state(), ['40', false, false]); });
test('ровно 50 символов → 0, лимит не превышен', () => { type('#text', 'а'.repeat(50)); assertEqual(state(), ['0', false, false]); });
test('51 символ → -1, класс over, кнопка отключена', () => { type('#text', 'а'.repeat(51)); assertEqual(state(), ['-1', true, true]); });
test('текст сократили → всё вернулось', () => { type('#text', 'кратко'); assertEqual(state(), ['44', false, false]); });
JS,
            'hints' => [
                'Событие — `input` на `textarea`. Осталось: `LIMIT - textarea.value.length`.',
                'Превышение — это `left < 0`. Класс: `counter.classList.toggle(\'over\', left < 0)`, кнопка: `button.disabled = left < 0`.',
            ],
            'solution' => <<<'HTML'
<style>
  .over { color: rgb(179, 55, 44); font-weight: 700; }
</style>

<form id="review">
  <p><label for="text">Отзыв</label></p>
  <textarea id="text" name="text" rows="3" cols="40"></textarea>
  <p>Осталось символов: <span id="left">50</span></p>
  <button type="submit">Отправить</button>
</form>

<script>
  const LIMIT = 50;
  const textarea = document.querySelector('#text');
  const counter = document.querySelector('#left');
  const button = document.querySelector('#review button');

  textarea.addEventListener('input', () => {
    const left = LIMIT - textarea.value.length;
    counter.textContent = left;
    counter.classList.toggle('over', left < 0);
    button.disabled = left < 0;
  });
</script>
HTML,
        ],
        [
            'id' => 'validation',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'html',
            'title' => 'Проверка с сообщениями',
            'text' => "Напиши проверку формы регистрации. У формы стоит `novalidate`, так что браузер сам ничего не проверяет.\n\nПри отправке:\n\n- почта должна содержать `@`, иначе под ней сообщение `Введите корректную почту`;\n- пароль — не короче 8 символов, иначе `Пароль не короче 8 символов`;\n- у поля с ошибкой появляется класс `invalid`, у поля без ошибки он убирается, а сообщение очищается;\n- если ошибок нет, показывается блок `#success` (свойство `hidden`), а если есть — он скрыт.\n\nСообщения выводятся в абзацы с атрибутом `data-error-for`. Пробелы по краям почты не учитываются.",
            'starter' => <<<'HTML'
<style>
  .invalid { outline: 2px solid rgb(179, 55, 44); }
  .error { color: rgb(179, 55, 44); margin: 4px 0 12px; min-height: 1.2em; }
</style>

<form id="signup" novalidate>
  <label for="email">Почта</label><br>
  <input type="email" id="email" name="email">
  <p class="error" data-error-for="email"></p>

  <label for="password">Пароль</label><br>
  <input type="password" id="password" name="password">
  <p class="error" data-error-for="password"></p>

  <button type="submit">Зарегистрироваться</button>
</form>
<p id="success" hidden>Готово! Проверьте почту.</p>

<script>

</script>
HTML,
            'tests' => <<<'JS'
const fill = (email, password) => { type('#email', email); type('#password', password); submit('#signup'); };
const errors = () => [text('[data-error-for=email]'), text('[data-error-for=password]')];
const invalid = () => [$('#email').classList.contains('invalid'), $('#password').classList.contains('invalid')];
test('обе ошибки сразу', () => {
  fill('anna', '123');
  assertEqual(errors(), ['Введите корректную почту', 'Пароль не короче 8 символов']);
  assertEqual(invalid(), [true, true]);
  assertEqual($('#success').hidden, true);
});
test('исправили почту — её ошибка исчезла, ошибка пароля осталась', () => {
  fill('anna@mail.ru', '123');
  assertEqual(errors(), ['', 'Пароль не короче 8 символов']);
  assertEqual(invalid(), [false, true]);
});
test('всё верно — ошибок нет, показан успех', () => {
  fill('anna@mail.ru', 'supersecret');
  assertEqual(errors(), ['', '']);
  assertEqual(invalid(), [false, false]);
  assertEqual($('#success').hidden, false);
});
test('снова ошибка — успех скрывается', () => { fill('', 'supersecret'); assertEqual([$('#success').hidden, errors()[0]], [true, 'Введите корректную почту']); });
test('пароль ровно из 8 символов подходит', () => { fill('a@b.ru', '12345678'); assertEqual(errors(), ['', '']); });
test('пробелы вокруг почты не мешают', () => { fill('  a@b.ru  ', '12345678'); assertEqual($('#success').hidden, false); });
test('страница не перезагружается', () => {
  const event = new Event('submit', { bubbles: true, cancelable: true });
  $('#signup').dispatchEvent(event);
  assert(event.defaultPrevented, 'Нет preventDefault');
});
JS,
            'hints' => [
                'Раздели на две функции, как в уроке: `validate(data)` возвращает объект ошибок, `showErrors(errors)` выводит их и снимает старые.',
                'Абзац для сообщения: `` form.querySelector(`[data-error-for="${name}"]`) ``. Для поля без ошибки записывай в него пустую строку.',
                'Успех: `success.hidden = Object.keys(errors).length > 0;`',
            ],
            'solution' => <<<'HTML'
<style>
  .invalid { outline: 2px solid rgb(179, 55, 44); }
  .error { color: rgb(179, 55, 44); margin: 4px 0 12px; min-height: 1.2em; }
</style>

<form id="signup" novalidate>
  <label for="email">Почта</label><br>
  <input type="email" id="email" name="email">
  <p class="error" data-error-for="email"></p>

  <label for="password">Пароль</label><br>
  <input type="password" id="password" name="password">
  <p class="error" data-error-for="password"></p>

  <button type="submit">Зарегистрироваться</button>
</form>
<p id="success" hidden>Готово! Проверьте почту.</p>

<script>
  const form = document.querySelector('#signup');
  const success = document.querySelector('#success');

  function validate(data) {
    const errors = {};
    if (!data.email.trim().includes('@')) {
      errors.email = 'Введите корректную почту';
    }
    if (data.password.length < 8) {
      errors.password = 'Пароль не короче 8 символов';
    }
    return errors;
  }

  function showErrors(errors) {
    for (const name of ['email', 'password']) {
      const message = errors[name] ?? '';
      form.elements[name].classList.toggle('invalid', message !== '');
      form.querySelector(`[data-error-for="${name}"]`).textContent = message;
    }
  }

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    const errors = validate({
      email: form.elements.email.value,
      password: form.elements.password.value,
    });

    showErrors(errors);
    success.hidden = Object.keys(errors).length > 0;
  });
</script>
HTML,
        ],
        [
            'id' => 'search',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'html',
            'title' => 'Поиск по списку',
            'text' => "Сделай живой поиск: по мере ввода в поле `#search` в списке остаются только подходящие напитки.\n\n- Пункт подходит, если его текст содержит введённую строку. Регистр букв и пробелы по краям запроса не важны.\n- Неподходящие пункты скрываются свойством `hidden`.\n- При пустом запросе видны все.\n- Если не подошёл ни один пункт, показывается абзац `#empty`.",
            'starter' => <<<'HTML'
<p><label for="search">Поиск</label> <input type="search" id="search"></p>

<ul id="drinks">
  <li>Эспрессо</li>
  <li>Американо</li>
  <li>Капучино</li>
  <li>Латте</li>
  <li>Раф</li>
  <li>Флэт уайт</li>
</ul>
<p id="empty" hidden>Ничего не найдено.</p>

<script>

</script>
HTML,
            'tests' => <<<'JS'
const visible = () => $$('#drinks li').filter((li) => !li.hidden).map((li) => li.textContent);
test('изначально видны все шесть', () => assertEqual(visible().length, 6));
test("запрос «а» оставляет названия с буквой «а»", () => { type('#search', 'а'); assertEqual(visible(), ['Американо', 'Капучино', 'Латте', 'Раф', 'Флэт уайт']); });
test('запрос «ка»', () => { type('#search', 'ка'); assertEqual(visible(), ['Американо', 'Капучино']); });
test('регистр не важен', () => { type('#search', 'ЛАТТЕ'); assertEqual(visible(), ['Латте']); });
test('пробелы по краям не важны', () => { type('#search', '  раф '); assertEqual(visible(), ['Раф']); });
test('ничего не найдено — показан #empty', () => { type('#search', 'чай'); assertEqual([visible(), $('#empty').hidden], [[], false]); });
test('пустой запрос возвращает всё и прячет #empty', () => { type('#search', ''); assertEqual([visible().length, $('#empty').hidden], [6, true]); });
JS,
            'hints' => [
                'Событие `input` на поле. Запрос: `search.value.trim().toLowerCase()`.',
                'Для каждого пункта: `li.hidden = !li.textContent.toLowerCase().includes(query);`',
                'Заведи счётчик видимых пунктов. В конце: `empty.hidden = count > 0;`',
            ],
            'solution' => <<<'HTML'
<p><label for="search">Поиск</label> <input type="search" id="search"></p>

<ul id="drinks">
  <li>Эспрессо</li>
  <li>Американо</li>
  <li>Капучино</li>
  <li>Латте</li>
  <li>Раф</li>
  <li>Флэт уайт</li>
</ul>
<p id="empty" hidden>Ничего не найдено.</p>

<script>
  const search = document.querySelector('#search');
  const items = document.querySelectorAll('#drinks li');
  const empty = document.querySelector('#empty');

  search.addEventListener('input', () => {
    const query = search.value.trim().toLowerCase();
    let count = 0;

    for (const li of items) {
      const match = li.textContent.toLowerCase().includes(query);
      li.hidden = !match;
      if (match) count++;
    }

    empty.hidden = count > 0;
  });
</script>
HTML,
        ],
    ],
    'cards' => [
        ['На какое событие вешать проверку формы и почему?', 'На `submit` формы. Оно срабатывает и при щелчке по кнопке, и при нажатии Enter в поле.'],
        ['Что будет, если в обработчике `submit` забыть `preventDefault()`?', 'Браузер отправит форму обычным способом: страница перезагрузится, результат работы скрипта пропадёт.'],
        ['Какого типа значение `input.value` у поля `type="number"`?', 'Строка. Перед арифметикой нужно `Number(input.value)`, иначе плюс склеит строки.'],
        ['Как организовать свою проверку формы?', 'Две функции: `validate(data)` возвращает объект ошибок и ничего не знает о странице; `showErrors(errors)` выводит ошибки и снимает устаревшие.'],
        ['Заменяет ли проверка в JavaScript проверку на сервере?', 'Нет. Её легко обойти, она нужна только для удобства. Сервер проверяет те же правила заново.'],
    ],
];
