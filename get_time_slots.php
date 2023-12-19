<?php
// get_time_slots.php

$host = 'localhost';
$port = 5432; // 您的連接端口
$dbname = 'Vaccine_system'; // 您的數據庫名稱
$user = 'postgres'; // 您的用戶名
$password = trim(file_get_contents('db_password.txt')); // 您的密碼 

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $selectedHospital = $_GET['hospital'] ?? '';

    $sql = "SELECT service_start_time, service_end_time FROM vaccination_location WHERE location_name = :hospital";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':hospital', $selectedHospital);
    $stmt->execute();

    $timeSlots = [];
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $startTime = new DateTime($row['service_start_time']);
        $endTime = new DateTime($row['service_end_time']);

        while ($startTime <= $endTime) {
            array_push($timeSlots, $startTime->format('H:i'));
            $startTime->modify('+30 minutes');
        }
    }

    echo json_encode($timeSlots);
} catch (PDOException $e) {
    // 錯誤處理
    echo json_encode(['error' => $e->getMessage()]);
}
?>
