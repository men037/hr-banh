<?php
include('auth.php'); 
checkAdmin(); 
include('config.php'); 

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
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; margin: 0; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); z-index: 1050; position: relative; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-pink thead { background-color: #ffdae3; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }
        .custom-toolbar { display: flex; align-items: flex-end; gap: 10px; flex-wrap: nowrap; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .filter-item { flex: 1; min-width: 140px; }
        .show-item { width: auto; min-width: 100px; }
        .search-item { width: auto; min-width: 200px; }
        .form-select-sm, .form-control-sm { border-radius: 8px !important; border: 1px solid #ffdae3 !important; }
        .dataTables_wrapper .row:first-child { display: none; }

        /* --- แก้ไขปัญหา Sidebar ทับเนื้อหา --- */
        .wrapper { 
            display: flex; 
            width: 100%; 
        }

        /* ส่วนเนื้อหาหลัก: บังคับเว้นที่ด้านซ้าย */
        #content {
            flex: 1;
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            background-color: #fff5f7;
            /* ผลักเนื้อหาออกไป 250px หาก Sidebar กว้างกว่านี้ให้ปรับเพิ่มตัวเลขครับ */
            margin-left: 250px; 
            transition: all 0.3s;
        }

        /* ปรับปรุงตารางให้ Scroll ภายใน */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* Responsive: ถ้าหน้าจอเล็กมาก (มือถือ) ให้เอา margin ออก */
        @media (max-width: 768px) {
            #content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>



<div class="wrapper">
    <?php include('sidebar.php'); ?>

    <div id="content">
        <div class="container-fluid"> 
               <nav class="navbar navbar-dark mb-4 shadow-sm" style="border-radius: 15px;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">รายชื่อเจ้าหน้าที่ (ตาม จ)</a>
        </div>
    </nav>
            <div class="d-flex justify-content-end gap-2 w-100 mb-3">
    <a href="export_excel3.php" id="btnExport" class="btn btn-outline-success btn-sm shadow-sm px-3">
        <i class="fa-solid fa-file-excel"></i> ส่งออก Excel
    </a>
    <a href="staff_list2.php<?php echo $show_all ? '' : '?show_all=1'; ?>" class="btn <?php echo $show_all ? 'btn-danger' : 'btn-outline-primary'; ?> btn-sm shadow-sm px-3">
        <i class="fa-solid <?php echo $show_all ? 'fa-filter' : 'fa-users-viewfinder'; ?>"></i> 
        <?php echo $show_all ? 'แสดงเฉพาะที่ใช้งาน' : 'แสดงรายชื่อทั้งหมด'; ?>
    </a>
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
                        <label class="small text-muted mb-1">กลุ่มงาน (จ)</label>
                        <select id="filter_gname_s" class="form-select form-select-sm"><option value="">--ทั้งหมด--</option></select>
                    </div>
                    <div class="filter-item">
                        <label class="small text-muted mb-1">หน่วยงาน (จ)</label>
                        <select id="filter_dname_s" class="form-select form-select-sm"><option value="">--ทั้งหมด--</option></select>
                    </div>
                     <button type="button" id="btn_refresh_filter" class="btn btn-sm btn-outline-secondary">
    <i class="fa-solid fa-rotate"></i> รีเฟรช
</button>
                    <div class="search-item">
                        <label class="small text-muted mb-1">ค้นหา</label>
                        <input type="text" id="customSearch" class="form-control form-control-sm" placeholder="พิมพ์เพื่อค้นหา...">
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
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where_sql = $show_all ? "" : " WHERE s.work_status = 'Y' ";
                            $sql = "SELECT s.*, g.NAME AS gname, d.NAME AS dname, pos.NAME AS posname, 
                                           t.NAME AS tname, ds.d_name AS dname_s, gs.g_name AS gname_s, f.name as prefix
                                    FROM staff_main s
                                    LEFT JOIN ref_group g ON s.group_id = g.id
                                    LEFT JOIN ref_dept d ON s.dept_id = d.id
                                    LEFT JOIN ref_position pos ON s.position_id = pos.id
                                    LEFT JOIN ref_type t ON s.type_id = t.id 
                                    LEFT JOIN ref_dept_s ds ON ds.d_id = s.s_dept_id
                                    LEFT JOIN ref_group_s gs ON gs.g_id = s.s_group_id
                                    LEFT JOIN ref_prefix f ON s.prefix_id = f.id " 
                                    . $where_sql;

                            $result = mysqli_query($conn, $sql);
                            while($row = mysqli_fetch_assoc($result)) {
                                $st_class = ($row['work_status'] == 'Y') ? 'bg-success' : 'bg-danger';
                                $st_text = ($row['work_status'] == 'Y') ? 'ใช้งาน' : 'ไม่ใช้งาน';
                            ?>
                                <tr>
                                    <td><span class="badge <?php echo $st_class; ?>"><?php echo $st_text; ?></span></td>
                                    <td data-gname="<?php echo $row['gname']; ?>" data-dname="<?php echo $row['dname']; ?>">
                                        <div class="d-flex flex-column">
                                            
                                            <span class="text-secondary small"><i class="fa-solid fa-layer-group me-1 text-secondary" style="font-size: 0.8rem;"></i>
                                            <?php echo $row['gname'] ?? '-'; ?></span>
                                            <strong class="text-dark"><i class="fa-solid fa-house-medical me-1 text-info" style="font-size: 0.8rem;"></i>
                                            <?php echo $row['dname'] ?? '-'; ?></strong>
                                        </div>
                                    </td>
                                    <td data-gname-s="<?php echo $row['gname_s']; ?>" data-dname-s="<?php echo $row['dname_s']; ?>">
                                        <div class="d-flex flex-column">
                                            <span class="text-muted small"><?php echo $row['gname_s'] ?? '-'; ?></span>
                                            <strong class="text-dark"><?php echo $row['dname_s'] ?? '-'; ?></strong>
                                        </div>
                                    </td>
                                    <td><small class="fw-bold"><?php echo $row['prefix']."".$row['fname']." ".$row['lname']; ?></small></td>
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
        
        <?php include('footer.php'); ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    const getStored = (id) => localStorage.getItem('staff_filter_' + id) || "";
    const setStored = (id, val) => localStorage.setItem('staff_filter_' + id, val);

    var table = $('#staffTable').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
        "dom": 'rtip',
        "stateSave": true,
        "pageLength": 10,
        initComplete: function () {
            var api = this.api();
            const lists = { g: new Set(), d: new Set(), gs: new Set(), ds: new Set() };
            
            api.rows().every(function() {
                const node = $(this.node());
                const g = node.find('td:eq(1)').data('gname');
                const d = node.find('td:eq(1)').data('dname');
                const gs = node.find('td:eq(2)').data('gname-s');
                const ds = node.find('td:eq(2)').data('dname-s');

                if(g && g !== '-') lists.g.add(g);
                if(d && d !== '-') lists.d.add(d);
                if(gs && gs !== '-') lists.gs.add(gs);
                if(ds && ds !== '-') lists.ds.add(ds);
            });

            lists.g.forEach(v => $('#filter_gname').append(new Option(v, v)));
            lists.d.forEach(v => $('#filter_dname').append(new Option(v, v)));
            lists.gs.forEach(v => $('#filter_gname_s').append(new Option(v, v)));
            lists.ds.forEach(v => $('#filter_dname_s').append(new Option(v, v)));

            $('#filter_gname').val(getStored('gname'));
            $('#filter_dname').val(getStored('dname'));
            $('#filter_gname_s').val(getStored('gname_s'));
            $('#filter_dname_s').val(getStored('dname_s'));
            
            const state = api.state.loaded();
            if (state) $('#customSearch').val(state.search.search);

            $('.filter-item select').each(function() {
                var options = $(this).find('option:not(:first)');
                options.sort((a,b) => $(a).text().localeCompare($(b).text(), 'th'));
                $(this).append(options);
            });

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const row = $(table.row(dataIndex).node());
                const f = {
                    g: $('#filter_gname').val(),
                    d: $('#filter_dname').val(),
                    gs: $('#filter_gname_s').val(),
                    ds: $('#filter_dname_s').val()
                };
                const matchG = !f.g || row.find('td:eq(1)').data('gname') === f.g;
                const matchD = !f.d || row.find('td:eq(1)').data('dname') === f.d;
                const matchGS = !f.gs || row.find('td:eq(2)').data('gname-s') === f.gs;
                const matchDS = !f.ds || row.find('td:eq(2)').data('dname-s') === f.ds;
                return matchG && matchD && matchGS && matchDS;
            });

            table.draw();
        }
    });

    $('.filter-item select').on('change', function() {
        setStored($(this).attr('id').replace('filter_', ''), $(this).val());
        table.draw();
        updateExport();
    });

    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
        updateExport();
    });

    $('#customLength').on('change', function() {
        table.page.len(this.value).draw();
    });
