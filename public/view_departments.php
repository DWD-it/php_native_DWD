<?php
/**
 * صفحة عرض الأقسام للطالب — Read Only
 * الطالب يشوف الأقسام بس، مش بيضيف أو يحذف
 */
require_once __DIR__ . '/../includes/auth.php';
checkStudent(); // يمنع الدخول لغير الطلاب

require_once __DIR__ . '/../classes/Department.php';
$dept = new Department();
$departments = $dept->getAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header glass-panel">
    <div>
        <h2>University Departments</h2>
        <p>Browse all available departments.</p>
    </div>
    <a href="student_dashboard.php" class="btn btn-secondary">&#8592; Back to Dashboard</a>
</div>

<div class="cards-grid">
    <?php if (empty($departments)): ?>
        <p class="empty-msg">No departments available yet.</p>
    <?php else: ?>
        <?php foreach ($departments as $d): ?>
        <div class="info-card glass-panel">
            <h3><?php echo htmlspecialchars($d['name']); ?></h3>
            <p><?php echo htmlspecialchars($d['description'] ?? 'No description available.'); ?></p>
            <!-- رابط لعرض مواد هذا القسم فقط -->
            <a href="view_courses.php?dept=<?php echo $d['id']; ?>" class="btn btn-primary" style="margin-top:1rem; display:inline-block;">
                View Courses
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
