<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Ticket - <?= htmlspecialchars((string) ($ticket['event_title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; padding: 40px; display: flex; justify-content: center; min-height: 100vh; align-items: center; }
        .ticket-container { background: white; width: 700px; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.1); display: flex; position: relative; }
        .ticket-left { flex: 1; padding: 40px; border-right: 2px dashed #e2e8f0; }
        .ticket-right { width: 220px; background: #2B4865; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center; }
        .brand { color: #548CA8; font-weight: 800; font-size: 1.2rem; margin-bottom: 30px; display: block; }
        .event-badge { background: #e0f2fe; color: #0369a1; padding: 6px 14px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; margin-bottom: 15px; display: inline-block; }
        h1 { font-size: 2rem; color: #1e293b; line-height: 1.2; margin-bottom: 25px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
        .info-item label { display: block; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 5px; }
        .info-item span { display: block; font-size: 1rem; color: #334155; font-weight: 600; }
        .qr-box { width: 140px; height: 140px; background: white; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 6px solid #355C7D; }
        .qr-box img { width: 100%; height: 100%; object-fit: contain; }
        .status-approved { color: #10b981; font-weight: 800; font-size: 1.2rem; transform: rotate(-15deg); border: 3px solid #10b981; padding: 5px 15px; border-radius: 8px; margin-top: 20px; text-transform: uppercase; }
        .ticket-id { font-size: 0.6rem; color: rgba(255,255,255,0.5); margin-top: auto; font-family: monospace; }
        .ticket-container::before, .ticket-container::after { content: ''; position: absolute; width: 30px; height: 30px; background: #f1f5f9; border-radius: 50%; left: 465px; }
        .ticket-container::before { top: -15px; }
        .ticket-container::after { bottom: -15px; }
        @media print { body { background: white; padding: 0; } .ticket-container { box-shadow: none; border: 1px solid #e2e8f0; margin: 0 auto; } .no-print { display: none; } }
        .no-print-btn { position: fixed; top: 20px; right: 20px; background: #2B4865; color: white; border: none; padding: 12px 24px; border-radius: 12px; cursor: pointer; font-weight: 700; box-shadow: 0 10px 20px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 10px; z-index: 100; transition: all 0.2s; }
        .no-print-btn:hover { background: #548CA8; transform: translateY(-2px); }
    </style>
</head>
<body>
    <button class="no-print-btn no-print" type="button" onclick="window.print()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        Print / Download PDF
    </button>
    <div class="ticket-container">
        <div class="ticket-left">
            <span class="brand">APPOLIOS</span>
            <div class="event-badge">Official Event Pass</div>
            <h1><?= htmlspecialchars((string) ($ticket['event_title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="info-grid">
                <div class="info-item" style="grid-column: 1 / -1;"><label>Event</label><span><?= htmlspecialchars((string) ($ticket['event_title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></div>
                <div class="info-item"><label>Attendee</label><span><?= htmlspecialchars((string) ($ticket['student_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></div>
                <div class="info-item"><label>Date &amp; Time</label><span><?= htmlspecialchars((string) $eventDateDisplay, ENT_QUOTES, 'UTF-8') ?></span></div>
                <div class="info-item"><label>Location</label><span><?= htmlspecialchars((string) $locationDisplay, ENT_QUOTES, 'UTF-8') ?></span></div>
                <div class="info-item"><label>Ticket Type</label><span>Student Pass</span></div>
            </div>
        </div>
        <div class="ticket-right">
            <div class="qr-box">
                <img src="<?= htmlspecialchars((string) $qrUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Ticket QR Code">
            </div>
            <div style="font-size: 0.65rem; font-weight: 700; letter-spacing: 1px; color: #94a3b8; margin-top: -10px;">SCAN TO VALIDATE</div>
            <div class="status-approved">Approved</div>
            <div class="ticket-id">#ID-<?= htmlspecialchars((string) $ticketIdPadded, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
    </div>
    <script>window.onload = function() { setTimeout(function() { window.print(); }, 800); };</script>
</body>
</html>
