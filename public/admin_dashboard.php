<?php
require_once __DIR__ . '/../includes/auth.php';
checkAdmin();

require_once __DIR__ . '/../classes/Admin.php';
$admin = new Admin();
$admin->loadById($_SESSION['user_id']);

// جلب الإحصائيات
$stats = $admin->getDashboardStats();

// التأكد من وجود قيم
$totalUsers = $stats['users'] ?? 0;
$totalDepartments = $stats['departments'] ?? 0;
$totalCourses = $stats['courses'] ?? 0;
$totalEnrollments = $stats['enrollments'] ?? 0;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="animate-fade-up">
    <!-- Hero Section -->
    <div class="glass-panel" style="padding: 2rem 2.5rem; margin-bottom: 2rem; background: linear-gradient(135deg, #0f172a, #1e1b4b, #2e1065); border-color: rgba(255,255,255,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.8rem; margin-bottom: 0.2rem;">
                    <span style="font-size: 2rem;">👋</span>
                    <h2 style="font-size: 1.8rem; font-weight: 800; color: white;">Admin Dashboard</h2>
                </div>
                <p style="color: rgba(255,255,255,0.7); font-size: 1rem;">
                    Welcome back, <strong style="color: white;"><?php echo htmlspecialchars($admin->getName()); ?></strong>
                </p>
            </div>
            <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
                <a href="manage_courses.php" class="btn btn-primary btn-sm">📚 Courses</a>
                <a href="departments.php" class="btn btn-secondary btn-sm" style="color: white; border-color: rgba(255,255,255,0.2);">🏛️ Departments</a>
                <a href="news.php" class="btn btn-success btn-sm">📰 News</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card glass-panel animate-fade-up delay-1">
            <div class="stat-icon">👥</div>
            <h3>Total Users</h3>
            <p class="stat-value blue"><?php echo $totalUsers; ?></p>
        </div>
        <div class="stat-card glass-panel animate-fade-up delay-2">
            <div class="stat-icon">🏛️</div>
            <h3>Departments</h3>
            <p class="stat-value purple"><?php echo $totalDepartments; ?></p>
        </div>
        <div class="stat-card glass-panel animate-fade-up delay-3">
            <div class="stat-icon">📚</div>
            <h3>Courses</h3>
            <p class="stat-value pink"><?php echo $totalCourses; ?></p>
        </div>
        <div class="stat-card glass-panel animate-fade-up delay-4">
            <div class="stat-icon">📝</div>
            <h3>Enrollments</h3>
            <p class="stat-value green"><?php echo $totalEnrollments; ?></p>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="actions-grid">
        <div class="action-card glass-panel animate-fade-up delay-1">
            <div class="card-icon">🏛️</div>
            <h3>Manage Departments</h3>
            <p>Add, edit, or remove university departments and programs.</p>
            <a href="departments.php" class="btn btn-primary btn-sm">Go to Departments →</a>
        </div>

        <div class="action-card glass-panel animate-fade-up delay-2">
            <div class="card-icon">📚</div>
            <h3>Manage Courses</h3>
            <p>Add, edit, or remove courses linked to departments.</p>
            <a href="manage_courses.php" class="btn btn-primary btn-sm">Go to Courses →</a>
        </div>

        <div class="action-card glass-panel animate-fade-up delay-3">
            <div class="card-icon">📰</div>
            <h3>Manage News</h3>
            <p>Post announcements and updates for students to read.</p>
            <a href="news.php" class="btn btn-primary btn-sm">Go to News →</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>