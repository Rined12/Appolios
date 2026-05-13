<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlContent = "-- APPOLIOS Full Database Structure\n";
    $sqlContent .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Get all tables
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        die("Erreur: Aucune table trouvée dans la base de données.");
    }

    foreach ($tables as $table) {
        $sqlContent .= "-- Structure de la table `$table`\n";
        $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
        
        $createStmt = $db->query("SHOW CREATE TABLE `$table`");
        $createRow = $createStmt->fetch(PDO::FETCH_ASSOC);
        
        if (isset($createRow['Create Table'])) {
            $sqlContent .= $createRow['Create Table'] . ";\n\n";
        } elseif (isset($createRow['Create View'])) {
            $sqlContent .= $createRow['Create View'] . ";\n\n";
        }
    }

    $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

    // Path to Desktop
    $desktopPath = 'C:\\Users\\user\\Desktop\\appolios_structure.sql';
    
    // Fallback if the user folder is named differently
    $username = getenv('USERNAME');
    if ($username && $username !== 'user') {
        $desktopPath = "C:\\Users\\$username\\Desktop\\appolios_structure.sql";
        if (!is_dir("C:\\Users\\$username\\Desktop") && is_dir("C:\\Users\\$username\\Bureau")) {
            $desktopPath = "C:\\Users\\$username\\Bureau\\appolios_structure.sql";
        }
    } else {
        if (!is_dir('C:\\Users\\user\\Desktop') && is_dir('C:\\Users\\user\\Bureau')) {
            $desktopPath = 'C:\\Users\\user\\Bureau\\appolios_structure.sql';
        }
    }

    if (file_put_contents($desktopPath, $sqlContent) !== false) {
        echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
        echo "<h2 style='color:green;'>Succès !</h2>";
        echo "<p>Le fichier SQL contenant toutes les tables et attributs a été créé sur votre bureau.</p>";
        echo "<p><strong>Emplacement :</strong> $desktopPath</p>";
        echo "</div>";
    } else {
        // Fallback: Offer as a direct download if writing to desktop fails due to permissions
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="appolios_structure.sql"');
        echo $sqlContent;
        exit;
    }

} catch(Exception $e) {
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px; color:red;'>";
    echo "<h2>Erreur SQL</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
