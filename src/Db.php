<?php
declare(strict_types=1);

namespace App;

use PDO;

/**
 * Подключение к SQLite, где хранится прогресс. Файл базы создаётся сам.
 */
final class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $pdo = new PDO('sqlite:' . DATA_DIR . '/progress.sqlite');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA busy_timeout = 3000');
            self::migrate($pdo);
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $pdo->exec('CREATE TABLE IF NOT EXISTS task_progress (
            task_id    TEXT PRIMARY KEY,
            status     TEXT NOT NULL DEFAULT "started",
            attempts   INTEGER NOT NULL DEFAULT 0,
            code       TEXT,
            updated_at TEXT NOT NULL
        )');
        $pdo->exec('CREATE TABLE IF NOT EXISTS cards (
            card_id TEXT PRIMARY KEY,
            box     INTEGER NOT NULL DEFAULT 1,
            due_at  TEXT NOT NULL,
            reviews INTEGER NOT NULL DEFAULT 0
        )');
    }
}
