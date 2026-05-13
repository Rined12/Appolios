<?php
/**
 * APPOLIOS Admin Controller
 * Handles admin dashboard and management
 */

require_once __DIR__ . '/../Controller/ActivityLogger.php';

class AdminController extends BaseController
{
    use ActivityLogger;



    public function slGroupes()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $groupModel = $this->model('Groupe');
        $pending = array_values(array_filter($groupModel->fetchAll(), function ($g) {
            return (string) ($g['approval_statut'] ?? '') === 'en_cours';
        }));
        $approved = $groupModel->fetchAllApproved();

        $normalizeCover = function (array $g): array {
            $raw = trim((string) ($g['image_url'] ?? ''));
            if ($raw === '') {
                $g['cover_url'] = '';
            } elseif (preg_match('~^https?://~i', $raw)) {
                $g['cover_url'] = $raw;
            } else {
                $g['cover_url'] = APP_URL . '/' . ltrim($raw, '/');
            }
            return $g;
        };
        $pending = array_map($normalizeCover, $pending);
        $approved = array_map($normalizeCover, $approved);

        $data = [
            'title' => 'Social Learning - Groupes',
            'description' => 'Manage Social Learning groups',
            'adminSidebarActive' => 'sl-groupes',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'pending_groups' => $pending,
            'approved_groups' => $approved,
            'flash' => $this->getFlash(),
        ];

