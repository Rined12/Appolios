<?php
/**
 * APPOLIOS - Admin Edit Course Page (COUR-QUIZ Style)
 */
$adminSidebarActive = 'courses';
$course = $course ?? [];
$chapters = $course['chapters'] ?? [];
$badges = $badges ?? [];

require_once __DIR__ . '/../../../Controller/CategoryController.php';
$categoryModel = new CategoryController();
$categories = $categoryModel->getAll();
$categoryTypesJson = [];
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

<!-- Header -->
<div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 10px 40px rgba(15, 23, 42, 0.4);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="margin: 0 0 0.5rem 0; font-size: 2rem; color: white; font-weight: 800;">Edit Course</h1>
            <p style="margin: 0; color: rgba(255,255,255,0.9); font-size: 1.1rem;">Update course details, chapters, and lessons</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="<?= APP_ENTRY ?>?url=admin/courses" style="background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 12px 24px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.3s; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);" onmouseover="this.style.background='rgba(255,255,255,0.3)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(0)'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Courses
            </a>
        </div>
    </div>
</div>

<style>
    .form-section {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .form-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .form-group label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.6rem;
        font-size: 0.95rem;
    }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #cbd5e1;
        border-radius: 12px;
        font-size: 1rem;
        background: #ffffff;
        color: #1e293b;
        transition: all 0.3s ease;
    }
    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #94a3b8;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #2B4865;
        background: white;
        box-shadow: 0 0 0 4px rgba(43, 72, 101, 0.15);
    }
    .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0 0 1.5rem 0;
        color: #1e293b;
        font-size: 1.25rem;
        font-weight: 700;
    }
    .section-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .ql-editor {
        font-size: 1rem;
        color: #1e293b !important;
        line-height: 1.6;
    }
    .ql-placeholder {
        color: #94a3b8 !important;
        font-style: normal !important;
    }
</style>

