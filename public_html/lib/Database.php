<?php
declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;
    private static string $dbPath = __DIR__ . '/../data/bigfootcms.db';
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                // Create data directory if it doesn't exist
                $dataDir = dirname(self::$dbPath);
                if (!file_exists($dataDir)) {
                    mkdir($dataDir, 0755, true);
                }
                
                // Create new PDO instance
                self::$instance = new PDO('sqlite:' . self::$dbPath, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                
                // Enable foreign keys
                self::$instance->exec('PRAGMA foreign_keys = ON');
                
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new RuntimeException('Database connection failed');
            }
        }
        
        return self::$instance;
    }
    
    public static function query(string $sql, array $params = []): PDOStatement {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function lastInsertId(): string {
        return self::getInstance()->lastInsertId();
    }
    
    // Prevent instantiation
    private function __construct() {}
    private function __clone() {}
} 