<?php
/**
 * APPOLIOS - Teacher Edit Course Page
 */

$teacherSidebarActive = 'courses';

$chapters = $course['chapters'] ?? [];

require_once 'C:/xampp/htdocs/Appolios-feature-user/Appolios-feature-user/Model/Category.php';
$categoryModel = new Category();
$categoryTypesJson = [];
$categories = $categoryModel->getAll();
foreach ($categories as $cat) {
    $categoryTypesJson[$cat['id']] = [
        'name' => $cat['name'],
        'types' => $categoryModel->getTypesArray($cat)
    ];
}
?>

<!-- Quill styles and script -->
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.js"></script>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>Edit Course</h1>
                        <p>Update course content</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=teacher/courses" class="btn btn-outline" style="padding: 10px 20px;">← Back to Courses</a>
                </div>

                <div class="form-container" style="width: min(100%, 900px); max-width: 900px;">
                    <form action="<?= APP_ENTRY ?>?url=teacher/update-course/<?= $course['id'] ?>" method="POST" novalidate id="courseForm" enctype="multipart/form-data">
                        <!-- Course Basic Info -->
                        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e5e7eb;">
                            <h3 style="margin: 0 0 1rem 0; color: #1f2937;">Course Information</h3>
                            
                            <div class="form-group">
                                <label for="title">Course Title *</label>
                                <input type="text" id="title" name="title" placeholder="Enter course title" value="<?= htmlspecialchars($course['title']) ?>">
                            </div>

                            <div class="form-group">
                                <label for="description">Course Description *</label>
                                <textarea id="description" name="description" placeholder="Describe what students will learn" style="min-height: 120px;"><?= htmlspecialchars($course['description']) ?></textarea>
                            </div>

