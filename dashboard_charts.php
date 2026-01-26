<?php
// 1. ฟังก์ชันดึงข้อมูลกราฟ
function getChartData($conn, $table, $join_field, $label_field = 'name') {
    $labels = []; $data = [];
    $sql = "SELECT t.$label_field as label, COUNT(s.cid) as total 
            FROM $table t 
            LEFT JOIN staff_main s ON s.$join_field = t.id AND s.work_status = 'Y'
            GROUP BY t.id";
    $res = mysqli_query($conn, $sql);
    while($r = mysqli_fetch_assoc($res)) {
        $labels[] = ($r['label'] != "") ? $r['label'] : "ไม่ระบุ";
        $data[] = $r['total'];
    }
    return ['labels' => $labels, 'data' => $data];
}

// 2. ดึงข้อมูลสถิติภาพรวม
$res_total = mysqli_query($conn, "SELECT COUNT(cid) as total FROM staff_main WHERE work_status = 'Y'");
$total_staff = mysqli_fetch_assoc($res_total)['total'];

$genderData = [0, 0];
$res_gen = mysqli_query($conn, "SELECT gender_id, COUNT(*) as total FROM staff_main WHERE work_status = 'Y' GROUP BY gender_id");
while($rg = mysqli_fetch_assoc($res_gen)) {
    if($rg['gender_id'] == 1) $genderData[0] = $rg['total'];
    if($rg['gender_id'] == 2) $genderData[1] = $rg['total'];
}
// // ในไฟล์ dashboard_charts.php
// if (!function_exists('getChartData')) {
//     function getChartData($conn, $table, $labelCol) {
//         // ... โค้ดของคุณ ...
//     }
// }
// 3. ข้อมูล 7 กล่องวิชาชีพ
$pso_list = [   'A0003'=>'เภสัชกร',
                'A0002'=>'ทันตแพทย์',
                'A0001'=>'แพทย์',
                'A0004'=>'พยาบาลวิชาชีพ',
                'A0005'=>'นักเทคนิคการแพทย์',
                'A0016'=>'แพทย์แผนไทย',
                'A0008'=>'นักกายภาพบําบัด'];
$pso_ids_str = "'".implode("','", array_keys($pso_list))."'";
$sql_pso = "SELECT provider_pos_id, COUNT(cid) as count FROM staff_main WHERE work_status = 'Y' AND provider_pos_id IN ($pso_ids_str) GROUP BY provider_pos_id";
$res_pso_query = mysqli_query($conn, $sql_pso);
$pso_counts = [];
while($r = mysqli_fetch_assoc($res_pso_query)) { $pso_counts[$r['provider_pos_id']] = $r['count']; }

// 1. หาจำนวนเจ้าหน้าที่ทั้งหมดที่ทำงานอยู่ (Total = 240)
$sql_total = "SELECT COUNT(cid) as total FROM staff_main WHERE work_status = 'Y'";
$res_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($res_total);
$all_staff = $row_total['total'];

// 2. หาผลรวมของ 7 วิชาชีพหลักที่คุณแสดงผลไปแล้ว
$excluded_ids = ['A0001', 'A0002', 'A0003', 'A0004', 'A0005', 'A0008', 'A0016'];
$sum_main = 0;
foreach ($excluded_ids as $id) {
    // ใช้ค่าจาก array $pso_counts ที่คุณมีอยู่แล้วมาบวกกัน
    $sum_main += $pso_counts[$id] ?? 0;
}

// 3. ค่าอื่นๆ คือ ทั้งหมดลบด้วย 7 วิชาชีพหลัก (240 - 102 = 138)
$total_others = $all_staff - $sum_main;



// 4. ข้อมูลกราฟ
$groupData = getChartData($conn, 'ref_group', 'group_id', 's_name');
$typeData  = getChartData($conn, 'ref_type', 'type_id');

// 1. ดึงรายชื่อประเภทบุคลากร
$type_res = mysqli_query($conn, "SELECT id, name FROM ref_type");
$types = [];
while($t = mysqli_fetch_assoc($type_res)) { $types[$t['id']] = $t['name']; }

// 2. ดึงข้อมูลจำนวนแยกตามกลุ่มงานและประเภท
$sql_stacked = "SELECT g.s_name as group_name, s.type_id, COUNT(s.cid) as total 
                FROM ref_group g 
                LEFT JOIN staff_main s ON s.group_id = g.id AND s.work_status = 'Y'
                GROUP BY g.id, s.type_id";
