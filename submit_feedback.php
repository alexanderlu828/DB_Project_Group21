<?php
$host = 'localhost';
$port = 5432;
$dbname = 'Vaccine_system';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

$pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

$userId = $_POST['userId'];
$score = $_POST['score'];
$feedbackDate = date('Y-m-d'); // 使用當前日期作為反饋日期

try {
    // 查詢對應的 appointment_id
    $stmt = $pdo->prepare("SELECT appointment_id FROM appointment WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $feedbackId = $result['appointment_id'];

        // 插入回饋資料，只插入 feedback_id, feedback_rating, feedback_date
        $insertStmt = $pdo->prepare("INSERT INTO user_feedback (feedback_id, feedback_rating, feedback_date) VALUES (:feedbackId, :score, :feedbackDate)");
        $insertStmt->bindParam(':feedbackId', $feedbackId);
        $insertStmt->bindParam(':score', $score);
        $insertStmt->bindParam(':feedbackDate', $feedbackDate);
        $insertStmt->execute();

        echo json_encode(['success' => 'Feedback submitted successfully']);
    } else {
        echo json_encode(['error' => 'No matching appointment found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
