<?php
// get_vaccine_inventory.php

$host = 'localhost';
$port = 5432; 
$dbname = 'Vaccine_system';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));
 
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $selectedHospital = $_GET['hospital'] ?? '';

    // 查詢 location_id
    $stmt = $pdo->prepare("SELECT location_id FROM vaccination_location WHERE location_name = :hospital");
    $stmt->bindParam(':hospital', $selectedHospital);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locationId = $row['location_id'];

        // 查詢疫苗存量
        $query = "SELECT vi.vaccine_id, vi.current_inventory, vinfo.vaccine_name 
                  FROM vaccine_inventory vi 
                  JOIN vaccine_info vinfo ON vi.vaccine_id = vinfo.vaccine_id
                  WHERE vi.location_id = :locationId";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':locationId', $locationId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
