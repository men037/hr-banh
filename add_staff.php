<?php 
include('auth.php'); 
checkAdmin(); 
include('config.php'); 

// --- 1. คำนวณรหัสพนักงานอัตโนมัติ ---
$sql_max = "SELECT MAX(CAST(SUBSTRING(staff_id, 2) AS UNSIGNED)) as max_id FROM staff_main WHERE staff_id LIKE 'B%'";
$res_max = mysqli_query($conn, $sql_max);
$row_max = mysqli_fetch_assoc($res_max);
$next_num = ($row_max['max_id'] > 0) ? $row_max['max_id'] + 1 : 1;
$next_staff_id = "B" . sprintf("%05d", $next_num); 
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลเจ้าหน้าที่ - รพ.บ้านนา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --blue-primary: #4a90e2;
            --blue-hover: #357abd;
            --blue-light: #f0f7ff;
            --blue-border: #cfe2ff;
            --text-dark: #2c3e50;
        }

        body { background-color: var(--blue-light); font-family: 'Sarabun', sans-serif; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(74, 144, 226, 0.1); overflow: hidden; }
        
        .card-header { 
            background: var(--blue-primary); 
            color: white; 
            font-weight: bold; 
            font-size: 1.1rem; 
            border: none;
            padding: 10px !important; 
        }
        
        .card-body { padding: 1.5rem !important; }

        .section-title { 
            color: var(--text-dark); 
            font-weight: bold; 
            border-left: 5px solid var(--blue-primary); 
            padding-left: 12px;
            margin-top: 10px;    
            margin-bottom: 15px; 
            display: flex;
            align-items: center;
            font-size: 1rem;
        }

        .section-title i { margin-right: 8px; }

        label { 
            font-weight: bold; 
            color: #666; 
            margin-bottom: 2px !important; 
            font-size: 0.85rem; 
        }

        .form-control, .form-select { 
            border: 1px solid var(--blue-border); 
            border-radius: 8px; 
            padding: 6px 12px; 
            font-size: 0.9rem;
            transition: all 0.3s ease-in-out;
        }

        /* เอฟเฟกต์เรืองแสงสีฟ้าเมื่อ Focus */
        .form-control:focus, .form-select:focus {
            border-color: var(--blue-primary);
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
            outline: 0;
        }

        .btn-blue-save { 
            background: var(--blue-primary); 
            color: white !important; 
            border-radius: 30px; 
            padding: 8px 40px; 
            border: none;
            transition: all 0.3s ease; 
        }

        .btn-blue-save:hover { 
            background: var(--blue-hover) !important; 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); 
        }

        .status-toggle input[type="radio"] { display: none; }
        .status-toggle label {
            padding: 6px 15px; margin: 2px;
            background-color: #fff; border: 1px solid var(--blue-border);
            border-radius: 10px; cursor: pointer; font-size: 0.85rem; min-width: 100px;
        }

        .status-toggle input[type="radio"]:checked + label.lab-yes { background-color: #d1e7dd !important; border-color: #a3cfbb !important; color: #0f5132 !important; font-weight: bold; }
        .status-toggle input[type="radio"]:checked + label.lab-no { background-color: #f8d7da !important; border-color: #f1aeb5 !important; color: #842029 !important; font-weight: bold; }
        
        .bg-readonly { background-color: #f1f3f5 !important; color: #6c757d; }
        .bg-readonly:focus { box-shadow: none !important; border-color: var(--blue-border) !important; }
    </style>
</head>
<body>

<div class="container mt-4 mb-5">
    <div class="card">
        <div class="card-header text-center">
            <i class="fas fa-user-plus me-2"></i> เพิ่มข้อมูลเจ้าหน้าที่ใหม่
        </div>
        <div class="card-body">
            <form action="save_staff.php" method="POST" id="staffForm">
                
                <div class="section-title"><i class="fas fa-id-card"></i> ข้อมูลส่วนตัวและรหัสพนักงาน</div>
                <div class="row g-2">
                <div class="col-md-2">
                        <label>รหัสเจ้าหน้าที่</label>
                        <input type="text" name="staff_id" class="form-control bg-readonly" value="<?php echo $next_staff_id; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                    <label>เลขบัตรประชาชน <span class="text-danger">*</span></label>
                        <input type="text" name="cid" class="form-control" maxlength="13" required>
                    </div>
                    
                    <div class="col-md-2">
                    <label>เพศ <span class="text-danger">*</span></label>
                        <select name="gender_id" class="form-select" required>
                        <option value="" selected disabled>-- เลือก --</option>
                            <?php $res=mysqli_query($conn,"SELECT * FROM ref_gender"); while($g=mysqli_fetch_assoc($res)) echo "<option value='{$g['id']}'>{$g['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                    <label>วันเกิด <span class="text-danger">*</span></label>
                        <input type="date" name="birthday" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                    <label>เลขที่ตำแหน่ง</label>
                        <input type="text" name="position_no" class="form-control" placeholder="ระบุเลขตำแหน่ง">
                    </div>
                    <div class="col-md-2">
                    <label>เลขใบประกอบวิชาชีพ</label>
                        <input type="text" name="license_no" class="form-control" placeholder="ถ้ามี">
                    </div>
                    <div class="col-md-1">
                    <label>คำนำหน้า <span class="text-danger">*</span></label>
                        <select name="prefix_id" class="form-select" required>
                        <option value="" selected disabled>-- เลือก --</option>
                            <?php $res=mysqli_query($conn,"SELECT * FROM ref_prefix"); while($p=mysqli_fetch_assoc($res)) echo "<option value='{$p['id']}'>{$p['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                    <label>คำนำหน้านาม</label>
                        <select name="prefix_academic_id" class="form-select">
                            <option value="">- ไม่มี -</option>
                            <?php $res=mysqli_query($conn,"SELECT * FROM ref_prefix_academic"); while($pa=mysqli_fetch_assoc($res)) echo "<option value='{$pa['id']}'>{$pa['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                    <label>ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" name="fname" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                    <label>นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" name="lname" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>การศึกษาสูงสุด <span class="text-danger">*</span></label>
                        <select name="ed_id" class="form-select" required>
                        <option value="" selected disabled>-- เลือกระดับการศึกษา --</option>
                            <?php 
                           $res_ed = mysqli_query($conn, "SELECT * FROM ref_education ORDER BY ed_id ASC"); 
                           while($ed = mysqli_fetch_assoc($res_ed)) {
                               echo "<option value='{$ed['ed_id']}'>{$ed['ed_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                </div>

                <div class="section-title mt-4"><i class="fas fa-hospital-user"></i> สังกัดและตำแหน่งงาน</div>
                <div class="row g-2">
                    <div class="col-md-4">
                    <label>ประเภทบุคลากร <span class="text-danger">*</span></label>
                        <select name="type_id" class="form-select" required>
                        <option value="" selected disabled>-- เลือก --</option>
                            <?php $res=mysqli_query($conn,"SELECT * FROM ref_type"); while($t=mysqli_fetch_assoc($res)) echo "<option value='{$t['id']}'>{$t['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                    <label>ตำแหน่งหลัก <span class="text-danger">*</span></label>
                        <select name="position_id" class="form-select" required>
                        <option value="" selected disabled>-- เลือก --</option>
                            <?php $res=mysqli_query($conn,"SELECT * FROM ref_position"); while($pos=mysqli_fetch_assoc($res)) echo "<option value='{$pos['id']}'>{$pos['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                    <label>ตำแหน่ง Provider</label>
                        <select name="provider_pos_id" class="form-select">
                            <option value="">-- เลือก --</option>
                            <?php $res=mysqli_query($conn,"SELECT * FROM ref_provider_pos"); while($pp=mysqli_fetch_assoc($res)) echo "<option value='{$pp['id']}'>{$pp['name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label> กลุ่มงาน <span class="text-danger">*</span></label>
                        <select name="group_id" class="form-select" required>
                            <option value="" selected disabled>-- เลือก --</option>
                            <?php 
                            $res = mysqli_query($conn, "SELECT * FROM ref_group"); 
                            while($g = mysqli_fetch_assoc($res)) {
                                echo "<option value='{$g['id']}'>{$g['name']}</option>"; 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label> หน่วยงาน <span class="text-danger">*</span></label>
                        <select name="dept_id" class="form-select" required>
                            <option value="" selected disabled>-- เลือก --</option>
                            <?php 
                                $res = mysqli_query($conn, "SELECT * FROM ref_dept"); 
                                while($d = mysqli_fetch_assoc($res)) {
                                    echo "<option value='{$d['id']}'>{$d['name']}</option>"; 
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 mt-2">
                    <label>กลุ่มงานจริง (ตาม จ)</label>
                        <select name="s_group_id" class="form-select">
                            <option value="">-- เลือก --</option>
                            <?php $res_gs=mysqli_query($conn,"SELECT * FROM ref_group_s ORDER BY g_id ASC"); while($gs=mysqli_fetch_assoc($res_gs)) echo "<option value='{$gs['g_id']}'>{$gs['g_name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mt-2">
                    <label>หน่วยงาน (ตาม จ)</label>
                        <select name="s_dept_id" class="form-select">
                            <option value="">-- เลือก --</option>
                            <?php $res_ds=mysqli_query($conn,"SELECT * FROM ref_dept_s ORDER BY d_id ASC"); while($ds=mysqli_fetch_assoc($res_ds)) echo "<option value='{$ds['d_id']}'>{$ds['d_name']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mt-2">
                    <label>สถานที่ปฏิบัติ (ตาม จ)</label>
                        <input type="text" name="workplace_s" class="form-control" placeholder="ระบุ">
                    </div>
                </div>

                <div class="section-title mt-4"><i class="fas fa-cog"></i> สถานะและวันที่เริ่มงาน</div>
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                    <label>วันเริ่มงาน <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-4 text-center border-start border-end">
                    <label>สถานะ Provider</label>
                        <div class="status-toggle">
                            <input type="radio" name="provider_status" id="pro_y" value="Y">
                            <label for="pro_y" class="lab-yes"><i class="fas fa-check-circle me-1"></i> ยืนยัน</label>
                            <input type="radio" name="provider_status" id="pro_n" value="N" checked>
                            <label for="pro_n" class="lab-no"><i class="fas fa-times-circle me-1"></i> รอยืนยัน</label>
                        </div>
                    </div>
                    <div class="col-md-5 text-center">
                    <label>สถานะ eKYC</label>
                        <div class="status-toggle">
                            <input type="radio" name="ekyc_status" id="ekyc_y" value="Y">
                            <label for="ekyc_y" class="lab-yes"><i class="fas fa-fingerprint me-1"></i> ยืนยันแล้ว</label>
                            <input type="radio" name="ekyc_status" id="ekyc_n" value="N" checked>
                            <label for="ekyc_n" class="lab-no"><i class="fas fa-exclamation-triangle me-1"></i> ยังไม่ยืนยัน</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-blue-save shadow">
                        <i class="fas fa-save me-2"></i> บันทึกข้อมูลเจ้าหน้าที่
                    </button>
                    <a href="staff_list.php" class="btn btn-outline-secondary px-5 ms-2" style="border-radius: 30px;">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('staffForm').addEventListener('submit', function(e) {
    const cid = document.getElementsByName('cid')[0].value;
    if(cid.length < 13) {
        e.preventDefault();
        Swal.fire({ icon: 'error', title: 'ข้อมูลไม่ถูกต้อง', text: 'กรุณากรอกเลขบัตรประชาชนให้ครบ 13 หลัก', confirmButtonColor: '#4a90e2' });
        return false;
    }
});

document.getElementsByName('cid')[0].addEventListener('blur', function() {
    const cid = this.value;
    const inputField = this;
    if (cid.length === 13) {
        fetch('check_cid.php?cid=' + cid)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    Swal.fire({ icon: 'warning', title: 'เลขบัตรประชาชนซ้ำ!', text: 'เลขบัตร ' + cid + ' มีในระบบแล้ว (ชื่อ: ' + data.name + ')', confirmButtonColor: '#4a90e2' });
                    inputField.classList.add('is-invalid');
                    inputField.value = '';
                } else {
                    inputField.classList.remove('is-invalid');
                    inputField.classList.add('is-valid');
                }
            });
    }
});
</script>
<!-- footer -->
<?php include('footer.php'); ?>
</body>
</html>