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

        $svg  = '<?xml version="1.0" encoding="UTF-8"?>';
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200">';
        
        // Emoji rendered as text (browsers render emoji fonts perfectly in SVG)
        $svg .= '<text x="100" y="110" font-size="160" text-anchor="middle" dominant-baseline="middle">'
              . $emoji
              . '</text>';

        $svg .= '</svg>';

        return $svg;
    }

    private function selectEmoji(string $skinTone, string $hairColor, string $gender, float $age): string
    {
        // Hair analysis
        $isBlonde = false;
        if (!empty($hairColor)) {
            [$rH, $gH, $bH] = sscanf(ltrim($hairColor, '#'), '%02x%02x%02x');
            $hairLuminance = 0.299 * ($rH ?? 0) + 0.587 * ($gH ?? 0) + 0.114 * ($bH ?? 0);
            $isBlonde = ($hairLuminance > 160);
        }

        if ($age < 12) {
            return ($gender === 'female') ? '👧' : '👦';
        }
        
        if ($age > 60) {
            return ($gender === 'female') ? '👵' : '👴';
        }

        if ($isBlonde) {
            return '👱'; // Simple Blond Person (No gender symbol sequence)
        }

        return ($gender === 'female') ? '👩' : '👨';
    }

    private function saveAvatar(int $userId, ?string $imageData, array $faceData): int
    {
        $config = json_encode($faceData);
        $stmt   = $this->db->prepare(
            "INSERT INTO avatars (user_id, filename, avatar_data) VALUES (:user_id, :name, :image)"
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':name'    => 'emoji_avatar_' . time() . '.svg',
            ':image'   => $imageData,
        ]);
        return (int) $this->db->lastInsertId();
    }

    private function updateUserAvatar(int $userId, string $filename): void
    {
        // Check if avatar column exists first
        $cols = $this->db->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('avatar', $cols)) {
            $stmt = $this->db->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
            $stmt->execute([':avatar' => $filename, ':id' => $userId]);
        }
    }
    
    public function getAvatarById(int $avatarId): array
    {
        $stmt = $this->db->prepare("SELECT avatar_data, filename FROM avatars WHERE id = ?");
        $stmt->execute([$avatarId]);
        $result = $stmt->fetch();
        
        if ($result && !empty($result['avatar_data'])) {
            return [
                'success' => true,
                'data' => $result['avatar_data'],
                'type' => 'image/svg+xml',
                'filename' => $result['filename']
            ];
        }
        
        return ['success' => false];
    }
}
