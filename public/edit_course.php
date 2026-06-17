<?php
require_once '../config/database.php';
require_once '../classes/University.php';

$university = new University($conn);
$error_message = "";

// التحقق من وجود ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_courses.php");
    exit();
}

$course_id = (int)$_GET['id'];
$course_data = $university->getCourseById($course_id);

if (!$course_data) {
    die("<div class='p-8 text-center text-red-600 font-bold'>⚠️ المادة غير موجودة!</div>");
}

// معالجة التحديث
if (isset($_POST['update_course'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $code = htmlspecialchars(trim($_POST['code']));
    $desc = htmlspecialchars(trim($_POST['desc']));
    $dept_id = (int)$_POST['dept_id'];
    $credit_hours = (int)$_POST['credit_hours'];

    if (!empty($name) && !empty($code) && $dept_id > 0) {
        if (!$university->isCodeExists($code, $course_id)) {
            if ($university->updateCourse($course_id, $name, $code, $desc, $dept_id, $credit_hours)) {
                header("Location: manage_courses.php?status=updated");
                exit();
            } else {
                $error_message = "حدث خطأ أثناء تحديث المادة!";
            }
        } else {
            $error_message = "عذراً، هذا الكود ($code) مستخدم لمادة أخرى!";
        }
    } else {
        $error_message = "الرجاء ملء جميع الحقول المطلوبة!";
    }
}

$depts = $university->getDepartments();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <title>تعديل مادة - جامعة برج العرب</title>
    <style>
        body { font-family: 'Tajawal', sans-serif; transition: background-color 0.3s ease, color 0.3s ease; }
        
        /* ========== الوضع الفاتح (الافتراضي) ========== */
        :root {
            --bg-from: #f8fafc;
            --bg-to: #f1f5f9;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --input-bg: #ffffff;
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        
        /* ========== الوضع المظلم ========== */
        body.dark-mode {
            --bg-from: #0f172a;
            --bg-to: #1e293b;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #334155;
            --input-bg: #0f172a;
            --shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.3);
        }
        
        body {
            background: linear-gradient(to bottom right, var(--bg-from), var(--bg-to));
            min-height: 100vh;
        }
        
        /* تطبيق المتغيرات */
        .bg-white {
            background-color: var(--card-bg) !important;
        }
        
        .text-slate-600, .text-slate-700, .text-slate-800 {
            color: var(--text-primary) !important;
        }
        
        .text-slate-500, .text-slate-400 {
            color: var(--text-secondary) !important;
        }
        
        .border-slate-100, .border-slate-200 {
            border-color: var(--border) !important;
        }
        
        input, select, textarea {
            background-color: var(--input-bg) !important;
            color: var(--text-primary) !important;
            border-color: var(--border) !important;
        }
        
        input::placeholder, textarea::placeholder {
            color: var(--text-muted) !important;
        }
        
        .bg-red-50 {
            background-color: #fee2e2 !important;
        }
        body.dark-mode .bg-red-50 {
            background-color: #450a0a !important;
        }
        
        /* زر تبديل الوضع المظلم */
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
        .dark-mode-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        /* تحسين الروابط */
        a {
            color: #2563eb;
        }
        body.dark-mode a {
            color: #60a5fa;
        }
        a:hover {
            color: #1d4ed8;
        }
        
        /* تحسين الظلال */
        .shadow-xl {
            box-shadow: var(--shadow-xl);
        }
        
        /* تحسين زر الحفظ */
        .btn-save {
            background-color: #2563eb;
        }
        body.dark-mode .btn-save {
            background-color: #1d4ed8;
        }
        .btn-save:hover {
            background-color: #1d4ed8;
        }
        body.dark-mode .btn-save:hover {
            background-color: #1e40af;
        }
        
        /* تحسين زر الإلغاء */
        .btn-cancel {
            background-color: #f1f5f9;
        }
        body.dark-mode .btn-cancel {
            background-color: #334155;
            color: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

    <div class="max-w-2xl mx-auto">
        <a href="manage_courses.php" class="inline-flex items-center gap-2 font-bold mb-6 hover:opacity-80 transition">
            ← العودة إلى قائمة المواد
        </a>

        <div class="rounded-3xl shadow-xl p-8 border" style="background-color: var(--card-bg); border-color: var(--border);">
            <h2 class="text-2xl font-black mb-6 flex items-center gap-2" style="color: var(--text-primary);">
                <span class="w-2 h-8 bg-yellow-500 rounded-full"></span>
                ✏️ تعديل بيانات المادة
            </h2>

            <?php if($error_message): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-sm border-r-4 border-red-500">
                    ⚠️ <?= $error_message ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">اسم المادة *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($course_data['name']) ?>" required
                           class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">كود المادة *</label>
                    <input type="text" name="code" value="<?= htmlspecialchars($course_data['code']) ?>" required
                           class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200 font-mono">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">البرنامج التكنولوجي *</label>
                    <select name="dept_id" required class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <?php foreach($depts as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= ($d['id'] == $course_data['dept_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['dept_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">عدد الساعات المعتمدة</label>
                    <select name="credit_hours" class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200">
                        <option value="1" <?= $course_data['credit_hours'] == 1 ? 'selected' : '' ?>>1 ساعة</option>
                        <option value="2" <?= $course_data['credit_hours'] == 2 ? 'selected' : '' ?>>2 ساعات</option>
                        <option value="3" <?= $course_data['credit_hours'] == 3 ? 'selected' : '' ?>>3 ساعات</option>
                        <option value="4" <?= $course_data['credit_hours'] == 4 ? 'selected' : '' ?>>4 ساعات</option>
                        <option value="6" <?= $course_data['credit_hours'] == 6 ? 'selected' : '' ?>>6 ساعات</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2" style="color: var(--text-secondary);">وصف المادة</label>
                    <textarea name="desc" rows="4" class="w-full px-4 py-3 rounded-xl border focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200"><?= htmlspecialchars($course_data['description']) ?></textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" name="update_course" class="btn-save flex-1 text-white font-bold py-3 rounded-xl transition shadow-md">
                        💾 حفظ التعديلات
                    </button>
                    <a href="manage_courses.php" class="btn-cancel flex-1 text-center font-bold py-3 rounded-xl transition">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- زر تبديل الوضع المظلم -->
    <button class="dark-mode-btn" id="darkModeToggle" title="تبديل الوضع المظلم">
        🌙
    </button>

    <script>
        // ========== Dark Mode Functionality ==========
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
        
        // التحقق من الوضع المحفوظ
        if (localStorage.getItem('darkMode') === 'enabled') {
            setDarkMode(true);
        } else if (localStorage.getItem('darkMode') === 'disabled') {
            setDarkMode(false);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            setDarkMode(true);
        }
        
        // حدث الضغط على الزر
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            const isDark = document.body.classList.contains('dark-mode');
            setDarkMode(!isDark);
        });

        // رسائل النجاح
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'updated') {
            Swal.fire({
                title: 'تم التحديث',
                text: 'تم تعديل المادة بنجاح',
                icon: 'success',
                background: document.body.classList.contains('dark-mode') ? '#1e293b' : '#ffffff',
                color: document.body.classList.contains('dark-mode') ? '#f1f5f9' : '#1e293b'
            });
        }
    </script>
</body>
</html>