<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php"; // ดึง user_id จาก session

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['player_id'])) {
    $player_id = $_POST['player_id'];
    $user_id = $_SESSION['user_id']; // ใช้ user_id ของผู้ใช้ที่ล็อกอิน

    // ดึงข้อมูลนักเตะจาก Academy_Players
    $query = $conn->prepare("SELECT * FROM Academy_Players WHERE academy_player_id = ? AND user_id = ?");
    $query->execute([$player_id, $user_id]);
    $player = $query->fetch(PDO::FETCH_ASSOC);

    if ($player) {
        // เพิ่มข้อมูลไปยังตาราง players โดยใช้ user_id ที่ถูกต้อง
        // และตั้งค่า is_academy_product เป็น 1
        $insertQuery = $conn->prepare("
            INSERT INTO players (user_id, name, position, role, status, is_academy_product)
            VALUES (?, ?, ?, 'prospect', 'no', 1)
        "); // เพิ่ม is_academy_product และตั้งค่าเป็น 1
        $insertQuery->execute([
            $user_id,  // ใช้ user_id ของผู้ใช้ที่ล็อกอิน
            $player['name'],
            $player['position']
        ]);

        // ลบนักเตะออกจาก Academy_Players
        $deleteQuery = $conn->prepare("DELETE FROM Academy_Players WHERE academy_player_id = ? AND user_id = ?");
        $deleteQuery->execute([$player_id, $user_id]);
    }
}

// กลับไปที่หน้าหลัก
header("Location: academy.php");
exit;
?>