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
    <title>จัดการผู้ใช้งาน - รพ.บ้านนา</title>
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
        .text-pink { color: #ff85a2 !important; }
        
        /* Toolbar สไตล์เดียวกับหน้าอื่น */
        .custom-toolbar { display: flex; align-items: flex-end; gap: 10px; flex-wrap: nowrap; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .show-item { width: auto; min-width: 80px; }
        .search-item { flex: 1; min-width: 150px; }
        .form-select-sm, .form-control-sm { border-radius: 8px !important; border: 1px solid #ffdae3 !important; }
        
        .dataTables_wrapper .row:first-child { display: none; }

        .wrapper { display: flex; width: 100%; }

        #content {
            flex: 1;
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            background-color: #fff5f7;
            margin-left: 250px; 
            transition: all 0.3s;
        }

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
                        <i class="fa-solid fa-users-gear me-2"></i>จัดการผู้ใช้งาน (User Management)
                    </a>
                </div>
            </nav>

            <div class="row g-4">
                <div class="col-xl-4 col-lg-5">
                    <div class="card p-4 shadow-sm">
                        <h5 class="fw-bold mb-3 text-pink">
                            <i class="fa-solid fa-user-plus me-2"></i>เพิ่มบัญชีผู้ใช้ใหม่
                        </h5>
                        <form action="save_user.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">ชื่อ-นามสกุลจริง</label>
                                <input type="text" name="full_name" class="form-control" placeholder="ระบุชื่อเจ้าหน้าที่" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-at text-muted"></i></span>
                                    <input type="text" name="username" class="form-control border-start-0" placeholder="สำหรับใช้ Login" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="ระบุรหัสผ่าน" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">ระดับสิทธิ์ (Role)</label>
                                <select name="role" class="form-select" required>
                                    <option value="user">User (ทั่วไป)</option>
                                    <option value="admin">Admin (ผู้ดูแลระบบ)</option>
                                    <?php if($_SESSION['role'] == 'super_admin'): ?>
                                        <option value="super_admin">Super Admin (สิทธิ์สูงสุด)</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-pink w-100 fw-bold shadow-sm py-2 text-white" style="background-color: #ff85a2; border: none;">
                                <i class="fa-solid fa-save me-2"></i>บันทึกข้อมูล
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <div class="card p-4 shadow-sm">
                        <div class="custom-toolbar">
                            <div class="show-item">
                                <label class="small text-muted mb-1">แสดง</label>
                                <select id="customLength" class="form-select form-select-sm">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            <div class="search-item text-end">
                                <label class="small text-muted mb-1">ค้นหา</label>
                                <input type="text" id="customSearch" class="form-control form-control-sm w-25 ms-auto" placeholder="พิมพ์เพื่อค้นหา...">
                            </div>
                            <button onclick="location.reload()" class="btn btn-outline-secondary btn-sm" title="รีเฟรช">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table id="userTable" class="table table-hover align-middle w-100">
                                <thead class="table-pink">
                                    <tr>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>Username</th>
                                        <th>สิทธิ์</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM sys_users ORDER BY id DESC";
                                    $res = mysqli_query($conn, $sql);
                                    while($u = mysqli_fetch_assoc($res)) {
                                        if ($u['role'] == 'super_admin') {
                                            $role_badge = 'bg-dark'; $role_text = 'Super Admin';
                                        } elseif ($u['role'] == 'admin') {
                                            $role_badge = 'bg-danger'; $role_text = 'Admin';
                                        } else {
                                            $role_badge = 'bg-primary'; $role_text = 'User';
                                        }
                                    ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?php echo $u['full_name']; ?></td>
                                            <td><code class="text-pink"><?php echo $u['username']; ?></code></td>
                                            <td><span class="badge <?php echo $role_badge; ?>"><?php echo $role_text; ?></span></td>
                                            <td class="text-center">
                                                <div class="btn-group gap-1">
                                                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                                        <i class="fa-solid fa-key me-1"></i>รหัส
                                                    </a>
                                                    <?php if($_SESSION['role'] == 'super_admin'): ?>
                                                        <a href="delete_user.php?id=<?php echo $u['id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                                           onclick="return confirm('ยืนยันการลบบัญชีผู้ใช้ ของคุณ <?php echo $u['full_name']; ?>?')">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
    var table = $('#userTable').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json" },
        "dom": 'rtip',
        "pageLength": 10,
        "order": [[0, "asc"]] 
    });

    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#customLength').on('change', function() {
        table.page.len(this.value).draw();
    });
});
</script>

</body>
</html>