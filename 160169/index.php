<?php
session_start(); // สำคัญมาก: ต้องอยู่บรรทัดแรกสุด ห้ามมีช่องว่างก่อนหน้า
include('auth.php'); // บรรทัดนี้จะสั่งให้ session_start และเช็คว่า Login หรือยัง
include('config.php'); 
$db_status_color = ($conn) ? "text-white" : "text-danger";

// ฟังก์ชันดึงข้อมูลสำหรับ Charts
function getChartData($conn, $table, $join_field) {
    $labels = []; $data = [];
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

$groupData = getChartData($conn, 'ref_group', 'group_id');
$typeData = getChartData($conn, 'ref_type', 'type_id');
$posData = getChartData($conn, 'ref_position', 'position_id');

$genderLabels = ['ชาย', 'หญิง']; $genderData = [0, 0];
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
    <title>Dashboard - ระบบเจ้าหน้าที่ รพ.บ้านนา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(255, 133, 162, 0.1); }
        .btn-main { background-color: #ff85a2; color: white; border-radius: 20px; padding: 10px 25px; text-decoration: none; transition: 0.3s; }
        .btn-main:hover { background-color: #f06292; color: white; transform: translateY(-3px); }
        .chart-container { position: relative; height: 250px; width: 100%; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
        
        <div class="ms-auto d-flex gap-2">
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'super_admin')): ?>
                <a href="staff_list.php" class="btn btn-light btn-sm fw-bold text-pink shadow-sm px-3">
                    📋 รายชื่อเจ้าหน้าที่
                </a>
                <a href="user_manage.php" class="btn btn-light btn-sm fw-bold text-pink shadow-sm px-3">
                    👥 จัดการผู้ใช้งาน
                </a>
            <?php endif; ?>

            <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="return confirm('ออกจากระบบ?')">
                🚪 ออกจากระบบ
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h3 class="fw-bold text-secondary">ภาพรวมบุคลากร</h3>
            <hr class="mx-auto" style="width: 100px; border: 2px solid #ff85a2;">
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card p-4 text-center">
                <h6 class="fw-bold mb-3">กลุ่มงาน</h6>
                <div class="chart-container"><canvas id="chartGroup"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card p-4 text-center">
                <h6 class="fw-bold mb-3">เพศ</h6>
                <div class="chart-container"><canvas id="chartGender"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card p-4 text-center">
                <h6 class="fw-bold mb-3">ประเภทบุคลากร</h6>
                <div class="chart-container"><canvas id="chartType"></canvas></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card p-4 text-center">
                <h6 class="fw-bold mb-3">ตำแหน่ง</h6>
                <div class="chart-container"><canvas id="chartPos"></canvas></div>
            </div>
        </div>
    </div>

    
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function createPie(elementId, labels, data) {
        new Chart(document.getElementById(elementId), {
            type: 'doughnut', // เปลี่ยนเป็นโดนัทเพื่อให้ดูทันสมัยขึ้น
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#ff85a2', '#ffb3c1', '#f06292', '#ffdae3', '#ce93d8', '#81d4fa']
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }
    createPie('chartGroup', <?php echo json_encode($groupData['labels']); ?>, <?php echo json_encode($groupData['data']); ?>);
    createPie('chartGender', <?php echo json_encode($genderLabels); ?>, <?php echo json_encode($genderData); ?>);
    createPie('chartType', <?php echo json_encode($typeData['labels']); ?>, <?php echo json_encode($typeData['data']); ?>);
    createPie('chartPos', <?php echo json_encode($posData['labels']); ?>, <?php echo json_encode($posData['data']); ?>);
</script>
</body>
</html>