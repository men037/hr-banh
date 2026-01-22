<?php
include('auth.php'); 
include('config.php'); 
checkSuperAdmin();
//session_start();
// ตรวจสอบเบื้องต้น: เฉพาะ Admin หรือ Super Admin เท่านั้นที่เข้าหน้านี้ได้
//if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin')) {
//    echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้'); window.location.href='index.php';</script>";
//    exit();
//}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้งาน - STAFF SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(255, 133, 162, 0.1); }
        .table-pink thead { background-color: #ffdae3; }
        .text-pink { color: #ff85a2 !important; }
        .badge { font-weight: normal; padding: 6px 12px; border-radius: 8px; }
    </style>
</head>
<body>

<!-- <nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | USER MANAGEMENT</a>
        <div class="ms-auto d-flex gap-2">
            <a href="index.php" class="btn btn-light btn-sm fw-bold text-pink shadow-sm px-3">🏠 กลับหน้าหลัก</a>
        </div>
    </div>
</nav> -->

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | STAFF SYSTEM</a>
    <div class="ms-auto d-flex gap-2">
        <a href="index.php" class="btn btn-light btn-sm fw-bold text-dark">🏠 กลับหน้า Dashboard</a>
        <a href="view_logs.php" class="btn btn-light btn-sm fw-bold text-dark">🚀 ประวัติการใช้งาน</a>
        <a href="logout.php" class="btn btn-danger btn-sm fw-bold shadow-sm px-3" onclick="return confirm('ออกจากระบบ?')"> 🚪 ออกจากระบบ </a>
        </div>
    </nav>

<div class="container">
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
                                
                                // ตั้งค่า Badge ตาม Role
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

</body>
</html>