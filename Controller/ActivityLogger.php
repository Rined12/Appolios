<?php
/**
 * Trait for logging user activities
 * Use this trait in controllers to automatically log activities
 */

trait ActivityLogger
{
    /**
     * Log an activity directly to database
     * @param string $activityType Type of activity
     * @param string $description Activity description
     * @param int|null $userId Optional user ID (uses session if not provided)
     * @param string|null $userName Optional user name (uses session if not provided)
     * @param string|null $userEmail Optional user email (uses session if not provided)
     * @param string|null $userRole Optional user role (uses session if not provided)
     */
    protected function logActivity(
        string $activityType,
        string $description,
        ?int $userId = null,
        ?string $userName = null,
        ?string $userEmail = null,
        ?string $userRole = null
    ): void {
        $sql = "INSERT INTO activity_log
                (user_id, user_name, user_email, user_role, activity_type, activity_description, ip_address, location, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->getDb()->prepare($sql);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // --- MODE TEST LOCAL ---
            // Si on est en local (::1 ou 127.0.0.1), on utilise une IP publique de test
            // pour voir la géolocalisation fonctionner.
            $queryIp = $ipAddress;
            if ($ipAddress === '127.0.0.1' || $ipAddress === '::1' || $ipAddress === 'unknown') {
                $queryIp = '197.230.150.10'; // Adresse IP de test (Tunisie)
            }
            
            // Fetch location from ip-api.com
            $location = $this->fetchIpLocation($queryIp);

            // Use provided values or fall back to session
            $userId = $userId ?? $_SESSION['user_id'] ?? null;
            $userName = $userName ?? $_SESSION['user_name'] ?? null;
            $userEmail = $userEmail ?? $_SESSION['user_email'] ?? null;
            $userRole = $userRole ?? $_SESSION['role'] ?? null;

            $stmt->execute([
                $userId,
                $userName,
                $userEmail,
                $userRole,
                $activityType,
                $description,
                $ipAddress,
                $location,
                $userAgent
            ]);
        } catch (PDOException $e) {
            error_log("ActivityLog error: " . $e->getMessage());
        }
    }

    /**
     * Fetch location details from IP address using ip-api.com
     */
    private function fetchIpLocation(string $ip): string
    {
        // Skip local addresses
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'unknown') {
            return "Local / Unknown";
        }

        try {
            $ch = curl_init("http://ip-api.com/json/{$ip}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Short timeout to avoid blocking
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['status']) && $data['status'] === 'success') {
                    return $data['city'] . ', ' . $data['country'];
                }
            }
        } catch (Exception $e) {
            // Silently fail to not interrupt the main flow
        }

        return "Location not found";
    }

    /**
     * Log the differences between old and new data
     */
    protected function logDiff(string $activityType, array $oldData, array $newData, string $descriptionPrefix = ""): void
    {
        $changes = [];
        $sensitiveFields = ['password', 'pwd', 'token', 'secret', 'face_descriptor'];

        foreach ($newData as $key => $value) {
            if (array_key_exists($key, $oldData)) {
                // Handle different types (string/int) comparison
                if ($oldData[$key] != $value) {
                    if (in_array($key, $sensitiveFields)) {
                        $changes[] = "[$key changed]";
                    } else {
                        $oldVal = $oldData[$key] ?: 'empty';
                        $newVal = $value ?: 'empty';
                        $changes[] = "<b>$key</b>: <span style='color: #ef4444;'>'$oldVal'</span> → <span style='color: #10b981;'>'$newVal'</span>";
                    }
                }
            }
        }

        if (!empty($changes)) {
            $description = $descriptionPrefix . " " . implode(" | ", $changes);
            $this->logActivity($activityType, $description);
        }
    }

    /**
     * Log login activity
     */
    protected function logLogin(): void
    {
        $this->logActivity('login', 'User logged in successfully');
    }

    /**
     * Log logout activity
     */
    protected function logLogout(): void
    {
        $this->logActivity('logout', 'User logged out');
    }

    /**
     * Log registration activity
     */
    protected function logRegister(string $userEmail): void
    {
        $this->logActivity('register', "New user registered: {$userEmail}");
    }

    /**
     * Log password reset request
     */
    protected function logPasswordResetRequest(string $userEmail): void
    {
        $this->logActivity('reset_password', "Password reset requested for: {$userEmail}");
    }

    /**
     * Log password reset completion
     */
    protected function logPasswordResetComplete(string $userEmail): void
    {
        $this->logActivity('reset_password', "Password reset completed for: {$userEmail}");
    }
}
