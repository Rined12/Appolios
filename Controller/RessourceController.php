<?php
/**
 * APPOLIOS Ressource Controller
 * SQL + business logic here. Model = getters/setters only.
 */
require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/EvenementRessource.php';

class RessourceController extends BaseController {

    private function getDb(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
                DB_USER, DB_PASS,
                [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return $pdo;
    }

    private function queryFindEvenement(int $id): array|false {
        $st = $this->getDb()->prepare("SELECT * FROM evenements WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryFindRessource(int $id): array|false {
        $st = $this->getDb()->prepare("SELECT * FROM evenement_ressources WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryByTypeAndEvenement(string $type, int $evenementId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.*, u.name as creator_name, e.title as evenement_title
             FROM evenement_ressources r
             JOIN users u ON r.created_by = u.id
             JOIN evenements e ON r.evenement_id = e.id
             WHERE r.type = ? AND r.evenement_id = ?
             ORDER BY r.created_at DESC"
        );
        $st->execute([$type, $evenementId]);
        return $st->fetchAll();
    }

    private function queryExistsInScope(int $id, int $evenementId, string $type): bool {
        $st = $this->getDb()->prepare(
            "SELECT id FROM evenement_ressources WHERE id=? AND evenement_id=? AND type=? LIMIT 1"
        );
        $st->execute([$id, $evenementId, $type]);
        return (bool)$st->fetch();
    }

    private function queryCreate(array $d): int|false {
        try {
            $st = $this->getDb()->prepare(
                "INSERT INTO evenement_ressources (evenement_id,type,title,details,created_by,created_at)
                 VALUES (?,?,?,?,?,NOW())"
            );
            $st->execute([$d['evenement_id'],$d['type'],$d['title'],$d['details'],$d['created_by']]);
            return (int)$this->getDb()->lastInsertId();
        } catch (PDOException $e) { return false; }
    }

    private function queryUpdate(int $id, array $d): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenement_ressources SET title=?,details=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=? AND evenement_id=?"
        );
        return $st->execute([$d['title'],$d['details'],$id,$d['evenement_id']]);
    }

    private function queryDelete(int $id, int $evenementId): bool {
        $st = $this->getDb()->prepare("DELETE FROM evenement_ressources WHERE id=? AND evenement_id=?");
        return $st->execute([$id, $evenementId]);
    }

    // ─── ACTIONS ──────────────────────────────────────────────────────────────

    public function evenementRessources() {
        if (!$this->isAdmin()) {
            $this->setFlash('error','Access denied.'); $this->redirect('admin/login'); return;
        }

        $selectedId = (int)($_GET['evenement_id'] ?? 0);
        if ($selectedId <= 0) {
            $this->setFlash('error','Please choose an evenement first.');
            $this->redirect('event/evenements'); return;
        }

        $selectedEvenement = $this->queryFindEvenement($selectedId);
        if (!$selectedEvenement) {
            $this->setFlash('error','Evenement not found.');
            $this->redirect('event/evenements'); return;
        }

        $editId       = (int)($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $this->queryFindRessource($editId);
            if ($candidate && (int)$candidate['evenement_id'] === $selectedId) {
                $editResource = $candidate;
            }
        }

        $this->view('BackOffice/admin/evenement_ressources', [
            'title'               => 'Evenement Resources - APPOLIOS',
            'description'         => 'Manage evenement rules, materiel, and day plans',
            'selectedEvenementId' => $selectedId,
            'selectedEvenement'   => $selectedEvenement,
            'editResource'        => $editResource,
            'rules'               => $this->queryByTypeAndEvenement('rule',    $selectedId),
            'materials'           => $this->queryByTypeAndEvenement('materiel', $selectedId),
            'plans'               => $this->queryByTypeAndEvenement('plan',    $selectedId),
            'participations'      => $this->queryParticipationsByEvent($selectedId),
            'flash'               => $this->getFlash(),
        ]);
    }

    private function queryParticipationsByEvent(int $eventId): array {
        $st = $this->getDb()->prepare(
            "SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    (SELECT u.email FROM users u WHERE u.id = r.created_by LIMIT 1) as student_email
             FROM evenement_ressources r
             WHERE r.evenement_id = ? AND r.type = 'participation'
             ORDER BY r.created_at DESC"
        );
        $st->execute([$eventId]);
        return $st->fetchAll();
    }

    public function storeEvenementRessource() {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('ressource/evenement-ressources'); return; }

        $type        = $this->sanitize($_POST['type'] ?? '');
        $title       = $this->sanitize($_POST['title'] ?? '');
        $details     = $this->sanitize($_POST['details'] ?? '');
        $evenementId = (int)($_POST['evenement_id'] ?? 0);
        $isAjax      = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                    || (isset($_POST['batch_mode']) && $_POST['batch_mode'] === '1');

