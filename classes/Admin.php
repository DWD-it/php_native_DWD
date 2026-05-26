<?php
/**
 * Admin Class
 * Demonstrates: Inheritance (extends User) and Polymorphism (implements getDashboardUrl)
 */
require_once __DIR__ . '/User.php';

class Admin extends User {
    
    public function __construct() {
        parent::__construct();
        $this->role = 'admin';
    }

    /**
     * Polymorphism: Implementation of the abstract method
     */
    public function getDashboardUrl() {
        return 'admin_dashboard.php';
    }

    /**
     * Admin specific function: Get counts for the dashboard stats (Bonus Feature)
     */
    public function getDashboardStats() {
        $stats = [];
        
        $stats['users'] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['departments'] = $this->db->query("SELECT COUNT(*) FROM departments")->fetchColumn();
        $stats['courses'] = $this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        $stats['enrollments'] = $this->db->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
        
        return $stats;
    }
}
?>
