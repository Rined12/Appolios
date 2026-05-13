<?php
require_once 'config/database.php';

$pdo = getConnection();

$sql = "
SET FOREIGN_KEY_CHECKS=0;

-- Fix groupe_user table structure
ALTER TABLE `groupe_user` 
ADD COLUMN IF NOT EXISTS `role` varchar(50) DEFAULT 'membre' AFTER `id_user`,
CHANGE COLUMN `date_rejoint` `date_adhesion` datetime DEFAULT CURRENT_TIMESTAMP;

-- Also fix any potential issues in groupe table
ALTER TABLE `groupe`
MODIFY COLUMN `statut` varchar(50) DEFAULT 'actif';

SET FOREIGN_KEY_CHECKS=1;
";

try {
    $pdo->exec($sql);
    echo "Groupe user table fixed successfully!\n";
} catch (PDOException $e) {
    echo "Error fixing table: " . $e->getMessage() . "\n";
}
