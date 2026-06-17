<?php
require_once __DIR__ . '/User.php';

class Admin extends User {
    
    public function __construct() {
        parent::__construct();
        $this->role = 'admin';
    }

    public function getDashboardUrl() {
        return 'admin_dashboard.php';
    }

    public function getDashboardStats() {
        $stats = [];
        
        try {
            $stats['users'] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        } catch (PDOException $e) {
            $stats['users'] = 0;
        }
        
        try {
            $stats['departments'] = $this->db->query("SELECT COUNT(*) FROM departments")->fetchColumn();
        } catch (PDOException $e) {
            $stats['departments'] = 0;
        }
        
        try {
            $stats['courses'] = $this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        } catch (PDOException $e) {
            $stats['courses'] = 0;
        }
        
        try {
            $stats['enrollments'] = $this->db->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
        } catch (PDOException $e) {
            $stats['enrollments'] = 0;
        }
        
        return $stats;
    }
}
?>