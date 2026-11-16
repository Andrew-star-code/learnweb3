# Формы и валидация

В блоке про вёрстку форма отправляла данные на сервер, и страница перезагружалась. Теперь перехватим отправку скриптом: проверим данные, покажем ошибки рядом с полями и решим сами, что делать дальше.

## Перехватить отправку

```js
const form = document.querySelector('#signup');

form.addEventListener('submit', (event) => {
  event.preventDefault();          // не перезагружать страницу
  // …читаем и проверяем данные
});
```

Слушать нужно событие **`submit` у формы**, а не `click` у кнопки. Форму отправляют ещё и клавишей Enter в поле — щелчка по кнопке при этом может не быть, а `submit` случится в любом случае.

Без `preventDefault()` браузер отправит форму обычным способом, страница перезагрузится, и результат работы скрипта пропадёт. Если после нажатия кнопки всё «мигает и сбрасывается» — забыт именно он.

## Прочитать значения

```js
form.elements.email.value;       // по атрибуту name
form.elements.agree.checked;     // флажок: true или false
form.elements.plan.value;        // группа переключателей: value выбранного
```

Все значения полей — **строки**, даже у `type="number"`. Перед арифметикой превращай их в числа: `Number(form.elements.qty.value)`. Иначе `'100' + '200'` даст `'100200'`.

Строки перед проверкой стоит очищать от пробелов по краям: `value.trim()`.

Собрать всю форму разом помогает `FormData`:

```js
const data = Object.fromEntries(new FormData(form));
// { email: 'a@b.c', password: '…' }
```

## Своя проверка

Встроенная проверка браузера (`required`, `minlength`) показывает собственные всплывающие подсказки, которые нельзя оформить. Когда нужны свои сообщения, её отключают атрибутом `novalidate` у формы и проверяют в скрипте.

Удобная схема — отделить **проверку** от **показа**:

```js
// 1. Чистая функция: данные на входе, объект ошибок на выходе
function validate(data) {
  const errors = {};
  if (!data.email.includes('@')) {
    errors.email = 'Введите корректную почту';
  }
  if (data.password.length < 8) {
    errors.password = 'Пароль не короче 8 символов';
  }
  return errors;
}

// 2. Показ: пройти по полям и вывести ошибку или очистить её
function showErrors(form, errors) {
  for (const field of form.querySelectorAll('[name]')) {
    const message = errors[field.name] ?? '';
    field.classList.toggle('invalid', message !== '');
    form.querySelector(`[data-error-for="${field.name}"]`).textContent = message;
  }
}

form.addEventListener('submit', (event) => {
  event.preventDefault();
  const errors = validate(Object.fromEntries(new FormData(form)));
  showErrors(form, errors);
  if (Object.keys(errors).length === 0) {
    // всё хорошо — отправляем
  }
});
```

Почему так:

- `validate` ничего не знает о странице. Её легко проверить и использовать повторно.
- `showErrors` на каждом вызове **и ставит, и снимает** ошибки. Частый баг — ошибку показали, пользователь исправил поле, а сообщение осталось.

## Реакция на ввод

Событие `input` срабатывает на каждое изменение поля. На нём строят счётчики символов, поиск по списку, проверку «на лету».

```js
textarea.addEventListener('input', () => {
  counter.textContent = `${textarea.value.length} / 200`;
});
```

Кнопку можно отключать, пока форма заполнена неверно: `button.disabled = true`.

## Проверка в браузере — не защита

Всё, что проверяет JavaScript, человек может обойти: отключить скрипты, поправить страницу в инструментах разработчика, отправить запрос напрямую. Проверка в браузере нужна для удобства — чтобы сразу подсказать, что не так. **Сервер обязан проверять те же правила заново.** Мы вернёмся к этому в блоках про PHP и безопасность.

## Глубже

- [Свойства и методы формы](https://learn.javascript.ru/form-elements) — learn.javascript.ru
- [Отправка формы: событие и метод submit](https://learn.javascript.ru/forms-submit) — learn.javascript.ru
- [Валидация форм на стороне клиента](https://developer.mozilla.org/ru/docs/Learn/Forms/Form_validation) — MDN
