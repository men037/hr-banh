<?php
include('auth.php');
include('config.php');
checkSuperAdmin();

header('Content-Type: application/json');

$token = isset($_POST['token']) ? trim($_POST['token']) : '';
$cid = isset($_POST['cid']) ? trim($_POST['cid']) : '';
$hosp_code = "10864"; // รหัสหน่วยงานของคุณ

if (empty($token) || empty($cid)) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// URL สำหรับ Delete (ส่ง Params ผ่าน URL ตามรูปแบบ Curl ที่คุณให้มา)
$url = "https://phr1.moph.go.th/idp/api/update_moph_personnel?Action=Delete&hospital_code=$hosp_code";

// ข้อมูล CID ส่งใน Body (JSON)
$post_data = ['cid' => $cid];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true); 
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data)); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // ตั้ง timeout ไว้ 30 วินาทีพอครับ
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($httpCode == 200) {
    echo json_encode([
        'status' => 'success', 
        'message' => 'ลบข้อมูลสำเร็จ (Record Deleted)'
    ]);
} else {
    $msg = $data['Message'] ?? 'เกิดข้อผิดพลาดในการลบ';
    echo json_encode(['status' => 'error', 'message' => $msg . " (Code: $httpCode)"]);
}