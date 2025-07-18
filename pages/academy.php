<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

// ตรวจสอบว่า user_id อยู่ใน session หรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้าไม่มี session ของ user_id ให้รีไดเร็กต์ไปหน้าเข้าสู่ระบบ
    header("Location: login.php");
    exit();
}

// รับ user_id จาก session
$user_id = $_SESSION['user_id'];

// แก้ไขคำสั่ง SQL เพื่อแสดงผลเฉพาะข้อมูลของ user นั้นๆ
$query = $conn->prepare("SELECT * FROM academy_players WHERE user_id = :user_id ORDER BY academy_player_id ASC");
$query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$query->execute();
$academyPlayers = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>จัดการนักเตะเยาวชน | FM25 Manager</title>
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
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include '../includes/navbar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto px-10 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-semibold flex items-center gap-2 mb-6">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-gray-600"></i>
                    จัดการนักเตะเยาวชน
                </h1>
                <a href="add_academy_player.php"
                    class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">เพิ่มนักเตะ</a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow-sm rounded-lg border border-gray-200">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 text-left">ชื่อ</th>
                            <th class="p-3 text-left">ตำแหน่ง</th>
                            <th class="p-3 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($academyPlayers as $player) { ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-3"><?php echo $player['name']; ?></td>
                                <td class="p-3"><?php echo strtoupper($player['position']); ?></td>
                                <td class="p-3 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="edit_academy_player.php?id=<?php echo $player['academy_player_id']; ?>"
                                            class="text-blue-500 hover:underline"><i data-lucide="user-pen" class="w-4 h-4"></i></a>
                                        <button
                                            class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition text-sm"
                                            onclick="promotePlayer(<?php echo $player['academy_player_id']; ?>)">
                                            ดันขึ้นชุดใหญ่
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
    </div>
    </main>
    </div>

    <!-- Promote script -->
    <script>
        function promotePlayer(playerId) {
            if (confirm("คุณแน่ใจหรือไม่ว่าต้องการดันนักเตะขึ้นชุดใหญ่?")) {
                fetch('promote_academy_player.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'player_id=' + playerId
                    })
                    .then(() => location.reload())
                    .catch(error => console.error('Error:', error));
            }
        }
        lucide.createIcons();
    </script>
</body>

</html>