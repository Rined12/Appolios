<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = getConnection();

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS groupe (
                id_groupe INT AUTO_INCREMENT PRIMARY KEY,
                nom_groupe VARCHAR(255) NOT NULL,
                description TEXT NULL,
                date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                id_createur INT NOT NULL,
                statut VARCHAR(32) NOT NULL DEFAULT 'actif',
                approval_statut VARCHAR(32) NOT NULL DEFAULT 'en_cours',
                image_url VARCHAR(500) NULL DEFAULT NULL,
                INDEX idx_creator (id_createur),
                INDEX idx_created (date_creation),
                INDEX idx_approval (approval_statut)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured groupe.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS groupe_user (
                id_groupe INT NOT NULL,
                id_user INT NOT NULL,
                role VARCHAR(32) NOT NULL DEFAULT 'membre',
                date_adhesion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id_groupe, id_user),
                INDEX idx_user (id_user),
                INDEX idx_group (id_groupe)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured groupe_user.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS discussion (
                id_discussion INT AUTO_INCREMENT PRIMARY KEY,
                id_groupe INT NOT NULL,
                id_auteur INT NOT NULL,
                titre VARCHAR(255) NOT NULL,
                contenu TEXT NULL,
                date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                approval_statut VARCHAR(32) NOT NULL DEFAULT 'approuve',
                INDEX idx_group_created (id_groupe, date_creation),
                INDEX idx_author_created (id_auteur, date_creation),
                INDEX idx_approval (approval_statut)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured discussion.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS live_locations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                discussion_id INT NOT NULL,
                latitude DECIMAL(10, 8) NOT NULL,
                longitude DECIMAL(11, 8) NOT NULL,
                shared_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                expires_at DATETIME NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                UNIQUE KEY unique_user_discussion (user_id, discussion_id),
                INDEX idx_discussion_active (discussion_id, is_active, expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured live_locations.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS group_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                user_id INT NOT NULL,
                post_type VARCHAR(20) NOT NULL DEFAULT 'text',
                content TEXT NULL,
                media_url VARCHAR(500) NULL,
                media_kind VARCHAR(20) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_group_created (group_id, created_at),
                INDEX idx_user_created (user_id, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured group_posts.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS group_post_reactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT NOT NULL,
                reaction VARCHAR(20) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_post_user (post_id, user_id),
                INDEX idx_post_reaction (post_id, reaction)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured group_post_reactions.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS group_post_comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_post_created (post_id, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured group_post_comments.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    echo "Database patch complete.";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
