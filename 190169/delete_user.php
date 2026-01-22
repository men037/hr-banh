<?php
include('auth.php'); 
checkSuperAdmin(); // ล็อคให้เฉพาะ Super Admin เท่านั้นที่เข้าได้
include('config.php');

// หมายเหตุ: ไม่ต้องใส่ session_start(); ซ้ำตรงนี้ เพราะมีอยู่ใน auth.php แล้ว

// 1. รับค่า ID ที่ต้องการลบ
if (isset($_GET['id'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_GET['id']);
    $current_user_id = $_SESSION['user_id'];

    // 2. ป้องกันการลบตัวเอง (Self-Deletion Prevention)
    if ($id_to_delete == $current_user_id) {
        echo "<script>
                alert('ผิดพลาด: คุณไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้');
                window.location.href = 'user_manage.php';
              </script>";
        exit();
    }

    // --- ส่วนเสริม: ดึงชื่อผู้ใช้ที่จะลบมาเก็บไว้ทำ Log ก่อนจะถูกลบจริง ---
    $sql_user = "SELECT username FROM sys_users WHERE id = '$id_to_delete'";
    $res_user = mysqli_query($conn, $sql_user);
    $user_data = mysqli_fetch_assoc($res_user);
    $target_name = $user_data['username'] ?? "ID: $id_to_delete";

    // 3. เริ่มการลบข้อมูลโดยใช้ Prepared Statement
    $sql = "DELETE FROM sys_users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_to_delete);

    if (mysqli_stmt_execute($stmt)) {
        
        // ✅ --- บันทึก LOG เมื่อลบผู้ใช้งานสำเร็จ ---
        $log_details = "ลบผู้ใช้งานระบบ: $target_name (ID: $id_to_delete)";
        write_log($conn, 'DELETE', 'sys_users', $id_to_delete, $log_details);
        // --------------------------------------

        echo "<script>
                alert('ลบบัญชีผู้ใช้งานเรียบร้อยแล้ว');
                window.location.href = 'user_manage.php';
              </script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: user_manage.php");
}

mysqli_close($conn);
?>