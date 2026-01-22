<?php
include('config.php');

$username = 'admin';
$password = '123456'; // รหัสผ่านตั้งต้น
$full_name = 'ผู้ดูแลระบบ รพ.บ้านนา';

// เข้ารหัสด้วยอัลกอริทึม BCRYPT
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO sys_users (username, password, full_name, role) VALUES (?, ?, ?, 'admin')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $full_name);

if (mysqli_stmt_execute($stmt)) {
    echo "สร้าง User Admin เรียบร้อยแล้ว!";
} else {
    echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
}
?>