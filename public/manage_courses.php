<?php
require_once '../config/database.php';
require_once '../classes/University.php';

$university = new University($conn);
$error_message = "";

// معالجة إضافة مادة جديدة
if (isset($_POST['save_course'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $code = htmlspecialchars(trim($_POST['code']));
    $desc = htmlspecialchars(trim($_POST['desc']));
    $dept_id = (int)$_POST['dept_id'];
    $credit_hours = (int)$_POST['credit_hours'];
    
    if(!empty($name) && !empty($code) && $dept_id > 0) {
        if (!$university->isCodeExists($code)) {
            if($university->addCourse($name, $code, $desc, $dept_id, $credit_hours)) {
                header("Location: manage_courses.php?status=success");
                exit();
            } else {
                $error_message = "حدث خطأ أثناء إضافة المادة!";
            }
        } else {
            $error_message = "عذراً، كود المادة ($code) مسجل مسبقاً في النظام!";
        }
    } else {
        $error_message = "الرجاء ملء جميع الحقول المطلوبة!";
    }
}

// معالجة الحذف
if (isset($_GET['del'])) {
    $university->deleteCourse((int)$_GET['del']);
    header("Location: manage_courses.php?status=deleted");
    exit();
}

// جلب البيانات
$depts = $university->getDepartments();
$courses = $university->getCourses("");
$stats = $university->getStats();
$deptStats = $university->getCoursesByDepartmentStats(); // للإحصائيات البيانية
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>إدارة المواد - جامعة برج العرب</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        
        :root {
            --bg-from: #f8fafc;
            --bg-to: #f1f5f9;
            --card-bg: #ffffff;
            --card-bg-2: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --input-bg: #ffffff;
            --table-header: #f1f5f9;
            --hover-bg: #f0f9ff;
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        
        body.dark-mode {
            --bg-from: #0f172a;
            --bg-to: #1e293b;
            --card-bg: #1e293b;
            --card-bg-2: #0f172a;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #334155;
            --input-bg: #0f172a;
            --table-header: #0f172a;
            --hover-bg: #334155;
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.3);
        }
        
        body {
            background: linear-gradient(to bottom right, var(--bg-from), var(--bg-to));
            min-height: 100vh;
        }
        
        .bg-white, .bg-slate-50, .bg-slate-100 { background-color: var(--card-bg) !important; }
        .text-slate-600, .text-slate-500, .text-slate-700, .text-slate-400 { color: var(--text-secondary) !important; }
        .text-slate-700, .text-slate-800, h1, h2, h3 { color: var(--text-primary) !important; }
        .border-slate-100, .border-slate-200 { border-color: var(--border) !important; }
        input, select, textarea {
            background-color: var(--input-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--border) !important;
        }
        table th { background-color: var(--table-header) !important; color: var(--text-secondary) !important; }
        .course-row:hover { background-color: var(--hover-bg) !important; }
        
        .dark-mode-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 1000;
            border: none;
            font-size: 28px;
        }
        .dark-mode-btn:hover { transform: scale(1.1); }
        
        body.dark-mode .bg-amber-100 { background-color: #451a03 !important; color: #fbbf24 !important; }
        body.dark-mode .bg-green-100 { background-color: #064e3b !important; color: #34d399 !important; }
        
        /* تحسينات الأزرار */
        .action-btn {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .action-btn:hover { transform: translateY(-2px); }
        
        /* شريط التمرير للجدول */
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .table-container::-webkit-scrollbar-track {
            background: var(--border);
            border-radius: 10px;
        }
        .table-container::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }
        
        /* تأثيرات التحويم */
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        
        /* مودال مخصص */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: none;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
        }
        
        /* تحسين الجدول القابل للفرز */
        .sortable-header {
            cursor: pointer;
            user-select: none;
            transition: background 0.2s;
        }
        .sortable-header:hover {
            background: var(--hover-bg);
        }
        .sort-icon {
            display: inline-block;
            margin-right: 5px;
            font-size: 12px;
            opacity: 0.5;
        }
        .sort-icon.active {
            opacity: 1;
            color: #2563eb;
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="max-w-7xl mx-auto">
        <!-- Header مع أزرار إضافية -->
        <div class="rounded-3xl shadow-lg p-6 mb-8 flex flex-col md:flex-row justify-between items-center border" style="background-color: var(--card-bg); border-color: var(--border);">
            <div>
                <h1 class="text-3xl font-black bg-gradient-to-r from-blue-700 to-indigo-700 bg-clip-text text-transparent">
                    🏫 جامعة برج العرب التكنولوجية
                </h1>
                <p class="mt-1" style="color: var(--text-secondary);">نظام إدارة المواد الدراسية | <span style="color: #3b82f6;" class="font-bold">Abd El-Rahman Ali</span></p>
            </div>
            <div class="flex gap-3 mt-4 md:mt-0 flex-wrap justify-center">
                <button onclick="openChartModal()" class="bg-purple-600 text-white px-5 py-2 rounded-2xl font-bold hover:bg-purple-700 transition shadow-md text-sm action-btn">
                    📊 رسم بياني
                </button>
                <button onclick="exportToExcel()" class="bg-green-600 text-white px-5 py-2 rounded-2xl font-bold hover:bg-green-700 transition shadow-md text-sm action-btn">
                    📎 تصدير Excel
                </button>
                <button onclick="exportToPDF()" class="bg-red-600 text-white px-5 py-2 rounded-2xl font-bold hover:bg-red-700 transition shadow-md text-sm action-btn">
                    📄 PDF
                </button>
                <button onclick="printTable()" class="bg-gray-600 text-white px-5 py-2 rounded-2xl font-bold hover:bg-gray-700 transition shadow-md text-sm action-btn">
                    🖨️ طباعة
                </button>
                <a href="view_departments.php" class="bg-indigo-600 text-white px-5 py-2 rounded-2xl font-bold hover:bg-indigo-700 transition shadow-md text-sm action-btn inline-block">
                    🏛️ البرامج
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card rounded-2xl p-6 text-white shadow-lg" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                <div class="flex justify-between items-start">
                    <div><p class="text-blue-100 text-sm font-bold">إجمالي المواد</p><p class="text-4xl font-black mt-2"><?= $stats['courses'] ?></p></div>
                    <div class="text-5xl opacity-30">📚</div>
                </div>
            </div>
            <div class="stat-card rounded-2xl p-6 text-white shadow-lg" style="background: linear-gradient(135deg, #059669, #047857);">
                <div class="flex justify-between items-start">
                    <div><p class="text-emerald-100 text-sm font-bold">البرامج المتاحة</p><p class="text-4xl font-black mt-2"><?= $stats['departments'] ?></p></div>
                    <div class="text-5xl opacity-30">🎓</div>
                </div>
            </div>
            <div class="stat-card rounded-2xl p-6 text-white shadow-lg" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                <div class="flex justify-between items-start">
                    <div><p class="text-purple-100 text-sm font-bold">إجمالي الساعات</p><p class="text-4xl font-black mt-2"><?= $stats['credit_hours'] ?></p></div>
                    <div class="text-5xl opacity-30">⏱️</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Add Course -->
            <div class="lg:col-span-1">
                <div class="rounded-3xl shadow-lg p-6 border" style="background-color: var(--card-bg); border-color: var(--border);">
                    <h2 class="text-xl font-black mb-6 flex items-center gap-2" style="color: var(--text-primary);">
                        <span class="w-2 h-8 bg-blue-600 rounded-full"></span>
                        ➕ إضافة مادة جديدة
                    </h2>
                    <?php if($error_message): ?>
                        <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-sm border-r-4 border-red-500">⚠️ <?= $error_message ?></div>
                    <?php endif; ?>
                    <form method="POST" class="space-y-4">
                        <div><label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">اسم المادة *</label><input type="text" name="name" required placeholder="مثال: شبكات الحاسوب" class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"></div>
                        <div><label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">كود المادة *</label><input type="text" name="code" required placeholder="مثال: IT101" class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200 font-mono"></div>
                        <div><label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">البرنامج التكنولوجي *</label>
                            <select name="dept_id" required class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <option value="">-- اختر البرنامج --</option>
                                <?php foreach($depts as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div><label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">عدد الساعات المعتمدة</label>
                            <select name="credit_hours" class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <option value="1">1 ساعة</option><option value="2">2 ساعات</option><option value="3" selected>3 ساعات</option><option value="4">4 ساعات</option><option value="6">6 ساعات</option>
                            </select>
                        </div>
                        <div><label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">وصف المادة</label><textarea name="desc" rows="3" placeholder="وصف مختصر للمادة..." class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea></div>
                        <button type="submit" name="save_course" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-md">💾 حفظ المادة</button>
                    </form>
                </div>
            </div>

            <!-- Courses Table with Filters -->
            <div class="lg:col-span-2">
                <div class="rounded-3xl shadow-lg overflow-hidden border" style="background-color: var(--card-bg); border-color: var(--border);">
                    <div class="p-6 border-b" style="background-color: var(--card-bg-2); border-color: var(--border);">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4 flex-wrap">
                            <h3 class="font-black text-lg" style="color: var(--text-primary);">📋 قائمة المواد الدراسية</h3>
                            <div class="flex gap-3 flex-wrap">
                                <select id="deptFilter" class="px-3 py-2 rounded-xl border text-sm">
                                    <option value="all">📌 جميع البرامج</option>
                                    <?php foreach($depts as $d): ?>
                                        <option value="<?= htmlspecialchars($d['dept_name']) ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select id="hoursFilter" class="px-3 py-2 rounded-xl border text-sm">
                                    <option value="all">⏱️ جميع الساعات</option>
                                    <option value="1">1 ساعة</option><option value="2">2 ساعات</option><option value="3">3 ساعات</option><option value="4">4 ساعات</option><option value="6">6 ساعات</option>
                                </select>
                                <input type="text" id="searchInput" placeholder="🔍 بحث..." class="px-3 py-2 rounded-xl border w-full md:w-48 text-sm">
                            </div>
                        </div>
                        <div class="mt-3 text-sm" style="color: var(--text-muted);">
                            📊 <span id="filteredCount">0</span> نتيجة من <?= count($courses) ?> مادة
                        </div>
                    </div>
                    
                    <div class="table-container overflow-x-auto">
                        <table class="w-full" id="coursesTable">
                            <thead>
                                <tr style="background-color: var(--table-header);">
                                    <th class="p-4 text-right sortable-header" data-sort="code">الكود <span class="sort-icon">↕️</span></th>
                                    <th class="p-4 text-right sortable-header" data-sort="name">المادة <span class="sort-icon">↕️</span></th>
                                    <th class="p-4 text-right sortable-header" data-sort="dept">البرنامج <span class="sort-icon">↕️</span></th>
                                    <th class="p-4 text-center sortable-header" data-sort="hours">ساعات <span class="sort-icon">↕️</span></th>
                                    <th class="p-4 text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="coursesTableBody" class="divide-y" style="border-color: var(--border);">
                                <?php foreach($courses as $c): ?>
                                <tr class="course-row hover:bg-blue-50/40 transition" data-code="<?= htmlspecialchars($c['code']) ?>" data-name="<?= htmlspecialchars($c['name']) ?>" data-dept="<?= htmlspecialchars($c['dept_name']) ?>" data-hours="<?= $c['credit_hours'] ?>">
                                    <td class="p-4 font-mono font-bold text-blue-600 text-sm"><?= htmlspecialchars($c['code']) ?></td>
                                    <td class="p-4 font-bold" style="color: var(--text-primary);"><?= htmlspecialchars($c['name']) ?></td>
                                    <td class="p-4 text-sm" style="color: var(--text-secondary);"><?= htmlspecialchars($c['dept_name']) ?></td>
                                    <td class="p-4 text-center"><span class="inline-block px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold"><?= $c['credit_hours'] ?> س</span></td>
                                    <td class="p-4 text-center"><div class="flex justify-center gap-3"><a href="edit_course.php?id=<?= $c['id'] ?>" class="text-blue-500 hover:text-blue-700 font-bold text-sm">✏️ تعديل</a><button onclick="confirmDelete(<?= $c['id'] ?>)" class="text-red-400 hover:text-red-600 font-bold text-sm">🗑️ حذف</button></div></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(count($courses) == 0): ?>
                                <tr><td colspan="5" class="p-8 text-center" style="color: var(--text-muted);">📭 لا توجد مواد مسجلة حالياً</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-4 border-t" style="border-color: var(--border);">
                        <div class="flex justify-between items-center flex-wrap gap-3">
                            <div class="text-sm" style="color: var(--text-muted);">
                                عرض <span id="pageStart">0</span> - <span id="pageEnd">0</span> من <span id="totalCount">0</span>
                            </div>
                            <div class="flex gap-2">
                                <button id="prevPage" class="px-4 py-2 rounded-xl border text-sm transition hover:bg-gray-100 disabled:opacity-50" style="border-color: var(--border);">السابق</button>
                                <button id="nextPage" class="px-4 py-2 rounded-xl border text-sm transition hover:bg-gray-100 disabled:opacity-50" style="border-color: var(--border);">التالي</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Chart -->
    <div id="chartModal" class="modal-overlay">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" style="color: var(--text-primary);">📊 إحصائيات المواد حسب البرنامج</h3>
                <button onclick="closeChartModal()" class="text-2xl hover:text-red-500">&times;</button>
            </div>
            <canvas id="deptChart" style="max-height: 400px;"></canvas>
        </div>
    </div>

    <!-- Dark Mode Button -->
    <button class="dark-mode-btn" id="darkModeToggle" title="تبديل الوضع المظلم">🌙</button>

    <script>
        // ========== Dark Mode ==========
        function setDarkMode(isDark) {
            if (isDark) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
                document.getElementById('darkModeToggle').innerHTML = '☀️';
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
                document.getElementById('darkModeToggle').innerHTML = '🌙';
            }
        }
        if (localStorage.getItem('darkMode') === 'enabled') setDarkMode(true);
        else if (localStorage.getItem('darkMode') === 'disabled') setDarkMode(false);
        else if (window.matchMedia('(prefers-color-scheme: dark)').matches) setDarkMode(true);
        document.getElementById('darkModeToggle').addEventListener('click', () => setDarkMode(!document.body.classList.contains('dark-mode')));

        // ========== Data Management ==========
        let allRows = [];
        let currentPage = 1;
        const rowsPerPage = 10;
        let filteredRows = [];

        function collectRows() {
            allRows = Array.from(document.querySelectorAll('#coursesTableBody .course-row'));
            applyFilters();
        }

        function applyFilters() {
            const deptFilter = document.getElementById('deptFilter').value;
            const hoursFilter = document.getElementById('hoursFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            filteredRows = allRows.filter(row => {
                const dept = row.getAttribute('data-dept');
                const hours = row.getAttribute('data-hours');
                const code = row.getAttribute('data-code').toLowerCase();
                const name = row.getAttribute('data-name').toLowerCase();
                
                const deptMatch = deptFilter === 'all' || dept === deptFilter;
                const hoursMatch = hoursFilter === 'all' || hours === hoursFilter;
                const searchMatch = searchTerm === '' || code.includes(searchTerm) || name.includes(searchTerm);
                
                return deptMatch && hoursMatch && searchMatch;
            });
            
            document.getElementById('filteredCount').innerText = filteredRows.length;
            currentPage = 1;
            renderPage();
        }
        
        let currentSort = { column: null, direction: 'asc' };
        
        function sortRows(column) {
            if (currentSort.column === column) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.column = column;
                currentSort.direction = 'asc';
            }
            
            filteredRows.sort((a, b) => {
                let aVal = a.getAttribute(`data-${column}`);
                let bVal = b.getAttribute(`data-${column}`);
                if (column === 'hours') {
                    aVal = parseInt(aVal);
                    bVal = parseInt(bVal);
                }
                if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
                if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            });
            
            document.querySelectorAll('.sort-icon').forEach(icon => icon.classList.remove('active'));
            const activeIcon = document.querySelector(`.sortable-header[data-sort="${column}"] .sort-icon`);
            if (activeIcon) {
                activeIcon.classList.add('active');
                activeIcon.innerHTML = currentSort.direction === 'asc' ? '↑' : '↓';
            }
            
            renderPage();
        }
        
        function renderPage() {
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const pageRows = filteredRows.slice(start, end);
            
            const tbody = document.getElementById('coursesTableBody');
            tbody.innerHTML = '';
            
            if (pageRows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="p-8 text-center" style="color: var(--text-muted);">📭 لا توجد نتائج مطابقة</td></tr>';
            } else {
                pageRows.forEach(row => tbody.appendChild(row.cloneNode(true)));
            }
            
            const total = filteredRows.length;
            document.getElementById('pageStart').innerText = total === 0 ? 0 : start + 1;
            document.getElementById('pageEnd').innerText = Math.min(end, total);
            document.getElementById('totalCount').innerText = total;
            
            document.getElementById('prevPage').disabled = currentPage === 1;
            document.getElementById('nextPage').disabled = end >= total;
        }
        
        function prevPage() { if (currentPage > 1) { currentPage--; renderPage(); } }
        function nextPage() { if (currentPage * rowsPerPage < filteredRows.length) { currentPage++; renderPage(); } }
        
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('deptFilter').addEventListener('change', applyFilters);
        document.getElementById('hoursFilter').addEventListener('change', applyFilters);
        document.getElementById('prevPage').addEventListener('click', prevPage);
        document.getElementById('nextPage').addEventListener('click', nextPage);
        
        document.querySelectorAll('.sortable-header').forEach(header => {
            header.addEventListener('click', () => sortRows(header.getAttribute('data-sort')));
        });
        
        collectRows();
        
        // ========== Export Functions ==========
        function exportToExcel() {
            const table = document.getElementById('coursesTable');
            const ws = XLSX.utils.table_to_sheet(table);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'المواد الدراسية');
            XLSX.writeFile(wb, `courses_${new Date().toISOString().slice(0,19)}.xlsx`);
            Swal.fire('تم التصدير', 'تم تصدير البيانات إلى Excel بنجاح', 'success');
        }
        
        function exportToPDF() {
            const element = document.getElementById('coursesTable');
            const opt = { margin: 0.5, filename: `courses_${new Date().toISOString().slice(0,19)}.pdf`, image: { type: 'jpeg', quality: 0.98 }, html2canvas: { scale: 2 }, jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' } };
            html2pdf().set(opt).from(element).save();
        }
        
        function printTable() {
            const printContent = document.getElementById('coursesTable').outerHTML;
            const originalTitle = document.title;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html dir="rtl"><head><title>قائمة المواد الدراسية</title>
                <style>body { font-family: 'Tajawal', sans-serif; padding: 20px; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 8px; text-align: right; } th { background: #f1f5f9; }</style>
                </head><body>${printContent}</body></html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
        
        // ========== Chart Modal ==========
        let chartInstance = null;
        
        function openChartModal() {
            const modal = document.getElementById('chartModal');
            modal.style.display = 'flex';
            
            const ctx = document.getElementById('deptChart').getContext('2d');
            if (chartInstance) chartInstance.destroy();
            
            fetch('get_chart_data.php')
                .then(res => res.json())
                .then(data => {
                    chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: { labels: data.labels, datasets: [{ label: 'عدد المواد', data: data.values, backgroundColor: 'rgba(99, 102, 241, 0.7)', borderRadius: 10 }] },
                        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top', labels: { font: { family: 'Tajawal' } } } }, scales: { y: { beginAtZero: true, grid: { color: document.body.classList.contains('dark-mode') ? '#334155' : '#e2e8f0' } }, x: { ticks: { font: { family: 'Tajawal' } } } } }
                    });
                });
        }
        
        function closeChartModal() { document.getElementById('chartModal').style.display = 'none'; }
        
        // ========== Delete Confirmation ==========
        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟', text: "لا يمكن استعادة المادة بعد الحذف!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#2563eb', cancelButtonColor: '#ef4444', confirmButtonText: 'نعم، احذف', cancelButtonText: 'إلغاء',
                background: document.body.classList.contains('dark-mode') ? '#1e293b' : '#ffffff', color: document.body.classList.contains('dark-mode') ? '#f1f5f9' : '#1e293b'
            }).then((result) => { if (result.isConfirmed) window.location.href = 'manage_courses.php?del=' + id; });
        }
        
        // ========== Success Messages ==========
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') Swal.fire('تم الإضافة', 'تمت إضافة المادة بنجاح', 'success');
        else if (urlParams.get('status') === 'deleted') Swal.fire('تم الحذف', 'تم حذف المادة بنجاح', 'success');
        else if (urlParams.get('status') === 'updated') Swal.fire('تم التحديث', 'تم تعديل المادة بنجاح', 'success');
        
        // Close modal on outside click
        document.getElementById('chartModal').addEventListener('click', function(e) { if (e.target === this) closeChartModal(); });
    </script>
</body>
</html>