        $this->view('BackOffice/admin/sl_groupes', $data);
    }

    public function approveGroupe($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupId = (int) $id;
        if ($groupId <= 0) {
            $this->setFlash('error', 'Invalid group id.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupModel = $this->model('Groupe');
        $g = $groupModel->findById($groupId);
        if (!$g) {
            $this->setFlash('error', 'Group not found.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $ok = $groupModel->updateGroupe($groupId, [
            'nom_groupe' => (string) ($g['nom_groupe'] ?? ''),
            'description' => (string) ($g['description'] ?? ''),
            'statut' => (string) ($g['statut'] ?? 'actif'),
            'approval_statut' => 'approuve',
            'image_url' => (string) ($g['image_url'] ?? ''),
        ]);

        if ($ok) {
            $this->setFlash('success', 'Group approved.');
        } else {
            $this->setFlash('error', 'Failed to approve group.');
        }

        $this->redirect('admin/sl-groupes');
    }

    public function editGroupe($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $groupId = (int) $id;
        if ($groupId <= 0) {
            $this->setFlash('error', 'Invalid group id.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupModel = $this->model('Groupe');
        $g = $groupModel->findById($groupId);
        if (!$g) {
            $this->setFlash('error', 'Group not found.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $data = [
            'title' => 'Edit Group - APPOLIOS',
            'description' => 'Edit Social Learning group',
            'adminSidebarActive' => 'sl-groupes',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'groupe' => $g,
            'flash' => $this->getFlash(),
        ];
        $this->view('BackOffice/admin/edit_groupe', $data);
    }

    public function updateGroupe($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupId = (int) $id;
        if ($groupId <= 0) {
            $this->setFlash('error', 'Invalid group id.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupModel = $this->model('Groupe');
        $g = $groupModel->findById($groupId);
        if (!$g) {
            $this->setFlash('error', 'Group not found.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $nom = trim((string) ($_POST['nom_groupe'] ?? ''));
        $desc = trim((string) ($_POST['description'] ?? ''));
        $statut = (string) ($_POST['statut'] ?? (string) ($g['statut'] ?? 'actif'));
        $approval = (string) ($_POST['approval_statut'] ?? (string) ($g['approval_statut'] ?? 'en_cours'));

        if ($nom === '' || $desc === '') {
            $this->setFlash('error', 'Name and description are required.');
            $this->redirect('admin/edit-groupe/' . $groupId);
            return;
        }
        if (!in_array($statut, ['actif', 'archivé'], true)) {
            $statut = 'actif';
        }
        if (!in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
            $approval = (string) ($g['approval_statut'] ?? 'en_cours');
        }

        $imageUrl = (string) ($g['image_url'] ?? '');
        if (!empty($_FILES['group_photo']) && is_array($_FILES['group_photo']) && ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->storeUploadedFile($_FILES['group_photo'], __DIR__ . '/../uploads/groupes');
            if ($uploadResult['ok']) {
                $imageUrl = 'uploads/groupes/' . $uploadResult['fileName'];
            }
        }

        $ok = $groupModel->updateGroupe($groupId, [
            'nom_groupe' => $nom,
            'description' => $desc,
            'statut' => $statut,
            'approval_statut' => $approval,
            'image_url' => $imageUrl,
        ]);

        if ($ok) {
            $this->setFlash('success', 'Group updated.');
        } else {
            $this->setFlash('error', 'Failed to update group.');
        }
        $this->redirect('admin/edit-groupe/' . $groupId);
    }

    public function groupActivityPdf($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $groupId = (int) $id;
        if ($groupId <= 0) {
            $this->setFlash('error', 'Invalid group id.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupModel = $this->model('Groupe');
        $postModel = $this->model('GroupPost');
        $reactionModel = $this->model('GroupPostReaction');
        $commentModel = $this->model('GroupPostComment');
        $discussionModel = $this->model('Discussion');

        $g = $groupModel->findById($groupId);
        if (!$g) {
            $this->setFlash('error', 'Group not found.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $members = $groupModel->fetchMembres($groupId);
        $posts = $postModel->fetchByGroup($groupId, 200);
        $postCount = count($posts);
        $commentCount = 0;
        $reactionCount = 0;
        foreach ($posts as $p) {
            $pid = (int) ($p['id'] ?? 0);
            $commentCount += $pid > 0 ? count($commentModel->fetchByPost($pid, 200)) : 0;
            $r = $pid > 0 ? $reactionModel->countByPost($pid) : [];
            foreach ($r as $cnt) {
                $reactionCount += (int) $cnt;
            }
        }
        $discussions = $discussionModel->fetchByGroup($groupId);

        $data = [
            'title' => 'Group Activity Report - APPOLIOS',
            'description' => 'Printable group activity report',
            'adminSidebarActive' => 'sl-groupes',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'groupe' => $g,
            'members' => $members,
            'posts' => $posts,
            'discussions' => $discussions,
            'stats' => [
                'posts' => $postCount,
                'comments' => $commentCount,
                'reactions' => $reactionCount,
                'members' => count($members),
                'discussions' => count($discussions),
            ],
            'flash' => $this->getFlash(),
        ];
        $this->view('BackOffice/admin/group_activity_pdf', $data);
    }

    public function showGroupe($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $groupId = (int) $id;
        if ($groupId <= 0) {
            $this->setFlash('error', 'Invalid group id.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupModel = $this->model('Groupe');
        $g = $groupModel->findById($groupId);
        if (!$g) {
            $this->setFlash('error', 'Group not found.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $members = $groupModel->fetchMembres($groupId);

        $data = [
            'title' => 'Group Details - APPOLIOS',
            'description' => 'Admin group view',
            'adminSidebarActive' => 'sl-groupes',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'groupe' => $g,
            'members' => $members,
            'flash' => $this->getFlash(),
        ];
        $this->view('BackOffice/admin/show_groupe', $data);
    }

    public function createGroupe()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $data = [
            'title' => 'Create Group - APPOLIOS',
            'description' => 'Create Social Learning group',
            'adminSidebarActive' => 'sl-groupes',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash(),
        ];
        $this->view('BackOffice/admin/create_groupe', $data);
    }

    public function storeGroupe()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/create-groupe');
            return;
        }

        $nom = trim((string) ($_POST['nom_groupe'] ?? ''));
        $desc = trim((string) ($_POST['description'] ?? ''));
        $statut = (string) ($_POST['statut'] ?? 'actif');
        $approval = (string) ($_POST['approval_statut'] ?? 'approuve');

        if ($nom === '' || $desc === '') {
            $this->setFlash('error', 'Name and description are required.');
            $this->redirect('admin/create-groupe');
            return;
        }
        if (!in_array($statut, ['actif', 'archivé'], true)) {
            $statut = 'actif';
        }
        if (!in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
            $approval = 'approuve';
        }

        $imageUrl = null;
        if (!empty($_FILES['group_photo']) && is_array($_FILES['group_photo']) && ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->storeUploadedFile($_FILES['group_photo'], __DIR__ . '/../uploads/groupes');
            if ($uploadResult['ok']) {
                $imageUrl = 'uploads/groupes/' . $uploadResult['fileName'];
            }
        }

        $groupModel = $this->model('Groupe');
        $newId = $groupModel->create([
            'nom_groupe' => $nom,
            'description' => $desc,
            'id_createur' => (int) ($_SESSION['user_id'] ?? 0),
            'statut' => $statut,
            'approval_statut' => $approval,
            'image_url' => $imageUrl,
        ]);

        if ($newId) {
            $this->setFlash('success', 'Group created.');
            $this->redirect('admin/edit-groupe/' . (int) $newId);
            return;
        }

        $this->setFlash('error', 'Failed to create group.');
        $this->redirect('admin/create-groupe');
    }

    public function slDiscussions()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $status = (string) ($_GET['status'] ?? 'all');
        $approval = null;
        if (in_array($status, ['en_cours', 'approuve', 'rejete'], true)) {
            $approval = $status;
        }

        $discussionModel = $this->model('Discussion');
        $rows = $discussionModel->fetchAllWithGroupAndAuthor($approval);

        $pendingCount = count($discussionModel->fetchAllWithGroupAndAuthor('en_cours'));
        $approvedCount = count($discussionModel->fetchAllWithGroupAndAuthor('approuve'));
        $rejectedCount = count($discussionModel->fetchAllWithGroupAndAuthor('rejete'));

        $data = [
            'title' => 'Social Learning - Discussions',
            'description' => 'Manage Social Learning discussions',
            'adminSidebarActive' => 'sl-discussions',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'discussions' => $rows,
            'filterStatus' => $status,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'flash' => $this->getFlash(),
        ];

        $this->view('BackOffice/admin/sl_discussions', $data);
    }

    public function approveDiscussion($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/sl-discussions');
            return;
        }

        $discussionId = (int) $id;
        if ($discussionId <= 0) {
            $this->setFlash('error', 'Invalid discussion id.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $row = $discussionModel->fetchRowByPk($discussionId);
        if (!$row) {
            $this->setFlash('error', 'Discussion not found.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $stmt = $this->getDb()->prepare('UPDATE discussion SET approval_statut = ? WHERE id_discussion = ?');
        $ok = $stmt->execute(['approuve', $discussionId]);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion approved.' : 'Failed to approve discussion.');
        $this->redirect('admin/sl-discussions');
    }

    public function rejectDiscussion($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/sl-discussions');
            return;
        }

        $discussionId = (int) $id;
        if ($discussionId <= 0) {
            $this->setFlash('error', 'Invalid discussion id.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $row = $discussionModel->fetchRowByPk($discussionId);
        if (!$row) {
            $this->setFlash('error', 'Discussion not found.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $stmt = $this->getDb()->prepare('UPDATE discussion SET approval_statut = ? WHERE id_discussion = ?');
        $ok = $stmt->execute(['rejete', $discussionId]);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion rejected.' : 'Failed to reject discussion.');
        $this->redirect('admin/sl-discussions');
    }

    public function deleteDiscussion($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/sl-discussions');
            return;
        }

        $discussionId = (int) $id;
        if ($discussionId <= 0) {
            $this->setFlash('error', 'Invalid discussion id.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $ok = $discussionModel->deleteByPrimaryKey($discussionId);
        $this->setFlash($ok ? 'success' : 'error', $ok ? 'Discussion deleted.' : 'Failed to delete discussion.');
        $this->redirect('admin/sl-discussions');
    }

    public function createDiscussion()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $groupModel = $this->model('Groupe');
        $groups = $groupModel->fetchAllApproved();

        $data = [
            'title' => 'Create Discussion - APPOLIOS',
            'description' => 'Create Social Learning discussion',
            'adminSidebarActive' => 'sl-discussions',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'groups' => $groups,
            'flash' => $this->getFlash(),
        ];
        $this->view('BackOffice/admin/create_discussion', $data);
    }

    public function storeDiscussion()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/create-discussion');
            return;
        }

        $groupId = (int) ($_POST['id_groupe'] ?? 0);
        $title = trim((string) ($_POST['titre'] ?? ''));
        $content = trim((string) ($_POST['contenu'] ?? ''));
        $approval = (string) ($_POST['approval_statut'] ?? 'approuve');

        if ($groupId <= 0 || $title === '' || $content === '') {
            $this->setFlash('error', 'Group, title and content are required.');
            $this->redirect('admin/create-discussion');
            return;
        }
        if (!in_array($approval, ['en_cours', 'approuve', 'rejete'], true)) {
            $approval = 'approuve';
        }

        $groupModel = $this->model('Groupe');
        $g = $groupModel->findById($groupId);
        if (!$g || (string) ($g['approval_statut'] ?? '') !== 'approuve') {
            $this->setFlash('error', 'Selected group is not approved.');
            $this->redirect('admin/create-discussion');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $ok = $discussionModel->createForGroup($groupId, (int) ($_SESSION['user_id'] ?? 0), $title, $content, $approval);
        if ($ok) {
            $this->setFlash('success', 'Discussion created.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $this->setFlash('error', 'Failed to create discussion.');
        $this->redirect('admin/create-discussion');
    }

    public function chatDiscussion($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $discussionId = (int) $id;
        if ($discussionId <= 0) {
            $this->setFlash('error', 'Invalid discussion id.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $discussionModel = $this->model('Discussion');
        $groupModel = $this->model('Groupe');
        $discussion = $discussionModel->fetchRowByPk($discussionId);
        if (!$discussion) {
            $this->setFlash('error', 'Discussion not found.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        if ((string) ($discussion['approval_statut'] ?? '') !== 'approuve') {
            $this->setFlash('error', 'Live chat is available only for approved discussions.');
            $this->redirect('admin/sl-discussions');
            return;
        }

        $group = $groupModel->findById((int) ($discussion['id_groupe'] ?? 0));
        $chatRoom = 'discussion_' . $discussionId;
        $socketUrl = (string) ($_ENV['REALTIME_SOCKET_URL'] ?? 'http://127.0.0.1:3001');

        $uploadUrl = APP_ENTRY . '?url=admin/upload-chat-attachment/' . $discussionId;
        $summarizeUrl = APP_ENTRY . '?url=student/summarize-text';

        $data = [
            'title' => 'Live discussion - APPOLIOS',
            'description' => 'Admin live discussion chat',
            'adminSidebarActive' => 'sl-discussions',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'discussion' => $discussion,
            'group' => $group ?: [],
            'chatRoom' => $chatRoom,
            'socketUrl' => $socketUrl,
            'currentUserId' => (int) ($_SESSION['user_id'] ?? 0),
            'currentUserName' => (string) ($_SESSION['user_name'] ?? 'Admin'),
            'discussion_chat' => [
                'back_url' => APP_ENTRY . '?url=admin/sl-discussions',
                'upload_url' => $uploadUrl,
                'summarize_url' => $summarizeUrl,
            ],
            'flash' => $this->getFlash(),
        ];

        $this->view('BackOffice/admin/chat_discussion', $data);
    }

    public function uploadChatAttachment($id)
    {
        if (!$this->isAdmin()) {
            $this->jsonResponse(['ok' => false, 'error' => 'Unauthorized.'], 401);
        }

        $discussionId = (int) $id;
        if ($discussionId <= 0) {
            $this->jsonResponse(['ok' => false, 'error' => 'Invalid discussion id.'], 422);
        }

        if (empty($_FILES['attachment']) || !is_array($_FILES['attachment'])) {
            $this->jsonResponse(['ok' => false, 'error' => 'No file uploaded.'], 422);
        }

        $result = $this->storeAdminChatAttachment($_FILES['attachment'], __DIR__ . '/../uploads/chat');
        if (!$result['ok']) {
            $this->jsonResponse(['ok' => false, 'error' => $result['error']], 422);
        }

        $url = APP_URL . '/uploads/chat/' . $result['fileName'];
        $this->jsonResponse(['ok' => true, 'data' => ['url' => $url, 'name' => $result['originalName'] ?? $result['fileName']]]);
    }

    private function storeAdminChatAttachment(array $file, string $targetDir): array
    {
        $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload failed.'];
        }

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $original = (string) ($file['name'] ?? 'file');
        $tmp = (string) ($file['tmp_name'] ?? '');
        $mime = (string) ($file['type'] ?? '');
        $size = (int) ($file['size'] ?? 0);

        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return ['ok' => false, 'error' => 'Invalid upload.'];
        }

        if ($size > 2 * 1024 * 1024) {
            return ['ok' => false, 'error' => 'File too large (max 2MB).'];
        }

        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = 'bin';
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'mp4', 'mp3', 'wav', 'doc', 'docx'];
        if (!in_array($ext, $allowed, true)) {
            return ['ok' => false, 'error' => 'Unsupported file type.'];
        }

        $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($original, PATHINFO_FILENAME));
        if ($safe === '' || $safe === null) {
            $safe = 'upload';
        }
        $fileName = $safe . '_' . date('Ymd_His') . '_' . random_int(1000, 9999) . '.' . $ext;
        $dest = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;

        if (!@move_uploaded_file($tmp, $dest)) {
            return ['ok' => false, 'error' => 'Failed to save file.'];
        }

        return [
            'ok' => true,
            'fileName' => $fileName,
            'originalName' => $original,
            'mime' => $mime,
        ];
    }

    public function rejectGroupe($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupId = (int) $id;
        if ($groupId <= 0) {
            $this->setFlash('error', 'Invalid group id.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupModel = $this->model('Groupe');
        $g = $groupModel->findById($groupId);
        if (!$g) {
            $this->setFlash('error', 'Group not found.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $ok = $groupModel->updateGroupe($groupId, [
            'nom_groupe' => (string) ($g['nom_groupe'] ?? ''),
            'description' => (string) ($g['description'] ?? ''),
            'statut' => (string) ($g['statut'] ?? 'actif'),
            'approval_statut' => 'rejete',
            'image_url' => (string) ($g['image_url'] ?? ''),
        ]);

        if ($ok) {
            $this->setFlash('success', 'Group rejected.');
        } else {
            $this->setFlash('error', 'Failed to reject group.');
        }

        $this->redirect('admin/sl-groupes');
    }

    public function deleteGroupe($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupId = (int) $id;
        if ($groupId <= 0) {
            $this->setFlash('error', 'Invalid group id.');
            $this->redirect('admin/sl-groupes');
            return;
        }

        $groupModel = $this->model('Groupe');
        $ok = $groupModel->deleteGroupe($groupId);
        if ($ok) {
            $this->setFlash('success', 'Group deleted.');
        } else {
            $this->setFlash('error', 'Failed to delete group.');
        }

        $this->redirect('admin/sl-groupes');
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        // Check if admin
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $enrollmentModel = $this->model('Enrollment');
        $evenementModel = $this->model('Evenement');

        $data = [
            'title' => 'Admin Dashboard - APPOLIOS',
            'description' => 'Administrator control panel',
            'totalUsers' => $this->countUsers(),
            'totalStudents' => $this->countStudents(),
            'totalCourses' => $courseModel->count(),
            'totalEnrollments' => $enrollmentModel->countAll(),
            'totalEvenements' => $evenementModel->count(),
            'recentCourses' => $courseModel->getAllWithCreator(),
            'recentEvenements' => $evenementModel->getRecent(3),
            'recentUsers' => $this->getStudents(),
            'pendingTeacherApps' => $this->countPendingApplications(),
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/dashboard', $data);
    }

    /**
     * Manage users page
     */
    public function users()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $users = $this->getStudents();

        $data = [
            'title' => 'Manage Users - APPOLIOS',
            'description' => 'User management panel',
            'users' => $users,
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/users', $data);
    }

    public function categories() {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getAll();

        $data = [
            'title' => 'Manage Categories - APPOLIOS',
            'categories' => $categories,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/categories', $data);
    }

    public function storeCategory() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/categories');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = $_POST['icon'] ?? 'folder';
        $types = trim($_POST['types'] ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'Category name is required');
            $this->redirect('admin/categories');
            return;
        }

        $categoryModel = $this->model('Category');
        $result = $categoryModel->create([
            'name' => $name,
            'description' => $description,
            'icon' => $icon,
            'types' => $types
        ]);

        if ($result) {
            $this->setFlash('success', 'Category created successfully!');
        } else {
            $this->setFlash('error', 'Failed to create category.');
        }

        $this->redirect('admin/categories');
    }

    public function deleteCategory($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $categoryModel = $this->model('Category');
        $categoryModel->delete($id);

        $this->setFlash('success', 'Category deleted successfully!');
        $this->redirect('admin/categories');
    }

    public function deleteCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $courseModel->delete($id);

        $this->setFlash('success', 'Course deleted successfully!');
        $this->redirect('admin/courses');
    }

    public function viewCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $course = $courseModel->getWithChapters($id);
        
        if (!$course) {
            $this->setFlash('error', 'Course not found');
            $this->redirect('admin/manage-courses');
            return;
        }

        $userModel = $this->model('User');
        $creator = $userModel->findById($course['created_by']);

        $db = $this->getDb();
        $stmt = $db->prepare("SELECT e.*, u.name as student_name, u.email as student_email FROM enrollments e JOIN users u ON e.user_id = u.id WHERE e.course_id = ?");
        $stmt->execute([$id]);
        $enrollments = $stmt->fetchAll();

        $lessonCount = 0;
        foreach ($course['chapters'] as $chapter) {
            $lessonCount += count($chapter['lessons'] ?? []);
        }
        $course['lesson_count'] = $lessonCount;

        $data = [
            'title' => 'View Course - APPOLIOS',
            'course' => $course,
            'chapters' => $course['chapters'] ?? [],
            'creator' => $creator,
            'enrollments' => $enrollments,
            'adminSidebarActive' => 'manage-courses'
        ];

        $this->view('BackOffice/admin/view_course', $data);
    }

    public function manageCourses() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $status = $_GET['status'] ?? 'approved';
        $courseModel = $this->model('Course');
        $db = $this->getDb();

        if ($status === 'all') {
            $courses = $courseModel->getAllWithCreator();
        } else {
            $courses = $courseModel->getByStatus($status);
        }

        $pendingCount = $courseModel->countByStatus('pending');
        $approvedCount = $courseModel->countByStatus('approved');
        $rejectedCount = $courseModel->countByStatus('rejected');
        $totalCourses = $pendingCount + $approvedCount + $rejectedCount;

        $earningsStmt = $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status IN ('succeeded', 'completed')");
        $totalEarnings = (float) ($earningsStmt->fetch()['total'] ?? 0);

        foreach ($courses as &$course) {
            $chCountStmt = $db->prepare("SELECT COUNT(*) as cnt FROM chapters WHERE course_id = ?");
            $chCountStmt->execute([$course['id']]);
            $course['chapters_count'] = (int) ($chCountStmt->fetch()['cnt'] ?? 0);

            $lesCountStmt = $db->prepare("SELECT COUNT(*) as cnt FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = ?");
            $lesCountStmt->execute([$course['id']]);
            $course['lessons_count'] = (int) ($lesCountStmt->fetch()['cnt'] ?? 0);
        }

        $data = [
            'title' => 'Manage Courses - APPOLIOS',
            'courses' => $courses,
            'filterStatus' => $status,
            'totalCourses' => $totalCourses,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'totalEarnings' => $totalEarnings,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/manage_courses', $data);
    }

    public function courseRequests() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $status = $_GET['status'] ?? 'all';
        $courseModel = $this->model('Course');
        $db = $this->getDb();
        $adminId = $_SESSION['user_id'] ?? 0;

        if ($status === 'all') {
            $courses = $courseModel->getAllWithCreator();
            $courses = array_filter($courses, fn($c) => $c['created_by'] != $adminId);
        } else {
            $courses = $courseModel->getByStatus($status);
            $courses = array_filter($courses, fn($c) => $c['created_by'] != $adminId);
        }

        $courses = array_values($courses);

        foreach ($courses as &$course) {
            $chCountStmt = $db->prepare("SELECT COUNT(*) as cnt FROM chapters WHERE course_id = ?");
            $chCountStmt->execute([$course['id']]);
            $course['chapters_count'] = (int) ($chCountStmt->fetch()['cnt'] ?? 0);

            $lesCountStmt = $db->prepare("SELECT COUNT(*) as cnt FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = ?");
            $lesCountStmt->execute([$course['id']]);
            $course['lessons_count'] = (int) ($lesCountStmt->fetch()['cnt'] ?? 0);
        }

        $data = [
            'title' => 'Course Requests - APPOLIOS',
            'courses' => $courses,
            'filterStatus' => $status,
            'pendingCount' => $courseModel->countByStatus('pending'),
            'approvedCount' => $courseModel->countByStatus('approved'),
            'rejectedCount' => $courseModel->countByStatus('rejected'),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/course_requests', $data);
    }

    public function approveCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $courseModel->updateStatus($id, 'approved');

        $this->setFlash('success', 'Course approved!');
        $this->redirect('admin/course-requests');
    }

    public function rejectCourse($id) {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $courseModel->updateStatus($id, 'rejected');

        $this->setFlash('success', 'Course rejected.');
        $this->redirect('admin/course-requests');
    }

    /**
     * Statistics page
     */
    public function statistics()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        // 1. Get ban categories from activity logs
        $sqlBans = "SELECT 
                    SUM(CASE WHEN activity_description LIKE '%2 hours%' THEN 1 ELSE 0 END) as ban_2h,
                    SUM(CASE WHEN activity_description LIKE '%10 hours%' THEN 1 ELSE 0 END) as ban_10h,
                    SUM(CASE WHEN activity_description LIKE '%1 day%' THEN 1 ELSE 0 END) as ban_1d,
                    SUM(CASE WHEN activity_description LIKE '%permanently%' OR activity_description LIKE '%blocked user%' THEN 1 ELSE 0 END) as ban_perm
                FROM activity_log 
                WHERE activity_type IN ('ban_user', 'block_user')";
        
        $stmtBans = $this->getDb()->prepare($sqlBans);
        $stmtBans->execute();
        $stats = $stmtBans->fetch(PDO::FETCH_ASSOC);

        // 2. Get user distribution (Students vs Teachers) using direct SQL
        $sqlUsers = "SELECT role, COUNT(*) as count FROM users WHERE role IN ('student', 'teacher') GROUP BY role";
        $stmtUsers = $this->getDb()->query($sqlUsers);
        $userCounts = $stmtUsers->fetchAll(PDO::FETCH_KEY_PAIR);

        $totalStudents = (int) ($userCounts['student'] ?? 0);
        $totalTeachers = (int) ($userCounts['teacher'] ?? 0);

        // 3. Dynamic 7-day Forecast Algorithm
        $forecast = [];
        $growthFactor = 1.05; // +5% expected growth

        // Get registrations from last 21 days to calculate averages
        $sqlHistory = "SELECT DATE(created_at) as reg_date, role, COUNT(*) as count 
                       FROM users 
                       WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 21 DAY)
                       AND role IN ('student', 'teacher')
                       GROUP BY reg_date, role";
        $stmtHistory = $this->getDb()->query($sqlHistory);
        $historyData = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

        // Organize history by day of week (0=Sun, 6=Sat)
        $dayAverages = [
            'student' => [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => []],
            'teacher' => [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => []]
        ];

        foreach ($historyData as $row) {
            $dayOfWeek = date('w', strtotime($row['reg_date']));
            $dayAverages[$row['role']][$dayOfWeek][] = $row['count'];
        }

        // Generate forecast for next 7 days
        for ($i = 0; $i < 7; $i++) {
            $targetTime = strtotime("+$i days");
            $targetDay = date('w', $targetTime);
            $dateLabel = date('d/m', $targetTime);

            // Calculate average for this specific day of week
            $avgStudents = !empty($dayAverages['student'][$targetDay]) 
                ? array_sum($dayAverages['student'][$targetDay]) / count($dayAverages['student'][$targetDay])
                : 5; // Default fallback if no data

            $avgTeachers = !empty($dayAverages['teacher'][$targetDay]) 
                ? array_sum($dayAverages['teacher'][$targetDay]) / count($dayAverages['teacher'][$targetDay])
                : 1; // Default fallback

            $forecast[] = [
                'date' => $dateLabel,
                'students' => round($avgStudents * $growthFactor),
                'teachers' => round($avgTeachers * $growthFactor)
            ];
        }

        $data = [
            'title' => 'Admin Statistics - APPOLIOS',
            'description' => 'Platform activity and ban analytics',
            'adminSidebarActive' => 'statistics',
            'stats' => $stats,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'forecast' => $forecast,
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/statistics', $data);
    }

    /**
     * Export users to PDF
     */
    public function exportUsersPDF()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $users = $this->getUsers();

        $data = [
            'title' => 'Users Report Export - APPOLIOS',
            'description' => 'Complete list of registered users',
            'users' => $users,
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/export_users', $data);
    }

    public function exportTeachersPDF()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $teachers = $this->getTeachers();

        $data = [
            'title' => 'Teachers Report Export - APPOLIOS',
            'description' => 'Complete list of registered teachers',
            'teachers' => $teachers,
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/export_teachers', $data);
    }

    private function getUsers()
    {
        $sql = "SELECT id, name, email, role, is_blocked, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Block a user (permanent)
     */
    public function blockUser($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        // Prevent blocking self
        if ((int) $id === (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'You cannot block yourself.');
            $this->redirect('admin/users');
            return;
        }

        $user = $this->findUserById((int) $id);

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
            return;
        }

        $sql = "UPDATE users SET is_blocked = 1, ban_until = NULL WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        if ($stmt->execute([$id])) {
            // Log Diff
            $this->logDiff(
                'block_user',
                ['is_blocked' => $user['is_blocked'], 'ban_until' => $user['ban_until']],
                ['is_blocked' => 1, 'ban_until' => null],
                "Admin blocked user: {$user['name']} ({$user['email']})"
            );

            $this->setFlash('success', 'User ' . htmlspecialchars($user['name']) . ' has been blocked permanently.');
        } else {
            $this->setFlash('error', 'Failed to block user.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Ban a user temporarily with duration
     */
    public function banUser($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        // Prevent banning self
        if ((int) $id === (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'You cannot ban yourself.');
            $this->redirect('admin/users');
            return;
        }

        $user = $this->findUserById((int) $id);

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
            return;
        }

        // Get duration from POST
        $duration = $_POST['ban_duration'] ?? 'permanent';

        // Calculate ban_until timestamp
        $banUntil = null;
        $banMessage = '';

        switch ($duration) {
            case '2h':
                $banUntil = date('Y-m-d H:i:s', strtotime('+2 hours'));
                $banMessage = 'banned for 2 hours';
                break;
            case '10h':
                $banUntil = date('Y-m-d H:i:s', strtotime('+10 hours'));
                $banMessage = 'banned for 10 hours';
                break;
            case '1d':
                $banUntil = date('Y-m-d H:i:s', strtotime('+1 day'));
                $banMessage = 'banned for 1 day';
                break;
            case 'permanent':
            default:
                $banUntil = null;
                $banMessage = 'blocked permanently';
                break;
        }

        try {
            $this->getDb()->exec("ALTER TABLE users ADD COLUMN ban_until DATETIME DEFAULT NULL");
        } catch (PDOException $e) {}

        // Use explicit column name with backticks to avoid any parsing issues
        $sql = "UPDATE `users` SET `is_blocked` = 1, `ban_until` = :ban_until WHERE `id` = :id";
        $stmt = $this->getDb()->prepare($sql);

        if ($stmt->execute(['ban_until' => $banUntil, 'id' => $id])) {
            // Log Diff
            $this->logDiff(
                'ban_user',
                ['is_blocked' => $user['is_blocked'], 'ban_until' => $user['ban_until']],
                ['is_blocked' => 1, 'ban_until' => $banUntil],
                "Admin " . $banMessage . ": {$user['name']}"
            );

            $this->setFlash('success', 'User ' . htmlspecialchars($user['name']) . ' has been ' . $banMessage . '.');
        } else {
            $this->setFlash('error', 'Failed to ban user.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Unblock a user
     */
    public function unblockUser($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        $user = $this->findUserById((int) $id);

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
            return;
        }

        $sql = "UPDATE users SET is_blocked = 0, ban_until = NULL WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        if ($stmt->execute([$id])) {
            // Log Diff
            $this->logDiff(
                'unblock_user',
                ['is_blocked' => $user['is_blocked'], 'ban_until' => $user['ban_until']],
                ['is_blocked' => 0, 'ban_until' => null],
                "Admin unblocked user: {$user['name']}"
            );

            $this->setFlash('success', 'User ' . htmlspecialchars($user['name']) . ' has been unblocked successfully.');
        } else {
            $this->setFlash('error', 'Failed to unblock user.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Delete a user permanently
     */
    public function deleteUser($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied.');
            $this->redirect('admin/login');
            return;
        }

        if ((int) $id === (int) $_SESSION['user_id']) {
            $this->setFlash('error', 'You cannot delete yourself.');
            $this->redirect('admin/users');
            return;
        }

        $user = $this->findUserById((int) $id);

        if (!$user) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('admin/users');
            return;
        }

        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        if ($stmt->execute([$id])) {
            $this->logDiff(
                'delete_user',
                ['id' => $id, 'email' => $user['email']],
                null,
                "Admin deleted user: {$user['name']} ({$user['email']})"
            );
            $this->setFlash('success', 'User has been deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete user.');
        }

        $this->redirect('admin/users');
    }

    /**
     * Contact Messages Inbox - List all messages
     */
    public function contactMessages()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $messages = $this->getAllContactMessages(100, 0);
        $unreadCount = $this->getContactMessageUnreadCount();

        $data = [
            'title' => 'Contact Messages - APPOLIOS',
            'description' => 'Manage contact messages from users',
            'adminSidebarActive' => 'contact-messages',
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/contact_messages', $data);
    }

    /**
     * Delete contact message
     */
    public function deleteContactMessage($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $sql = "DELETE FROM contact_messages WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);

        if ($stmt->execute([$id])) {
            $this->setFlash('success', 'Message deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete message.');
        }

        $this->redirect('admin/contact-messages');
    }

    /**
     * View single contact message
     */
    public function viewContactMessage($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        // Get message details
        $message = $this->getContactMessageById($id);

        if (!$message) {
            $this->setFlash('error', 'Message not found.');
            $this->redirect('admin/contact-messages');
            return;
        }

        // Mark as read if unread
        if (!$message['is_read']) {
            $this->markContactMessageAsRead($id, $_SESSION['user_id']);
            $message['is_read'] = 1;
            $message['reader_name'] = $_SESSION['user_name'];
            $message['read_at'] = date('Y-m-d H:i:s');
        }

        $data = [
            'title' => 'View Message - APPOLIOS',
            'description' => 'Contact message details',
            'adminSidebarActive' => 'contact-messages',
            'message' => $message,
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/view_contact_message', $data);
    }

    /**
     * Mark contact message as unread
     */
    public function markMessageUnread($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $sql = "UPDATE contact_messages SET is_read = 0, read_by = NULL, read_at = NULL WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);

        if ($stmt->execute([$id])) {
            $this->setFlash('success', 'Message marked as unread.');
        } else {
            $this->setFlash('error', 'Failed to mark message as unread.');
        }

        $this->redirect('admin/contact-messages');
    }

    /**
     * Courses list page
     */
    public function courses()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        $courses = $courseModel->getAllWithCreator();
        $db = $this->getDb();

        foreach ($courses as &$course) {
            $chCountStmt = $db->prepare("SELECT COUNT(*) as cnt FROM chapters WHERE course_id = ?");
            $chCountStmt->execute([$course['id']]);
            $course['chapters_count'] = (int) ($chCountStmt->fetch()['cnt'] ?? 0);

            $lesCountStmt = $db->prepare("SELECT COUNT(*) as cnt FROM lessons l JOIN chapters ch ON l.chapter_id = ch.id WHERE ch.course_id = ?");
            $lesCountStmt->execute([$course['id']]);
            $course['lessons_count'] = (int) ($lesCountStmt->fetch()['cnt'] ?? 0);
        }

        $data = [
            'title' => 'Manage Courses - APPOLIOS',
            'description' => 'Manage courses',
            'adminSidebarActive' => 'courses',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'courses' => $courses,
            'totalCourses' => $courseModel->countByStatus('pending') + $courseModel->countByStatus('approved') + $courseModel->countByStatus('rejected'),
            'pendingCount' => $courseModel->countByStatus('pending'),
            'approvedCount' => $courseModel->countByStatus('approved'),
            'rejectedCount' => $courseModel->countByStatus('rejected'),
            'totalEarnings' => (float) ($db->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status IN ('succeeded', 'completed')")->fetch()['total'] ?? 0),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/courses', $data);
    }

    /**
     * Add course page
     */
    public function addCourse()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $data = [
            'title' => 'Add Course - APPOLIOS',
            'description' => 'Create a new course',
            'adminSidebarActive' => 'add-course',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/add_course', $data);
    }

    /**
     * Generate course with AI
     */
    public function generateWithAI() {
        header('Content-Type: application/json');
        ob_clean(); // Ensure clean output
        
        if (!$this->isAdmin()) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }

        // Handle different types of AI generation
        $type = $_POST['type'] ?? 'course';

        if ($type === 'events' || $type === 'event_predictions') {
            $predictions = [
                'success' => true,
                'predictions' => [
                    'Attendance is expected to be high for technical workshops next month.',
                    'Students show increasing interest in AI and Web Development events.',
                    'Suggested time for your next event: Saturday afternoon.'
                ]
            ];
            echo json_encode($predictions);
            exit;
        }
        
        $topic = trim($_POST['topic'] ?? '');
        $audience = $_POST['audience'] ?? 'beginners';
        
        if (empty($topic)) {
            echo json_encode(['success' => false, 'error' => 'Please enter a course topic']);
            exit;
        }
        
        require_once __DIR__ . '/../Service/AICourseGenerator.php';
        $aiGenerator = new AICourseGenerator();
        
        $result = $aiGenerator->generateFullCourse($topic, $audience);
        
        echo json_encode($result);
        exit;
    }

    /**
     * Store new course with chapters and lessons
     */
    public function storeCourse()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/add-course');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = $_POST['image'] ?? '';
        $courseType = $_POST['course_type'] ?? '';
        $price = $_POST['price'] ?? 0.0;
        $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        
        // Validate price
        if (!is_numeric($price) || $price < 0) {
            $_SESSION['errors'] = ['price' => 'Invalid price. Must be a non-negative number.'];
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }
        
        // Handle course image upload
        if (isset($_FILES['course_image']) && !empty($_FILES['course_image']['tmp_name'])) {
            $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '_' . basename($_FILES['course_image']['name']);
            if (move_uploaded_file($_FILES['course_image']['tmp_name'], $uploadDir . $filename)) {
                $image = 'uploads/images/' . $filename;
            }
        }
        
        if (empty($title)) {
            $_SESSION['errors'] = ['title' => 'Course title is required'];
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }

        if (empty($description)) {
            $_SESSION['errors'] = ['description' => 'Course description is required'];
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-course');
            return;
        }

        $courseModel = $this->model('Course');
        $courseId = $courseModel->create([
            'title' => $title,
            'description' => $description,
            'status' => 'approved',
            'image' => $image,
            'course_type' => $courseType,
            'category_id' => $categoryId,
            'price' => $price,
            'created_by' => $_SESSION['user_id']
        ]);

        if ($courseId) {
            // Save chapters and lessons if provided
            $this->saveCourseChapters($courseId, $_POST);
            $this->saveCourseBadges($courseId, $_POST);
            $this->setFlash('success', 'Course created successfully!');
        } else {
            $this->setFlash('error', 'Failed to create course');
        }
        
        $this->redirect('admin/courses');
    }

    /**
     * Save chapters and lessons for a course
     */
    private function saveCourseChapters($courseId, $postData)
    {
        if (!isset($postData['chapters']) || empty($postData['chapters'])) {
            return;
        }

        $chapterModel = $this->model('Chapter');
        $lessonModel = $this->model('Lesson');

        foreach ($postData['chapters'] as $chapterIndex => $chapterData) {
            $chapterTitle = trim($chapterData['title'] ?? '');
            if (empty($chapterTitle)) continue;

            $chapterId = $chapterModel->create([
                'course_id' => $courseId,
                'title' => $chapterTitle,
                'description' => $chapterData['description'] ?? '',
                'sort_order' => $chapterIndex + 1
            ]);

            if (!$chapterId) continue;

            // Save lessons for this chapter
            if (isset($chapterData['lessons']) && is_array($chapterData['lessons'])) {
                $lessonOrder = 1;
                foreach ($chapterData['lessons'] as $lessonIndex => $lessonData) {
                    $lessonTitle = trim($lessonData['title'] ?? '');
                    if (empty($lessonTitle)) continue;

                    // Handle PDF upload if present
                    $pdfPath = null;
                    if (isset($_FILES['lessons']['tmp_name'][$chapterIndex][$lessonIndex]['pdf_file']) && 
                        !empty($_FILES['lessons']['tmp_name'][$chapterIndex][$lessonIndex]['pdf_file'])) {
                        
                        $tmpName = $_FILES['lessons']['tmp_name'][$chapterIndex][$lessonIndex]['pdf_file'];
                        $originalName = $_FILES['lessons']['name'][$chapterIndex][$lessonIndex]['pdf_file'];
                        
                        $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'lessons' . DIRECTORY_SEPARATOR;
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $filename = time() . '_' . $chapterIndex . '_' . $lessonIndex . '_' . basename($originalName);
                        if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
                            $pdfPath = 'uploads/lessons/' . $filename;
                        }
                    }

                    $lessonModel->createLesson([
                        'chapter_id' => $chapterId,
                        'title' => $lessonTitle,
                        'content' => $lessonData['content'] ?? '',
                        'lesson_type' => $lessonData['lesson_type'] ?? 'text',
                        'pdf_path' => $pdfPath,
                        'sort_order' => $lessonOrder++
                    ]);
                }
            }
        }
    }

    /**
     * Save course badges
     */
    private function saveCourseBadges($courseId, $postData)
    {
        if (!isset($postData['badges']) || empty($postData['badges'])) {
            return;
        }

        $badgeModel = $this->model('CourseBadge');

        foreach ($postData['badges'] as $badgeData) {
            $badgeName = trim($badgeData['badge_name'] ?? '');
            if (empty($badgeName)) continue;

            $badgeModel->create([
                'course_id' => $courseId,
                'badge_name' => $badgeName,
                'badge_icon' => $badgeData['badge_icon'] ?? 'trophy',
                'badge_condition' => $badgeData['badge_condition'] ?? 'completion',
                'description' => $badgeData['description'] ?? ''
            ]);
        }
    }

    /**
     * Add teacher page
     */
    public function addTeacher()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $data = [
            'title' => 'Add Teacher - APPOLIOS',
            'description' => 'Create a new teacher account',
            'adminSidebarActive' => 'add-teacher',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/add_teacher', $data);
    }

    public function storeTeacher() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/teachers');
            return;
        }

        $name = $this->sanitize($_POST['name'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if (empty($name)) $errors['name'] = 'Name is required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required';
        if (empty($password) || strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters';

        $userModel = $this->model('User');
        if ($userModel->emailExists($email)) $errors['email'] = 'Email already registered';

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/add-teacher');
            return;
        }

        $result = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'teacher'
        ]);

        if ($result) {
            $this->setFlash('success', 'Teacher account created successfully!');
            $this->redirect('admin/teachers');
        } else {
            $this->setFlash('error', 'Failed to create teacher account.');
            $this->redirect('admin/add-teacher');
        }
    }

    /**
     * Edit course page
     */
    public function editCourse($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $courseModel = $this->model('Course');
        // Use getWithChapters to get the full structure
        $course = $courseModel->getWithChapters($id);

        if (!$course) {
            $this->setFlash('error', 'Course not found');
            $this->redirect('admin/courses');
            return;
        }

        // Fetch badges if available
        $badges = [];
        try {
            $sql = "SELECT * FROM course_badges WHERE course_id = ?";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([$id]);
            $badges = $stmt->fetchAll();
        } catch (Exception $e) {
            // Table might not exist yet or other error
        }

        $data = [
            'title' => 'Edit Course - APPOLIOS',
            'description' => 'Update course details',
            'course' => $course,
            'badges' => $badges,
            'adminSidebarActive' => 'courses',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/edit_course', $data);
    }

    /**
     * Update course
     */
    public function updateCourse($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/edit-course/' . $id);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = $_POST['image'] ?? '';
        $courseType = $_POST['course_type'] ?? '';
        $price = $_POST['price'] ?? 0.0;
        $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        
        $courseModel = $this->model('Course');
        $course = $courseModel->findById($id);
        
        if (!$course) {
            $this->setFlash('error', 'Course not found');
            $this->redirect('admin/courses');
            return;
        }

        // Handle course image upload
        if (isset($_FILES['course_image']) && !empty($_FILES['course_image']['tmp_name'])) {
            $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = time() . '_' . basename($_FILES['course_image']['name']);
            if (move_uploaded_file($_FILES['course_image']['tmp_name'], $uploadDir . $filename)) {
                $image = 'uploads/images/' . $filename;
            }
        } else {
            $image = $course['image'];
        }

        $result = $courseModel->update($id, [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'course_type' => $courseType,
            'category_id' => $categoryId,
            'status' => $course['status'],
            'price' => $price,
            'admin_message' => $course['admin_message']
        ]);

        if ($result) {
            // Recreate structure
            $this->clearCourseContent($id);
            $this->saveCourseChapters($id, $_POST);
            $this->saveCourseBadges($id, $_POST);
            $this->setFlash('success', 'Course updated successfully!');
        } else {
            $this->setFlash('error', 'Failed to update course');
        }
        
        $this->redirect('admin/courses');
    }

    private function clearCourseContent($courseId) {
        $db = $this->getDb();
        $db->prepare("DELETE FROM course_badges WHERE course_id = ?")->execute([$courseId]);
        $db->prepare("DELETE FROM chapters WHERE course_id = ?")->execute([$courseId]);
    }

    /**
     * Manage teachers page
     */
    public function teachers()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $teachers = $this->getTeachers();

        $data = [
            'title' => 'Manage Teachers - APPOLIOS',
            'description' => 'Teacher management panel',
            'teachers' => $teachers,
            'adminSidebarActive' => 'teachers',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/teachers', $data);
    }

    /**
     * Edit evenement page
     */
    public function editEvenement($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findById($id);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found.');
            $this->redirect('admin/evenements');
            return;
        }

        $data = [
            'title' => 'Edit Evenement - APPOLIOS',
            'description' => 'Update evenement details',
            'evenement' => $evenement,
            'adminSidebarActive' => 'evenements',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/edit_evenement', $data);
    }

    /**
    public function updateEvenement($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }

        $title = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $dateDebut = $this->sanitize($_POST['date_debut'] ?? '');
        $dateFin = $this->sanitize($_POST['date_fin'] ?? '');
        $heureDebut = $this->sanitize($_POST['heure_debut'] ?? '');
        $heureFin = $this->sanitize($_POST['heure_fin'] ?? '');
        $lieu = $this->sanitize($_POST['lieu'] ?? '');
        $capaciteMax = (int) ($_POST['capacite_max'] ?? 0);
        $type = $this->sanitize($_POST['type'] ?? 'general');
        $statut = $this->sanitize($_POST['statut'] ?? 'planifie');

        $errors = [];

        if (empty($title)) {
            $errors['title'] = 'Event title is required';
        }

        if (empty($description)) {
            $errors['description'] = 'Event description is required';
        }

        if (empty($dateDebut) || strtotime($dateDebut) === false) {
            $errors['date_debut'] = 'Valid start date is required';
        }

        $minDate = date('Y-m-d', strtotime('+1 day'));
        if (!empty($dateDebut) && strtotime($dateDebut) !== false && $dateDebut < $minDate) {
            $errors['date_debut'] = 'Start date must be at least tomorrow';
        }

        if (empty($heureDebut)) {
            $errors['heure_debut'] = 'Start time is required';
        }

        if (!empty($dateFin) && strtotime($dateFin) !== false && !empty($dateDebut) && strtotime($dateFin) < strtotime($dateDebut)) {
            $errors['date_fin'] = 'End date cannot be before start date';
        }

        if ($capaciteMax < 0) {
            $errors['capacite_max'] = 'Capacity must be a positive number';
        }

        if (!empty($errors)) {
            $this->setErrors($errors);
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/edit-evenement/' . (int) $id);
            return;
        }

        $eventDate = $dateDebut . ' ' . (!empty($heureDebut) ? $heureDebut : '00:00') . ':00';

        $evenementModel = $this->model('Evenement');
        $result = $evenementModel->update($id, [
            'title' => $title,
            'titre' => $title,
            'description' => $description,
            'date_debut' => $dateDebut,
            'date_fin' => !empty($dateFin) ? $dateFin : null,
            'heure_debut' => !empty($heureDebut) ? $heureDebut : null,
            'heure_fin' => !empty($heureFin) ? $heureFin : null,
            'lieu' => $lieu,
            'capacite_max' => $capaciteMax > 0 ? $capaciteMax : null,
            'type' => $type,
            'statut' => $statut,
            'location' => $lieu,
            'event_date' => $eventDate
        ]);

        if ($result) {
            $this->setFlash('success', 'Evenement updated successfully!');
            $this->redirect('admin/evenements');
        } else {
            $this->setFlash('error', 'Failed to update evenement. Please try again.');
            $this->redirect('admin/edit-evenement/' . (int) $id);
        }
    }

    /**
     * Delete evenement
     */
    public function deleteEvenement($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $evenement = $evenementModel->findById($id);

        if (!$evenement) {
            $this->setFlash('error', 'Evenement not found.');
            $this->redirect('admin/evenements');
            return;
        }

        if ($evenement['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error', 'You can only delete events that you have created.');
            $this->redirect('admin/evenements');
            return;
        }

        $result = $evenementModel->delete($id);

        if ($result) {
            $this->setFlash('success', 'Evenement deleted successfully!');
        } else {
            $this->setFlash('error', 'Failed to delete evenement.');
        }

        $this->redirect('admin/evenements');
    }

    /**
     * Evenement resources workspace page
     */
    public function evenementRessources()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $evenementModel = $this->model('Evenement');
        $selectedEvenementId = (int) ($_GET['evenement_id'] ?? 0);

        if ($selectedEvenementId <= 0) {
            $this->setFlash('error', 'Please choose an evenement first.');
            $this->redirect('admin/evenements');
            return;
        }

        $selectedEvenement = $evenementModel->findById($selectedEvenementId);
        if (!$selectedEvenement) {
            $this->setFlash('error', 'Selected evenement was not found.');
            $this->redirect('admin/evenements');
            return;
        }

        $editId = (int) ($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $ressourceModel->findById($editId);
            if ($candidate && (int) $candidate['evenement_id'] === $selectedEvenementId) {
                $editResource = $candidate;
            }
        }

        $rules = $ressourceModel->getByTypeAndEvenement('rule', $selectedEvenementId);
        $materials = $ressourceModel->getByTypeAndEvenement('materiel', $selectedEvenementId);
        $plans = $ressourceModel->getByTypeAndEvenement('plan', $selectedEvenementId);

        $data = [
            'title' => 'Evenement Resources - APPOLIOS',
            'description' => 'Manage evenement rules, materiel, and day plans',
            'selectedEvenementId' => $selectedEvenementId,
            'selectedEvenement' => $selectedEvenement,
            'editResource' => $editResource,
            'rules' => $rules,
            'materials' => $materials,
            'plans' => $plans,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/evenement_ressources', $data);
    }

    /**
     * Store one evenement resource item
     */
    public function storeEvenementRessource()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenement-ressources');
            return;
        }

        $type = $this->sanitize($_POST['type'] ?? '');
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');
        $evenementId = (int) ($_POST['evenement_id'] ?? 0);
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_POST['batch_mode']) && $_POST['batch_mode'] === '1');

        $errors = [];

        if (!in_array($type, ['rule', 'materiel', 'plan'], true)) {
            $errors[] = 'Invalid resource type.';
        }

        if (empty($title)) {
            $errors[] = 'Title is required.';
        }

        if ($evenementId <= 0) {
            $errors[] = 'Please select an evenement.';
        } else {
            $evenementModel = $this->model('Evenement');
            if (!$evenementModel->findById($evenementId)) {
                $errors[] = 'Selected evenement was not found.';
            }
        }

        if (!empty($errors)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => implode(' ', $errors)
                ]);
                exit();
            }

            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $createdId = $ressourceModel->create([
            'evenement_id' => $evenementId,
            'type' => $type,
            'title' => $title,
            'details' => $details,
            'created_by' => $_SESSION['user_id']
        ]);

        $isVerifiedInRightList = false;
        if ($createdId) {
            $isVerifiedInRightList = $ressourceModel->existsInListScope($createdId, $evenementId, $type);
        }

        if ($createdId && $isVerifiedInRightList) {
            $labels = [
                'rule' => 'Rule',
                'materiel' => 'Materiel',
                'plan' => 'Plan'
            ];

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $labels[$type] . ' saved and verified in list successfully.',
                    'verified_in_right_list' => true,
                    'resource_id' => (int) $createdId
                ]);
                exit();
            }

            $this->setFlash('success', $labels[$type] . ' added and verified in list successfully.');
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Save verification failed. Check the right list and try again.',
                    'verified_in_right_list' => false
                ]);
                exit();
            }

            $this->setFlash('error', 'Save verification failed. Check the right list and try again.');
        }

        $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * Update one evenement resource item.
     */
    public function updateEvenementRessource($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }

        $evenementId = (int) ($_POST['evenement_id'] ?? 0);
        $title = $this->sanitize($_POST['title'] ?? '');
        $details = $this->sanitize($_POST['details'] ?? '');

        if ($evenementId <= 0 || empty($title)) {
            $this->setFlash('error', 'Please provide valid data before saving.');
            $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId . '&edit_id=' . (int) $id);
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $resource = $ressourceModel->findById($id);

        if (!$resource || (int) $resource['evenement_id'] !== $evenementId) {
            $this->setFlash('error', 'Resource not found for this evenement.');
            $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
            return;
        }

        $result = $ressourceModel->update($id, [
            'title' => $title,
            'details' => $details,
            'evenement_id' => $evenementId
        ]);

        if ($result) {
            $this->setFlash('success', 'Ressource updated successfully.');
        } else {
            $this->setFlash('error', 'Failed to update ressource.');
        }

        $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * Delete one evenement resource item.
     */
    public function deleteEvenementRessource($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenements');
            return;
        }

        $evenementId = (int) ($_POST['evenement_id'] ?? 0);
        if ($evenementId <= 0) {
            $this->setFlash('error', 'Invalid evenement context.');
            $this->redirect('admin/evenements');
            return;
        }

        $ressourceModel = $this->model('EvenementRessource');
        $result = $ressourceModel->deleteByEvenement($id, $evenementId);

        if ($result) {
            $this->setFlash('success', 'Ressource deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete ressource.');
        }

        $this->redirect('admin/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * List teacher evenement requests awaiting admin review.
     */
    public function evenements()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        
        // Fetch all participations for the modal
        $db = $this->getDb();
        $sql = "SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    e.title as event_title, e.created_by as event_creator_id,
                    u.name as student_name_full, u.email as student_email,
                    u.role as student_role, u.created_at as student_registered_at
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             JOIN users u ON r.created_by = u.id
             WHERE r.type = 'participation'
             ORDER BY r.created_at DESC";
        $stmt = $db->query($sql);
        $allParticipations = $stmt->fetchAll();
        
        $participationsByEvent = [];
        foreach ($allParticipations as $p) {
            $eventId = (int)$p['evenement_id'];
            if (!isset($participationsByEvent[$eventId])) {
                $participationsByEvent[$eventId] = [];
            }
            $participationsByEvent[$eventId][] = $p;
        }

        $data = [
            'title' => 'Manage Events - APPOLIOS',
            'description' => 'Event management panel',
            'adminSidebarActive' => 'evenements',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'evenements' => $evenementModel->getAllWithCreator(),
            'participationsByEvent' => $participationsByEvent,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/evenements', $data);
    }

    public function approveParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('admin/evenements'); return; }

        $db = $this->getDb();
        $stmt = $db->prepare(
            "SELECT r.*, e.created_by as event_creator_id
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.id = ? AND r.type = 'participation' LIMIT 1"
        );
        $stmt->execute([(int)$id]);
        $participation = $stmt->fetch();

        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
            $this->redirect('admin/evenements');
            return;
        }

        if ((int)$participation['event_creator_id'] !== (int)$_SESSION['user_id']) {
            $this->setFlash('error', 'You can only manage participations for events you created.');
            $this->redirect('admin/evenements');
            return;
        }

        $upd = $db->prepare(
            "UPDATE evenement_ressources
             SET details = 'approved', updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = 'participation'"
        );
        $upd->execute([(int)$id])
            ? $this->setFlash('success', 'Participation approved.')
            : $this->setFlash('error', 'Failed to approve.');

        $this->redirect('admin/evenements');
    }

    public function rejectParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('admin/evenements'); return; }

        $reason = trim($_POST['reason'] ?? '');
        $db = $this->getDb();
        $stmt = $db->prepare(
            "SELECT r.*, e.created_by as event_creator_id
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.id = ? AND r.type = 'participation' LIMIT 1"
        );
        $stmt->execute([(int)$id]);
        $participation = $stmt->fetch();

        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
            $this->redirect('admin/evenements');
            return;
        }

        if ((int)$participation['event_creator_id'] !== (int)$_SESSION['user_id']) {
            $this->setFlash('error', 'You can only manage participations for events you created.');
            $this->redirect('admin/evenements');
            return;
        }

        $upd = $db->prepare(
            "UPDATE evenement_ressources
             SET details = 'rejected', rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = 'participation'"
        );
        $upd->execute([$reason, (int)$id])
            ? $this->setFlash('success', 'Participation rejected.')
            : $this->setFlash('error', 'Failed to reject.');

        $this->redirect('admin/evenements');
    }

    public function evenementRequests()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $data = [
            'title' => 'Evenement Requests - APPOLIOS',
            'description' => 'Review pending evenement requests from teachers',
            'requests' => $evenementModel->getPendingTeacherRequests(),
            'rejectedRequests' => $evenementModel->getRejectedTeacherRequests(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/evenement_requests', $data);
    }

    /**
     * Approve teacher evenement request.
     */
    public function approveEvenement($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenement-requests');
            return;
        }

        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findById((int) $id);
        if (!$event) {
            $this->setFlash('error', 'Evenement request not found.');
            $this->redirect('admin/evenement-requests');
            return;
        }

        $result = $evenementModel->updateApprovalStatus((int) $id, 'approved', (int) $_SESSION['user_id']);
        if ($result) {
            $this->setFlash('success', 'Evenement request approved successfully.');
        } else {
            $this->setFlash('error', 'Failed to approve evenement request.');
        }

        $this->redirect('admin/evenement-requests');
    }

    /**
     * Reject teacher evenement request.
     */
    public function rejectEvenement($id)
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/evenement-requests');
            return;
        }

        $reason = $this->sanitize($_POST['rejection_reason'] ?? '');
        $evenementModel = $this->model('Evenement');
        $event = $evenementModel->findById((int) $id);
        if (!$event) {
            $this->setFlash('error', 'Evenement request not found.');
            $this->redirect('admin/evenement-requests');
            return;
        }

        $result = $evenementModel->updateApprovalStatus((int) $id, 'rejected', (int) $_SESSION['user_id'], $reason ?: null);
        if ($result) {
            $this->setFlash('success', 'Evenement request rejected.');
        } else {
            $this->setFlash('error', 'Failed to reject evenement request.');
        }

        $this->redirect('admin/evenement-requests');
    }

    /**
     * Teacher applications management page
     */
    public function teacherApplications()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $data = [
            'title' => 'Teacher Applications - APPOLIOS',
            'description' => 'Manage teacher registration requests',
            'applications' => $this->getPendingApplications(),
            'pendingCount' => $this->countPendingApplications(),
            'pendingTeacherApps' => $this->countPendingApplications(),
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'adminSidebarActive' => 'teacher-applications',
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/teacher_applications', $data);
    }

    /**
     * Approve teacher application
     */
    public function approveTeacher()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/teacher-applications');
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $adminNotes = $this->sanitize($_POST['admin_notes'] ?? '');

        // Get application details
        $application = $this->getApplicationById($applicationId);
        if (!$application) {
            $this->setFlash('error', 'Application not found.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        // Check if user already exists with this email
        if ($this->emailExists($application['email'])) {
            // User already exists, just update application status and send email
            $this->approveApplication($applicationId, (int) $_SESSION['user_id'], $adminNotes);

            require_once __DIR__ . '/MailService.php';
            $emailSent = MailService::sendTeacherApproved(
                $application['email'],
                $application['name'],
                $adminNotes
            );

            if ($emailSent) {
                $this->setFlash('success', 'Teacher application approved! An email has been sent to ' . htmlspecialchars($application['email']) . '.');
            } else {
                $this->setFlash('success', 'Teacher application approved! Failed to send email - check sendmail configuration.');
            }
            $this->redirect('admin/teacher-applications');
            return;
        }

        // Create user account for teacher using User Model - MVC Pattern
        $userId = $this->createUserWithHashedPassword([
            'name' => $application['name'],
            'email' => $application['email'],
            'password' => $application['password'], // Already hashed in teacher_applications
            'role' => 'teacher'
        ]);

        if ($userId) {
            // Copy face_descriptor from application to the new teacher account (if any) using Model
            if (!empty($application['face_descriptor'])) {
                $this->updateFaceDescriptor($userId, $application['face_descriptor']);
            }

            // Update application status
            $this->approveApplication($applicationId, (int) $_SESSION['user_id'], $adminNotes);

            // Log activity
            $this->logActivity(
                'approve_teacher',
                "Admin approved teacher application: {$application['name']} ({$application['email']})",
                $_SESSION['user_id'],
                $_SESSION['user_name'],
                $_SESSION['user_email'],
                'admin'
            );

            // Send approval email
            require_once __DIR__ . '/MailService.php';
            $emailSent = MailService::sendTeacherApproved(
                $application['email'],
                $application['name'],
                $adminNotes
            );

            if ($emailSent) {
                $this->setFlash('success', 'Teacher application approved! An email has been sent to ' . htmlspecialchars($application['email']) . '.');
            } else {
                $this->setFlash('success', 'Teacher application approved! Failed to send email - check sendmail configuration.');
            }
        } else {
            $this->setFlash('error', 'Failed to create teacher account.');
        }

        $this->redirect('admin/teacher-applications');
    }

    /**
     * Reject teacher application
     */
    public function rejectTeacher()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/teacher-applications');
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $adminNotes = $this->sanitize($_POST['admin_notes'] ?? '');

        if (empty($adminNotes)) {
            $this->setFlash('error', 'Rejection reason is required.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        $application = $this->getApplicationById($applicationId);

        if (!$application) {
            $this->setFlash('error', 'Application not found.');
            $this->redirect('admin/teacher-applications');
            return;
        }

        // Delete CV file
        $cvPath = __DIR__ . '/../' . $application['cv_path'];
        if (file_exists($cvPath)) {
            unlink($cvPath);
        }

        // Update application status
        $result = $this->rejectApplication($applicationId, (int) $_SESSION['user_id'], $adminNotes);

        if ($result) {
            // Log activity
            $this->logActivity(
                'reject_teacher',
                "Admin rejected teacher application: {$application['name']} ({$application['email']})",
                $_SESSION['user_id'],
                $_SESSION['user_name'],
                $_SESSION['user_email'],
                'admin'
            );

            // Send rejection email
            require_once __DIR__ . '/MailService.php';
            $emailSent = MailService::sendTeacherRejected(
                $application['email'],
                $application['name'],
                $adminNotes
            );

            if ($emailSent) {
                $this->setFlash('success', 'Teacher application rejected. A notification email has been sent to ' . htmlspecialchars($application['email']) . '.');
            } else {
                $this->setFlash('success', 'Teacher application rejected. Failed to send email - check sendmail configuration.');
            }
        } else {
            $this->setFlash('error', 'Failed to reject application.');
        }

        $this->redirect('admin/teacher-applications');
    }

    /**
     * Activity Log / History Page
     */
    public function activityLog()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        // Get filters
        $filters = [
            'user_id' => $_GET['user_id'] ?? null,
            'activity_type' => $_GET['activity_type'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
        ];

        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $activities = $this->getFilteredActivities($filters, $limit, $offset);
        $totalActivities = $this->countFilteredActivities($filters);
        $totalPages = ceil($totalActivities / $limit);

        // Get stats
        $stats = [
            'total' => $totalActivities,
            'logins' => $this->countActivitiesByType('login'),
            'logouts' => $this->countActivitiesByType('logout'),
            'registers' => $this->countActivitiesByType('register'),
        ];

        $data = [
            'title' => 'Activity Log - APPOLIOS',
            'description' => 'View all user activities on the platform',
            'activities' => $activities,
            'stats' => $stats,
            'filters' => $filters,
            'adminSidebarActive' => 'activity-log',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'page' => $page,
            'totalPages' => $totalPages,
            'totalActivities' => $totalActivities,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/activity_log', $data);
    }

    /**
     * Activity Map View
     */
    public function activityMap()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        // Get all activities that have coordinates
        $activities = $this->getAllActivities(200); // Get last 200 activities

        $data = [
            'title' => 'Carte d\'Activité - APPOLIOS',
            'description' => 'Visualisation géographique des activités',
            'activities' => $activities,
            'adminSidebarActive' => 'activity-map',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/activity_map', $data);
    }

    // ==========================================
    // DATABASE METHODS - For User operations
    // ==========================================

    public function countUsers()
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->getDb()->query($sql);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    public function countStudents()
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'student'";
        $stmt = $this->getDb()->query($sql);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function getStudents()
    {
        // Try with ban_until column first, fallback without it if column doesn't exist
        try {
            $sql = "SELECT id, name, email, role, is_blocked, ban_until, created_at FROM users WHERE role = 'student' ORDER BY created_at DESC";
            $stmt = $this->getDb()->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Fallback if ban_until column doesn't exist yet
            $sql = "SELECT id, name, email, role, is_blocked, created_at FROM users WHERE role = 'student' ORDER BY created_at DESC";
            $stmt = $this->getDb()->query($sql);
            return $stmt->fetchAll();
        }
    }


    public function findUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getTeachers()
    {
        $sql = "SELECT * FROM users WHERE role = 'teacher' ORDER BY created_at DESC";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll();
    }

    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function emailExists($email)
    {
        return $this->findUserByEmail($email) !== false;
    }

    public function createUser($data)
    {
        $sql = "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]),
                $data['role'] ?? 'student'
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createUserWithHashedPassword($data)
    {
        $sql = "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'] ?? 'teacher'
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            error_log('Admin Approve Teacher DB Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateFaceDescriptor($id, $faceDescriptor)
    {
        $sql = "UPDATE users SET face_descriptor = ? WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$faceDescriptor, $id]);
    }

    public function createContactMessage($data)
    {
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['subject'],
                $data['message']
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            error_log("createContactMessage error: " . $e->getMessage());
            return false;
        }
    }

    public function getAllContactMessages($limit = 50, $offset = 0)
    {
        $sql = "SELECT cm.*, u.name AS reader_name FROM contact_messages cm LEFT JOIN users u ON cm.read_by = u.id ORDER BY cm.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getContactMessageUnreadCount()
    {
        $sql = "SELECT COUNT(*) FROM contact_messages WHERE is_read = 0";
        $stmt = $this->getDb()->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function getContactMessageById($id)
    {
        $sql = "SELECT cm.*, u.name AS reader_name FROM contact_messages cm LEFT JOIN users u ON cm.read_by = u.id WHERE cm.id = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function markContactMessageAsRead($id, $adminId)
    {
        $sql = "UPDATE contact_messages SET is_read = 1, read_by = ?, read_at = NOW() WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$adminId, $id]);
    }

    public function markContactMessageAsUnread($id)
    {
        $sql = "UPDATE contact_messages SET is_read = 0, read_by = NULL, read_at = NULL WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function createTeacherApplication($data)
    {
        $sql = "INSERT INTO teacher_applications (name, email, password, cv_filename, cv_path, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['password'],
                $data['cv_filename'],
                $data['cv_path']
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createTeacherApplicationWithFace($data)
    {
        $sql = "INSERT INTO teacher_applications (name, email, password, cv_filename, cv_path, face_descriptor, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]),
                $data['cv_filename'],
                $data['cv_path'],
                $data['face_descriptor'] ?? null
            ]);
            return $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getPendingApplications()
    {
        $sql = "SELECT * FROM teacher_applications WHERE status = 'pending' ORDER BY created_at DESC";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll();
    }

    public function getApplicationById($id)
    {
        $sql = "SELECT * FROM teacher_applications WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function approveApplication($id, $adminId, $notes = '')
    {
        $sql = "UPDATE teacher_applications SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ? WHERE id = ?";
        try {
            $stmt = $this->getDb()->prepare($sql);
            return $stmt->execute([$adminId, $notes, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function rejectApplication($id, $adminId, $notes = '')
    {
        $sql = "UPDATE teacher_applications SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ? WHERE id = ?";
        try {
            $stmt = $this->getDb()->prepare($sql);
            return $stmt->execute([$adminId, $notes, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // ==========================================

    /**
     * Get all activities with pagination
     */
    public function getAllActivities(int $limit = 50, int $offset = 0): array {
        $sql = "SELECT * FROM activity_log
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Get activities by user ID
     */
    public function getActivitiesByUserId(int $userId, int $limit = 50): array {
        $sql = "SELECT * FROM activity_log
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?";

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get activities by type
     */
    public function getActivitiesByType(string $activityType, int $limit = 50): array {
        $sql = "SELECT * FROM activity_log
                WHERE activity_type = ?
                ORDER BY created_at DESC
                LIMIT ?";

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$activityType, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10): array {
        $sql = "SELECT * FROM activity_log
                ORDER BY created_at DESC
                LIMIT ?";

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Count total activities
     */
    public function countAllActivities(): int {
        $sql = "SELECT COUNT(*) as count FROM activity_log";
        $stmt = $this->getDb()->query($sql);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Count activities by type
     */
    public function countActivitiesByType(string $activityType): int {
        $sql = "SELECT COUNT(*) as count FROM activity_log WHERE activity_type = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$activityType]);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Get activities with filters
     */
    public function getFilteredActivities(array $filters, int $limit = 50, int $offset = 0): array {
        $where = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = "user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['activity_type'])) {
            $where[] = "activity_type = ?";
            $params[] = $filters['activity_type'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "created_at <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT * FROM activity_log {$whereClause}
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFilteredActivities(array $filters): int {
        $where = [];
        $params = [];

        if (!empty($filters['user_id'])) {
            $where[] = "user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['activity_type'])) {
            $where[] = "activity_type = ?";
            $params[] = $filters['activity_type'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "created_at <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) as count FROM activity_log {$whereClause}";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }


    /**
     * Get activity type labels
     */
    public function getActivityTypeLabel(string $type): string {
        $labels = [
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'register' => 'Inscription',
            'view_page' => 'Navigation',
            'create_course' => 'Création de cours',
            'update_course' => 'Modification de cours',
            'delete_course' => 'Suppression de cours',
            'create_event' => 'Création d\'événement',
            'update_event' => 'Modification d\'événement',
            'delete_event' => 'Suppression d\'événement',
            'approve_teacher' => 'Approbation professeur',
            'reject_teacher' => 'Rejet professeur',
            'block_user' => 'Blocage utilisateur',
            'unblock_user' => 'Déblocage utilisateur',
            'delete_user' => 'Suppression utilisateur',
            'reset_password' => 'Réinitialisation mot de passe',
            'change_password' => 'Changement mot de passe',
            'upload_file' => 'Téléchargement de fichier',
            'delete_file' => 'Suppression de fichier',
            'create_quiz' => 'Création de quiz',
            'update_quiz' => 'Modification de quiz',
            'delete_quiz' => 'Suppression de quiz',
            'create_question' => 'Création de question',
            'update_question' => 'Modification de question',
            'delete_question' => 'Suppression de question',
        ];

        return $labels[$type] ?? ucfirst($type);
    }

    // Quiz Management Methods

    /**
     * Admin quiz management page
     */
    public function quizzes()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $quizModel = $this->model('Quiz');
        $quizzes = $quizModel->getAllWithDetails();

        $data = [
            'title' => 'Quiz Management - Admin',
            'description' => 'Manage all quizzes in the system',
            'quizzes' => $quizzes,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/quizzes', $data);
    }

    /**
     * Admin question bank management
     */
    public function questions()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $questionBankModel = $this->model('QuestionBank');
        $questions = $questionBankModel->getAllWithStats();
        $stats = $questionBankModel->getTopStats();
        $charts = $questionBankModel->getChartData();
        $questionQa = $questionBankModel->getQualityAssessment();

        $data = [
            'title' => 'Question Bank - Admin',
            'description' => 'Manage the system question bank',
            'questions' => $questions,
            'qbTopStats' => $stats,
            'charts' => $charts,
            'questionQa' => $questionQa,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/questions_bank', $data);
    }

    /**
     * Add new quiz (admin)
     */
    public function addQuiz()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quizModel = $this->model('Quiz');
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'course_id' => (int) ($_POST['course_id'] ?? 0),
                'chapter_id' => (int) ($_POST['chapter_id'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? '',
                'time_limit_sec' => (int) ($_POST['time_limit_sec'] ?? 0),
                'status' => 'approved',
                'created_by' => $_SESSION['user_id']
            ];

            $quizId = $quizModel->create($data);
            
            if ($quizId) {
                // Add questions if provided
                if (!empty($_POST['questions'])) {
                    $questionBankModel = $this->model('QuestionBank');
                    foreach ($_POST['questions'] as $questionId) {
                        $quizModel->addQuestion($quizId, $questionId);
                    }
                }

                $this->logActivity('create_quiz', "Created quiz: {$data['title']}");
                $this->setFlash('success', 'Quiz created successfully.');
                $this->redirect('admin-quiz/quizzes');
            } else {
                $this->setFlash('error', 'Failed to create quiz.');
            }
        }

        $courseModel = $this->model('Course');
        $chapterModel = $this->model('Chapter');
        $questionBankModel = $this->model('QuestionBank');

        $data = [
            'title' => 'Add Quiz - Admin',
            'description' => 'Create a new quiz',
            'courses' => $courseModel->getAll(),
            'chapters' => $chapterModel->getAll(),
            'questions' => $questionBankModel->getAll(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/quiz_form', $data);
    }

    /**
     * Edit quiz (admin)
     */
    public function editQuiz($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->getById((int) $id);

        if (!$quiz) {
            $this->setFlash('error', 'Quiz not found.');
            $this->redirect('admin-quiz/quizzes');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'course_id' => (int) ($_POST['course_id'] ?? 0),
                'chapter_id' => (int) ($_POST['chapter_id'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? '',
                'time_limit_sec' => (int) ($_POST['time_limit_sec'] ?? 0)
            ];

            if ($quizModel->update((int) $id, $data)) {
                // Update questions if provided
                if (isset($_POST['questions'])) {
                    $quizModel->updateQuestions((int) $id, $_POST['questions']);
                }

                $this->logActivity('update_quiz', "Updated quiz: {$data['title']}");
                $this->setFlash('success', 'Quiz updated successfully.');
                $this->redirect('admin-quiz/quizzes');
            } else {
                $this->setFlash('error', 'Failed to update quiz.');
            }
        }

        $courseModel = $this->model('Course');
        $chapterModel = $this->model('Chapter');
        $questionBankModel = $this->model('QuestionBank');

        $data = [
            'title' => 'Edit Quiz - Admin',
            'description' => 'Edit quiz',
            'quiz' => $quiz,
            'courses' => $courseModel->getAll(),
            'chapters' => $chapterModel->getAll(),
            'questions' => $questionBankModel->getAll(),
            'quizQuestions' => $quizModel->getQuestions((int) $id),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/quiz_form', $data);
    }

    /**
     * Delete quiz (admin)
     */
    public function deleteQuiz($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $quizModel = $this->model('Quiz');
        $quiz = $quizModel->getById((int) $id);

        if ($quiz && $quizModel->delete((int) $id)) {
            $this->logActivity('delete_quiz', "Deleted quiz: {$quiz['title']}");
            $this->setFlash('success', 'Quiz deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete quiz.');
        }

        $this->redirect('admin-quiz/quizzes');
    }

    /**
     * Add new question (admin)
     */
    public function addQuestion()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questionBankModel = $this->model('QuestionBank');
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'question_text' => $_POST['question_text'] ?? '',
                'options' => $_POST['options'] ?? [],
                'correct_answer' => (int) ($_POST['correct_answer'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? '',
                'created_by' => $_SESSION['user_id']
            ];

            $questionId = $questionBankModel->create($data);
            
            if ($questionId) {
                $this->logActivity('create_question', "Created question: {$data['title']}");
                $this->setFlash('success', 'Question created successfully.');
                $this->redirect('admin-quiz/questions');
            } else {
                $this->setFlash('error', 'Failed to create question.');
            }
        }

        $data = [
            'title' => 'Add Question - Admin',
            'description' => 'Create a new question',
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/question_form', $data);
    }

    /**
     * Edit question (admin)
     */
    public function editQuestion($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $questionBankModel = $this->model('QuestionBank');
        $question = $questionBankModel->getById((int) $id);

        if (!$question) {
            $this->setFlash('error', 'Question not found.');
            $this->redirect('admin-quiz/questions');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'] ?? '',
                'question_text' => $_POST['question_text'] ?? '',
                'options' => $_POST['options'] ?? [],
                'correct_answer' => (int) ($_POST['correct_answer'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? ''
            ];

            if ($questionBankModel->update((int) $id, $data)) {
                $this->logActivity('update_question', "Updated question: {$data['title']}");
                $this->setFlash('success', 'Question updated successfully.');
                $this->redirect('admin-quiz/questions');
            } else {
                $this->setFlash('error', 'Failed to update question.');
            }
        }

        $data = [
            'title' => 'Edit Question - Admin',
            'description' => 'Edit question',
            'question' => $question,
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/question_form', $data);
    }

    /**
     * Delete question (admin)
     */
    public function deleteQuestion($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        $questionBankModel = $this->model('QuestionBank');
        $question = $questionBankModel->getById((int) $id);

        if ($question && $questionBankModel->delete((int) $id)) {
            $this->logActivity('delete_question', "Deleted question: {$question['title']}");
            $this->setFlash('success', 'Question deleted successfully.');
        } else {
            $this->setFlash('error', 'Failed to delete question.');
        }

        $this->redirect('admin-quiz/questions');
    }

    /**
     * Update quiz question (admin)
     */
    public function updateQuestion($id)
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questionBankModel = $this->model('QuestionBank');
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'question_text' => $_POST['question_text'] ?? '',
                'options' => $_POST['options'] ?? [],
                'correct_answer' => (int) ($_POST['correct_answer'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? ''
            ];

            if ($questionBankModel->update((int) $id, $data)) {
                $this->logActivity('update_question', "Updated question: {$data['title']}");
                $this->setFlash('success', 'Question updated successfully.');
                $this->redirect('admin-quiz/questions');
            } else {
                $this->setFlash('error', 'Failed to update question.');
            }
        }

        $this->redirect('admin-quiz/questions');
    }

    /**
     * Store new question (admin)
     */
    public function storeQuestion()
    {
        if (!$this->isAdmin()) {
            $this->setFlash('error', 'Access denied. Admin privileges required.');
            $this->redirect('admin/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questionBankModel = $this->model('QuestionBank');
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'question_text' => $_POST['question_text'] ?? '',
                'options' => $_POST['options'] ?? [],
                'correct_answer' => (int) ($_POST['correct_answer'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'beginner',
                'tags' => $_POST['tags'] ?? '',
                'created_by' => $_SESSION['user_id']
            ];

            $questionId = $questionBankModel->create($data);
            
            if ($questionId) {
                $this->logActivity('create_question', "Created question: {$data['title']}");
                $this->setFlash('success', 'Question created successfully.');
                $this->redirect('admin-quiz/questions');
            } else {
                $this->setFlash('error', 'Failed to create question.');
            }
        }

        $this->redirect('admin-quiz/add-question');
    }

    /**
     * Admin Event Statistics (improved dashboard)
     */
    public function statEvenements()
    {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }

        $db = $this->getDb();

        // Event stats (participations per event)
        $eventStats = $db->query(
            "SELECT e.id, e.title, e.capacite_max, e.statut, e.type,
                    COUNT(r.id) AS participant_count
             FROM evenements e
             LEFT JOIN evenement_ressources r
                ON r.evenement_id = e.id
               AND r.type = 'participation'
             GROUP BY e.id
             ORDER BY participant_count DESC
             LIMIT 12"
        )->fetchAll();

        // Events by type
        $typeStats = $db->query(
            "SELECT COALESCE(type, 'Autre') AS type, COUNT(*) AS count
             FROM evenements
             GROUP BY COALESCE(type, 'Autre')
             ORDER BY count DESC"
        )->fetchAll();

        // Participants by event type
        $participantTypeStats = $db->query(
            "SELECT COALESCE(e.type, 'Autre') AS type,
                    COUNT(r.id) AS participant_count
             FROM evenements e
             LEFT JOIN evenement_ressources r
                ON r.evenement_id = e.id
               AND r.type = 'participation'
             GROUP BY COALESCE(e.type, 'Autre')
             ORDER BY participant_count DESC"
        )->fetchAll();

        // Status distribution
        $statusStats = $db->query(
            "SELECT statut, COUNT(*) AS count FROM evenements GROUP BY statut"
        )->fetchAll();

        $data = array(
            'title' => 'Event Statistics - APPOLIOS',
            'adminSidebarActive' => 'stat-evenements',
            'eventStats' => $eventStats,
            'typeStats' => $typeStats,
            'participantTypeStats' => $participantTypeStats,
            'statusStats' => $statusStats,
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash(),
        );

        $this->view('BackOffice/admin/stat_evenement', $data);
    }

    public function countPendingApplications()
    {
        $sql = "SELECT COUNT(*) as count FROM teacher_applications WHERE status = 'pending'";
        $stmt = $this->getDb()->query($sql);
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    public function applicationEmailExistsPending($email)
    {
        $sql = "SELECT id FROM teacher_applications WHERE email = ? AND status = 'pending'";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
}