<?php
return [
    'tasks' => [
        [
            'id' => 'order-quiz',
            'type' => 'quiz',
            'title' => 'Порядок вывода',
            'text' => "```js\nconsole.log('A');\nsetTimeout(() => console.log('B'), 0);\nconsole.log('C');\n```\n\nЧто будет выведено?",
            'options' => ['A B C', 'A C B', 'B A C', 'Зависит от скорости компьютера'],
            'answer' => 1,
            'explain' => 'Функция из `setTimeout` встаёт в очередь и выполнится только после того, как текущий код закончится. Нулевая задержка означает «как только освободишься», а не «сейчас».',
        ],
        [
            'id' => 'micro-quiz',
            'type' => 'quiz',
            'title' => 'Таймер против промиса',
            'text' => "```js\nsetTimeout(() => console.log('таймер'), 0);\nPromise.resolve().then(() => console.log('промис'));\nconsole.log('код');\n```\n\nВ каком порядке появятся строки?",
            'options' => ['код, таймер, промис', 'таймер, промис, код', 'код, промис, таймер', 'промис, код, таймер'],
            'answer' => 2,
            'explain' => 'Сначала текущий код. Затем все микрозадачи — обработчики промисов. И только потом одна задача из обычной очереди — таймер.',
        ],
        [
            'id' => 'block-quiz',
            'type' => 'quiz',
            'title' => 'Занятый поток',
            'text' => "```js\nsetTimeout(() => console.log('таймер'), 100);\n\nconst start = Date.now();\nwhile (Date.now() - start < 2000) { }\n\nconsole.log('цикл закончился');\n```\n\nКогда появится слово «таймер»?",
            'options' => [
                'Через 100 мс, посреди работы цикла',
                'Примерно через 2 секунды, после «цикл закончился»',
                'Через 2,1 секунды: время цикла плюс задержка',
                'Никогда',
            ],
            'answer' => 1,
            'explain' => 'Таймер сработал через 100 мс и поставил функцию в очередь, но взять её оттуда некому: поток занят циклом. Она выполнится сразу после того, как текущий код закончится. Задержка таймера — минимум, а не гарантия.',
        ],
        [
            'id' => 'later',
            'type' => 'code',
            'kind' => 'fill',
            'lang' => 'js',
            'title' => 'Отложить на потом',
            'text' => "Функция `buildLog()` возвращает массив `log`. Сразу после вызова в нём должно быть `['начало', 'конец']`, а чуть позже туда же должна добавиться строка `'позже'`.\n\nЗаполни пропуски так, чтобы третья строка добавлялась не сразу, а после завершения текущего кода.",
            'starter' => <<<'JS'
function buildLog() {
  const log = [];

  log.push('начало');

  ___(() => {
    log.push('позже');
  }, ___);

  log.push('конец');

  return log;
}

const log = buildLog();
console.log('сразу:', log);
setTimeout(() => console.log('через 50 мс:', log), 50);
JS,
            'tests' => <<<'JS'
const pause = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
test('сразу после вызова — только начало и конец', () => assertEqual(buildLog(), ['начало', 'конец']));
test('чуть позже добавляется третья строка', async () => {
  const result = buildLog();
  await pause(30);
  assertEqual(result, ['начало', 'конец', 'позже']);
});
JS,
            'hints' => [
                'Отложить выполнение функции — `setTimeout(функция, задержка)`.',
                'Задержки `0` достаточно: функция всё равно выполнится после текущего кода.',
            ],
            'solution' => <<<'JS'
function buildLog() {
  const log = [];

  log.push('начало');

  setTimeout(() => {
    log.push('позже');
  }, 0);

  log.push('конец');

  return log;
}
JS,
        ],
        [
            'id' => 'callback-fix',
            'type' => 'code',
            'kind' => 'fix',
            'lang' => 'js',
            'title' => 'Результат из будущего',
            'text' => "Функция `loadPrice` имитирует запрос к серверу: цена становится известна через 50 мс. Автор попытался вернуть её через `return` и получает `undefined`.\n\nПеределай функцию: она принимает колбэк и вызывает его с ценой `890`, когда та готова.\n\n```js\nloadPrice((price) => console.log(price));   // через 50 мс: 890\n```",
            'starter' => <<<'JS'
function loadPrice() {
  let price;

  setTimeout(() => {
    price = 890;
  }, 50);

  return price;
}

console.log(loadPrice());
JS,
            'tests' => <<<'JS'
const pause = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
test('колбэк получает цену 890', async () => {
  let received = 'колбэк не вызван';
  loadPrice((price) => { received = price; });
  await pause(120);
  assertEqual(received, 890);
});
test('колбэк вызывается не сразу, а позже', () => {
  let called = false;
  loadPrice(() => { called = true; });
  assertEqual(called, false, 'Колбэк вызван синхронно');
});
test('колбэк вызывается ровно один раз', async () => {
  let calls = 0;
  loadPrice(() => { calls++; });
  await pause(120);
  assertEqual(calls, 1);
});
JS,
            'hints' => [
                'Добавь параметр: `function loadPrice(callback)`.',
                'Внутри таймера вместо присваивания вызови `callback(890)`. `return` и переменная `price` больше не нужны.',
            ],
            'solution' => <<<'JS'
function loadPrice(callback) {
  setTimeout(() => {
    callback(890);
  }, 50);
}

loadPrice((price) => console.log(price));
JS,
        ],
        [
            'id' => 'repeat',
            'type' => 'code',
            'kind' => 'tests',
            'lang' => 'js',
            'title' => 'Повторять и остановиться',
            'text' => "Напиши функцию `repeat(times, intervalMs, fn)`. Она вызывает `fn` каждые `intervalMs` миллисекунд, передавая номер вызова начиная с нуля, и останавливается после `times` вызовов.\n\n```js\nrepeat(3, 100, (i) => console.log(i));   // 0, 1, 2 — и всё\n```\n\nИспользуй `setInterval` и не забудь его остановить.",
            'starter' => <<<'JS'
function repeat(times, intervalMs, fn) {

}

repeat(3, 100, (i) => console.log('вызов', i));
JS,
            'tests' => <<<'JS'
const pause = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
test('три вызова с номерами 0, 1, 2', async () => {
  const calls = [];
  repeat(3, 10, (i) => calls.push(i));
  await pause(120);
  assertEqual(calls, [0, 1, 2]);
});
test('после нужного числа вызовов останавливается', async () => {
  let count = 0;
  repeat(2, 10, () => count++);
  await pause(150);
  assertEqual(count, 2, 'Интервал не остановлен');
});
test('первый вызов происходит не сразу', () => {
  let called = false;
  repeat(1, 20, () => { called = true; });
  assertEqual(called, false);
});
test('один вызов', async () => {
  const calls = [];
  repeat(1, 10, (i) => calls.push(i));
  await pause(80);
  assertEqual(calls, [0]);
});
JS,
            'hints' => [
                'Заведи счётчик `let count = 0;` и сохрани идентификатор интервала: `const timer = setInterval(() => { … }, intervalMs);`',
                'Внутри: вызвать `fn(count)`, увеличить счётчик, и если он дошёл до `times` — `clearInterval(timer)`.',
            ],
            'solution' => <<<'JS'
function repeat(times, intervalMs, fn) {
  let count = 0;

  const timer = setInterval(() => {
    fn(count);
    count++;

    if (count >= times) {
      clearInterval(timer);
    }
  }, intervalMs);
}
JS,
        ],
    ],
    'cards' => [
        ['Как JavaScript в один поток успевает ждать сеть и таймеры?', 'Ждёт не он, а браузер. Готовые к выполнению функции встают в очередь, и движок берёт их по одной, когда текущий код закончился.'],
        ['Что означает `setTimeout(fn, 0)`?', '«Выполни, как только освободишься»: функция встанет в очередь и запустится после текущего кода, а не немедленно.'],
        ['Что выполнится раньше — обработчик промиса или таймер с нулевой задержкой?', 'Обработчик промиса. Микрозадачи выполняются сразу после текущего кода, до следующей задачи из очереди.'],
        ['Почему функция с `setTimeout` внутри не может вернуть результат через `return`?', '`return` срабатывает сразу, а значение появится позже. Нужен колбэк, промис или `async`-функция.'],
        ['Что будет, если запустить долгий цикл на странице?', 'Всё замрёт: таймеры, обработчики событий и перерисовка ждут, пока текущий код не закончится.'],
    ],
];
