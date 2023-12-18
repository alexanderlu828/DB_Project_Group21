<?php


$host = 'localhost';
$port = 5432;
$dbname = 'postgres';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

$pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$location_id = $_POST['locationId'];
$vaccine_id = $_POST['vaccineId'];
$quantity_change = (int)$_POST['quantityChange'];

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("SELECT current_inventory FROM vaccine_inventory WHERE location_id = :location_id AND vaccine_id = :vaccine_id FOR UPDATE");
    $stmt->execute(['location_id' => $location_id, 'vaccine_id' => $vaccine_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        // 更新存量
        $new_inventory = $row['current_inventory'] + $quantity_change;
        $update_stmt = $pdo->prepare("UPDATE vaccine_inventory SET current_inventory = :new_inventory WHERE location_id = :location_id AND vaccine_id = :vaccine_id");
        $update_stmt->execute(['new_inventory' => $new_inventory, 'location_id' => $location_id, 'vaccine_id' => $vaccine_id]);
    } else {
        echo "No matching record found";
        return;
    }
    
    $pdo->commit();
    echo "更新成功！";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "發生錯誤： " . $e->getMessage();
}
?>
