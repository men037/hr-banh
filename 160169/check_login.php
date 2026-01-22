<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM sys_users WHERE username = ? AND status = 'Y'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // ตรวจสอบรหัสผ่านที่กรอกมา เทียบกับชุดรหัสที่เข้ารหัสไว้ใน DB
    if ($user && password_verify($password, $user['password'])) {
        // Login สำเร็จ
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        // อัปเดตเวลาล็อกอินล่าสุด
        mysqli_query($conn, "UPDATE sys_users SET last_login = NOW() WHERE id = " . $user['id']);

        header("Location: index.php");
    } else {
        // Login ไม่สำเร็จ
        echo "<script>alert('ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง'); window.history.back();</script>";
    }
}
?>