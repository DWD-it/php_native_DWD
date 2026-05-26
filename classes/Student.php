<?php
/**
 * Student Class
 * Demonstrates: Inheritance (extends User) and Polymorphism (implements getDashboardUrl)
 */
require_once __DIR__ . '/User.php';

class Student extends User {
    
    public function __construct() {
        parent::__construct();
        $this->role = 'student';
    }

    /**
     * Polymorphism: Implementation of the abstract method
     */
    public function getDashboardUrl() {
        return 'student_dashboard.php';
    }

    /**
     * Student specific function: Update profile (Bonus Feature)
     */
    public function updateProfile($newName, $newPassword = null) {
        if ($newPassword !== null && !empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
            $result = $stmt->execute([$newName, $hashedPassword, $this->id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ? WHERE id = ?");
            $result = $stmt->execute([$newName, $this->id]);
        }
        
        if ($result) {
            $this->name = $newName; // Update the object property
            return true;
        }
        return false;
    }
}
?>
