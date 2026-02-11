<?php
include('auth.php'); 
include('config.php'); 
checkSuperAdmin(); // ตรวจสอบสิทธิ์ Super Admin
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; margin: 0; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); z-index: 1050; position: relative; border-radius: 15px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-pink thead { background-color: #ffdae3; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }
        
        /* Toolbar สไตล์เดียวกับหน้า Staff */
        .custom-toolbar { display: flex; align-items: flex-end; gap: 10px; flex-wrap: nowrap; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .show-item { width: auto; min-width: 100px; }
        .search-item { flex: 1; min-width: 200px; }
        .form-select-sm, .form-control-sm { border-radius: 8px !important; border: 1px solid #ffdae3 !important; }
        
        /* ซ่อนส่วนค้นหาเดิมของ DataTable */
        .dataTables_wrapper .row:first-child { display: none; }

        .wrapper { display: flex; width: 100%; }

        /* ส่วนเนื้อหาหลัก */
        #content {
            flex: 1;
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            background-color: #fff5f7;
            margin-left: 250px; 
            transition: all 0.3s;
        }

        .table-responsive { width: 100%; overflow-x: auto; }

        /* สีป้ายกิจกรรม */
        .bg-add { background-color: #2dce89; color: white; }
        .bg-edit { background-color: #ffbc00; color: white; }
        .bg-delete { background-color: #f5365c; color: white; }
        .text-pink { color: #ff85a2 !important; }

        @media (max-width: 768px) {
            #content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include('sidebar.php'); ?>

    <div id="content">
        <div class="container-fluid"> 
            <nav class="navbar navbar-dark mb-4 shadow-sm">
                <div class="container-fluid">
                    <a class="navbar-brand fw-bold" href="#">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i>ประวัติการใช้งานระบบ (Logs)
                    </a>
                </div>
            </nav>

            <div class="d-flex justify-content-end mb-3">
                <button onclick="location.reload()" class="btn btn-outline-danger btn-sm shadow-sm px-3">
                    <i class="fa-solid fa-rotate"></i> รีเฟรชข้อมูล
                </button>
            </div>

            <div class="card p-4 shadow-sm">
                <div class="custom-toolbar">
                    <div class="show-item">
                        <label class="small text-muted mb-1">แสดง</label>
                        <select id="customLength" class="form-select form-select-sm">
                            <option value="10">10 แถว</option>
                            <option value="25">25 แถว</option>
                            <option value="50">50 แถว</option>
                            <option value="100">100 แถว</option>
                        </select>
                    </div>
                    <div class="search-item text-end">
                        <label class="small text-muted mb-1">ค้นหาประวัติ</label>
                        <input type="text" id="customSearch" class="form-control form-control-sm w-25 ms-auto" placeholder="ค้นหาชื่อผู้ใช้, กิจกรรม, หรือรายละเอียด...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="logTable" class="table table-hover align-middle w-100">
                        <thead class="table-pink">
                            <tr>
                                <th width="15%">วัน-เวลา</th>
                                <th width="15%">ผู้ใช้งาน</th>
                                <th width="10%" class="text-center">กิจกรรม</th>
                                <th width="15%">ตารางที่แก้ไข</th>
                                <th width="35%">รายละเอียด</th>
                                <th width="10%" class="text-center">ID เป้าหมาย</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($conn) {
                                $sql = "SELECT l.*, u.full_name 
                                        FROM sys_logs l 
                                        LEFT JOIN sys_users u ON l.user_id = u.id 
                                        ORDER BY l.id DESC LIMIT 500";
                                
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($result)) {
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
                                        <small class="text-muted"><?php echo htmlspecialchars($row['target_table']); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['details']); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <small class="fw-bold"><?php echo htmlspecialchars($row['target_id']); ?></small>
                                    </td>
                                </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <?php include('footer.php'); ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#logTable').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
        "dom": 'rtip', // ซ่อน Default Search/Length
        "pageLength": 10,
        "order": [[0, "desc"]] 
    });

    // เชื่อมต่อ Custom Search
    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // เชื่อมต่อ Custom Length
    $('#customLength').on('change', function() {
        table.page.len(this.value).draw();
    });
});
</script>

</body>
</html>