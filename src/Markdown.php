<?php
declare(strict_types=1);

namespace App;

/**
 * Небольшой рендерер Markdown — ровно то подмножество, которым написаны уроки:
 * заголовки, абзацы, списки, таблицы, цитаты-врезки, блоки кода,
 * а внутри строки — `код`, **жирный**, *курсив* и [ссылки](url).
 */
final class Markdown
{
    public static function render(string $src): string
    {
        $lines = explode("\n", str_replace("\r\n", "\n", trim($src, "\n")));
        return self::blocks($lines);
    }

    /** @param string[] $lines */
    private static function blocks(array $lines): string
    {
        $html = '';
        $n = count($lines);
        $i = 0;
        while ($i < $n) {
            $line = $lines[$i];

            if (trim($line) === '') {
                $i++;
                continue;
            }

            // ```lang ... ```; пометка «```php versions» добавляет к примеру кнопку
            // «запустить на PHP 8.3 и 7.0» (см. setupExamples в core.js)
            if (preg_match('/^```(\w*)(?:\s+(\w+))?\s*$/', $line, $m)) {
                $code = [];
                $i++;
                while ($i < $n && !preg_match('/^```\s*$/', $lines[$i])) {
                    $code[] = $lines[$i++];
                }
                $i++;
                $lang = $m[1] !== '' ? $m[1] : 'text';
                $run = ($m[2] ?? '') === 'versions' && $lang === 'php' ? ' data-run="versions"' : '';
                $html .= '<pre class="code" data-lang="' . self::esc($lang) . '"' . $run . '><code>'
                    . self::esc(implode("\n", $code)) . "</code></pre>\n";
                continue;
            }

            if (preg_match('/^(#{1,4})\s+(.*)$/', $line, $m)) {
                $level = strlen($m[1]);
                $html .= "<h$level>" . self::inline($m[2]) . "</h$level>\n";
                $i++;
                continue;
            }

            if (preg_match('/^-{3,}\s*$/', $line)) {
                $html .= "<hr>\n";
                $i++;
                continue;
            }

            // > врезка (внутри может быть любой Markdown)
            if (preg_match('/^>\s?/', $line)) {
                $inner = [];
                while ($i < $n && preg_match('/^>\s?(.*)$/', $lines[$i], $m)) {
                    $inner[] = $m[1];
                    $i++;
                }
                $html .= '<blockquote>' . self::blocks($inner) . "</blockquote>\n";
                continue;
            }

            // таблица: строка с | и под ней разделитель |---|
            if ($line[0] === '|' && isset($lines[$i + 1]) && preg_match('/^\|[\s:|-]+\|\s*$/', $lines[$i + 1])) {
                $html .= '<div class="table-wrap"><table><thead><tr>';
                foreach (self::cells($line) as $cell) {
                    $html .= '<th>' . self::inline($cell) . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                $i += 2;
                while ($i < $n && isset($lines[$i][0]) && $lines[$i][0] === '|') {
                    $html .= '<tr>';
                    foreach (self::cells($lines[$i]) as $cell) {
                        $html .= '<td>' . self::inline($cell) . '</td>';
                    }
                    $html .= '</tr>';
                    $i++;
                }
                $html .= "</tbody></table></div>\n";
                continue;
            }

            // списки: "- пункт" или "1. пункт"; строка с отступом продолжает пункт
            if (preg_match('/^([-*]|\d+\.)\s+/', $line, $m)) {
                $tag = ctype_digit($m[1][0]) ? 'ol' : 'ul';
                $items = [];
                while ($i < $n) {
                    if (preg_match('/^([-*]|\d+\.)\s+(.*)$/', $lines[$i], $mm)) {
                        $items[] = $mm[2];
                    } elseif (preg_match('/^\s{2,}(\S.*)$/', $lines[$i], $mm) && $items) {
                        $items[count($items) - 1] .= ' ' . $mm[1];
                    } else {
                        break;
                    }
                    $i++;
                }
                $html .= "<$tag>";
                foreach ($items as $item) {
                    $html .= '<li>' . self::inline($item) . '</li>';
                }
                $html .= "</$tag>\n";
                continue;
            }

            // обычный абзац: до пустой строки или начала другого блока
            $para = [];
            while ($i < $n && trim($lines[$i]) !== ''
                && !preg_match('/^(```|#{1,4}\s|>\s?|\||([-*]|\d+\.)\s+)/', $lines[$i])) {
                $para[] = trim($lines[$i++]);
            }
            if (!$para) { // строка похожа на начало блока, но им не является
                $para[] = trim($lines[$i++]);
            }
            $html .= '<p>' . self::inline(implode(' ', $para)) . "</p>\n";
        }
        return $html;
    }

    /** @return string[] */
    private static function cells(string $row): array
    {
        // \| внутри ячейки — это сам символ |, а не граница
        $row = str_replace('\\|', "\x01", trim(trim($row), '|'));
        return array_map(function ($cell) {
            return str_replace("\x01", '|', trim($cell));
        }, explode('|', $row));
    }

    public static function inline(string $text): string
    {
        // 1. Прячем `код`, чтобы звёздочки и скобки внутри него не трогались
        $codes = [];
        //    (двойные обратные кавычки нужны, когда обратная кавычка есть в самом коде)
        $text = preg_replace_callback('/``\s?(.+?)\s?``|`([^`]+)`/', function ($m) use (&$codes) {
            $codes[] = '<code>' . self::esc($m[2] ?? $m[1]) . '</code>';
            return "\x00" . (count($codes) - 1) . "\x00";
        }, $text);

        // 2. Экранируем HTML, затем размечаем
        $text = self::esc($text);
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)\s]+)\)/', function ($m) {
            $external = preg_match('#^https?://#', $m[2]) ? ' target="_blank" rel="noopener"' : '';
            return '<a href="' . $m[2] . '"' . $external . '>' . $m[1] . '</a>';
        }, $text);
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/(?<![\w*])\*([^*\s][^*]*?)\*(?![\w*])/u', '<em>$1</em>', $text);

        // 3. Возвращаем код на место
        return preg_replace_callback('/\x00(\d+)\x00/', function ($m) use ($codes) {
            return $codes[(int) $m[1]];
        }, $text);
    }

    private static function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
