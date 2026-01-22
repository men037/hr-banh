<?php
include('auth.php');
include('config.php');

// 1. รับค่าการกรอง (รับเป็นชื่อตามที่ JS ส่งมา)
$where_clauses = ["s.work_status = 'Y'"]; 

// กรองด้วยชื่อกลุ่มงาน (เช็คจากตาราง g.name)
if (!empty($_GET['group_name'])) {
    $group_name = mysqli_real_escape_string($conn, $_GET['group_name']);
    $where_clauses[] = "g.name = '$group_name'";
}

// กรองด้วยชื่อหน่วยงาน (เช็คจากตาราง d.name)
if (!empty($_GET['dept_name'])) {
    $dept_name = mysqli_real_escape_string($conn, $_GET['dept_name']);
    $where_clauses[] = "d.name = '$dept_name'";
}

// กรองด้วยช่องค้นหา (ชื่อ/นามสกุล/เลขบัตร)
if (!empty($_GET['search_name'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search_name']);
    $where_clauses[] = "(s.fname LIKE '%$search%' OR s.lname LIKE '%$search%' OR s.cid LIKE '%$search%')";
}

// รวมเงื่อนไข
$where = " WHERE " . implode(" AND ", $where_clauses);

// 2. Query ข้อมูล (ต้อง Join ตาราง d และ g เพื่อเอาชื่อมาเทียบ)
$sql = "SELECT s.*, 
               pre.name as prefix_name, 
               d.name as dept_name, 
               g.name as group_name
        FROM staff_main s
        LEFT JOIN ref_prefix pre ON s.prefix_id = pre.id
        LEFT JOIN ref_dept d ON s.dept_id = d.id
        LEFT JOIN ref_group g ON s.group_id = g.id
        $where 
        ORDER BY s.staff_id ASC";

$result = mysqli_query($conn, $sql);

// 3. ตั้งค่า Header สำหรับ Excel
$filename = "staff_report_" . date('Ymd_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/x-msexcel; charset=utf-8");
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
                <th>หน่วยงาน</th>
                <th>กลุ่มงาน</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>".$i++."</td>";
                    echo "<td>".$row['prefix_name']."</td>";
                    echo "<td>".$row['fname']."</td>";
                    echo "<td>".$row['lname']."</td>";
                    echo "<td>".$row['dept_name']."</td>";
                    echo "<td>".$row['group_name']."</td>";
                    echo "</tr>";
                }
            } else {
                // ถ้า SQL ผิดหรือหาไม่เจอจะแสดงบรรทัดนี้
                echo "<tr><td colspan='6' align='center'>ไม่พบข้อมูล (เงื่อนไข: $where)</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>