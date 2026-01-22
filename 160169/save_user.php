<?php
include('config.php');

// เริ่มต้น Session เพื่อใช้เก็บข้อความแจ้งเตือน (ถ้าต้องการ)
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์มและทำความสะอาดข้อมูลเบื้องต้น
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $password  = $_POST['password']; // รับรหัสผ่านดิบมาเพื่อทำการ hash
    $role      = mysqli_real_escape_string($conn, $_POST['role']);

    // 1. ตรวจสอบว่า Username นี้ถูกใช้ไปแล้วหรือยัง (เพื่อป้องกัน Duplicate Entry Error)
    $check_sql = "SELECT id FROM sys_users WHERE username = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        // ถ้าเจอชื่อซ้ำ ให้เด้งกลับและแจ้งเตือน
        echo "<script>
                alert('ขออภัย: ชื่อผู้ใช้งาน \"$username\" นี้ถูกใช้ไปแล้ว กรุณาใช้ชื่ออื่น');
                window.history.back();
              </script>";
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // 2. เข้ารหัสผ่านด้วย password_hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. บันทึกข้อมูลลงฐานข้อมูลโดยใช้ Prepared Statement
    $insert_sql = "INSERT INTO sys_users (full_name, username, password, role, status) VALUES (?, ?, ?, ?, 'Y')";
    $stmt_insert = mysqli_prepare($conn, $insert_sql);
    
    // "ssss" หมายถึงข้อมูลทั้ง 4 ตัวเป็น String
    mysqli_stmt_bind_param($stmt_insert, "ssss", $full_name, $username, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt_insert)) {
        // บันทึกสำเร็จ กลับไปหน้าจัดการผู้ใช้งาน
        echo "<script>
                alert('เพิ่มผู้ใช้งาน $full_name เรียบร้อยแล้ว');
                window.location.href = 'user_manage.php';
              </script>";
    } else {
        // กรณีเกิดความผิดพลาดอื่นๆ
        echo "Error: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt_insert);
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);
?>