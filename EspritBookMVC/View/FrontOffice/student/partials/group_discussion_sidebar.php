<?php
/**
 * Student or teacher sidebar for shared Groups / Discussions pages.
 */
$foPrefix = $foPrefix ?? 'student';
if ($foPrefix === 'teacher') {
    $teacherSidebarActive = $teacherSidebarActive ?? '';
    require __DIR__ . '/../../teacher/partials/sidebar.php';
} else {
    require __DIR__ . '/sidebar.php';
}
