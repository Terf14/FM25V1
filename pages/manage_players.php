<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";  // ตรวจสอบการเข้าสู่ระบบ

// สมมติว่า session มีการเก็บ user_id ไว้
$userId = $_SESSION['user_id'];

// คำนวณจำนวนของนักเตะในแต่ละสถานะ
$status_counts = [
    'sell' => 0,
    'for_loan' => 0,
    'on_loan' => 0,
    'in_loan' => 0,
    'no' => 0
];

if ($conn instanceof PDO) {
    $statusQuery = $conn->prepare("SELECT status, COUNT(*) as count FROM Players WHERE user_id = :user_id GROUP BY status");
    $statusQuery->execute([':user_id' => $userId]);
    $statusResults = $statusQuery->fetchAll(PDO::FETCH_ASSOC);
    foreach ($statusResults as $row) {
        if (isset($status_counts[$row['status']])) {
            $status_counts[$row['status']] = $row['count'];
        }
    }
} else {
    $statusQuery = mysqli_prepare($conn, "SELECT status, COUNT(*) as count FROM Players WHERE user_id = ? GROUP BY status");
    mysqli_stmt_bind_param($statusQuery, "i", $userId);
    mysqli_stmt_execute($statusQuery);
    $statusResults = mysqli_stmt_get_result($statusQuery);
    while ($row = mysqli_fetch_assoc($statusResults)) {
        if (isset($status_counts[$row['status']])) {
            $status_counts[$row['status']] = $row['count'];
        }
    }
}

// คำนวณจำนวนนักเตะทั้งหมดที่เชื่อมโยงกับผู้ใช้
$totalPlayers = 0;
if ($conn instanceof PDO) {
    $queryTotal = $conn->prepare("SELECT COUNT(*) as total FROM Players WHERE user_id = :user_id");
    $queryTotal->execute([':user_id' => $userId]);
    $totalPlayers = $queryTotal->fetch(PDO::FETCH_ASSOC)['total'];
} else {
    $queryTotal = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM Players WHERE user_id = ?");
    mysqli_stmt_bind_param($queryTotal, "i", $userId);
    mysqli_stmt_execute($queryTotal);
    $rowTotal = mysqli_stmt_get_result($queryTotal);
    $totalPlayers = mysqli_fetch_assoc($rowTotal)['total'];
}

// คำนวณจำนวนของนักเตะในแต่ละ Role สำหรับผู้ใช้
$role_counts = [];
$roles = ['crucial', 'important', 'rotation', 'sporadic', 'prospect'];

// สำหรับ PDO
if ($conn instanceof PDO) {
    foreach ($roles as $role) {
        $query = $conn->prepare("SELECT COUNT(*) as count FROM Players WHERE role = :role AND user_id = :user_id");
        $query->execute([':role' => $role, ':user_id' => $userId]);
        $role_result = $query->fetch(PDO::FETCH_ASSOC);
        $role_counts[$role] = $role_result['count'];
    }
} else {  // สำหรับ MySQLi
    foreach ($roles as $role) {
        $query = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM Players WHERE role = ? AND user_id = ?");
        mysqli_stmt_bind_param($query, "si", $role, $userId);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $role_result = mysqli_fetch_assoc($result);
        $role_counts[$role] = $role_result['count'];
    }
}

