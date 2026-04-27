<?php
/**
 * APPOLIOS Admin Controller
 * Handles admin dashboard and management
 */

require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Model/Course.php';
require_once __DIR__ . '/../Model/Enrollment.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';

class AdminController extends BaseController
{

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

        $users = $this->getStudents();

        // Generate PDF using simple HTML output optimized for printing
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <title>Users Export - APPOLIOS</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.5;
                    color: #333;
                    padding: 20px;
                }

                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #548CA8;
                }

                .header h1 {
                    color: #2B4865;
                    font-size: 24px;
                    margin-bottom: 5px;
                }

                .header p {
                    color: #666;
                    font-size: 12px;
                }

                .info {
                    margin-bottom: 20px;
                    color: #666;
                    font-size: 11px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }

                th {
                    background: #548CA8;
                    color: white;
                    padding: 10px 8px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 11px;
                }

                td {
                    padding: 8px;
                    border-bottom: 1px solid #ddd;
                    font-size: 11px;
                }

                tr:nth-child(even) {
                    background: #f8f9fa;
                }

                .badge {
                    padding: 2px 8px;
                    border-radius: 12px;
                    font-size: 10px;
                    color: white;
                    display: inline-block;
                }

                .badge-admin {
                    background: #E19864;
                }

                .badge-teacher {
                    background: #548CA8;
                }

                .badge-student {
                    background: #28a745;
                }

                .badge-blocked {
                    background: #dc3545;
                }

                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #999;
                    border-top: 1px solid #ddd;
                    padding-top: 15px;
                }

                @media print {
                    body {
                        padding: 0;
                    }

                    .no-print {
                        display: none;
                    }
                }
            </style>
        </head>

        <body>
            <div class="header">
                <h1>APPOLIOS - Users Report</h1>
                <p>Complete list of registered users</p>
            </div>

            <div class="info">
                <strong>Generated:</strong> <?= date('F d, Y H:i:s') ?><br>
                <strong>Total Users:</strong> <?= count($users) ?>
            </div>

            <div class="no-print" style="margin-bottom: 20px;">
                <button onclick="window.print()"
                    style="padding: 10px 20px; background: #548CA8; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                    Print / Save as PDF
                </button>
                <a href="<?= APP_ENTRY ?>?url=admin/users"
                    style="display: inline-block; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px; text-decoration: none;">
                    Back to Users
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 20%;">Full Name</th>
                        <th style="width: 25%;">Email Address</th>
                        <th style="width: 12%;">Role</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 20%;">Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $user['role'] ?>">
                                    <?= ucfirst(htmlspecialchars($user['role'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['is_blocked'] ?? 0): ?>
                                    <span class="badge badge-blocked">Blocked</span>
                                <?php else: ?>
                                    <span style="color: #28a745;">Active</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($user['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="footer">
                <p>APPOLIOS E-Learning Platform - User Management Report</p>
                <p>This document is confidential and intended for authorized personnel only.</p>
            </div>

            <script>
                // Auto-trigger print dialog when page loads
                window.onload = function () {
                    setTimeout(function () {
                        window.print();
                    }, 500);
                };
            </script>
        </body>

        </html>
        <?php
        exit;
    }

    /**
     * Block a user
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

        $sql = "UPDATE users SET is_blocked = 1 WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        if ($stmt->execute([$id])) {
            // Log activity
            $this->logActivity(
                'block_user',
                "Admin blocked user: {$user['name']} ({$user['email']})",
                $_SESSION['user_id'],
                $_SESSION['user_name'],
                $_SESSION['user_email'],
                'admin'
            );

            $this->setFlash('success', 'User ' . htmlspecialchars($user['name']) . ' has been blocked successfully.');
        } else {
            $this->setFlash('error', 'Failed to block user.');
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

        $sql = "UPDATE users SET is_blocked = 0 WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        if ($stmt->execute([$id])) {
            // Log activity
            $this->logActivity(
                'unblock_user',
                "Admin unblocked user: {$user['name']} ({$user['email']})",
                $_SESSION['user_id'],
                $_SESSION['user_name'],
                $_SESSION['user_email'],
                'admin'
            );

            $this->setFlash('success', 'User ' . htmlspecialchars($user['name']) . ' has been unblocked successfully.');
        } else {
            $this->setFlash('error', 'Failed to unblock user.');
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
        $course = $courseModel->findById($id);

        if (!$course) {
            $this->setFlash('error', 'Course not found');
            $this->redirect('admin/courses');
            return;
        }

        $data = [
            'title' => 'Edit Course - APPOLIOS',
            'description' => 'Update course details',
            'course' => $course,
            'adminSidebarActive' => 'edit-course',
            'unreadCount' => $this->getContactMessageUnreadCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/edit_course', $data);
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

        $limit = 100;
        $offset = 0;

        $activities = $this->getFilteredActivities($filters, $limit, $offset);
        $totalActivities = $this->countAllActivities();

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
            'flash' => $this->getFlash()
        ];

        $this->view('BackOffice/admin/activity_log', $data);
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
        $sql = "SELECT * FROM users WHERE role = 'student'";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetchAll();
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
            return false;
        }
    }

    public function updateFaceDescriptor($id, $faceDescriptor)
    {
        $sql = "UPDATE users SET face_descriptor = ? WHERE id = ?";
        $stmt = $this->getDb()->prepare($sql);
        return $stmt->execute([$faceDescriptor, $id]);
    }

    // ==========================================
    // CONTACT MESSAGE METHODS - From ContactMessage Model
    // ==========================================

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

    // ==========================================
    // TEACHER APPLICATION METHODS - From TeacherApplication Model
    // ==========================================

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
        $sql = "SELECT * FROM v_pending_teachers ORDER BY created_at DESC";
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

    public function applicationEmailExists($email)
    {
        $sql = "SELECT id FROM teacher_applications WHERE email = ?";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
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

    // ==========================================
    // DATABASE METHODS - For Activity Log operations
    // ==========================================

    /**
     * Log a new activity
     */
    public function logActivity(string $activityType, string $description, ?int $userId = null, ?string $userName = null, ?string $userEmail = null, ?string $userRole = null): bool {
        $sql = "INSERT INTO activity_log
                (user_id, user_name, user_email, user_role, activity_type, activity_description, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->getDb()->prepare($sql);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            return $stmt->execute([
                $userId,
                $userName,
                $userEmail,
                $userRole,
                $activityType,
                $description,
                $ipAddress,
                $userAgent
            ]);
        } catch (PDOException $e) {
            error_log("ActivityLog error: " . $e->getMessage());
            return false;
        }
    }

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
        ];

        return $labels[$type] ?? ucfirst($type);
    }
}