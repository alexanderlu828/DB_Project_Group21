<?php
$host = 'localhost';
$port = 5432;
$dbname = 'postgres';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $locationId = $_GET['locationId'];

    $sql = "SELECT location_id, location_name, location_address, slot_capacity, service_start_time, service_end_time FROM vaccination_location WHERE location_id = :locationId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':locationId', $locationId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Location not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
