<?php
require_once __DIR__ . '/../includes/auth.php';
checkAdmin();

require_once __DIR__ . '/../classes/Department.php';
$dept = new Department();

$error = '';
$success = '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $error = "Department name is required.";
        } else {
            if ($dept->create($name, $description)) {
                $success = "Department added successfully!";
            } else {
                $error = "Failed to add department.";
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = (int) $_POST['id'];
        $name = trim($_POST['name'] ?? '');
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

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($dept->delete($id)) {
        $success = "Department deleted successfully!";
    } else {
        $error = "Failed to delete department.";
    }
}

if (isset($_GET['edit'])) {
    $editData = $dept->getById((int) $_GET['edit']);
}

$departments = $dept->getAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header glass-panel">
    <div>
        <h2>Manage Departments</h2>
        <p>Add, edit, or remove university departments.</p>
    </div>
    <a href="admin_dashboard.php" class="btn btn-secondary">&#8592; Back to Dashboard</a>
</div>

<div class="form-panel glass-panel">
    <h3><?php echo $editData ? 'Edit Department' : 'Add New Department'; ?></h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="departments.php">
        <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>

        <div class="input-group">
            <label for="name">Department Name *</label>
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
                <td><?php echo htmlspecialchars($d['name']); ?></td>
                <td><?php echo htmlspecialchars($d['description'] ?? '—'); ?></td>
                <td><?php echo date('d M Y', strtotime($d['created_at'])); ?></td>
                <td class="actions">
                    <a href="departments.php?edit=<?php echo $d['id']; ?>" class="btn-edit">Edit</a>
                    <a href="departments.php?delete=<?php echo $d['id']; ?>"
                       class="btn-delete"
                       onclick="return confirm('Are you sure you want to delete this department?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>