$res_stacked = mysqli_query($conn, $sql_stacked);

$stacked_data = []; 
while($r = mysqli_fetch_assoc($res_stacked)) {
    $stacked_data[$r['group_name']][$r['type_id']] = (int)$r['total'];
}
$group_labels = array_keys($stacked_data);

// 3. คำนวณเป็นเปอร์เซ็นต์จากฝั่ง PHP
$color_idx = 0;
$typeColors = ['#4bc0c0', '#ffcd56', '#ff9f40', '#9966ff', '#ff6384', '#36a2eb', '#455a64', '#81c784', '#e57373'];
$datasets_json = [];

foreach($types as $type_id => $type_name) {
    $data_percents = [];
    $real_counts = []; // เพิ่มตัวแปรเก็บจำนวนคนจริง
    foreach($group_labels as $g_name) {
        $total_in_group = array_sum($stacked_data[$g_name]);
        $count = isset($stacked_data[$g_name][$type_id]) ? $stacked_data[$g_name][$type_id] : 0;
        
        $val = ($total_in_group > 0) ? round(($count * 100) / $total_in_group, 1) : 0;
        $data_percents[] = $val;
        $real_counts[] = $count; // เก็บจำนวนคน
    }
    
    $datasets_json[] = [
        'label' => $type_name,
        'data' => $data_percents,
        'realCount' => $real_counts, // ✅ ส่งค่าจำนวนคนพ่วงไปด้วย
        'backgroundColor' => $typeColors[$color_idx % 9],
        'borderRadius' => 5
    ];
    $color_idx++;
}
?>

