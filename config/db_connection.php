<?php
// config/db_connection.php
class Database {
    private static $connection = null;
    
    // MySQL configuration for XAMPP
    private static $host = 'localhost';
    private static $dbname = 'search_engine_db';
    private static $username = 'root';
    private static $password = '';
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . self::$host . 
                       ";dbname=" . self::$dbname . 
                       ";charset=utf8mb4";
                
                self::$connection = new PDO($dsn, self::$username, self::$password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                if (php_sapi_name() === 'cli') {
                    echo "✅ Database connected successfully!\n";
                }
            } catch (PDOException $e) {
                die("❌ Database connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
    
    public static function testConnection() {
        try {
            $conn = self::getConnection();
            $conn->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>