<div class="form-group">
                                <label for="category_id">Category *</label>
                                <select id="category_id" name="category_id" onchange="loadCourseTypes()" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem;">
                                    <option value="">Select a category...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (($course['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group" id="courseTypeField" style="<?= empty($course['course_type']) ? 'display: none;' : '' ?>">
                                <label for="course_type">Course Type *</label>
                                <select id="course_type" name="course_type" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem;">
                                    <option value="">Select a course type...</option>
                                    <?php if (!empty($course['category_id']) && !empty($categoryTypesJson[$course['category_id']]['types'])): ?>
                                        <?php foreach ($categoryTypesJson[$course['category_id']]['types'] as $type): ?>
                                            <option value="<?= htmlspecialchars($type) ?>" <?= ($course['course_type'] ?? '') === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            </div>

                            <div class="form-group">
                                <label for="image">Course Image</label>
                                <input type="file" id="courseImage" accept="image/*" onchange="handleCourseImageEdit(this)" style="display: none;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <button type="button" onclick="document.getElementById('courseImage').click()" style="background: #3b82f6; color: white; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                                        Choose Image
                                    </button>
                                    <span id="imageFilename" style="font-size: 0.85rem; color: #6b7280;"><?= !empty($course['image']) ? basename($course['image']) : 'No file chosen' ?></span>
                                </div>
                                <input type="hidden" name="image" id="imageUrl" value="<?= htmlspecialchars($course['image'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Chapters & Lessons -->
                        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h3 style="margin: 0; color: #1f2937;">Chapters & Lessons</h3>
                                <button type="button" onclick="addChapter()" style="background: #3b82f6; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">+ Add Chapter</button>
                            </div>

                            <div id="chapters-container">
                                <?php if (!empty($chapters)): ?>
                                    <?php foreach ($chapters as $chapterIndex => $chapter): ?>
                                        <div class="chapter-block" style="background: #f9fafb; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e5e7eb;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                                <h4 style="margin: 0; color: #374151;">Chapter <?= $chapterIndex + 1 ?>: <?= htmlspecialchars($chapter['title']) ?></h4>
                                                <button type="button" onclick="this.closest('.chapter-block').remove(); checkNoChapters()" style="background: #ef4444; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer;">Remove</button>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Chapter Title *</label>
                                                <input type="text" name="chapters[<?= $chapterIndex ?>][title]" value="<?= htmlspecialchars($chapter['title']) ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Chapter Description</label>
                                                <textarea name="chapters[<?= $chapterIndex ?>][description]"><?= htmlspecialchars($chapter['description'] ?? '') ?></textarea>
                                            </div>

                                            <div style="margin-top: 1rem;">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                                    <label style="font-weight: 600; color: #374151;">Lessons</label>
                                                    <button type="button" onclick="addLesson(this, <?= $chapterIndex ?>)" style="background: #10b981; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem;">+ Add Lesson</button>
                                                </div>
                                                <div class="lessons-container">
                                                    <?php if (!empty($chapter['lessons'])): ?>
                                                        <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                                                            <div class="lesson-block" style="background: white; border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; border: 1px solid #e5e7eb;">
                                                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                                                    <div style="flex: 1; min-width: 200px;">
                                                                        <div class="form-group" style="margin-bottom: 0.5rem;">
                                                                            <label style="font-size: 0.85rem;">Lesson Title *</label>
                                                                            <input type="text" name="chapters[<?= $chapterIndex ?>][lessons][<?= $lessonIndex ?>][title]" value="<?= htmlspecialchars($lesson['title']) ?>" required style="padding: 8px;">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div style="flex: 1; min-width: 150px;">
                                                                        <div class="form-group" style="margin-bottom: 0.5rem;">
                                                                            <label style="font-size: 0.85rem;">Type</label>
                                                                            <select name="chapters[<?= $chapterIndex ?>][lessons][<?= $lessonIndex ?>][lesson_type]" onchange="toggleLessonFields(this)" style="padding: 8px; border-radius: 6px; width: 100%;">
                                                                                <option value="text" <?= ($lesson['lesson_type'] ?? 'text') === 'text' ? 'selected' : '' ?>>Text/Article</option>
                                                                                <option value="pdf" <?= ($lesson['lesson_type'] ?? '') === 'pdf' ? 'selected' : '' ?>>PDF Document</option>
                                                                                <option value="both" <?= ($lesson['lesson_type'] ?? '') === 'both' ? 'selected' : '' ?>>Text + PDF</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="lesson-text-fields" style="flex: 1; min-width: 200px;">
                                                                        <div class="form-group" style="margin-bottom: 0.5rem;">
                                                                            <label style="font-size: 0.85rem;">Text Content</label>
                                                                            <textarea name="chapters[<?= $chapterIndex ?>][lessons][<?= $lessonIndex ?>][content]" placeholder="Lesson text content..." style="padding: 8px; min-height: 60px;"><?= htmlspecialchars($lesson['content'] ?? '') ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="lesson-pdf-fields" style="flex: 1; min-width: 200px; display: none;">
                                                                        <div class="form-group" style="margin-bottom: 0.5rem;">
                                                                            <label style="font-size: 0.85rem;">Upload PDF</label>
                                                                            <input type="file" name="lessons[<?= $chapterIndex ?>][<?= $lessonIndex ?>][pdf_file]" accept=".pdf" style="padding: 8px;">
                                                                            <?php if (!empty($lesson['pdf_path'])): ?>
                                                                                <p style="font-size: 0.75rem; color: #16a34a;">Current: <?= basename($lesson['pdf_path']) ?></p>
                                                                                <input type="hidden" name="chapters[<?= $chapterIndex ?>][lessons][<?= $lessonIndex ?>][pdf_path]" value="<?= htmlspecialchars($lesson['pdf_path']) ?>">
                                                                            <?php endif; ?>
                                                                        </div>
</div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div id="no-chapters" style="text-align: center; padding: 2rem; color: #6b7280; border: 2px dashed #e5e7eb; border-radius: 12px; <?= !empty($chapters) ? 'display: none;' : '' ?>">
                                Click "Add Chapter" to add course content
                            </div>
                        </div>

                        <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px; padding: 14px;" onclick="return validateFormEdit()">Update Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let chapterCount = <?= count($chapters) ?>;

function addChapter() {
    document.getElementById('no-chapters').style.display = 'none';
    
    const chapterHtml = `
        <div class="chapter-block" style="background: #f9fafb; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="margin: 0; color: #374151;">Chapter ${chapterCount + 1}</h4>
                <button type="button" onclick="this.closest('.chapter-block').remove(); checkNoChapters()" style="background: #ef4444; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer;">Remove</button>
            </div>
            
            <div class="form-group">
                <label>Chapter Title *</label>
                <input type="text" name="chapters[${chapterCount}][title]" placeholder="e.g., Getting Started" required>
            </div>
            
            <div class="form-group">
                <label>Chapter Description</label>
                <textarea name="chapters[${chapterCount}][description]" placeholder="Brief description of this chapter"></textarea>
            </div>

            <div style="margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label style="font-weight: 600; color: #374151;">Lessons</label>
                    <button type="button" onclick="addLesson(this, ${chapterCount})" style="background: #10b981; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem;">+ Add Lesson</button>
                </div>
                <div class="lessons-container">
                    <!-- Lessons will be added here -->
                </div>
            </div>
        </div>
    `;
    
    const container = document.getElementById('chapters-container');
    container.insertAdjacentHTML('beforeend', chapterHtml);
    chapterCount++;
}

function addLesson(button, chapterIndex) {
    const container = button.closest('.chapter-block').querySelector('.lessons-container');
    const lessonCount = container.children.length;
    
    const lessonHtml = `
        <div class="lesson-block" style="background: white; border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; border: 1px solid #e5e7eb;">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Lesson Title *</label>
                        <input type="text" name="chapters[${chapterIndex}][lessons][${lessonCount}][title]" placeholder="Lesson title" required style="padding: 8px;">
                    </div>
                </div>
                
                <div style="flex: 1; min-width: 150px;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Type</label>
                        <select name="chapters[${chapterIndex}][lessons][${lessonCount}][lesson_type]" onchange="toggleLessonFieldsNew(this)" style="padding: 8px; border-radius: 6px; width: 100%;">
                            <option value="text" selected>Text/Article</option>
                            <option value="pdf">PDF Document</option>
                            <option value="both">Text + PDF</option>
                        </select>
                    </div>
                </div>
                
                <div style="flex: 1; min-width: 200px;" class="field-text">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Text Content</label>
                        <textarea name="chapters[${chapterIndex}][lessons][${lessonCount}][content]" placeholder="Lesson text content..." style="padding: 8px; min-height: 60px;"></textarea>
                    </div>
                </div>
                
                <div style="flex: 1; min-width: 200px; display: none;" class="field-pdf">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Upload PDF</label>
                        <input type="file" name="lessons[${chapterIndex}][${lessonCount}][pdf_file]" accept=".pdf" style="padding: 8px;">
                    </div>
                </div>
                
                <div style="flex: 0 0 80px;">
                    <button type="button" onclick="this.closest('.lesson-block').remove()" style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer; margin-top: 20px;">X</button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', lessonHtml);
}

function toggleLessonFieldsNew(select) {
    const block = select.closest('.lesson-block');
    const type = select.value;
    
    block.querySelector('.field-text').style.display = (type === 'text' || type === 'both') ? 'block' : 'none';
    block.querySelector('.field-pdf').style.display = (type === 'pdf' || type === 'both') ? 'block' : 'none';
}

function toggleLessonFields(select) {
    const block = select.closest('.lesson-block');
    const type = select.value;
    
    block.querySelector('.lesson-text-fields').style.display = (type === 'text' || type === 'both') ? 'block' : 'none';
block.querySelector('.lesson-pdf-fields').style.display = (type === 'pdf' || type === 'both') ? 'block' : 'none';
}

function validateFormEdit() {
    syncQuillContent();
    let isValid = true;
    
    document.querySelectorAll('.field-error').forEach(el => el.remove());
    document.querySelectorAll('.error-border').forEach(el => {
        el.classList.remove('error-border');
        el.style.borderColor = '';
    });
    
    // Validate chapters exist
    const noChapters = document.getElementById('no-chapters');
    const chapterBlocks = document.querySelectorAll('.chapter-block');
    if (chapterBlocks.length === 0 && noChapters) {
        noChapters.style.borderColor = '#ef4444';
        noChapters.style.color = '#ef4444';
        noChapters.textContent = 'Please add at least one chapter';
        isValid = false;
    }
    
    chapterBlocks.forEach((chapter, idx) => {
        const chTitle = chapter.querySelector('input[name$="[title]"]');
        if (!chTitle.value.trim()) {
            showFieldErrorEdit(chTitle, 'Chapter title is required');
            isValid = false;
        }
        
        const lessons = chapter.querySelectorAll('.lesson-block');
        lessons.forEach((lesson, lIdx) => {
            const lTitle = lesson.querySelector('input[name$="[title]"]');
            if (!lTitle.value.trim()) {
                showFieldErrorEdit(lTitle, 'Lesson title is required');
                isValid = false;
            }
            
            const typeSelect = lesson.querySelector('select[name$="[lesson_type]"]');
            const type = typeSelect.value;
            
            if (type === 'video') {
                const videoUrl = lesson.querySelector('input[name$="[video_url]"]');
                if (videoUrl && !videoUrl.value.trim()) {
                    showFieldErrorEdit(videoUrl, 'Video URL is required');
                    isValid = false;
                }
            } else if (type === 'text') {
                const textContent = lesson.querySelector('input[name$="[text_content]"]');
                if (textContent && !textContent.value.trim()) {
                    showFieldErrorEdit(textContent, 'Text content is required');
                    isValid = false;
                }
            } else if (type === 'pdf') {
                const pdfUrl = lesson.querySelector('input[name$="[pdf_url]"]');
                if (pdfUrl && !pdfUrl.value.trim()) {
                    showFieldErrorEdit(pdfUrl, 'PDF URL is required');
                    isValid = false;
                }
            }
        });
    });
    
    if (!isValid) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    return isValid;
}

function showFieldErrorEdit(input, message) {
    const parent = input.closest('.form-group') || input.parentElement;
    if (!parent) return;
    const error = document.createElement('small');
    error.className = 'field-error';
    error.style.color = '#ef4444';
    error.style.fontSize = '0.85rem';
    error.style.display = 'block';
    error.style.marginTop = '4px';
    error.textContent = message;
    parent.appendChild(error);
    input.classList.add('error-border');
    input.style.borderColor = '#ef4444';
}
</script>

<script>
const categoryTypes = <?= json_encode($categoryTypesJson) ?>;

function loadCourseTypes() {
    const categorySelect = document.getElementById('category_id');
    const typeSelect = document.getElementById('course_type');
    const typeField = document.getElementById('courseTypeField');
    const categoryId = categorySelect.value;
    
    typeSelect.innerHTML = '<option value="">Select a course type...</option>';
    
    if (categoryId && categoryTypes[categoryId] && categoryTypes[categoryId].types.length > 0) {
        typeField.style.display = 'block';
        categoryTypes[categoryId].types.forEach(function(type) {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            typeSelect.appendChild(option);
        });
    } else {
        typeField.style.display = 'none';
    }
}

let quillInstancesEdit = {};
console.log('Quill loaded:', typeof Quill);

function handlePdfUploadEdit(input, chapterIndex, lessonCount) {
    const file = input.files[0];
    if (file) {
        if (file.type !== 'application/pdf') {
            alert('Please select a PDF file');
            input.value = '';
            return;
        }
        
        const filenameSpan = input.parentElement.querySelector('.pdf-filename');
        if (filenameSpan) {
            filenameSpan.textContent = file.name;
        }
        
        const hiddenInput = document.getElementById('pdf-url-edit-' + chapterIndex + '-' + lessonCount);
        if (hiddenInput) {
            hiddenInput.value = file.name;
        }
    }
}

function handleCourseImageEdit(input) {
    const file = input.files[0];
    if (file) {
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            input.value = '';
            return;
        }
        
        document.getElementById('imageFilename').textContent = file.name;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imageUrl').value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>