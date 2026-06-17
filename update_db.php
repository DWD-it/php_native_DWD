<?php
/**
 * سكربت لتحديث قاعدة البيانات تلقائياً
 */
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    $db->exec("DROP TABLE IF EXISTS enrollments, news, courses, departments, users");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    $sql = file_get_contents(__DIR__ . '/../database.sql');
    $db->exec($sql);
    
    echo "Database updated successfully!\n";
    echo "<br><a href='index.php'>Go to Login</a>";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>