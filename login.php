<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - STAFF SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            background: linear-gradient(135deg, #fff5f7 0%, #ffe4e8 100%);
            height: 100vh; display: flex; align-items: center; justify-content: center;
            margin: 0;
        }
        .login-card {
            border: none;
            border-radius: 25px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(255, 133, 162, 0.2);
            background: white;

            display: flex;
            flex-direction: column;
        }
        .btn-login {
            background-color: #ff85a2; border: none; border-radius: 10px;
            padding: 12px; font-weight: bold; color: white; transition: 0.3s;
        }
        .btn-login:hover { background-color: #f06292; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(240, 98, 146, 0.3); }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #ffdae3; }
        .form-control:focus { 
            border-color: #ff85a2; 
            box-shadow: 0 0 0 0.25rem rgba(255, 133, 162, 0.25); /* แก้ไขจาก 0.25 red เป็นค่าสีที่ถูกต้อง */
            outline: none;
        }
        .alert-timeout {
            background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba;
            border-radius: 12px; font-size: 0.85rem;
        }
        .text-footer {
    color:rgb(192, 190, 190);
    font-size: 0.7rem; 
}
        
    </style>
</head>
<body>

<div class="container d-flex flex-column align-items-center">
    
    <?php if (isset($_GET['error']) && $_GET['error'] == 'timeout'): ?>
        <div class="alert alert-timeout text-center mb-3 shadow-sm p-3 w-100" style="max-width: 400px;">
            <i class="fas fa-clock-rotate-left me-2"></i> 
            <strong>เซสชันหมดอายุ:</strong> เนื่องจากไม่มีการใช้งานนานเกินไป กรุณาเข้าสู่ระบบใหม่อีกครั้ง
        </div>
    <?php endif; ?>

    <div class="card login-card p-4 shadow-lg">
        <div class="text-center mb-2">
            <img src="img/logo.png" alt="Logo รพ.บ้านนา" class="mb-0" style="width: 120px; height: auto;">
            <h2 class="fw-bold" style="color: #ff85a2; margin-top: -10px;">STAFF LOGIN</h2>
            <p class="text-muted small">ระบบบริหารจัดการบุคลากร โรงพยาบาลบ้านนา</p>
        </div>

        <form action="check_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">ชื่อผู้ใช้งาน (Username)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #ffdae3;">
                        <i class="fas fa-user text-muted"></i>
                    </span>
                    <input type="text" name="username" class="form-control border-start-0" placeholder="Username" required style="border-radius: 0 10px 10px 0;">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">รหัสผ่าน (Password)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border-color: #ffdae3;">
                        <i class="fas fa-lock text-muted"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="Password" required style="border-radius: 0 10px 10px 0;">
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3 shadow-sm">เข้าสู่ระบบ</button>
            
            <div class="text-center">
                <hr class="text-pink opacity-25">
                <small class="text-muted">
                    <i class="fas fa-headset me-1"></i> พบปัญหาการใช้งาน ติดต่องานเทคโนโลยีสารสนเทศ 
                    
                </small>
            </div>
        </form>
        <form action="check_login.php" method="POST" class="flex-grow-1">
</form>

<div class="text-center mt-auto pt-2 border-top">
    <p class="small text-footer mb-1">
        © 2026 โรงพยาบาลบ้านนา สงวนลิขสิทธิ์
    </p>
</div>
    </div>
</div>

</body>
</html>