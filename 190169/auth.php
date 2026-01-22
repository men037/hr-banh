<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 1. ตรวจสอบการ Login และ Session Timeout ---

// ถ้าไม่ได้ Login เลย ให้เด้งไปหน้า Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// กำหนดเวลา Timeout (1800 วินาที = 30 นาที)
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
// อัปเดตเวลาการใช้งานครั้งล่าสุดเสมอ
$_SESSION['last_activity'] = time();


// --- 2. ฟังก์ชันตรวจสอบสิทธิ์ (Permissions) ---

// ตรวจสอบว่าเป็น Admin หรือ Super Admin (สำหรับหน้าจัดการทั่วไป)
if (!function_exists('checkAdmin')) {
    function checkAdmin() {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin')) {
            echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงส่วนนี้ (เฉพาะ Admin เท่านั้น)'); window.location.href='index.php';</script>";
            exit();
        }
    }
}

// ตรวจสอบว่าเป็น Super Admin เท่านั้น (สำหรับหน้าจัดการผู้ใช้งาน)
if (!function_exists('checkSuperAdmin')) {
    function checkSuperAdmin() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
            echo "<script>
                    alert('สิทธิ์ของคุณไม่เพียงพอ: หน้าจัดการผู้ใช้งานเข้าได้เฉพาะ Super Admin เท่านั้น'); 
                    window.location.href='index.php'; 
                  </script>";
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