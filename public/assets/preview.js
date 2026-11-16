// Задания по вёрстке: редактор HTML/CSS, живой предпросмотр и проверка тестами.
//
// Код ученика показывается в <iframe sandbox>: такая рамка изолирована от
// страницы платформы и не может прочитать её данные. Тесты выполняются внутри
// рамки и присылают результаты наружу через postMessage.

/** Собирает HTML-документ для рамки из кода ученика. */
function buildPreviewDoc(task, code, harness) {
  const bootstrap = task.bootstrap;
  return `<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
${bootstrap ? '<link rel="stylesheet" href="/vendor/bootstrap/bootstrap.min.css">' : ''}
</head>
<body>
${code}
${bootstrap ? '<script src="/vendor/bootstrap/bootstrap.bundle.min.js"><\/script>' : ''}
${harness ? `<script>${harness}<\/script>` : ''}
</body>
</html>`;
}

/** Код, который выполняет тесты внутри рамки. Вставляется в конец документа. */
function buildHarness(tests) {
  return `window.addEventListener('load', async () => {
  const pending = [];
  const results = [];
  let fatal = null;

  const test = (name, fn) => pending.push({ name, fn });
  const assert = (condition, message) => { if (!condition) throw new Error(message || 'Условие не выполнено'); };
  const assertEqual = (actual, expected, message) => {
    if (JSON.stringify(actual) !== JSON.stringify(expected)) {
      throw new Error((message ? message + ': ' : '') + 'ожидалось ' + JSON.stringify(expected) + ', получено ' + JSON.stringify(actual));
    }
  };
  const $$ = (selector) => [...document.querySelectorAll(selector)];
  // Элемент по селектору; если его нет — понятная ошибка вместо «null»
  const $ = (target) => {
    if (typeof target !== 'string') return target;
    const found = document.querySelector(target);
    if (!found) throw new Error('На странице нет элемента ' + target);
    return found;
  };
  const css = (target, property) => getComputedStyle($(target)).getPropertyValue(property).trim();
  const px = (target, property) => Math.round(parseFloat(css(target, property)) * 10) / 10;
  const box = (target) => $(target).getBoundingClientRect();
  const text = (target) => $(target).textContent.replace(/\\s+/g, ' ').trim();
  // Попросить страницу платформы изменить ширину рамки и дождаться перерисовки
  const setWidth = (width) => new Promise((resolve) => {
    const onMessage = (event) => {
      if (event.data && event.data.type === 'resized') {
        window.removeEventListener('message', onMessage);
        setTimeout(resolve, 60);
      }
    };
    window.addEventListener('message', onMessage);
    parent.postMessage({ type: 'resize', width }, '*');
  });

  try {
    ${tests}
  } catch (error) {
    fatal = String(error.message || error);
  }
  for (const { name, fn } of pending) {
    try {
      await fn();
      results.push({ name, ok: true });
    } catch (error) {
      results.push({ name, ok: false, message: String(error.message || error) });
    }
  }
  parent.postMessage({ type: 'results', results, fatal }, '*');
});`;
}

