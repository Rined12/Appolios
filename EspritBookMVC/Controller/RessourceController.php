<?php
/**
 * APPOLIOS Ressource Controller — validation and workflow for event resources.
 * Persistence: EvenementRepository, EvenementRessourceRepository.
 */
require_once __DIR__ . '/../Controller/BaseController.php';

class RessourceController extends BaseController {

    /**
     * @return array{0: EvenementRepository, 1: EvenementRessourceRepository}
     */
    private function services(): array
    {
        return [
            $this->model('EvenementRepository'),
            $this->model('EvenementRessourceRepository'),
        ];
    }

    public function evenementRessources() {
        if (!$this->isAdmin()) {
            $this->setFlash('error','Access denied.'); $this->redirect('admin/login'); return;
        }

        $selectedId = (int)($_GET['evenement_id'] ?? 0);
        if ($selectedId <= 0) {
            $this->setFlash('error','Please choose an evenement first.');
            $this->redirect('event/evenements'); return;
        }

        [$evenementRepo, $resRepo] = $this->services();
        $selectedEvenement = $evenementRepo->findById($selectedId);
        if (!$selectedEvenement) {
            $this->setFlash('error','Evenement not found.');
            $this->redirect('event/evenements'); return;
        }

        $editId       = (int)($_GET['edit_id'] ?? 0);
        $editResource = null;
        if ($editId > 0) {
            $candidate = $resRepo->findById($editId);
            if ($candidate && (int)$candidate['evenement_id'] === $selectedId) {
                $editResource = $candidate;
            }
        }

        $participationsList = $resRepo->findParticipationsByEvent($selectedId);
        $participationPendingCount = count(array_filter(
            $participationsList,
            static fn(array $p): bool => (string) ($p['status'] ?? '') === 'pending'
        ));
        $adminIsCreator = isset($_SESSION['user_id'])
            && (int) ($selectedEvenement['created_by'] ?? -1) === (int) $_SESSION['user_id'];

        $this->view('BackOffice/admin/evenement_ressources', [
            'title'               => 'Evenement Resources - APPOLIOS',
            'description'         => 'Manage evenement rules, materiel, and day plans',
            'selectedEvenementId' => $selectedId,
            'selectedEvenement'   => $selectedEvenement,
            'editResource'        => $editResource,
            'rules'               => $resRepo->findByTypeAndEvent('rule',    $selectedId),
            'materials'           => $resRepo->findByTypeAndEvent('materiel', $selectedId),
            'plans'               => $resRepo->findByTypeAndEvent('plan',    $selectedId),
            'participations'      => $participationsList,
            'participation_pending_count' => $participationPendingCount,
            'admin_is_creator'    => $adminIsCreator,
            'flash'               => $this->getFlash(),
        ]);
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

        if ($type === 'materiel') {
            $qty = (int)($_POST['quantite'] ?? 0);
            if ($qty > 0) {
                $details = 'Quantité: ' . $qty . ($details !== '' ? "\n" . $details : '');
            }
        }

        $errors = [];
        if (!in_array($type, ['rule','materiel','plan'], true)) $errors[] = 'Invalid resource type.';
        if (empty($title))    $errors[] = 'Title is required.';
        [$evenementRepo, $resRepo] = $this->services();
        if ($evenementId <= 0) $errors[] = 'Please select an evenement.';
        elseif (!$evenementRepo->findById($evenementId)) $errors[] = 'Evenement not found.';

        if (!empty($errors)) {
            if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>implode(' ',$errors)]); exit; }
            $_SESSION['errors'] = $errors; $_SESSION['old'] = $_POST;
            $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId); return;
        }

        $createdId = $resRepo->create([
            'evenement_id'=>$evenementId,'type'=>$type,
            'title'=>$title,'details'=>$details,'created_by'=>$_SESSION['user_id']
        ]);
        $verified = $createdId && $resRepo->existsInScope($createdId, $evenementId, $type);
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

        [, $resRepo] = $this->services();
        $resource = $resRepo->findById((int)$id);
        if (!$resource || (int)$resource['evenement_id'] !== $evenementId) {
            $this->setFlash('error','Resource not found for this evenement.');
            $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId);
            return;
        }

        $resRepo->update((int)$id, ['title'=>$title,'details'=>$details,'evenement_id'=>$evenementId])
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

        [, $resRepo] = $this->services();
        $resRepo->delete((int)$id, $evenementId)
            ? $this->setFlash('success','Ressource deleted successfully.')
            : $this->setFlash('error','Failed to delete ressource.');
        $this->redirect('ressource/evenement-ressources&evenement_id='.$evenementId);
    }

    public function approveParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['from_evenement_id'] ?? 0);
        [, $resRepo] = $this->services();
        $resRepo->updateParticipationStatusAdmin((int)$id, 'approved')
            ? $this->setFlash('success', 'Participation approved.')
            : $this->setFlash('error', 'Failed to approve participation.');

        $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
    }

    public function rejectParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['from_evenement_id'] ?? 0);
        $reason = $this->sanitize($_POST['reason'] ?? 'No specific reason provided.');

        [, $resRepo] = $this->services();
        $resRepo->updateParticipationStatusAdmin((int)$id, 'rejected', $reason)
            ? $this->setFlash('success', 'Participation rejected with reason.')
            : $this->setFlash('error', 'Failed to reject participation.');

        $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
    }

    public function deleteParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $evenementId = (int)($_POST['from_evenement_id'] ?? 0);

        [$evenementRepo, $resRepo] = $this->services();
        $event = $evenementRepo->findById($evenementId);
        if (!$event || (int)($event['created_by'] ?? -1) !== (int)$_SESSION['user_id']) {
            $this->setFlash('error', 'Access denied. You can only delete participations for events you created.');
            $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
            return;
        }

        if ($resRepo->deleteParticipationForEvent((int)$id, $evenementId) > 0) {
            $this->setFlash('success', 'Participation removed successfully.');
        } else {
            $this->setFlash('error', 'Participation not found.');
        }
        $this->redirect('ressource/evenement-ressources&evenement_id=' . $evenementId);
    }
}
