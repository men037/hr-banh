<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "banna_hospital";

$conn = mysqli_connect($host, $user, $pass, $dbname);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    $db_status = "<span style='color:red;'>เชื่อมต่อล้มเหลว</span>";
} else {
    $db_status = "<span style='color:green;'>เชื่อมต่อสำเร็จ</span>";
    mysqli_set_charset($conn, "utf8");
}
?>