<h1>Песочница</h1>
<p class="muted">Свободный запуск кода без заданий. JavaScript выполняется в браузере, PHP — на сервере платформы.</p>

<div class="toolbar">
    <label>Язык
        <select id="sb-lang">
            <option value="js">JavaScript</option>
            <option value="php8">PHP 8.3</option>
            <option value="php7"<?= $php7 ? '' : ' disabled' ?>>PHP 7.0<?= $php7 ? '' : ' (нужен docker compose)' ?></option>
            <option value="both"<?= $php7 ? '' : ' disabled' ?>>PHP 8.3 и 7.0 рядом<?= $php7 ? '' : ' (нужен docker compose)' ?></option>
        </select>
    </label>
    <button class="btn btn-primary" id="sb-run">▶ Запустить <kbd>Ctrl+Enter</kbd></button>
</div>
<div id="sb-editor" class="editor"></div>
<div id="sb-out" class="output" hidden></div>
