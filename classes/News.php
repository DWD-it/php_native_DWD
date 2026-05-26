<?php
/**
 * كلاس News — يتعامل مع عمليات CRUD الخاصة بالأخبار والإعلانات
 * الحقول: title, content, published_at, created_by (FK → users.id)
 */
require_once __DIR__ . '/../config/Database.php';

class News {
    // خاصية private للاتصال بقاعدة البيانات
    private $db;

    // الـ Constructor: نجيب الاتصال من الـ Singleton
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * CREATE — نشر خبر جديد
     * @param int    $created_by — رقم الأدمن الذي نشر الخبر (FK)
     * @param string $title      — عنوان الخبر
     * @param string $content    — محتوى الخبر
     */
    public function create($created_by, $title, $content) {
        $stmt = $this->db->prepare(
            "INSERT INTO news (created_by, title, content) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$created_by, $title, $content]);
    }

    /**
     * READ ALL — جلب كل الأخبار مع اسم الناشر (JOIN مع جدول users)
     */
    public function getAll() {
        $query = "
            SELECT n.*, u.name AS author_name
            FROM news n
            LEFT JOIN users u ON n.created_by = u.id
            ORDER BY n.published_at DESC
        ";
        return $this->db->query($query)->fetchAll();
    }

    /**
     * READ BY ID — جلب خبر واحد للتعديل
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * UPDATE — تعديل خبر موجود
     */
    public function update($id, $title, $content) {
        $stmt = $this->db->prepare(
            "UPDATE news SET title = ?, content = ? WHERE id = ?"
        );
        return $stmt->execute([$title, $content, $id]);
    }

    /**
     * DELETE — حذف خبر
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
