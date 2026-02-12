<?php
include('auth.php');
include('config.php');
checkSuperAdmin();

header('Content-Type: application/json');


// และอัปเดตเฉพาะ M.idmoph ที่เป็น NULL หรือว่าง
$sql = "UPDATE staff_main M
        INNER JOIN staff_moph_retrieve R ON CONCAT(M.fname,M.lname) = CONCAT(R.first_name,R.last_name)
        SET M.idmoph = R.moph_id
        WHERE M.idmoph IS NULL OR M.idmoph = '' OR M.idmoph = 0";

if (mysqli_query($conn, $sql)) {
    $affected_rows = mysqli_affected_rows($conn);
    echo json_encode([
        'status' => 'success', 
        'message' => "อัปเดต ID MOPH สำเร็จทั้งหมด $affected_rows รายการ"
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'เกิดข้อผิดพลาด: ' . mysqli_error($conn)
    ]);
}
?>