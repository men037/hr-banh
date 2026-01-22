<?php
// 1. เริ่มต้นการเรียกใช้ Session และไฟล์ที่จำเป็น
session_start();
include('config.php'); // ต้องเชื่อมต่อฐานข้อมูล
include('auth.php');   // ต้องมีฟังก์ชัน write_log

// ✅ บันทึก LOG ก่อนจะทำลาย Session
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $details = "ออกจากระบบ (IP: $user_ip)";
    
    // เรียกใช้ฟังก์ชันบันทึก Log
    write_log($conn, 'LOGOUT', 'sys_users', $user_id, $details);
}

// 2. ล้างตัวแปร Session ทั้งหมดที่มี
$_SESSION = array();

// 3. หากต้องการลบ Cookie ของ Session ด้วย
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. ทำลาย Session ทั้งหมด
session_destroy();

// 5. ส่งผู้ใช้งานกลับไปหน้า Login
header("Location: login.php");
exit();
?>