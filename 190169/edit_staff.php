// ดึงข้อมูลเดิมมาแสดง
$id = $_GET['id'];
$res = mysqli_query($conn, "SELECT * FROM staff_main WHERE cid = '$id'");
$data = mysqli_fetch_assoc($res);
// จากนั้นเอา $data['fname'] ไปใส่ใน value ของ input ต่างๆ