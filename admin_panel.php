<?php include('config.php'); ?>
<div class="container mt-4">
    <h3 class="text-pink">🛠 ส่วนผู้ดูแลระบบ</h3>
    <hr>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>⚙️ ตั้งค่าการเชื่อมต่อฐานข้อมูล</h5>
                    <p>สถานะปัจจุบัน: <?php echo $db_status; ?></p>
                    <form>
                        <input type="text" class="form-control mb-2" value="<?php echo $host; ?>" readonly>
                        <input type="text" class="form-control mb-2" value="<?php echo $dbname; ?>" readonly>
                        <button type="button" class="btn btn-sm btn-outline-secondary">ทดสอบการเชื่อมต่อใหม่</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>👥 จัดการผู้ใช้งานระบบ</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Username</th><th>ระดับ</th><th>จัดการ</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>admin_banna</td>
                                <td>Admin</td>
                                <td><button class="btn btn-sm btn-danger">ลบ</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-sm btn-pink">+ เพิ่มผู้ใช้งาน</button>
                </div>
            </div>
        </div>
    </div>
</div>