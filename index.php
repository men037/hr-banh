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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* 1. เพิ่ม CSS เพื่อดันเนื้อหาให้พ้นระยะ Sidebar */
        body { 
            font-family: 'Sarabun', sans-serif; 
            background-color: #fff5f7; 
            margin: 0; 
            display: flex; /* บังคับให้ Sidebar กับ Main-content เรียงแนวนอน */
        }

        .main-content { 
            margin-left: 260px; /* ระยะเท่าความกว้าง Sidebar */
            width: calc(100% - 260px); 
            padding: 30px; 
            min-height: 100vh;
            display: flex;
            flex-direction: column; /* จัดเนื้อหาเป็นแนวตั้งเพื่อให้ Footer อยู่ล่างสุด */
        }

        .content-body {
            flex: 1; /* ดัน Footer ให้ตกลงไปข้างล่างสุดของจอเสมอ */
        }

        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(255, 133, 162, 0.1); }
        .chart-container { position: relative; height: 250px; width: 100%; }

        /* ปรับแต่งสำหรับมือถือ */
        @media (max-width: 992px) {
            .main-content { margin-left: 80px; width: calc(100% - 80px); padding: 15px; }
        }
    </style>
</head>
<body>

    <?php include('sidebar.php'); ?>

    <div class="main-content">
        <div class="container-fluid content-body">
            <?php include('dashboard_charts.php'); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>