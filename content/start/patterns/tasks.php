<?php
return [
    'tasks' => [
        [
            'id' => 'which-pattern',
            'type' => 'quiz',
            'title' => 'Узнай приём',
            'text' => 'Шаг плана: «найти самый дешёвый товар в корзине». Какой это приём?',
            'options' => ['Накопление', 'Поиск лучшего', 'Отбор', 'Преобразование'],
            'answer' => 1,
            'explain' => 'Нужен один элемент — «самый-самый». Заводим переменную с первым товаром и заменяем её каждый раз, когда встречаем товар дешевле.',
        ],
        [
            'id' => 'sum-fill',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Накопление: сумма',
            'text' => 'Допиши функцию `sum(numbers)`, которая возвращает сумму чисел массива. Для пустого массива сумма равна `0`.',
            'starter' => <<<'JS'
function sum(numbers) {
  let total = ___;

  for (const n of numbers) {
    ___
  }

  return ___;
}

console.log(sum([1, 2, 3]));
JS,
            'tests' => <<<'JS'
test('sum([1, 2, 3]) → 6', () => assertEqual(sum([1, 2, 3]), 6));
test('sum([10]) → 10', () => assertEqual(sum([10]), 10));
test('отрицательные числа', () => assertEqual(sum([5, -8]), -3));
test('пустой массив → 0', () => assertEqual(sum([]), 0));
JS,
            'hints' => [
                'Начальное значение накопителя для суммы — `0`.',
                'Внутри цикла прибавляем текущее число: `total += n;`',
            ],
            'solution' => <<<'JS'
function sum(numbers) {
  let total = 0;

  for (const n of numbers) {
    total += n;
  }

  return total;
}
JS,
        ],
        [
            'id' => 'max-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Поиск лучшего: максимум',
            'text' => 'Функция `findMax` должна возвращать наибольшее число массива. На первый взгляд она работает, но один из тестов падает. Нажми «Проверить», прочитай сообщение и исправь.',
            'starter' => <<<'JS'
function findMax(numbers) {
  let max = 0;

  for (const n of numbers) {
    if (n > max) {
      max = n;
    }
  }

  return max;
}

console.log(findMax([3, 9, 4]));
JS,
            'tests' => <<<'JS'
test('findMax([3, 9, 4]) → 9', () => assertEqual(findMax([3, 9, 4]), 9));
test('максимум в начале', () => assertEqual(findMax([7, 1, 2]), 7));
test('все числа отрицательные', () => assertEqual(findMax([-5, -2, -9]), -2));
test('один элемент', () => assertEqual(findMax([-1]), -1));
JS,
            'hints' => [
                'Посмотри на упавший тест: функция вернула `0`, хотя нуля в массиве нет. Откуда он взялся?',
                'Начальное значение `max = 0` больше любого отрицательного числа, поэтому условие `n > max` ни разу не выполняется. Начинать нужно с первого элемента массива.',
            ],
            'solution' => <<<'JS'
function findMax(numbers) {
  let max = numbers[0];

  for (const n of numbers) {
    if (n > max) {
      max = n;
    }
  }

  return max;
}
JS,
        ],
        [
            'id' => 'even-tests',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Отбор: чётные числа',
            'text' => "Напиши функцию `onlyEven(numbers)`, которая возвращает новый массив только с чётными числами.\n\nЧисло чётное, если остаток от деления на 2 равен нулю: `n % 2 === 0`.",
            'starter' => <<<'JS'
function onlyEven(numbers) {

}

console.log(onlyEven([1, 2, 3, 4]));
JS,
            'tests' => <<<'JS'
test('onlyEven([1, 2, 3, 4]) → [2, 4]', () => assertEqual(onlyEven([1, 2, 3, 4]), [2, 4]));
test('нет чётных → пустой массив', () => assertEqual(onlyEven([1, 3]), []));
test('ноль — чётное число', () => assertEqual(onlyEven([0, 5]), [0]));
test('пустой массив', () => assertEqual(onlyEven([]), []));
test('исходный массив не меняется', () => {
  const source = [1, 2];
  onlyEven(source);
  assertEqual(source, [1, 2]);
});
JS,
            'hints' => [
                'Каркас приёма «отбор»: пустой массив `result` до цикла, `if` внутри цикла, `return result` после.',
                'Положить элемент в массив: `result.push(n)`.',
            ],
            'solution' => <<<'JS'
function onlyEven(numbers) {
  const result = [];

  for (const n of numbers) {
    if (n % 2 === 0) {
      result.push(n);
    }
  }

  return result;
}
JS,
        ],
        [
            'id' => 'orders-plan',
            'type' => 'plan',
            'title' => 'Комбинация приёмов: план',
            'text' => "Есть список позиций заказа. У каждой позиции есть цена `price` и количество `qty`:\n\n```js\n[{ price: 500, qty: 3 }, { price: 200, qty: 1 }, { price: 1200, qty: 1 }]\n```\n\nЗадача: посчитать общую стоимость только тех позиций, стоимость которых (цена × количество) не меньше заданного порога.\n\nДля порога `1000` ответ `2700`: первая позиция стоит 1500, третья 1200, вторая (200) не проходит.\n\nЗапиши шаги и отметь, какие приёмы ты используешь.",
            'reference' => <<<'MD'
1. Завести накопитель суммы, равный нулю. *(накопление)*
2. Пройти по всем позициям.
3. Для текущей позиции посчитать её стоимость: цена × количество.
4. Если стоимость не меньше порога — прибавить её к накопителю. *(отбор)*
5. Вернуть накопитель.

Это отбор и накопление в одном цикле. Шаг 3 стоит вынести в отдельную переменную: стоимость нужна дважды — в условии и в прибавлении.
MD,
        ],
        [
            'id' => 'orders-scratch',
            'type' => 'code',
            'kind' => 'scratch',
            'lang' => 'js',
            'title' => 'Комбинация приёмов: код',
            'text' => "Напиши функцию `totalAbove(items, threshold)` по своему плану.\n\n`items` — массив объектов с полями `price` и `qty`, `threshold` — порог. К полям объекта обращаются через точку: `item.price`.",
            'starter' => "",
            'tests' => <<<'JS'
const items = [{ price: 500, qty: 3 }, { price: 200, qty: 1 }, { price: 1200, qty: 1 }];
test('порог 1000 → 2700', () => assertEqual(totalAbove(items, 1000), 2700));
test('порог 0 → все позиции', () => assertEqual(totalAbove(items, 0), 2900));
test('порог ровно равен стоимости позиции', () => assertEqual(totalAbove(items, 1500), 1500));
test('ничего не проходит → 0', () => assertEqual(totalAbove(items, 5000), 0));
test('пустой список → 0', () => assertEqual(totalAbove([], 100), 0));
JS,
            'hints' => [
                'Начни с каркаса накопления: `let total = 0;`, цикл `for (const item of items)`, `return total;`.',
                'Внутри цикла сначала посчитай стоимость позиции: `const cost = item.price * item.qty;`',
                '«Не меньше порога» — это `cost >= threshold`.',
            ],
            'solution' => <<<'JS'
function totalAbove(items, threshold) {
  let total = 0;

  for (const item of items) {
    const cost = item.price * item.qty;
    if (cost >= threshold) {
      total += cost;
    }
  }

  return total;
}
JS,
        ],
    ],
    'cards' => [
        ['Назови четыре типовых приёма работы с набором данных.', 'Накопление (одно значение из всех), поиск лучшего (один «самый-самый» элемент), отбор (часть элементов), преобразование (столько же элементов, но изменённых).'],
        ['С какого значения начинать поиск максимума в массиве и почему не с нуля?', 'С первого элемента массива. Ноль может оказаться больше всех элементов (если они отрицательные), и тогда ответом станет число, которого в массиве нет.'],
        ['Какой общий каркас у всех четырёх приёмов?', 'Переменная до цикла (накопитель, «лучший» или пустой массив) → цикл, который её обновляет → результат после цикла.'],
    ],
];
