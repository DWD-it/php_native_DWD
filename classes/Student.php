<?php
/**
 * Student Class
 */
require_once __DIR__ . '/User.php';

class Student extends User {
    
    public function __construct() {
        parent::__construct();
        $this->role = 'student';
    }

    public function getDashboardUrl() {
        return 'student_dashboard.php';
    }

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
            $this->name = $newName;
            return true;
        }
        return false;
    }
}
?>