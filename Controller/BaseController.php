<?php

abstract class BaseController
{
    public function model(string $model)
    {
        $modelFile = __DIR__ . '/../Model/' . $model . '.php';

        if (!file_exists($modelFile)) {
            throw new Exception("Model '{$model}' not found");
        }

        require_once $modelFile;
        return new $model();
    }

    public function view(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../View/' . $view . '.php';
        $headerFile = __DIR__ . '/../View/layouts/header.php';
        $footerFile = __DIR__ . '/../View/layouts/footer.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View '{$view}' not found");
        }

        if (!file_exists($headerFile) || !file_exists($footerFile)) {
            throw new Exception('Layout files are missing in View/layouts.');
        }

        if (!isset($data['errors'])) {
            $data['errors'] = $this->getErrors();
        }

        extract($data);
        require $headerFile;
        require $viewFile;
        require $footerFile;
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id'], $_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    protected function isAdmin(): bool
    {
        return $this->isLoggedIn() && (($_SESSION['role'] ?? '') === 'admin');
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . APP_ENTRY . '?url=' . ltrim($url, '/'));
        exit();
    }

    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return htmlspecialchars(strip_tags(trim((string) $data)), ENT_QUOTES, 'UTF-8');
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    protected function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }

    protected function setErrors(array $errors): void
    {
        $_SESSION['form_errors'] = $errors;
    }

    protected function getErrors(): array
    {
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);
        return $errors;
    }

    protected function generateTempPassword(int $length = 10): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }

    /**
     * Send email via Gmail SMTP or fallback to mail()
     */
    protected function sendEmail(string $to, string $subject, string $message): bool
    {
        $envPath = __DIR__ . '/../.env';
        $gmailUser = '';
        $gmailPass = '';
        
        if (file_exists($envPath)) {
            $envVars = parse_ini_file($envPath);
            $gmailUser = $envVars['GMAIL_EMAIL'] ?? '';
            $gmailPass = $envVars['GMAIL_APP_PASSWORD'] ?? '';
        }

        if (!empty($gmailUser) && !empty($gmailPass)) {
            try {
                $socket = fsockopen("ssl://smtp.gmail.com", 465, $errno, $errstr, 15);
                if ($socket) {
                    // Helper to read multiline responses
                    $getServerResponse = function() use ($socket) {
                        $data = "";
                        while ($str = fgets($socket, 515)) {
                            $data .= $str;
                            if (substr($str, 3, 1) == " ") { break; }
                        }
                        return $data;
                    };

                    $getServerResponse(); // Read welcome message
                    
                    fputs($socket, "EHLO localhost\r\n");
                    $getServerResponse();
                    
                    fputs($socket, "AUTH LOGIN\r\n");
                    $getServerResponse();
                    
                    fputs($socket, base64_encode($gmailUser) . "\r\n");
                    $getServerResponse();
                    
                    fputs($socket, base64_encode($gmailPass) . "\r\n");
                    $getServerResponse();
                    
                    fputs($socket, "MAIL FROM: <$gmailUser>\r\n");
                    $getServerResponse();
                    
                    fputs($socket, "RCPT TO: <$to>\r\n");
                    $getServerResponse();
                    
                    fputs($socket, "DATA\r\n");
                    $getServerResponse();
                    
                    $headers = "From: APPOLIOS Events <$gmailUser>\r\n";
                    $headers .= "To: $to\r\n";
                    $headers .= "Subject: $subject\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    
                    fputs($socket, $headers . "\r\n" . $message . "\r\n.\r\n");
                    $getServerResponse();
                    
                    fputs($socket, "QUIT\r\n");
                    fclose($socket);
                    return true;
                }
            } catch (Exception $e) {
                // Ignore and fallback
            }
        }

        // Fallback
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: APPOLIOS Events <no-reply@appolios.com>\r\n";
        return @mail($to, $subject, $message, $headers);
    }
}
