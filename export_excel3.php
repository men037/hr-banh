<?php
include('auth.php');
include('config.php');

// 1. ตรวจสอบสถานะแสดงทั้งหมดหรือไม่
$show_all = (isset($_GET['show_all']) && $_GET['show_all'] == '1');
$where_clauses = [];

// ถ้าไม่แสดงทั้งหมด ให้เอาเฉพาะที่ใช้งาน (Work Status = 'Y')
if (!$show_all) {
    $where_clauses[] = "s.work_status = 'Y'";
}

// 2. รับค่า Filter ต่างๆ (ใช้ mysqli_real_escape_string ป้องกัน SQL Injection)
if (!empty($_GET['g'])) {
    $g = mysqli_real_escape_string($conn, $_GET['g']);
    $where_clauses[] = "g.name = '$g'";
}
if (!empty($_GET['d'])) {
    $d = mysqli_real_escape_string($conn, $_GET['d']);
    $where_clauses[] = "d.name = '$d'";
}
if (!empty($_GET['gs'])) {
    $gs = mysqli_real_escape_string($conn, $_GET['gs']);
    $where_clauses[] = "gs.g_name = '$gs'";
}
if (!empty($_GET['ds'])) {
    $ds = mysqli_real_escape_string($conn, $_GET['ds']);
    $where_clauses[] = "ds.d_name = '$ds'";
}
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clauses[] = "(s.fname LIKE '%$search%' OR s.lname LIKE '%$search%' OR s.cid LIKE '%$search%' OR pos.name LIKE '%$search%')";
}

// รวมเงื่อนไข WHERE
$where = "";
if (count($where_clauses) > 0) {
    $where = " WHERE " . implode(" AND ", $where_clauses);
}

// 3. SQL Query
$sql = "SELECT s.*, 
               g.name AS g_name_j, 
               d.name AS d_name_j, 
               gs.g_name AS g_name_real, 
               ds.d_name AS d_name_real,
               pos.name AS posname, 
               t.name AS tname,
               pre.name AS prefix_name
        FROM staff_main s
        LEFT JOIN ref_group g ON s.group_id = g.id
        LEFT JOIN ref_dept d ON s.dept_id = d.id
        LEFT JOIN ref_group_s gs ON s.s_group_id = gs.g_id
        LEFT JOIN ref_dept_s ds ON s.s_dept_id = ds.d_id
        LEFT JOIN ref_position pos ON s.position_id = pos.id
        LEFT JOIN ref_type t ON s.type_id = t.id
        LEFT JOIN ref_prefix pre ON s.prefix_id = pre.id
        $where 
        ORDER BY s.staff_id ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error in SQL: " . mysqli_error($conn));
}

// --- ส่วน Header และ Table คงเดิมตามที่คุณเขียนมาได้เลยครับ ---

// 3. ตั้งค่า Header
$filename = "staff_report_" . date('Ymd_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <table border="1">
        <thead>
            
                <th>ลำดับ</th>
                <th>คำนำหน้า</th>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <th>ตำแหน่ง</th>
                <th>หน่วยงาน</th>
                <th>กลุ่มงาน</th>
                <th>หน่วยงาน (ตาม จ)</th>
                <th>กลุ่มงาน (ตาม จ)</th>
                <th>สถานที่ (ตาม จ)</th>
            
        </thead>
        <tbody>
            <?php 
            $i = 1;
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$i++."</td>";
                echo "<td>".$row['prefix_name']."</td>";
                echo "<td>".$row['fname']."</td>";
                echo "<td>".$row['lname']."</td>";
                echo "<td>".$row['posname']."</td>";
                echo "<td>".$row['d_name_j']."</td>";
                echo "<td>".$row['g_name_j']."</td>";
                echo "<td>".$row['d_name_real']."</td>";
                echo "<td>".$row['g_name_real']."</td>";
                echo "<td>".$row['workplace_s']."</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>