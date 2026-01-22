<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 1. ตรวจสอบการ Login และ Session Timeout ---

// ใช้ชื่อ user_id ให้เหมือนกันทั้งระบบ
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// กำหนดเวลา Timeout (30 นาที)
$timeout_duration = 1800; 

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?error=timeout");
        exit();
    }
}
$_SESSION['last_activity'] = time();


// --- 2. ฟังก์ชันตรวจสอบสิทธิ์ (Permissions) ---

if (!function_exists('checkAdmin')) {
    function checkAdmin() {
        // เช็คชื่อบทบาทให้ตรงกับ Database (admin / super_admin)
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin')) {
            echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงส่วนนี้'); window.location.href='index.php';</script>";
            exit();
        }
    }
}

if (!function_exists('checkSuperAdmin')) {
    function checkSuperAdmin() {
        // เช็คชื่อบทบาทให้ตรงกับ Database (super_admin)
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
            echo "<script>alert('เฉพาะ Super Admin เท่านั้น'); window.location.href='index.php';</script>";
            exit();
        }
    }
}

// --- 3. ฟังก์ชันบันทึก Log ระบบ ---

if (!function_exists('write_log')) {
    function write_log($conn, $action, $table, $id = null, $details = null) {
        $user_id = $_SESSION['user_id'] ?? 0;
        
        $sql = "INSERT INTO sys_logs (user_id, action, target_table, target_id, details) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issss", $user_id, $action, $table, $id, $details);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
?>