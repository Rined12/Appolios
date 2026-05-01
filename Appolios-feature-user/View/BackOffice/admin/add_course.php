<?php
/**
 * APPOLIOS - Admin Add Course Page
 */
$adminSidebarActive = 'add-course';
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

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
                        <h1>Add New Course</h1>
                        <p>Create a course with chapters and lessons</p>
                    </div>
                    <a href="<?= APP_ENTRY ?>?url=admin/manage-courses" class="btn btn-outline" style="padding: 10px 20px;">← Back to Courses</a>
                </div>

                <div class="form-container" style="width: min(100%, 900px); max-width: 900px;">
                    <form action="<?= APP_ENTRY ?>?url=admin/store-course" method="POST" id="courseForm" enctype="multipart/form-data">

<!-- Course Basic Info -->
                        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e5e7eb;">
                            <h3 style="margin: 0 0 1rem 0; color: #1f2937;">Course Information</h3>
                            
                            <div class="form-group">
                                <label for="title">Course Title *</label>
                                <input type="text" id="title" name="title" placeholder="Enter course title" value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label for="description">Course Description *</label>
                                <textarea id="description" name="description" placeholder="Describe what students will learn" style="min-height: 120px;"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category *</label>
                                <div style="position: relative;">
                                    <select id="category_id" name="category_id" onchange="loadCourseTypes()" style="width: 100%; padding: 14px 40px 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; background: #f8fafc; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none; transition: all 0.3s; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><path fill="%2394a3b8" d="M6 9L1 4h10z"/></svg>'); background-repeat: no-repeat; background-position: right 15px center;"
                                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'; this.style.background='white'"
                                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.background='#f8fafc'">
                                        <option value="">Select a category...</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (($old['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="courseTypeField" style="display: none;">
                                <label for="course_type">Course Type *</label>
                                <div style="position: relative;">
                                    <select id="course_type" name="course_type" style="width: 100%; padding: 14px 40px 14px 16px; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 1rem; background: #f8fafc; cursor: pointer; appearance: none; -webkit-appearance: none; -moz-appearance: none; transition: all 0.3s; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12"><path fill="%2394a3b8" d="M6 9L1 4h10z"/></svg>'); background-repeat: no-repeat; background-position: right 15px center;"
                                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'; this.style.background='white'"
                                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'; this.style.background='#f8fafc'">
                                        <option value="">Select a course type...</option>
                                    </select>
                                </div>
                                <?php if (isset($errors['course_type'])): ?><small style="color: #ef4444; font-size: 0.85rem; margin-top: 4px; display: block;"><?= htmlspecialchars($errors['course_type']) ?></small><?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="image">Course Image</label>
                                <input type="file" name="course_image" id="courseImage" accept="image/*" onchange="handleCourseImage(this)" style="display: none;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <button type="button" onclick="document.getElementById('courseImage').click()" style="background: #3b82f6; color: white; padding: 10px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">
                                        Choose Image
                                    </button>
                                    <span id="imageFilename" style="font-size: 0.85rem; color: #6b7280;">No file chosen</span>
                                </div>
                                <input type="hidden" name="image" id="imageUrl" value="">
                            </div>

                            <div class="form-group">
                                <label for="price">Course Price (USD)</label>
                                <input type="number" step="0.01" name="price" id="price" placeholder="0.00" value="<?= htmlspecialchars($old['price'] ?? '0.00') ?>" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem;">
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
                            </div>

                            <div id="no-chapters" style="text-align: center; padding: 2rem; color: #6b7280; border: 2px dashed #e5e7eb; border-radius: 12px;">
                                Click "Add Chapter" to add course content
                            </div>
                        </div>

                        <!-- Course Badges -->
                        <div style="background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h3 style="margin: 0; color: #1f2937;">Course Badges</h3>
                                <button type="button" onclick="addBadge()" style="background: #f59e0b; color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600;">+ Add Badge</button>
                            </div>

                            <div id="badges-container">
                            </div>

                            <div id="no-badges" style="text-align: center; padding: 2rem; color: #6b7280; border: 2px dashed #e5e7eb; border-radius: 12px;">
                                Add badges students can earn by completing this course
                            </div>
                        </div>

                        <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px; padding: 14px;">Create Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.js"></script>

<script>
let chapterCount = 0;
let quillInstances = {};

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
                    <button type="button" onclick="addLesson(this)" style="background: #10b981; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem;">+ Add Lesson</button>
                </div>
                <div class="lessons-container">
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('chapters-container').insertAdjacentHTML('beforeend', chapterHtml);
    chapterCount++;
}

function addLesson(button) {
    const container = button.closest('.chapter-block').querySelector('.lessons-container');
    const lessonCount = container.children.length;
    const chapterIndex = chapterCount - 1;
    
    const lessonHtml = `
        <div class="lesson-block" style="background: white; border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; border: 1px solid #e5e7eb;">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <div style="flex: 1; min-width: 200px;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Lesson Title *</label>
                        <input type="text" name="chapters[${chapterIndex}][lessons][${lessonCount}][title]" placeholder="Lesson title" required style="padding: 8px;">
                    </div>
                </div>
                
                <div style="flex: 1; min-width: 150px;">
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem;">Type</label>
                        <select name="chapters[${chapterIndex}][lessons][${lessonCount}][lesson_type]" onchange="toggleLessonFieldsNew(this, ${chapterIndex}, ${lessonCount})" style="padding: 8px; border-radius: 6px; width: 100%;">
                            <option value="text" selected>Text/Article</option>
                            <option value="pdf">PDF Document</option>
                            <option value="both">Text + PDF</option>
                        </select>
                    </div>
                </div>
                
                <div style="flex: 0 0 80px; display: flex; align-items: flex-end;">
                    <button type="button" onclick="this.closest('.lesson-block').remove()" style="background: #ef4444; color: white; padding: 8px 12px; border-radius: 4px; border: none; cursor: pointer; margin-bottom: 8px;">X</button>
                </div>
            </div>
            
            <div style="flex: 1; min-width: 200px; display: none;" class="field-text">
                <div class="form-group" style="margin-bottom: 0.5rem;">
                    <label style="font-size: 0.85rem;">Text Content</label>
                    <div id="quill-${chapterIndex}-${lessonCount}" style="height: 250px; background: white;"></div>
                    <input type="hidden" name="chapters[${chapterIndex}][lessons][${lessonCount}][content]" id="quill-input-${chapterIndex}-${lessonCount}">
                </div>
            </div>
            
            <div style="flex: 1; min-width: 200px; display: none;" class="field-pdf">
                <div class="form-group" style="margin-bottom: 0.5rem;">
                    <label style="font-size: 0.85rem;">Upload PDF</label>
                    <input type="file" name="lessons[${chapterIndex}][${lessonCount}][pdf_file]" accept=".pdf" style="padding: 8px;">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', lessonHtml);
}

function toggleLessonFieldsNew(select, chapterIndex, lessonCount) {
    const block = select.closest('.lesson-block');
    const type = select.value;
    const quillId = 'quill-' + chapterIndex + '-' + lessonCount;
    
    block.querySelector('.field-text').style.display = (type === 'text' || type === 'both') ? 'block' : 'none';
    block.querySelector('.field-pdf').style.display = (type === 'pdf' || type === 'both') ? 'block' : 'none';
    
    if ((type === 'text' || type === 'both') && !quillInstances[quillId]) {
        setTimeout(() => {
            const quillContainer = document.getElementById(quillId);
            if (quillContainer && !quillContainer.classList.contains('ql-container')) {
                quillInstances[quillId] = new Quill('#' + quillId, {
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
                
                quillInstances[quillId].on('text-change', function() {
                    const input = document.getElementById('quill-input-' + chapterIndex + '-' + lessonCount);
                    if (input) input.value = quillInstances[quillId].root.innerHTML;
                });
            }
        }, 100);
    }
}

function checkNoChapters() {
    const container = document.getElementById('chapters-container');
    const noChapters = document.getElementById('no-chapters');
    if (container.children.length === 0) {
        noChapters.style.display = 'block';
    }
}

let badgeCount = 0;

function addBadge() {
    document.getElementById('no-badges').style.display = 'none';
    
    const badgeHtml = `
        <div class="badge-block" style="background: #fef3c7; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; border: 2px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="margin: 0; color: #92400e;">Badge ${badgeCount + 1}</h4>
                <button type="button" onclick="this.closest('.badge-block').remove(); checkNoBadges()" style="background: #ef4444; color: white; padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer;">Remove</button>
            </div>
            
            <div class="form-group">
                <label>Badge Name *</label>
                <input type="text" name="badges[${badgeCount}][badge_name]" placeholder="e.g., First Lesson Complete" required>
            </div>
            
            <div class="form-group">
                <label>Badge Icon</label>
                <select name="badges[${badgeCount}][badge_icon]" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem; background: white;">
                    <option value="trophy">🏆 Trophy</option>
                    <option value="star">⭐ Star</option>
                    <option value="medal">🎖️ Medal</option>
                    <option value="certificate">📜 Certificate</option>
                    <option value="rocket">🚀 Rocket</option>
                    <option value="fire">🔥 Fire</option>
                    <option value="check">✅ Check Mark</option>
                    <option value="crown">👑 Crown</option>
                    <option value="gem">💎 Gem</option>
                    <option value="lightning">⚡ Lightning</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Condition</label>
                <select name="badges[${badgeCount}][badge_condition]" style="width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 1rem; background: white;">
                    <option value="completion">Course Completion</option>
                    <option value="first_lesson">First Lesson</option>
                    <option value="chapter_complete">Each Chapter</option>
                    <option value="all_lessons">All Lessons</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="badges[${badgeCount}][description]" placeholder="Description of what this badge represents"></textarea>
            </div>
        </div>
    `;
    
    document.getElementById('badges-container').insertAdjacentHTML('beforeend', badgeHtml);
    badgeCount++;
}

function checkNoBadges() {
    const container = document.getElementById('badges-container');
    const noBadges = document.getElementById('no-badges');
    if (container.children.length === 0) {
        noBadges.style.display = 'block';
    }
}

const categoryTypes = <?= json_encode($categoryTypesJson) ?>;
const courseTypeSelect = document.getElementById('course_type');
const categorySelect = document.getElementById('category_id');
const courseTypeField = document.getElementById('courseTypeField');

function loadCourseTypes() {
    const categoryId = categorySelect.value;
    courseTypeSelect.innerHTML = '<option value="">Select a course type...</option>';
    
    if (categoryId && categoryTypes[categoryId] && categoryTypes[categoryId].types.length > 0) {
        courseTypeField.style.display = 'block';
        categoryTypes[categoryId].types.forEach(function(type) {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            courseTypeSelect.appendChild(option);
        });
    } else {
        courseTypeField.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (categorySelect.value) {
        loadCourseTypes();
        courseTypeSelect.value = '<?= htmlspecialchars($old['course_type'] ?? '') ?>';
    }
});
</script>