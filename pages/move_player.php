<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

if (isset($_GET['id'])) {
    $player_id = $_GET['id'];

    if ($conn instanceof PDO) {
        $query = "SELECT * FROM Players WHERE player_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$player_id]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $query = "SELECT * FROM Players WHERE player_id = $player_id";
        $result = mysqli_query($conn, $query);
        $player = mysqli_fetch_assoc($result);
    }

    if (!$player) {
        echo "ไม่พบข้อมูลนักเตะนี้!";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ย้ายผู้เล่นไป former_players
        if ($conn instanceof PDO) {
            $query = "INSERT INTO former_players (player_id, name, role, position, jersey_number, status, injured) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $player['player_id'],
                $player['name'],
                $player['role'],
                $player['position'],
                $player['jersey_number'],
                $player['status'],
                $player['injured']
            ]);
        } else {
            $query = "INSERT INTO former_players (player_id, name, role, position, jersey_number, status, injured) VALUES ('$player_id', '$player[name]', '$player[role]', '$player[position]', '$player[jersey_number]', '$player[status]', '$player[injured]')";
            mysqli_query($conn, $query);
        }

        // ลบผู้เล่นจาก Players
        if ($conn instanceof PDO) {
            $query = "DELETE FROM Players WHERE player_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$player_id]);
        } else {
            $query = "DELETE FROM Players WHERE player_id = $player_id";
            mysqli_query($conn, $query);
        }

        echo "<script>alert('ย้ายนักเตะสำเร็จ!'); window.location.href='manage_players.php';</script>";
    }
} else {
    echo "<script>alert('ไม่พบข้อมูลนักเตะ'); window.location.href='manage_players.php';</script>";
}
?>
