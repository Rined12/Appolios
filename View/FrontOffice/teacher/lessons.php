<?php
$teacherSidebarActive = 'lessons';
$lessons = isset($lessons) && is_array($lessons) ? $lessons : [];
$flash = $flash ?? null;
?>

<div class="dashboard">
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            <div class="admin-main pro-table-page">
                <div class="pro-table-head">
                    <div>
                        <h1>Lessons</h1>
                        <p>Liste des lessons de vos cours.</p>
                    </div>
                </div>

                <?php if (!empty($flash)): ?>
                    <p class="flash flash-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></p>
                <?php endif; ?>

                <div class="pro-table-card">
                    <div class="pro-table-wrap">
                        <table class="pro-table">
                            <thead>
                                <tr>
                                    <th>Lesson</th>
                                    <th>Chapter</th>
                                    <th>Course</th>
                                    <th>Type</th>
                                    <th>Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($lessons)): foreach ($lessons as $l): ?>
                                    <tr>
                                        <td>
                                            <div class="pro-cell-title">
                                                <strong><?= htmlspecialchars((string) ($l['title'] ?? '')) ?></strong>
                                                <span class="pro-cell-sub">#<?= (int) ($l['id'] ?? 0) ?></span>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars((string) ($l['chapter_title'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string) ($l['course_title'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string) ($l['lesson_type'] ?? '')) ?></td>
                                        <td><?= (int) ($l['sort_order'] ?? 0) ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="5">Aucune lesson trouvée.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
