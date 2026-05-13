<?php
/**
 * Shared Groups + Discussions for student + teacher routes.
 * Regenerate: php tools/build_collab_delegate.php
 */
require_once __DIR__ . '/BaseController.php';

final class CollabHubDelegate
{
    public static function collabViewShell(BaseController $c, string $prefix, string $sidebarKey): array
    {
        $isTeacher = ($prefix === 'teacher');
        return [
            'collab_shell' => $isTeacher ? 'teacher' : 'student',
            'foPrefix' => $prefix,
            'collab_dashboard_classes' => $isTeacher
                ? 'dashboard teacher-collab-page collab-hub'
                : 'dashboard student-events-page collab-hub',
            'flash' => $c->getFlash(),
        ] + ($isTeacher
            ? ['teacherSidebarActive' => $sidebarKey]
            : ['studentSidebarActive' => $sidebarKey]);
    }

    public static function runGroupes(BaseController $c, string $prefix, array $params): void
    {
        $groupModel = $c->model('Groupe');
        $discussionModel = $c->model('Discussion');
        $postModel = $c->model('GroupPost');
        $reactionModel = $c->model('GroupPostReaction');
        $commentModel = $c->model('GroupPostComment');
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        $first = $params[0] ?? '';
        $second = $params[1] ?? '';

        if ($first === '' || $first === null) {
            $q = trim((string) ($_GET['q'] ?? ''));
            $sort = (string) ($_GET['sort'] ?? 'name_asc');
            $all = $groupModel->fetchAllApproved();

            if ($q !== '') {
                $ql = mb_strtolower($q);
                $all = array_values(array_filter($all, function ($g) use ($ql) {
                    $name = mb_strtolower((string) ($g['nom_groupe'] ?? ''));
                    $desc = mb_strtolower((string) ($g['description'] ?? ''));
                    return (strpos($name, $ql) !== false) || (strpos($desc, $ql) !== false);
                }));
            }

            usort($all, function ($a, $b) use ($sort) {
                $an = (string) ($a['nom_groupe'] ?? '');
                $bn = (string) ($b['nom_groupe'] ?? '');
                $ad = (string) ($a['date_creation'] ?? '');
                $bd = (string) ($b['date_creation'] ?? '');
                if ($sort === 'name_desc') return strcasecmp($bn, $an);
                if ($sort === 'newest') return strcmp($bd, $ad);
                if ($sort === 'oldest') return strcmp($ad, $bd);
                return strcasecmp($an, $bn);
            });

            $pendingMine = array_values(array_filter($groupModel->fetchByCreator($userId), function ($g) {
                return (string) ($g['approval_statut'] ?? '') === 'en_cours';
            }));

            $pendingMine = array_map(function ($g) {
                $raw = trim((string) ($g['image_url'] ?? ''));
                if ($raw === '') {
                    $g['cover_url'] = '';
                    return $g;
                }
                if (preg_match('~^https?://~i', $raw)) {
                    $g['cover_url'] = $raw;
                    return $g;
                }
                $g['cover_url'] = APP_URL . '/' . ltrim($raw, '/');
                return $g;
            }, $pendingMine);

            $suggest = [];
            foreach (array_slice($all, 0, 25) as $g) {
                $suggest[] = [
                    'primary' => (string) ($g['nom_groupe'] ?? ''),
                    'secondary' => (string) ($g['description'] ?? ''),
                    'url' => APP_ENTRY . '?url=' . $prefix . '/groupes/' . (int) ($g['id_groupe'] ?? 0),
                ];
            }

            $rows = array_map(function ($g) use ($groupModel, $userId) {
                $gid = (int) ($g['id_groupe'] ?? 0);
                $isOwner = ((int) ($g['id_createur'] ?? 0)) === $userId;
                $isMember = $gid > 0 ? $groupModel->estMembre($gid, $userId) : false;
                $g['is_owner_viewer'] = $isOwner;
                $g['is_member_viewer'] = $isMember;
                $raw = trim((string) ($g['image_url'] ?? ''));
                if ($raw === '') {
                    $g['cover_url'] = '';
                } elseif (preg_match('~^https?://~i', $raw)) {
                    $g['cover_url'] = $raw;
                } else {
                    $g['cover_url'] = APP_URL . '/' . ltrim($raw, '/');
                }
                return $g;
            }, $all);

            $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'groupes'), [
                'groupes' => $rows,
                'mesGroupesEnApprobation' => $pendingMine,
                'listQ' => $q,
                'listSort' => $sort,
                'listQueryActive' => $q !== '',
                'group_search_suggestion_items' => $suggest,
            ]);
            $c->view('FrontOffice/student/groupes/index', $data);
            return;
        }

        if ($first === 'create') {
            $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'groupes'), [
                'old' => [],
                'errors' => [],
            ]);
            $c->view('FrontOffice/student/groupes/create', $data);
            return;
        }

        if ($first === 'store') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/groupes');
                return;
            }

            $nom = trim((string) ($_POST['nom_groupe'] ?? ''));
            $desc = trim((string) ($_POST['description'] ?? ''));
            $errors = [];
            if ($nom === '') $errors['nom_groupe'] = 'Group name is required.';
            if ($desc === '') $errors['description'] = 'Description is required.';

            $imageUrl = null;
            if (!empty($_FILES['group_photo']) && is_array($_FILES['group_photo']) && ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = $c->storeUploadedFile($_FILES['group_photo'], __DIR__ . '/../uploads/groupes');
                if (!$uploadResult['ok']) {
                    $errors['group_photo'] = $uploadResult['error'];
                } else {
                    $imageUrl = 'uploads/groupes/' . $uploadResult['fileName'];
                }
            }

            if (!empty($errors)) {
                $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'groupes'), [
                    'old' => ['nom_groupe' => $nom, 'description' => $desc],
                    'errors' => $errors,
                    'flash' => ['type' => 'error', 'message' => 'Please fix the form errors.'],
                ]);
                $c->view('FrontOffice/student/groupes/create', $data);
                return;
            }

            $newId = $groupModel->create([
                'nom_groupe' => $nom,
                'description' => $desc,
                'id_createur' => $userId,
                'statut' => 'actif',
                'approval_statut' => 'en_cours',
                'image_url' => $imageUrl,
            ]);

            if (!$newId) {
                $c->setFlash('error', 'Failed to create group.');
                $c->redirect($prefix . '/groupes/create');
                return;
            }

            $c->setFlash('success', 'Group created. Awaiting admin approval.');
            $c->redirect($prefix . '/groupes');
            return;
        }

        $groupId = (int) $first;
        if ($groupId <= 0) {
            $c->redirect($prefix . '/groupes');
            return;
        }

        if ($second === 'activity-pdf') {
            $groupe = $groupModel->findById($groupId);
            if (!$groupe) {
                $c->setFlash('error', 'Group not found.');
                $c->redirect($prefix . '/groupes');
                return;
            }
            if ((int) ($groupe['id_createur'] ?? 0) !== $userId) {
                $c->setFlash('error', 'Only the group creator can open the activity report.');
                $c->redirect($prefix . '/groupes');
                return;
            }

            $userModel = $c->model('User');
            $members = $groupModel->fetchMembres($groupId);
            $posts = $postModel->fetchByGroup($groupId, 200);
            $discussions = $discussionModel->fetchByGroup($groupId);
            $discussionCount = count($discussions);
            $postCount = count($posts);
            $commentCount = 0;
            foreach ($posts as $p) {
                $pid = (int) ($p['id'] ?? 0);
                $commentCount += $pid > 0 ? count($commentModel->fetchByPost($pid, 500)) : 0;
            }

            $chatTableOk = $discussionModel->discussionMessagesTableExists();
            $liveChatCount = $chatTableOk ? $discussionModel->countChatMessagesForGroup($groupId) : 0;
            $topDiscussions = $chatTableOk ? $discussionModel->fetchTopDiscussionsByChatVolume($groupId, 8) : [];
            $recentChat = $chatTableOk ? $discussionModel->fetchRecentChatForGroup($groupId, 10) : [];

            $totalActivityLines = $discussionCount + $liveChatCount + $postCount + $commentCount;

            $creatorRow = $userModel->findById((int) ($groupe['id_createur'] ?? 0));
            $creatorName = trim((string) ($creatorRow['name'] ?? ''));

            $chatNotice = '';
            if (!$chatTableOk) {
                $chatNotice = 'Live chat history table not found (run the realtime server once to create discussion_messages). Chat counts show 0.';
            }

            $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'groupes'), [
                'title' => 'Group Activity Report — APPOLIOS',
                'description' => 'Printable group activity report',
                'groupe' => $groupe,
                'members' => $members,
                'discussions' => $discussions,
                'top_discussions' => $topDiscussions,
                'recent_chat' => $recentChat,
                'stats' => [
                    'discussions' => $discussionCount,
                    'live_chat' => $liveChatCount,
                    'total_activity_lines' => $totalActivityLines,
                    'posts' => $postCount,
                    'comments' => $commentCount,
                ],
                'chat_table_ok' => $chatTableOk,
                'chat_notice' => $chatNotice,
                'creator_name' => $creatorName !== '' ? $creatorName : '—',
                'generated_at' => date('Y-m-d H:i'),
            ]);
            $c->view('FrontOffice/student/groupes/activity_report', $data);
            return;
        }

        if ($second === 'post') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $groupe = $groupModel->findById($groupId);
            if (!$groupe) {
                $c->setFlash('error', 'Group not found.');
                $c->redirect($prefix . '/groupes');
                return;
            }

            $isCreator = (int) ($groupe['id_createur'] ?? 0) === $userId;
            if (!$isCreator && !$groupModel->estMembre($groupId, $userId)) {
                $c->setFlash('error', 'You must join this group before posting.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $content = trim((string) ($_POST['content'] ?? ''));

            $mediaUrl = null;
            $mediaKind = null;
            if (!empty($_FILES['media']) && is_array($_FILES['media']) && ($_FILES['media']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = $c->storeUploadedFile($_FILES['media'], __DIR__ . '/../uploads/group_posts');
                if (!$uploadResult['ok']) {
                    $c->setFlash('error', $uploadResult['error']);
                    $c->redirect($prefix . '/groupes/' . $groupId);
                    return;
                }
                $mediaUrl = 'uploads/group_posts/' . $uploadResult['fileName'];
                $mime = (string) ($_FILES['media']['type'] ?? '');
                if (stripos($mime, 'image/') === 0) {
                    $mediaKind = 'image';
                } elseif (stripos($mime, 'video/') === 0) {
                    $mediaKind = 'video';
                } elseif (stripos($mime, 'audio/') === 0) {
                    $mediaKind = 'audio';
                } else {
                    $mediaKind = 'file';
                }
            }

            if ($content === '' && $mediaUrl === null) {
                $c->setFlash('error', 'Write something or attach a file.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $newId = $postModel->create([
                'group_id' => $groupId,
                'user_id' => $userId,
                'post_type' => $mediaUrl ? 'media' : 'text',
                'content' => $content !== '' ? $content : null,
                'media_url' => $mediaUrl,
                'media_kind' => $mediaKind,
            ]);

            if (!$newId) {
                $c->setFlash('error', 'Failed to create post.');
            } else {
                $c->setFlash('success', 'Post published.');
            }
            $c->redirect($prefix . '/groupes/' . $groupId);
            return;
        }

        if ($second === 'react') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $postId = (int) ($_POST['post_id'] ?? 0);
            $reaction = trim((string) ($_POST['reaction'] ?? ''));
            if ($postId <= 0 || $reaction === '') {
                $c->setFlash('error', 'Invalid reaction.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $post = $postModel->findById($postId);
            if (!$post || (int) ($post['group_id'] ?? 0) !== $groupId) {
                $c->setFlash('error', 'Post not found.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $groupe = $groupModel->findById($groupId);
            $isCreator = $groupe && (int) ($groupe['id_createur'] ?? 0) === $userId;
            if (!$isCreator && !$groupModel->estMembre($groupId, $userId)) {
                $c->setFlash('error', 'You must be a group member to react.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $current = $reactionModel->getUserReaction($postId, $userId);
            if ($current !== null && $current === $reaction) {
                $reactionModel->removeReaction($postId, $userId);
            } else {
                $reactionModel->setReaction($postId, $userId, $reaction);
            }

            $c->redirect($prefix . '/groupes/' . $groupId);
            return;
        }

        if ($second === 'comment') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $postId = (int) ($_POST['post_id'] ?? 0);
            $content = trim((string) ($_POST['content'] ?? ''));
            if ($postId <= 0 || $content === '') {
                $c->setFlash('error', 'Comment cannot be empty.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $post = $postModel->findById($postId);
            if (!$post || (int) ($post['group_id'] ?? 0) !== $groupId) {
                $c->setFlash('error', 'Post not found.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $groupe = $groupModel->findById($groupId);
            $isCreator = $groupe && (int) ($groupe['id_createur'] ?? 0) === $userId;
            if (!$isCreator && !$groupModel->estMembre($groupId, $userId)) {
                $c->setFlash('error', 'You must be a group member to comment.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $newId = $commentModel->add($postId, $userId, $content);
            if (!$newId) {
                $c->setFlash('error', 'Failed to add comment.');
            }

            $c->redirect($prefix . '/groupes/' . $groupId);
            return;
        }

        if ($second === 'delete-post') {
            $postId = (int) ($params[2] ?? 0);
            if ($postId <= 0) {
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $groupe = $groupModel->findById($groupId);
            $isCreator = $groupe && (int) ($groupe['id_createur'] ?? 0) === $userId;
            if (!$isCreator && !$groupModel->estMembre($groupId, $userId)) {
                $c->setFlash('error', 'You must be a group member.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $ok = $postModel->deletePost($postId, $userId, false);
            if (!$ok) {
                $c->setFlash('error', 'Cannot delete this post.');
            }
            $c->redirect($prefix . '/groupes/' . $groupId);
            return;
        }

        if ($second === 'delete-comment') {
            $commentId = (int) ($params[2] ?? 0);
            if ($commentId <= 0) {
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $groupe = $groupModel->findById($groupId);
            $isCreator = $groupe && (int) ($groupe['id_createur'] ?? 0) === $userId;
            if (!$isCreator && !$groupModel->estMembre($groupId, $userId)) {
                $c->setFlash('error', 'You must be a group member.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $ok = $commentModel->deleteComment($commentId, $userId, false);
            if (!$ok) {
                $c->setFlash('error', 'Cannot delete this comment.');
            }
            $c->redirect($prefix . '/groupes/' . $groupId);
            return;
        }

        if ($second === 'edit') {
            $groupe = $groupModel->findById($groupId);
            if (!$groupe || (int) ($groupe['id_createur'] ?? 0) !== $userId) {
                $c->setFlash('error', 'Access denied.');
                $c->redirect($prefix . '/groupes');
                return;
            }
            $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'groupes'), [
                'groupe' => $groupe,
                'old' => [],
                'errors' => [],
                'group_activity_series' => ['labels' => [], 'discussions' => [], 'visitors' => []],
            ]);
            $c->view('FrontOffice/student/groupes/edit', $data);
            return;
        }

        if ($second === 'create-discussion') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $groupe = $groupModel->findById($groupId);
            if (!$groupe) {
                $c->setFlash('error', 'Group not found.');
                $c->redirect($prefix . '/groupes');
                return;
            }
            if ((int) ($groupe['id_createur'] ?? 0) !== $userId) {
                $c->setFlash('error', 'Access denied.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }
            if ((string) ($groupe['approval_statut'] ?? '') !== 'approuve') {
                $c->setFlash('error', 'This group must be approved before creating discussions.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $title = trim((string) ($_POST['titre'] ?? ''));
            $content = trim((string) ($_POST['contenu'] ?? ''));
            if ($title === '' || $content === '') {
                $c->setFlash('error', 'Title and first message are required.');
                $c->redirect($prefix . '/groupes/' . $groupId);
                return;
            }

            $ok = $discussionModel->createForGroup($groupId, $userId, $title, $content, 'approuve');
            if ($ok) {
                $c->setFlash('success', 'Discussion created.');
            } else {
                $c->setFlash('error', 'Failed to create discussion.');
            }
            $c->redirect($prefix . '/groupes/' . $groupId);
            return;
        }

        if ($second === '' || $second === null) {
            $groupe = $groupModel->findById($groupId);
            if (!$groupe) {
                $c->setFlash('error', 'Group not found.');
                $c->redirect($prefix . '/groupes');
                return;
            }

            $members = $groupModel->fetchMembres($groupId);
            $memberChips = [];
            foreach ($members as $m) {
                $memberChips[] = [
                    'name' => (string) ($m['name'] ?? ''),
                    'role' => (string) ($m['role'] ?? ''),
                ];
            }

            $isCreator = (int) ($groupe['id_createur'] ?? 0) === $userId;
            $isGroupMember = $isCreator || $groupModel->estMembre($groupId, $userId);

            $postsDecorated = [];
            if ($isGroupMember) {
                $posts = $postModel->fetchByGroup($groupId, 60);
                foreach ($posts as $p) {
                    $postId = (int) ($p['id'] ?? 0);
                    $p['reactions'] = $postId > 0 ? $reactionModel->countByPost($postId) : [];
                    $p['viewer_reaction'] = $postId > 0 ? $reactionModel->getUserReaction($postId, $userId) : null;
                    $p['comments'] = $postId > 0 ? $commentModel->fetchByPost($postId, 50) : [];
                    $p['is_owner_viewer'] = ((int) ($p['user_id'] ?? 0)) === $userId;
                    $postsDecorated[] = $p;
                }
            }

            $shell = CollabHubDelegate::collabViewShell($c, $prefix, 'groupes');
            $shell['collab_dashboard_classes'] .= ' collab-hub--detail';
            $data = array_merge($shell, [
                'groupe' => $groupe,
                'is_owner_viewer' => ((int) ($groupe['id_createur'] ?? 0)) === $userId,
                'is_group_member' => $isGroupMember,
                'group_cover_url' => (string) ($groupe['image_url'] ?? ''),
                'group_posts' => $postsDecorated,
                'member_chips' => $memberChips,
            ]);
            $c->view('FrontOffice/student/groupes/show', $data);
            return;
        }

        if ($second === 'update') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/groupes/' . $groupId . '/edit');
                return;
            }
            $groupe = $groupModel->findById($groupId);
            if (!$groupe || (int) ($groupe['id_createur'] ?? 0) !== $userId) {
                $c->setFlash('error', 'Access denied.');
                $c->redirect($prefix . '/groupes');
                return;
            }

            $nom = trim((string) ($_POST['nom_groupe'] ?? ''));
            $desc = trim((string) ($_POST['description'] ?? ''));
            $statut = (string) ($_POST['statut'] ?? 'actif');
            $errors = [];
            if ($nom === '') $errors['nom_groupe'] = 'Group name is required.';
            if ($desc === '') $errors['description'] = 'Description is required.';
            if (!in_array($statut, ['actif', 'archivé'], true)) $errors['statut'] = 'Invalid status.';

            $imageUrl = (string) ($groupe['image_url'] ?? '');
            if (!empty($_FILES['group_photo']) && is_array($_FILES['group_photo']) && ($_FILES['group_photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = $c->storeUploadedFile($_FILES['group_photo'], __DIR__ . '/../uploads/groupes');
                if (!$uploadResult['ok']) {
                    $errors['group_photo'] = $uploadResult['error'];
                } else {
                    $imageUrl = 'uploads/groupes/' . $uploadResult['fileName'];
                }
            }

            if (!empty($errors)) {
                $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'groupes'), [
                    'groupe' => $groupe,
                    'old' => ['nom_groupe' => $nom, 'description' => $desc, 'statut' => $statut],
                    'errors' => $errors,
                    'group_activity_series' => ['labels' => [], 'discussions' => [], 'visitors' => []],
                ]);
                $c->view('FrontOffice/student/groupes/edit', $data);
                return;
            }

            $ok = $groupModel->updateGroupe($groupId, [
                'nom_groupe' => $nom,
                'description' => $desc,
                'statut' => $statut,
                'approval_statut' => (string) ($groupe['approval_statut'] ?? 'en_cours'),
                'image_url' => $imageUrl,
            ]);
            if ($ok) {
                $c->setFlash('success', 'Group updated.');
            } else {
                $c->setFlash('error', 'Failed to update group.');
            }
            $c->redirect($prefix . '/groupes/' . $groupId . '/edit');
            return;
        }

        if ($second === 'join') {
            $groupModel->ajouterMembre($groupId, $userId, 'membre');
            $c->setFlash('success', 'Joined group.');
            $c->redirect($prefix . '/groupes/' . $groupId);
            return;
        }

        if ($second === 'quit') {
            $groupModel->retirerMembre($groupId, $userId);
            $c->setFlash('success', 'You left the group.');
            $c->redirect($prefix . '/groupes');
            return;
        }

        if ($second === 'delete') {
            $groupe = $groupModel->findById($groupId);
            if ($groupe && (int) ($groupe['id_createur'] ?? 0) === $userId) {
                $groupModel->deleteGroupe($groupId);
                $c->setFlash('success', 'Group deleted.');
            } else {
                $c->setFlash('error', 'Access denied.');
            }
            $c->redirect($prefix . '/groupes');
            return;
        }

        $c->redirect($prefix . '/groupes/' . $groupId);
        return;
    }

    public static function runDiscussions(BaseController $c, string $prefix, array $params): void
    {
        $discussionModel = $c->model('Discussion');
        $groupModel = $c->model('Groupe');
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        $first = $params[0] ?? '';
        $second = $params[1] ?? '';

        if ($first === '' || $first === null) {
            $q = trim((string) ($_GET['q'] ?? ''));
            $sort = (string) ($_GET['sort'] ?? 'newest');
            $rows = $discussionModel->fetchVisibleForUser($userId);

            if ($q !== '') {
                $ql = mb_strtolower($q);
                $rows = array_values(array_filter($rows, function ($d) use ($ql) {
                    $t = mb_strtolower((string) ($d['titre'] ?? ''));
                    $c = mb_strtolower((string) ($d['contenu'] ?? ''));
                    $g = mb_strtolower((string) ($d['nom_groupe'] ?? ''));
                    return (strpos($t, $ql) !== false) || (strpos($c, $ql) !== false) || (strpos($g, $ql) !== false);
                }));
            }

            usort($rows, function ($a, $b) use ($sort) {
                $at = (string) ($a['titre'] ?? '');
                $bt = (string) ($b['titre'] ?? '');
                $ag = (string) ($a['nom_groupe'] ?? '');
                $bg = (string) ($b['nom_groupe'] ?? '');
                $ad = (string) ($a['date_creation'] ?? '');
                $bd = (string) ($b['date_creation'] ?? '');
                if ($sort === 'oldest') return strcmp($ad, $bd);
                if ($sort === 'title_asc') return strcasecmp($at, $bt);
                if ($sort === 'title_desc') return strcasecmp($bt, $at);
                if ($sort === 'group_asc') return strcasecmp($ag, $bg);
                if ($sort === 'group_desc') return strcasecmp($bg, $ag);
                return strcmp($bd, $ad);
            });

            $cards = [];
            foreach ($rows as $d) {
                $id = (int) ($d['id_discussion'] ?? 0);
                $cards[] = [
                    'group_name' => (string) ($d['nom_groupe'] ?? ''),
                    'title' => (string) ($d['titre'] ?? ''),
                    'content' => (string) ($d['contenu'] ?? ''),
                    'url_chat' => APP_ENTRY . '?url=' . $prefix . '/discussions/' . $id . '/chat',
                    'url_edit' => APP_ENTRY . '?url=' . $prefix . '/discussions/' . $id . '/edit',
                    'url_delete' => APP_ENTRY . '?url=' . $prefix . '/discussions/' . $id . '/delete',
                    'is_author' => ((int) ($d['id_auteur'] ?? 0) === $userId),
                ];
            }

            $suggest = [];
            foreach (array_slice($cards, 0, 25) as $row) {
                $suggest[] = [
                    'primary' => (string) ($row['title'] ?? ''),
                    'secondary' => (string) ($row['group_name'] ?? ''),
                    'url' => (string) ($row['url_chat'] ?? '#'),
                ];
            }

            $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'discussions'), [
                'discussion_cards' => $cards,
                'listQ' => $q,
                'listSort' => $sort,
                'listQueryActive' => $q !== '',
                'search_suggestion_items' => $suggest,
            ]);
            $c->view('FrontOffice/student/discussions/index', $data);
            return;
        }

        if ($first === 'create') {
            $myGroups = array_values(array_filter($groupModel->fetchAllApproved(), function ($g) use ($groupModel, $userId) {
                $gid = (int) ($g['id_groupe'] ?? 0);
                $isOwner = ((int) ($g['id_createur'] ?? 0)) === $userId;
                $isMember = $gid > 0 ? $groupModel->estMembre($gid, $userId) : false;
                return $gid > 0 && ($isOwner || $isMember);
            }));
            $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'discussions'), [
                'groups' => $myGroups,
                'old' => [],
                'errors' => [],
            ]);
            $c->view('FrontOffice/student/discussions/create', $data);
            return;
        }

        if ($first === 'store') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/discussions');
                return;
            }
            $groupId = (int) ($_POST['id_groupe'] ?? 0);
            $title = trim((string) ($_POST['titre'] ?? ''));
            $content = trim((string) ($_POST['contenu'] ?? ''));
            $errors = [];
            if ($groupId <= 0) $errors['id_groupe'] = 'Group is required.';
            if ($title === '') $errors['titre'] = 'Title is required.';
            if ($content === '') $errors['contenu'] = 'Content is required.';

            $myGroups = array_values(array_filter($groupModel->fetchAllApproved(), function ($g) use ($groupModel, $userId) {
                $gid = (int) ($g['id_groupe'] ?? 0);
                $isOwner = ((int) ($g['id_createur'] ?? 0)) === $userId;
                $isMember = $gid > 0 ? $groupModel->estMembre($gid, $userId) : false;
                return $gid > 0 && ($isOwner || $isMember);
            }));
            $allowedGroupIds = array_map(function ($g) { return (int) $g['id_groupe']; }, $myGroups);
            if ($groupId > 0 && !in_array($groupId, $allowedGroupIds, true)) {
                $errors['id_groupe'] = 'You can create discussions only in approved groups you own or joined.';
            }

            if (!empty($errors)) {
                $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'discussions'), [
                    'groups' => $myGroups,
                    'old' => ['id_groupe' => $groupId, 'titre' => $title, 'contenu' => $content],
                    'errors' => $errors,
                ]);
                $c->view('FrontOffice/student/discussions/create', $data);
                return;
            }

            $ok = $discussionModel->createForGroup($groupId, $userId, $title, $content, 'approuve');
            if ($ok) {
                $c->setFlash('success', 'Discussion created.');
                $c->redirect($prefix . '/discussions');
                return;
            }
            $c->setFlash('error', 'Failed to create discussion.');
            $c->redirect($prefix . '/discussions/create');
            return;
        }

        $discussionId = (int) $first;
        if ($discussionId <= 0) {
            $c->redirect($prefix . '/discussions');
            return;
        }

        if ($second === 'chat') {
            $discussion = $discussionModel->fetchRowByPk($discussionId);
            if (!$discussion) {
                $c->setFlash('error', 'Discussion not found.');
                $c->redirect($prefix . '/discussions');
                return;
            }
            $group = $groupModel->findById((int) ($discussion['id_groupe'] ?? 0));

            $chatRoom = 'discussion_' . $discussionId;
            $socketUrl = (string) ($_ENV['REALTIME_SOCKET_URL'] ?? 'http://127.0.0.1:3001');
            $uploadUrl = APP_ENTRY . '?url=student/upload-chat-attachment/' . $discussionId;
            $summarizeUrl = APP_ENTRY . '?url=student/summarize-text';

            $shell = CollabHubDelegate::collabViewShell($c, $prefix, 'discussions');
            $shell['collab_dashboard_classes'] .= ' collab-chat-root';
            $shell['collab_root_attrs'] = ' data-chat-appearance="default"';
            $data = array_merge($shell, [
                'discussion' => $discussion,
                'group' => $group ?: [],
                'chatRoom' => $chatRoom,
                'socketUrl' => $socketUrl,
                'currentUserId' => $userId,
                'currentUserName' => (string) ($_SESSION['user_name'] ?? 'User'),
                'discussion_chat' => [
                    'back_url' => APP_ENTRY . '?url=' . $prefix . '/discussions',
                    'upload_url' => $uploadUrl,
                    'summarize_url' => $summarizeUrl,
                ],
            ]);
            $c->view('FrontOffice/student/discussions/chat', $data);
            return;
        }

        if ($second === 'edit') {
            $owned = $discussionModel->findOwnedBy($discussionId, $userId);
            if (!$owned) {
                $c->setFlash('error', 'Access denied.');
                $c->redirect($prefix . '/discussions');
                return;
            }
            $myGroups = array_values(array_filter($groupModel->fetchAllApproved(), function ($g) use ($groupModel, $userId) {
                $gid = (int) ($g['id_groupe'] ?? 0);
                $isOwner = ((int) ($g['id_createur'] ?? 0)) === $userId;
                $isMember = $gid > 0 ? $groupModel->estMembre($gid, $userId) : false;
                return $gid > 0 && ($isOwner || $isMember);
            }));

            $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'discussions'), [
                'discussion_edit' => [
                    'discussion_id' => $discussionId,
                    'update_url' => APP_ENTRY . '?url=' . $prefix . '/discussions/' . $discussionId . '/update',
                    'selected_group_id' => (int) ($owned['id_groupe'] ?? 0),
                    'title_value' => (string) ($owned['titre'] ?? ''),
                    'content_value' => (string) ($owned['contenu'] ?? ''),
                ],
                'groups' => $myGroups,
                'errors' => [],
                'discussion_stats' => null,
            ]);
            $c->view('FrontOffice/student/discussions/edit', $data);
            return;
        }

        if ($second === 'update') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $c->redirect($prefix . '/discussions/' . $discussionId . '/edit');
                return;
            }
            $owned = $discussionModel->findOwnedBy($discussionId, $userId);
            if (!$owned) {
                $c->setFlash('error', 'Access denied.');
                $c->redirect($prefix . '/discussions');
                return;
            }
            $groupId = (int) ($_POST['id_groupe'] ?? 0);
            $title = trim((string) ($_POST['titre'] ?? ''));
            $content = trim((string) ($_POST['contenu'] ?? ''));
            $errors = [];
            if ($groupId <= 0) $errors['id_groupe'] = 'Group is required.';
            if ($title === '') $errors['titre'] = 'Title is required.';
            if ($content === '') $errors['contenu'] = 'Content is required.';

            $myGroups = array_values(array_filter($groupModel->fetchAllApproved(), function ($g) use ($groupModel, $userId) {
                $gid = (int) ($g['id_groupe'] ?? 0);
                $isOwner = ((int) ($g['id_createur'] ?? 0)) === $userId;
                $isMember = $gid > 0 ? $groupModel->estMembre($gid, $userId) : false;
                return $gid > 0 && ($isOwner || $isMember);
            }));
            $allowedGroupIds = array_map(function ($g) { return (int) $g['id_groupe']; }, $myGroups);
            if ($groupId > 0 && !in_array($groupId, $allowedGroupIds, true)) {
                $errors['id_groupe'] = 'You can move discussions only inside approved groups you own or joined.';
            }

            if (!empty($errors)) {
                $data = array_merge(CollabHubDelegate::collabViewShell($c, $prefix, 'discussions'), [
                    'discussion_edit' => [
                        'discussion_id' => $discussionId,
                        'update_url' => APP_ENTRY . '?url=' . $prefix . '/discussions/' . $discussionId . '/update',
                        'selected_group_id' => $groupId,
                        'title_value' => $title,
                        'content_value' => $content,
                    ],
                    'groups' => $myGroups,
                    'errors' => $errors,
                    'discussion_stats' => null,
                ]);
                $c->view('FrontOffice/student/discussions/edit', $data);
                return;
            }

            $ok = $discussionModel->updateOwned($discussionId, $userId, $title, $content, $groupId);
            if ($ok) {
                $c->setFlash('success', 'Discussion updated.');
            } else {
                $c->setFlash('error', 'Failed to update discussion.');
            }
            $c->redirect($prefix . '/discussions/' . $discussionId . '/edit');
            return;
        }

        if ($second === 'delete') {
            $owned = $discussionModel->findOwnedBy($discussionId, $userId);
            if ($owned) {
                $discussionModel->deleteByPrimaryKey($discussionId);
                $c->setFlash('success', 'Discussion deleted.');
            } else {
                $c->setFlash('error', 'Access denied.');
            }
            $c->redirect($prefix . '/discussions');
            return;
        }

        $c->redirect($prefix . '/discussions');
    }
}
