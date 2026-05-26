<?php
/**
 * صفحة إدارة الأقسام (Departments) — للأدمن فقط
 * عمليات CRUD كاملة: إضافة، عرض، تعديل، حذف
 */

// الحماية: نتحقق من الجلسة ونمنع الدخول إلا للأدمن
require_once __DIR__ . '/../includes/auth.php';
checkAdmin(); // إذا لم يكن أدمن، يتم التحويل تلقائياً لصفحة الدخول

// نستدعي كلاس Department لتنفيذ العمليات
require_once __DIR__ . '/../classes/Department.php';
$dept = new Department();

$error = '';
$success = '';
$editData = null; // ستحمل بيانات القسم عند الضغط على زر "تعديل"

// ============================================================
// معالجة الطلبات القادمة من الفورم (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- إضافة قسم جديد ---
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        // التحقق من أن الحقول ليست فارغة (Server-side Validation)
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $error = "Department name is required.";
        } else {
            // create() داخلياً يستخدم Prepared Statement — آمن من SQL Injection
            if ($dept->create($name, $description)) {
                $success = "Department added successfully!";
            } else {
                $error = "Failed to add department. Name might already exist.";
            }
        }
    }

    // --- تعديل قسم موجود ---
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id          = (int) $_POST['id'];       // نحول للـ integer للأمان
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $error = "Department name is required.";
        } else {
            if ($dept->update($id, $name, $description)) {
                $success = "Department updated successfully!";
            } else {
                $error = "Failed to update department.";
            }
        }
    }
}

// --- حذف قسم (GET Request) ---
// نستخدم GET مع id للحذف المباشر
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete']; // نحوله لـ integer لأمان إضافي
    if ($dept->delete($id)) {
        $success = "Department deleted successfully!";
    } else {
        $error = "Failed to delete department.";
    }
}

// --- تجهيز بيانات قسم للتعديل ---
// عندما يضغط الأدمن "Edit"، نجيب بيانات القسم لنملأ الفورم بها
if (isset($_GET['edit'])) {
    $editData = $dept->getById((int) $_GET['edit']);
}

// جلب كل الأقسام لعرضها في الجدول
$departments = $dept->getAll();

// تحميل الـ Header (يحتوي على HTML الأساسي والـ CSS)
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header glass-panel">
    <div>
        <h2>Manage Departments</h2>
        <p>Add, edit, or remove university departments.</p>
    </div>
    <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<!-- ============ فورم الإضافة / التعديل ============ -->
<div class="form-panel glass-panel">
    <h3><?php echo $editData ? 'Edit Department' : 'Add New Department'; ?></h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="departments.php">
        <!-- hidden input يحدد نوع العملية: create أو update -->
        <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
        <?php if ($editData): ?>
            <!-- نرسل الـ id مع التعديل حتى نعرف أي سجل نعدله -->
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>

        <div class="input-group">
            <label for="name">Department Name *</label>
            <!-- htmlspecialchars تحمي من XSS عند عرض البيانات -->
            <input type="text" id="name" name="name" required
                   value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>"
                   placeholder="e.g. Computer Science">
        </div>
        <div class="input-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                      placeholder="Brief description of the department..."><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $editData ? 'Update Department' : 'Add Department'; ?>
            </button>
            <?php if ($editData): ?>
                <a href="departments.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ============ جدول عرض الأقسام ============ -->
<div class="table-panel glass-panel">
    <h3>All Departments (<?php echo count($departments); ?>)</h3>
    <?php if (empty($departments)): ?>
        <p class="empty-msg">No departments found. Add one above!</p>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departments as $d): ?>
            <tr>
                <td><?php echo $d['id']; ?></td>
                <!-- htmlspecialchars تمنع XSS عند عرض البيانات القادمة من قاعدة البيانات -->
                <td><?php echo htmlspecialchars($d['name']); ?></td>
                <td><?php echo htmlspecialchars($d['description'] ?? '—'); ?></td>
                <td><?php echo date('d M Y', strtotime($d['created_at'])); ?></td>
                <td class="actions">
                    <a href="departments.php?edit=<?php echo $d['id']; ?>" class="btn-edit">Edit</a>
                    <!-- رابط الحذف مع تأكيد قبل التنفيذ -->
                    <a href="departments.php?delete=<?php echo $d['id']; ?>"
                       class="btn-delete"
                       onclick="return confirm('Are you sure you want to delete this department? This will also delete all linked courses!')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
