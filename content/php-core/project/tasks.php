<?php
return [
    'tasks' => [
        [
            'id' => 'plan',
            'type' => 'plan',
            'title' => 'План проекта',
            'text' => "Перечитай требования и запиши план: что делает каждая из пяти частей и какими предыдущими частями пользуется. Для методов `LogReport` отметь, какой из типовых приёмов нужен — накопление, отбор, счётчик, сортировка.",
            'reference' => <<<'MD'
**`LogEntry`** — объект-значение: пять свойств только для чтения и одна проверка `isError`. Логики почти нет.

**`LogParseException`** — пустой класс-наследник. Нужен, чтобы ошибки разбора можно было отличить от любых других.

**`parseLine`**

1. Убрать пробелы по краям и разбить строку по пробелам.
2. Если частей не пять — исключение про формат.
3. Проверить, что код и размер состоят только из цифр, — иначе исключение про число.
4. Создать и вернуть `LogEntry`, приведя два последних поля к `int`.

**`parseLog`**

1. Разбить текст на строки и пройти по ним с индексом.
2. Пустые пропустить.
3. Вызвать `parseLine` в `try`. Перехватить `LogParseException` и бросить новое — с номером строки в тексте и исходным исключением в качестве предыдущего.

**`LogReport`**

| Метод | Приём |
|---|---|
| `total` | Длина массива |
| `errorRate` | Отбор ошибочных, деление на общее число; отдельный случай — пустой журнал |
| `countByStatus` | Массив-счётчик, затем сортировка по ключам |
| `topIps` | Массив-счётчик, сортировка по убыванию значения, срез первых `n` |
| `totalBytes` | Накопление |

Самое тонкое место — `topIps`: сортировать нужно по значению, сохранив ключи-адреса, а при равенстве — по самому адресу. Удобно отсортировать список адресов функцией `usort`, сравнивая пары `[число запросов, адрес]`, и затем собрать результат заново.
MD,
        ],
        [
            'id' => 'log',
            'type' => 'code',
            'kind' => 'project',
            'lang' => 'php',
            'noSolution' => true,
            'title' => 'Разбор журнала',
            'text' => "Напиши два класса, исключение и две функции из требований. Тесты — чек-лист приёмки, они идут в том же порядке.\n\nВ заготовке лежит журнал для проверки руками.",
            'starter' => <<<'PHP'
<?php

declare(strict_types=1);

$sample = "192.168.1.10 GET /catalog 200 512
192.168.1.10 GET /catalog/5 200 1024
10.0.0.7 POST /login 401 120
10.0.0.7 POST /login 401 120
192.168.1.22 GET /missing 404 80
10.0.0.7 POST /login 500 0";

// 1. class LogEntry


// 2. class LogParseException


// 3. function parseLine(string $line): LogEntry


// 4. function parseLog(string $text): array


// 5. class LogReport


// $report = new LogReport(parseLog($sample));
// print_r($report->topIps(2));
PHP,
            'tests' => <<<'PHP'
$text = "192.168.1.10 GET /catalog 200 512\n192.168.1.10 GET /catalog/5 200 1024\n10.0.0.7 POST /login 401 120\n\n10.0.0.7 POST /login 401 120\n192.168.1.22 GET /missing 404 80\n10.0.0.7 POST /login 500 0";

test('1. LogEntry хранит пять полей', function () {
    $e = new LogEntry('10.0.0.7', 'POST', '/login', 401, 120);
    assert_eq(['10.0.0.7', 'POST', '/login', 401, 120], [$e->ip, $e->method, $e->path, $e->status, $e->bytes]);
});
test('1. свойства LogEntry только для чтения', function () {
    assert_throws(function () { $e = new LogEntry('a', 'GET', '/', 200, 1); $e->status = 500; }, 'Error', 'Свойства должны быть readonly');
});
test('1. isError: 400 и выше', function () {
    $make = fn(int $status) => (new LogEntry('a', 'GET', '/', $status, 1))->isError();
    assert_eq([false, false, true, true, true], [$make(200), $make(399), $make(400), $make(404), $make(500)]);
});

test('2. LogParseException — наследник RuntimeException', function () {
    assert_true(new LogParseException('x') instanceof RuntimeException, 'Нет наследования от RuntimeException');
});

test('3. parseLine разбирает строку в LogEntry', function () {
    $e = parseLine('192.168.1.10 GET /catalog 200 512');
    assert_true($e instanceof LogEntry, 'parseLine должна вернуть LogEntry');
    assert_eq(['192.168.1.10', 'GET', '/catalog', 200, 512], [$e->ip, $e->method, $e->path, $e->status, $e->bytes]);
});
test('3. пробелы по краям не мешают', function () { assert_eq(404, parseLine('  10.0.0.1 GET /x 404 0  ')->status); });
test('3. не пять полей → «Неверный формат строки»', function () {
    assert_eq('Неверный формат строки', assert_throws(function () { parseLine('10.0.0.1 GET /x 200'); }, 'LogParseException')->getMessage());
    assert_eq('Неверный формат строки', assert_throws(function () { parseLine('10.0.0.1 GET /x 200 5 лишнее'); }, 'LogParseException')->getMessage());
});
test('3. нечисловой код или размер → «Неверное число»', function () {
    assert_eq('Неверное число', assert_throws(function () { parseLine('10.0.0.1 GET /x OK 5'); }, 'LogParseException')->getMessage());
    assert_eq('Неверное число', assert_throws(function () { parseLine('10.0.0.1 GET /x 200 -5'); }, 'LogParseException')->getMessage());
    assert_eq('Неверное число', assert_throws(function () { parseLine('10.0.0.1 GET /x 200 1.5'); }, 'LogParseException')->getMessage());
});

test('4. parseLog разбирает текст, пропуская пустые строки', function () use ($text) {
    $entries = parseLog($text);
    assert_eq(6, count($entries));
    assert_eq(['192.168.1.22', 404], [$entries[4]->ip, $entries[4]->status]);
});
test('4. пустой текст → []', function () { assert_eq([], parseLog('')); });
test('4. ошибка содержит номер строки', function () {
    $e = assert_throws(function () { parseLog("10.0.0.1 GET /x 200 5\n\n10.0.0.1 GET /x плохо 5"); }, 'LogParseException');
    assert_eq('Строка 3: Неверное число', $e->getMessage());
});
test('4. исходное исключение сохранено как предыдущее', function () {
    $e = assert_throws(function () { parseLog('сломано'); }, 'LogParseException');
    assert_eq('Строка 1: Неверный формат строки', $e->getMessage());
    assert_true($e->getPrevious() instanceof LogParseException, 'В getPrevious() должно лежать исключение из parseLine');
    assert_eq('Неверный формат строки', $e->getPrevious()->getMessage());
});

test('5. total и totalBytes', function () use ($text) {
    $report = new LogReport(parseLog($text));
    assert_eq([6, 1856], [$report->total(), $report->totalBytes()]);
});
test('5. errorRate — доля ошибочных запросов', function () use ($text) {
    assert_eq(0.67, round((new LogReport(parseLog($text)))->errorRate(), 2));
});
test('5. countByStatus: ключи по возрастанию', function () use ($text) {
    assert_eq([200 => 2, 401 => 2, 404 => 1, 500 => 1], (new LogReport(parseLog($text)))->countByStatus());
});
test('5. topIps: по убыванию числа запросов', function () use ($text) {
    $report = new LogReport(parseLog($text));
    assert_eq(['10.0.0.7' => 3, '192.168.1.10' => 2], $report->topIps(2));
    assert_eq(['10.0.0.7' => 3], $report->topIps(1));
});
test('5. topIps: при равенстве — по алфавиту адреса, n больше числа адресов', function () {
    $report = new LogReport(parseLog("b.b.b.b GET / 200 1\na.a.a.a GET / 200 1\nc.c.c.c GET / 200 1\nc.c.c.c GET / 200 1"));
    assert_eq(['c.c.c.c' => 2, 'a.a.a.a' => 1, 'b.b.b.b' => 1], $report->topIps(10));
});
test('5. пустой журнал', function () {
    $report = new LogReport([]);
    assert_eq([0, 0.0, [], [], 0], [$report->total(), $report->errorRate(), $report->countByStatus(), $report->topIps(3), $report->totalBytes()]);
});
PHP,
            'hints' => [
                '**LogEntry.** Короткий конструктор с пятью параметрами вида `public readonly string $ip`. Метод: `return $this->status >= 400;`',
                '**parseLine.** Разбить: `$parts = preg_split(\'/\s+/\', trim($line));`. Проверка числа: `ctype_digit($parts[3])` — истина, только если строка состоит из одних цифр.',
                '**parseLog.** `foreach (explode("\n", $text) as $index => $line)`. Внутри: `try { $entries[] = parseLine($line); } catch (LogParseException $e) { throw new LogParseException(\'Строка \' . ($index + 1) . \': \' . $e->getMessage(), 0, $e); }`',
                '**countByStatus.** Счётчик `$counts[$entry->status] = ($counts[$entry->status] ?? 0) + 1;`, затем `ksort($counts);`',
                '**topIps.** Посчитать `$counts` по адресам. Взять `$ips = array_keys($counts);` и отсортировать: `usort($ips, fn($a, $b) => [$counts[$b], $a] <=> [$counts[$a], $b]);`. Затем пройти по `array_slice($ips, 0, $n)` и собрать результат: `$top[$ip] = $counts[$ip];`',
            ],
            'solution' => <<<'PHP'
<?php

declare(strict_types=1);

class LogEntry
{
    public function __construct(
        public readonly string $ip,
        public readonly string $method,
        public readonly string $path,
        public readonly int $status,
        public readonly int $bytes,
    ) {}

    public function isError(): bool
    {
        return $this->status >= 400;
    }
}

class LogParseException extends RuntimeException
{
}

function parseLine(string $line): LogEntry
{
    $parts = preg_split('/\s+/', trim($line));

    if (count($parts) !== 5) {
        throw new LogParseException('Неверный формат строки');
    }

    [$ip, $method, $path, $status, $bytes] = $parts;

    if (!ctype_digit($status) || !ctype_digit($bytes)) {
        throw new LogParseException('Неверное число');
    }

    return new LogEntry($ip, $method, $path, (int) $status, (int) $bytes);
}

function parseLog(string $text): array
{
    $entries = [];

    foreach (explode("\n", $text) as $index => $line) {
        if (trim($line) === '') {
            continue;
        }

        try {
            $entries[] = parseLine($line);
        } catch (LogParseException $e) {
            throw new LogParseException('Строка ' . ($index + 1) . ': ' . $e->getMessage(), 0, $e);
        }
    }

    return $entries;
}

class LogReport
{
    public function __construct(private array $entries) {}

    public function total(): int
    {
        return count($this->entries);
    }

    public function errorRate(): float
    {
        if ($this->total() === 0) {
            return 0.0;
        }

        $errors = array_filter($this->entries, fn(LogEntry $entry) => $entry->isError());

        return count($errors) / $this->total();
    }

    public function countByStatus(): array
    {
        $counts = [];
        foreach ($this->entries as $entry) {
            $counts[$entry->status] = ($counts[$entry->status] ?? 0) + 1;
        }
        ksort($counts);

        return $counts;
    }

    public function topIps(int $n): array
    {
        $counts = [];
        foreach ($this->entries as $entry) {
            $counts[$entry->ip] = ($counts[$entry->ip] ?? 0) + 1;
        }

        $ips = array_keys($counts);
        usort($ips, fn($a, $b) => [$counts[$b], $a] <=> [$counts[$a], $b]);

        $top = [];
        foreach (array_slice($ips, 0, $n) as $ip) {
            $top[$ip] = $counts[$ip];
        }

        return $top;
    }

    public function totalBytes(): int
    {
        return array_sum(array_map(fn(LogEntry $entry) => $entry->bytes, $this->entries));
    }
}
PHP,
        ],
    ],
    'cards' => [
        ['Как проверить, что строка состоит только из цифр?', '`ctype_digit($text)`. Отсекает минус, точку, пробелы и пустую строку — в отличие от `is_numeric`.'],
        ['Как отсортировать по одному признаку, а при равенстве — по другому?', 'Сравнить массивы-пары оператором `<=>`: `[$countB, $nameA] <=> [$countA, $nameB]` — сначала по убыванию числа, затем по возрастанию имени.'],
        ['Зачем проекту собственный класс исключения, даже пустой?', 'Чтобы вызывающий код мог перехватить именно ошибки этой части программы и не поймать случайно чужие.'],
    ],
];
