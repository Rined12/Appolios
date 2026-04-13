-- APPOLIOS : chapitres, quiz, banque de questions, tentatives (à exécuter sur une base existante)
-- mysql -u root -p appolios_db < database/migrate_learning_modules.sql

USE appolios_db;

CREATE TABLE IF NOT EXISTS chapters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_chapters_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Si la table chapters existait déjà sans sort_order, compléter la colonne :
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chapters' AND COLUMN_NAME = 'sort_order');
SET @sql := IF(@c = 0, 'ALTER TABLE chapters ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER content', 'SELECT 1');
PREPARE _ch_sort FROM @sql;
EXECUTE _ch_sort;
DEALLOCATE PREPARE _ch_sort;

CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chapter_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    difficulty VARCHAR(32) NOT NULL DEFAULT 'beginner',
    tags VARCHAR(500) DEFAULT NULL,
    time_limit_sec INT DEFAULT NULL,
    questions_json LONGTEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_quizzes_chapter (chapter_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Si la table quizzes existait déjà sans toutes les colonnes (erreur Unknown column 'q.difficulty', etc.) :
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quizzes' AND COLUMN_NAME = 'difficulty');
SET @sql := IF(@c = 0, 'ALTER TABLE quizzes ADD COLUMN difficulty VARCHAR(32) NOT NULL DEFAULT ''beginner'' AFTER title', 'SELECT 1');
PREPARE _qz_d FROM @sql; EXECUTE _qz_d; DEALLOCATE PREPARE _qz_d;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quizzes' AND COLUMN_NAME = 'tags');
SET @sql := IF(@c = 0, 'ALTER TABLE quizzes ADD COLUMN tags VARCHAR(500) DEFAULT NULL AFTER difficulty', 'SELECT 1');
PREPARE _qz_tags FROM @sql; EXECUTE _qz_tags; DEALLOCATE PREPARE _qz_tags;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quizzes' AND COLUMN_NAME = 'time_limit_sec');
SET @sql := IF(@c = 0, 'ALTER TABLE quizzes ADD COLUMN time_limit_sec INT DEFAULT NULL AFTER tags', 'SELECT 1');
PREPARE _qz_tl FROM @sql; EXECUTE _qz_tl; DEALLOCATE PREPARE _qz_tl;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quizzes' AND COLUMN_NAME = 'questions_json');
SET @sql := IF(@c = 0, 'ALTER TABLE quizzes ADD COLUMN questions_json LONGTEXT NULL AFTER time_limit_sec', 'SELECT 1');
PREPARE _qz_qj FROM @sql; EXECUTE _qz_qj; DEALLOCATE PREPARE _qz_qj;
UPDATE quizzes SET questions_json = '[]' WHERE questions_json IS NULL;
-- Si la colonne vient d’être ajoutée, la rendre obligatoire (échoue si MySQL refuse ; utiliser alors run_fix_quizzes_columns.php) :
-- ALTER TABLE quizzes MODIFY COLUMN questions_json LONGTEXT NOT NULL;

-- created_by / questions_json complètes : `php database/run_fix_quizzes_columns.php`

CREATE TABLE IF NOT EXISTS question_bank (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT NULL,
    question_text TEXT NOT NULL,
    options_json TEXT NOT NULL,
    correct_answer INT NOT NULL DEFAULT 0,
    tags VARCHAR(500) DEFAULT NULL,
    difficulty VARCHAR(32) NOT NULL DEFAULT 'beginner',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_qbank_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total INT NOT NULL,
    percentage INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_attempts_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quiz_attempts' AND COLUMN_NAME = 'total');
SET @sql := IF(@c = 0, 'ALTER TABLE quiz_attempts ADD COLUMN total INT NOT NULL DEFAULT 0 AFTER score', 'SELECT 1');
PREPARE _qa_tot FROM @sql; EXECUTE _qa_tot; DEALLOCATE PREPARE _qa_tot;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quiz_attempts' AND COLUMN_NAME = 'percentage');
SET @sql := IF(@c = 0, 'ALTER TABLE quiz_attempts ADD COLUMN percentage INT NOT NULL DEFAULT 0 AFTER total', 'SELECT 1');
PREPARE _qa_pct FROM @sql; EXECUTE _qa_pct; DEALLOCATE PREPARE _qa_pct;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quiz_attempts' AND COLUMN_NAME = 'submitted_at');
SET @sql := IF(@c = 0, 'ALTER TABLE quiz_attempts ADD COLUMN submitted_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1');
PREPARE _qa_sa FROM @sql; EXECUTE _qa_sa; DEALLOCATE PREPARE _qa_sa;
