<?php
/**
 * سكربت لتحديث قاعدة البيانات تلقائياً
 */
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Disable foreign key checks to drop tables safely
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Drop all existing tables
    $db->exec("DROP TABLE IF EXISTS enrollments, news, courses, departments, users");
    
    // Enable foreign key checks back
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Execute the SQL commands
    $db->exec($sql);
    
    echo "Database updated successfully!\n";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>
