<?php
// หน้า index.php
include('auth.php'); 
include('config.php'); 
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
        .chart-container { position: relative; height: 250px; width: 100%; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
        <div class="ms-auto d-flex gap-2">
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'super_admin')): ?>
                <a href="staff_list.php" class="btn btn-light btn-sm fw-bold text-pink shadow-sm px-3">📋 รายชื่อเจ้าหน้าที่ </a>
            <?php endif; ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'super_admin'): ?>
                <a href="user_manage.php" class="btn btn-light btn-sm fw-bold text-pink shadow-sm px-3">👥 จัดการผู้ใช้งาน</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="return confirm('ออกจากระบบ?')">🚪 ออกจากระบบ</a>
        </div>
    </div>
</nav>

<?php include('dashboard_charts.php'); ?>
<!-- footer -->
<?php include('footer.php'); ?>
</body>
</html>