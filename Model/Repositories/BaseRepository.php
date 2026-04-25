<?php

declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';

abstract class BaseRepository
{
    protected PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? getConnection();
    }

    public function getDb(): PDO
    {
        return $this->db;
    }
}
