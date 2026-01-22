<?php
// 1. เปิดแจ้งเตือน Error เพื่อดูว่าติดตรงไหน
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 2. เริ่ม Session (ถ้ายังไม่มี)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('config.php');

// ฟังก์ชัน Log แบบสร้างขึ้นมาเฉพาะในหน้านี้ เพื่อลดการ Include ไฟล์ซ้ำซ้อน
function write_login_log($conn, $user_id, $details) {
    $sql = "INSERT INTO sys_logs (user_id, action, target_table, target_id, details) 
            VALUES (?, 'LOGIN', 'sys_users', ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $user_id, $details);
    mysqli_stmt_execute($stmt);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // ค้นหา User
    $sql = "SELECT * FROM sys_users WHERE username = ? AND status = 'Y'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // --- Login สำเร็จ ---
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        // อัปเดต Last Login
        mysqli_query($conn, "UPDATE sys_users SET last_login = NOW() WHERE id = " . $user['id']);

        // ✅ บันทึก Log โดยใช้ฟังก์ชันที่เราสร้างไว้ด้านบน
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $details = "เข้าสู่ระบบสำเร็จ (IP: $user_ip)";
        write_login_log($conn, $user['id'], $details);

        // ส่งไปหน้าแรก
        header("Location: index.php");
        exit(); 

    } else {
        // Login ไม่สำเร็จ
        echo "<script>alert('ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง'); window.history.back();</script>";
        exit();
    }
}
?>