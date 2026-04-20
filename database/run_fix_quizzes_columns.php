<?php
/**
 * Ajoute sur `quizzes` les colonnes attendues par l’app (dont questions_json, difficulty, tags, time_limit_sec,
 * created_by, created_at, updated_at) si la table date d’une ancienne version du schéma.
 *
 * Exécution (depuis le dossier APPOLIOS) :
 *   php database/run_fix_quizzes_columns.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/config/config.php';
require_once $root . '/config/database.php';

$pdo = Database::getInstance()->getConnection();

$hasCol = static function (PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([DB_NAME, $table, $column]);

    return (int) $stmt->fetchColumn() > 0;
};

$table = 'quizzes';

if (!$hasCol($pdo, $table, 'difficulty')) {
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN difficulty VARCHAR(32) NOT NULL DEFAULT 'beginner' AFTER title");
    echo "OK : colonne difficulty ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'tags')) {
    $ref = $hasCol($pdo, $table, 'difficulty') ? 'difficulty' : 'title';
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN tags VARCHAR(500) DEFAULT NULL AFTER {$ref}");
    echo "OK : colonne tags ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'time_limit_sec')) {
    $ref = $hasCol($pdo, $table, 'tags') ? 'tags' : ($hasCol($pdo, $table, 'difficulty') ? 'difficulty' : 'title');
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN time_limit_sec INT DEFAULT NULL AFTER {$ref}");
    echo "OK : colonne time_limit_sec ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'questions_json')) {
    if ($hasCol($pdo, $table, 'time_limit_sec')) {
        $refQj = 'time_limit_sec';
    } elseif ($hasCol($pdo, $table, 'tags')) {
        $refQj = 'tags';
    } elseif ($hasCol($pdo, $table, 'difficulty')) {
        $refQj = 'difficulty';
    } else {
        $refQj = 'title';
    }
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN questions_json LONGTEXT NULL AFTER {$refQj}");
    if ($hasCol($pdo, $table, 'questions')) {
        $pdo->exec("UPDATE `{$table}` SET questions_json = IFNULL(NULLIF(TRIM(questions), ''), '[]') WHERE questions_json IS NULL");
    }
    $pdo->exec("UPDATE `{$table}` SET questions_json = '[]' WHERE questions_json IS NULL OR TRIM(questions_json) = ''");
    $pdo->exec("ALTER TABLE `{$table}` MODIFY COLUMN questions_json LONGTEXT NOT NULL");
    echo "OK : colonne questions_json ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'created_by')) {
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN created_by INT NULL");
    $pdo->exec("UPDATE `{$table}` q
        INNER JOIN chapters ch ON ch.id = q.chapter_id
        INNER JOIN courses c ON c.id = ch.course_id
        SET q.created_by = c.created_by");
    $uid = (int) $pdo->query('SELECT id FROM users ORDER BY id ASC LIMIT 1')->fetchColumn();
    if ($uid > 0) {
        $pdo->exec("UPDATE `{$table}` SET created_by = {$uid} WHERE created_by IS NULL");
    }
    $pdo->exec("ALTER TABLE `{$table}` MODIFY COLUMN created_by INT NOT NULL");
    echo "OK : colonne created_by ajoutée et renseignée.\n";
}

if (!$hasCol($pdo, $table, 'created_at')) {
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
    echo "OK : colonne created_at ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'updated_at')) {
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    echo "OK : colonne updated_at ajoutée.\n";
}

echo "Terminé : structure quizzes à jour.\n";
