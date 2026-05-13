<?php
/**
 * Simple PDF Certificate Generator
 * Opens a printable certificate page for browser print/save as PDF
 */

class PDFCertificateGenerator {
    
    public function generate($certificate) {
        $studentName = htmlspecialchars($certificate['student_name'] ?? 'Student');
        $courseName = htmlspecialchars($certificate['course_title'] ?? 'Course');
        $certificateCode = htmlspecialchars($certificate['certificate_code'] ?? '');
        $issuedAt = date('F d, Y', strtotime($certificate['issued_at'] ?? time()));
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Certificate - <?= $certificateCode ?></title>
            <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Great+Vibes&display=swap" rel="stylesheet">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Georgia', serif; 
                    background: #f0f0f0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    padding: 20px;
                }
                .certificate {
                    width: 800px;
                    background: linear-gradient(135deg, #fff 0%, #fafafa 100%);
                    border: 8px double #1a1a2e;
                    border-radius: 4px;
                    padding: 50px 40px;
                    text-align: center;
                    position: relative;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                }
                .corner {
                    position: absolute;
                    font-size: 40px;
                    color: #1a1a2e;
                }
                .corner-tl { top: 15px; left: 15px; }
                .corner-tr { top: 15px; right: 15px; transform: rotate(90deg); }
                .corner-bl { bottom: 15px; left: 15px; transform: rotate(270deg); }
                .corner-br { bottom: 15px; right: 15px; transform: rotate(180deg); }
                .platform { color: #666; font-size: 14px; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 10px; }
                .cert-title { color: #1a1a2e; font-size: 42px; font-weight: 700; margin-bottom: 5px; font-family: 'Playfair Display', serif; }
                .cert-subtitle { color: #8b5cf6; font-size: 18px; letter-spacing: 4px; text-transform: uppercase; font-weight: 600; }
                .divider { width: 60%; height: 2px; background: linear-gradient(90deg, transparent, #1a1a2e, transparent); margin: 30px auto; }
                .certify-text { color: #666; font-size: 16px; font-style: italic; margin-bottom: 10px; }
                .student-name { color: #1a1a2e; font-size: 36px; border-bottom: 3px solid #eab308; padding-bottom: 10px; margin: 10px auto; display: inline-block; font-family: 'Great Vibes', cursive; }
                .completed-text { color: #666; font-size: 16px; margin: 20px 0 10px; font-style: italic; }
                .course-name { color: #8b5cf6; font-size: 24px; font-weight: 700; margin: 10px 0; }
                .small-divider { width: 60%; height: 1px; background: #ddd; margin: 30px auto; }
                .footer-area { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
                .footer-item { text-align: center; }
                .footer-line { width: 120px; border-bottom: 2px solid #1a1a2e; margin-bottom: 5px; }
                .footer-label { color: #666; font-size: 12px; }
                .footer-value { color: #1a1a2e; font-weight: 600; }
                .footer-value.mono { font-family: monospace; font-size: 11px; }
                .trophy { font-size: 50px; color: #eab308; }
                .print-btn {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    background: #8b5cf6;
                    color: white;
                    border: none;
                    padding: 15px 30px;
                    border-radius: 8px;
                    font-size: 16px;
                    cursor: pointer;
                    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
                }
                .print-btn:hover { background: #7c3aed; }
                @media print {
                    body { background: white; padding: 0; }
                    .print-btn { display: none; }
                    .certificate { box-shadow: none; border: 4px double #1a1a2e; }
                }
            </style>
        </head>
        <body>
            <div class="certificate">
                <span class="corner corner-tl">❧</span>
                <span class="corner corner-tr">❧</span>
                <span class="corner corner-bl">❧</span>
                <span class="corner corner-br">❧</span>
                
                <div class="platform">APPOLIOS Learning Platform</div>
                <h1 class="cert-title">CERTIFICATE</h1>
                <div class="cert-subtitle">of Completion</div>
                
                <div class="divider"></div>
                
                <p class="certify-text">This is to certify that</p>
                <h2 class="student-name"><?= $studentName ?></h2>
                <p class="completed-text">has successfully completed the course</p>
                <h3 class="course-name"><?= $courseName ?></h3>
                
                <div class="small-divider"></div>
                
                <div class="footer-area">
                    <div class="footer-item">
                        <div class="footer-line"></div>
                        <div class="footer-label">Date of Issue</div>
                        <div class="footer-value"><?= $issuedAt ?></div>
                    </div>
                    <div class="trophy">🏆</div>
                    <div class="footer-item">
                        <div class="footer-line"></div>
                        <div class="footer-label">Certificate ID</div>
                        <div class="footer-value mono"><?= $certificateCode ?></div>
                    </div>
                </div>
            </div>
            
            <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
        </body>
        </html>
        <?php
    }
}
