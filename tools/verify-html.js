// Проверка заданий по вёрстке в настоящем браузере: для каждого задания
// открывает урок, подставляет эталонное решение и нажимает «Проверить».
// Заготовка при этом проходить тесты не должна.
//
// Нужен запущенный сервер платформы и Playwright:
//   node tools/verify-html.js http://localhost:8000

const { execFileSync } = require('node:child_process');
const path = require('node:path');
const { chromium } = require('playwright');

const base = process.argv[2] || 'http://localhost:8000';
const root = path.dirname(__dirname);
const tasks = JSON.parse(execFileSync('php', [path.join(root, 'tools/dump-tasks.php')], { maxBuffer: 1e8 }))
  .filter((task) => task.type === 'code' && task.lang === 'html');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1200, height: 900 } });
  page.on('dialog', (dialog) => dialog.dismiss());
  let problems = 0;
  let opened = null;

  async function check(task, code) {
    const [block, lesson] = task.id.split('/');
    const url = `${base}/lesson/${block}/${lesson}`;
    if (opened !== url) { await page.goto(url); opened = url; }
    const index = await page.evaluate((id) => JSON.parse(document.getElementById('tasks-data').textContent).findIndex((t) => t.id === id), task.id);
    const section = page.locator(`#task-${index + 1}`);
    await section.evaluate((node, value) => node.querySelector('.CodeMirror').CodeMirror.setValue(value), code);
    await section.locator('[data-act=check]').click();
    await section.locator('.output .tests, .output .out-error').first().waitFor({ timeout: 15000 });
    await page.waitForFunction((n) => !document.querySelector(`#task-${n} .output`).textContent.includes('Проверяю'), index + 1);
    return {
      passed: (await section.locator('.out-success').count()) > 0,
      details: await section.locator('.output').innerText(),
    };
  }

  for (const task of tasks) {
    const solved = await check(task, task.solution);
    if (!solved.passed) {
      problems++;
      console.log(`✗ ${task.id}: решение не проходит тесты\n${solved.details.replace(/^/gm, '    ')}`);
    }
    const blank = await check(task, task.starter.replace(/___/g, 'div'));
    if (blank.passed) {
      problems++;
      console.log(`✗ ${task.id}: заготовка уже проходит все тесты`);
    }
  }

  console.log(problems ? `\nПроблем: ${problems}` : `Всё в порядке: заданий по вёрстке ${tasks.length}.`);
  await browser.close();
  process.exit(problems ? 1 : 0);
})();
