<?php
/**
 * APPOLIOS MailService
 * Sends HTML emails via PHP mail() — works on XAMPP with sendmail configured.
 * For production, swap the send() method body with PHPMailer/SMTP.
 */

class MailService
{
    /** From address shown to recipients */
    private static string $fromEmail = MAIL_FROM_EMAIL;
    private static string $fromName = MAIL_FROM_NAME;

    // ─────────────────────────────────────────────
    //  PUBLIC API
    // ─────────────────────────────────────────────

    /**
     * Send "Application Approved" email to a teacher.
     */
    public static function sendTeacherApproved(string $toEmail, string $toName, string $adminNotes = ''): bool
    {
        $subject = '🎉 Your Teacher Application Has Been Approved — ' . APP_NAME;

        $notesBlock = '';
        if (!empty(trim($adminNotes))) {
            $notesBlock = '
            <div style="margin:24px 0;padding:16px 20px;background:#f0fdf4;border-left:4px solid #22c55e;border-radius:8px;">
                <p style="margin:0;font-size:14px;color:#166534;font-weight:600;">Message from Admin:</p>
                <p style="margin:6px 0 0;font-size:14px;color:#166534;">' . htmlspecialchars($adminNotes) . '</p>
            </div>';
        }

        $body = self::wrapTemplate(
            toName: $toName,
            accentColor: '#22c55e',
            iconEmoji: '🎓',
            headingText: 'Your Application Was Approved!',
            bodyHtml: '
            <p style="color:#374151;font-size:15px;line-height:1.7;">
                Congratulations, <strong>' . htmlspecialchars($toName) . '</strong>! We are thrilled to inform you that
                your teacher application to <strong>' . APP_NAME . '</strong> has been <span style="color:#16a34a;font-weight:700;">approved</span>.
            </p>
            ' . $notesBlock . '
            <p style="color:#374151;font-size:15px;line-height:1.7;">
                You can now log in using your registered email and password. Your account is fully active and you have
                access to all teacher features on the platform.
            </p>
            <div style="text-align:center;margin:28px 0;">
                <a href="' . APP_ENTRY . '?url=login"
                   style="display:inline-block;padding:13px 32px;background:linear-gradient(135deg,#2B4865,#548CA8);
                          color:#fff;text-decoration:none;border-radius:10px;font-weight:700;font-size:15px;
                          letter-spacing:.3px;box-shadow:0 4px 14px rgba(43,72,101,0.35);">
                    Login to APPOLIOS →
                </a>
            </div>
            <p style="color:#6b7280;font-size:13px;line-height:1.6;">
                If you registered a Face ID during sign-up, you can also use it to log in instantly.
            </p>'
        );

        return self::send($toEmail, $toName, $subject, $body);
    }

    /**
     * Send "Application Rejected" email to a teacher.
     */
    public static function sendTeacherRejected(string $toEmail, string $toName, string $reason = ''): bool
    {
        $subject = 'Update on Your Teacher Application — ' . APP_NAME;

        $reasonBlock = '';
        if (!empty(trim($reason))) {
            $reasonBlock = '
            <div style="margin:24px 0;padding:16px 20px;background:#fff7ed;border-left:4px solid #f97316;border-radius:8px;">
                <p style="margin:0;font-size:14px;color:#9a3412;font-weight:600;">Reason provided:</p>
                <p style="margin:6px 0 0;font-size:14px;color:#9a3412;">' . htmlspecialchars($reason) . '</p>
            </div>';
        }

        $body = self::wrapTemplate(
            toName: $toName,
            accentColor: '#f97316',
            iconEmoji: '📋',
            headingText: 'Application Not Approved',
            bodyHtml: '
            <p style="color:#374151;font-size:15px;line-height:1.7;">
                Dear <strong>' . htmlspecialchars($toName) . '</strong>, thank you for your interest in joining
                <strong>' . APP_NAME . '</strong> as a teacher.
            </p>
            <p style="color:#374151;font-size:15px;line-height:1.7;">
                After careful review, we regret to inform you that your application has <span style="color:#dc2626;font-weight:700;">not been approved</span> at this time.
            </p>
            ' . $reasonBlock . '
            <p style="color:#374151;font-size:15px;line-height:1.7;">
                You are welcome to re-apply in the future with an updated CV. If you have any questions, please contact our support team.
            </p>
            <div style="text-align:center;margin:28px 0;">
                <a href="' . APP_ENTRY . '?url=home/index"
                   style="display:inline-block;padding:13px 32px;background:linear-gradient(135deg,#64748b,#94a3b8);
                          color:#fff;text-decoration:none;border-radius:10px;font-weight:700;font-size:15px;
                          letter-spacing:.3px;box-shadow:0 4px 14px rgba(100,116,139,0.35);">
                    Visit APPOLIOS
                </a>
            </div>'
        );

        return self::send($toEmail, $toName, $subject, $body);
    }

