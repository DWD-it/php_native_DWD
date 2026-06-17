-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS `university_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `university_portal`;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'student') DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول الأقسام
CREATE TABLE IF NOT EXISTS `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- جدول المواد
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `department_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `code` VARCHAR(50) UNIQUE NOT NULL,
    `description` TEXT,
    `credit_hours` INT DEFAULT 3,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- جدول التسجيل
CREATE TABLE IF NOT EXISTS `enrollments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_enrollment` (`student_id`, `course_id`)
) ENGINE=InnoDB;

-- جدول الأخبار
CREATE TABLE IF NOT EXISTS `news` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_by` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `published_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- إدخال مستخدم أدمن افتراضي (admin@university.edu / admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin', 'admin@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- إدخال أقسام تجريبية
INSERT INTO `departments` (`name`, `description`) VALUES
('Computer Science', 'برنامج علوم الحاسوب'),
('Information Technology', 'برنامج تكنولوجيا المعلومات'),
('Artificial Intelligence', 'برنامج الذكاء الاصطناعي'),
('Cybersecurity', 'برنامج أمن المعلومات');

-- إدخال مواد تجريبية
INSERT INTO `courses` (`department_id`, `name`, `code`, `description`, `credit_hours`) VALUES
(1, 'Programming Fundamentals', 'CS101', 'أساسيات البرمجة', 3),
(1, 'Data Structures', 'CS201', 'هياكل البيانات', 3),
(2, 'Database Systems', 'IT201', 'أنظمة قواعد البيانات', 4),
(2, 'Web Development', 'IT301', 'تطوير تطبيقات الويب', 3),
(3, 'Machine Learning', 'AI301', 'تعلم الآلة', 3),
(4, 'Network Security', 'CY401', 'أمن الشبكات', 3);

-- إدخال بعض الأخبار
INSERT INTO `news` (`created_by`, `title`, `content`) VALUES
(1, 'مرحباً بكم في الفصل الدراسي الجديد', 'نتمنى لكم عاماً دراسياً موفقاً...'),
(1, 'موعد امتحانات منتصف الفصل', 'سيتم عقد امتحانات منتصف الفصل بتاريخ...');