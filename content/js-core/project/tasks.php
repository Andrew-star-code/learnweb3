<?php
return [
    'tasks' => [
        [
            'id' => 'plan',
            'type' => 'plan',
            'title' => 'План проекта',
            'text' => "Перечитай требования выше и запиши план для каждой из четырёх функций: `parseExpense`, `parseExpenses`, `totalByCategory`, `formatReport`.\n\nДля каждой — шаги и то, какими из предыдущих функций она пользуется.",
            'reference' => <<<'MD'
**`parseExpense(line)`**

1. Разбить строку по `;`.
2. Если частей не три — бросить ошибку формата.
3. У каждой части убрать пробелы по краям.
4. Сумму превратить в число.
5. Если получилось не число или оно меньше нуля — бросить ошибку суммы.
6. Вернуть объект из трёх полей.

**`parseExpenses(text)`**

1. Разбить текст на строки.
2. Пройти по строкам с номером.
3. Пустые строки пропустить.
4. Вызвать `parseExpense` внутри `try`. Если она бросила ошибку — бросить новую, добавив в начало текста номер строки.
5. Результат положить в массив, в конце вернуть массив.

**`totalByCategory(expenses)`**

Объект-счётчик: ключ — категория, значение — накопленная сумма.

**`formatReport(expenses)`**

1. Получить суммы по категориям через `totalByCategory`.
2. Превратить объект в массив пар.
3. Отсортировать пары по убыванию суммы.
4. Превратить каждую пару в строку отчёта.
5. Посчитать общий итог и добавить строку «Итого».
6. Склеить строки через перевод строки.

Форматирование суммы (`1650.50 ₽`) нужно в двух местах — это повод вынести его в маленькую пятую функцию.
MD,
        ],
        [
            'id' => 'expenses',
            'type' => 'code',
            'kind' => 'project',
            'lang' => 'js',
            'noSolution' => true,
            'title' => 'Учёт расходов',
            'text' => "Напиши четыре функции из требований. Тесты ниже — чек-лист приёмки: они идут в том же порядке, что и требования, так что по ним видно, сколько уже сделано.\n\nВ заготовке лежат данные для проверки руками.",
            'starter' => <<<'JS'
const sample = `2024-03-01;еда;450
2024-03-01;транспорт;60
2024-03-02;еда;1200.50

2024-03-03;развлечения;800`;

// 1. parseExpense(line)


// 2. parseExpenses(text)


// 3. totalByCategory(expenses)


// 4. formatReport(expenses)


// console.log(formatReport(parseExpenses(sample)));
JS,
            'tests' => <<<'JS'
function messageOf(fn) { try { fn(); } catch (error) { return error instanceof Error ? error.message : 'брошен не объект Error'; } return 'ошибки не было'; }
const text = '2024-03-01;еда;450\n2024-03-01;транспорт;60\n2024-03-02;еда;1200.50\n\n2024-03-03;развлечения;800';

test('1. parseExpense разбирает строку', () => assertEqual(parseExpense('2024-03-01;еда;450'), { date: '2024-03-01', category: 'еда', amount: 450 }));
test('1. amount — число, в том числе дробное', () => assertEqual(parseExpense('2024-03-02;еда;1200.50').amount, 1200.5));
test('1. пробелы вокруг значений убираются', () => assertEqual(parseExpense(' 2024-03-01 ; еда ; 450 '), { date: '2024-03-01', category: 'еда', amount: 450 }));
test('1. не три части → «Неверный формат строки»', () => {
  assertEqual(messageOf(() => parseExpense('2024-03-01;еда')), 'Неверный формат строки');
  assertEqual(messageOf(() => parseExpense('a;b;1;лишнее')), 'Неверный формат строки');
});
test('1. плохая сумма → «Неверная сумма»', () => {
  assertEqual(messageOf(() => parseExpense('2024-03-01;еда;много')), 'Неверная сумма');
  assertEqual(messageOf(() => parseExpense('2024-03-01;еда;-5')), 'Неверная сумма');
});

test('2. parseExpenses разбирает текст, пропуская пустые строки', () => {
  const list = parseExpenses(text);
  assertEqual(list.length, 4);
  assertEqual(list[3], { date: '2024-03-03', category: 'развлечения', amount: 800 });
});
test('2. пустой текст → []', () => assertEqual(parseExpenses(''), []));
test('2. ошибка содержит номер строки', () => {
  assertEqual(messageOf(() => parseExpenses('2024-03-01;еда;450\n\n2024-03-02;еда;много')), 'Строка 3: Неверная сумма');
  assertEqual(messageOf(() => parseExpenses('сломано')), 'Строка 1: Неверный формат строки');
});

test('3. totalByCategory складывает по категориям', () => assertEqual(totalByCategory(parseExpenses(text)), { еда: 1650.5, транспорт: 60, развлечения: 800 }));
test('3. пустой список → {}', () => assertEqual(totalByCategory([]), {}));

test('4. formatReport: категории по убыванию суммы и итог', () => assertEqual(
  formatReport(parseExpenses(text)),
  'еда: 1650.50 ₽\nразвлечения: 800.00 ₽\nтранспорт: 60.00 ₽\nИтого: 2510.50 ₽',
));
test('4. пустой список → только итог', () => assertEqual(formatReport([]), 'Итого: 0.00 ₽'));
test('4. formatReport не меняет исходный массив', () => {
  const list = parseExpenses(text);
  formatReport(list);
  assertEqual(list.map((e) => e.category), ['еда', 'транспорт', 'еда', 'развлечения']);
});
JS,
            'hints' => [
                '**parseExpense.** Разбей строку: `const parts = line.split(\';\');`. Проверь `parts.length !== 3`. Сумма: `Number(parts[2].trim())`, проверка — `Number.isNaN(amount) || amount < 0`.',
                '**parseExpenses.** Цикл `for (let i = 0; i < lines.length; i++)`. Внутри: `` try { result.push(parseExpense(lines[i])); } catch (error) { throw new Error(`Строка ${i + 1}: ${error.message}`); } ``',
                '**totalByCategory.** Тот же приём, что в подсчёте слов, только прибавляется не единица, а сумма: `totals[e.category] = (totals[e.category] ?? 0) + e.amount;`',
                '**formatReport.** `Object.entries(totals)` даёт массив пар `[категория, сумма]`. Сортировка: `.sort((a, b) => b[1] - a[1])`. Строка: `` `${category}: ${sum.toFixed(2)} ₽` ``.',
            ],
            'solution' => <<<'JS'
function parseExpense(line) {
  const parts = line.split(';');
  if (parts.length !== 3) {
    throw new Error('Неверный формат строки');
  }

  const [date, category, rawAmount] = parts.map((part) => part.trim());
  const amount = Number(rawAmount);
  if (rawAmount === '' || Number.isNaN(amount) || amount < 0) {
    throw new Error('Неверная сумма');
  }

  return { date, category, amount };
}

function parseExpenses(text) {
  const result = [];
  const lines = text.split('\n');

  for (let i = 0; i < lines.length; i++) {
    if (lines[i].trim() === '') {
      continue;
    }
    try {
      result.push(parseExpense(lines[i]));
    } catch (error) {
      throw new Error(`Строка ${i + 1}: ${error.message}`);
    }
  }

  return result;
}

function totalByCategory(expenses) {
  const totals = {};
  for (const expense of expenses) {
    totals[expense.category] = (totals[expense.category] ?? 0) + expense.amount;
  }
  return totals;
}

function money(value) {
  return `${value.toFixed(2)} ₽`;
}

function formatReport(expenses) {
  const totals = totalByCategory(expenses);
  const lines = Object.entries(totals)
    .sort((a, b) => b[1] - a[1])
    .map(([category, sum]) => `${category}: ${money(sum)}`);

  const total = expenses.reduce((sum, expense) => sum + expense.amount, 0);
  lines.push(`Итого: ${money(total)}`);

  return lines.join('\n');
}
JS,
        ],
    ],
    'cards' => [
        ['Как добавить к чужой ошибке свой контекст (например, номер строки)?', 'Перехватить и бросить новую: `` catch (error) { throw new Error(`Строка ${n}: ${error.message}`); } ``'],
        ['Как отсортировать объект `{ ключ: число }` по убыванию значений?', '`Object.entries(obj).sort((a, b) => b[1] - a[1])` — получится массив пар `[ключ, значение]` в нужном порядке.'],
    ],
];
