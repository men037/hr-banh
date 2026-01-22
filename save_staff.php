<?php
// เพิ่ม auth.php เพื่อใช้งาน session และฟังก์ชัน write_log
include('auth.php'); 
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
    $position_no = mysqli_real_escape_string($conn, $_POST['position_no']); 
    $workplace_s = mysqli_real_escape_string($conn, $_POST['workplace_s']); 
    
    // 3. จัดการข้อมูลรหัสอ้างอิง
    $prefix_id = mysqli_real_escape_string($conn, $_POST['prefix_id']);
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $provider_pos_id = mysqli_real_escape_string($conn, $_POST['provider_pos_id']);
    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
        if (empty($group_id)) {
            echo "<script>alert('กรุณาเลือกกลุ่มงาน'); window.history.back();</script>";
            exit;
        }
    $dept_id = mysqli_real_escape_string($conn, $_POST['dept_id']);

    // 4. จัดการข้อมูล ID ที่เป็นตัวเลข หรือค่าที่อาจเป็น NULL
    $gender_id = !empty($_POST['gender_id']) ? $_POST['gender_id'] : "NULL";
    $prefix_academic_id = !empty($_POST['prefix_academic_id']) ? $_POST['prefix_academic_id'] : "NULL";
    $type_id = !empty($_POST['type_id']) ? $_POST['type_id'] : "NULL";
    
    // --- เพิ่มการจัดการค่า ed_id (การศึกษาสูงสุด) ---
    $ed_id = !empty($_POST['ed_id']) ? $_POST['ed_id'] : "NULL";
    
    // ฟิลด์กลุ่มงานจริง/หน่วยงานจริง (s_group_id, s_dept_id)
    $s_group_id = !empty($_POST['s_group_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['s_group_id']) . "'" : "NULL";
    $s_dept_id = !empty($_POST['s_dept_id']) ? "'" . mysqli_real_escape_string($conn, $_POST['s_dept_id']) . "'" : "NULL";

    // 5. จัดการข้อมูลวันที่ (ถ้าว่างให้เป็น NULL)
    $birthday = !empty($_POST['birthday']) ? "'" . mysqli_real_escape_string($conn, $_POST['birthday']) . "'" : "NULL";
    $start_date = !empty($_POST['start_date']) ? "'" . mysqli_real_escape_string($conn, $_POST['start_date']) . "'" : "NULL";

    // 6. สร้างเวลาปัจจุบันสำหรับช่อง updated_at
    $updated_at = date('Y-m-d H:i:s');

    // 7. ข้อมูลสถานะ
    $provider_status = isset($_POST['provider_status']) ? $_POST['provider_status'] : 'N';
    $ekyc_status = isset($_POST['ekyc_status']) ? $_POST['ekyc_status'] : 'N';

    // 8. ตรวจสอบเลขบัตรประชาชนซ้ำ
    $check = mysqli_query($conn, "SELECT cid FROM staff_main WHERE cid = '$cid'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: 'เลขบัตรประชาชน $cid มีในระบบแล้ว',
                    confirmButtonColor: '#4a90e2'
                }).then(() => { window.history.back(); });
            }, 100);
        </script>";
        exit;
    }

    // 9. คำสั่ง SQL INSERT (เพิ่มฟิลด์ ed_id เข้าไปใน Query)
    $sql = "INSERT INTO staff_main (
                cid, staff_id, license_no, fname, lname, position_no,
                gender_id, prefix_id, prefix_academic_id, type_id, ed_id,
                position_id, provider_pos_id, group_id, dept_id, 
                s_group_id, s_dept_id, workplace_s,
                birthday, start_date, 
                provider_status, ekyc_status,  
                updated_at
            ) VALUES (
                '$cid', '$staff_id', '$license_no', '$fname', '$lname', '$position_no',
                $gender_id, '$prefix_id', $prefix_academic_id, $type_id, $ed_id,
                '$position_id', '$provider_pos_id', '$group_id', '$dept_id', 
                $s_group_id, $s_dept_id, '$workplace_s',
                $birthday, $start_date,
                '$provider_status', '$ekyc_status',  
                '$updated_at'
            )";

    if (mysqli_query($conn, $sql)) {
        
        // ✅ --- บันทึก LOG การเพิ่มข้อมูล ---
        $log_details = "เพิ่มเจ้าหน้าที่ใหม่: $fname $lname (รหัส: $staff_id)";
        write_log($conn, 'ADD', 'staff_main', $staff_id, $log_details);
        // ------------------------------------------

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    text: 'เพิ่มข้อมูลคุณ $fname $lname เรียบร้อยแล้ว',
                    confirmButtonColor: '#4a90e2'
                }).then(() => { window.location.href='staff_list.php'; });
            }, 100);
        </script>";
    } else {
        echo "<h4>Error:</h4> " . mysqli_error($conn);
        echo "<br>Query: " . $sql;
    }
}
?>