<?php
return [
    'tasks' => [
        [
            'id' => 'scope-quiz',
            'type' => 'quiz',
            'title' => 'Скидка, которой нет',
            'text' => "```php\n<?php\n\$discount = 10;\n\nfunction finalPrice(\$price)\n{\n    return \$price - \$price * \$discount / 100;\n}\n\necho finalPrice(200);\n```\n\nЧто выведет этот код?",
            'options' => [
                '180',
                '200 и сообщение о неизвестной переменной',
                '0',
                'Ничего: скрипт остановится с ошибкой',
            ],
            'answer' => 1,
            'explain' => 'Функция в PHP не видит переменных снаружи. Внутри `$discount` — новая пустая переменная: PHP сообщает о ней и подставляет `null`, то есть ноль. Скидка не применяется, скрипт идёт дальше. В старом коде до внешней переменной дотягиваются через `global $discount;`, в новом — передают её параметром.',
        ],
        [
            'id' => 'minimal-quiz',
            'type' => 'quiz',
            'title' => 'Одна опечатка',
            'text' => "Тебя попросили исправить опечатку в подписи «Итго» на странице заказа. Файл — 800 строк старого PHP вперемешку с HTML. Ты исправил слово, сохранил — и редактор заодно переформатировал весь файл: в списке изменений теперь 640 строк.\n\nЧто делать?",
            'options' => [
                'Отправить как есть: код стал аккуратнее, это бонус',
                'Раз уж файл открыт, заодно заменить array() на [] и убрать global',
                'Отменить форматирование и оставить в правке одну изменённую строку',
                'Отправить как есть, но написать в описании, что менялась только подпись',
            ],
            'answer' => 2,
            'explain' => 'Правка должна содержать то, о чём просили, и ничего больше. В 640 изменённых строках проверяющий не найдёт твоё слово и не сможет убедиться, что остальное не сломано. А если после выкладки что-то пойдёт не так, под подозрением окажется весь файл. Автоформатирование для таких файлов отключают в настройках редактора.',
        ],
        [
            'id' => 'two-holes',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'php',
            'php' => '7',
            'title' => 'Две дыры',
            'text' => "Две функции из старого каталога. В первой номер товара из адресной строки вклеивается в запрос, во второй отзыв покупателя вставляется в страницу как есть.\n\nЗаполни три пропуска: привести номер к целому числу; назвать функцию, которая экранирует текст для HTML; указать флаг, с которым она экранирует оба вида кавычек.\n\nЗадание проверяется на PHP 7.0.",
            'starter' => <<<'PHP'
<?php

function productQuery($id)
{
    return 'SELECT * FROM products WHERE id = ' . ___ $id;
}

function reviewHtml(array $review)
{
    return '<p><b>' . ___($review['author'], ENT_QUOTES, 'UTF-8') . '</b>: '
        . htmlspecialchars($review['text'], ___, 'UTF-8') . '</p>';
}

echo productQuery('7 OR 1=1'), "\n";
echo reviewHtml(array('author' => 'Аня', 'text' => '<script>alert(1)</script>')), "\n";
PHP,
            'tests' => <<<'PHP'
test('productQuery: обычный номер', function () { assert_eq('SELECT * FROM products WHERE id = 7', productQuery('7')); });
test('productQuery: хвост после числа отброшен', function () { assert_eq('SELECT * FROM products WHERE id = 7', productQuery('7 OR 1=1')); });
test('productQuery: не число → 0', function () { assert_eq('SELECT * FROM products WHERE id = 0', productQuery("' OR ''='")); });
test('reviewHtml: обычный отзыв', function () {
    assert_eq('<p><b>Аня</b>: Вкусно</p>', reviewHtml(array('author' => 'Аня', 'text' => 'Вкусно')));
});
test('reviewHtml: теги в тексте обезврежены', function () {
    assert_eq('<p><b>Аня</b>: &lt;script&gt;alert(1)&lt;/script&gt;</p>', reviewHtml(array('author' => 'Аня', 'text' => '<script>alert(1)</script>')));
});
test('reviewHtml: теги в имени обезврежены', function () {
    assert_eq('<p><b>&lt;i&gt;Аня&lt;/i&gt;</b>: ок</p>', reviewHtml(array('author' => '<i>Аня</i>', 'text' => 'ок')));
});
test('reviewHtml: оба вида кавычек экранируются', function () {
    assert_eq('<p><b>О&#039;Брайен</b>: &quot;отлично&quot; и &#039;супер&#039;</p>', reviewHtml(array('author' => "О'Брайен", 'text' => "\"отлично\" и 'супер'")));
});
PHP,
            'hints' => [
                'Приведение к целому числу: `(int) $id`. Строка `\'7 OR 1=1\'` превратится в `7`.',
                'Экранирование для HTML — `htmlspecialchars`. Она уже стоит строчкой ниже.',
                'Флаг — `ENT_QUOTES`. Он тоже уже есть строчкой выше.',
            ],
            'solution' => <<<'PHP'
<?php

function productQuery($id)
{
    return 'SELECT * FROM products WHERE id = ' . (int) $id;
}

function reviewHtml(array $review)
{
    return '<p><b>' . htmlspecialchars($review['author'], ENT_QUOTES, 'UTF-8') . '</b>: '
        . htmlspecialchars($review['text'], ENT_QUOTES, 'UTF-8') . '</p>';
}
PHP,
        ],
        [
            'id' => 'globals',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'php',
            'php' => '7',
            'title' => 'Сумма, которая растёт',
            'text' => "Из бухгалтерии кофейни пришли две жалобы на один файл:\n\n1. В сумму корзины не входит налог 20%.\n2. Если на странице сумма выводится дважды, второй раз она вдвое больше.\n\nЗапусти код и найди обе причины. Они связаны с тем, какие переменные видны внутри функций. Правка должна быть минимальной: по одной строке на каждую ошибку. Остальное — `array()`, `global \$currency`, названия — не трогай.\n\nЗадание проверяется на PHP 7.0.",
            'starter' => <<<'PHP'
<?php

$taxRate = 20;
$currency = 'руб.';
$total = 0;

function priceWithTax($price)
{
    return $price + $price * $taxRate / 100;
}

function formatPrice($price)
{
    global $currency;

    return number_format($price, 2, ',', ' ') . ' ' . $currency;
}

function cartTotal($items)
{
    global $total;

    foreach ($items as $item) {
        $total += priceWithTax($item['price']) * $item['qty'];
    }

    return $total;
}

$cart = array(
    array('price' => 100, 'qty' => 2),
    array('price' => 50, 'qty' => 1),
);

echo formatPrice(cartTotal($cart)), "\n";     // ждём 300,00 руб.
echo formatPrice(cartTotal($cart)), "\n";     // и здесь 300,00 руб.
PHP,
            'tests' => <<<'PHP'
test('priceWithTax: налог 20% учтён', function () { assert_eq(120.0, (float) priceWithTax(100)); });
test('priceWithTax: ставка берётся из настройки $taxRate', function () {
    $GLOBALS['taxRate'] = 10;
    $price = (float) priceWithTax(100);
    $GLOBALS['taxRate'] = 20;
    assert_eq(110.0, $price, 'При $taxRate = 10');
});
test('cartTotal: сумма с налогом', function () {
    assert_eq(300.0, (float) cartTotal(array(array('price' => 100, 'qty' => 2), array('price' => 50, 'qty' => 1))));
});
test('cartTotal: второй вызов даёт ту же сумму', function () {
    $cart = array(array('price' => 100, 'qty' => 1));
    cartTotal($cart);
    assert_eq(120.0, (float) cartTotal($cart));
});
test('cartTotal: пустая корзина → 0', function () { assert_eq(0.0, (float) cartTotal(array())); });
test('formatPrice не сломан', function () { assert_eq('1 234,50 руб.', formatPrice(1234.5)); });
test('страница выводит одну и ту же сумму дважды', function () {
    assert_eq("300,00 руб.\n300,00 руб.", trim(output()));
});
PHP,
            'hints' => [
                'Первая ошибка: `priceWithTax` не видит `$taxRate`. Посмотри, как `formatPrice` получает `$currency`.',
                'Вторая ошибка: `cartTotal` складывает в глобальную переменную `$total`, и та помнит прошлые вызовы. Сумма корзины должна каждый раз начинаться с нуля.',
                'Правка: в `priceWithTax` добавить `global $taxRate;`, а в `cartTotal` заменить `global $total;` на `$total = 0;`.',
            ],
            'solution' => <<<'PHP'
<?php

$taxRate = 20;
$currency = 'руб.';
$total = 0;

function priceWithTax($price)
{
    global $taxRate;

    return $price + $price * $taxRate / 100;
}

function formatPrice($price)
{
    global $currency;

    return number_format($price, 2, ',', ' ') . ' ' . $currency;
}

function cartTotal($items)
{
    $total = 0;

    foreach ($items as $item) {
        $total += priceWithTax($item['price']) * $item['qty'];
    }

    return $total;
}

$cart = array(
    array('price' => 100, 'qty' => 2),
    array('price' => 50, 'qty' => 1),
);

echo formatPrice(cartTotal($cart)), "\n";
echo formatPrice(cartTotal($cart)), "\n";
PHP,
        ],
        [
            'id' => 'extract-logic',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'php',
            'php' => '7',
            'title' => 'Расчёт из разметки — в функцию',
            'text' => "Страница со списком заказов. Сумма и скидка считаются прямо внутри таблицы, поэтому проверить расчёт отдельно нельзя.\n\nВынеси его в функцию `orderSummary(array \$order)`. Она возвращает массив с ключами `sum` и `note` — те самые значения, которые сейчас лежат в переменных `\$sum` и `\$note`. В таблице оставь вызов функции и вывод.\n\nПоследний тест сравнивает HTML страницы с тем, что она выводит сейчас. Он уже проходит — и должен проходить после твоей правки: это и есть «зафиксировать поведение».\n\nЗадание проверяется на PHP 7.0.",
            'starter' => <<<'PHP'
<?php
$orders = array(
    array('id' => 101, 'customer' => 'Анна', 'items' => array(
        array('price' => 250, 'qty' => 2),
        array('price' => 50, 'qty' => 1),
    )),
    array('id' => 102, 'customer' => 'Борис <Кофеман>', 'items' => array()),
    array('id' => 103, 'customer' => 'Вера', 'items' => array(
        array('price' => 1200, 'qty' => 1),
    )),
);
?>
<table>
<?php foreach ($orders as $order) { ?>
<?php
    $sum = 0;
    foreach ($order['items'] as $item) {
        $sum += $item['price'] * $item['qty'];
    }
    if ($sum >= 1000) {
        $sum = $sum - $sum * 10 / 100;
        $note = 'скидка 10%';
    } else {
        $note = '';
    }
?>
    <tr>
        <td><?= $order['id'] ?></td>
        <td><?= htmlspecialchars($order['customer'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= $sum ?> руб.</td>
        <td><?= $note ?></td>
    </tr>
<?php } ?>
</table>
PHP,
            'tests' => <<<'PHP'
function summaryOf($items) {
    $result = orderSummary(array('id' => 1, 'customer' => 'Тест', 'items' => $items));
    return array((float) $result['sum'], $result['note']);
}
test('orderSummary: сумма без скидки', function () {
    assert_eq(array(550.0, ''), summaryOf(array(array('price' => 250, 'qty' => 2), array('price' => 50, 'qty' => 1))));
});
test('orderSummary: пустой заказ', function () { assert_eq(array(0.0, ''), summaryOf(array())); });
test('orderSummary: от 1000 — скидка 10%', function () {
    assert_eq(array(1080.0, 'скидка 10%'), summaryOf(array(array('price' => 1200, 'qty' => 1))));
});
test('orderSummary: ровно 1000 — уже со скидкой', function () {
    assert_eq(array(900.0, 'скидка 10%'), summaryOf(array(array('price' => 500, 'qty' => 2))));
});
test('orderSummary: 999 — ещё без скидки', function () {
    assert_eq(array(999.0, ''), summaryOf(array(array('price' => 999, 'qty' => 1))));
});
test('orderSummary возвращает ключи sum и note', function () {
    $keys = array_keys(orderSummary(array('id' => 1, 'customer' => 'Тест', 'items' => array())));
    sort($keys);
    assert_eq(array('note', 'sum'), $keys);
});
test('страница выводит то же, что и раньше', function () {
    $html = trim(preg_replace('/>\s+</', '><', output()));
    $expected = '<table>'
        . '<tr><td>101</td><td>Анна</td><td>550 руб.</td><td></td></tr>'
        . '<tr><td>102</td><td>Борис &lt;Кофеман&gt;</td><td>0 руб.</td><td></td></tr>'
        . '<tr><td>103</td><td>Вера</td><td>1080 руб.</td><td>скидка 10%</td></tr>'
        . '</table>';
    assert_true($html === $expected, 'HTML страницы изменился. Получено: ' . $html);
});
PHP,
            'hints' => [
                'Расчёт читает только `$order[\'items\']` и создаёт `$sum` и `$note`. Значит, функция принимает заказ и возвращает `array(\'sum\' => $sum, \'note\' => $note)`.',
                'Объяви функцию в первом блоке PHP, рядом с `$orders`, и перенеси в неё строки расчёта без изменений.',
                'В таблице вместо расчёта останется одна строка: `<?php $summary = orderSummary($order); ?>`, а в ячейках — `<?= $summary[\'sum\'] ?>` и `<?= $summary[\'note\'] ?>`.',
            ],
            'solution' => <<<'PHP'
<?php
$orders = array(
    array('id' => 101, 'customer' => 'Анна', 'items' => array(
        array('price' => 250, 'qty' => 2),
        array('price' => 50, 'qty' => 1),
    )),
    array('id' => 102, 'customer' => 'Борис <Кофеман>', 'items' => array()),
    array('id' => 103, 'customer' => 'Вера', 'items' => array(
        array('price' => 1200, 'qty' => 1),
    )),
);

function orderSummary(array $order)
{
    $sum = 0;
    foreach ($order['items'] as $item) {
        $sum += $item['price'] * $item['qty'];
    }
    if ($sum >= 1000) {
        $sum = $sum - $sum * 10 / 100;
        $note = 'скидка 10%';
    } else {
        $note = '';
    }

    return array('sum' => $sum, 'note' => $note);
}
?>
<table>
<?php foreach ($orders as $order) { ?>
<?php $summary = orderSummary($order); ?>
    <tr>
        <td><?= $order['id'] ?></td>
        <td><?= htmlspecialchars($order['customer'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= $summary['sum'] ?> руб.</td>
        <td><?= $summary['note'] ?></td>
    </tr>
<?php } ?>
</table>
PHP,
        ],
        [
            'id' => 'query-plan',
            'type' => 'plan',
            'title' => 'Фильтр заказов без склейки: план',
            'text' => "В старой админке запрос для списка заказов собирается так:\n\n```php\n\$sql = \"SELECT * FROM orders WHERE 1=1\";\nif (\$_GET['status'] != '') {\n    \$sql .= \" AND status = '\" . \$_GET['status'] . \"'\";\n}\nif (\$_GET['customer'] != '') {\n    \$sql .= \" AND customer LIKE '%\" . \$_GET['customer'] . \"%'\";\n}\nif (\$_GET['min_total'] != '') {\n    \$sql .= \" AND total >= \" . \$_GET['min_total'];\n}\n\$sql .= \" ORDER BY id DESC\";\n```\n\nЛюбое из трёх полей позволяет дописать к запросу что угодно. Тебе поручили заменить этот кусок функцией `ordersQuery(array \$filters)`. Она возвращает массив из двух частей: `sql` — текст запроса со знаками `?` на месте значений, и `params` — сами значения по порядку.\n\nДля фильтров `status = new` и `min_total = 500` результат такой:\n\n```text\nsql:    SELECT * FROM orders WHERE status = ? AND total >= ? ORDER BY id DESC\nparams: ['new', 500]\n```\n\nФильтр, которого нет в массиве или который равен пустой строке, пропускается. Без фильтров слова `WHERE` в запросе нет.\n\nЗапиши шаги. Подумай, как собрать условия так, чтобы не возиться с тем, где ставить `AND`.",
            'reference' => <<<'MD'
1. Завести два пустых массива: условия и значения.
2. Для фильтра `status`: если он задан — добавить условие `status = ?` и значение.
3. Для `customer`: если задан — добавить `customer LIKE ?` и значение, обёрнутое в знаки процента.
4. Для `min_total`: если задан — добавить `total >= ?` и значение, приведённое к целому числу.
5. Начать текст запроса с `SELECT * FROM orders`.
6. Если условия есть — дописать ` WHERE ` и условия, склеенные через ` AND `. — `implode`
7. Дописать ` ORDER BY id DESC`.
8. Вернуть массив из текста и значений.

На что обратить внимание:

- Условия копятся в массиве и склеиваются в самом конце через `implode(' AND ', …)`. Вопрос «ставить ли `AND` перед этим условием» исчезает — как и трюк `WHERE 1=1` из старого кода, придуманный ровно ради этого.
- Условие и его значение добавляются **одновременно** — тогда порядок знаков `?` и порядок значений совпадут сами.
- Проверка «задан» повторяется трижды: ключ есть и значение не пустая строка. Это кандидат в маленькую функцию.
- `empty()` для этой проверки не годится: `empty('0')` — истина, и фильтр «сумма от 0» молча пропадёт.
- Знаки процента для `LIKE` — часть **значения**, а не текста запроса: в запросе остаётся `customer LIKE ?`.
- В старом коде обращение `$_GET['status']` к отсутствующему ключу давало замечание. `isset` в новой функции убирает и его.
- Текст запроса теперь состоит только из строк, написанных в коде. Что бы ни прислал пользователь, оно попадёт в `params`.
MD,
        ],
        [
            'id' => 'query',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'php',
            'php' => '7',
            'title' => 'Фильтр заказов без склейки: код',
            'text' => "Напиши `ordersQuery(array \$filters)` по своему плану. Функция возвращает `array('sql' => …, 'params' => …)`.\n\n| Фильтр | Условие | Значение |\n|---|---|---|\n| `status` | `status = ?` | как есть |\n| `customer` | `customer LIKE ?` | в знаках процента: `%Анна%` |\n| `min_total` | `total >= ?` | целым числом |\n\n- Условия идут в этом порядке и соединяются через ` AND `.\n- Фильтр пропускается, если ключа нет или значение — пустая строка.\n- Запрос начинается с `SELECT * FROM orders` и заканчивается ` ORDER BY id DESC`.\n\nКод пойдёт на сервер с PHP 7.0 — задание проверяется на нём.",
            'starter' => <<<'PHP'
<?php

PHP,
            'tests' => <<<'PHP'
test('без фильтров — без WHERE', function () {
    assert_eq(array('sql' => 'SELECT * FROM orders ORDER BY id DESC', 'params' => array()), ordersQuery(array()));
});
test('только статус', function () {
    assert_eq(array('sql' => 'SELECT * FROM orders WHERE status = ? ORDER BY id DESC', 'params' => array('new')), ordersQuery(array('status' => 'new')));
});
test('только клиент: проценты в значении, а не в запросе', function () {
    assert_eq(array('sql' => 'SELECT * FROM orders WHERE customer LIKE ? ORDER BY id DESC', 'params' => array('%Анна%')), ordersQuery(array('customer' => 'Анна')));
});
test('минимальная сумма становится целым числом', function () {
    assert_eq(array('sql' => 'SELECT * FROM orders WHERE total >= ? ORDER BY id DESC', 'params' => array(500)), ordersQuery(array('min_total' => '500')));
});
test('все три фильтра', function () {
    assert_eq(
        array('sql' => 'SELECT * FROM orders WHERE status = ? AND customer LIKE ? AND total >= ? ORDER BY id DESC', 'params' => array('paid', '%Борис%', 1000)),
        ordersQuery(array('status' => 'paid', 'customer' => 'Борис', 'min_total' => '1000'))
    );
});
test('порядок условий не зависит от порядка ключей', function () {
    assert_eq(
        array('sql' => 'SELECT * FROM orders WHERE status = ? AND total >= ? ORDER BY id DESC', 'params' => array('new', 500)),
        ordersQuery(array('min_total' => '500', 'status' => 'new'))
    );
});
test('пустые строки пропускаются', function () {
    assert_eq(
        array('sql' => 'SELECT * FROM orders WHERE customer LIKE ? ORDER BY id DESC', 'params' => array('%Вера%')),
        ordersQuery(array('status' => '', 'customer' => 'Вера', 'min_total' => ''))
    );
});
test('сумма от 0 — тоже фильтр', function () {
    assert_eq(array('sql' => 'SELECT * FROM orders WHERE total >= ? ORDER BY id DESC', 'params' => array(0)), ordersQuery(array('min_total' => '0')));
});
test('попытка дописать запрос остаётся значением', function () {
    $result = ordersQuery(array('status' => "' OR '1'='1", 'min_total' => '1; DROP TABLE orders'));
    assert_eq('SELECT * FROM orders WHERE status = ? AND total >= ? ORDER BY id DESC', $result['sql']);
    assert_eq(array("' OR '1'='1", 1), $result['params']);
});
test('посторонние ключи не мешают', function () {
    assert_eq(array('sql' => 'SELECT * FROM orders ORDER BY id DESC', 'params' => array()), ordersQuery(array('page' => '2')));
});
PHP,
            'hints' => [
                'Проверка «фильтр задан»: `isset($filters[\'status\']) && $filters[\'status\'] !== \'\'`. Чтобы не писать её трижды, вынеси в функцию `hasFilter(array $filters, $key)`.',
                'Каждый фильтр: `$where[] = \'status = ?\'; $params[] = $filters[\'status\'];` Для клиента значение — `\'%\' . $filters[\'customer\'] . \'%\'`, для суммы — `(int) $filters[\'min_total\']`.',
                'Сборка: `$sql = \'SELECT * FROM orders\'; if (count($where) > 0) { $sql .= \' WHERE \' . implode(\' AND \', $where); } $sql .= \' ORDER BY id DESC\';`',
            ],
            'solution' => <<<'PHP'
<?php

function hasFilter(array $filters, $key)
{
    return isset($filters[$key]) && $filters[$key] !== '';
}

function ordersQuery(array $filters)
{
    $where = array();
    $params = array();

    if (hasFilter($filters, 'status')) {
        $where[] = 'status = ?';
        $params[] = $filters['status'];
    }
    if (hasFilter($filters, 'customer')) {
        $where[] = 'customer LIKE ?';
        $params[] = '%' . $filters['customer'] . '%';
    }
    if (hasFilter($filters, 'min_total')) {
        $where[] = 'total >= ?';
        $params[] = (int) $filters['min_total'];
    }

    $sql = 'SELECT * FROM orders';
    if (count($where) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY id DESC';

    return array('sql' => $sql, 'params' => $params);
}
PHP,
        ],
    ],
    'cards' => [
        ['Видит ли функция в PHP переменные, объявленные снаружи?', 'Нет. Внутри функции это новая пустая переменная. В старом коде до внешней дотягиваются через `global $x;`, в новом — передают параметром.'],
        ['Чем опасен `global`?', 'Зависимость не видна в сигнатуре функции, а значение может изменить любая другая функция — результат начинает зависеть от порядка вызовов.'],
        ['В коде используется переменная, которой нигде не присваивают значение. Где искать?', 'В подключённых через `include` файлах (поиск по `$имя =`), в `extract(…)` выше по коду, в `global`. Помогают `get_included_files()` и `get_defined_vars()`.'],
        ['Как узнать склейку SQL и чем её заменяют?', 'Значение от пользователя вклеено в текст запроса точками или подстановкой в кавычках. Заменяют запросом со знаками `?` и отдельным списком значений; для целых чисел минимальная правка — `(int)`.'],
        ['Как правильно вызвать `htmlspecialchars` в коде для старых версий?', '`htmlspecialchars($text, ENT_QUOTES, \'UTF-8\')`. До PHP 8.1 без флага одинарная кавычка не экранировалась.'],
        ['Сформулируй правило минимальной правки.', 'Зафиксировать текущее поведение, изменить только то, о чём просили, не трогать остальное (формат, имена, стиль), проверить на версии сервера. Найденное по дороге — отдельной задачей.'],
    ],
];
