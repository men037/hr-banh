<?php 
include('auth.php'); 
checkAdmin(); // ใครไม่ใช่ Admin/Super Admin จะโดนเด้งกลับ index.php
include('config.php'); 
// หน้าเพิ่มข้อมูล ไม่ต้องดึงค่าเดิมจาก DB ดังนั้นเรากำหนด $row ให้เป็นค่าว่างป้องกัน Error ในฟอร์ม
$row = [
    'cid' => '', 'staff_id' => '', 'license_no' => '', 'fname' => '', 'lname' => '',
    'gender_id' => '', 'prefix_id' => '', 'prefix_academic_id' => '', 'birthday' => '',
    'type_id' => '', 'position_id' => '', 'provider_pos_id' => '', 'group_id' => '',
    'dept_id' => '', 'start_date' => '', 'provider_status' => 'N', 'ekyc_status' => 'N',
    'work_status' => 'Y', 'end_date' => ''
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มข้อมูลเจ้าหน้าที่ใหม่ - รพ.บ้านนา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f7ff; font-family: 'Sarabun', sans-serif; } /* เปลี่ยนสีพื้นหลังให้ต่างจากหน้าแก้ไข */
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .card-header { background: #4a90e2; color: white; font-weight: bold; border-radius: 15px 15px 0 0 !important; }
        .btn-blue { background: #4a90e2; color: white; border-radius: 20px; padding: 10px 40px; border: none; transition: 0.3s; }
        .btn-blue:hover { background: #357abd; transform: translateY(-2px); }
        .section-title { color: #2c3e50; font-weight: bold; border-bottom: 2px solid #d6eaff; padding-bottom: 5px; margin-top: 20px; }
        label { font-weight: bold; color: #555; margin-top: 5px; }
        .form-control, .form-select { border: 1px solid #d6eaff; border-radius: 8px; }
        
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
        <div class="card-header py-3 text-center">➕ เพิ่มข้อมูลเจ้าหน้าที่ใหม่</div>
        <div class="card-body p-4">
            <form action="save_staff.php" method="POST">

                <div class="section-title">👤 ข้อมูลส่วนตัวและรหัส</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>เลขบัตรประชาชน</label>
                        <input type="text" name="cid" class="form-control" placeholder="เลข 13 หลัก" required>
                    </div>
                    <div class="col-md-4">
                        <label>รหัสเจ้าหน้าที่</label>
                        <input type="text" name="staff_id" class="form-control" placeholder="รหัสเจ้าหน้าที่" required>
                    </div>
                    <div class="col-md-2">
                        <label>เพศ</label>
                        <select name="gender_id" class="form-select" required>
                        <option value="">-- เลือกเพศ --</option>
                         <?php 
                            $res_gender = mysqli_query($conn, "SELECT * FROM ref_gender");
                            while($g = mysqli_fetch_assoc($res_gender)) {
                                echo "<option value='{$g['id']}'>{$g['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>เลขใบประกอบวิชาชีพ</label>
                        <input type="text" name="license_no" class="form-control">
                    </div>
                    
                    <div class="col-md-2">
                        <label>คำนำหน้า</label>
                        <select name="prefix_id" class="form-select" required>
                            <option value="">-- เลือก --</option>
                            <?php 
                            $res_p = mysqli_query($conn, "SELECT * FROM ref_prefix");
                            while($p = mysqli_fetch_assoc($res_p)) {
                                echo "<option value='{$p['id']}'>{$p['name']}</option>";
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
                                echo "<option value='{$pa['id']}'>{$pa['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>ชื่อ</label>
                        <input type="text" name="fname" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>นามสกุล</label>
                        <input type="text" name="lname" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label>วันเดือนปีเกิด</label>
                        <input type="date" name="birthday" class="form-control">
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
                                echo "<option value='{$t['id']}'>{$t['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>ตำแหน่งหลัก</label>
                        <select name="position_id" class="form-select">
                            <option value="">-- เลือกตำแหน่ง --</option>
                            <?php 
                            $res_pos = mysqli_query($conn, "SELECT * FROM ref_position");
                            while($pos = mysqli_fetch_assoc($res_pos)) {
                                echo "<option value='{$pos['id']}'>{$pos['name']}</option>";
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
                                echo "<option value='{$pv['id']}'>{$pv['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>กลุ่มงาน</label>
                        <select name="group_id" class="form-select">
                            <option value="">-- เลือกกลุ่มงาน --</option>
                            <?php 
                            $res_g = mysqli_query($conn, "SELECT * FROM ref_group");
                            while($g = mysqli_fetch_assoc($res_g)) {
                                echo "<option value='{$g['id']}'>{$g['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>หน่วยงาน</label>
                        <select name="dept_id" class="form-select">
                            <option value="">-- เลือกหน่วยงาน --</option>
                            <?php 
                            $res_d = mysqli_query($conn, "SELECT * FROM ref_dept");
                            while($d = mysqli_fetch_assoc($res_d)) {
                                echo "<option value='{$d['id']}'>{$d['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="section-title">⚙️ สถานะและวันที่ปฏิบัติงาน</div>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label>วันเริ่มปฏิบัติงาน</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                
                    <div class="col-md-2 text-center">
                        <label class="d-block">สถานะ Provider</label>
                        <div class="status-toggle">
                            <input type="radio" name="provider_status" id="pro_y" value="Y">
                            <label for="pro_y" class="lab-yes">✅ มี</label>
                            <input type="radio" name="provider_status" id="pro_n" value="N" checked>
                            <label for="pro_n" class="lab-no">❌ ไม่มี </label>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <label class="d-block">สถานะ eKYC</label>
                        <div class="status-toggle">
                            <input type="radio" name="ekyc_status" id="ekyc_y" value="Y">
                            <label for="ekyc_y" class="lab-yes">✅ มี </label>
                            <input type="radio" name="ekyc_status" id="ekyc_n" value="N" checked>
                            <label for="ekyc_n" class="lab-no">❌ ไม่มี </label>
                        </div>
                    </div>
                    
                </div>

                <div class="mt-5 text-center">
                    <button type="submit" class="btn btn-blue shadow">💾 บันทึกข้อมูลเจ้าหน้าที่</button>
                    <a href="staff_list.php" class="btn btn-secondary px-5 ms-2 shadow-sm" style="border-radius: 20px; padding: 10px 40px;">ยกเลิก</a>
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
        endDateInput.value = "";
    }
}
window.onload = toggleEndDate;
</script>

</body>
</html>