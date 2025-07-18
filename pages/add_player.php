<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้าไม่มี session ของ user_id ให้รีไดเร็กต์ไปหน้าเข้าสู่ระบบ
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // รับ user_id จาก session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? NULL;
    $position = $_POST['position'] ?? NULL;
    $jersey_number = !empty($_POST['jersey_number']) ? $_POST['jersey_number'] : NULL;

    // เพิ่มข้อมูลลงในฐานข้อมูล
    if ($conn instanceof PDO) {
        $query = "INSERT INTO Players (name, role, position, jersey_number, user_id) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$name, $role, $position, $jersey_number, $user_id]);
    } else {
        $query = "INSERT INTO Players (name, role, position, jersey_number, user_id) 
                  VALUES ('$name', " . ($role ? "'$role'" : "NULL") . ", 
                          " . ($position ? "'$position'" : "NULL") . ", 
                          " . ($jersey_number ?? "NULL") . ", 
                          '$user_id')";
        mysqli_query($conn, $query);
    }

    echo "<script>alert('เพิ่มนักเตะสำเร็จ!'); window.location.href='manage_players.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>เพิ่มนักเตะ | FM25 Manager</title>
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
                    <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                    เพิ่มนักเตะ
                </h1>

                <form action="add_player.php" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="text-sm font-medium text-gray-700">ชื่อ</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 transition" />
                    </div>

                    <div>
                        <label for="role" class="text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 transition">
                            <option value="crucial">Crucial</option>
                            <option value="important">Important</option>
                            <option value="rotation">Rotation</option>
                            <option value="sporadic">Sporadic</option>
                            <option value="prospect">Prospect</option>
                        </select>
                    </div>

                    <div>
                        <label for="position" class="text-sm font-medium text-gray-700">ตำแหน่ง</label>
                        <select id="position" name="position"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 transition">
                            <option value="st">ST</option>
                            <option value="cf">CF</option>
                            <option value="lw">LW</option>
                            <option value="rw">RW</option>
                            <option value="lm">LM</option>
                            <option value="rm">RM</option>
                            <option value="cam">CAM</option>
                            <option value="cm">CM</option>
                            <option value="cdm">CDM</option>
                            <option value="rb">RB</option>
                            <option value="lb">LB</option>
                            <option value="cb">CB</option>
                            <option value="gk">GK</option>
                        </select>
                    </div>

                    <div>
                        <label for="jersey_number" class="text-sm font-medium text-gray-700">เบอร์เสื้อ</label>
                        <input type="number" id="jersey_number" name="jersey_number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 transition" />
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="submit"
                            class="bg-gray-900 text-white px-5 py-2 rounded-md hover:bg-gray-700 transition font-medium">เพิ่มนักเตะ</button>
                        <a href="manage_players.php" class="text-sm text-gray-600 hover:text-gray-900 transition">ยกเลิก</a>
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