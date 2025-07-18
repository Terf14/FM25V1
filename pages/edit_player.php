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
        echo "<script>alert('ไม่พบข้อมูลนักเตะนี้!'); window.location.href='manage_players.php';</script>";
        exit;
    }

    // ตรวจสอบการกดปุ่ม "ย้าย"
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move'])) {
        if ($conn instanceof PDO) {
            $query = "INSERT INTO former_players (player_id, name, role, position, jersey_number, status, injured, user_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $player['player_id'],
                $player['name'],
                $player['role'],
                $player['position'],
                $player['jersey_number'],
                $player['status'],
                $player['injured'],
                $player['user_id']
            ]);

            $query = "DELETE FROM Players WHERE player_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$player_id]);
        } else {
            $query = "INSERT INTO former_players (player_id, name, role, position, jersey_number, status, injured, user_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param(
                $stmt,
                'issssssi',
                $player['player_id'],
                $player['name'],
                $player['role'],
                $player['position'],
                $player['jersey_number'],
                $player['status'],
                $player['injured'],
                $player['user_id']
            );
            mysqli_stmt_execute($stmt);

            $query = "DELETE FROM Players WHERE player_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $player_id);
            mysqli_stmt_execute($stmt);
        }

        echo "<script>alert('ย้ายนักเตะสำเร็จ!'); window.location.href='manage_players.php';</script>";
        exit;
    }

    // ตรวจสอบการกดปุ่ม "ลบ"
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        if ($conn instanceof PDO) {
            $query = "DELETE FROM Players WHERE player_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$player_id]);
        } else {
            $query = "DELETE FROM Players WHERE player_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $player_id);
            mysqli_stmt_execute($stmt);
        }

        echo "<script>alert('ลบผู้เล่นสำเร็จ!'); window.location.href='manage_players.php';</script>";
        exit;
    }

    // อัปเดตข้อมูลนักเตะ
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['move']) && !isset($_POST['delete'])) {
        $name = $_POST['name'];
        $role = $_POST['role'];
        $position = $_POST['position'];
        $jersey_number = $_POST['jersey_number'];
        $status = $_POST['status'];
        $injured = $_POST['injured'];

        if ($conn instanceof PDO) {
            $query = "UPDATE Players SET name = ?, role = ?, position = ?, jersey_number = ?, status = ?, injured = ? WHERE player_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$name, $role, $position, $jersey_number, $status, $injured, $player_id]);
        } else {
            $query = "UPDATE Players SET name = ?, role = ?, position = ?, jersey_number = ?, status = ?, injured = ? WHERE player_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssssssi', $name, $role, $position, $jersey_number, $status, $injured, $player_id);
            mysqli_stmt_execute($stmt);
        }

        echo "<script>alert('อัปเดตนักเตะสำเร็จ!'); window.location.href='manage_players.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ไม่พบข้อมูลนักเตะ'); window.location.href='manage_players.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขนักเตะ | FM25 Manager</title>
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
            <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h1 class="text-xl font-semibold flex items-center gap-2 mb-6">
                    <i data-lucide="user-cog" class="w-5 h-5 text-gray-600"></i>
                    แก้ไขนักเตะ
                </h1>

                <a href="#" class="inline-block mb-6">
                    <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition text-sm font-medium">
                        เพิ่มค่าพลัง
                    </button>
                </a>

                <form action="edit_player.php?id=<?php echo $player['player_id']; ?>" method="POST" enctype="multipart/form-data" class="space-y-4">

                    <div>
                        <label for="name" class="text-sm font-medium text-gray-700">ชื่อ</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($player['name']); ?>" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="role" class="text-sm font-medium text-gray-700">Role</label>
                            <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900">
                                <option value="crucial" <?= $player['role'] === 'crucial' ? 'selected' : ''; ?>>Crucial</option>
                                <option value="important" <?= $player['role'] === 'important' ? 'selected' : ''; ?>>Important</option>
                                <option value="rotation" <?= $player['role'] === 'rotation' ? 'selected' : ''; ?>>Rotation</option>
                                <option value="sporadic" <?= $player['role'] === 'sporadic' ? 'selected' : ''; ?>>Sporadic</option>
                                <option value="prospect" <?= $player['role'] === 'prospect' ? 'selected' : ''; ?>>Prospect</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="text-sm font-medium text-gray-700">สถานะ</label>
                            <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900">
                                <option value="no" <?= $player['status'] === 'no' ? 'selected' : ''; ?>>อยู่กับทีม</option>
                                <option value="sell" <?= $player['status'] === 'sell' ? 'selected' : ''; ?>>ขาย</option>
                                <option value="for_loan" <?= $player['status'] === 'for_loan' ? 'selected' : ''; ?>>พร้อมปล่อยยืม</option>
                                <option value="on_loan" <?= $player['status'] === 'on_loan' ? 'selected' : ''; ?>>ถูกยืมตัว</option>
                                <option value="in_loan" <?= $player['status'] === 'in_loan' ? 'selected' : ''; ?>>กำลังยืมตัว</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="position" class="text-sm font-medium text-gray-700">ตำแหน่ง</label>
                            <select id="position" name="position" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900">
                                <?php
                                $positions = ['st', 'cf', 'lw', 'rw', 'lm', 'rm', 'cam', 'cm', 'cdm', 'rb', 'lb', 'cb', 'gk'];
                                foreach ($positions as $pos) {
                                    echo "<option value='$pos'" . ($player['position'] === $pos ? ' selected' : '') . ">" . strtoupper($pos) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="jersey_number" class="text-sm font-medium text-gray-700">เบอร์เสื้อ</label>
                            <input type="number" id="jersey_number" name="jersey_number" value="<?= htmlspecialchars($player['jersey_number']); ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900">
                        </div>
                    </div>

                    <div>
                        <label for="injured" class="text-sm font-medium text-gray-700">บาดเจ็บ</label>
                        <input type="text" id="injured" name="injured" value="<?= htmlspecialchars($player['injured']); ?>" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-900">
                    </div>

                    <div class="flex flex-wrap justify-center gap-4 pt-4">
                        <button type="submit"
                            class="flex items-center gap-2 bg-gray-900 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition font-medium">
                            <i data-lucide="check" class="w-5 h-5"></i> อัปเดตนักเตะ
                        </button>

                        <button type="submit" name="delete"
                            class="flex items-center gap-2 bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 transition font-medium">
                            <i data-lucide="trash" class="w-5 h-5"></i> ลบ
                        </button>

                        <button type="submit" name="move"
                            class="flex items-center gap-2 bg-yellow-500 text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition font-medium">
                            <i data-lucide="move-right" class="w-5 h-5"></i> ย้ายผู้เล่น
                        </button>

                        <a href="manage_players.php"
                            class="flex items-center gap-2 bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition font-medium">
                            <i data-lucide="x" class="w-5 h-5"></i> ยกเลิก
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>