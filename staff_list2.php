<?php
include('auth.php'); 
checkAdmin(); 
include('config.php'); 

// 1. รับค่าตัวแปรควบคุมการแสดงผล (ใช้แบบสั้นและชัดเจน)
$show_all = (isset($_GET['show_all']) && $_GET['show_all'] == '1');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-pink thead { background-color: #ffdae3; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }

        .custom-toolbar {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: nowrap;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .filter-item { flex: 1; min-width: 140px; }
        .show-item { width: auto; min-width: 100px; }
        .search-item { width: auto; min-width: 200px; }

        .form-select-sm, .form-control-sm {
            border-radius: 8px !important;
            border: 1px solid #ffdae3 !important;
            transition: all 0.3s ease-in-out;
        }
        .form-select-sm:focus, .form-control-sm:focus {
            border-color: #ff85a2 !important;
            box-shadow: 0 0 0 0.25rem rgba(255, 133, 162, 0.25) !important;
            outline: 0;
        }

        .dataTables_wrapper .row:first-child { display: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
        <div class="ms-auto d-flex gap-2">
            <a href="staff_list.php" class="btn btn-light btn-sm fw-bold text-dark">📋รายชื่อเจ้าหน้าที่</a>
            <a href="index.php" class="btn btn-light btn-sm fw-bold text-dark">🏠 กลับหน้า Dashboard</a>
            <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="return confirm('ออกจากระบบ?')"> 🚪 ออกจากระบบ </a>
        </div>
    </div>
</nav>

<div class="container"> 
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-secondary">📋 รายชื่อเจ้าหน้าที่ </h4>
        <div class="d-flex gap-2">
        <a href="export_excel3.php" id="btnExport" class="btn btn-outline-success btn-sm shadow-sm px-3">
        <i class="fa-solid fa-file-excel"></i> ส่งออก Excel
    </a>
    <?php if ($show_all): ?>
        <a href="staff_list2.php" class="btn btn-danger btn-sm shadow-sm px-3">
            <i class="fa-solid fa-filter"></i> แสดงเฉพาะที่ใช้งาน
        </a>
    <?php else: ?>
        <a href="staff_list2.php?show_all=1" class="btn btn-outline-primary btn-sm shadow-sm px-3">
            <i class="fa-solid fa-users-viewfinder"></i> แสดงรายชื่อทั้งหมด
        </a>
    <?php endif; ?>

    
</div>
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
            <div class="filter-item">
                <label class="small text-muted mb-1">กลุ่มงาน</label>
                <select id="filter_gname" class="form-select form-select-sm"><option value="">--ทั้งหมด--</option></select>
            </div>
            <div class="filter-item">
                <label class="small text-muted mb-1">หน่วยงาน</label>
                <select id="filter_dname" class="form-select form-select-sm"><option value="">--ทั้งหมด--</option></select>
            </div>
            <div class="filter-item">
                <label class="small text-muted mb-1">กลุ่มงาน (ตาม จ)</label>
                <select id="filter_gname_s" class="form-select form-select-sm"><option value="">--ทั้งหมด--</option></select>
            </div>
            <div class="filter-item">
                <label class="small text-muted mb-1">หน่วยงาน (ตาม จ)</label>
                <select id="filter_dname_s" class="form-select form-select-sm"><option value="">--ทั้งหมด--</option></select>
            </div>
            <div class="search-item">
                <label class="small text-muted mb-1">ค้นหา</label>
                <input type="text" id="customSearch" class="form-control form-control-sm" >
            </div>
        </div>

        <div class="table-responsive">
            <table id="staffTable" class="table table-hover align-middle w-100">
                <thead class="table-pink">
                    <tr>
                        <th>สถานะ</th>
                        <th>กลุ่มงาน/หน่วยงาน</th>
                        <th>กลุ่มงาน/หน่วยงาน (ตาม จ)</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>ตำแหน่ง</th>
                        <th>ประเภท</th>
                        <th class="text-center"> </th>
                    </tr>
                </thead>
                <tbody>
                  <?php
                    // --- 1. ตรวจสอบสถานะการรับค่า (เพื่อความชัวร์) ---
                    $is_show_all = (isset($_GET['show_all']) && $_GET['show_all'] == '1');

                    // --- 2. สร้างเงื่อนไข SQL แบบแยกส่วน ---
                    $where_sql = ""; 
                    if (!$is_show_all) {
                        $where_sql = " WHERE s.work_status = 'Y' ";
                    }

                    // --- 3. เขียน Query โดยใช้การต่อข้อความ (Concatenation) ---
                    $sql = "SELECT s.*, g.NAME AS gname, d.NAME AS dname, pos.NAME AS posname, 
                                t.NAME AS tname, ds.d_name AS dname_s, gs.g_name AS gname_s
                            FROM staff_main s
                            LEFT JOIN ref_group g ON s.group_id = g.id
                            LEFT JOIN ref_dept d ON s.dept_id = d.id
                            LEFT JOIN ref_position pos ON s.position_id = pos.id
                            LEFT JOIN ref_type t ON s.type_id = t.id 
                            LEFT JOIN ref_dept_s ds ON ds.d_id = s.s_dept_id
                            LEFT JOIN ref_group_s gs ON gs.g_id = s.s_group_id" 
                            . $where_sql; // เชื่อม SQL ตรงนี้

                    $result = mysqli_query($conn, $sql);

                    // --- 4. ดักจับข้อผิดพลาด (ถ้า SQL พิมพ์ผิดจะแจ้งที่นี่) ---
                    if (!$result) {
                        die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . mysqli_error($conn));
                    }

                    while($row = mysqli_fetch_assoc($result)) {
                        $st_class = ($row['work_status'] == 'Y') ? 'bg-success' : 'bg-danger';
                        $st_text = ($row['work_status'] == 'Y') ? 'ใช้งาน' : 'ไม่ใช้งาน';
                    ?>
                        <tr>
                            <td><span class="badge <?php echo $st_class; ?>"><?php echo $st_text; ?></span></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-secondary small"><?php echo $row['gname'] ?? '-'; ?></span>
                                    <strong class="text-dark"><?php echo $row['dname'] ?? '-'; ?></strong>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-muted small"><?php echo $row['gname_s'] ?? '-'; ?></span>
                                    <strong class="text-dark"><?php echo $row['dname_s'] ?? '-'; ?></strong>
                                </div>
                            </td>
                            <td><small class="fw-bold"><?php echo $row['fname']." ".$row['lname']; ?></small></td>
                            <td><small><?php echo $row['posname'] ?? '-'; ?></small></td>
                            <td><small><?php echo $row['tname'] ?? '-'; ?></small></td>
                            <td class="text-center">
                                <a href="view.php?id=<?php echo $row['cid']; ?>" class="btn btn-sm btn-light border rounded-circle">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </a>
                                
                            </td>
                        </tr>
                    <?php } ?>
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
    var table = $('#staffTable').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
        "pageLength": 10,
        "dom": 'rtip',
        "stateSave": false, // เพิ่มบรรทัดนี้เพื่อไม่ให้มันจำการค้นหาค้างไว้
        "order": [], // ไม่ต้องบังคับเรียงลำดับตอนโหลด
        initComplete: function () {
            var api = this.api();
            api.column(1).nodes().to$().each(function() {
                var g = $(this).find('span.text-secondary').text().trim();
                var d = $(this).find('strong.text-dark').text().trim();
                if (g && g !== '-' && $("#filter_gname option[value='" + g + "']").length === 0) 
                    $('#filter_gname').append('<option value="' + g + '">' + g + '</option>');
                if (d && d !== '-' && $("#filter_dname option[value='" + d + "']").length === 0) 
                    $('#filter_dname').append('<option value="' + d + '">' + d + '</option>');
            });
            api.column(2).nodes().to$().each(function() {
                var gs = $(this).find('span.text-muted').text().trim();
                var ds = $(this).find('strong.text-dark').text().trim();
                if (gs && gs !== '-' && $("#filter_gname_s option[value='" + gs + "']").length === 0) 
                    $('#filter_gname_s').append('<option value="' + gs + '">' + gs + '</option>');
                if (ds && ds !== '-' && $("#filter_dname_s option[value='" + ds + "']").length === 0) 
                    $('#filter_dname_s').append('<option value="' + ds + '">' + ds + '</option>');
            });
            sortDropdown('#filter_gname'); sortDropdown('#filter_dname');
            sortDropdown('#filter_gname_s'); sortDropdown('#filter_dname_s');
        }
    });

    function updateExportLink() {
        var params = $.param({
            g: $('#filter_gname').val(),
            d: $('#filter_dname').val(),
            gs: $('#filter_gname_s').val(),
            ds: $('#filter_dname_s').val(),
            search: $('#customSearch').val(),
            show_all: '<?php echo $show_all ? "1" : "0"; ?>'
        });
        $('#btnExport').attr('href', 'export_excel3.php?' + params);
    }

    $('#customLength').on('change', function() { table.page.len($(this).val()).draw(); });
    $('#customSearch').on('keyup', function() { 
        table.search($(this).val()).draw(); 
        updateExportLink();
    });

    $('#filter_gname, #filter_dname, #filter_gname_s, #filter_dname_s').on('change', function() {
        table.draw(); // ใช้ draw() เพื่อให้การค้นหาทำงาน
        updateExportLink();
    });

    // เพิ่มฟังก์ชันค้นหาพิเศษสำหรับ Dropdown (Custom Filter)
   $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    var fg = $('#filter_gname').val();
    var fd = $('#filter_dname').val();
    var fgs = $('#filter_gname_s').val();
    var fds = $('#filter_dname_s').val();
    
    // ถ้าไม่มีการเลือก Filter ใดๆ เลย ให้แสดงข้อมูลทั้งหมด (ป้องกันอาการแว็บหาย)
    if (fg === "" && fd === "" && fgs === "" && fds === "") {
        return true; 
    }

    var rowG = data[1] || ""; // คอลัมน์ กลุ่มงาน/หน่วยงาน
    var rowS = data[2] || ""; // คอลัมน์ กลุ่มงาน/หน่วยงาน (ตาม จ)

    // ตรวจสอบเงื่อนไขการ Filter
    var matchG = (fg === "" || rowG.indexOf(fg) !== -1);
    var matchD = (fd === "" || rowG.indexOf(fd) !== -1);
    var matchGS = (fgs === "" || rowS.indexOf(fgs) !== -1);
    var matchDS = (fds === "" || rowS.indexOf(fds) !== -1);

    return matchG && matchD && matchGS && matchDS;
});

    function sortDropdown(id) {
        var cl = $(id);
        var opts = cl.find('option:not(:first-child)');
        opts.sort(function(a, b) { return $(a).text().localeCompare($(b).text(), 'th'); });
        cl.append(opts);
    }
    
    updateExportLink();
});
</script>

<!-- footer -->
<?php include('footer.php'); ?>
</body>
</html>