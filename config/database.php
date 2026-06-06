<?php
// config/database.php
class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            $dbFile = __DIR__ . '/../data/search_engine.db';
            
            // Create directory if not exists
            if (!is_dir(dirname($dbFile))) {
                mkdir(dirname($dbFile), 0777, true);
            }
            
            // SQLite connection (no username/password needed)
            self::$connection = new PDO("sqlite:$dbFile");
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables
            self::createTables();
        }
        return self::$connection;
    }
    
    private static function createTables() {
        self::$connection->exec("
            CREATE TABLE IF NOT EXISTS documents (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        self::$connection->exec("
            CREATE TABLE IF NOT EXISTS term_positions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                term TEXT NOT NULL,
                document_id INTEGER NOT NULL,
                position INTEGER NOT NULL
            )
        ");
        
        self::$connection->exec("CREATE INDEX IF NOT EXISTS idx_term ON term_positions(term)");
    }
}