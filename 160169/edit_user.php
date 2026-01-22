<?php
include('auth.php'); 
checkAdmin(); // ล็อคให้เฉพาะ Admin ขึ้นไปเข้าได้
include('config.php'); 

// session_start();
// // ตรวจสอบสิทธิ์: เฉพาะ Admin หรือ Super Admin เท่านั้น
// if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin')) {
//     header("Location: index.php");
//     exit();
// }

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT * FROM sys_users WHERE id = '$id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้งาน";
    exit();
}

// จัดการการบันทึกข้อมูล
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        // ถ้ามีการกรอกรหัสผ่านใหม่ ให้ทำการ Hash ก่อนบันทึก
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE sys_users SET full_name = ?, role = ?, password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "sssi", $full_name, $role, $hashed_password, $id);
    } else {
        // ถ้าไม่กรอกรหัสผ่าน ให้แก้ไขแค่ชื่อและสิทธิ์
        $update_sql = "UPDATE sys_users SET full_name = ?, role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "ssi", $full_name, $role, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ'); window.location.href='user_manage.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขผู้ใช้งาน - STAFF SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #fff5f7; }
        .navbar { background: linear-gradient(90deg, #ff85a2 0%, #ffb3c1 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(255, 133, 162, 0.1); }
        .text-pink { color: #ff85a2 !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🏥 รพ.บ้านนา | EDIT USER</a>
        <a href="user_manage.php" class="btn btn-light btn-sm fw-bold text-pink shadow-sm px-3">🔙 ย้อนกลับ</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h5 class="fw-bold mb-4 text-pink text-center">⚙️ แก้ไขข้อมูลผู้ใช้งาน</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username (ไม่สามารถแก้ไขได้)</label>
                        <input type="text" class="form-control bg-light" value="<?php echo $user['username']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ชื่อ-นามสกุลจริง</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo $user['full_name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ระดับสิทธิ์ (Role)</label>
                        <select name="role" class="form-select" <?php echo ($_SESSION['role'] !== 'super_admin') ? 'disabled' : ''; ?>>
                            <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User (ทั่วไป)</option>
                            <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin (ผู้ดูแลระบบ)</option>
                            <option value="super_admin" <?php echo ($user['role'] == 'super_admin') ? 'selected' : ''; ?>>Super Admin (สิทธิ์สูงสุด)</option>
                        </select>
                        <?php if($_SESSION['role'] !== 'super_admin'): ?>
                            <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    <div class="mb-3">
                        <label class="form-label text-danger">เปลี่ยนรหัสผ่านใหม่ (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="ระบุรหัสผ่านใหม่ที่นี่">
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-bold shadow-sm">💾 บันทึกการแก้ไข</button>
                        <a href="user_manage.php" class="btn btn-outline-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>