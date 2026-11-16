<?php
return [
    'tasks' => [
        [
            'id' => 'full-name-quiz',
            'type' => 'quiz',
            'title' => 'Полное имя',
            'text' => "```php\n<?php\n// файл src/Services/Mailer.php\n\nnamespace App\\Services;\n\nclass Mailer\n{\n}\n```\n\nКак создать объект этого класса из файла, в котором нет ни `namespace`, ни `use`?",
            'options' => [
                'new Mailer()',
                'new App\\Services\\Mailer()',
                'new Services\\Mailer()',
                "new Mailer('App\\Services')",
            ],
            'answer' => 1,
            'explain' => 'Полное имя класса — неймспейс плюс короткое имя: `App\Services\Mailer`. Короткое `Mailer` сработало бы только после `use App\Services\Mailer;` или внутри того же неймспейса.',
        ],
        [
            'id' => 'builtin-quiz',
            'type' => 'quiz',
            'title' => 'Класс не найден',
            'text' => "```php\n<?php\n\nnamespace App\\Controllers;\n\nclass ReportController\n{\n    public function today(): string\n    {\n        return (new DateTime())->format('d.m.Y');\n    }\n}\n```\n\nВызов `today()` падает с ошибкой `Class \"App\\Controllers\\DateTime\" not found`. Почему?",
            'options' => [
                'Класса DateTime в PHP не существует',
                'Имя без ведущей косой черты ищется в текущем неймспейсе, а DateTime живёт в корневом',
                'Нужно подключить файл через require',
                'Метод format вызван с неверным аргументом',
            ],
            'answer' => 1,
            'explain' => 'Внутри неймспейса короткое имя класса считается относительным: PHP ищет `App\Controllers\DateTime`. Встроенные классы лежат в корне, поэтому нужно написать `new \DateTime()` или добавить в начало файла `use DateTime;`.',
        ],
        [
            'id' => 'use-quiz',
            'type' => 'quiz',
            'title' => 'Что делает use',
            'text' => 'В начале файла написано `use App\\Models\\User;`. Что происходит в этот момент?',
            'options' => [
                'Загружается файл с классом User',
                'Создаётся объект User',
                'Ничего не загружается: дальше в этом файле короткое имя User означает App\\Models\\User',
                'Класс User копируется в текущий неймспейс',
            ],
            'answer' => 2,
            'explain' => '`use` — только сокращение имени. Файл с классом загрузится позже, в момент первого обращения к классу, и сделает это автозагрузчик. Поэтому `use` с опечаткой не даёт ошибки, пока класс реально не понадобится.',
        ],
        [
            'id' => 'imports',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'title' => 'Импорт и корневой неймспейс',
            'text' => "В этом файле два неймспейса, записанных в фигурных скобках, — так делают только в учебных примерах, чтобы уместить всё в один файл.\n\nКлассу `Greeter` нужны класс `User` из соседнего неймспейса и встроенный `DateTime`. Заполни два пропуска:\n\n- первый — ключевое слово для импорта класса;\n- второй — один символ, который говорит «ищи в корневом неймспейсе».",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models {
    class User
    {
        public function __construct(public string $name) {}
    }
}

namespace App\Services {
    ___ App\Models\User;

    class Greeter
    {
        public function greet(User $user): string
        {
            return "Привет, {$user->name}!";
        }

        public function year(string $date): string
        {
            return (new ___DateTime($date))->format('Y');
        }
    }
}

namespace {
    $greeter = new App\Services\Greeter();
    echo $greeter->greet(new App\Models\User('Аня')) . ' ' . $greeter->year('2024-03-15');
}
PHP,
            'tests' => <<<'PHP'
test('greet принимает App\Models\User', function () {
    $greeter = new \App\Services\Greeter();
    assert_eq('Привет, Аня!', $greeter->greet(new \App\Models\User('Аня')));
});
test('year находит встроенный DateTime', function () {
    assert_eq('2024', (new \App\Services\Greeter())->year('2024-03-15'));
});
PHP,
            'hints' => [
                'Импорт класса — ключевое слово `use`.',
                'Корневой неймспейс обозначается ведущей обратной косой чертой: `new \DateTime(…)`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models {
    class User
    {
        public function __construct(public string $name) {}
    }
}

namespace App\Services {
    use App\Models\User;

    class Greeter
    {
        public function greet(User $user): string
        {
            return "Привет, {$user->name}!";
        }

        public function year(string $date): string
        {
            return (new \DateTime($date))->format('Y');
        }
    }
}
PHP,
        ],
        [
            'id' => 'not-found-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'title' => 'Два «не найдено»',
            'text' => "Класс `PriceList` перенесли в собственный неймспейс `App\\Reports`, и он сломался в двух местах. Запусти и прочитай сообщения: в обоих названо полное имя, которое PHP пытался найти.\n\nПочини, добавив в неймспейс `App\\Reports` недостающие импорты. Сами классы менять не нужно.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models {
    class Product
    {
        public function __construct(public string $name, public int $price) {}
    }
}

namespace App\Reports {
    class PriceList
    {
        public function line(Product $product): string
        {
            return "{$product->name} — {$product->price} ₽";
        }

        public function footer(string $date): string
        {
            return 'Цены на ' . (new DateTime($date))->format('d.m.Y');
        }
    }
}

namespace {
    $list = new App\Reports\PriceList();
    echo $list->line(new App\Models\Product('Эфиопия', 890)) . "\n";
    echo $list->footer('2024-03-15') . "\n";
}
PHP,
            'tests' => <<<'PHP'
test('line принимает App\Models\Product', function () {
    $list = new \App\Reports\PriceList();
    assert_eq('Эфиопия — 890 ₽', $list->line(new \App\Models\Product('Эфиопия', 890)));
});
test('footer находит DateTime', function () {
    assert_eq('Цены на 15.03.2024', (new \App\Reports\PriceList())->footer('2024-03-15'));
});
test('классы остались в своих неймспейсах', function () {
    assert_true(class_exists('App\Models\Product') && class_exists('App\Reports\PriceList'), 'Классы должны остаться в App\Models и App\Reports');
});
PHP,
            'hints' => [
                'Первая ошибка: параметр объявлен как `Product`, а внутри `App\Reports` это означает `App\Reports\Product`. Нужен импорт настоящего класса.',
                'В начало блока `namespace App\Reports { … }` добавь `use App\Models\Product;` и `use DateTime;`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models {
    class Product
    {
        public function __construct(public string $name, public int $price) {}
    }
}

namespace App\Reports {
    use App\Models\Product;
    use DateTime;

    class PriceList
    {
        public function line(Product $product): string
        {
            return "{$product->name} — {$product->price} ₽";
        }

        public function footer(string $date): string
        {
            return 'Цены на ' . (new DateTime($date))->format('d.m.Y');
        }
    }
}
PHP,
        ],
        [
            'id' => 'class-to-path',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'title' => 'Сердце автозагрузчика',
            'text' => "Вся автозагрузка по PSR-4 держится на одном преобразовании: полное имя класса → путь к файлу.\n\nНапиши функцию `classToPath(string \$class, string \$prefix, string \$baseDir): ?string`.\n\n- Если имя класса начинается с префикса, вернуть путь: префикс заменяется на папку, обратные косые черты — на прямые, в конце добавляется `.php`.\n- Если не начинается — вернуть `null`: это чужой класс.\n\n```php\nclassToPath('App\\\\Models\\\\User', 'App\\\\', 'src/')   // 'src/Models/User.php'\nclassToPath('Vendor\\\\Tool', 'App\\\\', 'src/')         // null\n```\n\nВ строках PHP обратная косая черта экранируется, поэтому `'App\\\\'` — это текст `App\\`.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

function classToPath(string $class, string $prefix, string $baseDir): ?string
{

}

var_dump(classToPath('App\\Models\\User', 'App\\', 'src/'));
PHP,
            'tests' => <<<'PHP'
test('App\Models\User → src/Models/User.php', function () { assert_eq('src/Models/User.php', classToPath('App\\Models\\User', 'App\\', 'src/')); });
test('класс прямо в префиксе', function () { assert_eq('src/Router.php', classToPath('App\\Router', 'App\\', 'src/')); });
test('глубокая вложенность', function () { assert_eq('lib/Http/Message/Request.php', classToPath('Shop\\Http\\Message\\Request', 'Shop\\', 'lib/')); });
test('чужой префикс → null', function () { assert_eq(null, classToPath('Vendor\\Tool', 'App\\', 'src/')); });
test('похожий, но другой префикс → null', function () { assert_eq(null, classToPath('Application\\User', 'App\\', 'src/')); });
test('префикс в середине имени не считается', function () { assert_eq(null, classToPath('Other\\App\\User', 'App\\', 'src/')); });
PHP,
            'hints' => [
                'Проверка «начинается с»: `str_starts_with($class, $prefix)`. Если нет — `return null;`',
                'Остаток имени после префикса: `substr($class, strlen($prefix))`. Имена классов — латиница, здесь `mb_` не нужен.',
                'Сборка: `$baseDir . str_replace(\'\\\\\', \'/\', $relative) . \'.php\'`.',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function classToPath(string $class, string $prefix, string $baseDir): ?string
{
    if (!str_starts_with($class, $prefix)) {
        return null;
    }

    $relative = substr($class, strlen($prefix));

    return $baseDir . str_replace('\\', '/', $relative) . '.php';
}
PHP,
        ],
        [
            'id' => 'autoloader-plan',
            'type' => 'plan',
            'title' => 'Свой автозагрузчик: план',
            'text' => "Задача: функция `registerAutoloader(string \$prefix, string \$baseDir)`, после вызова которой классы с данным префиксом загружаются сами — без единого `require` в остальном коде.\n\nЗапиши шаги. Что должно происходить в момент регистрации, а что — позже, когда PHP встречает незнакомый класс? Что делать, если класс не наш или файла нет?",
            'reference' => <<<'MD'
**При вызове `registerAutoloader`** происходит одно действие: в PHP регистрируется функция-загрузчик через `spl_autoload_register`. Ни один файл в этот момент не подключается.

**Позже, когда PHP встречает незнакомый класс,** он вызывает загрузчик и передаёт полное имя. Загрузчик:

1. Превращает имя класса в путь к файлу — это `classToPath` из прошлого задания.
2. Если получился `null` (класс с чужим префиксом) — молча выходит.
3. Если файла по этому пути нет — тоже молча выходит.
4. Иначе подключает файл через `require`.

«Молча выйти» — это важно. Загрузчиков может быть несколько, PHP вызывает их по очереди. Каждый отвечает только за свои классы; если не справился никто, PHP сам выдаст ошибку «класс не найден». Загрузчик, который бросает ошибку на чужом классе, ломает работу остальных.

Загрузчик — это замыкание: ему нужны `$prefix` и `$baseDir` в момент, когда `registerAutoloader` давно завершилась.
MD,
        ],
        [
            'id' => 'autoloader',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'title' => 'Свой автозагрузчик: код',
            'text' => "Напиши `registerAutoloader(string \$prefix, string \$baseDir): void` по своему плану. Функцию `classToPath` перенеси из прошлого задания.\n\nТесты создают временную папку с настоящими файлами классов и проверяют, что они загружаются.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

PHP,
            'tests' => <<<'PHP'
$dir = sys_get_temp_dir() . '/autoload_' . bin2hex(random_bytes(4)) . '/';
mkdir($dir . 'Models', 0700, true);
file_put_contents($dir . 'Models/Item.php', '<?php namespace Shop\Models; class Item { public function name(): string { return "товар"; } }');
file_put_contents($dir . 'Cart.php', '<?php namespace Shop; use Shop\Models\Item; class Cart { public function first(): Item { return new Item(); } }');

test('регистрация сама ничего не загружает', function () use ($dir) {
    registerAutoloader('Shop\\', $dir);
    assert_eq(false, class_exists('Shop\Cart', false), 'Класс загружен раньше, чем понадобился');
});
test('класс загружается при первом обращении', function () {
    $cart = new \Shop\Cart();
    assert_eq('Shop\Cart', get_class($cart));
});
test('класс из вложенной папки тоже находится', function () {
    assert_eq('товар', (new \Shop\Cart())->first()->name());
});
test('чужой префикс загрузчик не трогает', function () {
    assert_eq(false, class_exists('Other\Thing'), 'Для чужого класса загрузчик должен молча выйти');
});
test('нет файла — без ошибок и предупреждений', function () {
    ob_start();
    $exists = class_exists('Shop\Missing');
    $noise = ob_get_clean();
    assert_eq([false, ''], [$exists, $noise], 'Загрузчик не должен падать или что-то печатать, если файла нет');
});

array_map('unlink', [$dir . 'Models/Item.php', $dir . 'Cart.php']);
rmdir($dir . 'Models');
rmdir($dir);
PHP,
            'hints' => [
                'Каркас: `function registerAutoloader(string $prefix, string $baseDir): void { spl_autoload_register(function (string $class) use ($prefix, $baseDir): void { … }); }`',
                'Внутри: `$path = classToPath($class, $prefix, $baseDir);`',
                'Проверка и подключение: `if ($path !== null && is_file($path)) { require $path; }`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

function classToPath(string $class, string $prefix, string $baseDir): ?string
{
    if (!str_starts_with($class, $prefix)) {
        return null;
    }

    return $baseDir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
}

function registerAutoloader(string $prefix, string $baseDir): void
{
    spl_autoload_register(function (string $class) use ($prefix, $baseDir): void {
        $path = classToPath($class, $prefix, $baseDir);

        if ($path !== null && is_file($path)) {
            require $path;
        }
    });
}
PHP,
        ],
    ],
    'cards' => [
        ['Из чего состоит полное имя класса?', 'Неймспейс и короткое имя через обратную косую черту: `App\Models\User`.'],
        ['Что делает `use App\Models\User;`?', 'Только сокращает имя в пределах файла. Ничего не загружает — файл подключит автозагрузчик при первом обращении к классу.'],
        ['Почему внутри неймспейса `new DateTime()` даёт «класс не найден»?', 'Короткое имя ищется в текущем неймспейсе. Встроенные классы — в корневом: нужно `new \DateTime()` или `use DateTime;`.'],
        ['Как PSR-4 связывает имя класса и файл?', 'Префиксу неймспейса соответствует папка, остальная часть имени повторяет путь: `App\Models\User` → `src/Models/User.php`. Регистр важен.'],
        ['Что должен сделать автозагрузчик, если класс не его или файла нет?', 'Молча выйти. Загрузчиков может быть несколько; ошибку «класс не найден» выдаст сам PHP, если не справился никто.'],
        ['Чем `require` отличается от `include`, и что добавляет `_once`?', '`require` при отсутствии файла останавливает программу, `include` даёт предупреждение. `_once` не подключает файл повторно.'],
    ],
];
