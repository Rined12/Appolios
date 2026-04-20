<?php

function runSchema(PDO $pdo, string $schemaPath): void
{
    if (!is_readable($schemaPath)) {
        throw new RuntimeException('Schema file not found: ' . $schemaPath);
    }

    $sql = file_get_contents($schemaPath);
    if ($sql === false) {
        throw new RuntimeException('Unable to read schema file: ' . $schemaPath);
    }

    $statements = preg_split('/;\s*(?:\r?\n|$)/', $sql);
    if ($statements === false) {
        throw new RuntimeException('Unable to parse schema SQL statements.');
    }

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement === '') {
            continue;
        }

        $pdo->exec($statement);
    }
}

function getConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
        $port = defined('DB_PORT') ? (int) DB_PORT : 3306;
        $dbName = defined('DB_NAME') ? DB_NAME : 'appolios_db';
        $username = defined('DB_USER') ? DB_USER : 'root';
        $password = defined('DB_PASS') ? DB_PASS : '';
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $exception) {
            if (strpos($exception->getMessage(), '[1049]') === false) {
                throw $exception;
            }

            $bootstrapDsn = "mysql:host={$host};port={$port};charset={$charset}";
            $bootstrapPdo = new PDO($bootstrapDsn, $username, $password, $options);
            $bootstrapPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$charset} COLLATE utf8mb4_unicode_ci");
            $bootstrapPdo->exec("USE `{$dbName}`");
            runSchema($bootstrapPdo, __DIR__ . '/../database/schema.sql');

            $pdo = new PDO($dsn, $username, $password, $options);
        }
    }

    return $pdo;
}

?>
