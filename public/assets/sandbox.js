// Песочница: свободный запуск JS и PHP. Код хранится в localStorage браузера.

const SAMPLES = {
  js: "const names = ['Аня', 'Борис', 'Вера'];\n\nfor (const name of names) {\n  console.log(`Привет, ${name}!`);\n}\n",
  php8: "<?php\n\n$names = ['Аня', 'Борис', 'Вера'];\n\nforeach ($names as $name) {\n    echo \"Привет, $name!\\n\";\n}\n\necho 'Версия PHP: ' . PHP_VERSION . \"\\n\";\n",
};
SAMPLES.php7 = SAMPLES.php8;
SAMPLES.both = "<?php\n\n// Один код — две версии: вывод PHP 8.3 и PHP 7.0 появится рядом.\n\nvar_dump(0 == 'кофе');\nvar_dump(in_array('кофе', [0, 1, 2]));\n\necho 'Итого: ' . 2 + 3, \"\\n\";\n";

const langSelect = document.getElementById('sb-lang');
const output = document.getElementById('sb-out');

function load(key) {
  try { return localStorage.getItem('sandbox:' + key); } catch (e) { return null; }
}
function store(key, value) {
  try { localStorage.setItem('sandbox:' + key, value); } catch (e) { /* хранилище недоступно — не страшно */ }
}

const saved = load('lang');
if (saved && !langSelect.querySelector(`option[value="${saved}"]`)?.disabled) langSelect.value = saved;

const editor = makeEditor(document.getElementById('sb-editor'), 'js', '', run);

function switchLang() {
  const lang = langSelect.value;
  editor.setOption('mode', cmMode(lang === 'js' ? 'js' : 'php', '<?php'));
  editor.setValue(load('code:' + lang) ?? SAMPLES[lang]);
  store('lang', lang);
}

async function run() {
  const lang = langSelect.value;
  const code = editor.getValue();
  output.hidden = false;
  output.innerHTML = '<div class="muted">Выполняю…</div>';
  if (lang === 'both') {
    output.innerHTML = renderCompare(await runPhpBoth(code), { showTests: false });
    return;
  }
  const result = lang === 'js' ? await runJs(code, '') : await runPhp(code, lang === 'php7' ? '7' : '8');
  output.innerHTML = renderRun(result, { showTests: false })
    + (result.version ? `<div class="muted">PHP ${escapeHtml(result.version)}</div>` : '');
}

editor.on('change', () => store('code:' + langSelect.value, editor.getValue()));
langSelect.addEventListener('change', switchLang);
document.getElementById('sb-run').addEventListener('click', run);
switchLang();
