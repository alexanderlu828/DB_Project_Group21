<?php
$host = 'localhost';
$port = 5432;
$dbname = 'Vaccine_system';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_GET['userId'] ?? '';

    if (empty($userId)) {
        echo json_encode(['error' => 'Invalid user ID']);
        exit;
    }

    $sql = "SELECT vr.vaccination_date, vr.vaccination_time, vl.location_name, vi.vaccine_name 
            FROM appointment a 
            JOIN vaccination_record vr ON a.appointment_id = vr.vaccination_id 
            JOIN vaccination_location vl ON a.location_id = vl.location_id 
            JOIN vaccine_info vi ON a.vaccine_id = vi.vaccine_id 
            WHERE a.user_id = :userId";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
