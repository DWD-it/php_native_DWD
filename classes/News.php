<?php
/**
 * News Class - CRUD operations for news
 */
require_once __DIR__ . '/../config/Database.php';

class News {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($created_by, $title, $content) {
        $stmt = $this->db->prepare(
            "INSERT INTO news (created_by, title, content) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$created_by, $title, $content]);
    }

    public function getAll() {
        $query = "
            SELECT n.*, u.name AS author_name
            FROM news n
            LEFT JOIN users u ON n.created_by = u.id
            ORDER BY n.published_at DESC
        ";
        return $this->db->query($query)->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $title, $content) {
        $stmt = $this->db->prepare(
            "UPDATE news SET title = ?, content = ? WHERE id = ?"
        );
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>