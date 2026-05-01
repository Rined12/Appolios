<?php
/**
 * APPOLIOS Event Controller — routing, validation, and business rules.
 * Persistence: EvenementRepository, EvenementRessourceRepository.
 */
require_once __DIR__ . '/../Controller/BaseController.php';

class EventController extends BaseController {

    /**
     * @return array{0: EvenementRepository, 1: EvenementRessourceRepository}
     */
    private function evenementServices(): array
    {
        return [
            $this->model('EvenementRepository'),
            $this->model('EvenementRessourceRepository'),
        ];
    }

    public function evenements() {
        if (!$this->isAdmin()) { $this->setFlash('error','Access denied.'); $this->redirect('admin/login'); return; }
        [$evenementRepo] = $this->evenementServices();
        $this->view('BackOffice/admin/evenements', [
            'title'      => 'Manage Evenements - APPOLIOS',
            'description'=> 'Evenement management panel',
            'evenements' => $evenementRepo->findAllWithCreatorAndResourceCount(),
            'flash'      => $this->getFlash(),
        ]);
    }

    public function addEvenement() {
        if (!$this->isAdmin()) { $this->setFlash('error','Access denied.'); $this->redirect('admin/login'); return; }
        $this->view('BackOffice/admin/add_evenement', [
            'title'      => 'Add Evenement - APPOLIOS',
            'description'=> 'Create a new evenement',
            'flash'      => $this->getFlash(),
        ]);
    }

    public function storeEvenement() {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $title       = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $dateDebut   = $this->sanitize($_POST['date_debut'] ?? '');
        $dateFin     = $this->sanitize($_POST['date_fin'] ?? '');
        $heureDebut  = $this->sanitize($_POST['heure_debut'] ?? '');
        $heureFin    = $this->sanitize($_POST['heure_fin'] ?? '');
        $lieu        = $this->sanitize($_POST['lieu'] ?? '');
        $capaciteMax = (int)($_POST['capacite_max'] ?? 0);
        $type        = $this->sanitize($_POST['type'] ?? 'general');
        $statut      = $this->sanitize($_POST['statut'] ?? 'planifie');
        $errors      = [];
        $minDate     = date('Y-m-d', strtotime('+1 day'));

        if (empty($title))       $errors['title']        = 'Event title is required';
        if (empty($description)) $errors['description']  = 'Event description is required';
        if (empty($dateDebut) || !strtotime($dateDebut)) $errors['date_debut'] = 'Valid start date required';
        if (!empty($dateDebut) && strtotime($dateDebut) && $dateDebut < $minDate)
                                 $errors['date_debut']   = 'Start date must be at least tomorrow';
        if (empty($heureDebut))  $errors['heure_debut']  = 'Start time is required';
        if (!empty($dateFin) && strtotime($dateFin) && !empty($dateDebut) && strtotime($dateFin) < strtotime($dateDebut))
                                 $errors['date_fin']     = 'End date cannot be before start date';
        if ($capaciteMax < 0)    $errors['capacite_max'] = 'Capacity must be positive';

        if (!empty($errors)) {
            $this->setErrors($errors); $_SESSION['old'] = $_POST;
            $this->redirect('event/add-evenement'); return;
        }

        [$evenementRepo] = $this->evenementServices();
        $result = $evenementRepo->create([
            'title'=>$title, 'titre'=>$title, 'description'=>$description,
            'date_debut'=>$dateDebut, 'date_fin'=>$dateFin?:null,
            'heure_debut'=>$heureDebut?:null, 'heure_fin'=>$heureFin?:null,
            'lieu'=>$lieu, 'capacite_max'=>$capaciteMax>0?$capaciteMax:null,
            'type'=>$type, 'statut'=>$statut, 'location'=>$lieu,
            'event_date'=>$dateDebut.' '.($heureDebut?:'00:00').':00',
            'created_by'=>$_SESSION['user_id'],
        ]);

        if ($result) {
            $this->setFlash('success','Evenement created successfully!');
            if (isset($_POST['action']) && $_POST['action']==='save_and_resources')
                $this->redirect('ressource/evenement-ressources&evenement_id='.$result);
            else $this->redirect('event/evenements');
        } else {
            $this->setFlash('error','Failed to create evenement.');
            $this->redirect('event/add-evenement');
        }
    }

    public function editEvenement($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        [$evenementRepo] = $this->evenementServices();
        $evenement = $evenementRepo->findById((int)$id);
        if (!$evenement) { $this->setFlash('error','Evenement not found.'); $this->redirect('event/evenements'); return; }
        $this->view('BackOffice/admin/edit_evenement', [
            'title'=>'Edit Evenement - APPOLIOS','description'=>'Update evenement details',
            'evenement'=>$evenement,'flash'=>$this->getFlash(),
        ]);
    }

