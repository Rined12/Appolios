<?php
/**
 * APPOLIOS — Social Learning
 * Controller : MessageController
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Groupe.php';
require_once __DIR__ . '/../models/Discussion.php';
require_once __DIR__ . '/../models/Message.php';

class MessageController extends Controller {

    private Groupe     $groupeModel;
    private Discussion $discussionModel;
    private Message    $messageModel;

    public function __construct() {
        $this->groupeModel     = new Groupe();
        $this->discussionModel = new Discussion();
        $this->messageModel    = new Message();
    }

    // ==================================================================
    // ▌ CREATE — POST student/groupes/{idGroupe}/discussions/{idDisc}/messages/store
    // ==================================================================
    public function store(string $idGroupe, string $idDisc): void {
        $this->requireLogin();

        $idG  = (int)$idGroupe;
        $idD  = (int)$idDisc;

        // Vérifier appartenance au groupe
        if (!$this->groupeModel->isMember($idG, (int)$_SESSION['user_id'])) {
            $this->setFlash('error', 'Vous devez être membre du groupe pour répondre.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
            return;
        }

        $discussion = $this->discussionModel->findByIdWithAuthor($idD);
        if (!$discussion || (int)$discussion['id_groupe'] !== $idG) {
            $this->setFlash('error', 'Discussion introuvable.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG);
            return;
        }

        if (($discussion['approval_statut'] ?? 'approuve') !== 'approuve') {
            $this->setFlash('error', 'Cette discussion doit être approuvée avant d\'ajouter des messages.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
            return;
        }

        // Validation serveur
        $errors  = [];
        $contenu = trim($_POST['contenu'] ?? '');

        if ($contenu === '') {
            $errors[] = 'Le message ne peut pas être vide.';
        } elseif (strlen($contenu) < 10) {
            $errors[] = 'Le message doit contenir au moins 10 caractères.';
        }

        if (!empty($errors)) {
            // Réafficher la discussion avec les erreurs
            $groupe   = $this->groupeModel->findByIdWithCreator($idG);
            $page     = max(1, (int)($_GET['page'] ?? 1));
            $limit    = 20;
            $offset   = ($page - 1) * $limit;
            $total    = $this->messageModel->countByDiscussion($idD);

            require_once __DIR__ . '/../core/Controller.php';
            $this->view('student/discussion/show', [
                'title'              => htmlspecialchars($discussion['titre']) . ' — APPOLIOS',
                'description'        => 'Discussion et messages',
                'studentSidebarActive' => 'groupes',
                'groupe'             => $groupe,
                'discussion'         => $discussion,
                'messages'           => $this->messageModel->getByDiscussion($idD, $limit, $offset),
                'discussionApproved' => (($discussion['approval_statut'] ?? 'approuve') === 'approuve'),
                'isMember'           => true,
                'myRole'             => $this->groupeModel->getMemberRole($idG, (int)$_SESSION['user_id']),
                'currentPage'        => $page,
                'totalPages'         => (int)ceil($total / $limit),
                'errors'             => $errors,
                'msgContenu'         => htmlspecialchars($contenu),
                'flash'              => null,
            ]);
            return;
        }

        $this->messageModel->create(
            htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8'),
            $idD,
            (int)$_SESSION['user_id']
        );

        $this->setFlash('success', 'Message envoyé.');
        $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
    }

    // ==================================================================
    // ▌ DELETE — GET student/groupes/{idGroupe}/discussions/{idDisc}/messages/{id}/delete
    // ==================================================================
    public function destroy(string $idGroupe, string $idDisc, string $id): void {
        $this->requireLogin();

        $idG  = (int)$idGroupe;
        $idD  = (int)$idDisc;
        $idM  = (int)$id;
        $userId = (int)$_SESSION['user_id'];

        $message = $this->messageModel->findByIdWithAuthor($idM);
        if (!$message || (int)$message['id_discussion'] !== $idD) {
            $this->setFlash('error', 'Message introuvable.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
            return;
        }

        $myRole = $this->groupeModel->getMemberRole($idG, $userId);
        if ((int)$message['id_auteur'] !== $userId && $myRole !== 'admin' && !$this->isAdmin()) {
            $this->setFlash('error', 'Accès refusé.');
            $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
            return;
        }

        $this->messageModel->deleteById($idM);
        $this->setFlash('success', 'Message supprimé.');
        $this->redirect($this->socialLearningGroupesPath() . '/' . $idG . '/discussions/' . $idD);
    }

    // ==================================================================
    // ▌ Helpers
    // ==================================================================
    private function requireLogin(): void {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Connexion requise.');
            $this->redirect('login');
            exit();
        }
    }
}
