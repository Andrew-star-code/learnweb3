# Классы

Класс — это чертёж для объектов одного вида: какие у них данные и что они умеют. По чертежу создают сколько угодно объектов (их называют экземплярами).

```js
class User {
  constructor(name, age) {     // вызывается при создании объекта
    this.name = name;          // this — создаваемый объект
    this.age = age;
  }

  greet() {                    // метод
    return `Привет, я ${this.name}`;
  }
}

const anna = new User('Аня', 30);
anna.greet();     // 'Привет, я Аня'
anna.name;        // 'Аня'
```

Внутри методов к данным объекта обращаются **только через `this`**. Написать просто `name` вместо `this.name` — частая ошибка: получится `ReferenceError` или обращение к посторонней переменной.

## Геттеры и статические методы

```js
class Rectangle {
  constructor(width, height) {
    this.width = width;
    this.height = height;
  }

  get area() {                         // читается как свойство: rect.area
    return this.width * this.height;
  }

  static square(side) {                // вызывается у класса: Rectangle.square(5)
    return new Rectangle(side, side);
  }
}
```

Геттер удобен для значений, которые вычисляются из других полей: его нельзя забыть обновить.

## Приватные поля

Поле с решёткой в начале имени доступно только внутри класса.

```js
class Counter {
  #count = 0;

  increment() { this.#count++; }
  get value() { return this.#count; }
}

const c = new Counter();
c.increment();
c.value;       // 1
c.count;       // undefined — снаружи поля не видно
```

Это то же приватное состояние, что и в замыканиях, только с другим синтаксисом.

## Наследование

Класс может расширять другой: получает все его методы и добавляет или переопределяет свои.

```js
class Admin extends User {
  constructor(name, age, level) {
    super(name, age);            // сначала вызвать конструктор родителя
    this.level = level;
  }

  greet() {
    return `${super.greet()}, администратор`;   // super — доступ к методу родителя
  }
}

new Admin('Борис', 40, 2) instanceof User;   // true
```

В конструкторе наследника `super(...)` обязан идти до первого обращения к `this`.

Наследованием легко увлечься. Используй его, когда один класс действительно является разновидностью другого («администратор — это пользователь»). Если нужно просто переиспользовать код, чаще лучше передать один объект в другой.

## Потеря this

`this` определяется в момент **вызова**: это объект перед точкой. Если метод оторвать от объекта, `this` потеряется.

```js
const greet = anna.greet;
greet();                        // TypeError: this равен undefined

setTimeout(anna.greet, 1000);   // та же проблема: передали функцию без объекта
```

Два способа починить:

```js
setTimeout(() => anna.greet(), 1000);       // обернуть в стрелку
setTimeout(anna.greet.bind(anna), 1000);    // привязать this навсегда
```

Эта ошибка встретится тебе в обработчиках событий — запомни, как она выглядит.

## Класс или обычный объект

- Данные без поведения (пользователь из базы, настройки) — обычный объект.
- Несколько однотипных объектов с данными и методами — класс.
- Один-единственный объект с приватным состоянием — подойдёт и функция с замыканием.

## Глубже

- [Класс: базовый синтаксис](https://learn.javascript.ru/class) и [наследование классов](https://learn.javascript.ru/class-inheritance) — learn.javascript.ru
- [Приватные и защищённые свойства](https://learn.javascript.ru/private-protected-properties-methods) — learn.javascript.ru
- [Привязка контекста к функции](https://learn.javascript.ru/bind) — learn.javascript.ru
