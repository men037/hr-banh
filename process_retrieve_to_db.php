<?php
include('auth.php');
include('config.php');
checkSuperAdmin();

// ปิดการแสดง Error ปกติของ PHP เพื่อไม่ให้ไปกวน JSON
error_reporting(0);
header('Content-Type: application/json');

$token = isset($_POST['token']) ? trim($_POST['token']) : '';
$hosp_code = "10864"; 

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบ Token ในระบบ']);
    exit;
}

// ปรับ URL ตามที่ทดสอบผ่านใน Postman (POST แต่ส่ง Params ผ่าน URL)
$url = "https://phr1.moph.go.th/idp/api/update_moph_personnel?hospital_code=$hosp_code&Action=Retrieve";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true); 
curl_setopt($ch, CURLOPT_POSTFIELDS, ""); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo json_encode(['status' => 'error', 'message' => 'CURL Error: ' . $curl_error]);
    exit;
}

$data = json_decode($response, true);

if ($httpCode == 200 && isset($data['result'])) {
    $count_process = 0;
    
    if (empty($data['result'])) {
        echo json_encode(['status' => 'error', 'message' => 'เชื่อมต่อสำเร็จ แต่ไม่พบข้อมูลในระบบ MOPH']);
        exit;
    }

    foreach ($data['result'] as $row) {
        // เตรียมข้อมูลและป้องกัน SQL Injection
        $moph_id = intval($row['_id']);
        $cid = mysqli_real_escape_string($conn, $row['cid']);
        $prefix = mysqli_real_escape_string($conn, $row['prefix']);
        $fname = mysqli_real_escape_string($conn, $row['first_name']);
        $lname = mysqli_real_escape_string($conn, $row['last_name']);
        $type_name = mysqli_real_escape_string($conn, $row['type_name']);
        $dept_name = mysqli_real_escape_string($conn, $row['department_name']);
        $pos_name = mysqli_real_escape_string($conn, $row['position_name']);
        $pos_type = mysqli_real_escape_string($conn, $row['position_type_name']);
        $pos_level = mysqli_real_escape_string($conn, $row['position_level_name']);
        $pos_code = mysqli_real_escape_string($conn, $row['position_code']);
        $pos_std_id = intval($row['position_std_id']);
        $pos_std_type_id = intval($row['position_std_type_id']);
        $is_hr = ($row['is_hr_admin'] == true) ? 1 : 0;
        $has_prov = ($row['has_provider_id'] == true) ? 1 : 0;

        $sql = "INSERT INTO staff_moph_retrieve 
                (moph_id, cid, prefix, first_name, last_name, type_name, department_name, position_name, position_type_name, position_level_name, position_code, position_std_id, position_std_type_id, is_hr_admin, has_provider_id)
                VALUES 
                ($moph_id, '$cid', '$prefix', '$fname', '$lname', '$type_name', '$dept_name', '$pos_name', '$pos_type', '$pos_level', '$pos_code', $pos_std_id, $pos_std_type_id, $is_hr, $has_prov)
                ON DUPLICATE KEY UPDATE 
                moph_id = VALUES(moph_id),
                prefix = VALUES(prefix),
                first_name = VALUES(first_name),
                last_name = VALUES(last_name),
                position_name = VALUES(position_name),
                last_update = CURRENT_TIMESTAMP";

        if (mysqli_query($conn, $sql)) {
            $count_process++;
        }
    }

    echo json_encode([
        'status' => 'success', 
        'message' => "Retrieve สำเร็จ! บันทึก/อัปเดตข้อมูลได้ $count_process รายการ"
    ]);
} else {
    $msg = isset($data['Message']) ? $data['Message'] : 'HTTP Error Code: ' . $httpCode;
    echo json_encode(['status' => 'error', 'message' => $msg]);
}
?>