// ดึงข้อมูลนักเตะที่เชื่อมโยงกับผู้ใช้
if ($conn instanceof PDO) {
    $queryByJersey = $conn->prepare("SELECT * FROM Players WHERE user_id = :user_id ORDER BY jersey_number ASC");
    $queryByJersey->execute([':user_id' => $userId]);
    $resultByJersey = $queryByJersey->fetchAll(PDO::FETCH_ASSOC);

    $resultByRole = [];
    foreach ($roles as $role) {
        $query = $conn->prepare("SELECT * FROM Players WHERE role = :role AND user_id = :user_id ORDER BY player_id ASC");
        $query->execute([':role' => $role, ':user_id' => $userId]);
        $resultByRole[$role] = $query->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    $queryByJersey = mysqli_prepare($conn, "SELECT * FROM Players WHERE user_id = ? ORDER BY jersey_number ASC");
    mysqli_stmt_bind_param($queryByJersey, "i", $userId);
    mysqli_stmt_execute($queryByJersey);
    $resultByJersey = mysqli_fetch_all(mysqli_stmt_get_result($queryByJersey), MYSQLI_ASSOC);

    $resultByRole = [];
    foreach ($roles as $role) {
        $query = mysqli_prepare($conn, "SELECT * FROM Players WHERE role = ? AND user_id = ? ORDER BY player_id ASC");
        mysqli_stmt_bind_param($query, "si", $role, $userId);
        mysqli_stmt_execute($query);
        $resultByRole[$role] = mysqli_fetch_all(mysqli_stmt_get_result($query), MYSQLI_ASSOC);
    }
}

// คำนวณจำนวนแถวสูงสุดที่ต้องใช้
$maxRows = max(array_map('count', $resultByRole));

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการนักเตะ | FM25 Manager</title>
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
        <main class="flex-1 px-10 py-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-semibold flex items-center gap-2 mb-6">
                    <i data-lucide="users" class="w-5 h-5 text-gray-600"></i>
                    จัดการนักเตะ
                </h1>
                <a href="add_player.php"
                    class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">เพิ่มนักเตะ</a>
            </div>

            <!-- Filter / Stats Inline -->
            <div class="text-sm text-gray-900 mb-6 flex flex-wrap gap-x-4 gap-y-2 font-semibold">
                <span class="text-gray-900">นักเตะทั้งหมดในทีม: <?php echo $totalPlayers; ?> คน</span>
                <span class="text-sky-500">อยู่กับทีม : <?php echo $status_counts['no'] ?? 0; ?> คน</span>
                <span class="text-red-500">เตรียมขาย : <?php echo $status_counts['sell'] ?? 0; ?> คน</span>
                <span class="text-pink-500">เตรียมปล่อยยืมตัว : <?php echo $status_counts['for_loan'] ?? 0; ?> คน</span>
                <span class="text-blue-700">กำลังปล่อยยืมตัว : <?php echo $status_counts['on_loan'] ?? 0; ?> คน</span>
                <span class="text-green-700">กำลังยืมตัว : <?php echo $status_counts['in_loan'] ?? 0; ?> คน</span>
            </div>

            <!-- Tables -->
            <div class="grid grid-cols-4 gap-6">
                <!-- Left: ตามเบอร์เสื้อ -->
                <div class="bg-white p-4 rounded-lg shadow col-span-1">
                    <h2 class="text-lg font-semibold mb-3">ตามเบอร์เสื้อ</h2>
                    <table class="w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">เบอร์</th>
                                <th class="border p-2">ชื่อ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultByJersey as $row) { ?>
                                <tr>
                                    <td class="border p-2 text-center"><?php echo $row['jersey_number']; ?></td>
                                    <td class="border p-2"><?php echo $row['name']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Right: ตาม Role -->
                <div class="bg-white p-4 rounded-lg shadow col-span-3">
                    <h2 class="text-lg font-semibold mb-3">ตาม Role</h2>
                    <table class="w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <?php foreach ($roles as $role) { ?>
                                    <th class="border p-2 text-center">
                                        <?php echo ucfirst($role) . " (" . ($role_counts[$role] ?? 0) . ")"; ?>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < $maxRows; $i++) { ?>
                                <tr>
                                    <?php foreach ($roles as $role) { ?>
                                        <td class="border p-2 text-center">
                                            <?php
                                            if (isset($resultByRole[$role][$i])) {
                                                $player = $resultByRole[$role][$i];
                                                $statusClass = match ($player['status']) {
                                                    'sell'      => 'bg-red-500 hover:bg-red-600',
                                                    'for_loan'  => 'bg-pink-500 hover:bg-pink-600',
                                                    'on_loan'   => 'bg-blue-700 hover:bg-blue-800',
                                                    'in_loan'   => 'bg-green-700 hover:bg-green-800',
                                                    default     => 'bg-sky-300 hover:bg-blue-300',
                                                };
                                                echo '<a href="edit_player.php?id=' . $player['player_id'] . '" class="text-white font-semibold px-3 py-1 rounded ' . $statusClass . '">' . $player['name'] . '</a>';
                                            }
                                            ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Lucide icons -->
    <script>
        lucide.createIcons();
    </script>
</body>

</html>