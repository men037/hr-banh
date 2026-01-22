<!-- <footer class="mt-5 pb-4">
    <div class="container text-center">
        <hr style="border-top: 1px solid var(--purple-border); opacity: 0.5;">
        <p class="text-muted mb-0" style="font-size: 0.8rem;">
            <i class="fas fa-hospital-alt me-1"></i> ระบบฐานข้อมูลบุคลากร โรงพยาบาลบ้านนา
        </p>
        <p class="text-muted" style="font-size: 0.75rem;">
            Copyright &copy; 2026 งานเทคโนโลยีสารสนเทศ @TM V.260126 | ข้อมูล ณ วันที่ <?php echo date('d/m/Y H:i'); ?>
        </p>
    </div>
</footer> -->

<style>
    /* 1. ตั้งค่าพื้นฐาน */
    html, body {
        height: 100%;
        margin: 0;
    }
    
    body {
        display: flex;
        flex-direction: column;
    }

    /* 2. ส่วนที่ต้องการให้ยืด (ปกติคือตัวคลุมเนื้อหาหลัก) */
    /* ถ้าในหน้าหลักคุณมี <div class="container"> หรือ <div class="wrapper"> */
    .content-wrapper {
        flex: 1 0 auto;
    }

    /* 3. ส่วน Footer */
    footer {
        flex-shrink: 0;
        width: 100%;
    }
</style>

<footer class="mt-auto pb-4"> <div class="container text-center">
        <hr style="border-top: 1px solid var(--purple-border); opacity: 0.5;">
        <p class="text-muted mb-0" style="font-size: 0.8rem;">
            <i class="fas fa-hospital-alt me-1"></i> ระบบฐานข้อมูลบุคลากร โรงพยาบาลบ้านนา
        </p>
        <p class="text-muted" style="font-size: 0.75rem;">
            Copyright &copy; 2026 งานเทคโนโลยีสารสนเทศ @TM V.260126 | ข้อมูล ณ วันที่ <?php echo date('d/m/Y H:i'); ?>
        </p>
    </div>
</footer>