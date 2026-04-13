-- ============================================
-- Script pour ajouter le rôle teacher et modifier la structure
-- ============================================

USE appolios_db;

-- Modifier la colonne role pour ajouter 'teacher'
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student', 'teacher') DEFAULT 'student';

-- Ajouter une colonne pour savoir qui a créé l'enseignant (optionnel)
-- ALTER TABLE users ADD COLUMN created_by INT NULL;

-- ============================================
-- SAMPLE TEACHER USER (créé par admin)
-- Email: teacher@appolios.com
-- Password: teacher123
-- ============================================
-- Générer le hash: password_hash('teacher123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role) VALUES
('Teacher Demo', 'teacher@appolios.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher');

-- Note: Ce hash doit être remplacé par un hash valide généré avec password_hash('teacher123', PASSWORD_DEFAULT)
