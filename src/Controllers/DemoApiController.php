<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Db;

/**
 * Учебное API для заданий про fetch. К нему обращается код ученика из
 * предпросмотра, поэтому оно открыто для любых источников (CORS) и не требует
 * токена. Это безопасно, пока оно умеет только одно: хранить игрушечные данные.
 *
 * Параметры любого запроса:
 *   ?delay=500  — ответить через 500 мс (не больше 3000)
 *   ?fail=1     — ответить ошибкой 500; ?fail=404 — ошибкой с таким кодом
 */
final class DemoApiController
{
    private const PRODUCTS = [
        ['id' => 1, 'name' => 'Эфиопия Иргачеффе', 'price' => 890, 'inStock' => true],
        ['id' => 2, 'name' => 'Колумбия Супремо', 'price' => 750, 'inStock' => true],
        ['id' => 3, 'name' => 'Бразилия Сантос', 'price' => 620, 'inStock' => true],
        ['id' => 4, 'name' => 'Кения АА', 'price' => 980, 'inStock' => false],
        ['id' => 5, 'name' => 'Гватемала Антигуа', 'price' => 840, 'inStock' => true],
        ['id' => 6, 'name' => 'Руанда Бурбон', 'price' => 910, 'inStock' => true],
        ['id' => 7, 'name' => 'Эфиопия Сидамо', 'price' => 860, 'inStock' => false],
        ['id' => 8, 'name' => 'Колумбия Декаф', 'price' => 790, 'inStock' => true],
    ];

    private const SEED_TODOS = ['Заказать зёрна', 'Проверить кофемолку', 'Обновить меню'];

    public function __construct()
    {
        $this->migrate();
    }

    /** Общая часть каждого запроса: задержка и искусственные ошибки. */
    public function before(): void
    {
        $delay = min(3000, max(0, (int) ($_GET['delay'] ?? 0)));
        if ($delay > 0) {
            usleep($delay * 1000);
        }

        $fail = (int) ($_GET['fail'] ?? 0);
        if ($fail === 0 && $this->takeFailure()) {
            $fail = 500;
        }
        if ($fail !== 0) {
            $this->json(['error' => 'Учебная ошибка сервера'], $fail >= 400 && $fail < 600 ? $fail : 500);
        }
    }

    // ---------- Товары (только чтение) ----------

    public function products(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $items = self::PRODUCTS;
        if ($query !== '') {
            $items = array_values(array_filter($items, function ($product) use ($query) {
                return mb_stripos($product['name'], $query) !== false;
            }));
        }
        $this->json($items);
    }

    public function product(array $params): void
    {
        foreach (self::PRODUCTS as $product) {
            if ($product['id'] === (int) $params['id']) {
                $this->json($product);
            }
        }
        $this->json(['error' => 'Товар не найден'], 404);
    }

    // ---------- Список дел (чтение, создание, удаление) ----------

    public function todos(): void
    {
        $rows = Db::pdo()->query('SELECT id, text, done FROM demo_todos ORDER BY id')->fetchAll();
        $this->json(array_map([$this, 'todoRow'], $rows));
    }

    public function createTodo(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $this->json(['error' => 'Тело запроса должно быть JSON-объектом'], 400);
        }
        $text = trim((string) ($input['text'] ?? ''));
        if ($text === '') {
            $this->json(['error' => 'Поле text обязательно'], 422);
        }

        $pdo = Db::pdo();
        $pdo->prepare('INSERT INTO demo_todos (text, done) VALUES (?, 0)')->execute([$text]);
        $this->json(['id' => (int) $pdo->lastInsertId(), 'text' => $text, 'done' => false], 201);
    }

    public function deleteTodo(array $params): void
    {
        $stmt = Db::pdo()->prepare('DELETE FROM demo_todos WHERE id = ?');
        $stmt->execute([(int) $params['id']]);
        if ($stmt->rowCount() === 0) {
            $this->json(['error' => 'Дело не найдено'], 404);
        }
        http_response_code(204);
        exit;
    }

    // ---------- Служебное: для тестов ----------

    /** Вернуть список дел в исходное состояние. */
    public function reset(): void
    {
        $pdo = Db::pdo();
        $pdo->exec('DELETE FROM demo_todos');
        $pdo->exec('DELETE FROM sqlite_sequence WHERE name = "demo_todos"');
        $pdo->exec('DELETE FROM demo_state');
        $stmt = $pdo->prepare('INSERT INTO demo_todos (text, done) VALUES (?, 0)');
        foreach (self::SEED_TODOS as $text) {
            $stmt->execute([$text]);
        }
        $this->json(['ok' => true]);
    }

    /** Заказать ошибку: следующие N запросов к API вернут 500. */
    public function failNext(): void
    {
        $times = max(1, (int) ($_GET['times'] ?? 1));
        Db::pdo()->prepare('INSERT OR REPLACE INTO demo_state (key, value) VALUES ("fail", ?)')->execute([$times]);
        $this->json(['ok' => true]);
    }

    private function takeFailure(): bool
    {
        $pdo = Db::pdo();
        $left = (int) $pdo->query('SELECT value FROM demo_state WHERE key = "fail"')->fetchColumn();
        if ($left <= 0) {
            return false;
        }
        $pdo->prepare('UPDATE demo_state SET value = ? WHERE key = "fail"')->execute([$left - 1]);
        return true;
    }

    private function todoRow(array $row): array
    {
        return ['id' => (int) $row['id'], 'text' => $row['text'], 'done' => (bool) $row['done']];
    }

    private function migrate(): void
    {
        $pdo = Db::pdo();
        $pdo->exec('CREATE TABLE IF NOT EXISTS demo_todos (
            id   INTEGER PRIMARY KEY AUTOINCREMENT,
            text TEXT NOT NULL,
            done INTEGER NOT NULL DEFAULT 0
        )');
        $pdo->exec('CREATE TABLE IF NOT EXISTS demo_state (key TEXT PRIMARY KEY, value TEXT)');
        if ((int) $pdo->query('SELECT COUNT(*) FROM sqlite_sequence WHERE name = "demo_todos"')->fetchColumn() === 0) {
            $stmt = $pdo->prepare('INSERT INTO demo_todos (text, done) VALUES (?, 0)');
            foreach (self::SEED_TODOS as $text) {
                $stmt->execute([$text]);
            }
        }
    }

    /** @return never */
    private function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
