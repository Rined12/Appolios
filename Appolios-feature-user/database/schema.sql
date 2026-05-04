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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_blocked (is_blocked)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    created_by INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_message TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_created_by (created_by),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CHAPTERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    chapter_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_course_id (course_id),
    INDEX idx_chapter_order (chapter_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- LESSONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    video_url VARCHAR(500),
    pdf_path VARCHAR(500),
    lesson_type ENUM('video', 'text', 'pdf', 'both') DEFAULT 'video',
    lesson_order INT DEFAULT 0,
    duration INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE,
    INDEX idx_chapter_id (chapter_id),
    INDEX idx_lesson_order (lesson_order),
    INDEX idx_lesson_type (lesson_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ENROLLMENTS TABLE (Progress tracking per lesson)
-- ============================================
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    progress INT DEFAULT 0,
    completed_lessons TEXT,
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
INSERT INTO courses (title, description, image, created_by, status) VALUES
('Introduction to Web Development', 'Learn the fundamentals of web development including HTML, CSS, and JavaScript. This course is perfect for beginners who want to start their journey in web development. You will learn how to create responsive websites and understand modern web standards.', NULL, 1, 'published'),
('Python Programming Basics', 'Master Python programming from scratch. Learn variables, functions, loops, and more in this comprehensive beginner course. Perfect for those new to programming.', NULL, 1, 'published'),
('Database Design with MySQL', 'Learn how to design efficient databases using MySQL. Covers normalization, relationships, and SQL queries. Essential for backend developers.', NULL, 1, 'published'),
('JavaScript Advanced Concepts', 'Take your JavaScript skills to the next level. Learn about closures, promises, async/await, and modern ES6+ features.', NULL, 1, 'published'),
('Responsive Web Design', 'Create beautiful, responsive websites that work on all devices. Learn CSS Grid, Flexbox, and mobile-first design principles.', NULL, 1, 'published');

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
    c.image,
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


-- Add course_type column to courses table
ALTER TABLE courses ADD COLUMN course_type VARCHAR(100) DEFAULT NULL AFTER image;

-- ============================================
-- CHATBOT CONVERSATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS chatbot_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    user_id INT NULL,
    role ENUM('user', 'assistant') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CHATBOT CONTEXT TABLE (Student context)
-- ============================================
CREATE TABLE IF NOT EXISTS chatbot_context (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL UNIQUE,
    user_id INT,
    enrolled_courses TEXT,
    current_lesson_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSE CATEGORIES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(20) DEFAULT '#667eea',
    icon VARCHAR(50) DEFAULT 'fa-book',
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_parent_id (parent_id),
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSE REVIEWS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS course_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_course (user_id, course_id),
    INDEX idx_course_id (course_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER XP POINTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS user_xp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    xp_points INT DEFAULT 0,
    level INT DEFAULT 1,
    total_courses_completed INT DEFAULT 0,
    total_lessons_completed INT DEFAULT 0,
    total_quizzes_passed INT DEFAULT 0,
    streak_days INT DEFAULT 0,
    last_activity_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- XP TRANSACTIONS LOG
-- ============================================
CREATE TABLE IF NOT EXISTS xp_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    xp_amount INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSE BOOKMARKS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS course_bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_course (user_id, course_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER NOTIFICATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error', 'achievement', 'badge', 'course', 'event') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSE CERTIFICATES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    certificate_code VARCHAR(50) NOT NULL UNIQUE,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    download_url VARCHAR(500),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_course (user_id, course_id),
    INDEX idx_user_id (user_id),
    INDEX idx_course_id (course_id),
    INDEX idx_code (certificate_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BADGES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'fa-award',
    color VARCHAR(20) DEFAULT '#667eea',
    xp_reward INT DEFAULT 0,
    criteria_type VARCHAR(50),
    criteria_value INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- USER BADGES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_badge (user_id, badge_id),
    INDEX idx_user_id (user_id),
    INDEX idx_badge_id (badge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE CATEGORIES
-- ============================================
INSERT INTO categories (name, slug, description, color, icon) VALUES
('Web Development', 'web-development', 'Learn to build websites and web applications', '#667eea', 'fa-code'),
('Mobile Development', 'mobile-development', 'Create mobile apps for iOS and Android', '#764ba2', 'fa-mobile-alt'),
('Data Science', 'data-science', 'Analyze data and build ML models', '#f093fb', 'fa-database'),
('DevOps', 'devops', 'Learn CI/CD, containers, and cloud', '#4facfe', 'fa-cloud'),
('Design', 'design', 'UI/UX design and graphic design', '#43e97b', 'fa-palette');

-- ============================================
-- SAMPLE BADGES
-- ============================================
INSERT INTO badges (name, slug, description, icon, color, xp_reward, criteria_type, criteria_value) VALUES
('First Steps', 'first-steps', 'Complete your first lesson', 'fa-star', '#ffd700', 10, 'lessons_completed', 1),
('Quick Learner', 'quick-learner', 'Complete 5 lessons', 'fa-bolt', '#ff6b6b', 25, 'lessons_completed', 5),
('Course Master', 'course-master', 'Complete your first course', 'fa-trophy', '#4facfe', 100, 'courses_completed', 1),
('Dedicated Student', 'dedicated-student', 'Complete 3 courses', 'fa-graduation-cap', '#667eea', 250, 'courses_completed', 3),
('XP Champion', 'xp-champion', 'Earn 500 XP points', 'fa-medal', '#f093fb', 50, 'xp_earned', 500),
('Streak Starter', 'streak-starter', 'Maintain a 3-day streak', 'fa-fire', '#ff9a9e', 30, 'streak_days', 3),
('Perfect Score', 'perfect-score', 'Pass a quiz with 100%', 'fa-check-circle', '#43e97b', 20, 'quiz_perfect', 1),
('Bookworm', 'bookworm', 'Bookmark 5 courses', 'fa-bookmark', '#764ba2', 15, 'bookmarks', 5);

-- ============================================
-- PAYMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    stripe_session_id VARCHAR(255) NOT NULL,
    stripe_payment_id VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'usd',
    status ENUM('pending', 'succeeded', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_course_id (course_id),
    INDEX idx_status (status),
    INDEX idx_stripe_session (stripe_session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Add price column to courses if not exists
-- ============================================
ALTER TABLE courses ADD COLUMN price DECIMAL(10,2) DEFAULT 0 AFTER status;
