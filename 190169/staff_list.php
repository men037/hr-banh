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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        
        .btn-outline-pink { color: #ff85a2; border-color: #ff85a2; border-radius: 20px; transition: 0.3s; }
        .btn-outline-pink:hover { background-color: #ff85a2; color: white; transform: scale(1.05); }
        
        .table-pink thead { background-color: #ffdae3; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }
        .status-icon { font-size: 1.2rem; }

        /* จัดระเบียบแถวบนของตาราง (Filters + Search) */
        .dataTables_wrapper .row:first-child {
            padding: 10px 0 15px 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #f2f2f2;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* บังคับให้ Dropdown และ Search อยู่แถวเดียวกัน */
        .dataTables_filter {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            margin-top: 0 !important;
            flex-wrap: nowrap !important;
        }

        .dataTables_filter label {
            display: flex;
            align-items: center;
            margin-bottom: 0;
            white-space: nowrap;
        }

        /* ปรับแต่งช่อง Input และ Select ให้เข้าธีม */
        .form-select-sm, .dataTables_filter input {
            border-color: #ffdae3 !important;
            border-radius: 8px !important;
        }
        
        .form-select-sm:focus, .dataTables_filter input:focus {
            border-color: #ff85a2 !important;
            box-shadow: 0 0 0 0.25rem rgba(255, 133, 162, 0.25) !important;
            outline: none;
        }

        .dataTables_filter input {
            width: 150px !important;
            margin-left: 5px !important;
        }
        
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
        <div class="ms-auto d-flex gap-2">
            <a href="index.php" class="btn btn-light btn-sm fw-bold text-dark" style="color:#ff85a2;">🏠 กลับหน้า Dashboard</a>
            <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="return confirm('ออกจากระบบ?')"> 🚪 ออกจากระบบ </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-secondary">📋 รายชื่อเจ้าหน้าที่</h4>
        <div class="d-flex gap-2">
            <a href="export_excel.php" class="btn btn-outline-success btn-sm shadow-sm px-3">📥 ส่งออก Excel</a>
            <?php if ($show_all): ?>
                <a href="staff_list.php" class="btn btn-outline-primary btn-sm shadow-sm">🔍 แสดงเฉพาะที่ใช้งาน</a>
            <?php else: ?>
                <a href="staff_list.php?show_all=1" class="btn btn-outline-primary btn-sm shadow-sm">📁 แสดงรายชื่อทั้งหมด</a>
            <?php endif; ?>
            <a href="add_staff.php" class="btn btn-outline-danger btn-sm shadow-sm px-3"> 😀 เพิ่มข้อมูลใหม่ </a>
        </div>
    </div>

    <div class="card p-4 shadow-sm">
        <div id="filter_container" style="display:none;">
            <div class="d-flex gap-2 align-items-center me-2">
                <select id="filter_gname" class="form-select form-select-sm" style="width: 180px;">
                    <option value="">📂 ทุกกลุ่มงาน</option>
                </select>
                <select id="filter_dname" class="form-select form-select-sm" style="width: 180px;">
                    <option value="">🏢 ทุกหน่วยงาน</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="staffTable" class="table table-hover align-middle">
                <thead class="table-pink">
                    <tr>
                        <th width="5%">สถานะ</th>
                        <th width="20%">กลุ่มงาน/หน่วยงาน</th>
                        <th width="15%">ชื่อ-นามสกุล</th>
                        <th width="20%">ตำแหน่ง</th>
                        <th width="10%">ประเภท</th>
                        <th width="7%" class="text-center">eKYC</th>
                        <th width="7%" class="text-center">Provider</th>
                        <th width="5%" class="text-center">แก้ไข</th>
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
                        $sql .= " ORDER BY s.group_id ASC,s.dept_id ASC,s.staff_id ASC ";
                        
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)) {
                            $st_class = ($row['work_status'] == 'Y') ? 'bg-success' : 'bg-danger';
                            $st_text = ($row['work_status'] == 'Y') ? 'ใช้งาน' : 'ไม่ใช้งาน';
                    ?>
                        <tr>
                            <td><span class="badge <?php echo $st_class; ?>"><?php echo $st_text; ?></span></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="mb-1">
                                        <i class="fa-solid fa-layer-group me-1 text-secondary" style="font-size: 0.8rem;"></i>
                                        <span class="gname-text text-secondary" style="font-size: 0.85rem;"><?php echo $row['gname'] ?? '-'; ?></span>
                                    </div>
                                    <div>
                                        <i class="fa-solid fa-house-medical me-1 text-info" style="font-size: 0.8rem;"></i>
                                        <strong class="dname-text text-dark" style="font-size: 0.9rem;"><?php echo $row['dname'] ?? '-'; ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><small class="fw-bold"><?php echo $row['fname']." ".$row['lname']; ?></small></td>
                            <td><small><?php echo $row['posname'] ?? '-'; ?></small></td>
                            <td><small><?php echo $row['tname'] ?? '-'; ?></small></td>
                            <td class="text-center status-icon">
                                <?php echo ($row['ekyc_status'] == 'Y') ? '✅' : '❌'; ?>
                            </td>
                            <td class="text-center status-icon">
                                <?php echo ($row['provider_status'] == 'Y') ? '✅' : '❌'; ?>
                            </td>
                            <td class="text-center">
                                <a href="edit.php?id=<?php echo $row['cid']; ?>" class="btn btn-sm btn-info text-white shadow-sm px-3">แก้ไข</a>
                                <span style="display:none;">
                                    <?php echo $row['cid']; ?> 
                                    <?php echo $row['license_no']; ?> 
                                    <?php echo $row['staff_id']; ?>
                                </span>
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
    var table = $('#staffTable').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
        "pageLength": 10,
        "order": [],
        initComplete: function () {
            var api = this.api();

            // ย้าย Dropdown ไปไว้ในแถวเดียวกับ Search
            var searchWrapper = $('#staffTable_wrapper .dataTables_filter');
            $('#filter_container div').prependTo(searchWrapper);

            // ดึงข้อมูลกลุ่มงาน
            api.column(1).nodes().to$().find('.gname-text').each(function() {
                var txt = $(this).text().trim();
                if (txt && txt !== '-' && $("#filter_gname option[value='" + txt + "']").length === 0) {
                    $('#filter_gname').append('<option value="' + txt + '">' + txt + '</option>');
                }
            });

            // ดึงข้อมูลหน่วยงาน
            api.column(1).nodes().to$().find('.dname-text').each(function() {
                var txt = $(this).text().trim();
                if (txt && txt !== '-' && $("#filter_dname option[value='" + txt + "']").length === 0) {
                    $('#filter_dname').append('<option value="' + txt + '">' + txt + '</option>');
                }
            });
            
            sortDropdown('#filter_gname');
            sortDropdown('#filter_dname');
        }
    });

    function sortDropdown(selectId) {
        var cl = $(selectId);
        var opts = cl.find('option:not(:first-child)');
        opts.sort(function(a, b) {
            return $(a).text().localeCompare($(b).text(), 'th');
        });
        cl.append(opts);
    }

    // ระบบกรองข้อมูล (ชุดเดียวจบ)
    $('#filter_gname, #filter_dname').on('change', function() {
        var gname = $('#filter_gname').val();
        var dname = $('#filter_dname').val();

        table.column(1).search(''); 

        if (gname && dname) {
            // กรองทั้ง 2 อย่างพร้อมกัน
            var searchStr = '(?=.*' + gname + ')(?=.*' + dname + ')';
            table.column(1).search(searchStr, true, false).draw();
        } else if (gname) {
            table.column(1).search(gname).draw();
        } else if (dname) {
            table.column(1).search(dname).draw();
        } else {
            table.column(1).search('').draw();
        }
    });
});
</script>

</body>
</html>