<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$user_id = $_SESSION['user_id'];

$year = $_GET['year'] ?? null;

// ถ้ามีเลือกปี
if ($year) {
    $stmt = $conn->prepare("SELECT ps.*, p.name 
                            FROM player_statistics ps
                            INNER JOIN players p ON ps.player_id = p.player_id
                            WHERE ps.user_id = :user_id AND ps.year = :year
                            ORDER BY p.name ASC");
    $stmt->execute([
        'user_id' => $user_id,
        'year' => $year
    ]);
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานทีม | FM25 Manager</title>
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
                    <i data-lucide="list" class="w-5 h-5 text-gray-600"></i>
                    รายงานทีม
                </h1>
            </div>

            <!-- ฟอร์มเลือกปี -->
            <form method="get" class="flex items-center gap-4 mb-6">
                <label class="font-medium">เลือกปี</label>
                <input type="text" name="year" required placeholder="เช่น 2024 หรือ 2024/2025" value="<?= htmlspecialchars($year) ?>" class="border border-gray-300 rounded-md p-2 w-48">
                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ดูรายงาน
                </button>
            </form>

            <?php if ($year && isset($players) && count($players) > 0): ?>
                <div class="overflow-x-auto bg-white rounded-lg shadow-md p-6">
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
                                    <td class="border p-2 text-center"><?= htmlspecialchars($player['appearances']) ?></td>
                                    <td class="border p-2 text-center"><?= htmlspecialchars($player['goals']) ?></td>
                                    <td class="border p-2 text-center"><?= htmlspecialchars($player['assists']) ?></td>
                                    <td class="border p-2 text-center"><?= htmlspecialchars($player['clean_sheets']) ?></td>
                                    <td class="border p-2 text-center"><?= htmlspecialchars($player['yellow_cards']) ?></td>
                                    <td class="border p-2 text-center"><?= htmlspecialchars($player['red_cards']) ?></td>
                                    <td class="border p-2 text-center"><?= htmlspecialchars($player['rating']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($year): ?>
                <div class="bg-yellow-100 text-yellow-700 p-4 rounded-md text-center">
                    ไม่พบข้อมูลนักเตะสำหรับปี <?= htmlspecialchars($year) ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
