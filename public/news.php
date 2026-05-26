<?php
/**
 * صفحة إدارة الأخبار والإعلانات (News) — للأدمن فقط
 * عمليات CRUD: إضافة، عرض، تعديل، حذف
 * created_by مربوط بالأدمن الذي نشر الخبر من الـ Session
 */

require_once __DIR__ . '/../includes/auth.php';
checkAdmin();

require_once __DIR__ . '/../classes/News.php';
$news = new News();

$error    = '';
$success  = '';
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $title      = trim($_POST['title'] ?? '');
        $content    = trim($_POST['content'] ?? '');
        $created_by = $_SESSION['user_id'];

        if (empty($title) || empty($content)) {
            $error = "Title and content are required.";
        } else {
            if ($news->create($created_by, $title, $content)) {
                $success = "News published successfully!";
            } else {
                $error = "Failed to publish news.";
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id      = (int) $_POST['id'];
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($title) || empty($content)) {
            $error = "Title and content are required.";
        } else {
            if ($news->update($id, $title, $content)) {
                $success = "News updated successfully!";
            } else {
                $error = "Failed to update news.";
            }
        }
    }
}

if (isset($_GET['delete'])) {
    if ($news->delete((int) $_GET['delete'])) {
        $success = "News deleted successfully!";
    } else {
        $error = "Failed to delete news.";
    }
}

if (isset($_GET['edit'])) {
    $editData = $news->getById((int) $_GET['edit']);
}

$allNews = $news->getAll();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header glass-panel">
    <div>
        <h2>Manage News &amp; Announcements</h2>
        <p>Post, edit, or delete university announcements.</p>
    </div>
    <a href="admin_dashboard.php" class="btn btn-secondary">&#8592; Back to Dashboard</a>
</div>

<div class="form-panel glass-panel">
    <h3><?php echo $editData ? 'Edit Announcement' : 'Publish New Announcement'; ?></h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="news.php">
        <input type="hidden" name="action" value="<?php echo $editData ? 'update' : 'create'; ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
        <?php endif; ?>

        <div class="input-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" required
                   value="<?php echo htmlspecialchars($editData['title'] ?? ''); ?>"
                   placeholder="e.g. Registration Deadline Extended">
        </div>
        <div class="input-group">
            <label for="content">Content *</label>
            <textarea id="content" name="content" rows="5" required
                      placeholder="Write the announcement details here..."><?php echo htmlspecialchars($editData['content'] ?? ''); ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $editData ? 'Update Announcement' : 'Publish Announcement'; ?>
            </button>
            <?php if ($editData): ?>
                <a href="news.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-panel glass-panel">
    <h3>All Announcements (<?php echo count($allNews); ?>)</h3>
    <?php if (empty($allNews)): ?>
        <p class="empty-msg">No announcements yet. Publish one above!</p>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Content Preview</th>
                <th>Published By</th>
                <th>Published At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allNews as $n): ?>
            <tr>
                <td><?php echo $n['id']; ?></td>
                <td><?php echo htmlspecialchars($n['title']); ?></td>
                <td><?php echo htmlspecialchars(substr($n['content'], 0, 80)) . (strlen($n['content']) > 80 ? '...' : ''); ?></td>
                <td><?php echo htmlspecialchars($n['author_name']); ?></td>
                <td><?php echo date('d M Y, H:i', strtotime($n['published_at'])); ?></td>
                <td class="actions">
                    <a href="news.php?edit=<?php echo $n['id']; ?>" class="btn-edit">Edit</a>
                    <a href="news.php?delete=<?php echo $n['id']; ?>"
                       class="btn-delete"
                       onclick="return confirm('Delete this announcement?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
