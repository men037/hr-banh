<?php
include('auth.php'); 
checkSuperAdmin(); // ล็อคให้เฉพาะ Super Admin เท่านั้นที่เข้าได้
include('config.php');
session_start();

// 1. ระบบความปลอดภัย: ตรวจสอบว่าล็อกอินหรือยัง และต้องเป็น Super Admin เท่านั้น
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    echo "<script>
            alert('ขออภัย: เฉพาะ Super Admin เท่านั้นที่มีสิทธิ์ลบบัญชีผู้ใช้');
            window.location.href = 'user_manage.php';
          </script>";
    exit();
}

// 2. รับค่า ID ที่ต้องการลบ
if (isset($_GET['id'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_GET['id']);
    $current_user_id = $_SESSION['user_id'];

    // 3. ป้องกันการลบตัวเอง (Self-Deletion Prevention)
    if ($id_to_delete == $current_user_id) {
        echo "<script>
                alert('ผิดพลาด: คุณไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้');
                window.location.href = 'user_manage.php';
              </script>";
        exit();
    }

    // 4. เริ่มการลบข้อมูลโดยใช้ Prepared Statement
    $sql = "DELETE FROM sys_users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_to_delete);

    if (mysqli_stmt_execute($stmt)) {
        // ลบสำเร็จ
        echo "<script>
                alert('ลบบัญชีผู้ใช้งานเรียบร้อยแล้ว');
                window.location.href = 'user_manage.php';
              </script>";
    } else {
        // กรณีเกิดข้อผิดพลาดจากฐานข้อมูล
        echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    // กรณีไม่มีการส่ง ID มา
    header("Location: user_manage.php");
}

mysqli_close($conn);
?>