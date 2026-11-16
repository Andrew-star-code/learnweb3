# Bootstrap: компоненты

Компонент — готовый кусок интерфейса: кнопка, карточка, таблица, форма, модальное окно. У каждого своя разметка, которую берут из документации и подгоняют под себя. Помнить её наизусть не нужно — нужно знать, что бывает, и уметь быстро найти.

## Кнопки

```html
<button type="button" class="btn btn-primary">Сохранить</button>
<button type="button" class="btn btn-outline-secondary btn-sm">Отмена</button>
<a class="btn btn-danger" href="/delete">Удалить</a>
```

Базовый класс `btn` плюс вариант: `btn-primary`, `btn-secondary`, `btn-success`, `btn-danger`, `btn-warning`, `btn-light`, `btn-dark`, `btn-link`. С `outline` — только рамка. Размеры: `btn-sm`, `btn-lg`.

Схема «базовый класс + модификатор» повторяется во всех компонентах: `alert alert-warning`, `badge text-bg-success`, `table table-striped`.

## Карточка

```html
<div class="card">
  <img src="coffee.jpg" class="card-img-top" alt="…">
  <div class="card-body">
    <h5 class="card-title">Эфиопия</h5>
    <p class="card-text">Светлая обжарка.</p>
    <a href="#" class="btn btn-primary">Купить</a>
  </div>
</div>
```

## Формы

```html
<div class="mb-3">
  <label for="email" class="form-label">Почта</label>
  <input type="email" class="form-control" id="email" name="email">
  <div class="form-text">Никому не покажем.</div>
</div>

<div class="form-check">
  <input class="form-check-input" type="checkbox" id="agree" name="agree">
  <label class="form-check-label" for="agree">Согласен</label>
</div>
```

`form-control` — для текстовых полей и `textarea`, `form-select` — для выпадающего списка, `form-check-input` — для флажков и переключателей. Всё, что ты знаешь про `label`, `name` и `type`, остаётся в силе: Bootstrap меняет только внешний вид.

## Таблица

```html
<table class="table table-striped table-hover">
  <thead>
    <tr><th>Товар</th><th>Цена</th></tr>
  </thead>
  <tbody>
    <tr><td>Эфиопия</td><td>890 ₽</td></tr>
  </tbody>
</table>
```

Чтобы широкая таблица не ломала страницу на телефоне, её оборачивают в `<div class="table-responsive">`.

## Сообщения и метки

```html
<div class="alert alert-success" role="alert">Заказ оформлен.</div>
<span class="badge text-bg-primary">Новинка</span>
```

## Компоненты с поведением

Модальные окна, выпадающие меню, сворачивающийся навбар, вкладки не работают на одном CSS. Им нужен файл `bootstrap.bundle.min.js`, подключённый перед закрывающим `</body>`.

Управляются они **data-атрибутами** — писать JavaScript самому не нужно:

```html
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirm">
  Удалить
</button>

<div class="modal fade" id="confirm" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Удалить запись?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">Это действие нельзя отменить.</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-danger">Удалить</button>
      </div>
    </div>
  </div>
</div>
```

- `data-bs-toggle="modal"` — что делать по нажатию.
- `data-bs-target="#confirm"` — с каким элементом (селектор).
- `data-bs-dismiss="modal"` — закрыть.

Скрипт Bootstrap при загрузке находит эти атрибуты и сам вешает обработчики. Если компонент «не работает», причин обычно три: не подключён скрипт, опечатка в `data-bs-target`, или атрибуты написаны по-старому, без `bs` — как в Bootstrap 4.

## Навбар

```html
<nav class="navbar navbar-expand-md bg-light">
  <div class="container">
    <a class="navbar-brand" href="/">Зерно</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu"
            aria-controls="menu" aria-expanded="false" aria-label="Открыть меню">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="/menu">Меню</a></li>
      </ul>
    </div>
  </div>
</nav>
```

`navbar-expand-md` — «от 768px показывай меню в ряд, уже — прячь под кнопку». Это тот же приём, что в задании про меню из урока об адаптивности, плюс готовое открытие по кнопке.

## Настройка под себя

Bootstrap 5 построен на переменных CSS, поэтому тему можно менять без пересборки:

```css
:root {
  --bs-primary: #2f6f4f;
  --bs-body-font-family: Georgia, serif;
}

.btn-primary {
  --bs-btn-bg: #2f6f4f;
  --bs-btn-border-color: #2f6f4f;
}
```

Свои стили подключают **после** файла Bootstrap — при равной специфичности побеждает то, что ниже.

## Глубже

- [Компоненты](https://getbootstrap.com/docs/5.3/components/buttons/) — документация Bootstrap, раздел Components
- [Формы](https://getbootstrap.com/docs/5.3/forms/overview/) — документация Bootstrap
- [Переменные CSS](https://getbootstrap.com/docs/5.3/customize/css-variables/) — документация Bootstrap
