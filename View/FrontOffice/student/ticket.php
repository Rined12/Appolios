<?php
/**
 * APPOLIOS - Event Ticket View (Premium Design)
 */
?>

<div style="min-height: 100vh; background: #f8fafc; padding: 40px 20px; font-family: 'Inter', sans-serif; display: flex; flex-direction: column; align-items: center;">
    
    <!-- Action Bar (Print/Download) -->
    <div style="width: 100%; max-width: 700px; display: flex; justify-content: flex-end; margin-bottom: 24px; no-print">
        <button onclick="window.print()" style="background: #2B4865; color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print Ticket
        </button>
    </div>

    <!-- Ticket Card -->
    <div id="printable-ticket" style="width: 100%; max-width: 700px; background: white; border-radius: 30px; overflow: hidden; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.1); border: 1px solid #e2e8f0; display: flex; flex-direction: column;">
        
        <!-- Ticket Header (Hero Section) -->
        <div style="background: linear-gradient(135deg, #2B4865 0%, #548CA8 100%); padding: 40px; color: white; position: relative; overflow: hidden;">
            <!-- Abstract background circles -->
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -30px; left: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 8px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">
                        Official Entry Ticket
                    </div>
                </div>
                <h1 style="margin: 0; font-size: 2.2rem; font-weight: 900; line-height: 1.2;"><?= htmlspecialchars($event['title'] ?? ($event['titre'] ?? 'Event Title')) ?></h1>
                <p style="margin: 12px 0 0; font-size: 1.1rem; opacity: 0.9; font-weight: 500;"><?= htmlspecialchars($event['type'] ?? 'General Event') ?></p>
            </div>
        </div>

        <!-- Ticket Body -->
        <div style="padding: 40px; display: grid; grid-template-columns: 1fr 200px; gap: 40px;">
            
            <!-- Details Column -->
            <div style="display: flex; flex-direction: column; gap: 30px;">
                
                <!-- Participant -->
                <div>
                    <span style="display: block; color: #94a3b8; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Participant Name</span>
                    <p style="margin: 0; color: #0f172a; font-size: 1.25rem; font-weight: 800;"><?= htmlspecialchars($user['name'] ?? ($_SESSION['user_name'] ?? 'Attendee')) ?></p>
                    <p style="margin: 4px 0 0; color: #64748b; font-size: 0.9rem;"><?= htmlspecialchars($user['email'] ?? ($_SESSION['user_email'] ?? '')) ?></p>
                </div>

                <!-- Date & Time -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <span style="display: block; color: #94a3b8; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Date</span>
                        <p style="margin: 0; color: #0f172a; font-size: 1.1rem; font-weight: 700;"><?= date('F d, Y', strtotime($event['date_debut'] ?? 'now')) ?></p>
                    </div>
                    <div>
                        <span style="display: block; color: #94a3b8; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Time</span>
                        <p style="margin: 0; color: #0f172a; font-size: 1.1rem; font-weight: 700;"><?= date('H:i A', strtotime($event['heure_debut'] ?? '09:00:00')) ?></p>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <span style="display: block; color: #94a3b8; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Location</span>
                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2B4865" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <p style="margin: 0; color: #0f172a; font-size: 1.1rem; font-weight: 700;"><?= htmlspecialchars($event['lieu'] ?? ($event['location'] ?? 'Main Campus')) ?></p>
                    </div>
                </div>

            </div>

            <!-- QR Code Column -->
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; border-left: 2px dashed #e2e8f0; padding-left: 40px;">
                <div style="width: 140px; height: 140px; background: white; border: 2px solid #0f172a; padding: 10px; border-radius: 12px; margin-bottom: 12px; position: relative;">
                    <!-- Placeholder for QR -->
                    <div style="width: 100%; height: 100%; background: #0f172a; border-radius: 4px; display: flex; flex-wrap: wrap; padding: 2px;">
                        <?php for($i=0; $i<64; $i++): ?>
                            <div style="width: 12.5%; height: 12.5%; background: <?= (rand(0,1) ? '#fff' : '#0f172a') ?>;"></div>
                        <?php endfor; ?>
                    </div>
                </div>
                <p style="margin: 0; color: #94a3b8; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;">TICKET-ID-<?= strtoupper(substr(md5($event['id'].$_SESSION['user_id']), 0, 8)) ?></p>
            </div>

        </div>

        <!-- Ticket Footer -->
        <div style="background: #f8fafc; padding: 20px 40px; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="<?= APP_URL ?>/View/assets/images/branding/appolios-logo.png" alt="Logo" style="height: 24px;">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">Powered by APPOLIOS</span>
            </div>
            <div style="color: #94a3b8; font-size: 0.75rem; font-style: italic;">
                Please present this ticket at the entrance.
            </div>
        </div>

    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            #printable-ticket { box-shadow: none !important; border: 1px solid #ddd !important; }
            #printable-ticket div[style*="linear-gradient"] { -webkit-print-color-adjust: exact; background: #2B4865 !important; }
        }
    </style>

</div>
