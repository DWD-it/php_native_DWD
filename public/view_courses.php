<?php
/**
 * صفحة عرض الكورسات للطالب — Read Only مع فلترة حسب القسم
 * يقدر الطالب يشوف الكورسات ويفلتر حسب القسم
 * مفيش Add/Edit/Delete — قراءة فقط
 */
require_once __DIR__ . '/../includes/auth.php';
checkStudent();

require_once __DIR__ . '/../classes/Course.php';
require_once __DIR__ . '/../classes/Department.php';

$course = new Course();
$dept   = new Department();

// الفلترة: لو الطالب اختار قسم معين نجيب كورساته، لو لأ نجيب الكل
$selectedDept = isset($_GET['dept']) ? (int) $_GET['dept'] : 0;

if ($selectedDept > 0) {
    // getByDepartment() تجيب الكورسات المرتبطة بقسم معين بالـ Prepared Statement
    $courses       = $course->getByDepartment($selectedDept);
    $deptInfo      = $dept->getById($selectedDept);
    $pageTitle     = "Courses in: " . htmlspecialchars($deptInfo['name'] ?? 'Department');
} else {
    // لو مفيش فلتر، نجيب كل الكورسات
    $courses   = $course->getAll();
    $pageTitle = "All Available Courses";
}

// BONUS: بحث بالكلمة المفتاحية
$searchKeyword = trim($_GET['search'] ?? '');
if (!empty($searchKeyword)) {
    $courses   = $course->search($searchKeyword);
    $pageTitle = "Search Results for: " . htmlspecialchars($searchKeyword);
}

$departments = $dept->getAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header glass-panel">
    <div>
        <h2><?php echo $pageTitle; ?></h2>
        <p>Browse courses available at the university.</p>
    </div>
    <a href="student_dashboard.php" class="btn btn-secondary">&#8592; Back to Dashboard</a>
</div>

<!-- ============ فلتر الأقسام + البحث (Bonus) ============ -->
<div class="filter-panel glass-panel">
    <form method="GET" action="view_courses.php" class="filter-form">
        <!-- فلتر الأقسام -->
        <div class="input-group" style="margin-bottom:0;">
            <label for="dept">Filter by Department</label>
            <select id="dept" name="dept" class="form-select" onchange="this.form.submit()">
                <option value="0">— All Departments —</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?php echo $d['id']; ?>"
                        <?php echo ($selectedDept == $d['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($d['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- BONUS: بحث بكلمة مفتاحية -->
        <div class="input-group" style="margin-bottom:0;">
            <label for="search">Search Courses</label>
            <div style="display:flex; gap:0.5rem;">
                <input type="text" id="search" name="search"
                       value="<?php echo htmlspecialchars($searchKeyword); ?>"
                       placeholder="Search by name or code...">
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if (!empty($searchKeyword) || $selectedDept): ?>
                    <a href="view_courses.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- ============ عرض الكورسات ============ -->
<div class="cards-grid">
    <?php if (empty($courses)): ?>
        <div class="glass-panel" style="padding:2rem; text-align:center; grid-column:1/-1;">
            <p class="empty-msg">No courses found.</p>
        </div>
    <?php else: ?>
        <?php foreach ($courses as $c): ?>
        <div class="info-card glass-panel">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
                <span class="badge"><?php echo htmlspecialchars($c['code']); ?></span>
                <small style="color:var(--text-muted);"><?php echo htmlspecialchars($c['department_name']); ?></small>
            </div>
            <h3><?php echo htmlspecialchars($c['name']); ?></h3>
            <p style="margin-bottom: 1.5rem;"><?php echo htmlspecialchars($c['description'] ?? 'No description available.'); ?></p>
            <a href="student_dashboard.php?enroll=<?php echo $c['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">+ Enroll Now</a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
