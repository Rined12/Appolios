<?php
$root = dirname(__DIR__);
$lines = file($root . '/Controller/StudentController.php');
$slice = static fn (int $a, int $b) => implode('', array_slice($lines, $a - 1, $b - $a + 1));
$groupesBody = $slice(102, 650);
$discBody = $slice(661, 940);
$transform = static function (string $code): string {
    $code = preg_replace('/\$this->/', '$c->', $code);
    $code = str_replace("APP_ENTRY . '?url=student/discussions/", "APP_ENTRY . '?url=' . \$prefix . '/discussions/", $code);
    $code = str_replace("APP_ENTRY . '?url=student/groupes/", "APP_ENTRY . '?url=' . \$prefix . '/groupes/", $code);
    $code = str_replace("'student/discussions/'", "\$prefix . '/discussions/'", $code);
    $code = str_replace("'student/groupes/'", "\$prefix . '/groupes/'", $code);
    $code = str_replace("'student/discussions'", "\$prefix . '/discussions'", $code);
    $code = str_replace("'student/groupes'", "\$prefix . '/groupes'", $code);
    return $code;
};
$groupesBody = $transform($groupesBody);
$discBody = $transform($discBody);
$header = <<<'PHP'
<?php
/**
 * Shared Groups + Discussions for student + teacher routes.
 * Regenerate: php tools/build_collab_delegate.php
 */
require_once __DIR__ . '/BaseController.php';

final class CollabHubDelegate
{
    public static function collabViewShell(string $prefix, string $sidebarKey): array
    {
        $isTeacher = ($prefix === 'teacher');
        return [
            'collab_shell' => $isTeacher ? 'teacher' : 'student',
            'foPrefix' => $prefix,
            'collab_dashboard_classes' => $isTeacher
                ? 'dashboard teacher-collab-page collab-hub'
                : 'dashboard student-events-page collab-hub',
        ] + ($isTeacher
            ? ['teacherSidebarActive' => $sidebarKey]
            : ['studentSidebarActive' => $sidebarKey]);
    }

    public static function runGroupes(BaseController $c, string $prefix, array $params): void
    {

PHP;
$mid = <<<'PHP'
    }

    public static function runDiscussions(BaseController $c, string $prefix, array $params): void
    {

PHP;
$footer = <<<'PHP'
    }
}

PHP;
file_put_contents($root . '/Controller/CollabHubDelegate.php', $header . $groupesBody . $mid . $discBody . $footer);
echo "ok\n";
