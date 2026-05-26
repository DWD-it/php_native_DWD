<?php
/**
 * صفحة إدارة الكورسات (Courses) — للأدمن فقط
 * عمليات CRUD كاملة: إضافة، عرض، تعديل، حذف
 * كل كورس مرتبط بقسم (department_id — Foreign Key)
 */

require_once __DIR__ . '/../includes/auth.php';
checkAdmin();

require_once __DIR__ . '/../classes/Course.php';
require_once __DIR__ . '/../classes/Department.php';

$course = new Course();
$dept   = new Department();

$error    = '';
$success  = '';
$editData = null;

// ============================================================
// معالجة طلبات الفورم (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- إضافة كورس جديد ---
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $name          = trim($_POST['name'] ?? '');
        $code          = trim($_POST['code'] ?? '');
        $description   = trim($_POST['description'] ?? '');
        $department_id = (int) $_POST['department_id'];

        // التحقق من الحقول الإلزامية
        if (empty($name) || empty($code) || $department_id === 0) {
            $error = "Course name, code, and department are required.";
        } else {
            if ($course->create($department_id, $name, $code, $description)) {
                $success = "Course added successfully!";
            } else {
                $error = "Failed to add course. Code might already exist.";
            }
        }
    }

    // --- تعديل كورس موجود ---
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id            = (int) $_POST['id'];
        $name          = trim($_POST['name'] ?? '');
        $code          = trim($_POST['code'] ?? '');
        $description   = trim($_POST['description'] ?? '');
        $department_id = (int) $_POST['department_id'];

        if (empty($name) || empty($code) || $department_id === 0) {
            $error = "Course name, code, and department are required.";
        } else {
            if ($course->update($id, $department_id, $name, $code, $description)) {
                $success = "Course updated successfully!";
            } else {
                $error = "Failed to update course.";
            }
        }
    }
}

// --- حذف كورس ---
if (isset($_GET['delete'])) {
    if ($course->delete((int) $_GET['delete'])) {
        $success = "Course deleted successfully!";
    } else {
        $error = "Failed to delete course.";
    }
}

// --- تجهيز بيانات الكورس للتعديل ---
if (isset($_GET['edit'])) {
    $editData = $course->getById((int) $_GET['edit']);
}

// جلب كل الكورسات والأقسام
$courses     = $course->getAll();
$departments = $dept->getAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header glass-panel">
    <div>
        <h2>Manage Courses</h2>
        <p>Add, edit, or remove courses linked to departments.</p>
    </div>
    <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<!-- ============ فورم الإضافة / التعديل ============ -->
<div class="form-panel glass-panel">
    <h3><?php echo $editData ? 'Edit Course' : 'Add New Course'; ?></h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="courses.php">
        <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="input-group">
                <label for="name">Course Name *</label>
                <input type="text" id="name" name="name" required
                       value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>"
                       placeholder="e.g. Web Development II">
            </div>
            <div class="input-group">
                <label for="code">Course Code *</label>
                <input type="text" id="code" name="code" required
                       value="<?php echo htmlspecialchars($editData['code'] ?? ''); ?>"
                       placeholder="e.g. CS301">
            </div>
        </div>

        <div class="input-group">
            <label for="department_id">Department *</label>
            <!-- Dropdown مملوء بالأقسام من قاعدة البيانات -->
            <select id="department_id" name="department_id" required class="form-select">
                <option value="">— Select Department —</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?php echo $d['id']; ?>"
                        <?php echo (isset($editData['department_id']) && $editData['department_id'] == $d['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($d['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                      placeholder="Brief course description..."><?php echo htmlspecialchars($editData['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $editData ? 'Update Course' : 'Add Course'; ?>
            </button>
            <?php if ($editData): ?>
                <a href="courses.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ============ جدول عرض الكورسات ============ -->
<div class="table-panel glass-panel">
    <h3>All Courses (<?php echo count($courses); ?>)</h3>
    <?php if (empty($courses)): ?>
        <p class="empty-msg">No courses found. Add one above! (Make sure to add a department first)</p>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Department</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><span class="badge"><?php echo htmlspecialchars($c['code']); ?></span></td>
                <td><?php echo htmlspecialchars($c['name']); ?></td>
                <td><?php echo htmlspecialchars($c['department_name']); ?></td>
                <td><?php echo htmlspecialchars(substr($c['description'] ?? '—', 0, 60)) . (strlen($c['description'] ?? '') > 60 ? '...' : ''); ?></td>
                <td class="actions">
                    <a href="courses.php?edit=<?php echo $c['id']; ?>" class="btn-edit">Edit</a>
                    <a href="courses.php?delete=<?php echo $c['id']; ?>"
                       class="btn-delete"
                       onclick="return confirm('Delete this course?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
