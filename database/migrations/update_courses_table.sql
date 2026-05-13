-- Migration: Add new columns to courses table
-- Date: 2024-05-09

-- Add new columns to courses table
ALTER TABLE courses 
    ADD COLUMN IF NOT EXISTS image VARCHAR(500) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS course_type VARCHAR(100) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS price DECIMAL(10, 2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    ADD COLUMN IF NOT EXISTS admin_message TEXT DEFAULT NULL;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_status ON courses(status);
CREATE INDEX IF NOT EXISTS idx_course_type ON courses(course_type);
CREATE INDEX IF NOT EXISTS idx_price ON courses(price);
