// Исполнитель JS-заданий. Работает в Web Worker — отдельном потоке, который
// можно принудительно остановить, если код ученика ушёл в бесконечный цикл.
// Этот же файл подключает tools/verify.js в Node, чтобы проверять решения.

function format(value, depth = 0) {
  if (typeof value === 'string') return depth === 0 ? value : JSON.stringify(value);
  if (typeof value === 'function') return `[Function ${value.name || 'anonymous'}]`;
  if (typeof value === 'bigint') return `${value}n`;
  if (typeof value === 'symbol') return value.toString();
  if (value === null || typeof value !== 'object') return String(value);
  if (value instanceof Error) return `${value.name}: ${value.message}`;
  if (depth > 3) return Array.isArray(value) ? '[…]' : '{…}';
  if (Array.isArray(value)) return '[' + value.map((v) => format(v, depth + 1)).join(', ') + ']';
  if (value instanceof Map) return 'Map ' + format(Object.fromEntries(value), depth);
  if (value instanceof Set) return 'Set ' + format([...value], depth);
  const body = Object.keys(value).map((k) => `${k}: ${format(value[k], depth + 1)}`).join(', ');
  const name = value.constructor && value.constructor.name !== 'Object' ? value.constructor.name + ' ' : '';
  return name + (body ? `{ ${body} }` : '{}');
}

function deepEqual(a, b) {
  if (Object.is(a, b)) return true;
  if (typeof a !== 'object' || typeof b !== 'object' || a === null || b === null) return false;
  if (Array.isArray(a) !== Array.isArray(b)) return false;
  const ka = Object.keys(a);
  const kb = Object.keys(b);
  return ka.length === kb.length && ka.every((k) => deepEqual(a[k], b[k]));
}

// Номер строки в коде ученика, на которой возникла ошибка (если его удаётся достать из стека)
function errorLine(err, codeLines) {
  const m = /(?:<anonymous>|Function):(\d+):\d+/.exec(err && err.stack ? err.stack : '');
  if (!m) return null;
  const line = Number(m[1]) - 2; // new Function добавляет две строки заголовка
  return line >= 1 && line <= codeLines ? line : null;
}

async function runSuite(code, tests) {
  const logs = [];
  const results = [];
  const pending = [];
  const fakeConsole = {};
  for (const method of ['log', 'info', 'warn', 'error', 'debug']) {
    fakeConsole[method] = (...args) => {
      if (logs.length < 500) logs.push(args.map((a) => format(a)).join(' '));
    };
  }
  fakeConsole.table = fakeConsole.log;

  const api = {
    test(name, fn) { pending.push({ name, fn }); },
    assert(condition, message) {
      if (!condition) throw new Error(message || 'Условие не выполнено');
    },
    assertEqual(actual, expected, message) {
      if (!deepEqual(actual, expected)) {
        throw new Error(`${message ? message + ': ' : ''}ожидалось ${format(expected, 1)}, получено ${format(actual, 1)}`);
      }
    },
    assertThrows(fn, message) {
      try { fn(); } catch (e) { return; }
      throw new Error(message || 'Ожидалась ошибка, но код выполнился без неё');
    },
  };

  const codeLines = code.split('\n').length;
  try {
    // Код ученика и тесты выполняются в одной функции, поэтому тесты видят
    // всё, что объявлено в коде: function, const, let, class.
    const run = new Function('test', 'assert', 'assertEqual', 'assertThrows', 'console', 'logs',
      `'use strict'; ${code}\n;\n${tests || ''}`);
    run(api.test, api.assert, api.assertEqual, api.assertThrows, fakeConsole, logs);
  } catch (err) {
    return { logs, results, error: { name: err.name, message: err.message, line: errorLine(err, codeLines) } };
  }

  for (const { name, fn } of pending) {
    try {
      await fn();
      results.push({ name, ok: true });
    } catch (err) {
      const message = err instanceof ReferenceError || err instanceof TypeError
        ? `${err.name}: ${err.message}` : err.message;
      results.push({ name, ok: false, message });
    }
  }
  return { logs, results, error: null };
}

if (typeof module !== 'undefined') {
  module.exports = { runSuite };
} else {
  self.onmessage = async (event) => {
    self.postMessage(await runSuite(event.data.code, event.data.tests));
  };
}
