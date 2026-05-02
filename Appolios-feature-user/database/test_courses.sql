-- Clear existing data (order matters due to foreign keys)
DELETE FROM lessons;
DELETE FROM chapters;
DELETE FROM courses;

-- Add 1 test course
INSERT INTO courses (title, description, image, status, created_by, created_at) VALUES
('AI Image Generation Mastery', 'Learn to create stunning AI-generated images using modern tools and techniques.', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=500', 'published', 1, NOW());

-- Get course ID (should be 1) and add chapters
INSERT INTO chapters (course_id, title, description, chapter_order, created_at) VALUES
(1, 'Introduction to AI Art', 'Understanding AI image generation basics', 1, NOW()),
(1, 'Prompt Engineering', 'Writing effective prompts for better results', 2, NOW()),
(1, 'Advanced Techniques', 'Mastering advanced AI art creation', 3, NOW());

-- Get chapter IDs (should be 1, 2, 3) and add lessons
INSERT INTO lessons (chapter_id, title, lesson_type, video_url, content, duration, lesson_order, created_at) VALUES
(1, 'What is AI Image Generation?', 'text', NULL, '<h2>AI Image Generation</h2><p>AI image generation uses machine learning models to create images from text descriptions.</p>', 10, 1, NOW()),
(1, 'History of AI Art', 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', NULL, 15, 2, NOW()),
(1, 'Popular AI Tools Overview', 'text', NULL, '<h2>Top AI Image Tools</h2><p>Explore DALL-E, Midjourney, Stable Diffusion and more.</p>', 12, 3, NOW()),

(2, 'Understanding Prompts', 'text', NULL, '<h2>Writing Prompts</h2><p>Learn the fundamentals of writing effective AI prompts.</p>', 10, 1, NOW()),
(2, 'Advanced Prompt Techniques', 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', NULL, 20, 2, NOW()),
(2, 'Style and Composition', 'text', NULL, '<h2>Style Guidelines</h2><p>Master different artistic styles and compositions.</p>', 15, 3, NOW()),

(3, 'Image to Image Generation', 'text', NULL, '<h2>Img2Img</h2><p>Transform existing images using AI.</p>', 12, 1, NOW()),
(3, 'Inpainting and Outpainting', 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', NULL, 18, 2, NOW()),
(3, 'Commercial Use Guidelines', 'text', NULL, '<h2>Licensing</h2><p>Understanding rights and commercial usage of AI art.</p>', 10, 3, NOW());