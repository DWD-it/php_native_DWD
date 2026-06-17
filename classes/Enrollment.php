<?php
/**
 * Enrollment Class
 */
require_once __DIR__ . '/../config/Database.php';

class Enrollment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function enroll($student_id, $course_id) {
        try {
            $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
            return $stmt->execute([$student_id, $course_id]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function getByStudent($student_id) {
        $query = "
            SELECT e.id as enrollment_id, e.enrolled_at, c.*, d.name as department_name
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            JOIN departments d ON c.department_id = d.id
            WHERE e.student_id = ?
            ORDER BY e.enrolled_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }

    public function unenroll($enrollment_id, $student_id) {
        $stmt = $this->db->prepare("DELETE FROM enrollments WHERE id = ? AND student_id = ?");
        return $stmt->execute([$enrollment_id, $student_id]);
    }
}
?>