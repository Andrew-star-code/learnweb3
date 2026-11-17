// Страница урока: оживляет задания трёх типов — код, вопрос и план.

const tasks = JSON.parse(document.getElementById('tasks-data').textContent);

function el(html) {
  const t = document.createElement('template');
  t.innerHTML = html.trim();
  return t.content.firstElementChild;
}

function markDone(section, task) {
  task.status = 'done';
  section.classList.add('is-done');
  if (tasks.every((t) => t.status === 'done')) {
    document.querySelector('.lesson-nav').classList.add('all-done');
  }
}

// ---------- Задание с кодом ----------

function setupCode(section, task) {
  const body = section.querySelector('.task-body');
  const hints = task.hints || [];
  // задания блока «Легаси» проверяются на PHP 7.0 или сразу на двух версиях
  const needs7 = task.lang === 'php' && (task.php === '7' || task.php === 'both');
  const checkedOn = !needs7 ? ''
    : (task.php === 'both' ? 'Проверка: PHP 8.3 и 7.0' : 'Проверка: PHP 7.0')
      + (php7Ready() ? '' : ' — нужен запуск через docker compose');
  body.append(el(`
    <div>
      <div class="editor"></div>
      <div class="toolbar">
        <button class="btn" data-act="run">▶ Запустить</button>
        ${task.lang === 'php' ? `<select class="php-version" title="На какой версии PHP запустить код. Проверка тестами всегда идёт на версии задания.">
          <option value="8">PHP 8.3</option>
          <option value="7"${php7Ready() ? '' : ' disabled'}>PHP 7.0</option>
          <option value="both"${php7Ready() ? '' : ' disabled'}>Обе версии рядом</option>
        </select>` : ''}
        <button class="btn btn-primary" data-act="check">Проверить <kbd>Ctrl+Enter</kbd></button>
        ${checkedOn ? `<span class="muted check-on">${checkedOn}</span>` : ''}
        <span class="spacer"></span>
        ${hints.length ? '<button class="btn btn-quiet" data-act="hint"></button>' : ''}
        <button class="btn btn-quiet" data-act="solution" hidden>Показать решение</button>
        <button class="btn btn-quiet" data-act="reset" title="Вернуть исходный код">Сбросить</button>
      </div>
      <div class="hints"></div>
      <div class="output" hidden></div>
      <div class="solution" hidden></div>
    </div>`));

  const output = body.querySelector('.output');
  const hintBox = body.querySelector('.hints');
  const hintBtn = body.querySelector('[data-act=hint]');
  const solutionBtn = body.querySelector('[data-act=solution]');
  let hintsShown = 0;

  // версия для кнопки «Запустить» по умолчанию та же, на которой задание проверяется
  const versionSelect = body.querySelector('.php-version');
  if (needs7 && php7Ready()) versionSelect.value = task.php;

  const editor = makeEditor(body.querySelector('.editor'), task.lang, task.saved ?? task.starter, () => check());

  // черновик сохраняется сам через секунду после последнего нажатия
  let saveTimer;
  editor.on('change', () => {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => api('/api/progress', { taskId: task.id, code: editor.getValue() }), 1000);
  });

  function refreshButtons() {
    if (hintBtn) {
      hintBtn.textContent = hintsShown < hints.length
        ? `Подсказка (${hintsShown}/${hints.length})` : 'Подсказки закончились';
      hintBtn.disabled = hintsShown >= hints.length;
    }
    // решение открывается после двух честных попыток, всех подсказок или уже решённого задания
    // у проектов готового решения нет — только чек-лист из тестов
    solutionBtn.hidden = task.noSolution || !(task.status === 'done' || task.attempts >= 2 || (hints.length && hintsShown >= hints.length));
  }

  // В заданиях «Дописать» пропуски обозначены ___ — напоминаем, если они остались
  function hasBlanks() {
    if (!/\b___\b/.test(editor.getValue())) return false;
    output.hidden = false;
    output.innerHTML = '<div class="out-error">В коде остались пропуски <b>___</b>. Замени каждый из них своим кодом.</div>';
    return true;
  }

  async function run() {
    if (hasBlanks()) return;
    const code = editor.getValue();
    output.hidden = false;
    output.innerHTML = '<div class="muted">Выполняю…</div>';
    if (task.lang !== 'php') {
      output.innerHTML = renderRun(await runJs(code, ''), { showTests: false });
      return;
    }
    // «Запустить» — на выбранной версии; «Проверить» — на версии, заданной в задании
    const version = versionSelect.value;
    output.innerHTML = version === 'both'
      ? renderCompare(await runPhpBoth(code), { showTests: false })
      : renderRun(await runPhp(code, version), { showTests: false });
  }

  async function check() {
    if (hasBlanks()) return;
    const code = editor.getValue();
    output.hidden = false;
    output.innerHTML = '<div class="muted">Проверяю…</div>';
    let passed;
    let html;
    if (task.lang === 'php' && task.php === 'both') {
      const runs = await runPhpBoth(code, task.id);
      passed = runPassed(runs[8]) && runPassed(runs[7]);
      html = renderCompare(runs, { showTests: true });
      if (passed) html = '<div class="out-success">Все тесты пройдены на обеих версиях.</div>' + html;
    } else {
      const result = task.lang === 'php' ? await runPhp(code, task.php || '8', task.id) : await runJs(code, task.tests);
      passed = runPassed(result);
      html = renderRun(result, { showTests: true });
      if (passed) html = '<div class="out-success">Все тесты пройдены.</div>' + html;
    }
    output.innerHTML = html;

    const saved = await api('/api/progress', { taskId: task.id, code, attempt: true, passed });
    task.attempts = saved.attempts;
    if (passed) markDone(section, task);
    refreshButtons();
  }

  body.addEventListener('click', async (event) => {
    const act = event.target.closest('[data-act]')?.dataset.act;
    if (act === 'run') run();
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
          + `<pre class="code" data-lang="${task.lang}">${escapeHtml(solution)}</pre>`;
        highlightIn(box);
      }
      box.hidden = !box.hidden;
      solutionBtn.textContent = box.hidden ? 'Показать решение' : 'Скрыть решение';
    }
  });

  refreshButtons();
}

