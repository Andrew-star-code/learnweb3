<?php
return [
    'tasks' => [
        [
            'id' => 'use-quiz',
            'type' => 'quiz',
            'title' => 'Стрелочная функция на старом сервере',
            'text' => "Строку нужно перенести на PHP 7.0:\n\n```php\n\$cheap = array_filter(\$menu, fn(\$item) => \$item['price'] <= \$limit);\n```\n\nКакая замена даст тот же результат?",
            'options' => [
                'function ($item) { return $item[\'price\'] <= $limit; }',
                'function ($item) use ($limit) { return $item[\'price\'] <= $limit; }',
                'function ($item, $limit) { return $item[\'price\'] <= $limit; }',
                'function ($item) { global $limit; return $item[\'price\'] <= $limit; }',
            ],
            'answer' => 1,
            'explain' => 'Обычная анонимная функция не видит переменных снаружи — их перечисляют в `use`. Первый вариант сравнит цену с `null` и молча отберёт не то. Третий ждёт второй аргумент, которого `array_filter` не передаст. Четвёртый сработает, только если `$limit` — глобальная переменная, а не локальная переменная функции.',
        ],
        [
            'id' => 'match-quiz',
            'type' => 'quiz',
            'title' => 'Осовременили switch',
            'text' => "Было:\n\n```php\nfunction sizeName(\$code)\n{\n    switch (\$code) {\n        case 1: return 'маленький';\n        case 2: return 'большой';\n        default: return 'неизвестный';\n    }\n}\n```\n\nСтало:\n\n```php\nfunction sizeName(\$code)\n{\n    return match (\$code) {\n        1 => 'маленький',\n        2 => 'большой',\n        default => 'неизвестный',\n    };\n}\n```\n\nКод размера приходит из формы: `sizeName(\$_POST['size'])`. Что изменилось для пользователя, выбравшего размер 2?",
            'options' => [
                'Ничего: он по-прежнему получает «большой»',
                'Раньше получал «большой», теперь — «неизвестный»',
                'Раньше получал «неизвестный», теперь — «большой»',
                'Теперь функция бросает ошибку',
            ],
            'answer' => 1,
            'explain' => 'Из формы приходит строка `\'2\'`. `switch` сравнивает нестрого, и `\'2\' == 2` — истина. `match` сравнивает строго: строка `\'2\'` не равна числу `2`, срабатывает `default`. Перед такой заменой значение нужно привести: `match ((int) $code)`.',
        ],
        [
            'id' => 'downgrade-fill',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'php' => 'both',
            'title' => 'Три замены',
            'text' => "Три функции уже наполовину перенесены на PHP 7.0. В комментариях — как они выглядели в PHP 8. Заполни пропуски.\n\nЗадание проверяется на двух версиях: код для старого сервера обязан работать и на новом.",
            'starter' => <<<'PHP'
<?php

// было: fn($price) => $price > $limit
function expensive(array $prices, int $limit): array
{
    return array_values(array_filter($prices, function ($price) ___ {
        return $price > $limit;
    }));
}

// было: [$first, $last] = explode(' ', $full);
function splitName(string $full): array
{
    ___($first, $last) = explode(' ', $full);

    return ['first' => $first, 'last' => $last];
}

// было: return str_contains($title, 'без кофеина');
function isDecaf(string $title): bool
{
    return strpos($title, 'без кофеина') ___ false;
}

print_r(expensive([150, 250, 320], 200));
print_r(splitName('Анна Смирнова'));
var_dump(isDecaf('Латте без кофеина'));
PHP,
            'tests' => <<<'PHP'
test('expensive: цены выше порога', function () { assert_eq([250, 320], expensive([150, 250, 320], 200)); });
test('expensive: порог другой — результат другой', function () { assert_eq([320], expensive([150, 250, 320], 300)); });
test('splitName', function () { assert_eq(['first' => 'Анна', 'last' => 'Смирнова'], splitName('Анна Смирнова')); });
test('isDecaf: слова в середине', function () { assert_eq(true, isDecaf('Латте без кофеина')); });
test('isDecaf: слова в самом начале', function () { assert_eq(true, isDecaf('без кофеина, большой')); });
test('isDecaf: слов нет', function () { assert_eq(false, isDecaf('Капучино')); });
PHP,
            'hints' => [
                'Анонимной функции нужна переменная снаружи: `use ($limit)`.',
                'Распаковка массива по переменным в PHP 7.0 — `list($first, $last) = …`.',
                '`strpos` возвращает позицию или `false`. Позиция `0` — тоже «нашлось», поэтому сравнение строгое: `!== false`.',
            ],
            'solution' => <<<'PHP'
<?php

function expensive(array $prices, int $limit): array
{
    return array_values(array_filter($prices, function ($price) use ($limit) {
        return $price > $limit;
    }));
}

function splitName(string $full): array
{
    list($first, $last) = explode(' ', $full);

    return ['first' => $first, 'last' => $last];
}

function isDecaf(string $title): bool
{
    return strpos($title, 'без кофеина') !== false;
}
PHP,
        ],
        [
            'id' => 'downgrade',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'php' => 'both',
            'title' => 'Корзина для старого сервера',
            'text' => "Этот код написан для PHP 8 и там работает. Его нужно поставить на сервер с PHP 7.0.\n\nЗапусти на обеих версиях: правая колонка покажет первую ошибку разбора. Исправь её и запусти снова — PHP называет ошибки по одной. Когда файл разберётся, останутся ошибки времени выполнения.\n\nПоведение менять нельзя: тесты одни и те же для обеих версий.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

class CartItem
{
    public function __construct(
        private string $title,
        private int $price,
        private int $qty = 1,
        private ?string $note = null,
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function total(): int
    {
        return $this->price * $this->qty;
    }
}

function sizeLabel(string $code): string
{
    return match ($code) {
        's' => 'Маленький',
        'm' => 'Средний',
        'l', 'xl' => 'Большой',
        default => throw new InvalidArgumentException("Неизвестный размер: $code"),
    };
}

function cartTotal(array $items): int
{
    return array_sum(array_map(fn(CartItem $item) => $item->total(), $items));
}

function hasSyrup(array $items): bool
{
    foreach ($items as $item) {
        if (str_contains($item->getTitle(), 'сироп')) {
            return true;
        }
    }

    return false;
}

function firstTitle(?CartItem $item): ?string
{
    return $item?->getTitle();
}

$cart = [new CartItem('Латте', 250, 2), new CartItem('Сироп: карамель', 50)];
echo cartTotal($cart), "\n";
echo sizeLabel('l'), "\n";
var_dump(hasSyrup($cart));
PHP,
            'tests' => <<<'PHP'
test('CartItem: сумма строки', function () { assert_eq(500, (new CartItem('Латте', 250, 2))->total()); });
test('CartItem: количество по умолчанию — 1', function () { assert_eq(250, (new CartItem('Латте', 250))->total()); });
test('CartItem: название и примечание', function () {
    $item = new CartItem('Латте', 250, 1, 'без сахара');
    assert_eq(['Латте', 'без сахара'], [$item->getTitle(), $item->getNote()]);
});
test('CartItem: примечания нет → null', function () { assert_eq(null, (new CartItem('Латте', 250))->getNote()); });
test('CartItem: типы параметров сохранены', function () {
    assert_throws(function () { new CartItem('Латте', '250'); }, 'TypeError', 'Цена строкой должна давать TypeError — не убирай типы у параметров');
});
test('sizeLabel: известные размеры', function () {
    assert_eq(['Маленький', 'Средний', 'Большой', 'Большой'], [sizeLabel('s'), sizeLabel('m'), sizeLabel('l'), sizeLabel('xl')]);
});
test('sizeLabel: неизвестный размер → исключение', function () {
    $e = assert_throws(function () { sizeLabel('xxl'); }, 'InvalidArgumentException');
    assert_eq('Неизвестный размер: xxl', $e->getMessage());
});
test('cartTotal', function () { assert_eq(550, cartTotal([new CartItem('Латте', 250, 2), new CartItem('Сироп: карамель', 50)])); });
test('cartTotal: пустая корзина → 0', function () { assert_eq(0, cartTotal([])); });
test('hasSyrup: есть', function () { assert_eq(true, hasSyrup([new CartItem('Латте', 250), new CartItem('Ванильный сироп', 50)])); });
test('hasSyrup: нет', function () { assert_eq(false, hasSyrup([new CartItem('Латте', 250)])); });
test('firstTitle: позиция есть', function () { assert_eq('Латте', firstTitle(new CartItem('Латте', 250))); });
test('firstTitle: позиции нет → null', function () { assert_eq(null, firstTitle(null)); });
PHP,
            'hints' => [
                'Конструктор: объяви четыре свойства `private $title;` …, убери `private` из параметров и запятую после последнего, допиши присваивания `$this->title = $title;`. Вместо `?string $note = null` — `string $note = null`.',
                'Типы результата `?string` убираются совсем: `public function getNote()`, `function firstTitle(CartItem $item = null)`. Остальные (`: string`, `: int`, `: bool`) в 7.0 работают.',
                '`match` → `switch` с `return` в каждой ветке; `\'l\'` и `\'xl\'` — два `case` подряд. Исключение бросается в `default` обычной строкой `throw new …;`.',
                'Оставшееся: `fn(CartItem $item) => …` → `function (CartItem $item) { return …; }`; `str_contains(…)` → `strpos(…) !== false`; `$item?->getTitle()` → `$item !== null ? $item->getTitle() : null`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class CartItem
{
    /** @var string */
    private $title;

    /** @var int */
    private $price;

    /** @var int */
    private $qty;

    /** @var string|null */
    private $note;

    public function __construct(string $title, int $price, int $qty = 1, string $note = null)
    {
        $this->title = $title;
        $this->price = $price;
        $this->qty = $qty;
        $this->note = $note;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getNote()
    {
        return $this->note;
    }

    public function total(): int
    {
        return $this->price * $this->qty;
    }
}

function sizeLabel(string $code): string
{
    switch ($code) {
        case 's':
            return 'Маленький';
        case 'm':
            return 'Средний';
        case 'l':
        case 'xl':
            return 'Большой';
        default:
            throw new InvalidArgumentException("Неизвестный размер: $code");
    }
}

function cartTotal(array $items): int
{
    return array_sum(array_map(function (CartItem $item) {
        return $item->total();
    }, $items));
}

function hasSyrup(array $items): bool
{
    foreach ($items as $item) {
        if (strpos($item->getTitle(), 'сироп') !== false) {
            return true;
        }
    }

    return false;
}

/**
 * @return string|null
 */
function firstTitle(CartItem $item = null)
{
    return $item !== null ? $item->getTitle() : null;
}
PHP,
        ],
        [
            'id' => 'modernize',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Осовременить, не сломав',
            'text' => "Обратное направление. Проект переехал на PHP 8, и этот файл можно привести к современному виду. Код рабочий: первые тесты проходят уже сейчас и должны проходить после каждого твоего шага.\n\nОстальные тесты описывают цель:\n\n- в начале файла — `declare(strict_types=1)`;\n- свойства `Product` объявлены через продвижение в конструкторе, с типами `string`, `int`, `array`;\n- у методов `Product` есть типы результата: `string`, `int`, `bool`;\n- `priceLabel` принимает `?Product` и возвращает `string`;\n- `titles` принимает и возвращает `array`.\n\nВсё прочее — `[]` вместо `array()`, стрелочная функция, `match` — по желанию. Двигайся маленькими шагами и нажимай «Проверить» после каждого.",
            'starter' => <<<'PHP'
<?php

class Product
{
    private $title;
    private $price;
    private $tags;

    public function __construct($title, $price, $tags = array())
    {
        $this->title = $title;
        $this->price = $price;
        $this->tags = $tags;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function hasTag($tag)
    {
        return in_array($tag, $this->tags);
    }
}

function priceLabel($product)
{
    if ($product === null) {
        return 'нет в меню';
    }
    if ($product->getPrice() == 0) {
        return 'бесплатно';
    }

    return $product->getPrice() . ' ₽';
}

function titles($products)
{
    return array_map(function ($product) {
        return $product->getTitle();
    }, $products);
}
PHP,
            'tests' => <<<'PHP'
function typeOf($reflection) {
    $type = $reflection instanceof ReflectionParameter ? $reflection->getType() : $reflection->getReturnType();
    return $type === null ? 'нет типа' : (string) $type;
}
test('поведение: название, цена, метки', function () {
    $latte = new Product('Латте', 250, ['молоко', 'хит']);
    assert_eq(['Латте', 250, true, false], [$latte->getTitle(), $latte->getPrice(), $latte->hasTag('хит'), $latte->hasTag('веган')]);
});
test('поведение: меток по умолчанию нет', function () { assert_eq(false, (new Product('Вода', 0))->hasTag('хит')); });
test('поведение: priceLabel', function () {
    assert_eq(['250 ₽', 'бесплатно', 'нет в меню'], [priceLabel(new Product('Латте', 250)), priceLabel(new Product('Вода', 0)), priceLabel(null)]);
});
test('поведение: titles', function () {
    assert_eq(['Латте', 'Вода'], titles([new Product('Латте', 250), new Product('Вода', 0)]));
});
test('цель: declare(strict_types=1) в начале файла', function () {
    $source = file_get_contents((new ReflectionClass('Product'))->getFileName());
    assert_true(preg_match('/^<\?php\s+declare\(\s*strict_types\s*=\s*1\s*\)\s*;/', $source) === 1, 'Сразу после <?php должна стоять строка declare(strict_types=1);');
});
test('цель: параметры конструктора с типами', function () {
    $types = array_map('typeOf', (new ReflectionMethod('Product', '__construct'))->getParameters());
    assert_eq(['string', 'int', 'array'], $types);
});
test('цель: свойства объявлены продвижением в конструкторе', function () {
    foreach (['title', 'price', 'tags'] as $name) {
        assert_true((new ReflectionProperty('Product', $name))->isPromoted(), "Свойство $name объявлено отдельно, а не в параметрах конструктора");
    }
});
test('цель: свойства остались закрытыми', function () {
    foreach (['title', 'price', 'tags'] as $name) {
        assert_true((new ReflectionProperty('Product', $name))->isPrivate(), "Свойство $name должно быть private");
    }
});
test('цель: типы результата у методов Product', function () {
    assert_eq(['string', 'int', 'bool'], [
        typeOf(new ReflectionMethod('Product', 'getTitle')),
        typeOf(new ReflectionMethod('Product', 'getPrice')),
        typeOf(new ReflectionMethod('Product', 'hasTag')),
    ]);
});
test('цель: priceLabel(?Product): string', function () {
    $fn = new ReflectionFunction('priceLabel');
    assert_eq(['?Product', 'string'], [typeOf($fn->getParameters()[0]), typeOf($fn)]);
});
test('цель: titles(array): array', function () {
    $fn = new ReflectionFunction('titles');
    assert_eq(['array', 'array'], [typeOf($fn->getParameters()[0]), typeOf($fn)]);
});
test('типы теперь проверяются: цена строкой → TypeError', function () {
    assert_throws(function () { new Product('Латте', '250'); }, 'TypeError');
});
PHP,
            'hints' => [
                'Начни с самого безопасного: `declare(strict_types=1);` и типы результата — `getTitle(): string`, `getPrice(): int`, `hasTag($tag): bool`, `priceLabel(…): string`, `titles(…): array`.',
                'Затем параметры: `priceLabel(?Product $product)`, `titles(array $products)`.',
                'Конструктор целиком: `public function __construct(private string $title, private int $price, private array $tags = []) {}` — три объявления свойств над ним и три присваивания внутри удаляются.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class Product
{
    public function __construct(
        private string $title,
        private int $price,
        private array $tags = [],
    ) {}

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags, true);
    }
}

function priceLabel(?Product $product): string
{
    return match (true) {
        $product === null => 'нет в меню',
        $product->getPrice() === 0 => 'бесплатно',
        default => $product->getPrice() . ' ₽',
    };
}

function titles(array $products): array
{
    return array_map(fn(Product $product) => $product->getTitle(), $products);
}
PHP,
        ],
        [
            'id' => 'status-plan',
            'type' => 'plan',
            'title' => 'Перечисление без enum: план',
            'text' => "В новом проекте статусы заказа описаны перечислением:\n\n```php\nenum OrderStatus: string\n{\n    case New = 'new';\n    case Paid = 'paid';\n    case Done = 'done';\n    case Cancelled = 'cancelled';\n\n    public function label(): string\n    {\n        return match (\$this) {\n            self::New => 'Новый',\n            self::Paid => 'Оплачен',\n            self::Done => 'Выполнен',\n            self::Cancelled => 'Отменён',\n        };\n    }\n\n    public function canMoveTo(OrderStatus \$next): bool\n    {\n        return match (\$this) {\n            self::New => in_array(\$next, [self::Paid, self::Cancelled], true),\n            self::Paid => in_array(\$next, [self::Done, self::Cancelled], true),\n            self::Done, self::Cancelled => false,\n        };\n    }\n}\n```\n\nТу же логику нужно завести в проекте на PHP 7.0, где `enum` нет. Запиши план: чем заменить варианты, где хранить подписи и разрешённые переходы, и что перечисление делало само, а теперь придётся проверять руками.",
            'reference' => <<<'MD'
1. Завести класс `OrderStatus` с константами вместо вариантов: `const NEW = 'new';` и так далее. Статус в остальном коде теперь — обычная строка.
2. Написать метод `all()`, который возвращает список всех статусов.
3. Написать метод `isValid($status)`: есть ли строка в списке. — `in_array(…, true)`
4. Подписи хранить в массиве «статус → подпись». Метод `label($status)` сначала проверяет статус, потом берёт подпись из массива.
5. Переходы хранить в массиве «статус → список статусов, куда можно перейти». Для конечных статусов — пустой список.
6. Метод `canMove($from, $to)` проверяет оба статуса и отвечает, есть ли `$to` в списке для `$from`.
7. Для неизвестного статуса бросать `InvalidArgumentException`.

На что обратить внимание:

- Методы становятся **статическими** и получают статус аргументом: объекта-варианта больше нет, есть только строка.
- Главное, что теряется, — гарантия. В параметр типа `OrderStatus` нельзя было передать ничего, кроме четырёх вариантов. В строку можно записать что угодно, поэтому шаги 3 и 7 — не формальность: это ручная замена той проверки, которую перечисление делало само.
- `match` без `default` бросал ошибку для необработанного варианта. Массив по неизвестному ключу вернёт `null` с замечанием — ещё одна причина проверять статус заранее.
- Два массива вместо двух `match` — это та самая замена «справочником» из урока. Добавить пятый статус — значит добавить константу и по строке в каждый массив.
- Строгое сравнение в `in_array` обязательно: помни про `0 == 'new'` в PHP 7.
MD,
        ],
        [
            'id' => 'status',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'php' => 'both',
            'title' => 'Перечисление без enum: код',
            'text' => "Напиши класс `OrderStatus` для PHP 7.0:\n\n- константы `NEW`, `PAID`, `DONE`, `CANCELLED` со значениями `'new'`, `'paid'`, `'done'`, `'cancelled'`;\n- `OrderStatus::all()` — массив всех значений в этом порядке;\n- `OrderStatus::isValid(\$status)` — `true` только для этих четырёх строк;\n- `OrderStatus::label(\$status)` — `Новый`, `Оплачен`, `Выполнен`, `Отменён`;\n- `OrderStatus::canMove(\$from, \$to)` — разрешён ли переход: из нового — в оплаченный или отменённый, из оплаченного — в выполненный или отменённый, из выполненного и отменённого — никуда.\n\n`label` и `canMove` для неизвестного статуса бросают `InvalidArgumentException` с текстом `Неизвестный статус: значение`.\n\nЗадание проверяется на двух версиях.",
            'starter' => <<<'PHP'
<?php

PHP,
            'tests' => <<<'PHP'
test('константы', function () {
    assert_eq(['new', 'paid', 'done', 'cancelled'], [OrderStatus::NEW, OrderStatus::PAID, OrderStatus::DONE, OrderStatus::CANCELLED]);
});
test('all()', function () { assert_eq(['new', 'paid', 'done', 'cancelled'], OrderStatus::all()); });
test('isValid: известные статусы', function () { assert_eq([true, true], [OrderStatus::isValid('new'), OrderStatus::isValid('cancelled')]); });
test('isValid: неизвестная строка', function () { assert_eq(false, OrderStatus::isValid('archived')); });
test('isValid: число 0 — не статус', function () { assert_eq(false, OrderStatus::isValid(0)); });
test('label', function () {
    assert_eq(['Новый', 'Оплачен', 'Выполнен', 'Отменён'], array_map(function ($status) { return OrderStatus::label($status); }, OrderStatus::all()));
});
test('label: неизвестный статус → исключение', function () {
    $e = assert_throws(function () { OrderStatus::label('archived'); }, 'InvalidArgumentException');
    assert_eq('Неизвестный статус: archived', $e->getMessage());
});
test('canMove: из нового', function () {
    assert_eq([true, true, false], [OrderStatus::canMove('new', 'paid'), OrderStatus::canMove('new', 'cancelled'), OrderStatus::canMove('new', 'done')]);
});
test('canMove: из оплаченного', function () {
    assert_eq([true, true, false], [OrderStatus::canMove('paid', 'done'), OrderStatus::canMove('paid', 'cancelled'), OrderStatus::canMove('paid', 'new')]);
});
test('canMove: из конечных статусов — никуда', function () {
    foreach (['done', 'cancelled'] as $from) {
        foreach (OrderStatus::all() as $to) {
            assert_eq(false, OrderStatus::canMove($from, $to), "$from → $to");
        }
    }
});
test('canMove: в самого себя нельзя', function () { assert_eq(false, OrderStatus::canMove('new', 'new')); });
test('canMove: неизвестный статус с любой стороны → исключение', function () {
    assert_eq('Неизвестный статус: archived', assert_throws(function () { OrderStatus::canMove('archived', 'paid'); }, 'InvalidArgumentException')->getMessage());
    assert_eq('Неизвестный статус: lost', assert_throws(function () { OrderStatus::canMove('new', 'lost'); }, 'InvalidArgumentException')->getMessage());
});
PHP,
            'hints' => [
                'Каркас: `class OrderStatus { const NEW = \'new\'; … public static function all() { return [self::NEW, self::PAID, self::DONE, self::CANCELLED]; } }`',
                'Проверку с исключением удобно вынести в закрытый статический метод `check($status)` и вызывать в начале `label` и `canMove`.',
                'Переходы: `$moves = [self::NEW => [self::PAID, self::CANCELLED], self::PAID => [self::DONE, self::CANCELLED], self::DONE => [], self::CANCELLED => []];` и `return in_array($to, $moves[$from], true);`',
            ],
            'solution' => <<<'PHP'
<?php

class OrderStatus
{
    const NEW = 'new';
    const PAID = 'paid';
    const DONE = 'done';
    const CANCELLED = 'cancelled';

    public static function all()
    {
        return [self::NEW, self::PAID, self::DONE, self::CANCELLED];
    }

    public static function isValid($status)
    {
        return in_array($status, self::all(), true);
    }

    public static function label($status)
    {
        self::check($status);

        $labels = [
            self::NEW => 'Новый',
            self::PAID => 'Оплачен',
            self::DONE => 'Выполнен',
            self::CANCELLED => 'Отменён',
        ];

        return $labels[$status];
    }

    public static function canMove($from, $to)
    {
        self::check($from);
        self::check($to);

        $moves = [
            self::NEW => [self::PAID, self::CANCELLED],
            self::PAID => [self::DONE, self::CANCELLED],
            self::DONE => [],
            self::CANCELLED => [],
        ];

        return in_array($to, $moves[$from], true);
    }

    private static function check($status)
    {
        if (!self::isValid($status)) {
            throw new InvalidArgumentException('Неизвестный статус: ' . $status);
        }
    }
}
PHP,
        ],
    ],
    'cards' => [
        ['Как записать `fn($x) => $x * $k` для PHP 7.0 и что легко забыть?', '`function ($x) use ($k) { return $x * $k; }`. Забывают `use`: без него переменная внутри не видна, и вместо ошибки получаются нули.'],
        ['Назови три отличия `match` от `switch`.', '`match` возвращает значение, сравнивает строго и бросает ошибку, если ни одна ветка не подошла. `switch` сравнивает нестрого и молча идёт дальше.'],
        ['Как в PHP 7.0 объявить параметр «число или null»?', 'Значением по умолчанию: `int $x = null`. Записи `?int` там нет.'],
        ['Что делать с типом результата `?int` при переносе на 7.0?', 'Убрать из сигнатуры и записать в докблок: `@return int|null`.'],
        ['В каком порядке осовременивать старый код?', 'Тесты → внешние замены без смены поведения → типы результата → типы параметров → `switch` на `match` → `strict_types` в самом конце.'],
        ['Чем опасно добавление типа к параметру старой функции?', 'Тип — это проверка, которой раньше не было. Значения, которые функция раньше молча принимала (пустая строка из формы, `null`), начнут давать `TypeError`.'],
    ],
];