<style>
    .stat-card { border: none; border-radius: 15px; transition: all 0.3s ease; background: #fff; position: relative; padding: 20px 10px !important; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .stat-icon-number { width: 25px; height: 25px; background: #f0f7ff; border: 1px solid #2196f3; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1976d2; font-weight: bold; font-size: 0.75rem; position: absolute; top: 12px; left: 12px; }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: #1a73e8; line-height: 1; }
    .stat-label { font-size: 0.9rem; color: #34495e; font-weight: bold; margin-top: 10px; }
    .stat-sub { font-size: 0.75rem; color: #95a5a6; margin-top: 5px; }
    .stat-img { width: 55px; height: 55px; margin-bottom: 10px; }
    
    /* กล่องบนสุดพิเศษ */
    .hero-stat-card {
        background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
        color: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(26, 115, 232, 0.3);
    }

    
</style>

<div class="container mt-4 mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">📊 ระบบวิเคราะห์ข้อมูลบุคลากร</h2>
    </div>

    <div class="row mb-4 justify-content-center">
    <div class="col-md-4"> 
        <div class="hero-stat-card text-center py-3" style="padding: 15px !important;">
            <i class="fas fa-users fa-2x mb-2"></i> <h6 class="fw-bold opacity-75 mb-1">จำนวนบุคลากรทั้งหมด</h6>
            <div class="display-5 fw-bold"><?php echo number_format($total_staff); ?></div> <p class="small mb-0 opacity-75">คน (สถานะปกติ)</p> </div>
    </div>
</div>

    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow-sm border-0 h-100" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4 text-center text-secondary"><i class="fas fa-venus-mars me-2"></i>สัดส่วนเพศ (ชาย/หญิง)</h6>
                <div style="height: 300px;"><canvas id="chartGender"></canvas></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card p-4 shadow-sm border-0 h-100" style="border-radius: 20px;">
                <h6 class="fw-bold mb-4 text-center text-secondary"><i class="fas fa-user-tag me-2"></i>ประเภทบุคลากร</h6>
                <div style="height: 300px;"><canvas id="chartType"></canvas></div>
            </div>
        </div>
    </div>

  <div class="mb-4">
    <h5 class="fw-bold mb-4 text-dark text-center">
        <i class="fas fa-id-card-alt me-2 text-primary"></i> จำนวนบุคลากรรายวิชาชีพ
    </h5>
    <?php 
    $icons = [
        'A0001' => 'https://cdn-icons-png.flaticon.com/512/3952/3952988.png', 
        'A0002' => 'https://cdn-icons-png.flaticon.com/512/14694/14694150.png', 
        'A0003' => 'https://cdn-icons-png.flaticon.com/512/13892/13892841.png', 
        'A0004' => 'https://cdn-icons-png.flaticon.com/512/5278/5278073.png', 
        'A0005' => 'https://cdn-icons-png.flaticon.com/512/3461/3461651.png', 
        'A0016' => 'https://cdn-icons-png.flaticon.com/512/2382/2382579.png',   
        'A0008' => 'https://cdn-icons-png.flaticon.com/512/3208/3208998.png' 
    ];
    $order = ['A0001', 'A0002', 'A0003', 'A0004', 'A0005', 'A0008', 'A0016'];
    ?>

    <div class="row g-3 mb-3">
        <?php for($i=0; $i<4; $i++): $id = $order[$i]; $val = $pso_counts[$id] ?? 0; ?>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm text-center border-top border-primary border-4 h-100">
                <div class="stat-icon-number"><?php echo $i+1; ?></div>
                <img src="<?php echo $icons[$id]; ?>" class="stat-img mx-auto mt-3">
                <h6 class="fw-bold text-secondary"><?php echo $pso_list[$id]; ?></h6>
                <div class="display-6 fw-bold text-primary"><?php echo number_format($val); ?></div> <!-- เลขแถว1 -->
                <p class="text-muted small mb-0">ราย</p>
            </div>
        </div>
        <?php endfor; ?>
    </div>

    <div class="row g-3 mb-3">
        <?php for($i=4; $i<7; $i++): $id = $order[$i]; $val = $pso_counts[$id] ?? 0; ?>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm text-center border-top border-info border-4 h-100">
                <div class="stat-icon-number"><?php echo $i+1; ?></div>
                <img src="<?php echo $icons[$id]; ?>" class="stat-img mx-auto mt-3">
                <h6 class="fw-bold text-secondary"><?php echo $pso_list[$id]; ?></h6>
                <div class="display-6 fw-bold text-primary"><?php echo number_format($val); ?></div> <!-- เลขแถว2 -->
                <p class="text-muted small mb-0">ราย</p>
            </div>
        </div>
        <?php endfor; ?>

        <div class="col-md-3">
           <div class="card stat-card shadow-sm text-center border-top border-info border-4 h-100">
                <div class="stat-icon-number"><?php echo $i+1; ?></div>
                        <img src="https://cdn-icons-png.flaticon.com/512/9733/9733449.png" class="stat-img mx-auto mt-3"> 
                    <h6 class="fw-bold text-secondary">สายสนับสนุนและอื่นๆ</h6>
                    <div class="display-6 fw-bold text-primary"> <?php echo number_format($total_others); ?> </div> <!-- ข้อ8 -->
                    <p class="text-muted small mb-0">ราย</p>
                </div>
            </div>
        </div>
    </div>
</div>



       

<div class="row">
    <div class="col-md-10 mx-auto"> 
        <div class="card p-4 shadow-sm border-0" style="border-radius: 20px;">
            <h6 class="fw-bold mb-4 text-center text-secondary">🏢 สถิติจำนวนบุคลากรแยกตามกลุ่มงาน</h6>
            <div style="height: 380px;">
                <canvas id="barChartGroup"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5"> 
    <div class="col-md-10 mx-auto"> 
        <div class="card p-4 shadow-sm border-0" style="border-radius: 20px;">
            <h6 class="fw-bold mb-4 text-center text-secondary">🏢 สัดส่วนประเภทบุคลากร แยกตามกลุ่มงาน</h6>
            <div style="height: 400px;">
                <canvas id="stackedBarChart"></canvas>
            </div>
        </div>
    </div>
</div>
 <?php include('footer.php'); ?>
</div>

<!-- doughnut -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    Chart.register(ChartDataLabels);
    function createPie(elementId, labels, data, colors) {
    const filteredLabels = [];
    const filteredData = [];
    const filteredColors = [];

    data.forEach((value, index) => {
        if (value > 0) { 
            filteredLabels.push(labels[index]);
            filteredData.push(value);
            filteredColors.push(colors[index]);
        }
    });

    new Chart(document.getElementById(elementId), {
        type: 'doughnut',
        data: {
            labels: filteredLabels,
            datasets: [{
                data: filteredData,
                backgroundColor: filteredColors,
                borderWidth: 2, 
                borderColor: '#fff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { 
                        padding: 15, 
                        usePointStyle: true 
                    } 
                },
                datalabels: { display: false },
                // ✅ เพิ่มส่วนนี้เพื่อใส่คำว่า "คน" ใน Tooltip เมื่อเมาส์ชี้
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw; // ค่าตัวเลข
                            return ` ${label}: ${value} คน`; 
                        }
                    }
                }
            }
        }
    });
}
    
    // createPie
    createPie('chartGender', ['ชาย', 'หญิง'], <?php echo json_encode($genderData); ?>, ['#36a2eb', '#ff6384']);
    createPie('chartType', 
    <?php echo json_encode($typeData['labels']); ?>, 
    <?php echo json_encode($typeData['data']); ?>, 
    [
        '#4bc0c0', // เขียวมิ้นท์ (เดิม)
        '#ffcd56', // เหลือง (เดิม)
        '#ff9f40', // ส้ม (เดิม)
        '#9966ff', // ม่วง (เดิม)
        '#ff6384', // ชมพูแดง
        '#36a2eb', // ฟ้าใส
        '#455a64', // เทาเข้ม (Blue Grey)
        '#81c784', // เขียวใบไม้
        '#e57373'  // แดงอ่อน
    ]
);

