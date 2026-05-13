<?php
$f = dirname(__DIR__) . '/Controller/StudentController.php';
$s = file_get_contents($f);
if (strpos($s, 'CollabHubDelegate.php') !== false) {
    echo "already patched\n";
    exit(0);
}
$s = str_replace(
    "require_once __DIR__ . '/EvenementController.php';\n\nclass StudentController",
    "require_once __DIR__ . '/EvenementController.php';\nrequire_once __DIR__ . '/CollabHubDelegate.php';\n\nclass StudentController",
    $s
);
$pattern = '/public function groupes\(\.\.\.\$params\): void\s*\{.*?\n    public function uploadChatAttachment/ms';
$replacement = <<<'PHP'
public function groupes(...$params): void
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access groups.');
            $this->redirect('login');
            return;
        }
        CollabHubDelegate::runGroupes($this, 'student', $params);
    }

    public function discussions(...$params): void
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Please login to access discussions.');
            $this->redirect('login');
            return;
        }
        CollabHubDelegate::runDiscussions($this, 'student', $params);
    }

    public function uploadChatAttachment
PHP;
if (!preg_match($pattern, $s)) {
    fwrite(STDERR, "regex failed\n");
    exit(1);
}
$s = preg_replace($pattern, $replacement, $s, 1);
file_put_contents($f, $s);
echo "ok\n";
