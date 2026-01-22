<?php
// ฟังก์ชันสำหรับโหลดค่าจากไฟล์ .env (แบบไม่ต้องใช้ Library ภายนอก)
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // ข้ามบรรทัด comment
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// เรียกใช้ไฟล์ .env
loadEnv(__DIR__ . '/.env');

// ตั้งค่าตัวแปรจาก .env
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'Asia/Bangkok');

$host   = $_ENV['DB_HOST'];
$user   = $_ENV['DB_USERNAME'];
$pass   = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_DATABASE'];
$conn = mysqli_connect($host, $user, $pass, $dbname);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    $db_status = "<span style='color:red;'>เชื่อมต่อล้มเหลว</span>";
} else {
    $db_status = "<span style='color:green;'>เชื่อมต่อสำเร็จ</span>";
    mysqli_set_charset($conn, "utf8");
}
?>