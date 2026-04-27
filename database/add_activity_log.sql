-- ============================================
-- Activity Log Table - Track all user activities
-- ============================================

USE appolios_db;

CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    user_name VARCHAR(100) NULL,
    user_email VARCHAR(255) NULL,
    user_role VARCHAR(20) NULL,
    activity_type VARCHAR(50) NOT NULL,
    activity_description TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for testing
INSERT INTO activity_log (user_id, user_name, user_email, user_role, activity_type, activity_description, ip_address, created_at) VALUES
(1, 'Admin', 'admin@appolios.com', 'admin', 'login', 'Admin logged in', '127.0.0.1', NOW()),
(2, 'John Student', 'student@appolios.com', 'student', 'login', 'Student logged in', '127.0.0.1', NOW() - INTERVAL 1 HOUR),
(2, 'John Student', 'student@appolios.com', 'student', 'logout', 'Student logged out', '127.0.0.1', NOW() - INTERVAL 30 MINUTE);
