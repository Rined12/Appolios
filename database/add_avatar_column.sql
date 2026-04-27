-- ============================================
-- Add Avatar Column to Users Table
-- ============================================

USE `appolios-MVC`;

ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL DEFAULT NULL AFTER face_descriptor;
