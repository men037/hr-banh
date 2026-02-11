<?php
include('auth.php');
include('config.php');
checkSuperAdmin(); 

// ตรวจสอบโหมดการแสดงผล (ทำงานอยู่ Y หรือ พ้นสภาพ N)
$status_mode = isset($_GET['mode']) ? $_GET['mode'] : 'Y';

// Query ข้อมูลพนักงาน
$sql = "SELECT
          sm.cid AS cid,
          rp.`name` AS prefix,
          sm.fname AS first_name,
          sm.lname AS last_name,
          NULL AS type_name,
          NULL AS department_name,
          pr.name2 AS position_name,
          NULL AS position_type_name,
          NULL AS position_level_name,
          NULL AS position_code,
          pr.CODE AS position_std_id,
          '0' AS position_std_type_id,
          '000-0000000' AS mobile_phone,
          sm.license_no AS license_no,
          sm.staff_id,
          pos.`name` AS position,
          sm.ekyc_status,
          sm.provider_status,
          sm.work_status,
          smr.has_provider_id
        FROM
          staff_main sm
          LEFT JOIN ref_prefix rp ON rp.id = sm.prefix_id
          LEFT JOIN ref_provider_pos pr ON pr.id = sm.provider_pos_id
          LEFT JOIN ref_position pos ON pos.id = sm.position_id
          LEFT JOIN staff_moph_retrieve smr ON smr.moph_id = sm.idmoph 
        WHERE sm.work_status = '$status_mode'  
        ORDER BY sm.staff_id ASC";

$result = mysqli_query($conn, $sql);




