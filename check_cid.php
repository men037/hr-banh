<?php
include('config.php');

$cid = isset($_GET['cid']) ? mysqli_real_escape_string($conn, $_GET['cid']) : '';
$response = ['exists' => false, 'name' => ''];

if ($cid != '') {
    $sql = "SELECT fname, lname FROM staff_main WHERE cid = '$cid' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $response['exists'] = true;
        $response['name'] = $row['fname'] . " " . $row['lname'];
    }
}

echo json_encode($response);
?>