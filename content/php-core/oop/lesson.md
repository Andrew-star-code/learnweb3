# ООП

Классы в PHP ты уже умеешь читать: идея та же, что в JavaScript. Отличается запись, и добавляются инструменты, которых в JS нет, — модификаторы видимости, интерфейсы и перечисления.

## Класс

```php
class User
{
    private string $name;
    private int $age;

    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function greet(): string
    {
        return "Привет, я {$this->name}";
    }
}

$anna = new User('Аня', 30);
echo $anna->greet();
```

| JavaScript | PHP |
|---|---|
| `this.name` | `$this->name` |
| `anna.greet()` | `$anna->greet()` |
| `constructor(...)` | `__construct(...)` |
| `#name` | `private string $name` |
| `static create()` → `User.create()` | `User::create()` |

К свойствам и методам обращаются через **стрелку `->`**, к статическим — через **двойное двоеточие `::`**. Свойства объявляются в теле класса с типом.

## Видимость

| Модификатор | Кто видит |
|---|---|
| `public` | Все |
| `protected` | Сам класс и наследники |
| `private` | Только сам класс |

Правило то же, что с приватными полями в JS: **свойства закрыты, наружу — методы**. Если поле открыто, его может поменять кто угодно, минуя все проверки.

## Короткий конструктор

Объявить свойство, принять параметр и присвоить — три одинаковые строки на каждое поле. В PHP 8 это записывается разом:

```php
class User
{
    public function __construct(
        private string $name,
        private int $age,
    ) {}
}
```

Модификатор перед параметром конструктора создаёт свойство и заполняет его. Так пишут почти все современные классы.

`readonly` запрещает менять свойство после создания объекта (PHP 8.1):

```php
public function __construct(
    public readonly string $name,
) {}
```

Свойство можно сделать публичным и не бояться, что его перезапишут.

## Объекты передаются по ссылке

В отличие от массивов, объекты при присваивании **не копируются**:

```php
$a = new Cart();
$b = $a;
$b->add('чай');
$a->count();        // 1 — это один и тот же объект
```

Копию делает `clone $a`.

## Наследование

```php
class Admin extends User
{
    public function __construct(string $name, int $age, private int $level)
    {
        parent::__construct($name, $age);
    }

    public function greet(): string
    {
        return parent::greet() . ', администратор';
    }
}
```

Вместо `super` — `parent::`. В остальном всё как в JS.

## Интерфейсы

Интерфейс — это обещание: «у класса есть такие методы». Кода в нём нет, только заголовки.

```php
interface Discount
{
    public function apply(float $price): float;
}

class PercentDiscount implements Discount
{
    public function __construct(private float $percent) {}

    public function apply(float $price): float
    {
        return $price * (1 - $this->percent / 100);
    }
}

function finalPrice(float $price, Discount $discount): float
{
    return $discount->apply($price);
}
```

Функция `finalPrice` принимает **любой** объект, выполняющий обещание `Discount`. Ей не важно, процентная это скидка или фиксированная: она знает, что метод `apply` есть. Новый вид скидки добавляется новым классом, а `finalPrice` не меняется.

Это главный приём ООП для расширяемого кода, и в JavaScript прямого аналога у него нет.

## Статические члены и константы

```php
class Money
{
    public const CURRENCY = '₽';

    public static function format(float $amount): string
    {
        return number_format($amount, 2, '.', ' ') . ' ' . self::CURRENCY;
    }
}

Money::format(1500);    // '1 500.00 ₽'
Money::CURRENCY;        // '₽'
```

Внутри класса к своим статическим членам обращаются через `self::`.

## Перечисления

Когда у значения конечный набор вариантов — статус заказа, роль пользователя — в PHP 8.1 есть `enum`:

```php
enum Status: string
{
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';

    public function label(): string
    {
        return match ($this) {
            Status::New => 'Новый',
            Status::Paid => 'Оплачен',
            Status::Shipped => 'Отправлен',
        };
    }
}

$status = Status::from('paid');     // из строки, например из базы
$status->label();                   // 'Оплачен'
$status->value;                     // 'paid'
```

Перечисление не даёт записать в статус опечатку `'payed'`: такого варианта просто нет. `match` — строгий аналог `switch`, который возвращает значение и бросает ошибку, если вариант не предусмотрен.

> **В PHP 7.** Короткий конструктор, `match`, `readonly`, `enum` и запятая после последнего параметра — только PHP 8. Типы свойств — с 7.4. В PHP 7.0–7.3 класс пишется «длинно»: свойства без типов, присваивания в конструкторе, вместо `enum` — набор констант класса (`const STATUS_NEW = 'new';`), вместо `match` — `switch`. Видимость, наследование и интерфейсы работают одинаково во всех версиях.

## Глубже

- [Классы и объекты](https://www.php.net/manual/ru/language.oop5.php) — руководство PHP
- [Интерфейсы объектов](https://www.php.net/manual/ru/language.oop5.interfaces.php)
- [Перечисления](https://www.php.net/manual/ru/language.enumerations.php)
