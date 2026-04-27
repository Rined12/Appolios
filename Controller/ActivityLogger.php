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
                (user_id, user_name, user_email, user_role, activity_type, activity_description, ip_address, user_agent, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        try {
            $stmt = $this->getDb()->prepare($sql);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

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
                $userAgent
            ]);
        } catch (PDOException $e) {
            error_log("ActivityLog error: " . $e->getMessage());
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
