-- Optionnel : complète les colonnes manquantes sur quizzes (équivalent à migrate_learning_modules).
-- Recommandé sous XAMPP : php database/run_fix_quizzes_columns.php
-- Colonnes gérées : difficulty, tags, time_limit_sec, questions_json, created_by, created_at, updated_at

USE appolios_db;

-- Exemple si questions_json manque :
-- ALTER TABLE quizzes ADD COLUMN questions_json LONGTEXT NULL AFTER time_limit_sec;
-- UPDATE quizzes SET questions_json = '[]' WHERE questions_json IS NULL;
-- ALTER TABLE quizzes MODIFY COLUMN questions_json LONGTEXT NOT NULL;
