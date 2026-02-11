<style>
    :root {
        --sidebar-bg: #ffffff;
        --sidebar-text: #4a4a4a;
        --sidebar-active: #ff85a2;
        --sidebar-hover-bg: #fff0f3;
        --body-bg: #f8f9fa;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 260px;
        height: 100vh;
        background-color: var(--sidebar-bg);
        color: var(--sidebar-text);
        position: fixed;
        left: 0; top: 0;
        padding: 25px 15px;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #eee;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--sidebar-text);
        margin-bottom: 40px;
        padding-left: 10px;
    }

    .brand-icon {
        background: var(--sidebar-active);
        color: white;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 4px 10px rgba(255, 133, 162, 0.2);
    }

    .brand-text b { display: block; font-size: 1.1rem; line-height: 1; color: #333; }
    .brand-text small { font-size: 0.7rem; opacity: 0.6; text-transform: uppercase; }

    .menu-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #bbb;
        margin: 20px 0 10px 15px;
        letter-spacing: 1px;
        font-weight: 700;
    }

    .sidebar .nav-link {
        color: var(--sidebar-text) !important;
        padding: 12px 18px;
        border-radius: 15px;
        margin-bottom: 5px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 400;
        text-decoration: none;
    }

    .sidebar .nav-link i { width: 20px; font-size: 1.1rem; color: #888; transition: all 0.3s; }

    /* Hover Effect */
    .sidebar .nav-link:hover {
        background-color: var(--sidebar-hover-bg);
        color: var(--sidebar-active) !important;
    }
    .sidebar .nav-link:hover i { color: var(--sidebar-active); }

    /* Active Effect */
    .sidebar .nav-link.active {
        background-color: var(--sidebar-active);
        color: white !important;
        box-shadow: 0 4px 15px rgba(255, 133, 162, 0.3);
        font-weight: 500;
    }
    .sidebar .nav-link.active i { color: white; }

    .sidebar-footer {
        margin-top: auto;
        background: #fcfcfc;
        padding: 15px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #f0f0f0;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: #f0f0f0;
        color: #888;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<?php
// ฟังก์ชันสำหรับเช็คหน้าปัจจุบันเพื่อใส่คลาส active
function isActive($filenames) {
    $current_file = basename($_SERVER['PHP_SELF']);
    if (is_array($filenames)) {
        return in_array($current_file, $filenames) ? 'active' : '';
    }
    return ($current_file == $filenames) ? 'active' : '';
}
?>

<div class="sidebar">
    <a href="index.php" class="sidebar-brand text-decoration-none">
    <div  style="background: none !important; padding: 0;">
    <!-- <div class="brand-icon" style="background: none !important; padding: 0;"> -->
    <img src="img/logo.png" alt="Logo" style="width: 50px; height: auto;"> 
    </div>
    
        <div class="brand-text">
            <b>Staff System</b>
            <small>โรงพยาบาลบ้านนา</small>
        </div>
    </a>
    
    <div class="menu-label">เมนูระบบ</div>
    <nav class="nav flex-column">
        <a href="index.php" class="nav-link <?php echo isActive('index.php'); ?>">
            <i class="fa-solid fa-chart-pie"></i> <span>ภาพรวมระบบ</span>
        </a>
    </nav>

    <div class="menu-label">ข้อมูลบุคลากร</div>
    <nav class="nav flex-column">
         <a href="add_staff.php" class="nav-link <?php echo isActive('add_staff.php'); ?>">
            <i class="fa-solid fa-user-plus"></i> <span>เพิ่มข้อมูลใหม่</span>
        </a>
        <a href="staff_list.php" class="nav-link <?php echo isActive(['staff_list.php', 'edit.php']); ?>">
            <i class="fa-solid fa-address-book"></i> <span>รายชื่อเจ้าหน้าที่</span>
        </a>
        <a href="staff_list2.php" class="nav-link <?php echo isActive('staff_list2.php'); ?>">
            <i class="fa-solid fa-file-invoice"></i> <span>รายชื่อเจ้าหน้าที่ (จ)</span>
        </a>
       
    </nav>

    <div class="menu-label">Provider</div>
    <nav class="nav flex-column">
        <a href="moph_api_sync.php" class="nav-link <?php echo isActive('moph_api_sync.php'); ?>">
           <i class="fa-solid fa-cloud-arrow-up"></i> <span> MOPH API Sync</span>
        </a>
        <a href="view_moph_retrieve.php" class="nav-link <?php echo isActive('view_moph_retrieve.php'); ?>">
        <i class="fa-solid fa-database"></i> <span> MOPH (Retrieve)</span>
        </a>

    </nav>
    <div class="menu-label">ตั้งค่า</div>
    <nav class="nav flex-column">
        <a href="user_manage.php" class="nav-link <?php echo isActive('user_manage.php'); ?>">
           <i class="fa-solid fa-users-gear"></i> <span>จัดการผู้ใช้งาน</span>
        </a>
         <a href="view_logs.php" class="nav-link <?php echo isActive('view_logs.php'); ?>">
            <i class="fa-solid fa-clock-rotate-left"></i> <span>ประวัติการใช้งาน</span>
        </a>
    </nav>



    <div class="sidebar-footer mt-auto">
        <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
        <div class="flex-grow-1 overflow-hidden" style="font-size: 0.85rem;">
            <!-- <span class="d-block text-truncate fw-bold" style="color:#333;">ผู้ใช้งาน</span> -->
            <a href="logout.php" class="text-muted text-decoration-none" style="font-size: 1.00 rem;">ออกจากระบบ</a>
        </div>

        <!-- <div class="sidebar-footer mt-auto">
    <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
    <div class="flex-grow-1 overflow-hidden" style="font-size: 0.85rem;">
        <span class="d-block text-truncate fw-bold" style="color:#333;">ผู้ใช้งาน</span>
        <a href="logout.php" class="text-muted text-decoration-none" style="font-size: 0.75rem;">ออกจากระบบ</a>
    </div>
</div> -->

        <a href="logout.php" class="text-secondary" onclick="return confirm('ออกจากระบบ?')">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</div>