// 3. ปุ่มรีเฟรชตัวกรอง
$(document).on('click', '#btn_refresh_filter', function() {
    // 1. ล้างค่าใน Dropdown ทุกตัวบนหน้าจอ
    $('.filter-item select').val('');
    $('#customSearch').val('');

    // 2. ล้างค่าที่จำไว้ใน LocalStorage (ต้องลบออกให้หมด)
    ['gname', 'dname', 'gname_s', 'dname_s'].forEach(id => {
        localStorage.removeItem('staff_filter_' + id);
    });

    // 3. ล้าง State ของ DataTable (เช่น หน้าที่ค้างอยู่ หรือการเรียงลำดับ)
    table.state.clear();

    // 4. สั่งให้ตารางค้นหาเป็นค่าว่างและวาดใหม่
    table.search('').columns().search('').draw();
    
    // 5. เรียกใช้ฟังก์ชันอัปเดตลิงก์ (เช็คชื่อฟังก์ชันให้ตรงกับด้านล่าง)
    updateExport(); 
});
    
    function updateExport() {
        const params = $.param({
            g: $('#filter_gname').val(),
            d: $('#filter_dname').val(),
            gs: $('#filter_gname_s').val(),
            ds: $('#filter_dname_s').val(),
            search: $('#customSearch').val(),
            show_all: '<?php echo $show_all ? "1" : "0"; ?>'
        });
        $('#btnExport').attr('href', 'export_excel3.php?' + params);
    }
});
</script>

</body>
</html>