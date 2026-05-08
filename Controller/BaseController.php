<?php

require_once __DIR__ . '/../config/database.php';

abstract class BaseController
{
    protected function getDb()
    {
        return getConnection();
    }

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
        
        // Determine layout based on view path or user role
        $isAdmin = $this->isAdmin();
        $isAdminView = str_contains($view, 'BackOffice/admin');
        $isProfileView = ($view === 'FrontOffice/student/profile');

        if ($isAdminView || ($isAdmin && $isProfileView)) {
            $headerFile = __DIR__ . '/../View/BackOffice/admin/partials/admin_header.php';
            $footerFile = __DIR__ . '/../View/BackOffice/admin/partials/admin_footer.php';
            
            // Set active sidebar item for profile if needed
            if ($isProfileView) {
                $data['adminSidebarActive'] = 'profile';
            }
        } else {
            $headerFile = __DIR__ . '/../View/layouts/header.php';
            $footerFile = __DIR__ . '/../View/layouts/footer.php';
        }

        if (!file_exists($viewFile)) {
            throw new Exception("View '{$view}' not found");
        }

        if (!file_exists($headerFile) || !file_exists($footerFile)) {
            throw new Exception('Layout files are missing.');
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

    /**
     * Generate a random temporary password
     * @param int $length
     * @return string
     */
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

    // ==========================================
    // AVATAR GENERATOR  (from AvatarGenerator.php)
    // ==========================================

    /**
     * Generate an emoji-based SVG avatar from face-detection data,
     * save it to disk, and update the user's avatar column.
     *
     * @param array $faceData  Associative array from face-detection JS
     * @param int   $userId    Target user id
     * @return array           ['success' => bool, ...]
     */
    protected function buildAvatarFromFace(array $faceData, int $userId): array
    {
        try {
            $skinTone  = $this->sanitizeHexColor($faceData['skinTone']  ?? 'A8663A', 'A8663A');
            $hairColor = $this->sanitizeHexColor($faceData['hairColor'] ?? '1A0C04', '1A0C04');
            $eyeColor  = $this->sanitizeHexColor($faceData['eyeColor']  ?? '3D2410', '3D2410');
            $gender    = $faceData['gender'] ?? 'male';
            $age       = (float)($faceData['age'] ?? 25);

            $svg      = $this->buildEmojiAvatar($skinTone, $hairColor, $eyeColor, $gender, $age);
            $avatarId = $this->persistAvatarRecord($userId, $svg, $faceData);

            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'avatar_' . $userId . '_' . time() . '.svg';

            if (file_put_contents($uploadDir . $filename, $svg) !== false) {
                $this->setUserAvatarFilename($userId, $filename);
                return [
                    'success'   => true,
                    'avatar_id' => $avatarId,
                    'filename'  => $filename,
                    'url'       => APP_URL . '/uploads/avatars/' . $filename,
                ];
            }

            return ['success' => false, 'error' => 'Failed to save avatar image'];

        } catch (Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /** Build an SVG bubble containing the best-matching emoji. */
    private function buildEmojiAvatar(
        string $skinTone,
        string $hairColor,
        string $eyeColor,
        string $gender = 'male',
        float  $age    = 25
    ): string {
        $emoji  = $this->pickEmoji($skinTone, $hairColor, $gender, $age);
        $bgFrom = ($gender === 'female') ? '#f9a8d4' : '#93c5fd';
        $bgTo   = ($gender === 'female') ? '#ec4899' : '#3b82f6';

        $svg  = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">';
        $svg .= '<defs><radialGradient id="bgGrad" cx="50%" cy="40%" r="60%">'
              . '<stop offset="0%"   stop-color="' . $bgFrom . '"/>'
              . '<stop offset="100%" stop-color="' . $bgTo   . '"/>'
              . '</radialGradient></defs>';
        $svg .= '<circle cx="100" cy="100" r="100" fill="url(#bgGrad)"/>';
        $svg .= '<text x="100" y="130" font-size="110" text-anchor="middle" dominant-baseline="middle">'
              . $emoji . '</text>';
        $svg .= '</svg>';

        return $svg;
    }

    /** Pick the best emoji from detected face characteristics. */
    private function pickEmoji(string $skinTone, string $hairColor, string $gender, float $age): string
    {
        [$rH, $gH, $bH] = sscanf(ltrim($hairColor, '#'), '%02x%02x%02x');
        $lum           = 0.299 * $rH + 0.587 * $gH + 0.114 * $bH;
        $isRedHair     = ($rH > 140 && $rH > $gH * 1.4 && $rH > $bH * 1.6);
        $isBlonde      = ($lum > 160);
        $isWhiteOrGray = ($lum > 200 && abs($rH - $gH) < 15 && abs($gH - $bH) < 15);
        $isBald        = ($lum > 220);

        if ($age < 10)  return ($gender === 'female') ? '👧' : '👦';
        if ($age < 18)  return ($gender === 'female') ? '👩' : '👦';
        if ($age > 65)  return ($gender === 'female') ? '👵' : '👴';
        if ($age > 50)  return ($gender === 'female') ? '👩‍🦳' : '👨‍🦳';

        if ($gender === 'female') {
            if ($isBald)        return '👩‍🦲';
            if ($isWhiteOrGray) return '👩‍🦳';
            if ($isRedHair)     return '👩‍🦰';
            if ($isBlonde)      return '👱‍♀️';
            return '👩';
        }
        if ($isBald)        return '👨‍🦲';
        if ($isWhiteOrGray) return '👨‍🦳';
        if ($isRedHair)     return '👨‍🦰';
        if ($isBlonde)      return '👱‍♂️';
        return '🧔';
    }

    /** Persist a new avatar record and return its id. */
    private function persistAvatarRecord(int $userId, ?string $imageData, array $faceData): int
    {
        $stmt = $this->getDb()->prepare(
            "INSERT INTO avatars (user_id, name, avatar_config, avatar_image, is_active)
             VALUES (:user_id, :name, :config, :image, 1)"
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':name'    => 'Emoji Avatar',
            ':config'  => json_encode($faceData),
            ':image'   => $imageData,
        ]);
        return (int) $this->getDb()->lastInsertId();
    }

    /** Update the avatar filename on the users table. */
    private function setUserAvatarFilename(int $userId, string $filename): void
    {
        $stmt = $this->getDb()->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
        $stmt->execute([':avatar' => $filename, ':id' => $userId]);

        // Update session if it is the current user
        if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $userId) {
            $_SESSION['user_avatar'] = $filename;
        }
    }

    /** Validate and normalise a hex colour string. */
    private function sanitizeHexColor(string $hex, string $default): string
    {
        $hex = ltrim($hex, '#');
        return preg_match('/^[0-9A-Fa-f]{6}$/', $hex) ? '#' . strtolower($hex) : '#' . $default;
    }
}

