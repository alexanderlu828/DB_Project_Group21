<?php
$host = 'localhost';
$port = 5432;
$dbname = 'postgres';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_GET['userId'];

    $sql = "SELECT user_id, health_card_id, name, gender, phone, bdate, address FROM users WHERE user_id = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