function setupHtml(section, task) {
  const body = section.querySelector('.task-body');
  const hints = task.hints || [];
  body.append(el(`
    <div>
      <div class="editor"></div>
      <div class="preview-bar">
        <div class="tabs">
          <button class="tab is-active" data-view="mine">Мой результат</button>
          <button class="tab" data-view="sample">Образец</button>
        </div>
        <span class="spacer"></span>
        <div class="tabs" title="Ширина окна предпросмотра">
          <button class="tab" data-width="360">360</button>
          <button class="tab" data-width="768">768</button>
          <button class="tab is-active" data-width="">Вся ширина</button>
        </div>
      </div>
      <div class="preview-wrap"><iframe class="preview" sandbox="allow-scripts" title="Предпросмотр"></iframe></div>
      <div class="toolbar">
        <button class="btn btn-primary" data-act="check">Проверить <kbd>Ctrl+Enter</kbd></button>
        <span class="spacer"></span>
        ${hints.length ? '<button class="btn btn-quiet" data-act="hint"></button>' : ''}
        <button class="btn btn-quiet" data-act="solution" hidden>Показать решение</button>
        <button class="btn btn-quiet" data-act="reset" title="Вернуть исходный код">Сбросить</button>
      </div>
      <div class="hints"></div>
      <div class="output" hidden></div>
      <div class="solution" hidden></div>
    </div>`));

  const frame = body.querySelector('iframe');
  const wrap = body.querySelector('.preview-wrap');
  const output = body.querySelector('.output');
  const hintBox = body.querySelector('.hints');
  const hintBtn = body.querySelector('[data-act=hint]');
  const solutionBtn = body.querySelector('[data-act=solution]');
  let hintsShown = 0;
  let view = 'mine';
  let width = '';
  let checking = false;

  const editor = makeEditor(body.querySelector('.editor'), 'html', task.saved ?? task.starter, () => check());

  function applyWidth(value) {
    frame.style.width = value ? `${value}px` : '100%';
  }

  function render() {
    if (checking) return;
    frame.srcdoc = buildPreviewDoc(task, view === 'sample' ? task.sample : editor.getValue(), null);
    applyWidth(width);
  }

  let renderTimer;
  let saveTimer;
  editor.on('change', () => {
    clearTimeout(renderTimer);
    renderTimer = setTimeout(render, 400);
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => api('/api/progress', { taskId: task.id, code: editor.getValue() }), 1000);
  });

  function refreshButtons() {
    if (hintBtn) {
      hintBtn.textContent = hintsShown < hints.length
        ? `Подсказка (${hintsShown}/${hints.length})` : 'Подсказки закончились';
      hintBtn.disabled = hintsShown >= hints.length;
    }
    solutionBtn.hidden = task.noSolution || !(task.status === 'done' || task.attempts >= 2 || (hints.length && hintsShown >= hints.length));
  }

  /** Перезагружает рамку с тестами внутри и ждёт от неё результатов. */
  function runTests() {
    return new Promise((resolve) => {
      const finish = (result) => {
        clearTimeout(timer);
        window.removeEventListener('message', onMessage);
        resolve(result);
      };
      const onMessage = (event) => {
        if (event.source !== frame.contentWindow || !event.data) return; // сообщения только от нашей рамки
        if (event.data.type === 'resize') {
          applyWidth(Number(event.data.width) || '');
          void frame.offsetWidth; // заставляем браузер применить новую ширину сразу
          frame.contentWindow.postMessage({ type: 'resized' }, '*');
        }
        if (event.data.type === 'results') {
          finish({
            logs: [],
            results: event.data.results,
            error: event.data.fatal ? { name: 'Ошибка', message: event.data.fatal } : null,
          });
        }
      };
      const timer = setTimeout(() => finish({
        logs: [], results: [],
        error: { name: 'Таймаут', message: 'страница не ответила за 8 секунд.' },
      }), 8000);
      window.addEventListener('message', onMessage);
      applyWidth('');
      frame.srcdoc = buildPreviewDoc(task, editor.getValue(), buildHarness(task.tests));
    });
  }

  async function check() {
    if (checking) return;
    if (/\b___\b/.test(editor.getValue())) {
      output.hidden = false;
      output.innerHTML = '<div class="out-error">В коде остались пропуски <b>___</b>. Замени каждый из них своим кодом.</div>';
      return;
    }
    checking = true;
    output.hidden = false;
    output.innerHTML = '<div class="muted">Проверяю…</div>';
    const result = await runTests();
    checking = false;
    render(); // вернуть обычный предпросмотр и выбранную ширину

    const passed = !result.error && result.results.length > 0 && result.results.every((r) => r.ok);
    output.innerHTML = (passed ? '<div class="out-success">Все тесты пройдены.</div>' : '')
      + renderRun(result, { showTests: true });

    const saved = await api('/api/progress', { taskId: task.id, code: editor.getValue(), attempt: true, passed });
    task.attempts = saved.attempts;
    if (passed) markDone(section, task);
    refreshButtons();
  }

  body.addEventListener('click', async (event) => {
    const tab = event.target.closest('.tab');
    if (tab) {
      tab.parentElement.querySelectorAll('.tab').forEach((t) => t.classList.toggle('is-active', t === tab));
      if (tab.dataset.view) view = tab.dataset.view;
      if (tab.dataset.width !== undefined) width = tab.dataset.width;
      wrap.classList.toggle('is-sample', view === 'sample');
      render();
      return;
    }
    const act = event.target.closest('[data-act]')?.dataset.act;
    if (act === 'check') check();
    if (act === 'hint' && hintsShown < hints.length) {
      const hint = el(`<div class="hint"><b>Подсказка ${hintsShown + 1}.</b> ${hints[hintsShown]}</div>`);
      hintBox.append(hint);
      highlightIn(hint);
      hintsShown++;
      refreshButtons();
    }
    if (act === 'reset' && confirm('Вернуть исходный код задания? Твой текущий код пропадёт.')) {
      editor.setValue(task.starter);
    }
    if (act === 'solution') {
      const box = body.querySelector('.solution');
      if (box.hidden) {
        const { solution } = await api('/api/solution', { taskId: task.id });
        box.innerHTML = '<div class="out-label">Одно из возможных решений</div>'
          + `<pre class="code" data-lang="html">${escapeHtml(solution)}</pre>`;
        highlightIn(box);
      }
      box.hidden = !box.hidden;
      solutionBtn.textContent = box.hidden ? 'Показать решение' : 'Скрыть решение';
    }
  });

  refreshButtons();
  render();
}
