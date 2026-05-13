-- Migration for missing tables required by AdminController
-- Run this script to create the missing tables

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `subject` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `is_read` tinyint(1) NOT NULL DEFAULT 0,
    `read_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_contact_messages_is_read` (`is_read`),
    KEY `idx_contact_messages_read_by` (`read_by`),
    CONSTRAINT `fk_contact_messages_read_by` FOREIGN KEY (`read_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create teacher_applications table
CREATE TABLE IF NOT EXISTS `teacher_applications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `experience` text DEFAULT NULL,
    `qualifications` text DEFAULT NULL,
    `motivation` text DEFAULT NULL,
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `face_descriptor` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_teacher_applications_email` (`email`),
    KEY `idx_teacher_applications_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create activity_log table
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `activity_type` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_activity_log_user_id` (`user_id`),
    KEY `idx_activity_log_activity_type` (`activity_type`),
    KEY `idx_activity_log_created_at` (`created_at`),
    CONSTRAINT `fk_activity_log_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create view for pending teachers
CREATE OR REPLACE VIEW `v_pending_teachers` AS
SELECT 
    ta.id,
    ta.name,
    ta.email,
    ta.phone,
    ta.experience,
    ta.qualifications,
    ta.motivation,
    ta.status,
    ta.created_at
FROM teacher_applications ta
WHERE ta.status = 'pending'
ORDER BY ta.created_at DESC;

-- Insert some sample data if tables are empty
INSERT IGNORE INTO `contact_messages` (`name`, `email`, `subject`, `message`, `is_read`) VALUES
('John Doe', 'john@example.com', 'General Inquiry', 'I have a question about the courses offered.', 0),
('Jane Smith', 'jane@example.com', 'Technical Support', 'I am having trouble accessing my account.', 1);

INSERT IGNORE INTO `teacher_applications` (`name`, `email`, `phone`, `experience`, `qualifications`, `motivation`, `status`) VALUES
('Dr. Alice Johnson', 'alice@university.edu', '+1234567890', '5 years of teaching experience', 'PhD in Computer Science', 'Passionate about teaching and helping students learn', 'pending'),
('Prof. Bob Wilson', 'bob@college.edu', '+0987654321', '10 years of industry experience', 'Masters in Software Engineering', 'Want to share real-world experience with students', 'approved');

-- Grant necessary permissions (adjust as needed for your setup)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON appolios_db.contact_messages TO 'appolios_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON appolios_db.teacher_applications TO 'appolios_user'@'localhost';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON appolios_db.activity_log TO 'appolios_user'@'localhost';
-- GRANT SELECT ON appolios_db.v_pending_teachers TO 'appolios_user'@'localhost';

-- Flush privileges to apply changes
-- FLUSH PRIVILEGES;
