<?php

$host = 'localhost';
$port = 5432;
$dbname = 'postgres';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password); 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$location_id = $_POST['locationId'];

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("DELETE FROM vaccination_location WHERE location_id = :location_id");
    $stmt->execute(['location_id' => $location_id]);
    
    $pdo->commit();
    echo "接種地點已刪除！";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "發生錯誤： " . $e->getMessage();
}
?>
