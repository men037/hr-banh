<?php 
include('auth.php'); 
checkAdmin(); 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลเจ้าหน้าที่ - รพ.บ้านนา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
   <style>
    :root {
        --pink-primary: #ff85a2;
        --pink-hover: #f06292;
        --pink-light: #fff5f7;
        --pink-border: #ffdae3;
        --text-dark: #d81b60;
    }

    body { background-color: var(--pink-light); font-family: 'Sarabun', sans-serif; }
    .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(216, 27, 96, 0.1); overflow: hidden; }
    
    .card-header { 
        background: var(--pink-primary); 
        color: white; 
        font-weight: bold; 
        font-size: 1.1rem; 
        border: none;
        padding: 10px !important; 
    }
    
    .card-body {
        padding: 1.5rem !important; 
    }

    .section-title { 
        color: var(--text-dark); 
        font-weight: bold; 
        border-left: 5px solid var(--pink-primary); 
        padding-left: 12px;
        margin-top: 10px;    
        margin-bottom: 15px; 
        display: flex;
        align-items: center;
        font-size: 1rem;
    }

    .section-title:first-of-type {
        margin-top: 0 !important;
    }

    .section-title i { margin-right: 8px; }

    label { 
        font-weight: bold; 
        color: #666; 
        margin-bottom: 2px !important; 
        font-size: 0.85rem; 
    }

    /* แก้ไขส่วนกรอบสีชมพูและการเรืองแสง */
    .form-control, .form-select { 
        border: 1px solid var(--pink-border); 
        border-radius: 8px; 
        padding: 6px 12px; 
        font-size: 0.9rem;
        transition: all 0.3s ease-in-out;
    }

    /* เมื่อเอาเมาส์วาง (Hover) หรือ คลิกเลือก (Focus) */
    .form-control:hover, .form-select:hover {
        border-color: var(--pink-primary);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--pink-primary);
        box-shadow: 0 0 0 0.25rem rgba(255, 133, 162, 0.25); /* เรืองแสงสีชมพู */
        outline: 0;
    }

    .status-toggle label {
        padding: 6px 15px; 
        margin: 2px;       
        border-radius: 10px;
        font-size: 0.85rem;
        min-width: 100px;  
    }

    .btn-save { 
        background: var(--pink-primary); 
        color: white !important; 
        border-radius: 30px; 
        padding: 8px 40px; 
        font-size: 1rem;
        transition: all 0.3s ease; 
        border: none;
    }

    .btn-save:hover { 
        background: var(--pink-hover) !important; 
        color: #ffffff !important; 
        transform: translateY(-2px); 
        box-shadow: 0 5px 15px rgba(240, 98, 146, 0.4); 
    }

    .status-toggle input[type="radio"]:checked + label.lab-yes { 
        background-color: #d1e7dd !important; 
        border-color: #a3cfbb !important; 
        color: #0f5132 !important; 
        font-weight: bold;
    }
    .status-toggle input[type="radio"]:checked + label.lab-no { 
        background-color: #f8d7da !important; 
        border-color: #f1aeb5 !important; 
        color: #842029 !important; 
        font-weight: bold;
    }

    .status-toggle input[type="radio"] {
        display: none !important;
    }

    .bg-readonly { background-color: #f1f3f5 !important; color: #6c757d; }
    /* ป้องกันช่อง readonly เรืองแสงสีชมพู */
    .bg-readonly:focus { box-shadow: none !important; border-color: #dee2e6 !important; }

</style>
</head>
<body>

<div class="container mt-4 mb-5">
    <div class="card">
        <div class="card-header py-3 text-center">
            <i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลเจ้าหน้าที่: <?php echo $row['fname'] . " " . $row['lname']; ?>
        </div>
        <div class="card-body p-4 p-lg-4">
            <form action="update_staff.php" method="POST">
                <input type="hidden" name="old_cid" value="<?php echo $row['cid']; ?>">

                <div class="section-title"><i class="fas fa-id-card"></i> ข้อมูลส่วนตัวและรหัสพนักงาน</div>
                <div class="row g-2">
                <div class="col-md-2">
                        <label>รหัสเจ้าหน้าที่</label>
                        <input type="text" name="staff_id" class="form-control bg-readonly" value="<?php echo $row['staff_id']; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>เลขบัตรประชาชน</label>
                        <input type="text" name="cid" class="form-control" value="<?php echo $row['cid']; ?>" required maxlength="13">
                    </div>
                    <div class="col-md-2">
                        <label>เพศ</label>
                        <select name="gender_id" class="form-select">
                            <?php 
                            $res_gender = mysqli_query($conn, "SELECT * FROM ref_gender");
                            while($g = mysqli_fetch_assoc($res_gender)) {
                                $sel = ($g['id'] == $row['gender_id']) ? "selected" : "";
                                echo "<option value='{$g['id']}' $sel>{$g['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label>วันเดือนปีเกิด</label>
                        <input type="date" name="birthday" class="form-control" value="<?php echo $row['birthday']; ?>">
                    </div>
                    <div class="col-md-2">
                        <label>เลขที่ตำแหน่ง</label>
                        <input type="text" name="position_no" class="form-control" value="<?php echo $row['position_no']; ?>" placeholder="ระบุเลขที่ตำแหน่ง">
                    </div>
                    <div class="col-md-2">
                        <label>เลขใบประกอบวิชาชีพ</label>
                        <input type="text" name="license_no" class="form-control" value="<?php echo $row['license_no']; ?>" placeholder="ถ้ามี">
                    </div>
                    <div class="col-md-1">
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
                        <div class="col-md-1">
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
                    <div class="col-md-3">
                        <label>ชื่อ (ไม่ต้องมีคำนำหน้า)</label>
                        <input type="text" name="fname" class="form-control" value="<?php echo $row['fname']; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label>นามสกุล</label>
                        <input type="text" name="lname" class="form-control" value="<?php echo $row['lname']; ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label>การศึกษาสูงสุด</label>
                        <select name="ed_id" class="form-select">
                            <option value="">-- เลือกการศึกษา --</option>
                            <?php 
                            $res_ed = mysqli_query($conn, "SELECT * FROM ref_education ORDER BY ed_id ASC");
                            while($ed = mysqli_fetch_assoc($res_ed)) {
                                $sel = ($ed['ed_id'] == $row['ed_id']) ? "selected" : "";
                                echo "<option value='{$ed['ed_id']}' $sel>{$ed['ed_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                  

                   
                </div>

                <div class="section-title"><i class="fas fa-hospital-user"></i> สังกัดและตำแหน่งงาน</div>
<div class="row g-2 align-items-end"> <div class="col-md-4">
        <label>ประเภทบุคลากร</label>
        <select name="type_id" class="form-select">
            <?php 
            $res_type = mysqli_query($conn, "SELECT * FROM ref_type");
            while($t = mysqli_fetch_assoc($res_type)) {
                $sel = ($t['id'] == $row['type_id']) ? "selected" : "";
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
            <option value="">-- เลือกตำแหน่ง --</option>
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

    <div class="col-md-2 mt-2">
        <label class="d-block">สถานะ จ <span class="text-danger">*</span></label>
        <div class="status-toggle d-flex">
            <input type="radio" name="status_s" id="sts_y" value="Y" <?php echo ($row['status_s'] == 'Y' || empty($row['status_s'])) ? 'checked' : ''; ?> required onclick="toggleStatusS()">
            <label for="sts_y" class="lab-yes" style="min-width: 70px;"><i class="fas fa-check-circle"></i> ตาม</label>
            
            <input type="radio" name="status_s" id="sts_n" value="N" <?php echo ($row['status_s'] == 'N') ? 'checked' : ''; ?> onclick="toggleStatusS()">
            <label for="sts_n" class="lab-no" style="min-width: 70px;"><i class="fas fa-times-circle"></i> ไม่ตาม</label>
        </div>
    </div>
    
    <div class="col-md-3 mt-2">
        <label>กลุ่มงานจริง (ตาม จ)</label>
        <select name="s_group_id" id="s_group_id" class="form-select">
            <option value="">-- เลือก --</option>
            <?php 
            $res_gs = mysqli_query($conn, "SELECT * FROM ref_group_s ORDER BY g_id ASC");
            while($gs = mysqli_fetch_assoc($res_gs)) {
                $sel = ($gs['g_id'] == $row['s_group_id']) ? "selected" : "";
                echo "<option value='{$gs['g_id']}' $sel>{$gs['g_name']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <label>หน่วยงาน (ตาม จ)</label>
        <select name="s_dept_id" id="s_dept_id" class="form-select">
            <option value="">-- เลือก --</option>
            <?php 
            $res_ds = mysqli_query($conn, "SELECT * FROM ref_dept_s ORDER BY d_id ASC");
            while($ds = mysqli_fetch_assoc($res_ds)) {
                $sel = ($ds['d_id'] == $row['s_dept_id']) ? "selected" : "";
                echo "<option value='{$ds['d_id']}' $sel>{$ds['d_name']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-md-4 mt-2">
        <label>สถานที่ปฏิบัติงาน (ตาม จ)</label>
        <input type="text" name="workplace_s" id="workplace_s" class="form-control" value="<?php echo $row['workplace_s']; ?>" placeholder="ระบุ">
    </div>
</div>

                <div class="section-title mt-4"><i class="fas fa-cog"></i> สถานะและวันที่ปฏิบัติงาน</div>
                <div class="row g-2">
                    <div class="col-md-3">
                    <label>วันเริ่มปฏิบัติงาน</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $row['start_date']; ?>">
                    </div>
                    
                    <div class="col-md-4 text-center border-end border-start">
                    <label>สถานะการทำงาน</label>
                        <div class="status-toggle">
                            <input type="radio" name="work_status" id="work_y" value="Y" <?php echo ($row['work_status']=='Y')?'checked':''; ?> onclick="toggleWorkStatus()">
                            <label for="work_y" class="lab-yes"><i class="fas fa-check-square me-2"></i>ปฏิบัติงาน</label>
                            
                            <input type="radio" name="work_status" id="work_n" value="N" <?php echo ($row['work_status']=='N')?'checked':''; ?> onclick="toggleWorkStatus()">
                            <label for="work_n" class="lab-no"><i class="fas fa-times-circle me-2"></i>ไม่ปฏิบัติงาน</label>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div id="reason_area" class="p-2 rounded-3 border">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label id="end_date_label">วันที่สิ้นสุด</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $row['end_date']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label id="reason_label">เหตุผล</label>
                                    <select name="leave_reason_id" id="leave_reason_id" class="form-select">
                                        <option value="">-- เลือกเหตุผล --</option>
                                        <?php 
                                        $res_re = mysqli_query($conn, "SELECT * FROM ref_leave_reason");
                                        while($re = mysqli_fetch_assoc($res_re)) {
                                            $sel = ($row['leave_reason_id'] == $re['id']) ? "selected" : "";
                                            echo "<option value='{$re['id']}' $sel>{$re['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-3">
                    <label>สถานะ Provider</label>
                        <div class="status-toggle">
                            <input type="radio" name="provider_status" id="pro_y" value="Y" <?php echo ($row['provider_status']=='Y')?'checked':''; ?>>
                            <label for="pro_y" class="lab-yes"><i class="fas fa-id-badge me-2"></i>ยืนยันแล้ว</label>
                            
                            <input type="radio" name="provider_status" id="pro_n" value="N" <?php echo ($row['provider_status']=='N')?'checked':''; ?>>
                            <label for="pro_n" class="lab-no"><i class="fas fa-exclamation-triangle me-2"></i>ยังไม่ยืนยัน</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                    <label>สถานะ eKYC</label>
                        <div class="status-toggle">
                            <input type="radio" name="ekyc_status" id="ekyc_y" value="Y" <?php echo ($row['ekyc_status']=='Y')?'checked':''; ?>>
                            <label for="ekyc_y" class="lab-yes"><i class="fas fa-fingerprint me-2"></i>ยืนยันแล้ว</label>
                            
                            <input type="radio" name="ekyc_status" id="ekyc_n" value="N" <?php echo ($row['ekyc_status']=='N')?'checked':''; ?>>
                            <label for="ekyc_n" class="lab-no"><i class="fas fa-exclamation-triangle me-2"></i>ยังไม่ยืนยัน</label>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-save shadow-sm me-3">
                        <i class="fas fa-save me-2"></i>บันทึกการแก้ไข
                    </button>
                    <a href="staff_list.php" class="btn btn-outline-secondary px-5" style="border-radius: 30px;">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleWorkStatus() {
    const isWorking = document.getElementById('work_y').checked;
    const reasonArea = document.getElementById('reason_area');
    const endDateInput = document.getElementById('end_date');
    const reasonSelect = document.getElementById('leave_reason_id');
    const endDateLabel = document.getElementById('end_date_label');
    const reasonLabel = document.getElementById('reason_label');

    if (isWorking) {
        endDateInput.disabled = true;
        reasonSelect.disabled = true;
        reasonArea.style.opacity = "0.4";
        reasonArea.style.backgroundColor = "#f8f9fa";
        endDateLabel.style.color = "#bbb";
        reasonLabel.style.color = "#bbb";
    } else {
        endDateInput.disabled = false;
        reasonSelect.disabled = false;
        reasonArea.style.opacity = "1";
        reasonArea.style.backgroundColor = "#fff";
        endDateLabel.style.color = "#d81b60";
        reasonLabel.style.color = "#d81b60";
    }
}
document.addEventListener('DOMContentLoaded', toggleWorkStatus);
function toggleStatusS() {
    // ถ้าติ๊ก "ตาม" (sts_y) ให้ปิดช่องกรอกข้อมูล
    const isFollow = document.getElementById('sts_y').checked;
    const fields = ['s_group_id', 's_dept_id', 'workplace_s'];
    
    fields.forEach(id => {
        const field = document.getElementById(id);
        if (isFollow) {
            field.disabled = true;
            field.classList.add('bg-readonly'); // เพิ่มสีเทา
        } else {
            field.disabled = false;
            field.classList.remove('bg-readonly'); // คืนค่าสีขาว
        }
    });
}

// เรียกใช้งานตอนโหลดหน้าครั้งแรกเพื่อให้สถานะเริ่มต้นถูกต้อง
document.addEventListener('DOMContentLoaded', function() {
    toggleStatusS();
    toggleWorkStatus(); // เรียกของเดิมด้วย
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- footer -->
<?php include('footer.php'); ?>
</body>
</html>