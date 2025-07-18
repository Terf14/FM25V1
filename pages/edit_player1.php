<?php
session_start();
include '../includes/db_config.php'; // เรียกใช้งานไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า user_id อยู่ใน session หรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้าไม่มี session ของ user_id ให้รีไดเร็กต์ไปหน้าเข้าสู่ระบบ
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // รับ user_id จาก session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบและกำหนดค่าเริ่มต้น
    $name = !empty($_POST['name']) ? $_POST['name'] : 'Unknown';
    $position = !empty($_POST['position']) ? $_POST['position'] : 'N/A';

    // เพิ่มข้อมูลลงฐานข้อมูล โดยเพิ่ม user_id
    $query = $conn->prepare("INSERT INTO Academy_Players (name, position, user_id) VALUES (?, ?, ?)");
    $query->execute([$name, $position, $user_id]);

    // เพิ่ม alert ให้ผู้ใช้ทราบว่าเพิ่มสำเร็จ
    echo "<script>alert('เพิ่มนักเตะเยาวชนสำเร็จแล้ว!'); window.location.href='academy.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>เพิ่มนักเตะเยาวชน | FM25 Manager</title>
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
        <?php include '../includes/navbar.php'; /* */ ?>

        <main class="flex-1 overflow-y-auto px-10 py-8">
            <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h1 class="text-xl font-semibold flex items-center gap-2 mb-6">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-gray-600"></i>
                    เพิ่มนักเตะเยาวชน
                </h1>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">ชื่อ</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 transition">
                    </div>

                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">ตำแหน่ง</label>
                        <select id="position" name="position" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 transition">
                            <option value="">-- เลือกตำแหน่ง --</option>
                            <option value="st">ST</option>
                            <option value="cf">CF</option>
                            <option value="lw">LW</option>
                            <option value="rw">RW</option>
                            <option value="lm">LM</option>
                            <option value="rm">RM</option>
                            <option value="cam">CAM</option>
                            <option value="cm">CM</option>
                            <option value="cdm">CDM</option>
                            <option value="cb">CB</option>
                            <option value="lb">LB</option>
                            <option value="rb">RB</option>
                            <option value="gk">GK</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="submit"
                            class="bg-gray-900 text-white px-5 py-2 rounded-md hover:bg-gray-700 transition font-medium">
                            เพิ่มนักเตะ
                        </button>
                        <a href="academy.php" class="text-sm text-gray-600 hover:text-gray-900 transition">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script>
    lucide.createIcons();

    // ส่วนนี้เป็นโค้ดเดิมที่เพิ่ม * ต่อท้ายชื่อนักเตะ
    // หากไม่ต้องการให้เพิ่ม * อัตโนมัติ สามารถลบส่วนนี้ออกได้
    document.querySelector("form").addEventListener("submit", function (e) {
        const nameInput = document.getElementById("name");
        if (nameInput.value.trim() !== "") {
            // เช็คว่ามี * แล้วหรือยัง ถ้ายังไม่มีให้เพิ่ม
            if (!nameInput.value.trim().endsWith("*")) {
                nameInput.value = nameInput.value.trim() + " *";
            }
        }
    });
</script>

</body>

</html>