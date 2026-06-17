<?php
/**
 * Course Class - CRUD operations for courses
 */
require_once __DIR__ . '/../config/Database.php';

class Course {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($department_id, $name, $code, $description) {
        $stmt = $this->db->prepare(
            "INSERT INTO courses (department_id, name, code, description) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$department_id, $name, $code, $description]);
    }

    public function getAll() {
        $query = "
            SELECT c.*, d.name AS department_name
            FROM courses c
            LEFT JOIN departments d ON c.department_id = d.id
            ORDER BY c.created_at DESC
        ";
        return $this->db->query($query)->fetchAll();
    }

    public function getByDepartment($department_id) {
        $stmt = $this->db->prepare(
            "SELECT c.*, d.name AS department_name
             FROM courses c
             LEFT JOIN departments d ON c.department_id = d.id
             WHERE c.department_id = ?
             ORDER BY c.name ASC"
        );
        $stmt->execute([$department_id]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $department_id, $name, $code, $description) {
        $stmt = $this->db->prepare(
            "UPDATE courses SET department_id = ?, name = ?, code = ?, description = ? WHERE id = ?"
        );
        return $stmt->execute([$department_id, $name, $code, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search($keyword) {
        $term = "%$keyword%";
        $stmt = $this->db->prepare(
            "SELECT c.*, d.name AS department_name
             FROM courses c
             LEFT JOIN departments d ON c.department_id = d.id
             WHERE c.name LIKE ? OR c.code LIKE ? OR c.description LIKE ?"
        );
        $stmt->execute([$term, $term, $term]);
        return $stmt->fetchAll();
    }
}
?>