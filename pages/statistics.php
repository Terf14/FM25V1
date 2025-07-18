<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$user_id = $_SESSION['user_id'];

// ดึงนักเตะของ user คนที่ login เท่านั้น
$stmt = $conn->prepare("SELECT player_id, name FROM players WHERE user_id = :user_id ORDER BY name ASC");
$stmt->execute(['user_id' => $user_id]);
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = trim($_POST['year']);
    $player_data = $_POST['players'];

    try {
        $sql = "INSERT INTO player_statistics 
                (player_id, user_id, year, appearances, goals, assists, clean_sheets, yellow_cards, red_cards, rating)
                VALUES 
                (:player_id, :user_id, :year, :appearances, :goals, :assists, :clean_sheets, :yellow_cards, :red_cards, :rating)";
        $stmt = $conn->prepare($sql);

        foreach ($player_data as $player_id => $data) {
            $stmt->execute([
                'player_id' => $player_id,
                'user_id' => $user_id,
                'year' => $year,
                'appearances' => $data['appearances'],
                'goals' => $data['goals'],
                'assists' => $data['assists'],
                'clean_sheets' => $data['clean_sheets'],
                'yellow_cards' => $data['yellow_cards'],
                'red_cards' => $data['red_cards'],
                'rating' => $data['rating']
            ]);
        }

        header("Location: statistics.php");
        exit;
    } catch (PDOException $e) {
        $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสถิติ | FM25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen">
        <?php include '../includes/navbar.php'; ?>

        <main class="flex-1 px-10 py-8 overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 text-gray-600"></i>
                    เพิ่มสถิติ (นักเตะของคุณ)
                </h1>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <?php if (count($players) > 0): ?>
                <form method="post" class="space-y-6 bg-white p-6 rounded-lg shadow-md overflow-x-auto">
                    <div class="mb-6 flex items-center gap-4">
                        <label class="block font-medium text-gray-700">ปีที่ทำสถิติ *</label>
                        <input type="text" name="year" required placeholder="เช่น 2024 หรือ 2024/2025" class="border border-gray-300 rounded-md p-2 w-48">
                    </div>

                    <table class="min-w-full table-auto border-collapse">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2 text-left">นักเตะ</th>
                                <th class="border p-2">ลงเล่น</th>
                                <th class="border p-2">ยิง</th>
                                <th class="border p-2">แอสซิสต์</th>
                                <th class="border p-2">คลีนชีท</th>
                                <th class="border p-2">ใบเหลือง</th>
                                <th class="border p-2">ใบแดง</th>
                                <th class="border p-2">เรทติ้ง</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player): ?>
                                <tr>
                                    <td class="border p-2"><?= htmlspecialchars($player['name']) ?></td>
                                    <td class="border p-2"><input type="number" name="players[<?= $player['player_id'] ?>][appearances]" value="0" min="0" class="w-20 border-gray-300 rounded-md"></td>
                                    <td class="border p-2"><input type="number" name="players[<?= $player['player_id'] ?>][goals]" value="0" min="0" class="w-20 border-gray-300 rounded-md"></td>
                                    <td class="border p-2"><input type="number" name="players[<?= $player['player_id'] ?>][assists]" value="0" min="0" class="w-20 border-gray-300 rounded-md"></td>
                                    <td class="border p-2"><input type="number" name="players[<?= $player['player_id'] ?>][clean_sheets]" value="0" min="0" class="w-20 border-gray-300 rounded-md"></td>
                                    <td class="border p-2"><input type="number" name="players[<?= $player['player_id'] ?>][yellow_cards]" value="0" min="0" class="w-20 border-gray-300 rounded-md"></td>
                                    <td class="border p-2"><input type="number" name="players[<?= $player['player_id'] ?>][red_cards]" value="0" min="0" class="w-20 border-gray-300 rounded-md"></td>
                                    <td class="border p-2"><input type="number" step="0.01" min="0" max="10" name="players[<?= $player['player_id'] ?>][rating]" value="0.00" class="w-24 border-gray-300 rounded-md"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="bg-gray-900 text-white px-8 py-2 rounded-md hover:bg-gray-700 transition">
                            บันทึกสถิติทั้งหมด
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="bg-yellow-100 text-yellow-700 p-4 rounded-md text-center">
                    ยังไม่มีนักเตะในทีมของคุณ กรุณาเพิ่มนักเตะก่อน
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>