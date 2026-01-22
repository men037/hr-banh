<?php
include('auth.php'); 
checkAdmin(); 
include('config.php');
date_default_timezone_set('Asia/Bangkok');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_cid = mysqli_real_escape_string($conn, $_POST['old_cid']);
    
    // 1. ดึงข้อมูลเดิมมาเป็นตัวตั้งต้น
    $sql_before = "SELECT * FROM staff_main WHERE cid = '$old_cid'";
    $res_before = mysqli_query($conn, $sql_before);
    $old_data = mysqli_fetch_assoc($res_before);

    if (!$old_data) {
        echo "<script>alert('ไม่พบข้อมูลเดิม'); window.history.back();</script>";
        exit;
    }

    // 2. รับค่าจากฟอร์มและทำความสะอาดข้อมูล
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $cid = mysqli_real_escape_string($conn, $_POST['cid']);
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $license_no = mysqli_real_escape_string($conn, $_POST['license_no']);
    $prefix_id = mysqli_real_escape_string($conn, $_POST['prefix_id']);
    $prefix_academic_id_val = $_POST['prefix_academic_id'] ?? ""; 
    $type_id_val = $_POST['type_id'] ?: "";
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $provider_pos_id = mysqli_real_escape_string($conn, $_POST['provider_pos_id']);
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $dept_id = mysqli_real_escape_string($conn, $_POST['dept_id']);
    $work_status = mysqli_real_escape_string($conn, $_POST['work_status']);
    $provider_status = mysqli_real_escape_string($conn, $_POST['provider_status'] ?? "");
    $ekyc_status = mysqli_real_escape_string($conn, $_POST['ekyc_status'] ?? "");
    $gender_id_val = $_POST['gender_id'] ?? "";
    $birthday_val = $_POST['birthday'] ?? "";
    $start_date_val = $_POST['start_date'] ?? "";
    $end_date_val = $_POST['end_date'] ?? "";

    // --- 3. ส่วนเปรียบเทียบข้อมูลแบบละเอียด (ครบทุกช่อง) ---
    $diff = [];
    $check_fields = [
        'cid' => 'เลขบัตรประชาชน',
        'staff_id' => 'รหัสเจ้าหน้าที่',
        'license_no' => 'เลขใบประกอบ',
        'fname' => 'ชื่อ',
        'lname' => 'นามสกุล',
        'prefix_id' => 'คำนำหน้า',
        'prefix_academic_id' => 'คำนำหน้าวิชาการ',
        'gender_id' => 'เพศ',
        'type_id' => 'ประเภทพนักงาน',
        'position_id' => 'ตำแหน่ง',
        'provider_pos_id' => 'ตำแหน่งสายงาน',
        'group_id' => 'กลุ่มภารกิจ',
        'dept_id' => 'แผนก/ฝ่าย',
        'birthday' => 'วันเกิด',
        'start_date' => 'วันเริ่มงาน',
        'end_date' => 'วันลาออก',
        'work_status' => 'สถานะการทำงาน',
        'provider_status' => 'สถานะ Provider',
        'ekyc_status' => 'สถานะ eKYC'
    ];

    foreach ($check_fields as $field => $label) {
        $new_val = $_POST[$field] ?? "";
        $old_val = $old_data[$field] ?? "";

        // เทียบค่า (แปลงเป็น string เพื่อให้เทียบกันได้แม่นยำแม้เป็น NULL)
        if ((string)$new_val !== (string)$old_val) {
            $diff[] = "$label: [$old_val] -> [$new_val]";
        }
    }

    $log_details = !empty($diff) ? "แก้ไข: " . implode(" | ", $diff) : "ไม่มีการเปลี่ยนค่า";
    // ----------------------------------------------------

    // 4. จัดการค่าสำหรับ SQL (NULL Handling)
    $gender_sql = !empty($gender_id_val) ? $gender_id_val : "NULL";
    $type_sql = !empty($type_id_val) ? $type_id_val : "NULL";
    $academic_sql = !empty($prefix_academic_id_val) ? $prefix_academic_id_val : "NULL";
    $bday_sql = !empty($birthday_val) ? "'".mysqli_real_escape_string($conn, $birthday_val)."'" : "NULL";
    $start_sql = !empty($start_date_val) ? "'".mysqli_real_escape_string($conn, $start_date_val)."'" : "NULL";
    $end_sql = !empty($end_date_val) ? "'".mysqli_real_escape_string($conn, $end_date_val)."'" : "NULL";
    $updated_at = date('Y-m-d H:i:s');

    // 5. คำสั่ง SQL UPDATE
    $sql = "UPDATE staff_main SET 
                cid = '$cid', staff_id = '$staff_id', license_no = '$license_no', 
                fname = '$fname', lname = '$lname', gender_id = $gender_sql,
                type_id = $type_sql, birthday = $bday_sql, start_date = $start_sql, 
                end_date = $end_sql, prefix_id = '$prefix_id', 
                prefix_academic_id = $academic_sql, position_id = '$position_id', 
                provider_pos_id = '$provider_pos_id', group_id = '$group_id', 
                dept_id = '$dept_id', work_status = '$work_status', 
                provider_status = '$provider_status', ekyc_status = '$ekyc_status',
                updated_at = '$updated_at' 
                WHERE cid = '$old_cid'";

    if (mysqli_query($conn, $sql)) {
        write_log($conn, 'EDIT', 'staff_main', $staff_id, $log_details);
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='staff_list.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>