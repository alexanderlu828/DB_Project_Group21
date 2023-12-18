<?php
// get_vaccines.php

header('Content-Type: application/json'); // 确保输出为 JSON 格式

$host = 'localhost';
$port = 5432; // remember to replace your own connection port
$dbname = 'Vaccine_system'; // remember to replace your own database name
$user = 'postgres'; // remember to replace your own username 
$password = trim(file_get_contents('db_password.txt')); // remember to replace your own password 

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $selectedHospital = $_GET['hospital'] ?? '';

    if (!$selectedHospital) {
        throw new Exception("Hospital not specified");
    }

    $vaccines = [];
    $sql = "SELECT v.vaccine_name 
            FROM vaccine_inventory vi
            JOIN vaccine_info v ON vi.vaccine_id = v.vaccine_id
            WHERE vi.location_id = 
                (SELECT location_id FROM vaccination_location WHERE location_name = :hospital)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':hospital', $selectedHospital, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($vaccines, $row['vaccine_name']);
    }

    echo json_encode($vaccines);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
