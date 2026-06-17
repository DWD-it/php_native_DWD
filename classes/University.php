<?php
class University {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    // جلب جميع البرامج مع عدد المواد
    public function getDepartments() {
        $sql = "SELECT d.*, COUNT(c.id) as courses_count 
                FROM departments d 
                LEFT JOIN courses c ON d.id = c.dept_id 
                GROUP BY d.id 
                ORDER BY d.id ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب مادة بواسطة ID
    public function getCourseById($id) {
        $sql = "SELECT * FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // التحقق من وجود الكود
    public function isCodeExists($code, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM courses WHERE code = ?";
        $params = [$code];
        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // إضافة مادة جديدة
    public function addCourse($name, $code, $desc, $dept_id, $credit_hours = 3) {
        $sql = "INSERT INTO courses (name, code, description, dept_id, credit_hours) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $code, $desc, $dept_id, $credit_hours]);
    }

    // تحديث مادة
    public function updateCourse($id, $name, $code, $desc, $dept_id, $credit_hours = 3) {
        $sql = "UPDATE courses SET name = ?, code = ?, description = ?, dept_id = ?, credit_hours = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $code, $desc, $dept_id, $credit_hours, $id]);
    }

    // حذف مادة
    public function deleteCourse($id) {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // جلب جميع المواد
    public function getCourses($search = "") {
        $sql = "SELECT c.*, d.dept_name FROM courses c JOIN departments d ON c.dept_id = d.id";
        if (!empty($search)) {
            $sql .= " WHERE c.name LIKE ? OR c.code LIKE ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(["%$search%", "%$search%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->db->query($sql . " ORDER BY c.id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // جلب إحصائيات سريعة
    public function getStats() {
        $totalCourses = $this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        $totalDepts = $this->db->query("SELECT COUNT(*) FROM departments")->fetchColumn();
        $totalHours = $this->db->query("SELECT SUM(credit_hours) FROM courses")->fetchColumn();
        return [
            'courses' => $totalCourses,
            'departments' => $totalDepts,
            'credit_hours' => $totalHours ?: 0
        ];
    }

// إحصائية المواد لكل قسم (للرسم البياني)
public function getCoursesByDepartmentStats() {
    $sql = "SELECT d.id, d.dept_name, COUNT(c.id) as courses_count 
            FROM departments d 
            LEFT JOIN courses c ON d.id = c.dept_id 
            GROUP BY d.id 
            ORDER BY courses_count DESC";
    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// جلب مادة مع تفاصيلها الكاملة والمواد المشابهة
public function getCourseFullDetails($id) {
    $sql = "SELECT c.*, d.dept_name FROM courses c 
            JOIN departments d ON c.dept_id = d.id 
            WHERE c.id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($course) {
        // جلب مواد مشابهة في نفس القسم
        $sql2 = "SELECT id, name, code, credit_hours FROM courses 
                 WHERE dept_id = ? AND id != ? LIMIT 5";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->execute([$course['dept_id'], $id]);
        $course['similar_courses'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
    return $course;
}
}
?>