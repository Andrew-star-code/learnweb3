<?php
return [
    'tasks' => [
        [
            'id' => 'read-typeerror',
            'type' => 'quiz',
            'title' => 'Кто пострадавший',
            'text' => "Код упал с сообщением:\n\n```text\nTypeError: Cannot read properties of undefined (reading 'name')\n    at showUser (app.js:3:20)\n```\n\nВ третьей строке написано `return data.user.name;`. Что оказалось `undefined`?",
            'options' => ['data', 'data.user', 'data.user.name', 'Функция showUser'],
            'answer' => 1,
            'explain' => 'Сообщение говорит: читали свойство `name` у `undefined`. Значит, пусто то, что стоит перед `.name`, — это `data.user`. Сам объект `data` существует: иначе ошибка была бы про `reading \'user\'`.',
        ],
        [
            'id' => 'read-reference',
            'type' => 'quiz',
            'title' => 'Имя, которого нет',
            'text' => "```text\nReferenceError: totl is not defined\n```\n\nКакая причина наиболее вероятна?",
            'options' => [
                'В переменной totl лежит undefined',
                'Опечатка: переменная называется total',
                'Переменная totl объявлена через const и её нельзя менять',
                'Не хватает закрывающей скобки',
            ],
            'answer' => 1,
            'explain' => '`is not defined` означает, что такого имени не существует вообще. Это не то же самое, что значение `undefined`: переменная с `undefined` существует, просто в ней пусто. Чаще всего причина — опечатка.',
        ],
        [
            'id' => 'fix-reference',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Починить по сообщению',
            'text' => 'Функция считает стоимость покупки со скидкой. Нажми «Проверить», прочитай ошибку в тестах и исправь код. В коде две ошибки — они проявятся по очереди.',
            'starter' => <<<'JS'
function finalPrice(price, discountPercent) {
  const discount = price * discountPercent / 100;
  const result = price - discont;
  return reslt;
}

console.log(finalPrice(1000, 15));
JS,
            'tests' => <<<'JS'
test('finalPrice(1000, 15) → 850', () => assertEqual(finalPrice(1000, 15), 850));
test('без скидки', () => assertEqual(finalPrice(300, 0), 300));
test('скидка 100%', () => assertEqual(finalPrice(300, 100), 0));
JS,
            'hints' => [
                'Сообщение `discont is not defined` говорит, что имени `discont` не существует. Сравни его по буквам с именем переменной строкой выше.',
                'После первой правки появится новое сообщение — про другое имя. Это нормально: ошибки чинят по одной.',
            ],
            'solution' => <<<'JS'
function finalPrice(price, discountPercent) {
  const discount = price * discountPercent / 100;
  const result = price - discount;
  return result;
}
JS,
        ],
        [
            'id' => 'fix-undefined',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Свойство у undefined',
            'text' => "Функция `getCity(user)` возвращает город пользователя. Адрес заполнен не у всех: у некоторых пользователей поля `address` нет. Для них функция должна возвращать строку `'не указан'`, а сейчас она падает.",
            'starter' => <<<'JS'
function getCity(user) {
  return user.address.city;
}

console.log(getCity({ name: 'Аня', address: { city: 'Казань' } }));
console.log(getCity({ name: 'Борис' }));
JS,
            'tests' => <<<'JS'
test('город указан', () => assertEqual(getCity({ name: 'Аня', address: { city: 'Казань' } }), 'Казань'));
test('адреса нет → «не указан»', () => assertEqual(getCity({ name: 'Борис' }), 'не указан'));
test('адрес есть, но города в нём нет → «не указан»', () => assertEqual(getCity({ name: 'Вера', address: {} }), 'не указан'));
JS,
            'hints' => [
                'Ошибка `Cannot read properties of undefined (reading \'city\')`: пусто то, что стоит перед `.city`, то есть `user.address`.',
                'Перед обращением к `city` проверь, что адрес есть: `if (user.address === undefined) { return \'не указан\'; }`',
                'Третий тест: адрес есть, но `city` в нём `undefined`. Нужна ещё одна проверка — уже самого города.',
            ],
            'solution' => <<<'JS'
function getCity(user) {
  if (user.address === undefined) {
    return 'не указан';
  }
  if (user.address.city === undefined) {
    return 'не указан';
  }
  return user.address.city;
}

// То же самое короче — эти операторы разберём в блоке «Ядро JavaScript»:
// return user.address?.city ?? 'не указан';
JS,
        ],
        [
            'id' => 'fix-silent',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Ошибка без сообщения',
            'text' => "Функция `average(numbers)` считает среднее арифметическое. Она не падает, но возвращает `NaN`. Сообщения об ошибке нет, так что подсказку придётся добыть самому: добавь `console.log` внутрь цикла и посмотри, что лежит в `numbers[i]` на каждом шаге.\n\nДля пустого массива функция должна возвращать `0`.",
            'starter' => <<<'JS'
function average(numbers) {
  let total = 0;

  for (let i = 0; i <= numbers.length; i++) {
    total += numbers[i];
  }

  return total / numbers.length;
}

console.log(average([2, 4, 6]));
JS,
            'tests' => <<<'JS'
test('average([2, 4, 6]) → 4', () => assertEqual(average([2, 4, 6]), 4));
test('average([10]) → 10', () => assertEqual(average([10]), 10));
test('average([1, 2]) → 1.5', () => assertEqual(average([1, 2]), 1.5));
test('пустой массив → 0', () => assertEqual(average([]), 0));
JS,
            'hints' => [
                'Добавь в цикл `console.log(i, numbers[i]);` и запусти. Сколько раз выполнился цикл для массива из трёх элементов?',
                'В массиве из трёх элементов индексы 0, 1 и 2. Условие `i <= numbers.length` пускает цикл ещё и на индекс 3, где лежит `undefined`. А `число + undefined` даёт `NaN`.',
                'Для пустого массива получается `0 / 0`, это тоже `NaN`. Этот случай надо обработать отдельно в самом начале функции.',
            ],
            'solution' => <<<'JS'
function average(numbers) {
  if (numbers.length === 0) {
    return 0;
  }

  let total = 0;

  for (let i = 0; i < numbers.length; i++) {
    total += numbers[i];
  }

  return total / numbers.length;
}
JS,
        ],
    ],
    'cards' => [
        ['В каком порядке читать сообщение об ошибке?', 'Тип ошибки → текст (что произошло) → место (строка). И только потом смотреть в код.'],
        ['`TypeError: Cannot read properties of undefined (reading \'x\')` — что именно пусто?', 'То, что стоит в коде перед `.x`. В сообщении назван пострадавший (свойство, которое не смогли прочитать), а не виновник.'],
        ['Чем `x is not defined` отличается от значения `undefined`?', '`is not defined` — имени не существует вообще (обычно опечатка). `undefined` — переменная существует, но в ней пусто.'],
        ['Откуда в результате берётся `NaN`?', 'В арифметику попало не число — чаще всего `undefined`: например, при выходе за границу массива или делении `0 / 0`.'],
    ],
];
