<?php
$host = 'localhost';
$port = 5432;
$dbname = 'postgres';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csvFile'])) {
        $csvFile = $_FILES['csvFile']['tmp_name'];
        
        if (is_uploaded_file($csvFile)) {
            try {
                $pdo->beginTransaction();
                
                $fileHandle = fopen($csvFile, 'r');
                
                // Skip the first line if CSV file includes header
                fgetcsv($fileHandle);
                
                while (($data = fgetcsv($fileHandle, 1000, ",")) !== FALSE) {
                    // Assuming the CSV columns match the table columns
                    $stmt = $pdo->prepare("INSERT INTO vaccination_location (location_id, location_name, location_address, slot_capacity, service_start_time, service_end_time) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute($data);
                }
                
                fclose($fileHandle);
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'CSV file has been imported successfully.']);
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload failed.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
