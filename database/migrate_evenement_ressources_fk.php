<?php
/**
 * Normalize evenement_ressources relation for CRUD
 * - Force evenement_id NOT NULL
 * - Enforce FK to evenements(id) with ON DELETE CASCADE
 * - Add useful indexes
 *
 * Run: php database/migrate_evenement_ressources_fk.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS evenement_ressources (
                id INT AUTO_INCREMENT PRIMARY KEY,
                evenement_id INT NOT NULL,
                type ENUM('rule', 'materiel', 'plan') NOT NULL,
                title VARCHAR(255) NOT NULL,
                details TEXT DEFAULT NULL,
                created_by INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_evenement_id (evenement_id),
                INDEX idx_evenement_type (evenement_id, type),
                INDEX idx_type (type),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $hasColumn = $db->query("SHOW COLUMNS FROM evenement_ressources LIKE 'evenement_id'")->fetch();
    if (!$hasColumn) {
        $db->exec("ALTER TABLE evenement_ressources ADD COLUMN evenement_id INT NULL AFTER id");
        echo "[OK] Added evenement_id column\n";
    }

    $fkStmt = $db->query("SELECT CONSTRAINT_NAME
                          FROM information_schema.KEY_COLUMN_USAGE
                          WHERE TABLE_SCHEMA = DATABASE()
                            AND TABLE_NAME = 'evenement_ressources'
                            AND COLUMN_NAME = 'evenement_id'
                            AND REFERENCED_TABLE_NAME IS NOT NULL
                          LIMIT 1");
    $fk = $fkStmt->fetch();

    if (!empty($fk['CONSTRAINT_NAME'])) {
        $db->exec("ALTER TABLE evenement_ressources DROP FOREIGN KEY {$fk['CONSTRAINT_NAME']}");
        echo "[OK] Dropped old evenement FK\n";
    }

    $db->exec("DELETE r
              FROM evenement_ressources r
              LEFT JOIN evenements e ON e.id = r.evenement_id
              WHERE r.evenement_id IS NULL OR e.id IS NULL");
    echo "[OK] Removed orphan/null resources\n";

    $columnInfo = $db->query("SHOW COLUMNS FROM evenement_ressources LIKE 'evenement_id'")->fetch();
    if (isset($columnInfo['Null']) && strtoupper($columnInfo['Null']) === 'YES') {
        $db->exec("ALTER TABLE evenement_ressources MODIFY COLUMN evenement_id INT NOT NULL");
        echo "[OK] Set evenement_id to NOT NULL\n";
    }

    $db->exec("ALTER TABLE evenement_ressources MODIFY COLUMN type ENUM('rule', 'material', 'materiel', 'plan') NOT NULL");
    $db->exec("UPDATE evenement_ressources SET type = 'materiel' WHERE type = 'material'");
    $db->exec("ALTER TABLE evenement_ressources MODIFY COLUMN type ENUM('rule', 'materiel', 'plan') NOT NULL");
    echo "[OK] Normalized type values to rule/materiel/plan\n";

    $hasIdxEvent = $db->query("SHOW INDEX FROM evenement_ressources WHERE Key_name = 'idx_evenement_id'")->fetch();
    if (!$hasIdxEvent) {
        $db->exec("ALTER TABLE evenement_ressources ADD INDEX idx_evenement_id (evenement_id)");
        echo "[OK] Added idx_evenement_id\n";
    }

    $hasIdxEventType = $db->query("SHOW INDEX FROM evenement_ressources WHERE Key_name = 'idx_evenement_type'")->fetch();
    if (!$hasIdxEventType) {
        $db->exec("ALTER TABLE evenement_ressources ADD INDEX idx_evenement_type (evenement_id, type)");
        echo "[OK] Added idx_evenement_type\n";
    }

    $db->exec("ALTER TABLE evenement_ressources
              ADD CONSTRAINT fk_ressource_evenement
              FOREIGN KEY (evenement_id) REFERENCES evenements(id) ON DELETE CASCADE");
    echo "[OK] Added FK fk_ressource_evenement with ON DELETE CASCADE\n";

    echo "\nMigration completed successfully.\n";
} catch (Throwable $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
