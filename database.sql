-- =============================================
-- University Portal System — Database Schema
-- Web Development II Project
-- =============================================

-- إنشاء قاعدة البيانات إذا لم تكن موجودة
CREATE DATABASE IF NOT EXISTS university_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE university_portal;

-- =============================================
-- 1. جدول المستخدمين (users)
-- يحتوي على بيانات الأدمن والطلاب معاً
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)                        NOT NULL,
    email       VARCHAR(100)                        NOT NULL UNIQUE,
    password    VARCHAR(255)                        NOT NULL, -- يتم تخزين الهاش فقط باستخدام password_hash()
    role        ENUM('admin', 'student')            NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إدراج حساب أدمن تجريبي (الباسورد: password — مشفر بـ BCRYPT)
INSERT IGNORE INTO users (name, email, password, role)
VALUES ('Super Admin', 'admin@university.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =============================================
-- 2. جدول الأقسام (departments)
-- =============================================
CREATE TABLE IF NOT EXISTS departments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)    NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- 3. جدول الكورسات (courses)
-- code: كود المادة المختصر مثل CS101
-- department_id: مرتبط بجدول departments
-- =============================================
CREATE TABLE IF NOT EXISTS courses (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150)    NOT NULL,
    code            VARCHAR(20)     NOT NULL UNIQUE,      -- كود المادة (مثل CS101)
    description     TEXT,
    department_id   INT             NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Foreign Key: ربط المادة بالقسم مع الحذف التتالي
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- =============================================
-- 4. جدول الأخبار والإعلانات (news)
-- created_by: المستخدم الأدمن الذي نشر الخبر
-- published_at: تاريخ النشر
-- =============================================
CREATE TABLE IF NOT EXISTS news (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(200)    NOT NULL,
    content         TEXT            NOT NULL,
    published_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by      INT             NOT NULL,
    -- Foreign Key: ربط الخبر بالمستخدم (الأدمن) الذي أنشأه
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 5. جدول التسجيل في الكورسات (enrollments) — اختياري / Bonus
-- UNIQUE: يمنع الطالب من التسجيل مرتين في نفس المادة
-- =============================================
CREATE TABLE IF NOT EXISTS enrollments (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    student_id  INT             NOT NULL,
    course_id   INT             NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id)  REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, course_id) -- منع التكرار
);
