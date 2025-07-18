<?php
session_start();
include "../includes/db_config.php"; // เชื่อมต่อฐานข้อมูล

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = 'รหัสผ่านใหม่ไม่ตรงกัน';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'ไม่พบชื่อผู้ใช้นี้';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$hashed_password, $user['id']]);
            $success = 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีเซ็ตรหัสผ่าน | FM25 Manager</title>
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

<body class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white w-full max-w-md p-8 rounded-2xl shadow border border-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">🔐 รีเซ็ตรหัสผ่าน</h1>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 text-sm px-4 py-3 rounded-lg mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="bg-green-50 text-green-600 text-sm px-4 py-3 rounded-lg mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-600 text-sm mb-1" for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>

            <div>
                <label class="block text-gray-600 text-sm mb-1" for="new_password">รหัสผ่านใหม่</label>
                <input type="password" id="new_password" name="new_password" required
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>

            <div>
                <label class="block text-gray-600 text-sm mb-1" for="confirm_password">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none">
            </div>

            <button type="submit"
                class="w-full bg-black text-white py-2 rounded-lg font-semibold hover:bg-gray-800 transition">รีเซ็ตรหัสผ่าน</button>

            <p class="text-xs text-gray-500 text-center mt-4">
                <a href="login.php" class="hover:underline">กลับไปที่เข้าสู่ระบบ</a>
            </p>
        </form>
    </div>
</body>

</html>
