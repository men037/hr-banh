<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. ถ้าไม่ได้ Login เลย ให้เด้งไปหน้า Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. ฟังก์ชันตรวจสอบสิทธิ์ Admin ขึ้นไป
function checkAdmin() {
    if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin') {
        echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (เฉพาะ Admin เท่านั้น)'); window.location.href='index.php';</script>";
        exit();
    }
}

// 3. ฟังก์ชันตรวจสอบสิทธิ์ Super Admin เท่านั้น
function checkSuperAdmin() {
    if ($_SESSION['role'] !== 'super_admin') {
        echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (เฉพาะ Super Admin เท่านั้น)'); window.location.href='user_manage.php';</script>";
        exit();
    }
}
?>