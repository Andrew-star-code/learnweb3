// Общие помощники для всех страниц: запросы к API, редактор кода, запуск JS.

const CM_MODES = {
  js: 'javascript',
  json: { name: 'javascript', json: true },
  php: 'application/x-httpd-php',
  html: 'htmlmixed',
  css: 'css',
  sql: 'text/x-sql',
};

function cmMode(lang, code = '') {
  if (lang === 'php' && !code.includes('<?php')) return 'text/x-php';
  return CM_MODES[lang] || null;
}

function escapeHtml(text) {
  return String(text).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

/** POST-запрос к API платформы. Возвращает разобранный JSON. */
async function api(path, body) {
  const response = await fetch(path, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify(body),
  });
  return response.json();
}

/** Редактор кода на месте элемента el. */
function makeEditor(el, lang, value, onRun) {
  return CodeMirror(el, {
    value: value || '',
    mode: cmMode(lang, value || '<?php'),
    lineNumbers: true,
    indentUnit: lang === 'php' ? 4 : 2,
    tabSize: lang === 'php' ? 4 : 2,
    matchBrackets: true,
    autoCloseBrackets: true,
    viewportMargin: Infinity, // редактор растёт по высоте вместе с кодом
    extraKeys: {
      Tab: (cm) => (cm.somethingSelected() ? cm.indentSelection('add') : cm.execCommand('insertSoftTab')),
      'Ctrl-Enter': () => onRun && onRun(),
      'Cmd-Enter': () => onRun && onRun(),
      'Ctrl-/': 'toggleComment',
      'Cmd-/': 'toggleComment',
    },
  });
}

/** Подсветка статичных блоков кода в теории и подсказках. */
function highlightIn(root) {
  root.querySelectorAll('pre.code:not(.cm-s-default)').forEach((pre) => {
    const code = pre.textContent;
    pre.classList.add('cm-s-default');
    CodeMirror.runMode(code, cmMode(pre.dataset.lang, code), pre);
  });
}

/**
 * Выполняет JS-код (и, если переданы, тесты) в отдельном потоке.
 * Если код не завершился за 3 секунды — поток уничтожается.
 */
function runJs(code, tests) {
  return new Promise((resolve) => {
    const worker = new Worker('/assets/runner-worker.js');
    const timer = setTimeout(() => {
      worker.terminate();
      resolve({
        logs: [], results: [],
        error: { name: 'Таймаут', message: 'код выполнялся дольше 3 секунд. Скорее всего, где-то бесконечный цикл.' },
      });
    }, 3000);
    worker.onmessage = (event) => {
      clearTimeout(timer);
      worker.terminate();
      resolve(event.data);
    };
    worker.onerror = (event) => {
      clearTimeout(timer);
      worker.terminate();
      resolve({ logs: [], results: [], error: { name: 'Ошибка', message: event.message } });
    };
    worker.postMessage({ code, tests });
  });
}

/** Приводит ответ PHP-исполнителя к тому же виду, что и результат runJs. */
async function runPhp(code, version, taskId) {
  const r = await api('/api/run-php', { code, version, taskId });
  if (r.error) return { logs: [], results: [], error: { name: 'Ошибка', message: r.error } };
  const out = { logs: r.stdout ? [r.stdout.replace(/^\n+/, '')] : [], results: r.results || [], error: null, version: r.version };
  if (r.timedOut) {
    out.error = { name: 'Таймаут', message: 'код выполнялся дольше 5 секунд. Скорее всего, где-то бесконечный цикл.' };
  } else if (taskId && !r.results) {
    out.error = { name: 'Ошибка', message: 'код завершился раньше, чем дошло до тестов — смотри вывод выше.' };
  }
  return out;
}

/** Есть ли у платформы исполнитель PHP 7.0 (запуск через docker compose). */
function php7Ready() {
  return document.querySelector('meta[name=php7]')?.content === '1';
}

/** Один и тот же код на обеих версиях PHP. Возвращает { 8: результат, 7: результат }. */
async function runPhpBoth(code, taskId) {
  const [modern, legacy] = await Promise.all([runPhp(code, '8', taskId), runPhp(code, '7', taskId)]);
  return { 8: modern, 7: legacy };
}

function runPassed(run) {
  return !run.error && run.results.length > 0 && run.results.every((r) => r.ok);
}

/** Две колонки вывода: слева PHP 8, справа PHP 7 — и пометка, совпал ли результат. */
function renderCompare(runs, { showTests }) {
  const fingerprint = (run) => JSON.stringify([run.logs, run.error && run.error.message, showTests ? run.results : null]);
  const same = fingerprint(runs[8]) === fingerprint(runs[7]);
  const column = (run, fallback) => `<div class="compare-col">
      <div class="compare-head">PHP ${escapeHtml(run.version || fallback)}</div>
      ${renderRun(run, { showTests })}
    </div>`;
  return `<div class="compare-verdict ${same ? 'is-same' : 'is-diff'}">${same
    ? 'Версии ведут себя одинаково.' : 'Версии ведут себя по-разному — сравни колонки.'}</div>
    <div class="compare">${column(runs[8], '8.3')}${column(runs[7], '7.0')}</div>`;
}

/**
 * Примеры из теории с пометкой «```php versions»: под блоком появляется кнопка,
 * которая выполняет его на обеих версиях PHP и показывает вывод рядом.
 */
function setupExamples(root) {
  root.querySelectorAll('pre.code[data-run="versions"]').forEach((pre) => {
    const source = pre.textContent;
    const code = source.includes('<?php') ? source : '<?php\n' + source;
    const box = document.createElement('div');
    box.className = 'example-run';
    box.innerHTML = '<button class="btn" type="button">▶ Запустить на PHP 8.3 и 7.0</button><div class="output" hidden></div>';
    const output = box.querySelector('.output');
    box.querySelector('button').addEventListener('click', async () => {
      output.hidden = false;
      output.innerHTML = '<div class="muted">Выполняю…</div>';
      output.innerHTML = renderCompare(await runPhpBoth(code), { showTests: false });
    });
    pre.after(box);
  });
}

/** HTML для панели вывода: консоль, ошибка, результаты тестов. */
function renderRun(run, { showTests }) {
  let html = '';
  if (run.logs.length) {
    html += `<div class="out-label">Вывод</div><pre class="out-log">${escapeHtml(run.logs.join('\n'))}</pre>`;
  }
  if (run.error) {
    const where = run.error.line ? ` (строка ${run.error.line})` : '';
    html += `<div class="out-error"><b>${escapeHtml(run.error.name)}${where}:</b> ${escapeHtml(run.error.message)}</div>`;
  }
  if (showTests && run.results.length) {
    const passed = run.results.filter((r) => r.ok).length;
    html += `<div class="out-label">Тесты: ${passed} из ${run.results.length}</div><ul class="tests">`;
    for (const r of run.results) {
      html += `<li class="${r.ok ? 'ok' : 'bad'}"><span>${r.ok ? '✓' : '✗'}</span><div>${escapeHtml(r.name)}`
        + (r.ok ? '' : `<small>${escapeHtml(r.message)}</small>`) + '</div></li>';
    }
    html += '</ul>';
  }
  if (!html) html = '<div class="muted">Код выполнился, но ничего не вывел. Добавь console.log или echo, чтобы увидеть результат.</div>';
  return html;
}

document.addEventListener('DOMContentLoaded', () => {
  setupExamples(document); // до подсветки: ей нужен исходный текст блока
  highlightIn(document);
});
