<?php
/**
 * APPOLIOS - Admin Edit Course Page
 */
$adminSidebarActive = 'manage-courses';

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
                    <a href="<?= APP_ENTRY ?>?url=admin/manage-courses" class="btn btn-outline" style="padding: 10px 20px;">← Back to Courses</a>
                </div>

                <div class="form-container" style="width: min(100%, 900px); max-width: 900px;">
                    <form action="<?= APP_ENTRY ?>?url=admin/update-course/<?= $course['id'] ?>" method="POST" id="courseForm" enctype="multipart/form-data">

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

                            <div class="form-group">
                                <label for="image">Course Image</label>
                                <input type="file" name="course_image" id="courseImage" accept="image/*" onchange="handleCourseImage(this)" style="display: none;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <button type="button" onclick="document.getElementById('courseImage').click()" style="background: #3b82f6; color: white; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                                        Choose Image
                                    </button>
                                    <span id="imageFilename" style="font-size: 0.85rem; color: #6b7280;"><?= !empty($course['image']) ? basename($course['image']) : 'No file chosen' ?></span>
                                </div>
                                <input type="hidden" name="image" id="imageUrl" value="<?= htmlspecialchars($course['image'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label for="price">Course Price (USD)</label>
                                <input type="number" step="0.01" name="price" id="price" placeholder="0.00" value="<?= htmlspecialchars($course['price'] ?? '0.00') ?>" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
                                <small style="color: #6b7280; font-size: 0.85rem;">Enter price in USD. Enter 0.00 for free courses.</small>
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
                                                                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
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
                                                                    
                                                                    <div style="flex: 0 0 80px;">
                                                                        <button type="button" onclick="this.closest('.lesson-block').remove()" style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer; margin-top: 20px;">X</button>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="lesson-text-fields" style="width: 100%; display: <?= (($lesson['lesson_type'] ?? 'text') === 'text' || ($lesson['lesson_type'] ?? 'text') === 'both') ? 'block' : 'none' ?>;">
                                                                    <div class="form-group" style="margin-bottom: 0.5rem;">
                                                                        <label style="font-size: 0.85rem;">Text Content</label>
                                                                        <div id="quill-edit-<?= $chapterIndex ?>-<?= $lessonIndex ?>" style="background: white; min-height: 200px;"><?= $lesson['content'] ?? '' ?></div>
                                                                        <input type="hidden" name="chapters[<?= $chapterIndex ?>][lessons][<?= $lessonIndex ?>][content]" id="quill-input-<?= $chapterIndex ?>-<?= $lessonIndex ?>" value="<?= htmlspecialchars($lesson['content'] ?? '') ?>">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="lesson-pdf-fields" style="width: 100%; display: <?= (($lesson['lesson_type'] ?? 'text') === 'pdf' || ($lesson['lesson_type'] ?? 'text') === 'both') ? 'block' : 'none' ?>;">
                                                                    <div class="form-group" style="margin-bottom: 0.5rem;">
                                                                        <label style="font-size: 0.85rem;">Upload PDF</label>
                                                                        <input type="file" name="chapters[<?= $chapterIndex ?>][lessons][<?= $lessonIndex ?>][pdf_file]" accept=".pdf" style="padding: 8px;">
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
let quillInstancesEdit = {};

