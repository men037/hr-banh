<?php include('config.php'); ?>
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white"><strong>➕ เพิ่มข้อมูลกลุ่มงาน / ตำแหน่ง</strong></div>
        <div class="card-body">
            <form action="admin_actions.php" method="POST" class="row g-3">
                <div class="col-md-3">
                    <select name="type" class="form-select" required>
                        <option value="group">กลุ่มงาน</option>
                        <option value="position">ตำแหน่ง</option>
                        <option value="dept">หน่วยงาน</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="id" class="form-control" placeholder="รหัส (ID)" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="name" class="form-control" placeholder="ชื่อที่ต้องการเพิ่ม" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>