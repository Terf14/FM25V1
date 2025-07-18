<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php"; // สำหรับการตรวจสอบการเข้าสู่ระบบ

// ตรวจสอบว่า user_id ถูกตั้งค่าใน session หรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้าไม่มี user_id ใน session ให้ redirect ไปที่หน้าเข้าสู่ระบบ
    header("Location: login.php");
    exit;
}

// ดึง user_id จาก session
$user_id = $_SESSION['user_id'];

// ดึงข้อมูลจาก former_players ที่เชื่อมโยงกับ user_id
if ($conn instanceof PDO) {
    $query = "SELECT * FROM former_players WHERE user_id = :user_id ORDER BY player_id ASC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $query = "SELECT * FROM former_players WHERE user_id = ? ORDER BY player_id ASC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $players = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>นักเตะในอดีต | FM25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include '../includes/navbar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto px-10 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-semibold flex items-center gap-2 mb-6">
                    <i data-lucide="clock" class="w-5 h-5 text-gray-600"></i>
                    นักเตะในอดีต
                </h1>
            </div>

            <div class="overflow-x-auto bg-white shadow-sm border border-gray-200 rounded-lg">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide text-xs">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">ชื่อ</th>
                            <th class="px-4 py-3">ตำแหน่ง</th>
                            <th class="px-4 py-3">หมายเลขเสื้อ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($players)): ?>
                            <?php foreach ($players as $player): ?>
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($player['player_id']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($player['name']); ?></td>
                                    <td class="px-4 py-2"><?php echo strtoupper(htmlspecialchars($player['position'])); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($player['jersey_number']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">ไม่มีนักเตะในอดีต</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
    </div>
    </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>