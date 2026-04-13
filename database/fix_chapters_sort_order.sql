-- Ajoute sort_order à chapters si elle manque (table créée avant la migration complète)
USE appolios_db;

SET @c := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'chapters'
    AND COLUMN_NAME = 'sort_order'
);

SET @sql := IF(@c = 0,
  'ALTER TABLE chapters ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER content',
  'SELECT ''sort_order déjà présent'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