function handleCourseImage(input) {
    const file = input.files[0];
    if (file && !file.type.startsWith('image/')) {
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

function addChapter() {
    document.getElementById('no-chapters').style.display = 'none';
    
    const chapterHtml = `
        <div class="chapter-block" style="background: #f9fafb; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="margin: 0; color: #374151;">Chapter \${chapterCount + 1}</h4>
                <button type="button" onclick="this.closest('.chapter-block').remove(); checkNoChapters()" style="background: #ef4444; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer;">Remove</button>
            </div>
            
            <div class="form-group">
                <label>Chapter Title *</label>
                <input type="text" name="chapters[\${chapterCount}][title]" placeholder="e.g., Getting Started" required>
            </div>
            
            <div class="form-group">
                <label>Chapter Description</label>
                <textarea name="chapters[\${chapterCount}][description]" placeholder="Brief description of this chapter"></textarea>
            </div>

            <div style="margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label style="font-weight: 600; color: #374151;">Lessons</label>
                    <button type="button" onclick="addLesson(this, \${chapterCount})" style="background: #10b981; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem;">+ Add Lesson</button>
                </div>
                <div class="lessons-container">
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('chapters-container').insertAdjacentHTML('beforeend', chapterHtml);
    chapterCount++;
}

function addLesson(button, chapterIndex) {
    const container = button.closest('.chapter-block').querySelector('.lessons-container');
    const lessonCount = container.children.length;
    
    const lessonHtml = `
        <div class="lesson-block" style="background: white; border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; border: 1px solid #e5e7eb;">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <div style="flex: 1; min-width: 200px;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Lesson Title *</label>
                        <input type="text" name="chapters[\${chapterIndex}][lessons][\${lessonCount}][title]" placeholder="Lesson title" required style="padding: 8px;">
                    </div>
                </div>
                
                <div style="flex: 1; min-width: 150px;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Type</label>
                        <select name="chapters[\${chapterIndex}][lessons][\${lessonCount}][lesson_type]" onchange="toggleLessonFieldsNew(this)" style="padding: 8px; border-radius: 6px; width: 100%;">
                            <option value="text" selected>Text/Article</option>
                            <option value="pdf">PDF Document</option>
                            <option value="both">Text + PDF</option>
                        </select>
                    </div>
                </div>
                
                <div style="flex: 0 0 80px;">
                    <button type="button" onclick="this.closest('.lesson-block').remove()" style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer; margin-top: 20px;">X</button>
                </div>
            </div>
            
            <div style="flex: 1; min-width: 200px;" class="field-text">
                <div class="form-group" style="margin-bottom: 0.5rem;">
                    <label style="font-size: 0.85rem;">Text Content</label>
                    <div id="quill-new-\${chapterIndex}-\${lessonCount}" style="background: white; min-height: 150px;"></div>
                    <input type="hidden" name="chapters[\${chapterIndex}][lessons][\${lessonCount}][content]" id="quill-input-new-\${chapterIndex}-\${lessonCount}" value="">
                </div>
            </div>
            
            <div style="flex: 1; min-width: 200px; display: none;" class="field-pdf">
                <div class="form-group" style="margin-bottom: 0.5rem;">
                    <label style="font-size: 0.85rem;">Upload PDF</label>
                    <input type="file" name="chapters[\${chapterIndex}][lessons][\${lessonCount}][pdf_file]" accept=".pdf" style="padding: 8px;">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', lessonHtml);
    
    // Initialize Quill for new lesson
    setTimeout(() => {
        const quillId = 'quill-new-' + chapterIndex + '-' + lessonCount;
        const quillContainer = document.getElementById(quillId);
        if (quillContainer && !quillContainer.classList.contains('ql-container')) {
            quillInstancesEdit[quillId] = new Quill('#' + quillId, {
                theme: 'snow',
                placeholder: 'Write your lesson content here...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'header': [1, 2, 3, false] }],
                        ['link'],
                        ['clean']
                    ]
                }
            });
            
            quillInstancesEdit[quillId].on('text-change', function() {
                const input = document.getElementById('quill-input-' + quillId.replace('quill-new-', ''));
                if (input) input.value = quillInstancesEdit[quillId].root.innerHTML;
            });
        }
    }, 100);
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

    const textFields = block.querySelector('.lesson-text-fields');
    const pdfFields = block.querySelector('.lesson-pdf-fields');
    if (textFields) textFields.style.display = (type === 'text' || type === 'both') ? 'block' : 'none';
    if (pdfFields) pdfFields.style.display = (type === 'pdf' || type === 'both') ? 'block' : 'none';
}

function checkNoChapters() {
    const container = document.getElementById('chapters-container');
    const noChapters = document.getElementById('no-chapters');
    if (container.children.length === 0) {
        noChapters.style.display = 'block';
    }
}

function syncQuillContent() {
    for (const [quillId, quill] of Object.entries(quillInstancesEdit)) {
        const input = document.getElementById(quillId.replace('quill-edit-', 'quill-input-'));
        if (input) {
            input.value = quill.root.innerHTML;
        }
    }
}

function validateFormEdit() {
    syncQuillContent();
    return true;
}

function initQuillEdit(type, chapterIndex, lessonIndex) {
    const quillId = 'quill-edit-' + chapterIndex + '-' + lessonIndex;
    
    if ((type === 'text' || type === 'both') && !quillInstancesEdit[quillId]) {
        setTimeout(() => {
            const quillContainer = document.getElementById(quillId);
            if (quillContainer && !quillContainer.classList.contains('ql-container')) {
                quillInstancesEdit[quillId] = new Quill('#' + quillId, {
                    theme: 'snow',
                    placeholder: 'Write your lesson content here...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'header': [1, 2, 3, false] }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });
                
                quillInstancesEdit[quillId].on('text-change', function() {
                    const input = document.getElementById('quill-input-' + chapterIndex + '-' + lessonIndex);
                    if (input) input.value = quillInstancesEdit[quillId].root.innerHTML;
                });
            }
        }, 100);
    }
}

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

const categoryTypes = <?= json_encode($categoryTypesJson) ?>;

// Initialize Quill on page load
window.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($chapters)): ?>
        <?php foreach ($chapters as $chapterIndex => $chapter): ?>
            <?php if (!empty($chapter['lessons'])): ?>
                <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                    initQuillEdit('<?= $lesson['lesson_type'] ?? 'text' ?>', <?= $chapterIndex ?>, <?= $lessonIndex ?>);
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
});
</script>