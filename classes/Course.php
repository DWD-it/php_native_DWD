<?php
/**
 * كلاس Course — يتعامل مع عمليات CRUD الخاصة بالمواد الدراسية
 * الحقول حسب المطلوب في المشروع: name, code, description, department_id
 */
require_once __DIR__ . '/../config/Database.php';

class Course {
    // خاصية private للاتصال بقاعدة البيانات
    private $db;

    // الـ Constructor: نجيب الاتصال من الـ Singleton
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * CREATE — إضافة مادة جديدة
     * نستخدم Prepared Statement لحماية من SQL Injection
     * @param int    $department_id — رقم القسم (Foreign Key)
     * @param string $name          — اسم المادة
     * @param string $code          — كود المادة مثل CS101
     * @param string $description   — وصف المادة
     */
    public function create($department_id, $name, $code, $description) {
        $stmt = $this->db->prepare(
            "INSERT INTO courses (department_id, name, code, description) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$department_id, $name, $code, $description]);
    }

    /**
     * READ ALL — جلب كل المواد مع اسم القسم باستخدام JOIN
     * LEFT JOIN تجمع بيانات جدولين في نتيجة واحدة
     */
    public function getAll() {
        $query = "
            SELECT c.*, d.name AS department_name
            FROM courses c
            LEFT JOIN departments d ON c.department_id = d.id
            ORDER BY c.created_at DESC
        ";
        // query() بدون بيانات متغيرة — آمنة هنا لأنه لا يوجد إدخال مستخدم
        return $this->db->query($query)->fetchAll();
    }

    /**
     * READ BY DEPARTMENT — جلب المواد المرتبطة بقسم معين (للطالب)
     * نستخدم Prepared Statement لأن الـ id جاي من المستخدم
     */
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

    /**
     * READ BY ID — جلب مادة واحدة عن طريق الـ id (للتعديل)
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * UPDATE — تعديل بيانات مادة موجودة
     * نمرر الـ id في الآخر علشان يوافق ترتيب الـ ? في الـ WHERE
     */
    public function update($id, $department_id, $name, $code, $description) {
        $stmt = $this->db->prepare(
            "UPDATE courses SET department_id = ?, name = ?, code = ?, description = ? WHERE id = ?"
        );
        return $stmt->execute([$department_id, $name, $code, $description, $id]);
    }

    /**
     * DELETE — حذف مادة
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * SEARCH — بونص: بحث عن مادة بالاسم أو الكود
     * LIKE مع % للبحث الجزئي
     */
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
