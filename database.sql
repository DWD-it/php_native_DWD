-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS `university_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `university_db`;

-- جدول البرامج التكنولوجية (Departments)
CREATE TABLE IF NOT EXISTS `departments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `dept_name` VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- جدول المواد (Courses) مع عمود credit_hours
CREATE TABLE IF NOT EXISTS `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `credit_hours` INT DEFAULT 3,
  `dept_id` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`dept_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- إدخال البرامج التكنولوجية
INSERT INTO `departments` (dept_name) VALUES 
('برنامج تكنولوجيا السكك الحديدية'),
('برنامج تكنولوجيا المعلومات'),
('برنامج تكنولوجيا تشغيل وصيانة معدات الغزل والنسيج'),
('برنامج تكنولوجيا الصناعات الغذائية'),
('برنامج تكنولوجيا الجرارات والمعدات الزراعية');

-- إضافة بعض المواد التجريبية
INSERT INTO `courses` (name, code, description, credit_hours, dept_id) VALUES
('شبكات الحاسوب', 'IT101', 'دراسة أساسيات شبكات الحاسوب والبروتوكولات', 3, 2),
('أمن المعلومات', 'IT102', 'مبادئ أمن المعلومات والتشفير', 3, 2),
('قواعد البيانات', 'IT103', 'تصميم وإدارة قواعد البيانات', 4, 2),
('صيانة الجرارات', 'AG201', 'صيانة وإصلاح الجرارات الزراعية', 3, 5),
('تصنيع الأغذية', 'FD301', 'عمليات تصنيع المواد الغذائية', 3, 4);