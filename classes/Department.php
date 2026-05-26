<?php
/**
 * Department Entity Class
 * Handles CRUD operations for Departments
 */
require_once __DIR__ . '/../config/Database.php';

class Department {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // CREATE
    public function create($name, $description) {
        $stmt = $this->db->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
        return $stmt->execute([$name, $description]);
    }

    // READ ALL
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM departments ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // READ BY ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // UPDATE
    public function update($id, $name, $description) {
        $stmt = $this->db->prepare("UPDATE departments SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $id]);
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM departments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // BONUS: Search
    public function search($keyword) {
        $searchTerm = "%$keyword%";
        $stmt = $this->db->prepare("SELECT * FROM departments WHERE name LIKE ? OR description LIKE ?");
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}
?>
