<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $staff = $_POST['staff'];
    $hosp_code = "10864";

    // เตรียม Payload จากข้อมูลที่ดึงมาจาก SQL Query โดยตรง
    $payload = [
        "cid"                  => (string)$staff['cid'],
        "prefix"               => (string)$staff['prefix'],
        "first_name"           => (string)$staff['first_name'],
        "last_name"            => (string)$staff['last_name'],
        "type_name"            => $staff['type_name'] ?? "",
        "department_name"      => $staff['department_name'] ?? "",
        "position_name"        => (string)$staff['position_name'],
        "position_type_name"   => $staff['position_type_name'] ?? "",
        "position_level_name"  => $staff['position_level_name'] ?? "",
        "position_code"        => $staff['position_code'] ?? "",
        "position_std_id"      => (int)$staff['position_std_id'], // จาก pr.CODE
        "position_std_type_id" => (int)$staff['position_std_type_id'],
        "mobile_phone"         => (string)$staff['mobile_phone'],
        "license_no"           => $staff['license_no'] ?? ""
    ];

    $url = "https://phr1.moph.go.th/idp/api/update_moph_personnel?hospital_code=$hosp_code&Action=Update";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . trim($token)
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ส่งผลลัพธ์กลับไปแสดงที่ Console Log หน้าจอ
    echo json_encode([
        "status"  => $http_code,
        "message" => $response
    ], JSON_UNESCAPED_UNICODE);
}