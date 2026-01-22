<?php
include('auth.php'); 
include('config.php'); 
checkSuperAdmin();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการใช้งานระบบ - รพ.บ้านนา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-pink thead { background-color: #ffdae3; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }
        .text-pink { color: #ff85a2 !important; }
        
        /* สี Badge กิจกรรม */
        .bg-add { background-color: #2dce89; color: white; }
        .bg-edit { background-color: #ffbc00; color: white; }
        .bg-delete { background-color: #f5365c; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
        <div class="ms-auto d-flex gap-2">
            <a href="index.php" class="btn btn-light btn-sm fw-bold text-dark">🏠 กลับหน้า Dashboard</a>
            <a href="user_manage.php" class="btn btn-light btn-sm fw-bold text-dark">👥 จัดการผู้ใช้งาน</a>
            <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="return confirm('ออกจากระบบ?')"> 🚪 ออกจากระบบ </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-secondary">📜 ประวัติการใช้งานระบบ (Logs)</h4>
        <button onclick="location.reload()" class="btn btn-outline-danger btn-sm shadow-sm px-3">
            🔄 รีเฟรชข้อมูล
        </button>
    </div>

    <div class="card p-4 shadow-sm">
        <div class="table-responsive">
            <table id="logTable" class="table table-hover align-middle">
                <thead class="table-pink">
                    <tr>
                        <th width="15%">วัน-เวลา</th>
                        <th width="15%">ผู้ใช้งาน</th>
                        <th width="10%" class="text-center">กิจกรรม</th>
                        <th width="15%">ตารางที่แก้ไข</th>
                        <th width="35%">รายละเอียด</th>
                        <th width="10%" class="text-center">เป้าหมาย</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($conn) {
                        // ดึงข้อมูล Logs พร้อม Join ชื่อผู้ใช้งาน
                        $sql = "SELECT l.*, u.full_name 
                                FROM sys_logs l 
                                LEFT JOIN sys_users u ON l.user_id = u.id 
                                ORDER BY l.id DESC LIMIT 500";
                        
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            // กำหนดสีตามประเภทกิจกรรม
                            $action = $row['action'];
                            $st_class = 'bg-secondary';
                            if ($action == 'ADD') $st_class = 'bg-add';
                            if ($action == 'EDIT') $st_class = 'bg-edit';
                            if ($action == 'DELETE') $st_class = 'bg-delete';
                    ?>
                        <tr>
                            <td>
                                <small class="text-muted d-block"><?php echo date('d/m/Y', strtotime($row['log_time'])); ?></small>
                                <strong><?php echo date('H:i:s', strtotime($row['log_time'])); ?></strong>
                            </td>
                            <td>
                                <small><i class="fa fa-user-circle text-pink me-1"></i><?php echo $row['full_name'] ?? 'System'; ?></small>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $st_class; ?>"><?php echo $action; ?></span>
                            </td>
                            <td>
                                <!-- <span class="badge bg-light text-dark border"><?php echo $row['target_table']; ?></span> -->
                                <small class="text-muted"><?php echo htmlspecialchars($row['target_table']); ?></small>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo htmlspecialchars($row['details']); ?></small>
                            </td>
                            <td class="text-center">
                                <small class="text-muted"><?php echo htmlspecialchars($row['target_id']); ?></small>
                                <!-- <span class="badge bg-white text-pink border">#<?php echo $row['target_id']; ?></span> -->
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
        $('#logTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
            "pageLength": 10,
            "order": [[0, "desc"]] // เรียงจากใหม่ไปเก่า
        });
    });
</script>

</body>
</html>