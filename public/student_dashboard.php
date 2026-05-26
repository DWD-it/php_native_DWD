<?php
/**
 * لوحة تحكم الطالب (Student Dashboard)
 * تعرض روابط لعرض الأقسام، الكورسات، الأخبار، والبروفايل
 */
require_once __DIR__ . '/../includes/auth.php';
checkStudent();

require_once __DIR__ . '/../classes/Student.php';
require_once __DIR__ . '/../classes/Enrollment.php';

$student = new Student();
$student->loadById($_SESSION['user_id']);

$enrollment = new Enrollment();
$myEnrollments = $enrollment->getByStudent($_SESSION['user_id']);

$error   = '';
$success = '';

// ============================================================
// BONUS: تحديث الملف الشخصي (Profile Update)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $newName     = trim($_POST['name'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    if (empty($newName)) {
        $error = "Name cannot be empty.";
    } else {
        if ($student->updateProfile($newName, $newPassword ?: null)) {
            // نحدث الاسم في الـ SESSION بعد التغيير
            $_SESSION['user_name'] = $newName;
            $success = "Profile updated successfully!";
            // إعادة تحميل بيانات الطالب من قاعدة البيانات
            $student->loadById($_SESSION['user_id']);
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// BONUS: التسجيل في كورس
if (isset($_GET['enroll'])) {
    $course_id = (int) $_GET['enroll'];
    try {
        $enrollment->enroll($_SESSION['user_id'], $course_id);
        $success = "Enrolled successfully!";
        $myEnrollments = $enrollment->getByStudent($_SESSION['user_id']);
    } catch (PDOException $e) {
        $error = "You are already enrolled in this course.";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-header glass-panel" style="margin-bottom: 2rem; padding: 2rem;">
    <h2>Student Dashboard</h2>
    <p>Welcome, <strong><?php echo htmlspecialchars($student->getName()); ?></strong></p>
</div>

<!-- روابط التنقل الرئيسية -->
<div class="actions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="action-card glass-panel" style="padding: 2rem;">
        <h3>View Departments</h3>
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Browse all university departments and their details.</p>
        <a href="view_departments.php" class="btn btn-primary">Browse Departments</a>
    </div>

    <div class="action-card glass-panel" style="padding: 2rem;">
        <h3>View Courses</h3>
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Browse all available courses, filter by department.</p>
        <a href="view_courses.php" class="btn btn-primary">Browse Courses</a>
    </div>

    <div class="action-card glass-panel" style="padding: 2rem;">
        <h3>News &amp; Announcements</h3>
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Read the latest university announcements.</p>
        <a href="view_news.php" class="btn btn-primary">Read News</a>
    </div>
</div>

<!-- BONUS: My Enrollments -->
<div class="glass-panel" style="padding: 2rem; margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1rem;">My Enrolled Courses (<?php echo count($myEnrollments); ?>)</h3>
    <?php if (empty($myEnrollments)): ?>
        <p style="color: var(--text-muted);">You have not enrolled in any courses yet. <a href="view_courses.php" style="color: var(--accent);">Browse courses</a></p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <?php foreach ($myEnrollments as $e): ?>
            <div style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3); border-radius: 10px; padding: 1rem;">
                <span class="badge"><?php echo htmlspecialchars($e['code']); ?></span>
                <p style="margin-top: 0.5rem; font-weight: 600;"><?php echo htmlspecialchars($e['name']); ?></p>
                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($e['department_name']); ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- BONUS: تحديث الملف الشخصي (Profile) -->
<div class="glass-panel" style="padding: 2rem;">
    <h3 style="margin-bottom: 1.5rem;">My Profile</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="student_dashboard.php">
        <input type="hidden" name="action" value="update_profile">
        <div class="input-group">
            <label>Email (cannot be changed)</label>
            <!-- البريد الإلكتروني لا يمكن تغييره — نعرضه كـ read-only -->
            <input type="email" value="<?php echo htmlspecialchars($student->getEmail()); ?>" disabled style="opacity: 0.5;">
        </div>
        <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required
                   value="<?php echo htmlspecialchars($student->getName()); ?>">
        </div>
        <div class="input-group">
            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
