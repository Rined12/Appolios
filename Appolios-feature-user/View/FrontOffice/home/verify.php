<?php
/**
 * APPOLIOS - Certificate Verification Page (Public)
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Verify Certificate') ?></title>
    <link rel="stylesheet" href="<?= APP_ENTRY ?>/assets/css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .verify-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 3rem;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .logo p {
            color: #666;
            font-size: 0.9rem;
        }
        .verify-form {
            margin-bottom: 2rem;
        }
        .verify-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .verify-form input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .verify-form input:focus {
            outline: none;
            border-color: #667eea;
        }
        .verify-form button {
            width: 100%;
            margin-top: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .verify-form button:hover {
            transform: translateY(-2px);
        }
        .result {
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
        }
        .result.success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #10b981;
        }
        .result.error {
            background: #fee2e2;
            border: 2px solid #ef4444;
        }
        .result-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .result h2 {
            color: #065f46;
            margin-bottom: 1rem;
        }
        .result.error h2 {
            color: #991b1b;
        }
        .cert-details {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 1rem;
            text-align: left;
        }
        .cert-details p {
            margin: 0.5rem 0;
            color: #333;
        }
        .cert-details strong {
            color: #667eea;
        }
        .cert-code {
            font-family: monospace;
            background: #f3f4f6;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            color: #667eea;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="logo">
            <h1>🎓 APPOLIOS</h1>
            <p>Certificate Verification System</p>
        </div>

        <?php if (!$searched): ?>
            <form class="verify-form" method="POST">
                <label for="certificate_code">Enter Certificate Code</label>
                <input type="text" id="certificate_code" name="certificate_code" 
                       placeholder="e.g., APP-20260505-D0595C48" required>
                <button type="submit">Verify Certificate</button>
            </form>
            <p style="text-align: center; color: #666; font-size: 0.85rem;">
                Find your certificate code in your dashboard under "My Certificates"
            </p>
        <?php elseif ($error): ?>
            <div class="result error">
                <div class="result-icon">❌</div>
                <h2>Certificate Not Found</h2>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
            <a href="<?= APP_ENTRY ?>?url=home/verify" class="back-link">Try Again</a>
        <?php elseif ($certificate): ?>
            <div class="result success">
                <div class="result-icon">✅</div>
                <h2>Certificate Verified!</h2>
                <p>This certificate is valid and authentic.</p>
                
                <div class="cert-details">
                    <p><strong>Student:</strong> <?= htmlspecialchars($certificate['student_name']) ?></p>
                    <p><strong>Course:</strong> <?= htmlspecialchars($certificate['course_title']) ?></p>
                    <p><strong>Date Issued:</strong> <?= date('F d, Y', strtotime($certificate['issued_at'])) ?></p>
                    <p><strong>Certificate ID:</strong></p>
                    <div class="cert-code"><?= htmlspecialchars($certificate['certificate_code']) ?></div>
                </div>
            </div>
            <a href="<?= APP_ENTRY ?>?url=home/verify" class="back-link">Verify Another Certificate</a>
        <?php endif; ?>
    </div>
</body>
</html>