    public function updateEvenement($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $title       = $this->sanitize($_POST['title'] ?? '');
        $description = $this->sanitize($_POST['description'] ?? '');
        $dateDebut   = $this->sanitize($_POST['date_debut'] ?? '');
        $dateFin     = $this->sanitize($_POST['date_fin'] ?? '');
        $heureDebut  = $this->sanitize($_POST['heure_debut'] ?? '');
        $heureFin    = $this->sanitize($_POST['heure_fin'] ?? '');
        $lieu        = $this->sanitize($_POST['lieu'] ?? '');
        $capaciteMax = (int)($_POST['capacite_max'] ?? 0);
        $type        = $this->sanitize($_POST['type'] ?? 'general');
        $statut      = $this->sanitize($_POST['statut'] ?? 'planifie');
        $errors      = [];
        $minDate     = date('Y-m-d', strtotime('+1 day'));

        if (empty($title))       $errors['title']        = 'Event title is required';
        if (empty($description)) $errors['description']  = 'Event description is required';
        if (empty($dateDebut) || !strtotime($dateDebut)) $errors['date_debut'] = 'Valid start date required';
        if (!empty($dateDebut) && strtotime($dateDebut) && $dateDebut < $minDate)
                                 $errors['date_debut']   = 'Start date must be at least tomorrow';
        if (empty($heureDebut))  $errors['heure_debut']  = 'Start time is required';
        if (!empty($dateFin) && strtotime($dateFin) && !empty($dateDebut) && strtotime($dateFin) < strtotime($dateDebut))
                                 $errors['date_fin']     = 'End date cannot be before start date';
        if ($capaciteMax < 0)    $errors['capacite_max'] = 'Capacity must be positive';

        if (!empty($errors)) {
            $this->setErrors($errors); $_SESSION['old'] = $_POST;
            $this->redirect('event/edit-evenement/'.(int)$id); return;
        }

        [$evenementRepo] = $this->evenementServices();
        $result = $evenementRepo->update((int)$id, [
            'title'=>$title,'titre'=>$title,'description'=>$description,
            'date_debut'=>$dateDebut,'date_fin'=>$dateFin?:null,
            'heure_debut'=>$heureDebut?:null,'heure_fin'=>$heureFin?:null,
            'lieu'=>$lieu,'capacite_max'=>$capaciteMax>0?$capaciteMax:null,
            'type'=>$type,'statut'=>$statut,'location'=>$lieu,
            'event_date'=>$dateDebut.' '.($heureDebut?:'00:00').':00',
        ]);

        if ($result) { $this->setFlash('success','Evenement updated!'); $this->redirect('event/evenements'); }
        else { $this->setFlash('error','Failed to update.'); $this->redirect('event/edit-evenement/'.(int)$id); }
    }

    public function deleteEvenement($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        [$evenementRepo] = $this->evenementServices();
        $ev = $evenementRepo->findById((int)$id);
        if (!$ev) { $this->setFlash('error','Not found.'); $this->redirect('event/evenements'); return; }
        if ($ev['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error','You can only delete events you created.');
            $this->redirect('event/evenements'); return;
        }
        $evenementRepo->delete((int)$id)
            ? $this->setFlash('success','Evenement deleted!')
            : $this->setFlash('error','Failed to delete.');
        $this->redirect('event/evenements');
    }

    public function evenementRequests() {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }

        [$evenementRepo, $resRepo] = $this->evenementServices();
        $pending  = $evenementRepo->findPendingTeacherRequests();
        $rejected = $evenementRepo->findRejectedTeacherRequests();

        foreach ($pending  as &$ev) { $ev['ressources'] = $resRepo->getGroupedPublicRessources((int)$ev['id']); }
        foreach ($rejected as &$ev) { $ev['ressources'] = $resRepo->getGroupedPublicRessources((int)$ev['id']); }
        unset($ev);

        $this->view('BackOffice/admin/evenement_requests', [
            'title'           =>'Evenement Requests - APPOLIOS',
            'description'     =>'Review pending evenement requests from teachers',
            'requests'        => $pending,
            'rejectedRequests'=> $rejected,
            'flash'           => $this->getFlash(),
        ]);
    }

    public function approveEvenement($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD']!=='POST') { $this->redirect('event/evenement-requests'); return; }
        [$evenementRepo] = $this->evenementServices();
        if (!$evenementRepo->findById((int)$id)) {
            $this->setFlash('error','Not found.'); $this->redirect('event/evenement-requests'); return;
        }
        $evenementRepo->updateApproval((int)$id,'approved',(int)$_SESSION['user_id'],null)
            ? $this->setFlash('success','Request approved.')
            : $this->setFlash('error','Failed to approve.');
        $this->redirect('event/evenement-requests');
    }

    public function rejectEvenement($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD']!=='POST') { $this->redirect('event/evenement-requests'); return; }
        $reason = $this->sanitize($_POST['rejection_reason'] ?? '');
        if (empty($reason)) {
            $this->setErrors(['rejection_reason_'.$id=>'Veuillez renseigner ce champ.']);
            $this->redirect('event/evenement-requests'); return;
        }
        [$evenementRepo] = $this->evenementServices();
        if (!$evenementRepo->findById((int)$id)) {
            $this->setFlash('error','Not found.'); $this->redirect('event/evenement-requests'); return;
        }
        $evenementRepo->updateApproval((int)$id,'rejected',(int)$_SESSION['user_id'],$reason)
            ? $this->setFlash('success','Request rejected.')
            : $this->setFlash('error','Failed to reject.');
        $this->redirect('event/evenement-requests');
    }
}
