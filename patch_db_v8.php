<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = getConnection();

    try {
        $db->exec(
            "CREATE TABLE IF NOT EXISTS discussion_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                discussion_id INT NOT NULL,
                room VARCHAR(128) NOT NULL,
                user_id INT NOT NULL,
                user_name VARCHAR(255) NULL,
                message TEXT NULL,
                message_type VARCHAR(32) NOT NULL DEFAULT 'text',
                file_url VARCHAR(600) NULL,
                file_name VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_discussion_created (discussion_id, created_at),
                INDEX idx_room_created (room, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        echo "Created/ensured discussion_messages.\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    echo "Database patch complete.";
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
