// Проверка содержимого курса: у каждого JS-задания эталонное решение должно
// проходить все тесты, а исходная заготовка — не проходить.
// Запуск из корня проекта: node tools/verify.js

const { execFileSync } = require('node:child_process');
const path = require('node:path');
const { runSuite } = require('../public/assets/runner-worker.js');

const root = path.dirname(__dirname);
const tasks = JSON.parse(execFileSync('php', [path.join(root, 'tools/dump-tasks.php')], { maxBuffer: 1e8 }));

// Заготовки заданий «Починить» могут оставлять необработанные отклонённые промисы.
// В браузере это только сообщение в консоли, а Node от такого завершается.
process.on('unhandledRejection', () => {});

const allPassed = (run) => !run.error && run.results.length > 0 && run.results.every((r) => r.ok);

(async () => {
  let problems = 0;
  let checked = 0;
  const ids = new Set();

  for (const task of tasks) {
    if (ids.has(task.id)) { console.log(`✗ ${task.id}: повторяющийся id`); problems++; }
    ids.add(task.id);

    if (task.type === 'quiz' && !(task.answer >= 0 && task.answer < task.options.length)) {
      console.log(`✗ ${task.id}: неверный номер ответа`); problems++;
    }
    if (task.type === 'plan' && !task.reference) { console.log(`✗ ${task.id}: нет разбора`); problems++; }
    if (task.type !== 'code' || task.lang !== 'js') continue;
    checked++;

    const solved = await runSuite(task.solution, task.tests);
    if (!allPassed(solved)) {
      problems++;
      console.log(`✗ ${task.id}: решение не проходит тесты`);
      if (solved.error) console.log(`    ${solved.error.name}: ${solved.error.message}`);
      solved.results.filter((r) => !r.ok).forEach((r) => console.log(`    ${r.name}: ${r.message}`));
    }

    // заготовка с бесконечным циклом здесь повесила бы проверку — такие задания помечены 'hangs'
    if (!task.hangs && allPassed(await runSuite(task.starter, task.tests))) {
      problems++;
      console.log(`✗ ${task.id}: заготовка уже проходит все тесты`);
    }
  }

  console.log(problems ? `\nПроблем: ${problems}` : `Всё в порядке: заданий ${tasks.length}, из них с кодом на JS ${checked}.`);
  process.exit(problems ? 1 : 0);
})();
