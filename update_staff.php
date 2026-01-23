<?php
include('auth.php'); 
checkAdmin(); 
include('config.php');
date_default_timezone_set('Asia/Bangkok');

if (!function_exists('write_log')) {
    function write_log($conn, $action, $table, $id, $details) {
        $user_id = $_SESSION['user_id'] ?? 'system';
        $details_clean = mysqli_real_escape_string($conn, $details);
        $sql_log = "INSERT INTO sys_log (action_type, table_name, record_id, details, user_id, log_date) 
                    VALUES ('$action', '$table', '$id', '$details_clean', '$user_id', NOW())";
        mysqli_query($conn, $sql_log);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_cid = mysqli_real_escape_string($conn, $_POST['old_cid']);
    
    // 1. ดึงข้อมูลเดิมมาเปรียบเทียบ
    $sql_before = "SELECT * FROM staff_main WHERE cid = '$old_cid'";
    $res_before = mysqli_query($conn, $sql_before);
    $old_data = mysqli_fetch_assoc($res_before);

    if (!$old_data) {
        echo "<script>alert('ไม่พบข้อมูลเดิม'); window.history.back();</script>";
        exit;
    }

    // 2. รับค่าพื้นฐาน
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $cid = mysqli_real_escape_string($conn, $_POST['cid']);
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $license_no = mysqli_real_escape_string($conn, $_POST['license_no']);
    $position_no = mysqli_real_escape_string($conn, $_POST['position_no']);
    $ed_id = mysqli_real_escape_string($conn, $_POST['ed_id']);
    $prefix_id = mysqli_real_escape_string($conn, $_POST['prefix_id']);
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $provider_pos_id = mysqli_real_escape_string($conn, $_POST['provider_pos_id']);
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $dept_id = mysqli_real_escape_string($conn, $_POST['dept_id']);
    $work_status = mysqli_real_escape_string($conn, $_POST['work_status']);
    
    // 3. ✅ จัดการส่วน "สถานะตาม จ"
    $status_s = mysqli_real_escape_string($conn, $_POST['status_s'] ?? 'Y');
    
    // ตรรกะ: ถ้าเลือก 'ตาม' (Y) ให้ล้างค่าช่อง (ตาม จ) เป็น NULL ทันที เพื่อไม่ให้มีข้อมูลค้าง
    if ($status_s === 'Y') {
        $s_group_sql = "NULL";
        $s_dept_sql = "NULL";
        $workplace_s_val = ""; // หรือจะเก็บเป็นค่าว่างใน DB
    } else {
        $s_group_id = $_POST['s_group_id'] ?? "";
        $s_dept_id = $_POST['s_dept_id'] ?? "";
        $workplace_s_val = mysqli_real_escape_string($conn, $_POST['workplace_s'] ?? "");
        
        $s_group_sql = !empty($s_group_id) ? "'$s_group_id'" : "NULL";
        $s_dept_sql = !empty($s_dept_id) ? "'$s_dept_id'" : "NULL";
    }

    // 4. จัดการค่า NULL อื่นๆ
    $prefix_academic_id_val = $_POST['prefix_academic_id'] ?? ""; 
    $type_id_val = $_POST['type_id'] ?: "";
    $gender_id_val = $_POST['gender_id'] ?? "";
    $birthday_val = $_POST['birthday'] ?? "";
    $start_date_val = $_POST['start_date'] ?? "";
    
    $academic_sql = !empty($prefix_academic_id_val) ? "'$prefix_academic_id_val'" : "NULL";
    $type_sql = !empty($type_id_val) ? "'$type_id_val'" : "NULL";
    $gender_sql = !empty($gender_id_val) ? "'$gender_id_val'" : "NULL";
    $bday_sql = !empty($birthday_val) ? "'$birthday_val'" : "NULL";
    $start_sql = !empty($start_date_val) ? "'$start_date_val'" : "NULL";
    $ed_sql = !empty($ed_id) ? "'$ed_id'" : "NULL";

    if ($work_status === 'Y') {
        $end_sql = "NULL";
        $leave_reason_sql = "NULL";
    } else {
        $end_date_val = $_POST['end_date'] ?? "";
        $leave_reason_id_val = $_POST['leave_reason_id'] ?? "";
        $end_sql = !empty($end_date_val) ? "'$end_date_val'" : "NULL";
        $leave_reason_sql = !empty($leave_reason_id_val) ? "'$leave_reason_id_val'" : "NULL";
    }

    $provider_status = mysqli_real_escape_string($conn, $_POST['provider_status'] ?? "N");
    $ekyc_status = mysqli_real_escape_string($conn, $_POST['ekyc_status'] ?? "N");
    $updated_at = date('Y-m-d H:i:s');

    // 5. เปรียบเทียบข้อมูลเพื่อทำ Log (เพิ่ม status_s เข้าไปด้วย)
    $diff = [];
    $check_fields = [
        'cid' => 'เลขบัตรประชาชน', 'staff_id' => 'รหัสเจ้าหน้าที่', 'fname' => 'ชื่อ', 'lname' => 'นามสกุล',
        'status_s' => 'สถานะตาม จ', 's_group_id' => 'กลุ่มงานจริง', 's_dept_id' => 'หน่วยงานจริง'
    ];
    foreach ($check_fields as $field => $label) {
        $new_v = $_POST[$field] ?? "";
        $old_v = $old_data[$field] ?? "";
        if ((string)$new_v !== (string)$old_v) {
            $diff[] = "$label: [$old_v] -> [$new_v]";
        }
    }
    $log_details = !empty($diff) ? "แก้ไข: " . implode(" | ", $diff) : "ไม่มีการเปลี่ยนค่า";

    // 6. UPDATE (เพิ่ม status_s ใน Query)
    $sql = "UPDATE staff_main SET 
                cid = '$cid', 
                staff_id = '$staff_id', 
                license_no = '$license_no', 
                position_no = '$position_no', 
                fname = '$fname', 
                lname = '$lname', 
                ed_id = $ed_sql, 
                gender_id = $gender_sql, 
                type_id = $type_sql, 
                birthday = $bday_sql, 
                start_date = $start_sql, 
                end_date = $end_sql, 
                leave_reason_id = $leave_reason_sql, 
                prefix_id = '$prefix_id', 
                prefix_academic_id = $academic_sql, 
                position_id = '$position_id', 
                provider_pos_id = '$provider_pos_id', 
                group_id = '$group_id', 
                dept_id = '$dept_id', 
                status_s = '$status_s',      -- ✅ เพิ่มตรงนี้
                s_group_id = $s_group_sql, 
                s_dept_id = $s_dept_sql, 
                workplace_s = '$workplace_s_val', 
                work_status = '$work_status', 
                provider_status = '$provider_status', 
                ekyc_status = '$ekyc_status', 
                updated_at = '$updated_at' 
                WHERE cid = '$old_cid'";

    if (mysqli_query($conn, $sql)) {
        write_log($conn, 'EDIT', 'staff_main', $staff_id, $log_details);
        echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>
                Swal.fire({
                    icon: 'success',
                    title: 'อัปเดตข้อมูลสำเร็จ',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => { window.location.href='staff_list.php'; });
            </script></body></html>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>