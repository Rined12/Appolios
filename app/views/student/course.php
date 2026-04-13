<?php
/**
 * APPOLIOS — Fiche cours (étudiant)
 */
$studentSidebarActive = $studentSidebarActive ?? 'courses';
$chapters = $chapters ?? [];
$quizzesByChapter = $quizzesByChapter ?? [];
$courseQuizCount = (int) ($courseQuizCount ?? 0);
?>
<div class="dashboard student-learning-page">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main">
                <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;">
                    <div>
                        <p style="margin:0 0 8px;">
                            <a href="<?= APP_URL ?>/index.php?url=<?= $isEnrolled ? 'student/my-courses' : 'student/courses' ?>" class="student-back-link">← <?= $isEnrolled ? 'Mes cours' : 'Catalogue' ?></a>
                        </p>
                        <h1 style="margin:0;"><?= htmlspecialchars($course['title']) ?></h1>
                        <p style="color:var(--gray-dark);margin:8px 0 0;">Par <?= htmlspecialchars($course['creator_name'] ?? '') ?></p>
                    </div>
                    <?php if ($isEnrolled): ?>
                        <span class="student-badge student-badge--ok">Inscrit</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>" style="margin:16px 0;"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="student-course-layout" style="display:grid;grid-template-columns:1fr 320px;gap:28px;margin-top:24px;align-items:start;">
                    <div>
                        <div class="table-container student-panel">
                            <div class="table-header"><h2 style="margin:0;font-size:1.1rem;">À propos de ce cours</h2></div>
                            <div style="padding:24px;">
                                <p style="line-height:1.75;margin:0;"><?= nl2br(htmlspecialchars((string) ($course['description'] ?? ''))) ?></p>
                            </div>
                        </div>

                        <?php if ($isEnrolled && !empty($chapters)): ?>
                            <div class="table-container student-panel" style="margin-top:20px;">
                                <div class="table-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                                    <h2 style="margin:0;font-size:1.1rem;">Parcours : chapitres & quiz</h2>
                                    <a href="<?= APP_URL ?>/index.php?url=student/chapitres" class="btn btn-outline" style="padding:6px 12px;font-size:0.85rem;">Tous mes chapitres</a>
                                </div>
                                <div style="padding:8px 0;">
                                    <?php foreach ($chapters as $ch): ?>
                                        <?php
                                        $chid = (int) ($ch['id'] ?? 0);
                                        $chapterQuizzes = $quizzesByChapter[$chid] ?? [];
                                        ?>
                                        <details class="student-chapter-details" <?= $chid === (int) ($chapters[0]['id'] ?? 0) ? 'open' : '' ?>>
                                            <summary>
                                                <span class="student-chapter-order"><?= (int) ($ch['sort_order'] ?? 0) ?></span>
                                                <span class="student-chapter-title"><?= htmlspecialchars($ch['title'] ?? '') ?></span>
                                            </summary>
                                            <div class="student-chapter-body">
                                                <div class="student-chapter-content">
                                                    <?= nl2br(htmlspecialchars((string) ($ch['content'] ?? '(Aucun contenu pour ce chapitre.)'))) ?>
                                                </div>
                                                <?php if (!empty($chapterQuizzes)): ?>
                                                    <div class="student-chapter-quizzes">
                                                        <strong>Quiz</strong>
                                                        <ul class="student-quiz-links">
                                                            <?php foreach ($chapterQuizzes as $qz): ?>
                                                                <li>
                                                                    <a href="<?= APP_URL ?>/index.php?url=student/quiz/<?= (int) $qz['id'] ?>">
                                                                        <?= htmlspecialchars($qz['title'] ?? 'Quiz') ?>
                                                                    </a>
                                                                    <span class="student-quiz-meta">
                                                                        <?= (int) ($qz['question_count'] ?? 0) ?> question(s)
                                                                        · <?= htmlspecialchars(Quiz::difficultyLabelFr($qz['difficulty'] ?? 'beginner')) ?>
                                                                    </span>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="student-muted" style="margin:12px 0 0;">Pas encore de quiz pour ce chapitre.</p>
                                                <?php endif; ?>
                                            </div>
                                        </details>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($isEnrolled && empty($chapters)): ?>
                            <div class="table-container student-panel" style="margin-top:20px;padding:24px;">
                                <p style="margin:0;color:var(--gray-dark);">Ce cours ne contient pas encore de chapitres. Revenez plus tard ou contactez votre enseignant.</p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($course['video_url'])): ?>
                            <div class="table-container student-panel" style="margin-top:20px;">
                                <div class="table-header"><h3 style="margin:0;font-size:1rem;">Vidéo</h3></div>
                                <div style="padding:20px;">
                                    <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;background:#111;">
                                        <?php
                                        $videoUrl = $course['video_url'];
                                        if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                                            if (strpos($videoUrl, 'youtu.be') !== false) {
                                                $videoId = basename(parse_url($videoUrl, PHP_URL_PATH));
                                            } else {
                                                parse_str((string) parse_url($videoUrl, PHP_URL_QUERY), $params);
                                                $videoId = $params['v'] ?? '';
                                            }
                                            echo '<iframe style="position:absolute;top:0;left:0;width:100%;height:100%;" src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '" title="Vidéo du cours" allowfullscreen></iframe>';
                                        } else {
                                            echo '<video controls style="width:100%;"><source src="' . htmlspecialchars($videoUrl) . '" type="video/mp4">Lecture vidéo non supportée.</video>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <aside class="student-course-aside">
                        <div class="table-container student-panel">
                            <div style="padding:22px;">
                                <h3 style="margin:0 0 16px;font-size:1rem;">Actions</h3>
                                <?php if ($isEnrolled): ?>
                                    <div class="student-aside-highlight">
                                        <p style="margin:0;font-weight:600;">Vous suivez ce cours</p>
                                        <p style="margin:8px 0 0;font-size:0.9rem;color:var(--gray-dark);">
                                            <?= count($chapters) ?> chapitre(s)
                                            <?php if ($courseQuizCount > 0): ?>
                                                · <?= $courseQuizCount ?> quiz
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <a href="<?= APP_URL ?>/index.php?url=student/quiz" class="btn btn-primary btn-block" style="margin-top:14px;">Voir tous les quiz</a>
                                    <a href="<?= APP_URL ?>/index.php?url=student/questions" class="btn btn-outline btn-block" style="margin-top:10px;">Banque de questions</a>
                                <?php else: ?>
                                    <p style="font-size:0.95rem;color:var(--gray-dark);margin:0 0 16px;">
                                        Inscrivez-vous pour accéder aux chapitres, quiz et ressources.
                                    </p>
                                    <a href="<?= APP_URL ?>/index.php?url=student/enroll/<?= (int) $course['id'] ?>" class="btn btn-yellow btn-block">S’inscrire</a>
                                <?php endif; ?>
                                <div style="margin-top:22px;padding-top:18px;border-top:1px solid var(--gray);font-size:0.9rem;color:var(--gray-dark);">
                                    <p style="margin:0 0 8px;"><strong>En bref</strong></p>
                                    <ul style="margin:0;padding-left:1.1rem;line-height:1.6;">
                                        <li>Contenu structuré par chapitres</li>
                                        <li>Quiz pour vous entraîner</li>
                                        <li>Accès depuis votre espace</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</div>
