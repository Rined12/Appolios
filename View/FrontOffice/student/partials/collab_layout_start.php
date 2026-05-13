<?php
/**
 * Opens dashboard wrapper + sidebar for shared student/teacher Groups & Discussions views.
 * Expects extract(): collab_shell, collab_dashboard_classes, foPrefix (optional), collab_root_attrs (optional HTML attrs on root div).
 */
$collab_shell = $collab_shell ?? 'student';
$foPrefix = $foPrefix ?? 'student';
$collab_dashboard_classes = $collab_dashboard_classes ?? 'dashboard student-events-page collab-hub';
$collab_root_attrs = $collab_root_attrs ?? '';
$collab_sidebar = ($collab_shell === 'teacher')
    ? __DIR__ . '/../../teacher/partials/sidebar.php'
    : __DIR__ . '/../partials/sidebar.php';
?>
<div class="<?= htmlspecialchars(trim((string) $collab_dashboard_classes), ENT_QUOTES, 'UTF-8') ?>"<?= $collab_root_attrs ?>>
    <div class="container admin-dashboard-container">
        <div class="admin-layout">
            <?php require $collab_sidebar; ?>
            <div class="admin-main">
