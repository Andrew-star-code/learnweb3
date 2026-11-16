<?php
declare(strict_types=1);

namespace App;

/**
 * Шаблоны — обычные PHP-файлы в templates/. Шаблон страницы рендерится в строку,
 * которая затем вставляется в общий каркас layout.php.
 */
final class View
{
    public static function render(string $template, array $data = []): string
    {
        $content = self::partial($template, $data);
        return self::partial('layout', $data + [
            'content' => $content,
            'csrf' => Security::token(),
            'dueCount' => count(Progress::dueCards()),
        ]);
    }

    public static function partial(string $template, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require ROOT . "/templates/$template.php";
        return ob_get_clean();
    }

    /** Экранирование для вывода в HTML. В шаблонах: <?= e($value) ?> */
    public static function e(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** JSON, который безопасно вставлять внутрь <script>. */
    public static function json($value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }
}