?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOPH API Sync - STAFF SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { display: flex; margin: 0; font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .main-content { 
            margin-left: 260px; 
            width: calc(100% - 260px); 
            padding: 25px; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        @media (max-width: 992px) {
            .main-content { margin-left: 80px; width: calc(100% - 80px); }
        }

        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); border-radius: 15px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(255, 133, 162, 0.1); }
        .table-pink thead { background-color: #ffdae3; color: #ff85a2; }
        .status-log {
        height: 300px;
        overflow-y: auto;
        background: #1e1e1e !important; /* พื้นหลังดำเข้ม */
        color: #dcdcdc;
        padding: 15px;
        border-radius: 12px;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 13px;
        line-height: 1.5;
        border: 2px solid #333;
    }
    .log-line {
        border-bottom: 1px solid #2d2d2d;
        padding-bottom: 2px;
    }
    /* สีของข้อความใน Log */
    .text-success { color: #4caf50 !important; } /* เขียว */
    .text-danger { color: #f44336 !important; }  /* แดง */
    .text-warning { color: #ffeb3b !important; } /* เหลือง */
    .text-info { color: #00bcd4 !important; }    /* ฟ้า */
    .text-muted { color: #888 !important; }      /* เทา */
        
        .btn-config {
            background-color: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.4);
            color: white;
            transition: 0.3s;
        }
        .btn-config:hover { background-color: white; color: #ff85a2; }

        .search-box { position: relative; max-width: 300px; }
        .search-box input { padding-left: 35px; border-radius: 10px; border: 1px solid #ffdae3; }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #ff85a2; }
        
        .status-indicator { font-size: 1.1rem; }
    </style>
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <nav class="navbar navbar-dark mb-4 shadow-sm">
        <div class="container-fluid d-flex justify-content-between">
            <span class="navbar-brand fw-bold">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i> MOPH API Sync (IDPADMIN)
            </span>
            <div class="d-flex gap-2">
                <?php if($status_mode == 'Y'): ?>
                    <a href="?mode=N" class="btn btn-sm btn-light text-danger fw-bold">
                        <i class="fa-solid fa-user-slash me-1"></i> แสดงเฉพาะที่ใช้งาน
                    </a>
                <?php else: ?>
                    <a href="?mode=Y" class="btn btn-sm btn-light text-success fw-bold">
                        <i class="fa-solid fa-user-check me-1"></i> แสดงรายชื่อทั้งหมด
                    </a>
                <?php endif; ?>
                <button class="btn btn-sm btn-config" type="button" data-bs-toggle="collapse" data-bs-target="#apiConfigSection">
                    <i class="fa-solid fa-gears me-1"></i> ตั้งค่า API
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-0">
        <div class="collapse mb-4" id="apiConfigSection">
            <div class="card p-4 border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-pink"><i class="fa-solid fa-key me-2"></i>API Configuration</h5>
                    <button type="button" class="btn-close" data-bs-toggle="collapse" data-bs-target="#apiConfigSection"></button>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <label class="form-label small fw-bold text-muted">MOPH IDP Token (Bearer)</label>
                        <textarea id="api_token" class="form-control" rows="3" placeholder="วาง Token ที่นี่..."></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Hospital Code</label>
                        <input type="text" id="hosp_code" class="form-control bg-light" value="10864" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <h5 class="fw-bold mb-0 text-secondary border-bottom pb-2">
                            รายชื่อเจ้าหน้าที่พนักงาน (สถานะ: <?php echo $status_mode == 'Y' ? 'ปกติ' : 'พ้นสภาพ'; ?>)
                        </h5>

                        <div class="d-flex gap-2">
                            <button id="btnSyncAll" class="btn btn-sm btn-success px-3">
                                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Sync All
                            </button>
                            
                            <button id="btnRetrieve" class="btn btn-sm btn-primary px-3" title="ดึงข้อมูลจาก MOPH มาเก็บในตารางสำรอง">
                                <i class="fa-solid fa-cloud-arrow-down me-1"></i> Retrieve Data
                            </button>

                            <button id="btnRefresh" class="btn btn-sm btn-outline-secondary px-3" title="โหลดข้อมูลใหม่">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                            
                            <div class="search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="ค้นหาชื่อ, CID, ตำแหน่ง...">
                            </div>
                        </div>
                        </div>
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover align-middle" id="staffTable">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>ตำแหน่ง</th>
                                    <th>ตำแหน่ง Provider</th>
                                    <th class="text-center">eKYC</th>
                                    <th class="text-center">Provider</th>
                                    <th class="text-center">C-Provider</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><small class="fw-bold"><?php echo $row['prefix'].$row['first_name'].' '.$row['last_name']; ?></small></td>
                                    <td><small><?php echo $row['position'] ?? '-'; ?></small></td>
                                    <td><small><?php echo $row['position_name'] ?? '-'; ?></small></td>
                                    <td class="text-center status-indicator">
                                        <?php echo $row['ekyc_status'] == 'Y' 
                                            ? '<i class="fa-solid fa-circle-check text-success" title="ยืนยันตัวตนแล้ว"></i>' 
                                            : '<i class="fa-solid fa-circle-xmark text-danger opacity-50" title="ยังไม่ยืนยัน"></i>'; ?>
                                    </td>
                                    <td class="text-center status-indicator">
                                        <?php echo $row['provider_status'] == 'Y' 
                                            ? '<i class="fa-solid fa-circle-check text-success" title="Provider Active"></i>' 
                                            : '<i class="fa-solid fa-circle-xmark text-danger opacity-50" title="Inactive"></i>'; ?>
                                    </td>

                                    <td class="text-center status-indicator">
                                        <?php echo $row['has_provider_id'] == '1' 
                                            ? '<i class="fa-solid fa-circle-check text-success" title="Provider Active"></i>' 
                                            : '<i class="fa-solid fa-circle-xmark text-danger opacity-50" title="Inactive"></i>'; ?>
                                    </td>

                                    <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-primary btn-sync" 
                                                data-staff='<?php echo json_encode($row, JSON_UNESCAPED_UNICODE); ?>' title="ส่งเข้า IDPADMIN">
                                            <!-- <i class="fa-solid fa-rotate"></i> -->
                                            <i class="fa-solid fa-cloud-arrow-up"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger btn-delete-moph" 
                                                data-cid="<?php echo $row['cid']; ?>" 
                                                data-name="<?php echo $row['first_name']; ?>"
                                                title="ลบออกจาก IDPADMIN">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        
                                        <a href="edit.php?id=<?php echo $row['cid']; ?>&return_to=moph_api_sync.php" 
                                        class="btn btn-sm btn-warning text-white shadow-sm" title="แก้ไขข้อมูล">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                                </tr>
                                <?php endwhile; ?>
                                <tr id="noData" style="display:none;"><td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูล...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card p-2 bg-dark shadow-sm" style="border: 1px solid #444;"> <div class="d-flex justify-content-between align-items-center mb-1 text-white"> <h6 class="fw-bold mb-0" style="font-size: 0.85rem;"> <i class="fa-solid fa-terminal me-2 text-warning"></i>Console Log Output
                    </h6>
                    <button class="btn btn-sm btn-outline-light py-0" style="font-size: 0.7rem;" onclick="$('#log_output').empty().append('> Cleared...')">ล้าง Log</button>
                </div>
                
                <div id="log_output" class="status-log" 
                    style="max-height: 120px; overflow-y: auto; font-size: 0.8rem; line-height: 1.4; color: #00ff00; background: #000; padding: 8px; border-radius: 4px; font-family: 'Courier New', Courier, monospace;">
                    > Ready for API synchronization...
                </div>
            </div>
            </div>
        </div>
    </div>
    <footer class="mt-auto py-3 text-center text-muted small"><?php include('footer.php'); ?></footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // 1. จัดการ Token และ LocalStorage
    $('#api_token').val(localStorage.getItem('moph_token') || '');
    $('#api_token').on('input', function() { 
        localStorage.setItem('moph_token', $(this).val().trim()); 
    });

    // 2. ระบบค้นหา (Search Filter)
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var visibleRows = 0;
        $("#tableBody tr:not(#noData)").filter(function() {
            var match = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(match);
            if(match) visibleRows++;
        });
        visibleRows === 0 && value !== "" ? $("#noData").show() : $("#noData").hide();
    });

    // 3. ฟังก์ชันสำหรับจัดการ Log สีสันสวยงาม
    function appendLog(msg) {
        const log = $('#log_output');
        const time = new Date().toLocaleTimeString();
        
        let colorClass = 'text-info'; 
        if (msg.includes('✅')) colorClass = 'text-success';
        if (msg.includes('❌') || msg.includes('🚫')) colorClass = 'text-danger';
        if (msg.includes('⚠️') || msg.includes('🚀') || msg.includes('🎬') || msg.includes('📡') || msg.includes('🔄') || msg.includes('🗑️')) colorClass = 'text-warning';

        const logItem = `
            <div class="log-line mb-1">
                <span class="text-muted small">[${time}]</span> 
                <span class="${colorClass}">${msg}</span>
            </div>`;

        log.append(logItem);
        log.animate({ scrollTop: log[0].scrollHeight }, 100);
    }

    // 4. ฟังก์ชันหลักในการส่ง API (Single Sync)
    function syncSingleRow(btn, token, callback = null) {
        const staffData = btn.data('staff');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        appendLog(`📡 [${staffData.cid}] กำลังส่ง: ${staffData.first_name}...`);

        $.ajax({
            url: 'process_moph_api.php',
            method: 'POST',
            data: { token: token, staff: staffData },
            success: function(response) {
                try {
                    const res = JSON.parse(response);
                    if(res.status === 200) {
                        appendLog(`✅ สำเร็จ: ${staffData.first_name}`);
                        btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fa-solid fa-check"></i>');
                    } else {
                        appendLog(`❌ ผิดพลาด [${staffData.first_name}]: ${res.message}`);
                        btn.prop('disabled', false).addClass('btn-danger').html('<i class="fa-solid fa-triangle-exclamation"></i>');
                    }
                } catch(e) {
                    appendLog(`⚠️ JSON Error: ${staffData.first_name}`);
                    btn.prop('disabled', false).html('Err');
                }
                if (callback) callback();
            },
            error: function() {
                appendLog(`🚫 Connection Failed: ${staffData.first_name}`);
                btn.prop('disabled', false).html('Error');
                if (callback) callback();
            }
        });
    }

    // 5. ปุ่ม Sync รายบุคคล
    $(document).on('click', '.btn-sync', function() {
        const token = $('#api_token').val().trim();
        if(!token) {
            bootstrap.Collapse.getOrCreateInstance('#apiConfigSection').show();
            alert('กรุณาใส่ Token ก่อนครับ');
            return;
        }
        syncSingleRow($(this), token);
    });

    // === เพิ่มเติม: ปุ่ม Delete รายบุคคล ===
    $(document).on('click', '.btn-delete-moph', function() {
        const cid = $(this).data('cid');
        const name = $(this).data('name');
        const token = $('#api_token').val().trim();

        if (!token) {
            bootstrap.Collapse.getOrCreateInstance('#apiConfigSection').show();
            alert('กรุณาใส่ Token ก่อนทำการลบครับ');
            return;
        }

        // ระบบยืนยันก่อนลบ (Safety First)
        if (!confirm(`⚠️ ยืนยันการลบคุณ "${name}" ออกจากระบบ MOPH ใช่หรือไม่?\n\n*หมายเหตุ: ข้อมูลจะถูกลบออกจากฐานข้อมูลกลางของกระทรวงทันที`)) {
            return;
        }

        const btn = $(this);
        const oldHtml = btn.html();
        
        // แสดงสถานะกำลังทำงาน
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        appendLog(`🗑️ [${cid}] กำลังส่งคำสั่งลบพนักงานออกจาก MOPH...`, 'warning');

        $.ajax({
            url: 'process_delete_moph.php',
            method: 'POST',
            data: { 
                token: token, 
                cid: cid 
            },
            success: function(res) {
                if (res.status === 'success') {
                    appendLog(`✅ ลบสำเร็จ: ${name} ออกจาก MOPH เรียบร้อยแล้ว`, 'success');
                    alert(res.message);
                    // เปลี่ยนสถานะปุ่มเพื่อแจ้งว่าลบแล้ว
                    btn.removeClass('btn-danger').addClass('btn-secondary').html('<i class="fa-solid fa-user-minus"></i>');
                } else {
                    appendLog(`❌ ลบไม่สำเร็จ: ${res.message}`, 'danger');
                    alert('เกิดข้อผิดพลาด: ' + res.message);
                    btn.prop('disabled', false).html(oldHtml);
                }
            },
            error: function() {
                appendLog(`🚫 Connection Failed: ไม่สามารถติดต่อไฟล์ประมวลผลการลบได้`, 'danger');
                btn.prop('disabled', false).html(oldHtml);
            }
        });
    });

    // 6. ปุ่ม Sync All
    $('#btnSyncAll').click(async function() {
        const token = $('#api_token').val().trim();
        if (!token) {
            bootstrap.Collapse.getOrCreateInstance('#apiConfigSection').show();
            alert('กรุณาใส่ Token ก่อนครับ');
            return;
        }
        const visibleButtons = $('#tableBody tr:visible').find('.btn-sync').not('.btn-success');
        if (visibleButtons.length === 0) {
            alert('ไม่พบรายชื่อที่ต้องการ Sync');
            return;
        }
        if (!confirm(`ยืนยันการส่งข้อมูลเจ้าหน้าที่จำนวน ${visibleButtons.length} รายการ?`)) return;

        $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
        appendLog(`🎬 เริ่มต้นการส่งข้อมูลแบบกลุ่มจำนวน ${visibleButtons.length} รายการ...`);

        for (let i = 0; i < visibleButtons.length; i++) {
            await new Promise((resolve) => {
                syncSingleRow($(visibleButtons[i]), token, resolve);
            });
            await new Promise(r => setTimeout(r, 300));
        }
        $(this).prop('disabled', false).html('<i class="fa-solid fa-cloud-arrow-up me-1"></i> Sync All');
        appendLog(`🏁 เสร็จสิ้นการประมวลผลทั้งหมด`, 'success');
    });

    // 7. ปุ่มรีเฟรชตาราง
    $('#btnRefresh').click(function() {
        const btn = $(this);
        btn.find('i').addClass('fa-spin');
        btn.prop('disabled', true);
        appendLog(`🔄 กำลังรีเฟรชข้อมูลตาราง...`);
        setTimeout(() => {
            window.location.reload();
        }, 500);
    });

    // 8. ปุ่ม Retrieve Data (ดึงข้อมูลกลับมาเก็บในตารางสำรอง)
    $('#btnRetrieve').click(function() {
        const token = $('#api_token').val().trim();
        const hosp_code = $('#hosp_code').val();

        if (!token) {
            bootstrap.Collapse.getOrCreateInstance('#apiConfigSection').show();
            alert('กรุณาใส่ Token ในส่วนตั้งค่าก่อนดึงข้อมูลครับ');
            return;
        }
        if (!confirm(`ยืนยันการดึงข้อมูลจากระบบ MOPH (Retrieve) ของหน่วยงาน ${hosp_code} หรือไม่?`)) return;

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Processing...');
        appendLog(`🎬 เริ่มต้นการ Retrieve ข้อมูลจาก MOPH API...`);

        $.ajax({
            url: 'process_retrieve_to_db.php',
            method: 'POST',
            data: { token: token, hosp_code: hosp_code },
            success: function(response) {
                try {
                    const res = typeof response === 'object' ? response : JSON.parse(response);
                    if(res.status === 'success') {
                        appendLog(`✅ สำเร็จ: ${res.message}`);
                        alert(res.message);
                    } else {
                        appendLog(`❌ ผิดพลาด: ${res.message}`);
                        alert('Error: ' + res.message);
                    }
                } catch(e) {
                    appendLog(`⚠️ Error: การตอบกลับจาก Server ผิดพลาด`);
                }
                btn.prop('disabled', false).html('<i class="fa-solid fa-cloud-arrow-down me-1"></i> Retrieve Data');
            },
            error: function() {
                appendLog(`🚫 Connection Failed: ติดต่อไฟล์ประมวลผลไม่ได้`);
                btn.prop('disabled', false).html('<i class="fa-solid fa-cloud-arrow-down me-1"></i> Retrieve Data');
            }
        });
    });
});
</script>
</body>
</html>