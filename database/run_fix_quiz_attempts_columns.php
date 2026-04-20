<?php
/**
 * Complète quiz_attempts (score, total, percentage, submitted_at, etc.) si la table est une ancienne version.
 *
 * Exécution (depuis le dossier APPOLIOS) :
 *   php database/run_fix_quiz_attempts_columns.php
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

$table = 'quiz_attempts';

if (!$hasCol($pdo, $table, 'score')) {
    $ref = $hasCol($pdo, $table, 'quiz_id') ? 'quiz_id' : 'id';
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN score INT NOT NULL DEFAULT 0 AFTER {$ref}");
    echo "OK : colonne score ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'total')) {
    $ref = $hasCol($pdo, $table, 'score') ? 'score' : 'quiz_id';
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN total INT NOT NULL DEFAULT 0 AFTER {$ref}");
    echo "OK : colonne total ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'percentage')) {
    $ref = $hasCol($pdo, $table, 'total') ? 'total' : 'score';
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN percentage INT NOT NULL DEFAULT 0 AFTER {$ref}");
    echo "OK : colonne percentage ajoutée.\n";
}

if (!$hasCol($pdo, $table, 'submitted_at')) {
    $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN submitted_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
    if ($hasCol($pdo, $table, 'created_at')) {
        $pdo->exec("UPDATE `{$table}` SET submitted_at = created_at WHERE submitted_at IS NULL");
    }
    echo "OK : colonne submitted_at ajoutée.\n";
}

echo "Terminé : quiz_attempts à jour.\n";
