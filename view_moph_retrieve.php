<?php
include('auth.php');
include('config.php');
checkSuperAdmin(); 

// รับค่าจากปุ่มสลับ (Default คือซ่อน อสม.)
$show_osm = isset($_GET['osm']) ? $_GET['osm'] : 'hide';

if ($show_osm === 'all') {
    // แสดงทั้งหมดรวม อสม.
    $where_clause = ""; // ไม่ต้องมี WHERE เพื่อดึงทุกอย่างออกมา
    $btn_text = "แสดงเฉพาะเจ้าหน้าที่";
    $btn_link = "?osm=hide";
    $btn_color = "btn-outline-secondary";
    $title_sub = "แสดงข้อมูลทั้งหมด (รวม อสม.)";
    $empty_msg = "ไม่พบข้อมูลในระบบ";
} else {
    // ซ่อน อสม. (แสดงเฉพาะเจ้าหน้าที่) - Default
    $where_clause = "WHERE position_std_id <> '51'";
    $btn_text = "แสดงทั้งหมดรวม อสม.";
    $btn_link = "?osm=all";
    $btn_color = "btn-outline-primary";
    $title_sub = "แสดงข้อมูลเฉพาะเจ้าหน้าที่ (ไม่รวม อสม.)";
    $empty_msg = "ไม่พบข้อมูลเจ้าหน้าที่ในระบบ";
}

$sql = "SELECT * FROM staff_moph_retrieve $where_clause ORDER BY position_std_id ";
$result = mysqli_query($conn, $sql);
$total_rows = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOPH Retrieved Data - STAFF SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { display: flex; margin: 0; font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .main-content { 
            margin-left: 260px; 
            width: calc(100% - 260px); 
            padding: 25px; 
            min-height: 100vh;
        }
        @media (max-width: 992px) {
            .main-content { margin-left: 80px; width: calc(100% - 80px); }
        }

        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); border-radius: 15px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        .search-box { position: relative; max-width: 350px; }
        .search-box input { padding-left: 35px; border-radius: 10px; border: 1px solid #dee2e6; }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #4e73df; }
        
        .badge-moph { background-color: #eef2ff; color: #4e73df; border: 1px solid #d1d9ff; }
        .table-responsive { max-height: 70vh; overflow-y: auto; }
        thead.sticky-top { z-index: 10; background: white; }
    </style>
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <nav class="navbar navbar-dark mb-4 shadow-sm">
        <div class="container-fluid d-flex justify-content-between">
            <span class="navbar-brand fw-bold">
                <i class="fa-solid fa-database me-2"></i> ข้อมูลพนักงานจากระบบ MOPH (Retrieve)
            </span>
        </div>
    </nav>

    <div class="container-fluid px-0">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0 text-secondary">
                        ข้อมูลล่าสุดในตารางพัก (<?php echo number_format($total_rows); ?> รายการ)
                    </h5>
                    <small class="text-info fw-bold"><?php echo $title_sub; ?></small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="<?php echo $btn_link; ?>" class="btn <?php echo $btn_color; ?> btn-sm d-flex align-items-center shadow-sm" style="height: 31px;">
                        <i class="fa-solid <?php echo ($show_osm === 'show') ? 'fa-users' : 'fa-user-nurse'; ?> me-1"></i> 
                        <?php echo $btn_text; ?>
                    </a>

                    <button id="btnUpdateMainID" class="btn btn-primary text-white shadow-sm btn-sm d-flex align-items-center justify-content-center" 
                            style="height: 31px; padding: 0 15px; line-height: 1;">
                        <i class="fa-solid fa-id-card-clip me-1"></i> อัปเดต ID เข้าตารางหลัก
                    </button>
                    
                    <div class="search-box mb-0">
                        <i class="fa-solid fa-magnifying-glass" style="top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" id="searchInput" class="form-control form-control-sm" 
                            placeholder="ค้นหาชื่อ, CID..." 
                            style="min-width: 200px; height: 31px;">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="mophTable">
                    <thead class="sticky-top shadow-sm">
                        <tr class="table-light">
                            <th>ชื่อ-นามสกุล</th>
                            <th>เลขบัตรประชาชน</th>
                            <th>ตำแหน่งในระบบ MOPH</th>
                            <th>หน่วยงาน</th>
                            <th class="text-center">Provider</th>
                            <th class="text-center">อัปเดตล่าสุด</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if($total_rows > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark"><?php echo $row['prefix'].$row['first_name'].' '.$row['last_name']; ?></span>
                                    <br><small class="text-muted">ID: <?php echo $row['moph_id']; ?></small>
                                </td>
                                <td><code><?php echo $row['cid']; ?></code></td>
                                <td>
                                    <span class="badge badge-moph"><?php echo $row['position_name']; ?></span>
                                    <br><small><?php echo $row['type_name']; ?> (<?php echo $row['position_level_name']; ?>)</small>
                                </td>
                                <td><small><?php echo $row['department_name']; ?></small></td>
                                <td class="text-center">
                                    <?php echo $row['has_provider_id'] ? '<i class="fa-solid fa-check-circle text-success"></i>' : '<i class="fa-solid fa-circle-xmark text-danger"></i>'; ?>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($row['last_update'])); ?></small>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-20"></i><br>
                                    <?php echo $empty_msg; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer class="mt-auto py-3 text-center text-muted small"><?php include('footer.php'); ?></footer>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // 1. ระบบค้นหาในตาราง
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableBody tr").filter(function() {
            // ดักไว้ไม่ให้ซ่อนแถว No Data
            if ($(this).find('td').length > 1) {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            }
        });
    });

    // 2. ระบบอัปเดต ID เข้าตารางหลัก
    $('#btnUpdateMainID').click(function() {
        if (!confirm('ยืนยันการนำ MOPH ID ไปอัปเดตเข้าฐานข้อมูลหลัก เฉพาะรายที่ยังไม่มีข้อมูล?')) return;

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> กำลังอัปเดต...');

        $.ajax({
            url: 'process_update_idmoph.php',
            method: 'POST',
            success: function(res) {
                const data = typeof res === 'object' ? res : JSON.parse(res);
                if (data.status === 'success') {
                    alert(data.message);
                    location.reload(); // รีโหลดเพื่อให้เห็นข้อมูลล่าสุด
                } else {
                    alert('ผิดพลาด: ' + data.message);
                }
                btn.prop('disabled', false).html('<i class="fa-solid fa-id-card-clip me-1"></i> อัปเดต ID เข้าตารางหลัก');
            },
            error: function() {
                alert('ไม่สามารถติดต่อไฟล์ประมวลผลได้');
                btn.prop('disabled', false).html('<i class="fa-solid fa-id-card-clip me-1"></i> อัปเดต ID เข้าตารางหลัก');
            }
        });
    });
});
</script>
</body>
</html>