new Chart(document.getElementById('barChartGroup'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($groupData['labels']); ?>,
        datasets: [{
            label: 'จำนวนเจ้าหน้าที่', // ใส่ชื่อชุดข้อมูลเพื่อให้แสดงใน Tooltip
            data: <?php echo json_encode($groupData['data']); ?>,
            backgroundColor: 'rgba(33, 150, 243, 0.7)',
            borderColor: '#2196f3', borderWidth: 1, borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { 
                beginAtZero: true,
                min: 0,
                max: 100,
                ticks: { stepSize: 10 }
            },
            x: { grid: { display: false } }
        },
        plugins: {
            legend: { display: false },
            datalabels: {
                anchor: 'end',
                align: 'top',
                color: '#1976d2',
                font: { weight: 'bold' },
                formatter: (value) => value > 0 ? value : "" // บนยอดกราฟโชว์แค่ตัวเลขตามเดิม
            },
            // ✅ เพิ่มส่วนนี้เพื่อให้ Tooltip แสดงคำว่า "คน"
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        let value = context.parsed.y;
                        return ` ${label}: ${value} คน`; // แสดง "จำนวนเจ้าหน้าที่: 15 คน"
                    }
                }
            }
        }
    }
});


const ctxStacked = document.getElementById('stackedBarChart').getContext('2d');

new Chart(ctxStacked, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($group_labels); ?>,
        datasets: <?php echo json_encode($datasets_json); ?> 
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { stacked: true, grid: { display: false } },
            y: { 
                stacked: true, 
                beginAtZero: true,
                max: 100,
                ticks: { callback: (value) => value + "%" }
            }
        },
        plugins: {
            legend: { 
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 20,
                    font: { size: 13 },
                    // ✅ กรอง Legend: แสดงเฉพาะหัวข้อที่มีข้อมูลมากกว่า 0 ในระบบ
                    filter: function(legendItem, chartData) {
                        const datasetIndex = legendItem.datasetIndex;
                        const data = chartData.datasets[datasetIndex].data;
                        // ถ้าผลรวมในทุกกลุ่มงานเป็น 0 ไม่ต้องโชว์ชื่อประเภทนี้ใน Legend
                        return data.reduce((a, b) => a + b, 0) > 0;
                    }
                }
            },
            datalabels: {
                color: '#fff',
                font: { weight: 'bold', size: 11 },
                // ✅ แสดงตัวเลข % บนกราฟเฉพาะช่องที่มากกว่า 0
                formatter: (value) => value > 0 ? value + "%" : ""
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.dataset.label || '';
                        const percent = context.parsed.y;
                        // ป้องกัน Error กรณีไม่มี realCount ส่งมา
                        const count = context.dataset.realCount ? context.dataset.realCount[context.dataIndex] : 0;
                        
                        // ถ้าค่านั้นเป็น 0 ไม่ต้องแสดง Tooltip
                        if (percent === 0) return null;
                        
                        return ` ${label}: ${count} คน (${percent}%)`;
                    }
                }
            }
        }
    }
});
</script>