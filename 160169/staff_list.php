<?php
include('auth.php'); 
checkAdmin(); // ใครไม่ใช่ Admin/Super Admin จะโดนเด้งกลับ index.php
include('config.php'); 
// ตรวจสอบสถานะการแสดงผล (Toggle Show All)
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == '1';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายชื่อเจ้าหน้าที่ - รพ.บ้านนา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        /* ปรับแต่งปุ่มเพิ่มข้อมูลใหม่ให้เป็นสไตล์ Outline สีชมพู */
        .btn-outline-pink { color: #ff85a2; border-color: #ff85a2; border-radius: 20px; transition: 0.3s; }
        .btn-outline-pink:hover { background-color: #ff85a2; color: white; transform: scale(1.05); }
        
        .table-pink thead { background-color: #ffdae3; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }
        .status-icon { font-size: 1.2rem; }
    </style>
</head>
<body>

<!-- <nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
        <a href="index.php" class="btn btn-light btn-sm fw-bold text-pink">🏠 กลับหน้า Dashboard</a>
    </div>
</nav> -->


<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
    <div class="ms-auto d-flex gap-2">
        <a href="index.php" class="btn btn-light btn-sm fw-bold text-pink">🏠 กลับหน้า Dashboard</a>
        <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="return confirm('ออกจากระบบ?')"> 🚪 ออกจากระบบ </a>
        </div>
    </nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-secondary">📋 รายชื่อเจ้าหน้าที่</h4>
        
        <div class="d-flex gap-2">
            <a href="export_excel.php" class="btn btn-outline-success btn-sm shadow-sm px-3">
                📥 ส่งออก Excel
            </a>

            <?php if ($show_all): ?>
                <a href="staff_list.php" class="btn btn-outline-primary btn-sm shadow-sm">🔍 แสดงเฉพาะที่ใช้งาน</a>
            <?php else: ?>
                <a href="staff_list.php?show_all=1" class="btn btn-outline-primary btn-sm shadow-sm">📁 แสดงรายชื่อทั้งหมด</a>
            <?php endif; ?>

            <!-- <a href="add_staff.php" class="btn btn-outline-pink btn-sm px-3 shadow-sm fw-bold">+ เพิ่มข้อมูลใหม่</a> -->
            <a href="add_staff.php" class="btn btn-outline-danger btn-sm shadow-sm px-3"> 😀 เพิ่มข้อมูลใหม่ </a>
        </div>
        
    </div>

    <div class="card p-4 shadow-sm">
        <div class="table-responsive">
            <table id="staffTable" class="table table-hover align-middle">
                <thead class="table-pink">
                    <tr>
                        <th width="10%">สถานะ</th>
                        <th width="15%">หน่วยงาน/กลุ่มงาน</th>
                        <th width="15%">ชื่อ-นามสกุล</th>
                        <th width="15%">ตำแหน่ง</th>
                        <th width="10%">ประเภท</th>
                        <th width="8%" class="text-center">eKYC</th>
                        <th width="8%" class="text-center">Provider</th>
                        <th width="9%" class="text-center">แก้ไข</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($conn) {
                        $sql = "SELECT s.*, g.name as gname, d.name as dname, pos.name as posname, t.name as tname 
                                FROM staff_main s 
                                LEFT JOIN ref_group g ON s.group_id = g.id 
                                LEFT JOIN ref_dept d ON s.dept_id = d.id
                                LEFT JOIN ref_position pos ON s.position_id = pos.id
                                LEFT JOIN ref_type t ON s.type_id = t.id";
                        
                        if (!$show_all) { $sql .= " WHERE s.work_status = 'Y'"; }
                        $sql .= " ORDER BY s.staff_id DESC";
                        
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            $st_class = ($row['work_status'] == 'Y') ? 'bg-success' : 'bg-danger';
                            $st_text = ($row['work_status'] == 'Y') ? 'ใช้งาน' : 'ไม่ใช้งาน';
                    ?>
                        <tr>
                            <td><span class="badge <?php echo $st_class; ?>"><?php echo $st_text; ?></span></td>
                            <td>
                                <small class="text-muted d-block"><?php echo $row['gname'] ?? '-'; ?></small>
                                <strong><?php echo $row['dname'] ?? '-'; ?></strong>
                            </td>
                            <td><?php echo $row['fname']."  ".$row['lname']; ?></td>
                            <td><?php echo $row['posname'] ?? '-'; ?></td>
                            <td><?php echo $row['tname'] ?? '-'; ?></td>
                            <td class="text-center status-icon">
                                <?php echo ($row['ekyc_status'] == 'Y') ? '✅' : '❌'; ?>
                            </td>
                            <td class="text-center status-icon">
                                <?php echo ($row['provider_status'] == 'Y') ? '✅' : '❌'; ?>
                            </td>
                            <td class="text-center">
                                <a href="edit.php?id=<?php echo $row['cid']; ?>" class="btn btn-sm btn-info text-white shadow-sm px-3">แก้ไข</a>
                            </td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#staffTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
            "pageLength": 10,
            "order": [] 
        });
    });
</script>

</body>
</html>