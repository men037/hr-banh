<?php
include('config.php');

// ตั้งชื่อไฟล์
$filename = "รายชื่อเจ้าหน้าที่_รพ_บ้านนา_" . date('Ymd_Hi') . ".xls";

// Header สำหรับบังคับ Download ไฟล์ Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// SQL ดึงข้อมูลเฉพาะคนที่ใช้งานอยู่ (Y) และ Join ตารางที่จำเป็น
$sql = "SELECT s.*, pro.name as provider_name, pre.name as prefix_name, prp.name as prefix_academic
        FROM staff_main s 
        LEFT JOIN ref_provider_pos pro ON s.provider_pos_id = pro.id
        LEFT JOIN ref_prefix pre ON s.prefix_id = pre.id
        LEFT JOIN ref_prefix_academic prp ON s.prefix_academic_id = prp.id
        
        WHERE s.work_status = 'Y'
        ORDER BY s.group_id ASC,s.dept_id ASC,s.staff_id ASC ";

$result = mysqli_query($conn, $sql);
?>

<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head><meta http-equiv="Content-type" content="text/html;charset=utf-8" /></head>
<body>
    <table border="1">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>ลำดับ</th>
                <th>คำนำหน้า</th>
                <th>คำนำหน้านาม</th>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <th>เบอร์มือถือ</th>
                <th>เลขประจำตัวประชาชน</th>
                <th>ตำแหน่ง</th>
                <th>วิชาชีพเฉพาะทาง</th>
                <th>เลขใบประกอบวิชาชีพ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            while($row = mysqli_fetch_assoc($result)) {
            ?>
                <tr>
                    <td align="center"><?php echo $i++; ?></td>
                    <td><?php echo $row['prefix_name']; ?></td> 
                    <td><?php echo $row['prefix_academic']; ?></td>
                    <td><?php echo $row['fname']; ?></td>
                    <td><?php echo $row['lname']; ?></td>
                    <td></td>
                    <td style="mso-number-format:'\@';"> <?php echo $row['cid']; ?> </td>
                    <td><?php echo $row['provider_name']; ?></td>
                    <td></td>
                    <td><?php echo $row['license_no']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>