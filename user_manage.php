<?php
include('auth.php'); 
include('config.php'); 
checkSuperAdmin();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้งาน - STAFF SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; margin: 0; padding: 0; }
        
        /* โครงสร้าง Flex สำหรับ Sidebar */
        .wrapper { display: flex; width: 100%; min-height: 100vh; }
        
        .main-content { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            background-color: #fff5f7; 
            min-width: 0;
        }

        .content-body { flex: 1; padding: 20px; }

        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(255, 133, 162, 0.1); }
        .table-pink thead { background-color: #ffdae3; }
        .text-pink { color: #ff85a2 !important; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }
    </style>
</head>
<body>

<div class="wrapper">
    <aside style="width: 260px; min-width: 260px; background: #fff;">
        <?php include('sidebar.php'); ?>
    </aside>

    <div class="main-content">
        
        <nav class="navbar navbar-dark mb-4 shadow-sm">
            <div class="container-fluid px-4">
                <a class="navbar-brand fw-bold" href="index.php">จัดการผู้ใช้งาน</a>
          
            </div>
        </nav>

        <div class="content-body">
            <div class="container-fluid">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card p-4">
                            <h5 class="fw-bold mb-3 text-pink">➕ เพิ่มบัญชีผู้ใช้ใหม่</h5>
                            <form action="save_user.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small">ชื่อ-นามสกุลจริง</label>
                                    <input type="text" name="full_name" class="form-control" placeholder="ระบุชื่อเจ้าหน้าที่" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="สำหรับใช้ Login" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="ระบุรหัสผ่าน" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">ระดับสิทธิ์ (Role)</label>
                                    <select name="role" class="form-select" required>
                                        <option value="user">User (ทั่วไป)</option>
                                        <option value="admin">Admin (ผู้ดูแลระบบ)</option>
                                        <?php if($_SESSION['role'] == 'super_admin'): ?>
                                            <option value="super_admin">Super Admin (สิทธิ์สูงสุด)</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">บันทึกข้อมูล</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card p-4">
                            <h5 class="fw-bold mb-3 text-secondary">👥 รายชื่อผู้ใช้งานในระบบ</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-pink text-pink">
                                        <tr>
                                            <th>ชื่อ-นามสกุล</th>
                                            <th>Username</th>
                                            <th>สิทธิ์การใช้งาน</th>
                                            <th class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM sys_users ORDER BY id DESC";
                                        $res = mysqli_query($conn, $sql);
                                        while($u = mysqli_fetch_assoc($res)) {
                                            
                                            if ($u['role'] == 'super_admin') {
                                                $role_badge = 'bg-dark';
                                                $role_text = 'Super Admin';
                                            } elseif ($u['role'] == 'admin') {
                                                $role_badge = 'bg-danger';
                                                $role_text = 'Admin';
                                            } else {
                                                $role_badge = 'bg-primary';
                                                $role_text = 'User';
                                            }
                                        ?>
                                        <tr>
                                            <td><?php echo $u['full_name']; ?></td>
                                            <td><code><?php echo $u['username']; ?></code></td>
                                            <td><span class="badge <?php echo $role_badge; ?>"><?php echo $role_text; ?></span></td>
                                            <td class="text-center">
                                                <div class="btn-group gap-1">
                                                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-warning">🔑 รหัส</a>
                                                    
                                                    <?php if($_SESSION['role'] == 'super_admin'): ?>
                                                        <a href="delete_user.php?id=<?php echo $u['id']; ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('ยืนยันการลบบัญชีผู้ใช้ ของคุณ <?php echo $u['full_name']; ?>?')">
                                                           🗑️ ลบ
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
        </div>

        <div class="text-center pb-4">
            <?php include('footer.php'); ?>
        </div>
    </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>