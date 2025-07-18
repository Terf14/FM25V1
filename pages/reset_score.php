<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

try {
    if ($conn instanceof PDO) {
        $stmt = $conn->prepare("UPDATE Players SET performance_score = 0 WHERE user_id = ?");
        $stmt->execute([$user_id]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'การเชื่อมต่อฐานข้อมูลล้มเหลว']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