        // Prepend quantity to details for materiel
        if ($type === 'materiel') {
            $qty = (int)($_POST['quantite'] ?? 0);
            if ($qty > 0) {
                $details = 'Quantité: ' . $qty . ($details !== '' ? "\n" . $details : '');
            }
        }

        $errors = [];
        if (!in_array($type, ['rule','materiel','plan'], true)) $errors[] = 'Invalid resource type.';
        if (empty($title))    $errors[] = 'Title is required.';
        if ($evenementId <= 0) $errors[] = 'Please select an evenement.';
        elseif (!$this->queryFindEvenement($evenementId)) $errors[] = 'Evenement not found.';

        if (!empty($errors)) {
            if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>implode(' ',$errors)]); exit; }
            $_SESSION['errors'] = $errors; $_SESSION['old'] = $_POST;
            $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId); return;
        }

        $createdId = $this->queryCreate([
            'evenement_id'=>$evenementId,'type'=>$type,
            'title'=>$title,'details'=>$details,'created_by'=>$_SESSION['user_id']
        ]);
        $verified = $createdId && $this->queryExistsInScope($createdId, $evenementId, $type);
        $labels   = ['rule'=>'Rule','materiel'=>'Materiel','plan'=>'Plan'];

        if ($verified) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success'=>true,'message'=>$labels[$type].' saved successfully.','verified_in_right_list'=>true,'resource_id'=>(int)$createdId]);
                exit;
            }
            $this->setFlash('success', $labels[$type].' added successfully.');
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success'=>false,'message'=>'Save verification failed.','verified_in_right_list'=>false]);
                exit;
            }
            $this->setFlash('error','Save verification failed.');
        }
        $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId);
    }

    public function updateEvenementRessource($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['evenement_id'] ?? 0);
        $title       = $this->sanitize($_POST['title'] ?? '');
        $details     = $this->sanitize($_POST['details'] ?? '');

        if ($evenementId <= 0 || empty($title)) {
            $this->setFlash('error','Invalid data.');
            $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId.'&edit_id='.(int)$id);
            return;
        }

        $resource = $this->queryFindRessource((int)$id);
        if (!$resource || (int)$resource['evenement_id'] !== $evenementId) {
            $this->setFlash('error','Resource not found for this evenement.');
            $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId);
            return;
        }

        // Prepend quantity to details for materiel
        if ($resource['type'] === 'materiel') {
            $qty = (int) ($_POST['quantite'] ?? 0);
            if ($qty > 0) {
                $details = 'Quantité: ' . $qty . ($details !== '' ? "\n" . $details : '');
            }
        }

        $this->queryUpdate((int)$id, ['title'=>$title,'details'=>$details,'evenement_id'=>$evenementId])
            ? $this->setFlash('success','Ressource updated successfully.')
            : $this->setFlash('error','Failed to update ressource.');
        $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId);
    }

    public function deleteEvenementRessource($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['evenement_id'] ?? 0);
        if ($evenementId <= 0) {
            $this->setFlash('error','Invalid evenement context.');
            $this->redirect('event/evenements'); return;
        }

        $this->queryDelete((int)$id, $evenementId)
            ? $this->setFlash('success','Ressource deleted successfully.')
            : $this->setFlash('error','Failed to delete ressource.');
        $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId);
    }

    // ─── PARTICIPATION ACTIONS (Admin) ────────────────────────────────────────

    /**
     * Approve a student participation request.
     * Sets details = 'approved' on the evenement_ressources row.
     */
    public function approveParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['from_evenement_id'] ?? 0);
        $this->queryUpdateParticipationStatus((int)$id, 'approved')
            ? $this->setFlash('success', 'Participation approved.')
            : $this->setFlash('error', 'Failed to approve participation.');

        $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * Reject a student participation request.
     * Sets details = 'rejected' on the evenement_ressources row.
     */
    public function rejectParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['from_evenement_id'] ?? 0);
        $reason = $this->sanitize($_POST['reason'] ?? 'No specific reason provided.');
        
        $this->queryUpdateParticipationStatus((int)$id, 'rejected', $reason)
            ? $this->setFlash('success', 'Participation rejected with reason.')
            : $this->setFlash('error', 'Failed to reject participation.');

        $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
    }

    private function queryUpdateParticipationStatus(int $id, string $status, string $reason = null): bool {
        $s = in_array($status, ['approved','rejected'], true) ? $status : 'pending';
        $st = $this->getDb()->prepare(
            "UPDATE evenement_ressources
             SET details = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = 'participation'"
        );
        return $st->execute([$s, $reason, $id]);
    }

    /**
     * Delete a student participation record.
     * Admin can only delete participations on events THEY created.
     */
    public function deleteParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['from_evenement_id'] ?? 0);

        // Security: verify the event belongs to the current admin
        $event = $this->queryFindEvenement($evenementId);
        if (!$event || (int)($event['created_by'] ?? -1) !== (int)$_SESSION['user_id']) {
            $this->setFlash('error', 'Access denied. You can only delete participations for events you created.');
            $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
            return;
        }

        // Verify participation belongs to that event
        $st = $this->getDb()->prepare(
            "DELETE FROM evenement_ressources WHERE id = ? AND evenement_id = ? AND type = 'participation'"
        );
        $st->execute([(int)$id, $evenementId]);

        if ($st->rowCount() > 0) {
            $this->setFlash('success', 'Participation removed successfully.');
        } else {
            $this->setFlash('error', 'Participation not found.');
        }
        $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
    }

    /**
     * Generate AI dummy resources based on event details
     */
    public function generateAiResources() {
        if (!$this->isAdmin()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            exit;
        }

        $evenementId = (int)($_POST['evenement_id'] ?? 0);
        if ($evenementId <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid evenement']);
            exit;
        }

        $evenement = $this->queryFindEvenement($evenementId);
        if (!$evenement) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Event not found']);
            exit;
        }

        $title = $evenement['title'] ?? 'Evénement';
        $type = strtolower($evenement['type'] ?? 'general');
        $heureDebut = $evenement['heure_debut'] ?? '09:00:00';
        $heureFin = $evenement['heure_fin'] ?? '15:00:00';
        $capacite = (int)($evenement['capacite_max'] > 0 ? $evenement['capacite_max'] : rand(20, 100));

        // Attempt to use Gemini API if available
        if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'La clé API Gemini n\'est pas configurée dans config.php.']);
            exit;
        }

        $prompt = "Génère des ressources pour un événement nommé '$title' (Type: $type, Capacité: $capacite, de $heureDebut à $heureFin). Pour la liste des 'materiels', tu dois lister UNIQUEMENT les équipements que l'étudiant/participant doit apporter avec lui (ex: Ordinateur portable, Multiprise, Rallonge, etc.), et non ce que l'événement fournit. Renvoie un JSON strict avec ce format : {\"rules\":[{\"title\":\"...\",\"details\":\"...\"}],\"materiels\":[{\"title\":\"...\",\"quantite\":... ,\"details\":\"...\"}],\"plan\":[{\"title\":\"...\",\"details\":\"...\"}]}. Ne renvoie que le JSON, pas de markdown.";
        
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . GEMINI_API_KEY;
        $data = ['contents' => [['parts' => [['text' => $prompt]]]]];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Erreur de connexion à l\'API: ' . $error_msg]);
            exit;
        }
        curl_close($ch);

        $apiResponse = null;
        if ($response) {
            $json = json_decode($response, true);
            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $json['candidates'][0]['content']['parts'][0]['text'];
                $text = trim(str_replace(['```json', '```'], '', $text));
                $apiResponse = json_decode($text, true);
            } elseif (isset($json['error'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur API: ' . ($json['error']['message'] ?? 'Inconnue')]);
                exit;
            }
        }

        if (!$apiResponse || !isset($apiResponse['rules'], $apiResponse['materiels'], $apiResponse['plan'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'L\'IA a renvoyé un format invalide. Veuillez réessayer.']);
            exit;
        }

        $rules = $apiResponse['rules'];
        $materiels = $apiResponse['materiels'];
        $plan = $apiResponse['plan'];

        // Insert directly into DB
        foreach ($rules as $r) {
            $this->queryCreate([
                'evenement_id' => $evenementId,
                'type' => 'rule',
                'title' => $r['title'],
                'details' => $r['details'],
                'created_by' => $_SESSION['user_id']
            ]);
        }

        foreach ($materiels as $m) {
            $materielDetails = 'Quantité: ' . ($m['quantite'] ?? 1) . "\n" . ($m['details'] ?? '');
            $this->queryCreate([
                'evenement_id' => $evenementId,
                'type' => 'materiel',
                'title' => $m['title'],
                'details' => trim($materielDetails),
                'created_by' => $_SESSION['user_id']
            ]);
        }

        foreach ($plan as $p) {
            $this->queryCreate([
                'evenement_id' => $evenementId,
                'type' => 'plan',
                'title' => $p['title'],
                'details' => $p['details'],
                'created_by' => $_SESSION['user_id']
            ]);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Ressources générées et ajoutées avec succès.'
        ]);
        exit;
    }
}
