<?php
$host = 'localhost';
$port = 5432;
$dbname = 'postgres';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt')); 

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $locationId = $_POST['locationId'];
    $locationName = $_POST['locationName'];
    $locationAddress = $_POST['locationAddress'];
    $slotCapacity = $_POST['slotCapacity'];
    $serviceStartTime = $_POST['serviceStartTime'];
    $serviceEndTime = $_POST['serviceEndTime'];

    try {
        $stmt = $pdo->prepare("UPDATE vaccination_location SET
            location_name = ?,
            location_address = ?,
            slot_capacity = ?,
            service_start_time = ?,
            service_end_time = ?
            WHERE location_id = ?");

        $stmt->execute([
            $locationName,
            $locationAddress,
            $slotCapacity,
            $serviceStartTime,
            $serviceEndTime,
            $locationId
        ]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => '接種地點資訊已更新！']);
        } else {
            echo json_encode(['message' => '沒有進行更新，請檢查地點編號']);
        }
    } catch (PDOException $e) {
        echo json_encode(['message' => '更新失敗：' . $e->getMessage()]);
    }
} else {
    echo json_encode(['message' => '無效的請求方法']);
}
?>
