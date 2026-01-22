<?php
include('config.php');

// ฟังก์ชันดึงข้อมูลนับจำนวนแยกตามฟิลด์ที่กำหนด
function getChartData($conn, $field, $table) {
    $sql = "SELECT t.name as label, COUNT(s.cid) as total 
            FROM $table t 
            LEFT JOIN staff_main s ON s.$field = t.id AND s.work_status = 'Y'
            GROUP BY t.id";
    $result = mysqli_query($conn, $sql);
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = $row['label'];
        $data['data'][] = $row['total'];
    }
    return $data;
}

// ตัวอย่างดึงข้อมูลกลุ่มงาน
$groupData = getChartData($conn, 'group_id', 'ref_group');
?>