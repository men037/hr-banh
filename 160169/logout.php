<?php
// 1. เริ่มต้นการเรียกใช้ Session
session_start();

// 2. ล้างตัวแปร Session ทั้งหมดที่มี
$_SESSION = array();

// 3. หากต้องการลบ Cookie ของ Session ด้วย (เพื่อความปลอดภัยสูงสุด)
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