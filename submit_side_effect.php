<?php
$host = 'localhost';
$port = 5432;
$dbname = 'Vaccine_system';
$user = 'postgres';
$password = trim(file_get_contents('db_password.txt'));

$pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password); 

$userId = $_POST['userId'];
$hadSideEffects = $_POST['hadSideEffects'];
$severityLevel = $_POST['severityLevel'];
$sideEffectType = $_POST['sideEffectType'];
$sideEffectStartDate = $_POST['sideEffectStartDate'];
$sideEffectEndDate = $_POST['sideEffectEndDate'];

try {
    $stmt = $pdo->prepare("SELECT appointment_id FROM appointment WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $followupId = $result['appointment_id'];

        // 更新 follow_up 表
        $insertStmt = $pdo->prepare("INSERT INTO follow_up (followup_id, had_side_effects) VALUES (:followupId, :hadSideEffects)");
        $insertStmt->bindParam(':followupId', $followupId);
        $insertStmt->bindParam(':hadSideEffects', $hadSideEffects);
        $insertStmt->execute();

        // 更新 side_effects_report 表
        if ($hadSideEffects === 'Yes') {
            $insertStmt = $pdo->prepare("INSERT INTO side_effects_report (side_effects_report_id, side_effect_type, severity_level, start_date, end_date) VALUES (:reportId, :type, :severity, :startDate, :endDate)");
            $insertStmt->bindParam(':reportId', $followupId);
            $insertStmt->bindParam(':type', $sideEffectType);
            $insertStmt->bindParam(':severity', $severityLevel);
            $insertStmt->bindParam(':startDate', $sideEffectStartDate);
            $insertStmt->bindParam(':endDate', $sideEffectEndDate);
            $insertStmt->execute();
        }

        echo json_encode(['success' => 'Side effect report submitted successfully']);
    } else {
        echo json_encode(['error' => 'No matching appointment found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
