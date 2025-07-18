<?php
session_start();
include '../includes/db_config.php';

// ตรวจสอบว่ามี ID นักเตะหรือไม่
if (!isset($_GET['id'])) {
    header("Location: manage_academy_players.php");
    exit;
}

$player_id = $_GET['id'];

// ดึงข้อมูลนักเตะจากฐานข้อมูล
$query = $conn->prepare("SELECT * FROM Academy_Players WHERE academy_player_id = ?");
$query->execute([$player_id]);
$player = $query->fetch(PDO::FETCH_ASSOC);

if (!$player) {
    header("Location: manage_academy_players.php");
    exit;
}

// อัปเดตข้อมูลเมื่อกดบันทึก
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];

    $updateQuery = $conn->prepare("UPDATE Academy_Players SET name = ?, position = ? WHERE academy_player_id = ?");
    $updateQuery->execute([$name, $position, $player_id]);

    header("Location: academy.php");
    exit;
}

// ลบนักเตะ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $deleteQuery = $conn->prepare("DELETE FROM Academy_Players WHERE academy_player_id = ?");
    $deleteQuery->execute([$player_id]);

    header("Location: academy.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>แก้ไขนักเตะ | FC25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../includes/navbar.php'; ?>

        <main class="flex-1 px-6 py-8 overflow-y-auto">
            <div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                <h1 class="text-2xl font-bold mb-8">แก้ไขข้อมูลนักเตะ</h1>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-600 mb-1">ชื่อ</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($player['name']) ?>"
                            class="w-full border-none bg-gray-100 rounded-xl px-4 py-2 focus:ring-2 focus:ring-black focus:bg-white transition" required>
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-600 mb-1">ตำแหน่ง</label>
                        <select id="position" name="position"
                            class="w-full border-none bg-gray-100 rounded-xl px-4 py-2 focus:ring-2 focus:ring-black focus:bg-white transition" required>
                            <?php
                            $positions = ['st', 'cf', 'lw', 'rw', 'lm', 'rm', 'cam', 'cm', 'cdm', 'cb', 'lb', 'rb', 'gk'];
                            foreach ($positions as $pos) {
                                $selected = ($player['position'] == $pos) ? "selected" : "";
                                echo "<option value=\"$pos\" $selected>" . strtoupper($pos) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="submit" name="update"
                            class="bg-black text-white font-semibold px-6 py-2 rounded-xl hover:bg-gray-800 transition">
                            บันทึกการเปลี่ยนแปลง
                        </button>
                        <a href="academy.php" class="text-sm text-gray-500 hover:underline">ยกเลิก</a>
                    </div>
                </form>

                <!-- ปุ่มลบ -->
                <form method="POST" onsubmit="return confirmDelete();" class="mt-10">
                    <button type="submit" name="delete"
                        class="w-full bg-red-500 text-white font-semibold px-6 py-3 rounded-xl hover:bg-red-600 transition">
                        ลบนักเตะ
                    </button>
                </form>
            </div>
        </main>

        <script>
            function confirmDelete() {
                return confirm('คุณต้องการลบนักเตะคนนี้หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้');
            }
            lucide.createIcons();
        </script>
</body>

</html>