<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['player_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$player_id = intval($_POST['player_id']);
$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

try {
    if ($conn instanceof PDO) {
        // ดึงข้อมูลนักเตะ
        $stmt = $conn->prepare("SELECT performance_score, score_change, change_count FROM Players WHERE player_id = ? AND user_id = ?");
        $stmt->execute([$player_id, $user_id]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$player) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบผู้เล่น']);
            exit;
        }

        $current_score = intval($player['performance_score']);
        $last_change = $player['score_change'];
        $change_count = intval($player['change_count']);

        // คำนวณคะแนนใหม่
        if ($action === 'increase') {
            $new_score = $current_score + 1;
            if ($last_change === 'increase') {
                $change_count += 1;
            } else {
                $change_count = 1;  // รีเซ็ตถ้าสลับจาก decrease -> increase
            }
            $change_status = 'increase';
        } elseif ($action === 'decrease') {
            $new_score = $current_score - 1;
            if ($last_change === 'decrease') {
                $change_count += 1;
            } else {
                $change_count = 1;  // รีเซ็ตถ้าสลับจาก increase -> decrease
            }
            $change_status = 'decrease';
        } elseif ($action === 'reset') {
            $new_score = 0;
            $change_status = 'neutral';
            $change_count = 0;
        } else {
            echo json_encode(['success' => false, 'message' => 'action ไม่ถูกต้อง']);
            exit;
        }

        // อัปเดตคะแนนและจำนวนครั้ง
        $updateStmt = $conn->prepare("UPDATE Players SET performance_score = ?, score_change = ?, change_count = ? WHERE player_id = ? AND user_id = ?");
        $updateStmt->execute([$new_score, $change_status, $change_count, $player_id, $user_id]);

        echo json_encode([
            'success' => true,
            'new_score' => $new_score,
            'change_status' => $change_status,
            'change_count' => $change_count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'การเชื่อมต่อฐานข้อมูลล้มเหลว']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
