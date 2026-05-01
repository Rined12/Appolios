-- =====================================================
-- SAMPLE COURSES FOR APPOLIOS
-- =====================================================
-- Just run step 1, find the IDs, then run step 2

-- ==================== STEP 1 ====================
-- Run this first to add courses:

INSERT INTO courses (title, description, image, status, created_by, created_at) VALUES
('Full Stack Web Development', 'Master HTML, CSS, JavaScript, PHP and MySQL.', 'https://images.unsplash.com/photo-1461749280684-dccba630e4f6?w=500', 'published', 1, NOW()),
('Data Science with Python', 'Analyze data and build ML models with Python.', 'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?w=500', 'published', 1, NOW()),
('Mobile App Development', 'Build iOS and Android apps with modern frameworks.', 'https://images.unsplash.com/photo-1544383835-bda2bc66a137?w=500', 'published', 1, NOW());

-- After running step 1, run this to see the new IDs:
-- SELECT id, title FROM courses ORDER BY id DESC LIMIT 3;

-- ==================== STEP 2 ====================
-- After you know the course IDs from above, UPDATE the IDs below and run:

-- === For "Full Stack Web Development" course (replace 16 with actual ID) ===
INSERT INTO chapters (course_id, title, description, chapter_order, created_at) VALUES
(16, 'HTML Foundation', 'Learn HTML basics', 1, NOW()),
(16, 'CSS Styling', 'Style your pages', 2, NOW()),
(16, 'JavaScript Fundamentals', 'Add interactivity', 3, NOW());

-- === For "Data Science with Python" course (replace 17 with actual ID) ===
INSERT INTO chapters (course_id, title, description, chapter_order, created_at) VALUES
(17, 'Python Basics', 'Learn Python syntax', 1, NOW()),
(17, 'Data Analysis', 'Analyze data with pandas', 2, NOW()),
(17, 'Machine Learning', 'Build ML models', 3, NOW());

-- === For "Mobile App Development" course (replace 18 with actual ID) ===
INSERT INTO chapters (course_id, title, description, chapter_order, created_at) VALUES
(18, 'Mobile Intro', 'Mobile development overview', 1, NOW()),
(18, 'React Native', 'Build cross-platform apps', 2, NOW()),
(18, 'Publishing Apps', 'Deploy to stores', 3, NOW());

-- ==================== STEP 3 ====================
-- After chapters are created, run this to see chapter IDs:
-- SELECT id, title FROM chapters ORDER BY id DESC LIMIT 9;

-- Then UPDATE lesson IDs below and run:

-- Lessons for HTML Chapter (chapter 25)
INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration, lesson_order, created_at) VALUES
(25, 'What is HTML?', 'text', NULL, '<h2>HTML Introduction</h2><p>HTML is the standard markup language for web pages.</p>', 10, 1, NOW()),
(25, 'Your First HTML Page', 'video', 'https://www.youtube.com/watch?v=UB1O30gRrR0', NULL, 15, 2, NOW()),
(25, 'HTML Tags Guide', 'pdf', 'https://www.w3schools.com/tags/ref_byfunc.asp', NULL, 20, 3, NOW());

-- Lessons for CSS Chapter (chapter 26)
INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration, lesson_order, created_at) VALUES
(26, 'CSS Introduction', 'text', NULL, '<h2>Cascading Style Sheets</h2><p>CSS styles your HTML elements.</p>', 12, 1, NOW()),
(26, 'CSS Selectors', 'video', 'https://www.youtube.com/watch?v=yFOFdUn4eAQ', NULL, 18, 2, NOW());

-- Lessons for JavaScript Chapter (chapter 27)
INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration, lesson_order, created_at) VALUES
(27, 'JavaScript Basics', 'text', NULL, '<h2>JavaScript</h2><p>Add interactivity to your website.</p>', 10, 1, NOW()),
(27, 'Variables in JS', 'video', 'https://www.youtube.com/watch?v=W6NZfCO2U3A', NULL, 20, 2, NOW());

-- Lessons for Python Chapter (chapter 28)
INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration, lesson_order, created_at) VALUES
(28, 'Why Python?', 'text', NULL, '<h2>Python Overview</h2><p>Python is beginner-friendly.</p>', 10, 1, NOW()),
(28, 'Install Python', 'video', 'https://www.youtube.com/watch?v=YYKdXZfjohLc', NULL, 15, 2, NOW());

-- Lessons for Data Analysis (chapter 29)
INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration, lesson_order, created_at) VALUES
(29, 'Pandas Basics', 'text', NULL, '<h2>Data Analysis</h2><p>Use pandas for data analysis.</p>', 12, 1, NOW()),
(29, 'DataFrames', 'video', 'https://www.youtube.com/watch?v=2Ji-ClqBuYA', NULL, 18, 2, NOW());

-- Lessons for Mobile Intro (chapter 31)
INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration, lesson_order, created_at) VALUES
(31, 'Mobile Dev Overview', 'text', NULL, '<h2>Mobile Development</h2><p>Overview of mobile app development.</p>', 10, 1, NOW()),
(31, 'Choosing a Framework', 'video', 'https://www.youtube.com/watch?v=w5IJhF1sOaU', NULL, 15, 2, NOW());

-- ==================== STEP 4 ====================
-- Enroll user 2 in Web Dev course (replace 16 with actual course ID)
INSERT INTO enrollments (user_id, course_id, progress, enrolled_at) VALUES
(2, 16, 25, NOW());