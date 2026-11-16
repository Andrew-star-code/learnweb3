# Исключения

Исключения в PHP работают так же, как ошибки в JavaScript: `throw` бросает, `try/catch/finally` перехватывает. Всё, что ты знаешь из урока про ошибки, применимо. Разница — в типах: в PHP `catch` выбирает ошибку по классу.

## Бросить и перехватить

```php
function divide(float $a, float $b): float
{
    if ($b === 0.0) {
        throw new InvalidArgumentException('Деление на ноль');
    }

    return $a / $b;
}

try {
    echo divide(10, 0);
} catch (InvalidArgumentException $e) {
    echo 'Ошибка: ' . $e->getMessage();
} finally {
    echo ' — готово';
}
```

У объекта исключения есть методы `getMessage()`, `getCode()`, `getFile()`, `getLine()`, `getTraceAsString()` и `getPrevious()`.

## catch выбирает по типу

В JavaScript один `catch` ловит всё, а тип проверяют внутри через `instanceof`. В PHP у `catch` указывается класс, и блоков может быть несколько:

```php
try {
    $order = $service->create($data);
} catch (ValidationException $e) {
    showFormErrors($e->getErrors());       // ожидаемая ошибка — показать пользователю
} catch (PaymentException | NetworkException $e) {
    showMessage('Оплата не прошла, попробуйте позже');
}
// всё остальное летит дальше — и это правильно
```

Блоки проверяются сверху вниз, срабатывает первый подходящий. Исключение, которое не подошло ни под один, продолжает подниматься. Неизвестную ошибку не нужно ловить «на всякий случай».

`catch (Exception $e)` ловит любое исключение. Это уместно в одном месте — на самом верху приложения, чтобы записать ошибку в журнал и показать страницу «что-то пошло не так».

## Встроенные классы

| Класс | Когда бросать |
|---|---|
| `InvalidArgumentException` | Аргумент недопустим по смыслу |
| `RuntimeException` | Что-то пошло не так во время работы: нет файла, не ответил сервис |
| `LogicException` | Ошибка в самом коде: метод вызван не вовремя |
| `OutOfRangeException`, `LengthException` | Частные случаи |
| `JsonException` | Не удалось разобрать JSON |

Отдельная ветка — `Error`: её бросает сам PHP при ошибках в коде. `TypeError` — неверный тип аргумента, `DivisionByZeroError` — целочисленное деление на ноль, `Error` — вызов несуществующего метода. У `Exception` и `Error` общий предок — интерфейс `Throwable`.

Ловить `Error` в обычном коде не нужно: это не ситуация, а баг, и его надо чинить.

## Свои исключения

```php
class ValidationException extends RuntimeException
{
    public function __construct(
        string $message,
        private string $field,
    ) {
        parent::__construct($message);
    }

    public function getField(): string
    {
        return $this->field;
    }
}

throw new ValidationException('Возраст должен быть числом', 'age');
```

Свой класс нужен, когда вызывающему коду важно отличить эту ошибку от остальных или получить вместе с ней данные.

## Цепочка исключений

Перехватить техническую ошибку и бросить понятную, не потеряв причину:

```php
try {
    $config = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    throw new RuntimeException('Файл настроек повреждён', 0, $e);
}
```

Третий аргумент конструктора — предыдущее исключение. Оно остаётся доступным через `getPrevious()` и попадает в журнал вместе с новым.

## Ошибки старого образца

Не всё в PHP сообщает о проблемах исключениями. Старые функции возвращают `false` или `null` и печатают предупреждение:

```php
$text = file_get_contents('нет-такого.txt');     // Warning + false
$data = json_decode('{сломано');                 // null, без единого сообщения
$number = (int) 'много';                         // 0
```

Отсюда правило: **результат старых функций проверяют явно**.

```php
$text = file_get_contents($path);
if ($text === false) {
    throw new RuntimeException("Не удалось прочитать $path");
}
```

`json_decode` по умолчанию молчит при ошибке. Флаг `JSON_THROW_ON_ERROR` заставляет его бросать `JsonException` — используй его всегда.

В легаси встречается оператор `@` перед вызовом: `@file_get_contents($path)`. Он глушит предупреждение. Ошибка при этом никуда не девается — её просто перестаёт быть видно. Это аналог пустого `catch`.

> **В PHP 7.** Исключения, `finally`, `Throwable` и перехват нескольких классов через `|` (с 7.1) работают так же. `JSON_THROW_ON_ERROR` появился в 7.3 — до него после `json_decode` проверяют `json_last_error() !== JSON_ERROR_NONE`. Главное отличие в поведении: многое из того, что в PHP 7 было предупреждением и позволяло коду идти дальше, в PHP 8 стало исключением. Этому посвящён блок «Легаси PHP».

## Глубже

- [Исключения](https://www.php.net/manual/ru/language.exceptions.php) — руководство PHP
- [Встроенные исключения](https://www.php.net/manual/ru/spl.exceptions.php)
- [Ошибки в PHP 7 и выше](https://www.php.net/manual/ru/language.errors.php7.php)
