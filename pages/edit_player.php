<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

if (isset($_GET['id'])) {
    $player_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // ดึง user_id จาก session

    if ($conn instanceof PDO) {
        // ดึงข้อมูลนักเตะ รวมถึง is_academy_product, injured, injured_count
        $query = "SELECT * FROM Players WHERE player_id = ? AND user_id = ?"; // เพิ่ม user_id ในเงื่อนไข
        $stmt = $conn->prepare($query);
        $stmt->execute([$player_id, $user_id]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // สำหรับ mysqli
        $query = "SELECT * FROM Players WHERE player_id = $player_id AND user_id = $user_id";
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
            $query = "INSERT INTO former_players (player_id, name, role, position, jersey_number, status, injured, injured_count, user_id, is_academy_product) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // เพิ่ม injured_count, is_academy_product
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $player['player_id'],
                $player['name'],
                $player['role'],
                $player['position'],
                $player['jersey_number'],
                $player['status'],
                $player['injured'],
                $player['injured_count'], // เพิ่ม injured_count
                $player['user_id'],
                $player['is_academy_product'] // เพิ่ม is_academy_product
            ]);

            $query = "DELETE FROM Players WHERE player_id = ? AND user_id = ?"; // เพิ่ม user_id ในเงื่อนไข
            $stmt = $conn->prepare($query);
            $stmt->execute([$player_id, $user_id]);
        } else {
            // สำหรับ mysqli
            $query = "INSERT INTO former_players (player_id, name, role, position, jersey_number, status, injured, injured_count, user_id, is_academy_product) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param(
                $stmt,
                'issssssiii', // เพิ่ม i สำหรับ injured_count, is_academy_product
                $player['player_id'],
                $player['name'],
                $player['role'],
                $player['position'],
                $player['jersey_number'],
                $player['status'],
                $player['injured'],
                $player['injured_count'],
                $player['user_id'],
                $player['is_academy_product']
            );
            mysqli_stmt_execute($stmt);

            $query = "DELETE FROM Players WHERE player_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $player_id, $user_id);
            mysqli_stmt_execute($stmt);
        }

        echo "<script>alert('ย้ายนักเตะสำเร็จ!'); window.location.href='manage_players.php';</script>";
        exit;
    }

    // ตรวจสอบการกดปุ่ม "ลบ"
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        if ($conn instanceof PDO) {
            $query = "DELETE FROM Players WHERE player_id = ? AND user_id = ?"; // เพิ่ม user_id ในเงื่อนไข
            $stmt = $conn->prepare($query);
            $stmt->execute([$player_id, $user_id]);
        } else {
            // สำหรับ mysqli
            $query = "DELETE FROM Players WHERE player_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $player_id, $user_id);
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
        // บาดเจ็บ
        $injured_checkbox = isset($_POST['injured_checkbox']) ? 1 : 0; // 1 ถ้าติ๊ก, 0 ถ้าไม่ติ๊ก
        $add_injured_days = isset($_POST['add_injured_days']) ? (int)$_POST['add_injured_days'] : 0;

        // ดึงค่า injured_count ปัจจุบันจาก player object
        $current_injured_count = $player['injured_count'];
        $new_injured_count = $current_injured_count + $add_injured_days;

        // is_academy_product (ยังไม่ให้แก้ไขจากหน้านี้ เพื่อคงสถานะที่ถูก set ตอน promote)
        // $is_academy_product = isset($_POST['is_academy_product']) ? 1 : 0; 

        if ($conn instanceof PDO) {
            $query = "UPDATE Players SET name = ?, role = ?, position = ?, jersey_number = ?, status = ?, injured = ?, injured_count = ? WHERE player_id = ? AND user_id = ?"; // เพิ่ม injured, injured_count
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $name,
                $role,
                $position,
                $jersey_number,
                $status,
                $injured_checkbox, // อัปเดต injured ตาม checkbox
                $new_injured_count, // อัปเดต injured_count
                $player_id,
                $user_id
            ]);
        } else {
            // สำหรับ mysqli
            $query = "UPDATE Players SET name = ?, role = ?, position = ?, jersey_number = ?, status = ?, injured = ?, injured_count = ? WHERE player_id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param(
                $stmt,
                'sssssiiii', // เพิ่ม i สำหรับ injured, injured_count
                $name,
                $role,
                $position,
                $jersey_number,
                $status,
                $injured_checkbox,
                $new_injured_count,
                $player_id,
                $user_id
            );
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
        <?php include '../includes/navbar.php'; ?>

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
                                    echo "<option value='$pos'" . ($player['position'] === $pos ? 'selected' : '') . ">" . strtoupper($pos) . "</option>";
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

                    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                        <label class="block text-sm font-semibold text-gray-800 mb-2">ข้อมูลการบาดเจ็บ</label>
                        <div class="flex items-center gap-3 mb-2">
                            <input type="checkbox" id="injured_checkbox" name="injured_checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                                <?= $player['injured'] > 0 ? 'checked' : ''; ?>>
                            <label for="injured_checkbox" class="text-sm font-medium text-gray-700">นักเตะบาดเจ็บอยู่</label>
                        </div>
                        <div class="flex items-center gap-3">
                            <label for="injured_count" class="text-sm font-medium text-gray-700">จำนวนวันบาดเจ็บสะสม:</label>
                            <span id="current_injured_count" class="text-lg font-bold text-gray-900"><?= htmlspecialchars($player['injured_count']); ?> วัน</span>
                        </div>
                        <div class="mt-3">
                            <label for="add_injured_days" class="block text-sm font-medium text-gray-700 mb-1">เพิ่มจำนวนวันบาดเจ็บ (กรอกตัวเลข):</label>
                            <input type="number" id="add_injured_days" name="add_injured_days" value="0" min="0"
                                class="w-32 px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>

                    <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="is_academy_product" name="is_academy_product" class="h-4 w-4 text-purple-600 border-gray-300 rounded"
                                <?= $player['is_academy_product'] == 1 ? 'checked' : ''; ?> disabled>
                            <label for="is_academy_product" class="text-sm font-medium text-gray-700">นักเตะจาก Academy</label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">สถานะนี้จะถูกตั้งค่าอัตโนมัติเมื่อดันนักเตะจาก Academy ขึ้นชุดใหญ่</p>
                    </div>

                    <div class="flex flex-wrap justify-center gap-4 pt-4">
                        <button type="submit"
                            class="flex items-center gap-2 bg-gray-900 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition font-medium">
                            <i data-lucide="check" class="w-5 h-5"></i> อัปเดตนักเตะ
                        </button>

                        <button type="submit" name="delete"
                            class="flex items-center gap-2 bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 transition font-medium"
                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบนักเตะคนนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                            <i data-lucide="trash" class="w-5 h-5"></i> ลบ
                        </button>

                        <button type="submit" name="move"
                            class="flex items-center gap-2 bg-yellow-500 text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition font-medium"
                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการย้ายนักเตะคนนี้ไปอดีตนักเตะ?');">
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