<?php
// process_vaccine_reservation.php

$host = 'localhost';
$port = 5432;
$dbname = 'Vaccine_system';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt')); 

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 從 POST 請求中獲取數據
    $userId = $_POST['userId'];
    $name = $_POST['name'];
    $healthCardNumber = $_POST['healthCardNumber'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $locationName = $_POST['locationName'];
    $vaccinationDate = $_POST['vaccinationDate'];
    $vaccinationTime = $_POST['vaccinationTime'];
    $vaccineName = $_POST['vaccineName'];

    // 檢查 Users 表中是否存在用戶
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        // 用戶不存在，插入新用戶
        $insertUser = $pdo->prepare("INSERT INTO Users (user_id, health_card_number, name, gender, phone, bIrthday, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertUser->execute([$userId, $healthCardNumber, $name, $gender, $phone, $birthday, $address]);
    }

     // 获取 vaccine_id 和 location_id
    $stmtVaccine = $pdo->prepare("SELECT vaccine_id FROM vaccine_info WHERE vaccine_name = :vaccineName");
    $stmtVaccine->execute([':vaccineName' => $vaccineName]);
    $vaccineId = $stmtVaccine->fetchColumn();

    $stmtLocation = $pdo->prepare("SELECT location_id FROM vaccination_location WHERE location_name = :locationName");
    $stmtLocation->execute([':locationName' => $locationName]);
    $locationId = $stmtLocation->fetchColumn();

    // 插入 Appointment 表
    $appointmentId = generateUniqueAppointmentId();
    $insertAppointment = $pdo->prepare("INSERT INTO Appointment (appointment_id, user_id, vaccine_id, appointment_date, appointment_time, location_id) VALUES (?, ?, ?, ?, ?, ?)");
    // 需要實現邏輯以獲取 vaccine_id 和 location_id
    $insertAppointment->execute([$appointmentId, $userId, $vaccineId, $vaccinationDate, $vaccinationTime, $locationId]);

    // 插入 vaccination_record 表
    $insertVaccinationRecord = $pdo->prepare("INSERT INTO vaccination_record (vaccination_id, vaccination_date, vaccination_time, status) VALUES (?, ?, ?, 'No')");
    $insertVaccinationRecord->execute([$appointmentId, $vaccinationDate, $vaccinationTime]);

    $pdo->commit(); // 提交事务

    echo json_encode(["success" => true, "appointmentId" => $appointmentId]);
    } catch (PDOException $e) {
        $pdo->rollBack(); // 发生错误时回滚事务
        echo json_encode(["error" => $e->getMessage()]);
    }

// 函數: 生成唯一的預約ID
function generateUniqueAppointmentId() {
    return uniqid('', true);
}
?>