// ---------- Вопрос с вариантами ----------

function setupQuiz(section, task) {
  const body = section.querySelector('.task-body');
  const list = el('<div class="options"></div>');
  const explain = el(`<div class="hint" hidden>${task.explain || ''}</div>`);
  task.options.forEach((text, index) => {
    const btn = el(`<button class="option"></button>`);
    btn.textContent = text;
    btn.addEventListener('click', async () => {
      const correct = index === task.answer;
      btn.classList.add(correct ? 'correct' : 'wrong');
      btn.disabled = true;
      if (correct) {
        list.querySelectorAll('.option').forEach((b) => { b.disabled = true; });
        explain.hidden = false;
        highlightIn(explain);
        markDone(section, task);
      }
      api('/api/progress', { taskId: task.id, attempt: true, passed: correct });
    });
    list.append(btn);
  });
  body.append(list, explain);
  if (task.status === 'done') {
    list.querySelectorAll('.option').forEach((b, i) => {
      b.disabled = true;
      if (i === task.answer) b.classList.add('correct');
    });
    explain.hidden = false;
  }
}

// ---------- План решения (декомпозиция) ----------

function setupPlan(section, task) {
  const body = section.querySelector('.task-body');
  body.append(el(`
    <div>
      <textarea class="plan" rows="7" placeholder="1. …&#10;2. …&#10;3. …"></textarea>
      <div class="toolbar">
        <button class="btn btn-primary" data-act="compare" disabled>Сравнить с разбором</button>
        <span class="muted plan-note">Напиши хотя бы три шага — обычными словами, без кода.</span>
      </div>
      <div class="reference" hidden></div>
    </div>`));
  const area = body.querySelector('textarea');
  const button = body.querySelector('[data-act=compare]');
  const reference = body.querySelector('.reference');
  area.value = task.saved || '';

  const steps = () => area.value.split('\n').filter((line) => line.trim().length > 3).length;
  const refresh = () => { button.disabled = steps() < 3; };

  let saveTimer;
  area.addEventListener('input', () => {
    refresh();
    clearTimeout(saveTimer);
    saveTimer = setTimeout(() => api('/api/progress', { taskId: task.id, code: area.value }), 1000);
  });

  async function reveal() {
    const { html } = await api('/api/solution', { taskId: task.id });
    reference.innerHTML = '<div class="out-label">Разбор</div>' + html
      + '<p class="muted">Твой план не обязан совпадать дословно. Проверь главное: нет ли шага, который ты пропустил, и можно ли каждый твой шаг сразу записать кодом.</p>';
    highlightIn(reference);
    reference.hidden = false;
    body.querySelector('.plan-note').hidden = true;
  }

  button.addEventListener('click', async () => {
    await api('/api/progress', { taskId: task.id, code: area.value, attempt: true, passed: true });
    markDone(section, task);
    reveal();
  });

  refresh();
  if (task.status === 'done') reveal();
}

document.querySelectorAll('.task').forEach((section) => {
  const task = tasks[Number(section.dataset.task)];
  if (task.type === 'code' && task.lang === 'html') setupHtml(section, task); // см. preview.js
  else if (task.type === 'code') setupCode(section, task);
  if (task.type === 'quiz') setupQuiz(section, task);
  if (task.type === 'plan') setupPlan(section, task);
});
