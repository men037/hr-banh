<?php
include('auth.php');
include('config.php');
checkSuperAdmin(); 

// Query ข้อมูลจากตารางที่เราเพิ่งสร้างใหม่
$sql = "SELECT * FROM staff_moph_retrieve ORDER BY last_update DESC";
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
                        ข้อมูลล่าสุดในตารางพัก (ทั้งหมด <?php echo number_format($total_rows); ?> รายการ)
                    </h5>
                    <small class="text-muted">ข้อมูลนี้ดึงมาจาก API เพื่อใช้ตรวจสอบความถูกต้อง</small>
                </div>
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="ค้นหาชื่อ, CID, ตำแหน่ง...">
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
                            <th class="text-center">HR Admin</th>
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
                                    <?php echo $row['is_hr_admin'] ? '<i class="fa-solid fa-check-circle text-success"></i>' : '-'; ?>
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
                                    ยังไม่มีข้อมูลในตารางพัก กรุณากดปุ่ม Retrieve Data ในหน้า Sync ก่อน
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
    // ระบบค้นหาในตาราง
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
</body>
</html>