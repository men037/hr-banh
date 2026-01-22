<?php 
// 1. เชื่อมต่อฐานข้อมูล
include('config.php'); 

// 2. ตรวจสอบสถานะการแสดงผล (Toggle Show All)
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == '1';

// 3. ตรวจสอบสถานะการเชื่อมต่อสำหรับ Navbar
$db_status_text = ($conn) ? "เชื่อมต่อฐานข้อมูลสำเร็จ" : "การเชื่อมต่อล้มเหลว";
$db_status_color = ($conn) ? "text-white" : "text-danger";

// 4. ฟังก์ชันดึงข้อมูลสำหรับ Charts (ดึงค่าจริงจาก DB)
function getChartData($conn, $table, $join_field) {
    $labels = [];
    $data = [];
    $sql = "SELECT t.name as label, COUNT(s.cid) as total 
            FROM $table t 
            LEFT JOIN staff_main s ON s.$join_field = t.id AND s.work_status = 'Y'
            GROUP BY t.id";
    $res = mysqli_query($conn, $sql);
    while($r = mysqli_fetch_assoc($res)) {
        $labels[] = $r['label'];
        $data[] = $r['total'];
    }
    return ['labels' => $labels, 'data' => $data];
}

// ดึงข้อมูลจริงเข้าตัวแปรสำหรับส่งให้ JavaScript
$groupData = getChartData($conn, 'ref_group', 'group_id');
$typeData = getChartData($conn, 'ref_type', 'type_id');
$posData = getChartData($conn, 'ref_position', 'position_id');

// ข้อมูลเพศ (กรณีไม่ได้ทำตารางแยก)
$genderLabels = ['ชาย', 'หญิง'];
$genderData = [0, 0];
$res_gen = mysqli_query($conn, "SELECT gender_id, COUNT(*) as total FROM staff_main WHERE work_status = 'Y' GROUP BY gender_id");
while($rg = mysqli_fetch_assoc($res_gen)) {
    if($rg['gender_id'] == 1) $genderData[0] = $rg['total'];
    if($rg['gender_id'] == 2) $genderData[1] = $rg['total'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบข้อมูลเจ้าหน้าที่ - รพ.บ้านนา</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(255, 133, 162, 0.1); }
        .btn-pink { background-color: #ff85a2; color: white; border-radius: 20px; border: none; transition: 0.3s; }
        .btn-pink:hover { background-color: #f06292; color: white; transform: scale(1.05); }
        .table-pink thead { background-color: #ffdae3; }
        .chart-container { position: relative; height: 180px; width: 100%; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
        <span class="badge rounded-pill bg-light <?php echo $db_status_color; ?> p-2">
            Status: <?php echo $db_status_text; ?>
        </span>
    </div>
</nav>

<div class="container">
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card p-3 text-center"><h6 class="fw-bold">กลุ่มงาน</h6><div class="chart-container"><canvas id="chartGroup"></canvas></div></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><h6 class="fw-bold">เพศ (ชาย/หญิง)</h6><div class="chart-container"><canvas id="chartGender"></canvas></div></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><h6 class="fw-bold">ประเภทบุคลากร</h6><div class="chart-container"><canvas id="chartType"></canvas></div></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><h6 class="fw-bold">ตำแหน่ง</h6><div class="chart-container"><canvas id="chartPos"></canvas></div></div></div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-secondary">รายชื่อเจ้าหน้าที่</h4>
        <div>
            <?php if ($show_all): ?>
                <a href="index.php" class="btn btn-outline-success btn-sm me-2 shadow-sm">🔍 แสดงเฉพาะที่ใช้งาน</a>
            <?php else: ?>
                <a href="index.php?show_all=1" class="btn btn-outline-danger btn-sm me-2 shadow-sm">📁 แสดงรายชื่อทั้งหมด</a>
            <?php endif; ?>
            <a href="admin_settings.php" class="btn btn-outline-secondary btn-sm me-2">ตั้งค่าระบบ</a>
            <a href="add_staff.php" class="btn btn-pink px-4 shadow-sm fw-bold">+ เพิ่มข้อมูลใหม่</a>
        </div>
    </div>

    <div class="card p-4 shadow-sm">
        <div class="table-responsive">
            <table id="staffTable" class="table table-hover align-middle">
                <thead class="table-pink">
                    <tr>
                        <th>สถานะ</th>
                        <th>หน่วยงาน/กลุ่มงาน</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>ตำแหน่ง</th>
                        <th>ประเภท</th>
                        <th>eKYC</th>
                        <th>Provider</th>
                        <th class="text-center">จัดการ</th>
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
                        
                        // กรองข้อมูลตามสถานะที่เลือก
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
                            <td><?php echo $row['fname']." ".$row['lname']; ?></td>
                            <td><?php echo $row['posname'] ?? '-'; ?></td>
                            <td><?php echo $row['tname'] ?? '-'; ?></td>
                            <td class="text-center"><?php echo ($row['ekyc_status'] == 'Y') ? '✅' : '❌'; ?></td>
                            <td class="text-center"><?php echo ($row['provider_status'] == 'Y') ? '✅' : '❌'; ?></td>
                            <td class="text-center">
                                <a href="edit.php?id=<?php echo $row['cid']; ?>" class="btn btn-sm btn-info text-white shadow-sm">แก้ไข</a>
                            </td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#staffTable').DataTable({
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
            "pageLength": 25
        });

        function createPie(elementId, labels, data) {
            new Chart(document.getElementById(elementId), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#ff85a2', '#ffb3c1', '#f06292', '#ffdae3', '#f48fb1', '#ce93d8', '#81d4fa']
                    }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // ส่งข้อมูลจริงจาก PHP สู่ JavaScript
        createPie('chartGroup', <?php echo json_encode($groupData['labels']); ?>, <?php echo json_encode($groupData['data']); ?>);
        createPie('chartGender', <?php echo json_encode($genderLabels); ?>, <?php echo json_encode($genderData); ?>);
        createPie('chartType', <?php echo json_encode($typeData['labels']); ?>, <?php echo json_encode($typeData['data']); ?>);
        createPie('chartPos', <?php echo json_encode($posData['labels']); ?>, <?php echo json_encode($posData['data']); ?>);
    });
</script>

</body>
</html>