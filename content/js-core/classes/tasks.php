<?php
return [
    'tasks' => [
        [
            'id' => 'this-quiz',
            'type' => 'quiz',
            'title' => 'Оторванный метод',
            'text' => "```js\nclass User {\n  constructor(name) { this.name = name; }\n  greet() { return `Привет, я \${this.name}`; }\n}\n\nconst anna = new User('Аня');\nconst greet = anna.greet;\ngreet();\n```\n\nЧто произойдёт при вызове `greet()`?",
            'options' => ["Вернётся 'Привет, я Аня'", "Вернётся 'Привет, я undefined'", 'TypeError: this равен undefined', 'ReferenceError: greet is not defined'],
            'answer' => 2,
            'explain' => '`this` определяется в момент вызова — это объект перед точкой. У вызова `greet()` перед точкой ничего нет, поэтому `this` равен `undefined`, и чтение `this.name` падает. Исправление: `() => anna.greet()` или `anna.greet.bind(anna)`.',
        ],
        [
            'id' => 'rectangle',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Первый класс',
            'text' => "Допиши класс `Rectangle`:\n\n- конструктор сохраняет ширину и высоту;\n- геттер `area` возвращает площадь;\n- геттер `isSquare` возвращает `true`, если стороны равны;\n- метод `scale(factor)` возвращает **новый** прямоугольник, увеличенный в `factor` раз.",
            'starter' => <<<'JS'
class Rectangle {
  constructor(width, height) {
    ___
  }

  get area() {
    return ___;
  }

  get isSquare() {
    return ___;
  }

  scale(factor) {
    return new ___;
  }
}

const rect = new Rectangle(3, 4);
console.log(rect.area, rect.isSquare, rect.scale(2));
JS,
            'tests' => <<<'JS'
test('поля width и height', () => { const r = new Rectangle(3, 4); assertEqual([r.width, r.height], [3, 4]); });
test('area', () => assertEqual(new Rectangle(3, 4).area, 12));
test('isSquare', () => assertEqual([new Rectangle(3, 4).isSquare, new Rectangle(5, 5).isSquare], [false, true]));
test('scale возвращает новый прямоугольник', () => {
  const r = new Rectangle(3, 4);
  const big = r.scale(2);
  assert(big instanceof Rectangle, 'scale должен вернуть Rectangle');
  assertEqual([big.width, big.height, big.area], [6, 8, 48]);
});
test('scale не меняет исходный', () => { const r = new Rectangle(3, 4); r.scale(2); assertEqual(r.area, 12); });
JS,
            'hints' => [
                'В конструкторе: `this.width = width; this.height = height;`',
                'В геттерах всё через `this`: `this.width * this.height` и `this.width === this.height`.',
                'В `scale`: `new Rectangle(this.width * factor, this.height * factor)`.',
            ],
            'solution' => <<<'JS'
class Rectangle {
  constructor(width, height) {
    this.width = width;
    this.height = height;
  }

  get area() {
    return this.width * this.height;
  }

  get isSquare() {
    return this.width === this.height;
  }

  scale(factor) {
    return new Rectangle(this.width * factor, this.height * factor);
  }
}
JS,
        ],
        [
            'id' => 'timer-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Забытый this',
            'text' => "Класс `Playlist` хранит список песен. В нём три ошибки, и все про `this`. Чини по одной, читая сообщения.",
            'starter' => <<<'JS'
class Playlist {
  constructor(title) {
    title = title;
    this.songs = [];
  }

  add(song) {
    songs.push(song);
  }

  get size() {
    return songs.length;
  }
}

const list = new Playlist('В дорогу');
list.add('Песня 1');
console.log(list.title, list.size);
JS,
            'tests' => <<<'JS'
test('title сохраняется', () => assertEqual(new Playlist('В дорогу').title, 'В дорогу'));
test('новый плейлист пуст', () => assertEqual(new Playlist('x').size, 0));
test('add добавляет песню', () => {
  const p = new Playlist('x');
  p.add('а'); p.add('б');
  assertEqual([p.size, p.songs], [2, ['а', 'б']]);
});
test('плейлисты независимы', () => {
  const a = new Playlist('a');
  const b = new Playlist('b');
  a.add('песня');
  assertEqual(b.size, 0);
});
JS,
            'hints' => [
                '`songs is not defined`: внутри метода просто `songs` — это поиск обычной переменной. Поле объекта — `this.songs`.',
                'В конструкторе `title = title` присваивает параметр самому себе. Нужно `this.title = title`.',
            ],
            'solution' => <<<'JS'
class Playlist {
  constructor(title) {
    this.title = title;
    this.songs = [];
  }

  add(song) {
    this.songs.push(song);
  }

  get size() {
    return this.songs.length;
  }
}
JS,
        ],
        [
            'id' => 'stack',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Стек с приватным полем',
            'text' => "Стек — это стопка: кладём сверху и берём сверху. Так работает кнопка «назад» в браузере и отмена действий в редакторе.\n\nНапиши класс `Stack` с приватным полем `#items`:\n\n- `push(value)` — положить наверх;\n- `pop()` — снять верхний элемент и вернуть его (`undefined`, если пусто);\n- `peek()` — вернуть верхний элемент, не снимая;\n- геттер `size` — количество элементов;\n- геттер `isEmpty`.",
            'starter' => <<<'JS'
class Stack {
  #items = [];

}

const stack = new Stack();
stack.push(1);
stack.push(2);
console.log(stack.pop(), stack.peek(), stack.size);
JS,
            'tests' => <<<'JS'
test('новый стек пуст', () => { const s = new Stack(); assertEqual([s.size, s.isEmpty], [0, true]); });
test('push и size', () => { const s = new Stack(); s.push('a'); s.push('b'); assertEqual([s.size, s.isEmpty], [2, false]); });
test('pop возвращает последний положенный', () => { const s = new Stack(); s.push(1); s.push(2); assertEqual([s.pop(), s.pop()], [2, 1]); });
test('peek не снимает элемент', () => { const s = new Stack(); s.push(1); s.push(2); assertEqual([s.peek(), s.size], [2, 2]); });
test('pop и peek на пустом стеке → undefined', () => { const s = new Stack(); assertEqual([s.pop(), s.peek()], [undefined, undefined]); });
test('массив недоступен снаружи', () => { const s = new Stack(); s.push(1); assertEqual(Object.keys(s), []); });
JS,
            'hints' => [
                'К приватному полю обращаются с решёткой: `this.#items.push(value)`.',
                '`pop` у массива делает ровно то, что нужно. Верхний элемент без снятия: `this.#items.at(-1)`.',
                'Геттеры: `get size() { return this.#items.length; }` и `get isEmpty() { return this.#items.length === 0; }`.',
            ],
            'solution' => <<<'JS'
class Stack {
  #items = [];

  push(value) {
    this.#items.push(value);
  }

  pop() {
    return this.#items.pop();
  }

  peek() {
    return this.#items.at(-1);
  }

  get size() {
    return this.#items.length;
  }

  get isEmpty() {
    return this.#items.length === 0;
  }
}
JS,
        ],
        [
            'id' => 'inherit',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Наследование',
            'text' => "Класс `Product` уже написан. Добавь класс `DigitalProduct`, который его расширяет:\n\n- конструктор принимает `name`, `price` и `sizeMb` (размер файла);\n- доставка цифрового товара бесплатна: `deliveryCost()` возвращает `0`;\n- `describe()` возвращает описание родителя с добавкой размера: `Курс: 1500 ₽ (файл 700 МБ)`.",
            'starter' => <<<'JS'
class Product {
  constructor(name, price) {
    this.name = name;
    this.price = price;
  }

  deliveryCost() {
    return 300;
  }

  describe() {
    return `${this.name}: ${this.price} ₽`;
  }
}

// class DigitalProduct ...
JS,
            'tests' => <<<'JS'
test('поля родителя и своё поле', () => {
  const d = new DigitalProduct('Курс', 1500, 700);
  assertEqual([d.name, d.price, d.sizeMb], ['Курс', 1500, 700]);
});
test('DigitalProduct — это Product', () => assert(new DigitalProduct('x', 1, 1) instanceof Product, 'Нет наследования от Product'));
test('доставка бесплатна', () => assertEqual(new DigitalProduct('x', 1, 1).deliveryCost(), 0));
test('describe дополняет описание родителя', () => assertEqual(new DigitalProduct('Курс', 1500, 700).describe(), 'Курс: 1500 ₽ (файл 700 МБ)'));
test('обычный товар не изменился', () => {
  const p = new Product('Книга', 500);
  assertEqual([p.deliveryCost(), p.describe()], [300, 'Книга: 500 ₽']);
});
JS,
            'hints' => [
                'Каркас: `class DigitalProduct extends Product { constructor(name, price, sizeMb) { super(name, price); this.sizeMb = sizeMb; } }`',
                'Метод с тем же именем, что у родителя, заменяет его: `deliveryCost() { return 0; }`',
                'Вызвать метод родителя: `super.describe()`. К результату приклей хвост.',
            ],
            'solution' => <<<'JS'
class Product {
  constructor(name, price) {
    this.name = name;
    this.price = price;
  }

  deliveryCost() {
    return 300;
  }

  describe() {
    return `${this.name}: ${this.price} ₽`;
  }
}

class DigitalProduct extends Product {
  constructor(name, price, sizeMb) {
    super(name, price);
    this.sizeMb = sizeMb;
  }

  deliveryCost() {
    return 0;
  }

  describe() {
    return `${super.describe()} (файл ${this.sizeMb} МБ)`;
  }
}
JS,
        ],
        [
            'id' => 'account-plan',
            'type' => 'plan',
            'title' => 'Банковский счёт: план',
            'text' => "Задача: класс `Account` для счёта.\n\n- Счёт создаётся с начальным балансом (по умолчанию ноль).\n- `deposit(amount)` пополняет счёт.\n- `withdraw(amount)` снимает деньги. Если денег не хватает, баланс не меняется, а метод возвращает `false`. При успехе возвращает `true`.\n- Баланс можно прочитать, но нельзя изменить напрямую — только через методы.\n- `history` возвращает список успешных операций по порядку: `[{ type: 'deposit', amount: 100 }, ...]`.\n\nЗапиши: какие данные хранит объект, какие из них приватные, и что по шагам делает `withdraw`.",
            'reference' => <<<'MD'
**Данные:** баланс и массив операций. Оба приватные (`#balance`, `#history`): если их открыть, баланс можно будет поменять в обход `deposit` и `withdraw`, и история перестанет ему соответствовать.

**Доступ на чтение:** геттеры `balance` и `history`. Геттер `history` отдаёт копию массива — иначе снаружи в настоящую историю можно будет что-нибудь дописать.

**Шаги `withdraw(amount)`:**

1. Если сумма больше баланса — вернуть `false`, ничего не меняя.
2. Уменьшить баланс на сумму.
3. Добавить операцию в историю.
4. Вернуть `true`.

`deposit` — то же без первого шага. Проверка идёт до любых изменений: это ранний выход, благодаря которому объект никогда не остаётся в наполовину изменённом состоянии.
MD,
        ],
        [
            'id' => 'account',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Банковский счёт: код',
            'text' => 'Напиши класс `Account` по своему плану.',
            'starter' => "",
            'tests' => <<<'JS'
test('начальный баланс', () => assertEqual([new Account(500).balance, new Account().balance], [500, 0]));
test('deposit увеличивает баланс', () => { const a = new Account(100); a.deposit(50); assertEqual(a.balance, 150); });
test('withdraw уменьшает баланс и возвращает true', () => { const a = new Account(100); assertEqual([a.withdraw(30), a.balance], [true, 70]); });
test('можно снять всё до нуля', () => { const a = new Account(100); assertEqual([a.withdraw(100), a.balance], [true, 0]); });
test('не хватает денег → false, баланс прежний', () => { const a = new Account(100); assertEqual([a.withdraw(150), a.balance], [false, 100]); });
test('история успешных операций', () => {
  const a = new Account(100);
  a.deposit(50); a.withdraw(500); a.withdraw(20);
  assertEqual(a.history, [{ type: 'deposit', amount: 50 }, { type: 'withdraw', amount: 20 }]);
});
test('баланс нельзя изменить напрямую', () => {
  const a = new Account(100);
  try { a.balance = 1000000; } catch (e) { /* в строгом режиме присваивание геттеру бросает ошибку — это тоже подходит */ }
  assertEqual(a.balance, 100);
});
test('историю нельзя испортить снаружи', () => {
  const a = new Account(100);
  a.deposit(1);
  a.history.push({ type: 'deposit', amount: 999 });
  assertEqual(a.history.length, 1);
});
JS,
            'hints' => [
                'Поля: `#balance;` и `#history = [];`. В конструкторе: `constructor(balance = 0) { this.#balance = balance; }`',
                'Геттеры: `get balance() { return this.#balance; }` и `get history() { return [...this.#history]; }`',
                'В `withdraw` первым делом: `if (amount > this.#balance) return false;`',
            ],
            'solution' => <<<'JS'
class Account {
  #balance;
  #history = [];

  constructor(balance = 0) {
    this.#balance = balance;
  }

  get balance() {
    return this.#balance;
  }

  get history() {
    return [...this.#history];
  }

  deposit(amount) {
    this.#balance += amount;
    this.#history.push({ type: 'deposit', amount });
  }

  withdraw(amount) {
    if (amount > this.#balance) {
      return false;
    }
    this.#balance -= amount;
    this.#history.push({ type: 'withdraw', amount });
    return true;
  }
}
JS,
        ],
    ],
    'cards' => [
        ['Как внутри метода класса обратиться к данным объекта?', 'Только через `this`: `this.name`. Просто `name` — это поиск обычной переменной, а не поля.'],
        ['Чем определяется значение `this` в методе?', 'Моментом вызова: это объект перед точкой. Если метод оторвать от объекта (`const f = obj.method`), `this` потеряется.'],
        ['Как передать метод объекта как колбэк, не потеряв `this`?', 'Обернуть в стрелку `() => obj.method()` или привязать: `obj.method.bind(obj)`.'],
        ['Что обязательно сделать в конструкторе класса-наследника?', 'Вызвать `super(...)` до первого обращения к `this`.'],
        ['Как сделать поле класса недоступным снаружи?', 'Назвать его с решёткой: `#balance`. Читать снаружи — через геттер.'],
    ],
];
