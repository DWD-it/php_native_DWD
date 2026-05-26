<?php
/**
 * لوحة التحكم للأدمن (Admin Dashboard)
 * تعرض الإحصائيات وأزرار التنقل لصفحات الإدارة الحقيقية
 */
require_once __DIR__ . '/../includes/auth.php';
checkAdmin(); // حماية: إذا لم يكن أدمن يتم التحويل لصفحة الدخول

require_once __DIR__ . '/../classes/Admin.php';
$admin = new Admin();
$admin->loadById($_SESSION['user_id']);

// BONUS: إحصائيات لوحة التحكم
$stats = $admin->getDashboardStats();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-header glass-panel" style="margin-bottom: 2rem; padding: 2rem;">
    <h2>Admin Dashboard</h2>
    <p>Welcome back, Administrator <strong><?php echo htmlspecialchars($admin->getName()); ?></strong></p>
</div>

<!-- BONUS: إحصائيات النظام -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card glass-panel" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-muted); font-size: 1rem;">Total Users</h3>
        <p class="stat-value" style="font-size: 2.5rem; font-weight: 800; color: var(--accent);"><?php echo $stats['users']; ?></p>
    </div>
    <div class="stat-card glass-panel" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-muted); font-size: 1rem;">Departments</h3>
        <p class="stat-value" style="font-size: 2.5rem; font-weight: 800; color: #8b5cf6;"><?php echo $stats['departments']; ?></p>
    </div>
    <div class="stat-card glass-panel" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-muted); font-size: 1rem;">Courses</h3>
        <p class="stat-value" style="font-size: 2.5rem; font-weight: 800; color: #ec4899;"><?php echo $stats['courses']; ?></p>
    </div>
    <div class="stat-card glass-panel" style="padding: 1.5rem; text-align: center;">
        <h3 style="color: var(--text-muted); font-size: 1rem;">Enrollments</h3>
        <p class="stat-value" style="font-size: 2.5rem; font-weight: 800; color: #10b981;"><?php echo $stats['enrollments']; ?></p>
    </div>
</div>

<!-- أزرار التنقل — مرتبطة بالصفحات الحقيقية الآن -->
<div class="actions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <div class="action-card glass-panel" style="padding: 2rem;">
        <h3>Manage Departments</h3>
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Add, edit, or remove university departments.</p>
        <a href="departments.php" class="btn btn-primary">Go to Departments</a>
    </div>

    <div class="action-card glass-panel" style="padding: 2rem;">
        <h3>Manage Courses</h3>
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Add, edit, or remove courses linked to departments.</p>
        <a href="courses.php" class="btn btn-primary">Go to Courses</a>
    </div>

    <div class="action-card glass-panel" style="padding: 2rem;">
        <h3>Manage News</h3>
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Post announcements for students to read.</p>
        <a href="news.php" class="btn btn-primary">Go to News</a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
