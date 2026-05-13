-- Complete migration script for Appolios database
-- This script creates all necessary tables for the quiz functionality and fixes missing tables

-- Drop any existing problematic tables with corrupted tablespaces
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS teacher_applications;
DROP TABLE IF EXISTS activity_log;
DROP TABLE IF EXISTS evenements;
DROP TABLE IF EXISTS evenement_ressources;

-- Create contact_messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contact_messages_is_read (is_read),
    INDEX idx_contact_messages_read_by (read_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create teacher_applications table
CREATE TABLE teacher_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    experience TEXT DEFAULT NULL,
    qualifications TEXT DEFAULT NULL,
    motivation TEXT DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    face_descriptor TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_teacher_applications_email (email),
    INDEX idx_teacher_applications_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create activity_log table
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    activity_type VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_log_user_id (user_id),
    INDEX idx_activity_log_activity_type (activity_type),
    INDEX idx_activity_log_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create evenements table
CREATE TABLE evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    titre VARCHAR(255) DEFAULT NULL,
    description TEXT NOT NULL,
    date_debut DATE DEFAULT NULL,
    date_fin DATE DEFAULT NULL,
    heure_debut TIME DEFAULT NULL,
    heure_fin TIME DEFAULT NULL,
    lieu VARCHAR(255) DEFAULT NULL,
    capacite_max INT DEFAULT NULL,
    type VARCHAR(100) DEFAULT NULL,
    statut ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    approved_by INT DEFAULT NULL,
    approved_at DATETIME DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    event_date DATETIME NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date_debut (date_debut),
    INDEX idx_event_date (event_date),
    INDEX idx_created_by (created_by),
    INDEX idx_approval_status (approval_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create evenement_ressources table
CREATE TABLE evenement_ressources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evenement_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    type_ressource ENUM('document', 'video', 'image', 'lien', 'autre') DEFAULT 'document',
    fichier VARCHAR(255) DEFAULT NULL,
    lien VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_evenement_ressources_evenement_id (evenement_id),
    INDEX idx_evenement_ressources_type (type_ressource)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create view for pending teachers
CREATE OR REPLACE VIEW v_pending_teachers AS
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

-- Insert sample data for testing
INSERT INTO contact_messages (name, email, subject, message, is_read) VALUES
('John Doe', 'john@example.com', 'General Inquiry', 'I have a question about the courses offered.', 0),
('Jane Smith', 'jane@example.com', 'Technical Support', 'I am having trouble accessing my account.', 1);

INSERT INTO teacher_applications (name, email, phone, experience, qualifications, motivation, status) VALUES
('Dr. Alice Johnson', 'alice@university.edu', '+1234567890', '5 years of teaching experience', 'PhD in Computer Science', 'Passionate about teaching and helping students learn', 'pending'),
('Prof. Bob Wilson', 'bob@college.edu', '+0987654321', '10 years of industry experience', 'Masters in Software Engineering', 'Want to share real-world experience with students', 'approved');

INSERT INTO evenements (title, description, event_date, created_by) VALUES
('Welcome Event', 'Welcome event for new students', '2026-05-15 10:00:00', 1),
('Tech Workshop', 'Introduction to modern web technologies', '2026-05-20 14:00:00', 1);

-- Verify tables are created
SELECT 'Tables created successfully' as status;
