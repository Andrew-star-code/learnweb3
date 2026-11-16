# Объекты

Объект — набор пар «ключ: значение». Массив отвечает на вопрос «какой элемент под номером N», объект — «какое значение у свойства с таким именем».

```js
const user = {
  name: 'Аня',
  age: 30,
};

user.name;             // 'Аня'
user['age'];           // 30 — то же самое через скобки
user.email;            // undefined — такого свойства нет
user.email = 'a@b.c';  // добавить свойство
delete user.age;       // удалить свойство
```

Квадратные скобки нужны, когда имя свойства лежит в переменной:

```js
const field = 'name';
user[field];           // 'Аня'
user.field;            // undefined — ищет свойство с именем «field»
```

Проверить, есть ли свойство: `'name' in user`.

## Перебор

```js
Object.keys(user);      // ['name', 'email'] — ключи
Object.values(user);    // ['Аня', 'a@b.c'] — значения
Object.entries(user);   // [['name', 'Аня'], ['email', 'a@b.c']] — пары

for (const [key, value] of Object.entries(user)) {
  console.log(key, value);
}
```

Эти три метода превращают объект в массив — и дальше работают все методы массивов.

## Объект как счётчик и как группы

Очень частый приём: ключ объекта — это то, что считаем, значение — сколько раз встретилось.

```js
const counts = {};
for (const word of words) {
  counts[word] = (counts[word] ?? 0) + 1;
}
```

Выражение `counts[word] ?? 0` означает «текущее значение, а если его ещё нет — ноль».

## Переменная хранит ссылку

Главное, что нужно понять про объекты и массивы: переменная хранит не сам объект, а **ссылку** на него.

```js
const a = { x: 1 };
const b = a;       // не копия! та же ссылка
b.x = 2;
a.x;               // 2
```

Отсюда два следствия:

- Функция, которая меняет полученный объект, меняет его для всех. Обычно это неожиданно, поэтому лучше возвращать новый объект.
- `{ x: 1 } === { x: 1 }` даёт `false`: сравниваются ссылки, а это два разных объекта.

Копия делается через `...`:

```js
const copy = { ...user };                       // копия
const updated = { ...user, age: 31 };           // копия с изменённым полем
```

Копия **поверхностная**: вложенные объекты и массивы остаются общими. Полная копия — `structuredClone(user)`.

## Короткие записи

```js
const name = 'Аня';
const age = 30;
const user = { name, age };            // то же, что { name: name, age: age }

const { name: userName, age: userAge } = user;   // разбор объекта в переменные
const { email = 'нет почты' } = user;            // со значением по умолчанию

function show({ name, age }) { /* ... */ }       // разбор прямо в параметрах
```

## Безопасный доступ

```js
user.address.city;       // TypeError, если address нет
user.address?.city;      // undefined, без ошибки
```

`?.` останавливается, если слева `null` или `undefined`. Часто идёт в паре с `??`: `user.address?.city ?? 'не указан'`.

## JSON

Формат, в котором данные путешествуют между браузером и сервером, — это текст, похожий на запись объекта.

```js
JSON.stringify({ a: 1 });      // '{"a":1}' — объект в строку
JSON.parse('{"a":1}');         // { a: 1 } — строку в объект
```

## Глубже

- [Объекты](https://learn.javascript.ru/object) и [копирование по ссылке](https://learn.javascript.ru/object-copy) — learn.javascript.ru
- [Object.keys, values, entries](https://learn.javascript.ru/keys-values-entries) — learn.javascript.ru
- [Опциональная цепочка ?.](https://learn.javascript.ru/optional-chaining) и [JSON](https://learn.javascript.ru/json) — learn.javascript.ru
