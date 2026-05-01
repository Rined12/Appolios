<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Teachers Export - APPOLIOS</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
        .badge-teacher { background: #548CA8; }
        .badge-blocked { background: #dc3545; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>APPOLIOS - Teachers Report</h1>
        <p>Complete list of registered teachers</p>
    </div>

    <div class="info">
        <strong>Generated:</strong> <?= htmlspecialchars((string) $generatedAt, ENT_QUOTES, 'UTF-8') ?><br>
        <strong>Total Teachers:</strong> <?= (int) $totalTeachers ?>
    </div>

    <div class="no-print" style="margin-bottom: 20px;">
        <button type="button" onclick="window.print()" style="padding: 10px 20px; background: #548CA8; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            Print / Save as PDF
        </button>
        <a href="<?= htmlspecialchars((string) $backUrl, ENT_QUOTES, 'UTF-8') ?>" style="display: inline-block; padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px; text-decoration: none;">
            Back to Teachers
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 25%;">Full Name</th>
                <th style="width: 30%;">Email Address</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 22%;">Registered Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teacherRows as $teacher): ?>
            <tr>
                <td><?= htmlspecialchars((string) ($teacher['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($teacher['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($teacher['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="badge badge-teacher">Teacher</span>
                    <?php if (!empty($teacher['is_blocked'])): ?>
                        <span class="badge badge-blocked" style="margin-left: 5px;">Blocked</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars((string) ($teacher['registered_display'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>APPOLIOS E-Learning Platform - Teachers Management Report</p>
        <p>This document is confidential and intended for authorized personnel only.</p>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
