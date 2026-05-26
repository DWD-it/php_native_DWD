<?php
/**
 * Session Management & Role-Based Access Control (RBAC)
 */
session_start();

// Prevent caching to ensure secure logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /university_portal/public/index.php");
        exit();
    }
}

function checkAdmin() {
    checkLogin();
    if ($_SESSION['user_role'] !== 'admin') {
        header("Location: /university_portal/public/student_dashboard.php");
        exit();
    }
}

function checkStudent() {
    checkLogin();
    if ($_SESSION['user_role'] !== 'student') {
        header("Location: /university_portal/public/admin_dashboard.php");
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
