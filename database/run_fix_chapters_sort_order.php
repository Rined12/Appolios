<?php
/**
 * Ajoute la colonne sort_order à chapters si elle manque.
 * Exécution : php database/run_fix_chapters_sort_order.php
 * (depuis le dossier APPOLIOS)
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/config/config.php';
require_once $root . '/config/database.php';

$pdo = Database::getInstance()->getConnection();

$stmt = $pdo->query("SELECT COUNT(*) AS n FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = " . $pdo->quote(DB_NAME) . "
    AND TABLE_NAME = 'chapters'
    AND COLUMN_NAME = 'sort_order'");
$row = $stmt->fetch();
$n = (int) ($row['n'] ?? 0);

if ($n > 0) {
    echo "OK : la colonne chapters.sort_order existe déjà.\n";
    exit(0);
}

$pdo->exec('ALTER TABLE chapters ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER content');
echo "OK : colonne sort_order ajoutée sur chapters.\n";
exit(0);