<div style="max-width: 900px; margin: 0 auto; padding-bottom: 3rem;">
    <form action="<?= APP_ENTRY ?>?url=admin/update-course/<?= $course['id'] ?>" method="POST" id="courseForm" enctype="multipart/form-data" onsubmit="syncQuillContent()">

        <!-- Course Basic Info -->
        <div class="form-section">
            <h3 class="section-title">
                <div class="section-icon">📚</div>
                Course Information
            </h3>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="title">Course Title *</label>
                <input type="text" id="title" name="title" placeholder="e.g., Introduction to Web Development" value="<?= htmlspecialchars($course['title'] ?? '') ?>" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="description">Course Description *</label>
                <textarea id="description" name="description" placeholder="Describe what students will learn in this course..." required style="min-height: 140px; resize: vertical;"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" onchange="loadCourseTypes()" style="width: 100%; padding: 14px 18px; border: 2px solid #cbd5e1; border-radius: 12px; font-size: 1rem; background: white;">
                        <option value="">Select a category...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (($course['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="courseTypeField">
                    <label for="course_type">Course Type *</label>
                    <select id="course_type" name="course_type" style="width: 100%; padding: 14px 18px; border: 2px solid #cbd5e1; border-radius: 12px; font-size: 1rem; background: white;">
                        <option value="">Select a type...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Course Price (USD)</label>
                    <input type="number" step="0.01" name="price" id="price" placeholder="0.00" value="<?= htmlspecialchars($course['price'] ?? '0.00') ?>">
                    <small style="color: #6b7280; font-size: 0.85rem; margin-top: 4px; display: block;">💡 Enter 0.00 for free courses</small>
                </div>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
                <label for="image">📸 Course Cover Image</label>
                <input type="file" name="course_image" id="courseImage" accept="image/*" onchange="handleCourseImage(this)" style="display: none;">
                <div style="display: flex; gap: 12px; align-items: center; padding: 16px; background: #f8fafc; border-radius: 12px; border: 2px dashed #cbd5e1;">
                    <button type="button" onclick="document.getElementById('courseImage').click()" style="background: linear-gradient(135deg, #2B4865 0%, #1e293b 100%); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(43, 72, 101, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Change Image
                    </button>
                    <span id="imageFilename" style="font-size: 0.9rem; color: #64748b;"><?= !empty($course['image']) ? basename($course['image']) : 'No file chosen' ?></span>
                </div>
                <input type="hidden" name="image" id="imageUrl" value="<?= htmlspecialchars($course['image'] ?? '') ?>">
            </div>
        </div>

        <!-- Chapters & Lessons -->
        <div class="form-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 class="section-title" style="margin: 0;">
                    <div class="section-icon">📖</div>
                    Chapters & Lessons
                </h3>
                <button type="button" onclick="addChapter()" style="background: linear-gradient(135deg, #2B4865 0%, #1e293b 100%); color: white; padding: 10px 18px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(43, 72, 101, 0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Chapter
                </button>
            </div>

            <div id="chapters-container">
            </div>

            <div id="no-chapters" style="text-align: center; padding: 3rem; color: #94a3b8; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 2px dashed #cbd5e1; border-radius: 16px; display: <?= empty($chapters) ? 'block' : 'none' ?>;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin: 0 auto 1rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M12 6v7"/><path d="M9 9h6"/></svg>
                <p style="margin: 0; font-weight: 500;">Click "Add Chapter" to start building your course content</p>
            </div>
        </div>

        <!-- Course Badges -->
        <div class="form-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 class="section-title" style="margin: 0;">
                    <div class="section-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">🏆</div>
                    Course Badges
                </h3>
                <button type="button" onclick="addBadge()" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 12px 24px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(245, 158, 11, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(245, 158, 11, 0.3)'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Badge
                </button>
            </div>

            <div id="badges-container">
            </div>

            <div id="no-badges" style="text-align: center; padding: 3rem; color: #94a3b8; background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border: 2px dashed #fcd34d; border-radius: 16px; display: <?= empty($badges) ? 'block' : 'none' ?>;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fcd34d" stroke-width="1.5" style="margin: 0 auto 1rem;"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                <p style="margin: 0; font-weight: 500;">Add badges to motivate students as they progress</p>
            </div>
        </div>

        <button type="submit" class="btn btn-yellow btn-block" style="margin-top: 20px; padding: 18px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 16px; font-weight: 700; font-size: 1.1rem; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 30px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(16, 185, 129, 0.3)'">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
            Save Changes
        </button>
    </form>
</div>

<script>
let chapterCount = 0;
let badgeCount = 0;
let quillInstances = {};

function handleCourseImage(input) {
    const file = input.files[0];
    if (file && !file.type.startsWith('image/')) {
        alert('Please select an image file');
        input.value = '';
        return;
    }
    document.getElementById('imageFilename').textContent = file ? file.name : 'No file chosen';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function addChapter(data = null) {
    document.getElementById('no-chapters').style.display = 'none';
    
    const index = chapterCount;
    const title = data ? data.title : '';
    const desc = data ? data.description : '';
    
    const chapterHtml = `
        <div class="chapter-block" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #2B4865 0%, #1e293b 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem;">${chapterCount + 1}</div>
                    <h4 style="margin: 0; color: #1e293b; font-weight: 700; font-size: 1.1rem;">Chapter ${chapterCount + 1}</h4>
                </div>
                <button type="button" onclick="this.closest('.chapter-block').remove(); checkNoChapters()" style="background: #fee2e2; color: #dc2626; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 0.85rem; transition: all 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">Remove</button>
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-size: 0.9rem; color: #475569;">Chapter Title *</label>
                <input type="text" name="chapters[${index}][title]" value="${escapeHtml(title)}" placeholder="e.g., Getting Started" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-size: 0.9rem; color: #475569;">Chapter Description</label>
                <textarea name="chapters[${index}][description]" placeholder="Brief description of this chapter" style="min-height: 80px;">${escapeHtml(desc)}</textarea>
            </div>

            <div style="margin-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <label style="font-weight: 600; color: #475569; font-size: 1rem;">📚 Lessons</label>
                    <button type="button" onclick="addLesson(this, ${index})" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 0.85rem; transition: all 0.3s; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);">+ Add Lesson</button>
                </div>
                <div class="lessons-container" data-chapter-index="${index}" style="display: grid; gap: 1rem;">
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('chapters-container').insertAdjacentHTML('beforeend', chapterHtml);
    
    if (data && data.lessons) {
        const container = document.getElementById('chapters-container').lastElementChild.querySelector('.lessons-container');
        data.lessons.forEach(lesson => {
            addLessonToContainer(container, index, lesson);
        });
    }
    
    chapterCount++;
}

function addLesson(button, chapterIndex) {
    const container = button.closest('.chapter-block').querySelector('.lessons-container');
    addLessonToContainer(container, chapterIndex);
}

function addLessonToContainer(container, chapterIndex, data = null) {
    const lessonCount = container.children.length;
    const title = data ? data.title : '';
    const type = data ? data.lesson_type : 'text';
    const content = data ? data.content : '';
    
    const lessonHtml = `
        <div class="lesson-block" style="background: white; border-radius: 12px; padding: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.3s;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 700;">${lessonCount + 1}</div>
                    <span style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">Lesson ${lessonCount + 1}</span>
                </div>
                <button type="button" onclick="this.closest('.lesson-block').remove()" style="background: #fee2e2; color: #dc2626; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600;">Remove</button>
            </div>
            
            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label style="font-size: 0.85rem; color: #64748b; font-weight: 500;">Lesson Title *</label>
                <input type="text" name="chapters[${chapterIndex}][lessons][${lessonCount}][title]" value="${escapeHtml(title)}" placeholder="e.g., Introduction to Variables" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label style="font-size: 0.85rem; color: #64748b; font-weight: 500;">Lesson Type</label>
                <select name="chapters[${chapterIndex}][lessons][${lessonCount}][lesson_type]" onchange="toggleLessonFieldsNew(this, ${chapterIndex}, ${lessonCount})">
                    <option value="text" ${type === 'text' ? 'selected' : ''}>📝 Text/Article</option>
                    <option value="pdf" ${type === 'pdf' ? 'selected' : ''}>📄 PDF Document</option>
                    <option value="both" ${type === 'both' ? 'selected' : ''}>📝 Text + PDF</option>
                </select>
            </div>
            
            <div class="field-text" style="display: ${ (type === 'text' || type === 'both') ? 'block' : 'none' };">
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <label style="font-size: 0.85rem; color: #1e293b; font-weight: 500;">Text Content</label>
                    <div id="quill-${chapterIndex}-${lessonCount}" style="height: 250px; background: white; border-radius: 8px; border: 1px solid #cbd5e1;"></div>
                    <input type="hidden" name="chapters[${chapterIndex}][lessons][${lessonCount}][content]" id="quill-input-${chapterIndex}-${lessonCount}" value="${escapeHtml(content)}">
                </div>
            </div>
            
            <div class="field-pdf" style="display: ${ (type === 'pdf' || type === 'both') ? 'block' : 'none' };">
                <div class="form-group">
                    <label style="font-size: 0.85rem; color: #64748b; font-weight: 500;">Upload PDF</label>
                    <input type="file" name="lessons[${chapterIndex}][${lessonCount}][pdf_file]" accept=".pdf">
                    ${data && data.pdf_path ? `<div style="margin-top: 8px; font-size: 0.85rem; color: #10b981; display: flex; align-items: center; gap: 5px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Current: ${data.pdf_path.split('/').pop()}
                    </div>` : ''}
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', lessonHtml);
    
    if (type === 'text' || type === 'both') {
        setTimeout((cIdx, lIdx, content) => {
            initQuillForLesson(cIdx, lIdx, content);
        }, 100, chapterIndex, lessonCount, content);
    }
}

function initQuillForLesson(chapterIndex, lessonCount, content = '') {
    const quillId = 'quill-' + chapterIndex + '-' + lessonCount;
    const quillContainer = document.getElementById(quillId);
    if (quillContainer && !quillInstances[quillId]) {
        try {
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
            if (content) {
                quillInstances[quillId].root.innerHTML = content;
                document.getElementById('quill-input-' + chapterIndex + '-' + lessonCount).value = content;
            }
            
            quillInstances[quillId].on('text-change', function() {
                const input = document.getElementById('quill-input-' + chapterIndex + '-' + lessonCount);
                if (input) input.value = quillInstances[quillId].root.innerHTML;
            });
        } catch (err) {
            console.error('Error initializing Quill for', quillId, err);
        }
    }
}

function toggleLessonFieldsNew(select, chapterIndex, lessonCount) {
    const block = select.closest('.lesson-block');
    const type = select.value;
    const quillId = 'quill-' + chapterIndex + '-' + lessonCount;
    
    block.querySelector('.field-text').style.display = (type === 'text' || type === 'both') ? 'block' : 'none';
    block.querySelector('.field-pdf').style.display = (type === 'pdf' || type === 'both') ? 'block' : 'none';
    
    if ((type === 'text' || type === 'both') && !quillInstances[quillId]) {
        initQuillForLesson(chapterIndex, lessonCount);
    }
}

function checkNoChapters() {
    const container = document.getElementById('chapters-container');
    const noChapters = document.getElementById('no-chapters');
    noChapters.style.display = (container.children.length === 0) ? 'block' : 'none';
}

function addBadge(data = null) {
    document.getElementById('no-badges').style.display = 'none';
    
    const index = badgeCount;
    const name = data ? data.badge_name : '';
    const icon = data ? data.badge_icon : 'trophy';
    const cond = data ? data.badge_condition : 'completion';
    const desc = data ? data.description : '';
    
    const badgeHtml = `
        <div class="badge-block" style="background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border-radius: 16px; padding: 1.5rem; margin-bottom: 1rem; border: 2px solid #fbbf24; box-shadow: 0 2px 10px rgba(251, 191, 36, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(251, 191, 36, 0.3);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">🏆</div>
                    <h4 style="margin: 0; color: #92400e; font-weight: 700; font-size: 1.1rem;">Badge ${badgeCount + 1}</h4>
                </div>
                <button type="button" onclick="this.closest('.badge-block').remove(); checkNoBadges()" style="background: #fef3c7; color: #b45309; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 0.85rem; transition: all 0.2s;">Remove</button>
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="font-size: 0.9rem; color: #78350f; font-weight: 500;">Badge Name *</label>
                <input type="text" name="badges[${index}][badge_name]" value="${escapeHtml(name)}" placeholder="e.g., First Steps Champion" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="font-size: 0.9rem; color: #78350f; font-weight: 500;">Badge Icon</label>
                    <select name="badges[${index}][badge_icon]">
                        <option value="trophy" ${icon === 'trophy' ? 'selected' : ''}>🏆 Trophy</option>
                        <option value="star" ${icon === 'star' ? 'selected' : ''}>⭐ Star</option>
                        <option value="medal" ${icon === 'medal' ? 'selected' : ''}>🎖️ Medal</option>
                        <option value="certificate" ${icon === 'certificate' ? 'selected' : ''}>📜 Certificate</option>
                        <option value="rocket" ${icon === 'rocket' ? 'selected' : ''}>🚀 Rocket</option>
                        <option value="fire" ${icon === 'fire' ? 'selected' : ''}>🔥 Fire</option>
                        <option value="check" ${icon === 'check' ? 'selected' : ''}>✅ Check</option>
                        <option value="crown" ${icon === 'crown' ? 'selected' : ''}>👑 Crown</option>
                        <option value="gem" ${icon === 'gem' ? 'selected' : ''}>💎 Gem</option>
                        <option value="lightning" ${icon === 'lightning' ? 'selected' : ''}>⚡ Lightning</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="font-size: 0.9rem; color: #78350f; font-weight: 500;">Condition</label>
                    <select name="badges[${index}][badge_condition]">
                        <option value="completion" ${cond === 'completion' ? 'selected' : ''}>🎓 Course Completion</option>
                        <option value="first_lesson" ${cond === 'first_lesson' ? 'selected' : ''}>🚀 First Lesson</option>
                        <option value="chapter_complete" ${cond === 'chapter_complete' ? 'selected' : ''}>📖 Each Chapter</option>
                        <option value="all_lessons" ${cond === 'all_lessons' ? 'selected' : ''}>✅ All Lessons</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.9rem; color: #78350f; font-weight: 500;">Description</label>
                <textarea name="badges[${index}][description]" placeholder="What students need to do to earn this badge..." style="min-height: 80px;">${escapeHtml(desc)}</textarea>
            </div>
        </div>
    `;
    
    document.getElementById('badges-container').insertAdjacentHTML('beforeend', badgeHtml);
    badgeCount++;
}

function checkNoBadges() {
    const container = document.getElementById('badges-container');
    const noBadges = document.getElementById('no-badges');
    noBadges.style.display = (container.children.length === 0) ? 'block' : 'none';
}

function syncQuillContent() {
    for (const [quillId, quill] of Object.entries(quillInstances)) {
        const parts = quillId.split('-');
        const chapterIndex = parts[1];
        const lessonIndex = parts[2];
        const input = document.getElementById('quill-input-' + chapterIndex + '-' + lessonIndex);
        if (input) {
            input.value = quill.root.innerHTML;
        }
    }
}

const categoryTypes = <?= json_encode($categoryTypesJson) ?>;
const courseTypeSelect = document.getElementById('course_type');
const categorySelect = document.getElementById('category_id');

function loadCourseTypes() {
    const categoryId = categorySelect.value;
    courseTypeSelect.innerHTML = '<option value="">Select a type...</option>';
    
    if (categoryId && categoryTypes[categoryId] && categoryTypes[categoryId].types.length > 0) {
        document.getElementById('courseTypeField').style.display = 'block';
        categoryTypes[categoryId].types.forEach(function(type) {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            if (type === '<?= htmlspecialchars($course['course_type'] ?? '') ?>') {
                option.selected = true;
            }
            courseTypeSelect.appendChild(option);
        });
    } else {
        document.getElementById('courseTypeField').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Load existing data
    const existingChapters = <?= json_encode($chapters) ?>;
    const existingBadges = <?= json_encode($badges) ?>;
    
    existingChapters.forEach(chapter => {
        addChapter(chapter);
    });
    
    existingBadges.forEach(badge => {
        addBadge(badge);
    });
    
    if (categorySelect.value) {
        loadCourseTypes();
    }
});
</script>