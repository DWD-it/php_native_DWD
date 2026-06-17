<?php
require_once __DIR__ . '/../includes/auth.php';
checkStudent();

require_once __DIR__ . '/../classes/Student.php';
require_once __DIR__ . '/../classes/Enrollment.php';

$student = new Student();
$student->loadById($_SESSION['user_id']);

$enrollment = new Enrollment();
$myEnrollments = $enrollment->getByStudent($_SESSION['user_id']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $newName = trim($_POST['name'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    if (empty($newName)) {
        $error = "⚠️ Name cannot be empty.";
    } else {
        if ($student->updateProfile($newName, $newPassword ?: null)) {
            $_SESSION['user_name'] = $newName;
            $success = "✅ Profile updated successfully!";
            $student->loadById($_SESSION['user_id']);
        } else {
            $error = "❌ Failed to update profile.";
        }
    }
}

if (isset($_GET['enroll'])) {
    $course_id = (int) $_GET['enroll'];
    try {
        $enrollment->enroll($_SESSION['user_id'], $course_id);
        $success = "🎉 Enrolled successfully!";
        $myEnrollments = $enrollment->getByStudent($_SESSION['user_id']);
    } catch (PDOException $e) {
        $error = "⚠️ You are already enrolled in this course.";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="animate-fade-up">
    <!-- Hero Section -->
    <div class="glass-panel" style="padding: 2rem 2.5rem; margin-bottom: 2rem; background: linear-gradient(135deg, #1e1b4b, #2e1065); border-color: rgba(255,255,255,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.8rem; margin-bottom: 0.2rem;">
                    <span style="font-size: 2rem;">🎓</span>
                    <h2 style="font-size: 1.8rem; font-weight: 800; color: white;">Student Dashboard</h2>
                </div>
                <p style="color: rgba(255,255,255,0.7); font-size: 1rem;">
                    Welcome, <strong style="color: white;"><?php echo htmlspecialchars($student->getName()); ?></strong>
                </p>
            </div>
            <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
                <a href="view_courses.php" class="btn btn-primary btn-sm">📚 Courses</a>
                <a href="view_departments.php" class="btn btn-secondary btn-sm" style="color: white; border-color: rgba(255,255,255,0.2);">🏛️ Departments</a>
                <a href="view_news.php" class="btn btn-success btn-sm">📰 News</a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card glass-panel animate-fade-up delay-1">
            <div class="stat-icon">📚</div>
            <h3>Enrolled Courses</h3>
            <p class="stat-value blue"><?php echo count($myEnrollments); ?></p>
        </div>
        <div class="stat-card glass-panel animate-fade-up delay-2">
            <div class="stat-icon">🎓</div>
            <h3>Student Since</h3>
            <p class="stat-value purple" style="font-size: 1.2rem;"><?php echo date('M Y'); ?></p>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="actions-grid" style="margin-bottom: 2rem;">
        <div class="action-card glass-panel animate-fade-up delay-1">
            <div class="card-icon">🏛️</div>
            <h3>View Departments</h3>
            <p>Browse all university departments and their details.</p>
            <a href="view_departments.php" class="btn btn-primary btn-sm">Browse →</a>
        </div>

        <div class="action-card glass-panel animate-fade-up delay-2">
            <div class="card-icon">📚</div>
            <h3>View Courses</h3>
            <p>Browse all available courses, filter by department.</p>
            <a href="view_courses.php" class="btn btn-primary btn-sm">Browse →</a>
        </div>

        <div class="action-card glass-panel animate-fade-up delay-3">
            <div class="card-icon">📰</div>
            <h3>News &amp; Announcements</h3>
            <p>Read the latest university announcements.</p>
            <a href="view_news.php" class="btn btn-primary btn-sm">Read →</a>
        </div>
    </div>

    <!-- Enrollments -->
    <div class="glass-panel" style="padding: 1.5rem 2rem; margin-bottom: 2rem;">
        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.6rem; font-size: 1.2rem;">
            <span>📋</span> My Enrolled Courses <span class="badge badge-ghost"><?php echo count($myEnrollments); ?></span>
        </h3>
        <?php if (empty($myEnrollments)): ?>
            <div class="empty-state" style="padding: 1.5rem;">
                <div class="empty-icon" style="font-size: 2.5rem;">📭</div>
                <h3 style="font-size: 1rem;">No Enrollments Yet</h3>
                <p style="font-size: 0.9rem;">You haven't enrolled in any courses. <a href="view_courses.php" style="color: var(--blue); font-weight: 600;">Browse courses →</a></p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.8rem;">
                <?php foreach ($myEnrollments as $e): ?>
                <div style="background: rgba(37,99,235,0.04); border: 1px solid rgba(37,99,235,0.06); border-radius: var(--radius-sm); padding: 1rem 1.2rem; transition: all var(--transition);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                        <span class="badge" style="font-size: 0.65rem;"><?php echo htmlspecialchars($e['code']); ?></span>
                        <span style="font-size: 0.6rem; color: var(--text-muted);"><?php echo date('d M Y', strtotime($e['enrolled_at'])); ?></span>
                    </div>
                    <p style="font-weight: 600; font-size: 0.95rem;"><?php echo htmlspecialchars($e['name']); ?></p>
                    <small style="color: var(--text-muted); font-size: 0.75rem;"><?php echo htmlspecialchars($e['department_name']); ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Profile -->
    <div class="glass-panel" style="padding: 1.5rem 2rem;">
        <h3 style="margin-bottom: 1.2rem; display: flex; align-items: center; gap: 0.6rem; font-size: 1.2rem;">
            <span>👤</span> My Profile
        </h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="student_dashboard.php">
            <input type="hidden" name="action" value="update_profile">
            <div class="input-group">
                <label>📧 Email</label>
                <input type="email" value="<?php echo htmlspecialchars($student->getEmail()); ?>" disabled style="opacity: 0.6;">
            </div>
            <div class="input-group">
                <label for="name">👤 Full Name</label>
                <input type="text" id="name" name="name" required
                       value="<?php echo htmlspecialchars($student->getName()); ?>"
                       placeholder="Enter your full name">
            </div>
            <div class="input-group">
                <label for="password">🔑 New Password <span style="font-weight: 400; color: var(--text-muted); font-size: 0.8rem;">(leave blank to keep current)</span></label>
                <input type="password" id="password" name="password" placeholder="Enter new password...">
            </div>
            <button type="submit" class="btn btn-primary">💾 Update Profile</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>