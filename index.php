<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="img/logo/fm25_logo_2.png">
    <title>FC25 Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-50 text-gray-800">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm text-center px-10 py-12 max-w-lg w-full">
        <h1 class="text-2xl font-semibold mb-2 tracking-tight">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <p class="text-gray-500 mb-8">เข้าสู่ระบบจัดการข้อมูลนักเตะ FC25</p>

        <div class="flex justify-center gap-4">
            <a href="pages/dashboard.php"
                class="bg-gray-900 text-white px-5 py-2 rounded-lg hover:bg-gray-700 transition-all">เข้าสู่แดชบอร์ด</a>

            <a href="pages/logout.php"
                class="border border-gray-300 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-100 transition-all">ออกจากระบบ</a>
        </div>
    </div>
</body>

</html>