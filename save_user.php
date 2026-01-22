<?php
// 1. ดึงไฟล์ตั้งค่าและระบบความปลอดภัย (auth.php มี session_start และ write_log อยู่แล้ว)
include('auth.php'); 
checkAdmin(); // ป้องกันคนแอบเข้าหน้าบันทึกโดยไม่ได้รับอนุญาต
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์มและทำความสะอาดข้อมูลเบื้องต้น
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $password  = $_POST['password']; // รับรหัสผ่านดิบมาเพื่อทำการ hash
    $role      = mysqli_real_escape_string($conn, $_POST['role']);

    // 2. ตรวจสอบว่า Username นี้ถูกใช้ไปแล้วหรือยัง
    $check_sql = "SELECT id FROM sys_users WHERE username = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        echo "<script>
                alert('ขออภัย: ชื่อผู้ใช้งาน \"$username\" นี้ถูกใช้ไปแล้ว กรุณาใช้ชื่ออื่น');
                window.history.back();
              </script>";
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // 3. เข้ารหัสผ่าน
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. บันทึกข้อมูลลงฐานข้อมูล
    $insert_sql = "INSERT INTO sys_users (full_name, username, password, role, status) VALUES (?, ?, ?, ?, 'Y')";
    $stmt_insert = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt_insert, "ssss", $full_name, $username, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt_insert)) {
        
        // ดึง ID ล่าสุดที่เพิ่ง Insert เข้าไปเพื่อเก็บใน Log
        $new_user_id = mysqli_insert_id($conn);

        // ✅ --- บันทึก LOG เมื่อเพิ่มผู้ใช้งานระบบสำเร็จ ---
        $log_details = "เพิ่มผู้ใช้งานระบบใหม่: $full_name (Username: $username, Role: $role)";
        write_log($conn, 'ADD', 'sys_users', $new_user_id, $log_details);
        // ------------------------------------------

        echo "<script>
                alert('เพิ่มผู้ใช้งาน $full_name เรียบร้อยแล้ว');
                window.location.href = 'user_manage.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt_insert);
}

mysqli_close($conn);
?>