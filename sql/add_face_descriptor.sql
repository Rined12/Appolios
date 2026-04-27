-- ============================================
-- Face ID Migration — run in phpMyAdmin
-- Safe to re-run (uses IF NOT EXISTS guard)
-- ============================================

-- 1. Add face_descriptor to the users table
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS face_descriptor LONGTEXT NULL DEFAULT NULL
    COMMENT 'JSON-encoded 128-float face descriptor for Face ID login';

-- 2. Add face_descriptor to teacher_applications table
--    Stored during registration, copied to users when admin approves
ALTER TABLE teacher_applications
    ADD COLUMN IF NOT EXISTS face_descriptor LONGTEXT NULL DEFAULT NULL
    COMMENT 'JSON-encoded 128-float face descriptor captured at registration';
