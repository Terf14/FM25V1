<?php
session_start();
include "../includes/db_config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php");
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>เข้าสู่ระบบ | FM25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-50 text-gray-800">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm w-full max-w-sm p-8">
        <h2 class="text-xl font-semibold text-center mb-6 tracking-tight">เข้าสู่ระบบ</h2>

        <?php if (!empty($error)) echo "<p class='text-red-500 text-sm text-center mb-4'>$error</p>"; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1">ชื่อผู้ใช้</label>
                <input type="text" name="username" required
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">รหัสผ่าน</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent transition">
            </div>

            <button type="submit"
                class="w-full bg-gray-900 text-white py-2 rounded-lg hover:bg-gray-700 transition-all">เข้าสู่ระบบ</button>

            <div class="text-center text-sm text-gray-500 mt-4">
                <a href="register.php" class="hover:underline hover:text-gray-800">สมัครสมาชิก</a>
                <span class="mx-1">|</span>
                <a href="reset_password.php" class="hover:underline hover:text-gray-800">ลืมรหัสผ่าน?</a>
            </div>
        </form>
    </div>
</body>

</html>