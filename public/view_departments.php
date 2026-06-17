<?php
require_once __DIR__ . '/../includes/auth.php';
checkStudent();

require_once __DIR__ . '/../classes/Department.php';
require_once __DIR__ . '/../classes/Course.php';

$dept = new Department();
$course = new Course();

$departments = $dept->getAll();

foreach ($departments as &$d) {
    $courses = $course->getByDepartment($d['id']);
    $d['courses_count'] = count($courses);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <title>البرامج التكنولوجية - جامعة برج العرب</title>
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
            --hover-bg: #f0f9ff;
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
            --gradient-header: linear-gradient(135deg, #4338ca, #3730a3);
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
            --hover-bg: #334155;
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.3);
            --gradient-header: linear-gradient(135deg, #1e1b4b, #2e1065);
        }
        
        body {
            background: linear-gradient(to bottom right, var(--bg-from), var(--bg-to));
            min-height: 100vh;
        }
        
        .bg-white { background-color: var(--card-bg) !important; }
        .text-slate-700, .text-slate-800, h1, h3 { color: var(--text-primary) !important; }
        .text-slate-500, .text-slate-400 { color: var(--text-secondary) !important; }
        .border-slate-100 { border-color: var(--border) !important; }
        .bg-slate-50 { background-color: var(--card-bg-2) !important; }
        .hover\:bg-indigo-50:hover { background-color: var(--hover-bg) !important; }
        
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
        
        a:not(.dark-mode-btn) { color: #2563eb; }
        body.dark-mode a:not(.dark-mode-btn) { color: #60a5fa; }
        a:hover:not(.dark-mode-btn) { color: #1d4ed8; }
        
        .rounded-xl { background-color: var(--card-bg-2); border: 1px solid var(--border); }
        .shadow-xl { box-shadow: var(--shadow-xl); }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

    <div class="max-w-5xl mx-auto">
        <a href="student_dashboard.php" class="inline-flex items-center gap-2 font-bold mb-6 hover:opacity-80 transition">
            ← العودة إلى لوحة التحكم
        </a>

        <div class="rounded-3xl shadow-xl overflow-hidden" style="background-color: var(--card-bg);">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-700 to-indigo-800 p-8 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-black">🏛️ البرامج التكنولوجية المعتمدة</h1>
                        <p class="text-indigo-200 mt-2">جامعة برج العرب التكنولوجية</p>
                    </div>
                    <div class="text-6xl opacity-30">🎓</div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-8">
                <?php if(count($departments) > 0): ?>
                    <div class="grid gap-4">
                        <?php foreach($departments as $dept): ?>
                            <div class="flex justify-between items-center p-5 rounded-xl hover:shadow-md transition group" style="background-color: var(--card-bg-2);">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center font-black text-lg" style="color: #4f46e5;">
                                        #<?= str_pad($dept['id'], 2, '0', STR_PAD_LEFT) ?>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg" style="color: var(--text-primary);"><?= htmlspecialchars($dept['name']) ?></h3>
                                        <p class="text-sm" style="color: var(--text-secondary);">
                                            📚 عدد المواد: <?= $dept['courses_count'] ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 bg-green-100 rounded-full text-xs font-bold flex items-center gap-1">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        نشط
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12" style="color: var(--text-muted);">
                        📭 لا توجد برامج تكنولوجية مسجلة
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Dark Mode Button -->
    <button class="dark-mode-btn" id="darkModeToggle" title="تبديل الوضع المظلم">
        🌙
    </button>

    <script>
        function setDarkMode(isDark) {
            if (isDark) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
                document.getElementById('darkModeToggle').innerHTML = '☀️';
                document.getElementById('darkModeToggle').title = 'الوضع الفاتح';
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
                document.getElementById('darkModeToggle').innerHTML = '🌙';
                document.getElementById('darkModeToggle').title = 'الوضع المظلم';
            }
        }
        
        if (localStorage.getItem('darkMode') === 'enabled') setDarkMode(true);
        else if (localStorage.getItem('darkMode') === 'disabled') setDarkMode(false);
        else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) setDarkMode(true);
        
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            const isDark = document.body.classList.contains('dark-mode');
            setDarkMode(!isDark);
        });
    </script>
</body>
</html>