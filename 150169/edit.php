<?php 
include('config.php'); 

if (isset($_GET['id'])) {
    $cid_to_edit = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM staff_main WHERE cid = '$cid_to_edit'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    
    if (!$row) {
        echo "<script>alert('ไม่พบข้อมูล'); window.location.href='index.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลเจ้าหน้าที่ - รพ.บ้านนา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fff5f7; font-family: 'Sarabun', sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .card-header { background: #ff85a2; color: white; font-weight: bold; border-radius: 15px 15px 0 0 !important; }
        .btn-pink { background: #ff85a2; color: white; border-radius: 20px; padding: 10px 40px; border: none; transition: 0.3s; }
        .btn-pink:hover { background: #f06292; transform: translateY(-2px); }
        .section-title { color: #d81b60; font-weight: bold; border-bottom: 2px solid #ffdae3; padding-bottom: 5px; margin-top: 20px; }
        label { font-weight: bold; color: #555; margin-top: 5px; }
        .form-control, .form-select { border: 1px solid #ffdae3; border-radius: 8px; }
        
        .status-toggle input[type="radio"] { display: none; }
        .status-toggle label {
            display: inline-block; padding: 8px 18px; margin-top: 5px;
            background-color: #f8f9fa; border: 1px solid #dee2e6;
            border-radius: 10px; cursor: pointer; transition: 0.2s; font-weight: normal; font-size: 0.9rem;
        }
        .status-toggle input[type="radio"]:checked + label.lab-yes { background-color: #d1e7dd; border-color: #a3cfbb; color: #0f5132; font-weight: bold; }
        .status-toggle input[type="radio"]:checked + label.lab-no { background-color: #f8d7da; border-color: #f1aeb5; color: #842029; font-weight: bold; }
    </style>
</head>
<body>

<div class="container mt-4 mb-5">
    <div class="card">
        <div class="card-header py-3 text-center">✏️ แก้ไขข้อมูลเจ้าหน้าที่: <?php echo $row['fname'] . " " . $row['lname']; ?></div>
        <div class="card-body p-4">
            <form action="update_staff.php" method="POST">
                <input type="hidden" name="old_cid" value="<?php echo $row['cid']; ?>">

                <div class="section-title">👤 ข้อมูลส่วนตัวและรหัส</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>เลขบัตรประชาชน</label>
                        <input type="text" name="cid" class="form-control" value="<?php echo $row['cid']; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>รหัสเจ้าหน้าที่</label>
                        <input type="text" name="staff_id" class="form-control" value="<?php echo $row['staff_id']; ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label>เพศ</label>
                        <select name="gender_id" class="form-select">
                        <option value="">-- เลือกเพศ --</option>
                         <?php 
                            $res_gender = mysqli_query($conn, "SELECT * FROM ref_gender");
                            while($g = mysqli_fetch_assoc($res_gender)) {
                                $sel = (isset($row['gender_id']) && $g['id'] == $row['gender_id']) ? "selected" : "";
                                echo "<option value='{$g['id']}' $sel>{$g['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>เลขใบประกอบวิชาชีพ</label>
                        <input type="text" name="license_no" class="form-control" value="<?php echo $row['license_no']; ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label>คำนำหน้า</label>
                        <select name="prefix_id" class="form-select" required>
                            <?php 
                            $res_p = mysqli_query($conn, "SELECT * FROM ref_prefix");
                            while($p = mysqli_fetch_assoc($res_p)) {
                                $sel = ($p['id'] == $row['prefix_id']) ? "selected" : "";
                                echo "<option value='{$p['id']}' $sel>{$p['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>คำนำหน้านาม</label>
                        <select name="prefix_academic_id" class="form-select">
                            <option value="">-- ไม่มี --</option>
                            <?php 
                            $res_pa = mysqli_query($conn, "SELECT * FROM ref_prefix_academic");
                            while($pa = mysqli_fetch_assoc($res_pa)) {
                                $sel = ($pa['id'] == $row['prefix_academic_id']) ? "selected" : "";
                                echo "<option value='{$pa['id']}' $sel>{$pa['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>ชื่อ</label>
                        <input type="text" name="fname" class="form-control" value="<?php echo $row['fname']; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>นามสกุล</label>
                        <input type="text" name="lname" class="form-control" value="<?php echo $row['lname']; ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label>วันเดือนปีเกิด</label>
                        <input type="date" name="birthday" class="form-control" value="<?php echo $row['birthday']; ?>">
                    </div>
                    
                </div>

                <div class="section-title">🏥 สังกัดและตำแหน่ง</div>
                <div class="row g-3">
                <div class="col-md-4">
                    <label>ประเภทบุคลากร</label>
                    <select name="type_id" class="form-select">
                    <option value="">-- เลือกประเภท --</option>
                        <?php 
                         $res_type = mysqli_query($conn, "SELECT * FROM ref_type");
                         while($t = mysqli_fetch_assoc($res_type)) {
            // เช็คว่าค่าใน DB ตรงกับ ID นี้หรือไม่เพื่อทำ Selected
                        $sel = (isset($row['type_id']) && $t['id'] == $row['type_id']) ? "selected" : "";
                        echo "<option value='{$t['id']}' $sel>{$t['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>ตำแหน่งหลัก</label>
                        <select name="position_id" class="form-select">
                            <?php 
                            $res_pos = mysqli_query($conn, "SELECT * FROM ref_position");
                            while($pos = mysqli_fetch_assoc($res_pos)) {
                                $sel = ($pos['id'] == $row['position_id']) ? "selected" : "";
                                echo "<option value='{$pos['id']}' $sel>{$pos['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label>ตำแหน่ง Provider</label>
                        <select name="provider_pos_id" class="form-select">
                            <option value="">-- เลือกตำแหน่ง Provider --</option>
                            <?php 
                            $res_pv = mysqli_query($conn, "SELECT * FROM ref_provider_pos");
                            while($pv = mysqli_fetch_assoc($res_pv)) {
                                $sel = ($pv['id'] == $row['provider_pos_id']) ? "selected" : "";
                                echo "<option value='{$pv['id']}' $sel>{$pv['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>กลุ่มงาน</label>
                        <select name="group_id" class="form-select">
                            <?php 
                            $res_g = mysqli_query($conn, "SELECT * FROM ref_group");
                            while($g = mysqli_fetch_assoc($res_g)) {
                                $sel = ($g['id'] == $row['group_id']) ? "selected" : "";
                                echo "<option value='{$g['id']}' $sel>{$g['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>หน่วยงาน</label>
                        <select name="dept_id" class="form-select">
                            <?php 
                            $res_d = mysqli_query($conn, "SELECT * FROM ref_dept");
                            while($d = mysqli_fetch_assoc($res_d)) {
                                $sel = ($d['id'] == $row['dept_id']) ? "selected" : "";
                                echo "<option value='{$d['id']}' $sel>{$d['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="section-title">⚙️ สถานะและวันที่ปฏิบัติงาน</div>
                <div class="row g-3">
                <div class="col-md-2">
                        <label>วันเริ่มปฏิบัติงาน</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $row['start_date']; ?>">
                    </div>
                
                    <div class="col-md-2 text-center">
                        <label class="d-block">สถานะ Provider</label>
                        <div class="status-toggle">
                            <input type="radio" name="provider_status" id="pro_y" value="Y" <?php echo ($row['provider_status']=='Y')?'checked':''; ?>>
                            <label for="pro_y" class="lab-yes">✅ มี</label>
                            <input type="radio" name="provider_status" id="pro_n" value="N" <?php echo ($row['provider_status']=='N')?'checked':''; ?>>
                            <label for="pro_n" class="lab-no">❌ ไม่มี </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <label class="d-block">สถานะ eKYC</label>
                        <div class="status-toggle">
                            <input type="radio" name="ekyc_status" id="ekyc_y" value="Y" <?php echo ($row['ekyc_status']=='Y')?'checked':''; ?>>
                            <label for="ekyc_y" class="lab-yes">✅ มี </label>
                            <input type="radio" name="ekyc_status" id="ekyc_n" value="N" <?php echo ($row['ekyc_status']=='N')?'checked':''; ?>>
                            <label for="ekyc_n" class="lab-no">❌ ไม่มี </label>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <label class="d-block">สถานะการทำงาน</label>
                        <div class="status-toggle">
                            <input type="radio" name="work_status" id="work_y" value="Y" <?php echo ($row['work_status']=='Y')?'checked':''; ?> onclick="toggleEndDate()">
                            <label for="work_y" class="lab-yes">✅ ปฏิบัติงาน</label>
                            <input type="radio" name="work_status" id="work_n" value="N" <?php echo ($row['work_status']=='N')?'checked':''; ?> onclick="toggleEndDate()">
                            <label for="work_n" class="lab-no">❌ ไม่ปฏิบัติงาน</label>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label id="end_date_label">วันที่สิ้นสุดการทำงาน</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $row['end_date']; ?>">
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <button type="submit" class="btn btn-pink shadow">💾 บันทึกการแก้ไขข้อมูล</button>
                    <a href="index.php" class="btn btn-secondary px-5 ms-2 shadow-sm" style="border-radius: 20px; padding: 10px 40px;">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEndDate() {
    const workNo = document.getElementById('work_n');
    const endDateInput = document.getElementById('end_date');
    const endDateLabel = document.getElementById('end_date_label');

    if (workNo.checked) {
        endDateInput.disabled = false;
        endDateInput.style.backgroundColor = "#fff";
        endDateLabel.style.color = "#d81b60";
    } else {
        endDateInput.disabled = true;
        endDateInput.style.backgroundColor = "#e9ecef";
        endDateLabel.style.color = "#555";
        // ไม่ล้างค่า value ที่นี่เพื่อให้ user เห็นข้อมูลเก่า (หากมี) แต่ถ้าต้องการล้างให้ใช้ endDateInput.value = "";
    }
}
window.onload = toggleEndDate;
</script>

</body>
</html>