    /**
     * Send password reset email with 4-digit verification code.
     */
    public static function sendVerificationCode(string $toEmail, string $toName, string $code): bool
    {
        $subject = 'Your Verification Code — ' . APP_NAME;

        $body = self::wrapTemplate(
            toName: $toName,
            accentColor: '#548CA8',
            iconEmoji: '🔐',
            headingText: 'Verification Code',
            bodyHtml: '
            <p style="color:#374151;font-size:15px;line-height:1.7;">
                You requested a password reset for your <strong>' . htmlspecialchars($toName) . '</strong> account.
            </p>
            <p style="color:#374151;font-size:15px;line-height:1.7;">
                Use the following 4-digit verification code:
            </p>
            <div style="text-align:center;margin:28px 0;">
                <div style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#2B4865,#548CA8);
                          color:#fff;border-radius:10px;font-weight:800;font-size:28px;
                          letter-spacing:8px;box-shadow:0 4px 14px rgba(43,72,101,0.35);">
                    ' . htmlspecialchars($code) . '
                </div>
            </div>
            <p style="color:#6b7280;font-size:13px;line-height:1.6;">
                This code expires in 10 minutes. If you didn\'t request this, you can safely ignore this email.
            </p>'
        );

        return self::send($toEmail, $toName, $subject, $body);
    }

    // ─────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────

    /**
     * Wraps content inside a premium HTML email shell.
     */
    private static function wrapTemplate(
        string $toName,
        string $accentColor,
        string $iconEmoji,
        string $headingText,
        string $bodyHtml
    ): string {
        $year = date('Y');
        $appName = APP_NAME;
        $statusIcon = ($accentColor === '#22c55e') ? '✅' : '❌';

        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>' . htmlspecialchars($headingText) . '</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:\'Segoe UI\',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;">

        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%);border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
            <div style="font-size:36px;margin-bottom:10px;">' . $iconEmoji . '</div>
            <div style="color:#fff;font-size:22px;font-weight:800;letter-spacing:-.3px;">' . htmlspecialchars($headingText) . '</div>
            <div style="color:rgba(255,255,255,.55);font-size:13px;margin-top:6px;">
                ' . $statusIcon . ' Application Status Update &middot; ' . htmlspecialchars($appName) . '
            </div>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="background:#fff;padding:36px 40px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
            ' . $bodyHtml . '
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f8fafc;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 16px 16px;padding:20px 40px;text-align:center;">
            <p style="margin:0;color:#94a3b8;font-size:12px;line-height:1.6;">
              This email was sent automatically by the <strong style="color:#64748b;">' . htmlspecialchars($appName) . '</strong> platform.<br>
              &copy; ' . $year . ' APPOLIOS. All rights reserved.
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';
    }

    /**
     * Core send method — uses PHP mail().
     * Replace with PHPMailer/SMTP calls for production.
     */
    private static function send(string $toEmail, string $toName, string $subject, string $body): bool
    {
        $fromEmail = self::$fromEmail;
        $fromName = self::$fromName;

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromEmail}\r\n";
        $headers .= "X-Mailer: APPOLIOS-MailService/1.0\r\n";

        $encodedSubject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        $to = "=?UTF-8?B?" . base64_encode($toName) . "?= <{$toEmail}>";

        return @mail($to, $encodedSubject, $body, $headers);
    }
}
