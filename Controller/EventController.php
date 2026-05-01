<?php
/**
 * APPOLIOS Event Controller
 * SQL + business logic here. Model = getters/setters only.
 */
require_once __DIR__ . '/../Controller/BaseController.php';
require_once __DIR__ . '/../Model/Evenement.php';

class EventController extends BaseController {

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

    private function queryAllEvenements(): array {
        return $this->getDb()->query(
            "SELECT e.*, u.name as creator_name, u.role as creator_role, COUNT(r.id) as resource_count
             FROM evenements e
             JOIN users u ON e.created_by = u.id
             LEFT JOIN evenement_ressources r ON r.evenement_id = e.id
             GROUP BY e.id
             ORDER BY COALESCE(CONCAT(e.date_debut,' ',e.heure_debut), e.event_date) ASC"
        )->fetchAll();
    }

    private function queryFindById(int $id): array|false {
        $st = $this->getDb()->prepare("SELECT * FROM evenements WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryCreate(array $d): int|false {
        try {
            $st = $this->getDb()->prepare(
                "INSERT INTO evenements
                 (title,titre,description,date_debut,date_fin,heure_debut,heure_fin,
                  lieu,capacite_max,type,statut,approval_status,location,event_date,created_by,created_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())"
            );
            $st->execute([
                $d['title'],$d['titre'],$d['description'],$d['date_debut'],$d['date_fin'],
                $d['heure_debut'],$d['heure_fin'],$d['lieu'],$d['capacite_max'],
                $d['type'],$d['statut'],$d['approval_status']??'approved',
                $d['location'],$d['event_date'],$d['created_by']
            ]);
            return (int)$this->getDb()->lastInsertId();
        } catch (PDOException $e) { return false; }
    }

    private function queryUpdate(int $id, array $d): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenements
             SET title=?,titre=?,description=?,date_debut=?,date_fin=?,
                 heure_debut=?,heure_fin=?,lieu=?,capacite_max=?,type=?,
                 statut=?,location=?,event_date=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=?"
        );
        return $st->execute([
            $d['title'],$d['titre'],$d['description'],$d['date_debut'],$d['date_fin'],
            $d['heure_debut'],$d['heure_fin'],$d['lieu'],$d['capacite_max'],
            $d['type'],$d['statut'],$d['location'],$d['event_date'],$id
        ]);
    }

    private function queryDelete(int $id): bool {
        $st = $this->getDb()->prepare("DELETE FROM evenements WHERE id=?");
        return $st->execute([$id]);
    }

    private function queryPendingRequests(): array {
        return $this->getDb()->query(
            "SELECT e.*,u.name as creator_name,u.email as creator_email
             FROM evenements e JOIN users u ON u.id=e.created_by
             WHERE e.approval_status='pending' AND u.role='teacher'
             ORDER BY e.created_at DESC"
        )->fetchAll();
    }

    private function queryRejectedRequests(): array {
        return $this->getDb()->query(
            "SELECT e.*,u.name as creator_name,u.email as creator_email
             FROM evenements e JOIN users u ON u.id=e.created_by
             WHERE e.approval_status='rejected' AND u.role='teacher'
             ORDER BY e.updated_at DESC"
        )->fetchAll();
    }

    private function queryRessourcesByEvent(int $id): array {
        $st = $this->getDb()->prepare(
            "SELECT type, title, details FROM evenement_ressources
             WHERE evenement_id = ? AND type IN ('rule','materiel','plan')
             ORDER BY type, created_at ASC"
        );
        $st->execute([$id]);
        $rows = $st->fetchAll();
        $grouped = ['rule' => [], 'materiel' => [], 'plan' => []];
        foreach ($rows as $r) {
            $grouped[$r['type']][] = ['title' => $r['title'], 'details' => $r['details'] ?? ''];
        }
        return $grouped;
    }

    private function queryUpdateApproval(int $id, string $status, ?int $adminId, ?string $reason): bool {
        $s = strtolower($status) === 'approved' ? 'approved' : 'rejected';
        $st = $this->getDb()->prepare(
            "UPDATE evenements
             SET approval_status=?,approved_by=?,approved_at=NOW(),
                 rejection_reason=?,updated_at=CURRENT_TIMESTAMP
             WHERE id=?"
        );
        return $st->execute([$s, $adminId, $s==='rejected'?$reason:null, $id]);
    }

    private function queryAllParticipations(): array {
        $st = $this->getDb()->query(
            "SELECT r.id, r.evenement_id, r.created_by as student_id,
                    r.title as student_name, r.details as status, r.created_at,
                    e.title as event_title, e.date_debut, e.heure_debut,
                    u.name as student_name_full, u.email as student_email,
                    u.role as student_role, u.created_at as student_registered_at
             FROM evenement_ressources r
             JOIN evenements e ON r.evenement_id = e.id
             JOIN users u ON r.created_by = u.id
             WHERE r.type = 'participation'
             ORDER BY r.created_at DESC"
        );
        return $st->fetchAll();
    }

    private function queryFindParticipationById(int $id): array|false {
        $st = $this->getDb()->prepare(
            "SELECT * FROM evenement_ressources WHERE id = ? AND type = 'participation' LIMIT 1"
        );
        $st->execute([$id]);
        return $st->fetch();
    }

    private function queryUpdateParticipationStatus(int $id, string $status, string $reason = null): bool {
        $st = $this->getDb()->prepare(
            "UPDATE evenement_ressources
             SET details = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ? AND type = 'participation'"
        );
        return $st->execute([$status, $reason, $id]);
    }

    // ─── ACTIONS ──────────────────────────────────────────────────────────────

    public function evenements() {
        if (!$this->isAdmin()) { $this->setFlash('error','Access denied.'); $this->redirect('admin/login'); return; }
        
        $allParticipations = $this->queryAllParticipations();
        $participationsByEvent = [];
        foreach ($allParticipations as $p) {
            $eventId = (int)$p['evenement_id'];
            if (!isset($participationsByEvent[$eventId])) {
                $participationsByEvent[$eventId] = [];
            }
            $participationsByEvent[$eventId][] = $p;
        }

        $this->view('BackOffice/admin/evenements', [
            'title'      => 'Manage Evenements - APPOLIOS',
            'description'=> 'Evenement management panel',
            'evenements' => $this->queryAllEvenements(),
            'participationsByEvent' => $participationsByEvent,
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

        $result = $this->queryCreate([
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
        $evenement = $this->queryFindById((int)$id);
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

        $result = $this->queryUpdate((int)$id, [
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
        $ev = $this->queryFindById((int)$id);
        if (!$ev) { $this->setFlash('error','Not found.'); $this->redirect('event/evenements'); return; }
        if ($ev['created_by'] != $_SESSION['user_id']) {
            $this->setFlash('error','You can only delete events you created.');
            $this->redirect('event/evenements'); return;
        }
        $this->queryDelete((int)$id)
            ? $this->setFlash('success','Evenement deleted!')
            : $this->setFlash('error','Failed to delete.');
        $this->redirect('event/evenements');
    }

    public function statsEvenements() {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }

        // Get basic participation stats for the radar/line charts
        $st = $this->getDb()->query(
            "SELECT e.title, e.capacite_max, e.event_date, e.date_debut,
                    (SELECT COUNT(*) FROM evenement_ressources r WHERE r.evenement_id = e.id AND r.type = 'participation' AND r.details = 'approved') as participant_count
             FROM evenements e
             ORDER BY COALESCE(e.date_debut, e.event_date, e.created_at) ASC"
        );
        $eventStats = $st->fetchAll();

        // Get type counts for pie/doughnut/bar
        $stTypes = $this->getDb()->query("SELECT type, COUNT(*) as count FROM evenements GROUP BY type");
        $typeStats = $stTypes->fetchAll();

        $data = [
            'title' => 'Event Statistics - APPOLIOS',
            'description' => 'Dashboard for event statistics, participation and insights',
            'eventStats' => $eventStats,
            'typeStats' => $typeStats
        ];

        $this->view('BackOffice/admin/stat_evenement', $data);
    }

    public function exportStatsPdf() {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            return;
        }

        // Get Event Stats
        $st = $this->getDb()->query(
            "SELECT e.title, e.capacite_max, e.event_date, e.date_debut,
                    (SELECT COUNT(*) FROM evenement_ressources r WHERE r.evenement_id = e.id AND r.type = 'participation' AND r.details = 'approved') as participant_count
             FROM evenements e
             ORDER BY COALESCE(e.date_debut, e.event_date, e.created_at) ASC"
        );
        $eventStats = $st->fetchAll();

        // Get Type Stats
        $stTypes = $this->getDb()->query("SELECT type, COUNT(*) as count FROM evenements GROUP BY type");
        $typeStats = $stTypes->fetchAll();

        // Generate Simple PDF/Printable HTML
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Event Statistics Export - APPOLIOS</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                    padding: 40px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 40px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #548CA8;
                }
                .header h1 {
                    color: #2B4865;
                    font-size: 28px;
                    margin-bottom: 10px;
                }
                .header p {
                    color: #666;
                }
                h2 {
                    color: #548CA8;
                    font-size: 20px;
                    margin-top: 30px;
                    margin-bottom: 15px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                }
                th, td {
                    border: 1px solid #ccc;
                    padding: 12px;
                    text-align: left;
                }
                th {
                    background-color: #f8fafc;
                    color: #2B4865;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #fafafa;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .footer {
                    text-align: center;
                    margin-top: 50px;
                    font-size: 12px;
                    color: #999;
                    border-top: 1px solid #eee;
                    padding-top: 20px;
                }
                @media print {
                    body { padding: 0; }
                    .no-print { display: none; }
                }
            </style>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        </head>
        <body>
            <div class="no-print" style="margin-bottom: 20px; text-align: center;">
                <p style="font-size: 16px; color: #548CA8; font-weight: bold;">Generating your PDF, please wait...</p>
                <p style="font-size: 12px; color: #666;">This tab will automatically close once the download begins.</p>
            </div>

            <div id="pdf-content" style="padding: 20px;">
                <div class="header">
                    <h1>APPOLIOS - Event Statistics Export</h1>
                    <p>Generated on <?= date('Y-m-d H:i:s') ?></p>
                </div>

                <h2>1. Participation by Scheduled Event</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Date</th>
                            <th class="text-right">Max Capacity</th>
                            <th class="text-right">Total Participants</th>
                            <th class="text-right">Fill Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($eventStats)): ?>
                            <tr><td colspan="5" class="text-center">No event data available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($eventStats as $stat): 
                                $date = !empty($stat['event_date']) ? $stat['event_date'] : $stat['date_debut'];
                                $capMax = (int)$stat['capacite_max'];
                                $parts = (int)$stat['participant_count'];
                                $fillRate = $capMax > 0 ? round(($parts / $capMax) * 100, 2) : 0;
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($stat['title']) ?></strong></td>
                                <td><?= $date ? date('M d, Y', strtotime($date)) : 'N/A' ?></td>
                                <td class="text-right"><?= $capMax > 0 ? $capMax : 'Unlimited' ?></td>
                                <td class="text-right"><?= $parts ?></td>
                                <td class="text-right">
                                    <?php if ($fillRate >= 100): ?>
                                        <span style="color: #dc3545; font-weight: bold;"><?= $fillRate ?>% (Full)</span>
                                    <?php elseif ($fillRate >= 75): ?>
                                        <span style="color: #fd7e14;"><?= $fillRate ?>%</span>
                                    <?php else: ?>
                                        <span style="color: #28a745;"><?= $fillRate ?>%</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h2>2. Events Distribution by Category (Type)</h2>
                <table style="width: 50%;">
                    <thead>
                        <tr>
                            <th>Event Type</th>
                            <th class="text-right">Total Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($typeStats)): ?>
                            <tr><td colspan="2" class="text-center">No data available.</td></tr>
                        <?php else: ?>
                            <?php foreach ($typeStats as $ts): ?>
                            <tr>
                                <td><?= htmlspecialchars(ucfirst($ts['type'])) ?></td>
                                <td class="text-right"><?= (int)$ts['count'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="footer">
                    &copy; <?= date('Y') ?> APPOLIOS Educational Platform. All rights reserved.
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var element = document.getElementById('pdf-content');
                    var opt = {
                        margin:       0.5,
                        filename:     'APPOLIOS_Event_Statistics.pdf',
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2 },
                        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
                    };

                    // Generate PDF and then close the window
                    html2pdf().set(opt).from(element).save().then(function() {
                        setTimeout(function() {
                            window.close();
                        }, 1000);
                    });
                });
            </script>
        </body>
        </html>
        <?php
    }

    public function evenementRequests() {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }

        $pending  = $this->queryPendingRequests();
        $rejected = $this->queryRejectedRequests();

        // Attach resources to each event
        foreach ($pending  as &$ev) { $ev['ressources'] = $this->queryRessourcesByEvent((int)$ev['id']); }
        foreach ($rejected as &$ev) { $ev['ressources'] = $this->queryRessourcesByEvent((int)$ev['id']); }
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
        if (!$this->queryFindById((int)$id)) {
            $this->setFlash('error','Not found.'); $this->redirect('event/evenement-requests'); return;
        }
        $this->queryUpdateApproval((int)$id,'approved',(int)$_SESSION['user_id'],null)
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
        if (!$this->queryFindById((int)$id)) {
            $this->setFlash('error','Not found.'); $this->redirect('event/evenement-requests'); return;
        }
        $this->queryUpdateApproval((int)$id,'rejected',(int)$_SESSION['user_id'],$reason)
            ? $this->setFlash('success','Request rejected.')
            : $this->setFlash('error','Failed to reject.');
        $this->redirect('event/evenement-requests');
    }

    public function approveParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $participation = $this->queryFindParticipationById((int) $id);
        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
        } else {
            $this->queryUpdateParticipationStatus((int) $id, 'approved');
            $this->setFlash('success', 'Participation approved.');
        }
        $this->redirect('event/evenements');
    }

    public function rejectParticipation($id) {
        if (!$this->isAdmin()) { $this->redirect('admin/login'); return; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('event/evenements'); return; }

        $reason = $this->sanitize($_POST['reason'] ?? 'No specific reason provided.');
        $participation = $this->queryFindParticipationById((int) $id);
        if (!$participation) {
            $this->setFlash('error', 'Participation not found.');
        } else {
            $this->queryUpdateParticipationStatus((int) $id, 'rejected', $reason);
            $this->setFlash('success', 'Participation rejected.');
        }
        $this->redirect('event/evenements');
    }
}
