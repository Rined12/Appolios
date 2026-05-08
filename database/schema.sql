-- ============================================
-- APPOLIOS Database Schema
-- E-Learning Platform
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS appolios_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE appolios_db;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student', 'teacher') DEFAULT 'student',
    is_blocked TINYINT(1) DEFAULT 0,
    face_descriptor LONGTEXT NULL DEFAULT NULL,
    reset_token VARCHAR(64) NULL DEFAULT NULL,
    reset_token_expiry DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_blocked (is_blocked),
    INDEX idx_reset_token (reset_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_url VARCHAR(500),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ENROLLMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    progress INT DEFAULT 0,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    INDEX idx_user_id (user_id),
    INDEX idx_course_id (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- EVENEMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS evenements (
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
    location VARCHAR(255) DEFAULT NULL,
    event_date DATETIME NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_date_debut (date_debut),
    INDEX idx_event_date (event_date),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- EVENEMENT RESSOURCES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS evenement_ressources (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT ADMIN USER
-- Email: admin@appolios.com
-- Password: admin123
-- ============================================
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@appolios.com', '$2y$10$QMtpxtWzC.cf7SO0kkrUVeLq9UccyCjlHfxKNRY6nRvRsHaC1XjeK', 'admin');

-- ============================================
-- SAMPLE STUDENT USER
-- Email: student@appolios.com
-- Password: student123
-- ============================================
INSERT INTO users (name, email, password, role) VALUES
('John Student', 'student@appolios.com', '$2y$10$U1xXpXYYlmfkdWBtZyNEuOnIVTmVTOMBw1mQqeTvNLgAeoooqB99e', 'student');

-- ============================================
-- SAMPLE COURSES
-- ============================================
INSERT INTO courses (title, description, video_url, created_by) VALUES
('Introduction to Web Development', 'Learn the fundamentals of web development including HTML, CSS, and JavaScript. This course is perfect for beginners who want to start their journey in web development. You will learn how to create responsive websites and understand modern web standards.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 1),
('Python Programming Basics', 'Master Python programming from scratch. Learn variables, functions, loops, and more in this comprehensive beginner course. Perfect for those new to programming.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 1),
('Database Design with MySQL', 'Learn how to design efficient databases using MySQL. Covers normalization, relationships, and SQL queries. Essential for backend developers.', NULL, 1),
('JavaScript Advanced Concepts', 'Take your JavaScript skills to the next level. Learn about closures, promises, async/await, and modern ES6+ features.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 1),
('Responsive Web Design', 'Create beautiful, responsive websites that work on all devices. Learn CSS Grid, Flexbox, and mobile-first design principles.', NULL, 1);

-- ============================================
-- SAMPLE EVENEMENT
-- ============================================
INSERT INTO evenements (title, titre, description, date_debut, date_fin, heure_debut, heure_fin, lieu, capacite_max, type, statut, location, event_date, created_by) VALUES
('Journee Portes Ouvertes APPOLIOS', 'Journee Portes Ouvertes APPOLIOS', 'Presentation des modules de formation et rencontre avec les enseignants.', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', '17:00:00', 'Main Campus Hall', 300, 'open-day', 'planifie', 'Main Campus Hall', DATE_ADD(NOW(), INTERVAL 7 DAY), 1);

-- ============================================
-- VIEW FOR EVENEMENTS WITH CREATOR INFO
-- ============================================
CREATE OR REPLACE VIEW v_evenements_with_creator AS
SELECT
    e.id,
    e.title,
    e.titre,
    e.description,
    e.date_debut,
    e.date_fin,
    e.heure_debut,
    e.heure_fin,
    e.lieu,
    e.capacite_max,
    e.type,
    e.statut,
    e.location,
    e.event_date,
    e.created_by,
    e.created_at,
    e.updated_at,
    u.name AS creator_name,
    u.role AS creator_role
FROM evenements e
JOIN users u ON u.id = e.created_by;

-- ============================================
-- VIEW FOR ENROLLED COURSES WITH DETAILS
-- ============================================
CREATE OR REPLACE VIEW v_student_courses AS
SELECT
    e.id as enrollment_id,
    e.user_id,
    e.course_id,
    e.progress,
    e.enrolled_at,
    c.title,
    c.description,
    c.video_url,
    u.name as student_name
FROM enrollments e
JOIN courses c ON e.course_id = c.id
JOIN users u ON e.user_id = u.id;

-- ============================================
-- SAMPLE ENROLLMENT (Student 2 enrolled in Course 1)
-- ============================================
INSERT INTO enrollments (user_id, course_id, progress) VALUES (2, 1, 45);

-- ============================================
-- TEACHER APPLICATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS teacher_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    cv_filename VARCHAR(255) NOT NULL,
    cv_path VARCHAR(500) NOT NULL,
    face_descriptor LONGTEXT NULL DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- VIEW FOR PENDING TEACHER APPLICATIONS
-- ============================================
CREATE OR REPLACE VIEW v_pending_teachers AS
SELECT
    ta.id,
    ta.name,
    ta.email,
    ta.cv_filename,
    ta.cv_path,
    ta.status,
    ta.admin_notes,
    ta.created_at,
    ta.reviewed_by,
    ta.reviewed_at,
    u.name AS reviewer_name
FROM teacher_applications ta
LEFT JOIN users u ON ta.reviewed_by = u.id
WHERE ta.status = 'pending';

-- ============================================
-- CONTACT MESSAGES TABLE (Contact Us Inbox)
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_by INT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (read_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- END OF DATABASE SCHEMA
-- ============================================
