-- Créer la base de données
CREATE DATABASE IF NOT EXISTS appolios_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE appolios_db;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des cours
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

-- Table des inscriptions
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

-- Utilisateur Admin (mot de passe: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@appolios.com', '$2y$10$hR9QJPKvHYKvGz3xXWvYqOQYQjZcX1GqNqQ7QJmXpQZG9ZMQ8Y9K2', 'admin');

-- Utilisateur Étudiant (mot de passe: student123)
INSERT INTO users (name, email, password, role) VALUES
('John Student', 'student@appolios.com', '$2y$10$hR9QJPKvHYKvGz3xXWvYqOQYQjZcX1GqNqQ7QJmXpQZG9ZMQ8Y9K2', 'student');

-- Cours exemples
INSERT INTO courses (title, description, video_url, created_by) VALUES
('Introduction to Web Development', 'Learn HTML, CSS, and JavaScript basics.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 1),
('Python Programming', 'Master Python from scratch.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 1),
('Database Design', 'Learn MySQL database design.', NULL, 1);

-- Inscription exemple
INSERT INTO enrollments (user_id, course_id, progress) VALUES (2, 1, 45);