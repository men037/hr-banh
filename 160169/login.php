<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - STAFF SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            background: linear-gradient(135deg, #fff5f7 0%, #ffe4e8 100%);
            height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .login-card {
            border: none; border-radius: 20px; width: 100%; max-width: 400px;
            box-shadow: 0 10px 25px rgba(255, 133, 162, 0.2);
        }
        .btn-login {
            background-color: #ff85a2; border: none; border-radius: 10px;
            padding: 12px; font-weight: bold; color: white; transition: 0.3s;
        }
        .btn-login:hover { background-color: #f06292; transform: translateY(-2px); }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #ffdae3; }
        .form-control:focus { border-color: #ff85a2; box-shadow: 0 0 0 0.25 red rgba(255, 133, 162, 0.25); }
    </style>
</head>
<body>

<div class="card login-card p-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold" style="color: #ff85a2;">🏥 STAFF LOGIN</h2>
        <p class="text-muted">ระบบบริหารจัดการบุคลากร รพ.บ้านนา</p>
    </div>
    <form action="check_login.php" method="POST">
        <div class="mb-3">
            <label class="form-label">ชื่อผู้ใช้งาน (Username)</label>
            <input type="text" name="username" class="form-control" placeholder="ระบุชื่อผู้ใช้งาน" required>
        </div>
        <div class="mb-4">
            <label class="form-label">รหัสผ่าน (Password)</label>
            <input type="password" name="password" class="form-control" placeholder="ระบุรหัสผ่าน" required>
        </div>
        <button type="submit" class="btn btn-login w-100 mb-3 shadow-sm">เข้าสู่ระบบ</button>
        <div class="text-center">
            <small class="text-muted">พบปัญหาการใช้งาน ติดต่อศูนย์คอมพิวเตอร์</small>
        </div>
    </form>
</div>

</body>
</html>