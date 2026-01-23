<?php 
include('auth.php'); 
include('config.php');
checkAdmin(); 

if (isset($_GET['id'])) {
    $cid_to_view = mysqli_real_escape_string($conn, $_GET['id']);
    // Join ข้อมูลเพื่อให้แสดงเป็นชื่อข้อความ (ไม่ใช่ ID)
    $sql = "SELECT s.*, 
            p.name as pos_name, g.name as group_name, d.name as dept_name, 
            t.name as type_name, pre.name as prefix_name, 
            pa.name as prefix_academic, pv.name as provider_pos,
            ed.ed_name, gs.g_name as group_s_name, ds.d_name as dept_s_name,
            lr.name as leave_reason
            FROM staff_main s
            LEFT JOIN ref_position p ON s.position_id = p.id
            LEFT JOIN ref_group g ON s.group_id = g.id
            LEFT JOIN ref_dept d ON s.dept_id = d.id
            LEFT JOIN ref_type t ON s.type_id = t.id
            LEFT JOIN ref_prefix pre ON s.prefix_id = pre.id
            LEFT JOIN ref_prefix_academic pa ON s.prefix_academic_id = pa.id
            LEFT JOIN ref_provider_pos pv ON s.provider_pos_id = pv.id
            LEFT JOIN ref_education ed ON s.ed_id = ed.ed_id
            LEFT JOIN ref_group_s gs ON s.s_group_id = gs.g_id
            LEFT JOIN ref_dept_s ds ON s.s_dept_id = ds.d_id
            LEFT JOIN ref_leave_reason lr ON s.leave_reason_id = lr.id
            WHERE s.cid = '$cid_to_view'";
            
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    
    if (!$row) {
        echo "<script>alert('ไม่พบข้อมูล'); window.location.href='staff_list.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลเจ้าหน้าที่ - <?php echo $row['fname']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
   <style>
    :root {
        --purple-main: #673ab7;
        --purple-light: #f8f5ff;
        --purple-border: #e1d5f2;
        --text-label: #5e35b1;
    }

    body { background-color: var(--purple-light); font-family: 'Sarabun', sans-serif; font-size: 0.9rem; }
    .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(103, 58, 183, 0.1); }
    
    .card-header { 
        background: var(--purple-main); 
        color: white; 
        font-weight: bold; 
        border-radius: 15px 15px 0 0 !important;
        padding: 10px !important;
        font-size: 1rem;
    }
    
    .section-title { 
        color: var(--text-label); 
        font-weight: bold; 
        border-left: 4px solid var(--purple-main);
        padding-left: 10px;
        margin: 15px 0 10px 0;
        font-size: 0.95rem;
    }

    label { font-weight: bold; color: #888; font-size: 0.8rem; margin-bottom: 2px; }

    .view-box { 
        background-color: #fff; 
        border: 1px solid var(--purple-border);
        border-radius: 8px; 
        padding: 6px 12px; 
        color: #333;
        min-height: 38px;
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }
    .view-box:hover { background-color: #fcfaff; border-color: var(--purple-main); }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .bg-yes { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .bg-no { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

    .btn-back {
        border-radius: 30px;
        padding: 8px 30px;
        font-size: 0.9rem;
    }
</style>
</head>
<body>

<div class="container mt-4 mb-5">
    <div class="card mx-auto" style="max-width: 1000px;">
        <div class="card-header text-center">
            <i class="fas fa-search me-2"></i>แสดงข้อมูลเจ้าหน้าที่: <?php echo $row['fname']." ".$row['lname']; ?>
        </div>
        <div class="card-body">
            
            <div class="section-title"><i class="fas fa-id-card"></i> ข้อมูลส่วนตัวและรหัสพนักงาน</div>
            <div class="row g-2 mb-3">
                <div class="col-md-2">
                    <label>รหัสเจ้าหน้าที่</label>
                    <div class="view-box"><?php echo $row['staff_id']; ?></div>
                </div>
                <div class="col-md-2">
                    <label>เลขบัตรประชาชน</label>
                    <div class="view-box"><?php echo $row['cid']; ?></div>
                </div>
                <div class="col-md-2">
                    <label>เพศ</label>
                    <div class="view-box"><?php echo ($row['gender_id'] == 1) ? 'ชาย' : 'หญิง'; ?></div>
                </div>
                <div class="col-md-2">
                    <label>วันเดือนปีเกิด</label>
                    <div class="view-box">
                        <?php 
                            echo (!empty($row['birthday']) && $row['birthday'] != '0000-00-00') 
                                ? date('d/m/Y', strtotime($row['birthday'])) 
                                : '-'; 
                        ?>
                    </div>
                    <!-- <div class="view-box"><?php echo ($row['birthday'] != '0000-00-00') ? date('d/m/Y', strtotime($row['birthday'])) : '-'; ?></div> -->
                </div>
                <div class="col-md-2">
                    <label>เลขที่ตำแหน่ง</label>
                    <div class="view-box"><?php echo $row['position_no'] ?: '-'; ?></div>
                </div>
                <div class="col-md-2">
                    <label>เลขใบประกอบวิชาชีพ</label>
                    <div class="view-box"><?php echo $row['license_no'] ?: '-'; ?></div>
                </div>
                
                <div class="col-md-2">
                    <label>คำนำหน้า</label>
                    <div class="view-box"><?php echo $row['prefix_name']; ?></div>
                </div>
                <div class="col-md-2">
                    <label>คำนำหน้านาม</label>
                    <div class="view-box"><?php echo $row['prefix_academic'] ?: '-'; ?></div>
                </div>
                <div class="col-md-4">
                    <label>ชื่อ-นามสกุล</label>
                    <div class="view-box"><?php echo $row['fname']." ".$row['lname']; ?></div>
                </div>
                <div class="col-md-4">
                    <label>การศึกษาสูงสุด</label>
                    <div class="view-box"><?php echo $row['ed_name'] ?: '-'; ?></div>
                </div>
            </div>

            <div class="section-title"><i class="fas fa-hospital-user"></i> สังกัดและตำแหน่งงาน</div>
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label>ตำแหน่งหลัก</label>
                    <div class="view-box"><?php echo $row['pos_name']; ?></div>
                </div>
                <div class="col-md-4">
                    <label>ประเภทบุคลากร</label>
                    <div class="view-box"><?php echo $row['type_name']; ?></div>
                </div>
                <div class="col-md-4">
                    <label>ตำแหน่ง Provider</label>
                    <div class="view-box"><?php echo $row['provider_pos'] ?: '-'; ?></div>
                </div>
                <div class="col-md-6">
                    <label>กลุ่มงาน</label>
                    <div class="view-box"><?php echo $row['group_name']; ?></div>
                </div>
                <div class="col-md-6">
                    <label>หน่วยงาน</label>
                    <div class="view-box"><?php echo $row['dept_name']; ?></div>
                </div>
                
                <div class="col-md-4">
                    <label>กลุ่มงาน (ตาม จ)</label>
                    <div class="view-box text-muted"><?php echo $row['group_s_name'] ?: '-'; ?></div>
                </div>
                <div class="col-md-4">
                    <label>หน่วยงาน (ตาม จ)</label>
                    <div class="view-box text-muted"><?php echo $row['dept_s_name'] ?: '-'; ?></div>
                </div>
                <div class="col-md-4">
                    <label>สถานที่ปฏิบัติงาน (ตาม จ)</label>
                    <div class="view-box text-muted"><?php echo $row['workplace_s'] ?: '-'; ?></div>
                </div>
            </div>

            <div class="section-title"><i class="fas fa-check-circle"></i> สถานะการทำงาน</div>
            <div class="row g-2 mb-3 align-items-center">
                <div class="col-md-3">
                    <label>วันเริ่มปฏิบัติงาน</label>
                    <div class="view-box">
                        <?php 
                            echo (!empty($row['start_date']) && $row['start_date'] != '0000-00-00') 
                                ? date('d/m/Y', strtotime($row['start_date'])) 
                                : '-'; 
                        ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>สถานะปัจจุบัน</label>
                    <div>
                        <?php if($row['work_status']=='Y'): ?>
                            <span class="status-badge bg-yes"><i class="fas fa-check me-1"></i> ปฏิบัติงาน</span>
                        <?php else: ?>
                            <span class="status-badge bg-no"><i class="fas fa-times me-1"></i> ไม่ปฏิบัติงาน</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if($row['work_status']=='N'): ?>
                <div class="col-md-3">
                    <label>วันที่สิ้นสุด</label>
                    <div class="view-box bg-light"><?php echo date('d/m/Y', strtotime($row['end_date'])); ?></div>
                </div>
                <div class="col-md-3">
                    <label>เหตุผลที่พ้นสภาพ</label>
                    <div class="view-box bg-light"><?php echo $row['leave_reason']; ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="row g-2">
                <div class="col-md-3">
                    <label>สถานะ Provider</label>
                    <div>
                        <span class="status-badge <?php echo ($row['provider_status']=='Y')?'bg-yes':'bg-no'; ?>">
                            <?php echo ($row['provider_status']=='Y')?'ยืนยันแล้ว':'ยังไม่ยืนยัน'; ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>สถานะ eKYC</label>
                    <div>
                        <span class="status-badge <?php echo ($row['ekyc_status']=='Y')?'bg-yes':'bg-no'; ?>">
                            <?php echo ($row['ekyc_status']=='Y')?'ยืนยันแล้ว':'ยังไม่ยืนยัน'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 pt-3 border-top">
                <a href="staff_list2.php" class="btn btn-secondary btn-back shadow-sm">
                    <i class="fas fa-chevron-left me-2"></i> กลับหน้ารายชื่อ
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- footer -->
<?php include('footer.php'); ?>

</body>
</html>