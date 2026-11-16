<?php
return [
    'tasks' => [
        [
            'id' => 'reference-quiz',
            'type' => 'quiz',
            'title' => 'Массив и объект',
            'text' => "```php\nclass Box { public int \$count = 0; }\n\n\$a = new Box();\n\$b = \$a;\n\$b->count = 5;\n\necho \$a->count;\n```\n\nЧто будет выведено?",
            'options' => ['0', '5', 'null', 'Будет ошибка'],
            'answer' => 1,
            'explain' => 'Массивы в PHP при присваивании копируются, а объекты — нет: `$a` и `$b` указывают на один объект. Чтобы получить независимую копию, пишут `clone $a`.',
        ],
        [
            'id' => 'rectangle',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'title' => 'Первый класс',
            'text' => "Допиши класс `Rectangle`:\n\n- свойства ширины и высоты закрыты снаружи;\n- конструктор сохраняет их;\n- метод `area()` возвращает площадь;\n- метод `isSquare()` возвращает `true`, если стороны равны.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

class Rectangle
{
    ___ int $width;
    ___ int $height;

    public function __construct(int $width, int $height)
    {
        ___ = $width;
        ___ = $height;
    }

    public function area(): int
    {
        return ___;
    }

    public function isSquare(): bool
    {
        return ___;
    }
}

$rect = new Rectangle(3, 4);
var_dump($rect->area(), $rect->isSquare());
PHP,
            'tests' => <<<'PHP'
test('area', function () { assert_eq(12, (new Rectangle(3, 4))->area()); });
test('isSquare', function () { assert_eq([false, true], [(new Rectangle(3, 4))->isSquare(), (new Rectangle(5, 5))->isSquare()]); });
test('свойства закрыты снаружи', function () {
    assert_throws(function () { $r = new Rectangle(3, 4); return $r->width; }, 'Error', 'Свойство width должно быть private');
});
test('два прямоугольника независимы', function () {
    $a = new Rectangle(2, 2);
    $b = new Rectangle(10, 1);
    assert_eq([4, 10], [$a->area(), $b->area()]);
});
PHP,
            'hints' => [
                'Модификатор закрытого свойства — `private`.',
                'К свойствам своего объекта обращаются через `$this->`: `$this->width = $width;`',
                'Площадь: `$this->width * $this->height`. Квадрат: `$this->width === $this->height`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class Rectangle
{
    private int $width;
    private int $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function area(): int
    {
        return $this->width * $this->height;
    }

    public function isSquare(): bool
    {
        return $this->width === $this->height;
    }
}
PHP,
        ],
        [
            'id' => 'this-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Забытый $this',
            'text' => "Класс `Playlist` хранит список песен. В нём три ошибки — те же, что ты чинил в JavaScript, только с синтаксисом PHP. Запускай и чини по одной, читая сообщения.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

class Playlist
{
    private string $title;
    private array $songs = [];

    public function __construct(string $title)
    {
        $title = $title;
    }

    public function add(string $song): void
    {
        $songs[] = $song;
    }

    public function size(): int
    {
        return count($songs);
    }

    public function title(): string
    {
        return $this->title;
    }
}

$list = new Playlist('В дорогу');
$list->add('Песня 1');
echo $list->title() . ': ' . $list->size();
PHP,
            'tests' => <<<'PHP'
test('title сохраняется', function () { assert_eq('В дорогу', (new Playlist('В дорогу'))->title()); });
test('новый плейлист пуст', function () { assert_eq(0, (new Playlist('x'))->size()); });
test('add добавляет песню', function () {
    $p = new Playlist('x');
    $p->add('а');
    $p->add('б');
    assert_eq(2, $p->size());
});
test('плейлисты независимы', function () {
    $a = new Playlist('a');
    $b = new Playlist('b');
    $a->add('песня');
    assert_eq(0, $b->size());
});
PHP,
            'hints' => [
                'Внутри метода `$songs` — это новая локальная переменная, а не свойство объекта. Свойство — `$this->songs`.',
                'В конструкторе `$title = $title` присваивает параметр самому себе. Нужно `$this->title = $title`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class Playlist
{
    private string $title;
    private array $songs = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function add(string $song): void
    {
        $this->songs[] = $song;
    }

    public function size(): int
    {
        return count($this->songs);
    }

    public function title(): string
    {
        return $this->title;
    }
}
PHP,
        ],
        [
            'id' => 'promotion',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Короткий конструктор и наследование',
            'text' => "Напиши два класса.\n\n**`Product`** — товар.\n\n- Конструктор в короткой записи принимает название и цену и создаёт свойства `name` (строка) и `price` (целое). Оба публичные и только для чтения.\n- `deliveryCost(): int` возвращает `300`.\n- `describe(): string` возвращает строку вида `Книга: 500 ₽`.\n\n**`DigitalProduct`** расширяет `Product`.\n\n- Конструктор дополнительно принимает размер файла `sizeMb` — тоже публичное свойство только для чтения.\n- Доставка бесплатна: `deliveryCost()` возвращает `0`.\n- `describe()` дополняет описание родителя: `Курс: 1500 ₽ (файл 700 МБ)`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

class Product
{

}

// class DigitalProduct ...
PHP,
            'tests' => <<<'PHP'
test('Product: свойства и методы', function () {
    $p = new Product('Книга', 500);
    assert_eq(['Книга', 500, 300, 'Книга: 500 ₽'], [$p->name, $p->price, $p->deliveryCost(), $p->describe()]);
});
test('Product: свойства только для чтения', function () {
    assert_throws(function () { $p = new Product('Книга', 500); $p->price = 1; }, 'Error', 'Свойство price должно быть readonly');
});
test('DigitalProduct: свои и унаследованные свойства', function () {
    $d = new DigitalProduct('Курс', 1500, 700);
    assert_eq(['Курс', 1500, 700], [$d->name, $d->price, $d->sizeMb]);
});
test('DigitalProduct — это Product', function () { assert_true(new DigitalProduct('x', 1, 1) instanceof Product, 'Нет наследования от Product'); });
test('доставка цифрового товара бесплатна', function () { assert_eq(0, (new DigitalProduct('x', 1, 1))->deliveryCost()); });
test('describe дополняет описание родителя', function () { assert_eq('Курс: 1500 ₽ (файл 700 МБ)', (new DigitalProduct('Курс', 1500, 700))->describe()); });
PHP,
            'hints' => [
                'Короткий конструктор: `public function __construct(public readonly string $name, public readonly int $price) {}`',
                'Наследник: `class DigitalProduct extends Product`. В его конструкторе — `parent::__construct($name, $price);`, а новое свойство объявляется прямо в параметрах: `public readonly int $sizeMb`.',
                'Метод родителя вызывается так: `parent::describe()`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class Product
{
    public function __construct(
        public readonly string $name,
        public readonly int $price,
    ) {}

    public function deliveryCost(): int
    {
        return 300;
    }

    public function describe(): string
    {
        return "{$this->name}: {$this->price} ₽";
    }
}

class DigitalProduct extends Product
{
    public function __construct(
        string $name,
        int $price,
        public readonly int $sizeMb,
    ) {
        parent::__construct($name, $price);
    }

    public function deliveryCost(): int
    {
        return 0;
    }

    public function describe(): string
    {
        return parent::describe() . " (файл {$this->sizeMb} МБ)";
    }
}
PHP,
        ],
        [
            'id' => 'interface',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Интерфейс',
            'text' => "Интерфейс `Discount` и функция `finalPrice` уже написаны. Добавь два класса, которые этот интерфейс выполняют:\n\n- `PercentDiscount` — скидка в процентах: `new PercentDiscount(20)` снижает цену на 20%;\n- `FixedDiscount` — скидка в рублях: `new FixedDiscount(300)` снижает цену на 300, но не ниже нуля.\n\nФункцию `finalPrice` менять не нужно: в этом и смысл интерфейса.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

interface Discount
{
    public function apply(float $price): float;
}

function finalPrice(float $price, Discount $discount): float
{
    return $discount->apply($price);
}

// class PercentDiscount implements Discount ...

// class FixedDiscount implements Discount ...
PHP,
            'tests' => <<<'PHP'
test('оба класса выполняют интерфейс Discount', function () {
    assert_true(new PercentDiscount(10) instanceof Discount, 'PercentDiscount не реализует Discount');
    assert_true(new FixedDiscount(10) instanceof Discount, 'FixedDiscount не реализует Discount');
});
test('скидка 20% с 1000 → 800', function () { assert_eq(800.0, finalPrice(1000.0, new PercentDiscount(20))); });
test('скидка 0% и 100%', function () { assert_eq([500.0, 0.0], [finalPrice(500.0, new PercentDiscount(0)), finalPrice(500.0, new PercentDiscount(100))]); });
test('скидка 300 рублей с 1000 → 700', function () { assert_eq(700.0, finalPrice(1000.0, new FixedDiscount(300))); });
test('фиксированная скидка не уводит цену ниже нуля', function () { assert_eq(0.0, finalPrice(200.0, new FixedDiscount(300))); });
test('finalPrice не принимает посторонние объекты', function () {
    assert_throws(function () { finalPrice(100.0, new stdClass()); }, 'TypeError');
});
PHP,
            'hints' => [
                'Каркас: `class PercentDiscount implements Discount { public function __construct(private float $percent) {} public function apply(float $price): float { … } }`',
                'Процентная: `$price * (1 - $this->percent / 100)`.',
                'Фиксированная: `max(0.0, $price - $this->amount)`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

interface Discount
{
    public function apply(float $price): float;
}

function finalPrice(float $price, Discount $discount): float
{
    return $discount->apply($price);
}

class PercentDiscount implements Discount
{
    public function __construct(private float $percent) {}

    public function apply(float $price): float
    {
        return $price * (1 - $this->percent / 100);
    }
}

class FixedDiscount implements Discount
{
    public function __construct(private float $amount) {}

    public function apply(float $price): float
    {
        return max(0.0, $price - $this->amount);
    }
}
PHP,
        ],
        [
            'id' => 'account-plan',
            'type' => 'plan',
            'title' => 'Заказ со статусами: план',
            'text' => "Задача: класс `Order` — заказ, который проходит через статусы.\n\n- Статусы: новый → оплачен → отправлен. Назад и через шаг переходить нельзя.\n- В заказ можно добавлять товары (название и цена), но только пока он новый.\n- `pay()` переводит новый заказ в оплаченный, `ship()` — оплаченный в отправленный. Вызов не вовремя — ошибка.\n- Пустой заказ оплатить нельзя.\n- `total()` возвращает сумму.\n\nЗапиши: как представить статус, какие данные хранит объект и что по шагам проверяет каждый метод.",
            'reference' => <<<'MD'
**Статус** — перечисление `OrderStatus` с тремя вариантами. Строка тоже сработала бы, но в строку можно записать опечатку, а в перечисление — нет.

**Данные объекта** (все закрытые): массив товаров и текущий статус, изначально «новый». Статус наружу отдаётся методом для чтения, менять его напрямую нельзя — иначе все проверки переходов можно обойти.

**Методы:**

- `addItem(название, цена)`: если статус не «новый» — бросить ошибку. Иначе добавить товар в массив.
- `pay()`: если статус не «новый» — ошибка. Если товаров нет — ошибка. Иначе статус становится «оплачен».
- `ship()`: если статус не «оплачен» — ошибка. Иначе статус становится «отправлен».
- `total()`: сумма цен товаров.

У каждого метода, меняющего состояние, одна и та же форма: **сначала все проверки, потом изменение**. Это ранний выход для объектов: заказ никогда не оказывается в состоянии, которого не должно быть.

Тип ошибки — `LogicException`: метод вызван не вовремя, это ошибка в логике вызывающего кода.
MD,
        ],
        [
            'id' => 'order',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Заказ со статусами: код',
            'text' => "Напиши перечисление `OrderStatus` и класс `Order` по своему плану.\n\n- `OrderStatus` — строковое перечисление с вариантами `New = 'new'`, `Paid = 'paid'`, `Shipped = 'shipped'`.\n- `Order`: методы `addItem(string \$name, int \$price): void`, `pay(): void`, `ship(): void`, `total(): int`, `status(): OrderStatus`.\n- Недопустимое действие бросает `LogicException`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
test('перечисление OrderStatus', function () {
    assert_eq(['new', 'paid', 'shipped'], [OrderStatus::New->value, OrderStatus::Paid->value, OrderStatus::Shipped->value]);
});
test('новый заказ: статус New, сумма 0', function () {
    $o = new Order();
    assert_true($o->status() === OrderStatus::New, 'Статус нового заказа должен быть OrderStatus::New');
    assert_eq(0, $o->total());
});
test('addItem увеличивает сумму', function () {
    $o = new Order();
    $o->addItem('Эфиопия', 890);
    $o->addItem('Фильтры', 250);
    assert_eq(1140, $o->total());
});
test('pay переводит в Paid, ship — в Shipped', function () {
    $o = new Order();
    $o->addItem('Эфиопия', 890);
    $o->pay();
    assert_true($o->status() === OrderStatus::Paid, 'После pay() статус должен быть Paid');
    $o->ship();
    assert_true($o->status() === OrderStatus::Shipped, 'После ship() статус должен быть Shipped');
});
test('пустой заказ оплатить нельзя', function () {
    assert_throws(function () { (new Order())->pay(); }, 'LogicException');
});
test('нельзя отправить неоплаченный', function () {
    assert_throws(function () { $o = new Order(); $o->addItem('x', 1); $o->ship(); }, 'LogicException');
});
test('нельзя оплатить дважды', function () {
    assert_throws(function () { $o = new Order(); $o->addItem('x', 1); $o->pay(); $o->pay(); }, 'LogicException');
});
test('нельзя добавлять товары после оплаты', function () {
    $o = new Order();
    $o->addItem('x', 100);
    $o->pay();
    assert_throws(function () use ($o) { $o->addItem('y', 50); }, 'LogicException');
    assert_eq(100, $o->total());
});
test('неудачное действие не меняет статус', function () {
    $o = new Order();
    try { $o->pay(); } catch (LogicException $e) { }
    assert_true($o->status() === OrderStatus::New, 'Статус изменился после неудачной оплаты');
});
test('статус нельзя изменить снаружи', function () {
    assert_throws(function () { $o = new Order(); $o->status = OrderStatus::Shipped; }, 'Error', 'Свойство со статусом должно быть private');
});
PHP,
            'hints' => [
                'Перечисление: `enum OrderStatus: string { case New = \'new\'; case Paid = \'paid\'; case Shipped = \'shipped\'; }`',
                'Свойства: `private array $items = [];` и `private OrderStatus $status = OrderStatus::New;`',
                'Проверка в начале метода: `if ($this->status !== OrderStatus::New) { throw new LogicException(\'Заказ уже оплачен\'); }`',
                'Сумма: `array_sum(array_column($this->items, \'price\'))`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

enum OrderStatus: string
{
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';
}

class Order
{
    private array $items = [];
    private OrderStatus $status = OrderStatus::New;

    public function addItem(string $name, int $price): void
    {
        if ($this->status !== OrderStatus::New) {
            throw new LogicException('Состав заказа уже нельзя менять');
        }

        $this->items[] = ['name' => $name, 'price' => $price];
    }

    public function pay(): void
    {
        if ($this->status !== OrderStatus::New) {
            throw new LogicException('Заказ уже оплачен');
        }
        if (count($this->items) === 0) {
            throw new LogicException('Пустой заказ оплатить нельзя');
        }

        $this->status = OrderStatus::Paid;
    }

    public function ship(): void
    {
        if ($this->status !== OrderStatus::Paid) {
            throw new LogicException('Отправить можно только оплаченный заказ');
        }

        $this->status = OrderStatus::Shipped;
    }

    public function total(): int
    {
        return array_sum(array_column($this->items, 'price'));
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }
}
PHP,
        ],
    ],
    'cards' => [
        ['Как в PHP обратиться к свойству объекта и к статическому члену класса?', 'Свойство и метод объекта — через стрелку: `$user->name`, `$this->name`. Статический член и константа — через двойное двоеточие: `Money::format()`, `self::CURRENCY`.'],
        ['Что означают `public`, `protected`, `private`?', '`public` — видно всем, `protected` — классу и наследникам, `private` — только самому классу. Свойства закрывают, наружу отдают методы.'],
        ['Что такое короткий конструктор?', 'Модификатор перед параметром конструктора сразу создаёт и заполняет свойство: `public function __construct(private string $name) {}`. PHP 8.'],
        ['Копируются ли объекты при присваивании?', 'Нет: обе переменные указывают на один объект. Копию делает `clone`. Массивы, наоборот, копируются.'],
        ['Зачем нужен интерфейс?', 'Это обещание набора методов. Функция, принимающая интерфейс, работает с любым классом, который его выполняет, — новые варианты добавляются без её изменения.'],
        ['Когда использовать `enum`?', 'Когда у значения конечный набор вариантов: статус, роль, тип. В отличие от строки, в перечисление нельзя записать опечатку.'],
    ],
];
