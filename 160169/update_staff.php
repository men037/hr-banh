<?php
include('config.php');
date_default_timezone_set('Asia/Bangkok');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_cid = mysqli_real_escape_string($conn, $_POST['old_cid']);
    
    // ... (รับค่าอื่นๆ เหมือนเดิม) ...
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $cid = mysqli_real_escape_string($conn, $_POST['cid']);
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $license_no = mysqli_real_escape_string($conn, $_POST['license_no']);
    
    // รับค่า gender_id
    $gender_id = !empty($_POST['gender_id']) ? $_POST['gender_id'] : "NULL";

    // ข้อมูลวันที่และเวลา
    $birthday = !empty($_POST['birthday']) ? "'".$_POST['birthday']."'" : "NULL";
    $start_date = !empty($_POST['start_date']) ? "'".$_POST['start_date']."'" : "NULL";
    $end_date = !empty($_POST['end_date']) ? "'".$_POST['end_date']."'" : "NULL";
    $updated_at = date('Y-m-d H:i:s');

    // ข้อมูลรหัสอ้างอิงอื่นๆ
    $prefix_id = $_POST['prefix_id'];
    $prefix_academic_id = !empty($_POST['prefix_academic_id']) ? $_POST['prefix_academic_id'] : "NULL";
    $position_id = $_POST['position_id'];
    $provider_pos_id = $_POST['provider_pos_id'];
    $group_id = $_POST['group_id'];
    $dept_id = $_POST['dept_id'];
    $type_id = !empty($_POST['type_id']) ? $_POST['type_id'] : "NULL";

    // สถานะ
    $work_status = $_POST['work_status'];
    $provider_status = $_POST['provider_status'];
    $ekyc_status = $_POST['ekyc_status'];

    // คำสั่ง SQL UPDATE (เพิ่ม gender_id)
    $sql = "UPDATE staff_main SET 
                cid = '$cid', 
                staff_id = '$staff_id', 
                license_no = '$license_no', 
                fname = '$fname', 
                lname = '$lname', 
                gender_id = $gender_id,
                type_id = $type_id,
                birthday = $birthday,
                start_date = $start_date,
                end_date = $end_date,
                prefix_id = '$prefix_id', 
                prefix_academic_id = $prefix_academic_id, 
                position_id = '$position_id', 
                provider_pos_id = '$provider_pos_id',
                group_id = '$group_id', 
                dept_id = '$dept_id',
                work_status = '$work_status', 
                provider_status = '$provider_status', 
                ekyc_status = '$ekyc_status',
                updated_at = '$updated_at' 
                WHERE cid = '$old_cid'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='staff_list.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>