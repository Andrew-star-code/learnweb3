<?php
declare(strict_types=1);

namespace App;

/**
 * Прогресс по заданиям и колода карточек для интервального повторения.
 */
final class Progress
{
    /** Через сколько дней показать карточку снова — по номеру «коробки» (система Лейтнера). */
    private const INTERVALS = [1 => 1, 2 => 3, 3 => 7, 4 => 14, 5 => 30];

    /** @return array<string, array> прогресс по всем заданиям, ключ — id задания */
    public static function all(): array
    {
        $rows = Db::pdo()->query('SELECT * FROM task_progress')->fetchAll();
        return array_column($rows, null, 'task_id');
    }

    public static function save(string $taskId, ?string $code, bool $attempt, bool $passed): array
    {
        $pdo = Db::pdo();
        $stmt = $pdo->prepare('SELECT * FROM task_progress WHERE task_id = ?');
        $stmt->execute([$taskId]);
        $row = $stmt->fetch() ?: ['status' => 'started', 'attempts' => 0, 'code' => null];

        $status = ($passed || $row['status'] === 'done') ? 'done' : 'started';
        $attempts = (int) $row['attempts'] + ($attempt ? 1 : 0);

        $pdo->prepare('INSERT OR REPLACE INTO task_progress (task_id, status, attempts, code, updated_at)
                       VALUES (?, ?, ?, ?, ?)')
            ->execute([$taskId, $status, $attempts, $code ?? $row['code'], date('c')]);

        return ['status' => $status, 'attempts' => $attempts];
    }

    /** Кладёт карточки урока в колоду при первом открытии урока. */
    public static function addCards(array $cards): void
    {
        $stmt = Db::pdo()->prepare('INSERT OR IGNORE INTO cards (card_id, box, due_at) VALUES (?, 1, ?)');
        foreach ($cards as $card) {
            $stmt->execute([$card['id'], date('c')]);
        }
    }

    /** @return string[] id карточек, которые пора повторить */
    public static function dueCards(): array
    {
        $stmt = Db::pdo()->prepare('SELECT card_id FROM cards WHERE due_at <= ? ORDER BY due_at');
        $stmt->execute([date('c')]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function reviewCard(string $cardId, bool $remembered): void
    {
        $pdo = Db::pdo();
        $stmt = $pdo->prepare('SELECT box FROM cards WHERE card_id = ?');
        $stmt->execute([$cardId]);
        $box = $stmt->fetchColumn();
        if ($box === false) {
            return;
        }
        if ($remembered) {
            $due = date('c', time() + self::INTERVALS[(int) $box] * 86400);
            $box = min(5, (int) $box + 1);
        } else {
            $box = 1; // забыл — карточка возвращается в начало и всплывёт через 10 минут
            $due = date('c', time() + 600);
        }
        $pdo->prepare('UPDATE cards SET box = ?, due_at = ?, reviews = reviews + 1 WHERE card_id = ?')
            ->execute([$box, $due, $cardId]);
    }
}
