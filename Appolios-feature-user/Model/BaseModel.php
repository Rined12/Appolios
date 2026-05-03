<?php

require_once __DIR__ . '/../config/database.php';

abstract class BaseModel
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function getDb(): PDO
    {
        return $this->db;
    }

    public function setDb(PDO $db): void
    {
        $this->db = $db;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }
    
    public function __call($method, $args) {
        $className = get_class($this);
        $modelName = basename($className, '.php');
        $controllerName = $modelName . 'Controller';
        $controllerPath = __DIR__ . '/../Controller/' . $controllerName . '.php';
        
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $method)) {
                    return call_user_func_array([$controller, $method], $args);
                }
            }
        }
        
        throw new Exception("Method {$method} does not exist in " . get_class($this));
    }
}
