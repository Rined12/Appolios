<?php
/**
 * APPOLIOS - Admin Course View (With Chapters & Lessons)
 */

$chapters = $course['chapters'] ?? [];
$lessons = $course['lessons'] ?? [];

$adminSidebarActive = 'manage-courses';
?>

<div class="dashboard">
    <div class="container admin-dashboard-container" style="max-width: 1400px; width: 100%;">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            
            <div class="admin-main" style="background: transparent; padding: 1rem 0 2rem 0; font-family: 'Inter', sans-serif;">
                
                <!-- Header -->
                <div style="background: white; border-radius: 20px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <p style="margin: 0 0 0.5rem;"><a href="<?= APP_ENTRY ?>?url=admin/manage-courses" style="color: #3b82f6; text-decoration: none; font-weight: 600;">← Back to Courses</a></p>
                            <h1 style="margin: 0 0 0.5rem; font-size: 1.8rem; color: #1e293b; font-weight: 800;"><?= htmlspecialchars($course['title']) ?></h1>
                            <p style="margin: 0; color: #64748b;">By <?= htmlspecialchars($course['creator_name'] ?? 'Unknown') ?></p>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="<?= APP_ENTRY ?>?url=admin/edit-course/<?= $course['id'] ?>" style="background: #3b82f6; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 600;">Edit Course</a>
                        </div>
                    </div>
                </div>
                
                <!-- Course Overview -->
                <div style="background: white; border-radius: 20px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                    <h2 style="margin: 0 0 1rem; font-size: 1.25rem; color: #1e293b;">Course Overview</h2>
                    <p style="margin: 0; color: #64748b; line-height: 1.7;"><?= nl2br(htmlspecialchars((string) ($course['description'] ?? 'No description provided.'))) ?></p>
                    
                    <?php if (!empty($chapters)): ?>
                        <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                            <span style="background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;"><?= count($chapters) ?> Chapters</span>
                            <span style="background: #dcfce7; color: #16a34a; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;"><?= $course['lesson_count'] ?? 0 ?> Lessons</span>
                            <span style="background: #fef3c7; color: #d97706; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;"><?= htmlspecialchars(ucfirst($course['status'] ?? 'draft')) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Chapters & Lessons Accordion -->
                <?php if (!empty($chapters)): ?>
                    <div style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                        <?php foreach ($chapters as $chapterIndex => $chapter): ?>
                            <div style="border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                                <!-- Chapter Header -->
                                <div onclick="toggleChapter(this)" style="padding: 1rem 1.5rem; background: linear-gradient(135deg, #548CA8 0%, #355C7D 100%); color: white; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <span style="font-weight: 700; font-size: 1rem;">Chapter <?= $chapterIndex + 1 ?>: <?= htmlspecialchars($chapter['title']) ?></span>
                                    </div>
                                    <svg class="chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="transition: transform 0.3s;">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>

                                <!-- Chapter Content -->
                                <div class="chapter-content" style="display: none; padding: 1rem 1.5rem; background: #f8fafc;">
                                    <?php if (!empty($chapter['description'])): ?>
                                        <p style="margin: 0 0 1rem; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($chapter['description']) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($chapter['lessons'])): ?>
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            <?php foreach ($chapter['lessons'] as $lessonIndex => $lesson): ?>
                                                <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                                    <!-- Lesson Header -->
                                                    <div onclick="toggleLesson(this)" style="padding: 0.75rem 1rem; background: white; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                            <div style="width: 32px; height: 32px; background: <?= $lesson['lesson_type'] === 'video' ? '#eff6ff' : ($lesson['lesson_type'] === 'pdf' ? '#fef3c7' : '#f0fdf4') ?>; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                <?php if ($lesson['lesson_type'] === 'video'): ?>
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                                                <?php elseif ($lesson['lesson_type'] === 'pdf'): ?>
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                                <?php else: ?>
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                                                <?php endif; ?>
                                                            </div>
                                                            <span style="font-weight: 600; font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($lesson['title']) ?></span>
                                                            <span style="font-size: 0.75rem; color: #64748b; background: #e2e8f0; padding: 2px 8px; border-radius: 12px;"><?= ucfirst($lesson['lesson_type']) ?></span>
                                                        </div>
                                                        <svg class="lesson-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="transition: transform 0.3s;">
                                                            <polyline points="6 9 12 15 18 9"></polyline>
                                                        </svg>
                                                    </div>

                                                    <!-- Lesson Content -->
                                                    <div class="lesson-content" style="display: none; padding: 1rem; background: #fafafa; border-top: 1px solid #e2e8f0;">
                                                        <?php if (($lesson['lesson_type'] === 'text' || $lesson['lesson_type'] === 'both') && !empty($lesson['content'])): ?>
                                                            <div style="margin-bottom: 0.75rem;">
                                                                <strong style="font-size: 0.85rem; color: #475569;">Content:</strong>
                                                                <div style="margin-top: 0.5rem; padding: 0.75rem; background: white; border-radius: 6px; font-size: 0.85rem; color: #475569; line-height: 1.6;">
                                                                    <?= strip_tags($lesson['content'], '<p><br><strong><em><ul><ol><li>') ?>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($lesson['duration'])): ?>
                                                            <div style="margin-bottom: 0.75rem; font-size: 0.85rem; color: #64748b;">
                                                                <strong>Duration:</strong> <?= $lesson['duration'] ?> minutes
                                                            </div>
                                                        <?php endif; ?>
                                                        <div style="display: flex; gap: 0.5rem;">
                                                            <?php if (!empty($lesson['pdf_path'])): ?>
                                                                <button onclick="viewPdfInline(this, '<?= APP_URL ?>/<?= htmlspecialchars($lesson['pdf_path']) ?>')" style="background: #dc2626; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                                    View PDF
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if (!empty($lesson['video_url'])): ?>
                                                                <a href="<?= htmlspecialchars($lesson['video_url']) ?>" target="_blank" style="background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                                                    Watch Video
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="pdf-viewer" style="display: none; margin-top: 1rem; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; background: #f1f5f9; border-bottom: 1px solid #e2e8f0;">
                                                                <span style="font-size: 0.85rem; font-weight: 600; color: #475569;">PDF Preview</span>
                                                                <button onclick="closePdfViewer(this)" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 4px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#64748b'">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                            <iframe src="" style="width: 100%; height: 500px; border: none;" title="PDF Viewer"></iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p style="color: #94a3b8; font-size: 0.85rem;">No lessons in this chapter</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="background: white; border-radius: 20px; padding: 3rem; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin: 0 auto 1rem;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <h3 style="margin: 0 0 0.5rem; color: #475569;">No content yet</h3>
                        <p style="margin: 0; color: #94a3b8;">This course has no chapters or lessons.</p>
                    </div>
                <?php endif; ?>

                <script>
                function toggleChapter(header) {
                    const content = header.nextElementSibling;
                    const chevron = header.querySelector('.chevron');
                    const isHidden = content.style.display === 'none';
                    content.style.display = isHidden ? 'block' : 'none';
                    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                }

                function toggleLesson(header) {
                    const content = header.nextElementSibling;
                    const chevron = header.querySelector('.lesson-chevron');
                    const isHidden = content.style.display === 'none';
                    content.style.display = isHidden ? 'block' : 'none';
                    chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
                }

                function viewPdfInline(btn, pdfUrl) {
                    const lessonContent = btn.closest('.lesson-content');
                    const pdfViewer = lessonContent.querySelector('.pdf-viewer');
                    const iframe = pdfViewer.querySelector('iframe');

                    if (pdfViewer.style.display === 'block') {
                        pdfViewer.style.display = 'none';
                        iframe.src = '';
                    } else {
                        iframe.src = pdfUrl;
                        pdfViewer.style.display = 'block';
                    }
                }

                function closePdfViewer(btn) {
                    const pdfViewer = btn.closest('.pdf-viewer');
                    const iframe = pdfViewer.querySelector('iframe');
                    iframe.src = '';
                    pdfViewer.style.display = 'none';
                }
                </script>
                
            </div>
        </div>
    </div>
</div>