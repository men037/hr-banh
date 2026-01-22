<?php
include('config.php');

// 1. ตั้งค่า Timezone ให้ตรงกับประเทศไทย
date_default_timezone_set('Asia/Bangkok');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 2. รับค่าและป้องกัน SQL Injection
    $cid = mysqli_real_escape_string($conn, $_POST['cid']);
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $license_no = mysqli_real_escape_string($conn, $_POST['license_no']);
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    
    // 3. จัดการข้อมูลรหัสอ้างอิง (ใส่ ' ' ครอบสำหรับค่าที่มีตัวอักษร)
    $prefix_id = mysqli_real_escape_string($conn, $_POST['prefix_id']);
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $provider_pos_id = mysqli_real_escape_string($conn, $_POST['provider_pos_id']);
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $dept_id = mysqli_real_escape_string($conn, $_POST['dept_id']);

    // 4. จัดการข้อมูล ID ที่เป็นตัวเลข (ถ้าว่างให้เป็น NULL)
    $gender_id = !empty($_POST['gender_id']) ? $_POST['gender_id'] : "NULL";
    $prefix_academic_id = !empty($_POST['prefix_academic_id']) ? $_POST['prefix_academic_id'] : "NULL";
    $type_id = !empty($_POST['type_id']) ? $_POST['type_id'] : "NULL";

    // 5. จัดการข้อมูลวันที่ (ถ้าว่างให้เป็น NULL)
    $birthday = !empty($_POST['birthday']) ? "'" . mysqli_real_escape_string($conn, $_POST['birthday']) . "'" : "NULL";
    $start_date = !empty($_POST['start_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['start_date']) . "'" : "NULL";
    $end_date = !empty($_POST['end_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['end_date']) . "'" : "NULL";

    // 6. สร้างเวลาปัจจุบันสำหรับช่อง updated_at
    $updated_at = date('Y-m-d H:i:s');

    // 7. ข้อมูลสถานะ
    $provider_status = isset($_POST['provider_status']) ? $_POST['provider_status'] : 'N';
    $ekyc_status = isset($_POST['ekyc_status']) ? $_POST['ekyc_status'] : 'N';
    $work_status = isset($_POST['work_status']) ? $_POST['work_status'] : 'Y';

    // 8. ตรวจสอบเลขบัตรประชาชนซ้ำ
    $check = mysqli_query($conn, "SELECT cid FROM staff_main WHERE cid = '$cid'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('ผิดพลาด: เลขบัตรประชาชน $cid มีในระบบแล้ว'); window.history.back();</script>";
        exit;
    }

    // 9. คำสั่ง SQL INSERT (ตัด created_at ออก เหลือเพียง updated_at)
    $sql = "INSERT INTO staff_main (
                cid, staff_id, license_no, fname, lname, 
                gender_id, prefix_id, prefix_academic_id, type_id, 
                position_id, provider_pos_id, group_id, dept_id, 
                birthday, start_date, end_date, 
                provider_status, ekyc_status, work_status, 
                updated_at
            ) VALUES (
                '$cid', '$staff_id', '$license_no', '$fname', '$lname', 
                $gender_id, '$prefix_id', $prefix_academic_id, $type_id, 
                '$position_id', '$provider_pos_id', '$group_id', '$dept_id', 
                $birthday, $start_date, $end_date, 
                '$provider_status', '$ekyc_status', '$work_status', 
                '$updated_at'
            )";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('บันทึกข้อมูลเจ้าหน้าที่เรียบร้อย'); window.location.href='index.php';</script>";
    } else {
        echo "<h4>Error:</h4> " . mysqli_error($conn);
        echo "<br>Query: " . $sql;
    }
}
?>