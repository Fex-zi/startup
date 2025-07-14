<?php

namespace Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static $instance = null;
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $options;

    private function __construct()
    {
        $config = require_once __DIR__ . '/../../config/database.php';
        
        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->database = $config['database'];
        $this->options = $config['options'];
        
        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage() . " SQL: " . $sql);
        }
    }

    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }

    public function update($sql, $params = [])
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function delete($sql, $params = [])
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollback()
    {
        return $this->connection->rollback();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function tableExists($tableName)
    {
        try {
            // Method 1: Use SHOW TABLES with prepared statement
            $sql = "SHOW TABLES LIKE ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$tableName]);
            $result = $stmt->fetch();
            
            if ($result !== false) {
                return true;
            }
            
            // Method 2: Fallback - Query information_schema
            $sql = "SELECT COUNT(*) as count FROM information_schema.tables 
                    WHERE table_schema = DATABASE() AND table_name = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$tableName]);
            $result = $stmt->fetch();
            
            return $result && $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Table exists check failed: " . $e->getMessage());
            
            // Method 3: Ultimate fallback - try to describe table
            try {
                $sql = "DESCRIBE `{$tableName}`";
                $this->connection->query($sql);
                return true;
            } catch (PDOException $e2) {
                return false;
            }
        }
    }

    public function createDatabase($databaseName)
    {
        try {
            // Connect without specifying database first
            $dsn = "mysql:host={$this->host};charset=utf8mb4";
            $tempConnection = new PDO($dsn, $this->username, $this->password, $this->options);
            
            $sql = "CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $tempConnection->exec($sql);
            
            // Now reconnect to the specific database
            $this->connect();
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Failed to create database: " . $e->getMessage());
        }
    }

    public function runMigration($migrationFile)
    {
        if (!file_exists($migrationFile)) {
            throw new Exception("Migration file not found: {$migrationFile}");
        }

        $sql = file_get_contents($migrationFile);
        
        // Split multiple statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $currentStatement = '';
        try {
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $currentStatement = $statement;
                    // Execute each statement directly (DDL statements don't support transactions)
                    $this->connection->exec($statement);
                }
            }
            return true;
        } catch (PDOException $e) {
            throw new Exception("Migration failed: " . $e->getMessage() . " SQL: " . $currentStatement);
        }
    }

    /**
     * Safe table count method
     */
    public function getTableCount($tableName)
    {
        try {
            if (!$this->tableExists($tableName)) {
                return 0;
            }
            
            $sql = "SELECT COUNT(*) as count FROM `{$tableName}`";
            $result = $this->fetch($sql);
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Table count failed for {$tableName}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all table names
     */
    public function getAllTables()
    {
        try {
            $sql = "SHOW TABLES";
            $result = $this->connection->query($sql);
            $tables = [];
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            return $tables;
        } catch (PDOException $e) {
            error_log("Get all tables failed: " . $e->getMessage());
            return [];
        }
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}