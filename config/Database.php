<?php
/**
 * كلاس الاتصال بقاعدة البيانات (Database Connection Class)
 * نستخدم هنا نمط تصميم يسمى Singleton Pattern (النسخة الوحيدة).
 */
class Database {
    private static $instance = null;
    private $pdo;
    private $host = 'localhost';
    private $db_name = 'university_portal';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    private function __clone() {}
    public function __wakeup() {
        throw new Exception("لا يمكن عمل unserialize للنسخة الوحيدة.");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>