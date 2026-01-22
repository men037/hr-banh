<div class="card p-3 shadow-sm">
    <h5>เพิ่มกลุ่มงานใหม่</h5>
    <form action="save_group.php" method="POST" class="row g-3">
        <div class="col-auto">
            <input type="text" name="group_id" class="form-control" placeholder="รหัสกลุ่มงาน">
        </div>
        <div class="col-auto">
            <input type="text" name="group_name" class="form-control" placeholder="ชื่อกลุ่มงาน">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-pink">บันทึก</button>
        </div>
    </form>
</div>