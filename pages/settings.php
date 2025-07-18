<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$user_id = $_SESSION['user_id'];

// ดึงข้อมูล user ปัจจุบัน
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// อัปเดตข้อมูลเมื่อกดบันทึก
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_password = trim($_POST['password']);

    if (!empty($new_username)) {
        $update_stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $update_stmt->execute([$new_username, $user_id]);
    }

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->execute([$hashed_password, $user_id]);
    }

    header("Location: settings.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ตั้งค่า | FM25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen">
        <?php include '../includes/navbar.php'; ?>

        <main class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-2xl font-semibold flex items-center gap-2 mb-8">
                <i data-lucide="settings" class="w-5 h-5 text-gray-600"></i> ตั้งค่า
            </h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-6">
                    ✅ บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว
                </div>
            <?php endif; ?>

            <form method="POST" class="bg-white border rounded-xl shadow-sm p-6 max-w-xl space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">ชื่อผู้ใช้ (Username)</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนให้เว้นว่าง)</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        บันทึกการเปลี่ยนแปลง
                    </button>
                    <a href="dashboard.php"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                        ยกเลิก
                    </a>
                </div>
            </form>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
