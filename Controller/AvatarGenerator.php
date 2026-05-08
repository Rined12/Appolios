<?php

require_once __DIR__ . '/../config/database.php';

class AvatarGenerator
{
    private $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    private function sanitizeHex(string $hex, string $default): string
    {
        $hex = ltrim($hex, '#');
        if (!preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
            return '#' . $default;
        }
        return '#' . strtolower($hex);
    }

    public function generateAvatar(array $faceData, int $userId): array
    {
        try {
            $skinTone  = $this->sanitizeHex($faceData['skinTone']  ?? 'A8663A', 'A8663A');
            $hairColor = $this->sanitizeHex($faceData['hairColor'] ?? '1A0C04', '1A0C04');
            $eyeColor  = $this->sanitizeHex($faceData['eyeColor']  ?? '3D2410', '3D2410');
            $faceShape = $faceData['faceShape'] ?? 'oval';
            $gender    = $faceData['gender']    ?? 'male';
            $age       = (float)($faceData['age'] ?? 25);

            $svg       = $this->createEmojiAvatar($skinTone, $hairColor, $eyeColor, $gender, $age);
            $imageData = $svg;
            $extension = 'svg';

            $avatarId = $this->saveAvatar($userId, $imageData, $faceData);

            // Path updated to account for being in Controller folder
            $uploadDir = __DIR__ . '/../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;

            if ($imageData && file_put_contents($filepath, $imageData) !== false) {
                $this->updateUserAvatar($userId, $filename);
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

    /**
     * Selects the best matching emoji and wraps it in a clean SVG bubble.
     */
    private function createEmojiAvatar(
        string $skinTone,
        string $hairColor,
        string $eyeColor,
        string $gender = 'male',
        float  $age    = 25
    ): string {

        $emoji = $this->selectEmoji($skinTone, $hairColor, $gender, $age);

        // Background gradient colour based on gender
        $bgFrom = ($gender === 'female') ? '#f9a8d4' : '#93c5fd';
        $bgTo   = ($gender === 'female') ? '#ec4899' : '#3b82f6';

        $svg  = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">';
        $svg .= '<defs>';
        $svg .= '<radialGradient id="bgGrad" cx="50%" cy="40%" r="60%">'
              . '<stop offset="0%"   stop-color="' . $bgFrom . '"/>'
              . '<stop offset="100%" stop-color="' . $bgTo   . '"/>'
              . '</radialGradient>';
        $svg .= '</defs>';

        // Circle background
        $svg .= '<circle cx="100" cy="100" r="100" fill="url(#bgGrad)"/>';

        // Emoji rendered as text (browsers render emoji fonts perfectly in SVG)
        $svg .= '<text x="100" y="130" font-size="110" text-anchor="middle" dominant-baseline="middle">'
              . $emoji
              . '</text>';

        $svg .= '</svg>';

        return $svg;
    }

    /**
     * Picks the best emoji from detected characteristics.
     */
    private function selectEmoji(string $skinTone, string $hairColor, string $gender, float $age): string
    {
        // ── Hair analysis ──────────────────────────────────────────────────
        [$rH, $gH, $bH] = sscanf(ltrim($hairColor, '#'), '%02x%02x%02x');
        $hairLuminance   = 0.299 * $rH + 0.587 * $gH + 0.114 * $bH;   // 0-255
        $isRedHair       = ($rH > 140 && $rH > $gH * 1.4 && $rH > $bH * 1.6);
        $isBlonde        = ($hairLuminance > 160);
        $isWhiteOrGray   = ($hairLuminance > 200 && abs($rH - $gH) < 15 && abs($gH - $bH) < 15);
        $isBald          = ($hairLuminance > 220);

        // ── Age groups ─────────────────────────────────────────────────────
        if ($age < 10) {
            return ($gender === 'female') ? '👧' : '👦';
        }
        if ($age < 18) {
            return ($gender === 'female') ? '👩' : '👦';
        }
        if ($age > 65) {
            return ($gender === 'female') ? '👵' : '👴';
        }
        if ($age > 50) {
            return ($gender === 'female') ? '👩🦳' : '👨🦳';
        }

        // ── Adults ─────────────────────────────────────────────────────────
        if ($gender === 'female') {
            if ($isBald)        return '👩🦲';
            if ($isWhiteOrGray) return '👩🦳';
            if ($isRedHair)     return '👩🦰';
            if ($isBlonde)      return '👱♀️';
            return '👩';
        }

        // Male
        if ($isBald)        return '👨🦲';
        if ($isWhiteOrGray) return '👨🦳';
        if ($isRedHair)     return '👨🦰';
        if ($isBlonde)      return '👱♂️';
        return '🧔';   // default adult male → bearded
    }

    private function saveAvatar(int $userId, ?string $imageData, array $faceData): int
    {
        $config = json_encode($faceData);
        $stmt   = $this->db->prepare(
            "INSERT INTO avatars (user_id, name, avatar_config, avatar_image, is_active)
             VALUES (:user_id, :name, :config, :image, 1)"
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':name'    => 'Emoji Avatar',
            ':config'  => $config,
            ':image'   => $imageData,
        ]);
        return (int) $this->db->lastInsertId();
    }

    private function updateUserAvatar(int $userId, string $filename): void
    {
        $stmt = $this->db->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
        $stmt->execute([':avatar' => $filename, ':id' => $userId]);
    }
}
