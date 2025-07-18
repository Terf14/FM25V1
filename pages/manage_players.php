<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";  // ตรวจสอบการเข้าสู่ระบบ

// Function to get initials (วางไว้ด้านบนสุดเพื่อให้ใช้งานได้ทั่วถึง)
if (!function_exists('getInitials')) {
    function getInitials($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        // Handle cases where name is like "Marcus Rashford" -> MR, or "John Doe" -> JD
        // If only one word, just take first two letters or first letter if very short
        if (strlen($initials) > 2 && count($words) > 1) {
            $initials = substr($initials, 0, 1) . substr($initials, -1);
        } elseif (empty($initials) && !empty($name)) {
            $initials = strtoupper(substr($name, 0, min(2, strlen($name))));
        }
        return $initials;
    }
}

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
    // เพิ่ม is_academy_product ใน SELECT statement
    $queryByJersey = $conn->prepare("SELECT player_id, name, position, jersey_number, status, injured, injured_count, performance_score, is_academy_product FROM Players WHERE user_id = :user_id ORDER BY jersey_number ASC");
    $queryByJersey->execute([':user_id' => $userId]);
    $resultByJersey = $queryByJersey->fetchAll(PDO::FETCH_ASSOC);

    $resultByRole = [];
    foreach ($roles as $role) {
        // เพิ่ม is_academy_product ใน SELECT statement
        $query = $conn->prepare("SELECT player_id, name, position, role, jersey_number, status, injured, injured_count, performance_score, is_academy_product FROM Players WHERE role = :role AND user_id = :user_id ORDER BY player_id ASC");
        $query->execute([':role' => $role, ':user_id' => $userId]);
        $resultByRole[$role] = $query->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // เพิ่ม is_academy_product ใน SELECT statement
    $queryByJersey = mysqli_prepare($conn, "SELECT player_id, name, position, jersey_number, status, injured, injured_count, performance_score, is_academy_product FROM Players WHERE user_id = ? ORDER BY jersey_number ASC");
    mysqli_stmt_bind_param($queryByJersey, "i", $userId);
    mysqli_stmt_execute($queryByJersey);
    $resultByJersey = mysqli_fetch_all(mysqli_stmt_get_result($queryByJersey), MYSQLI_ASSOC);

    $resultByRole = [];
    foreach ($roles as $role) {
        // เพิ่ม is_academy_product ใน SELECT statement
        $query = mysqli_prepare($conn, "SELECT player_id, name, position, role, jersey_number, status, injured, injured_count, performance_score, is_academy_product FROM Players WHERE role = ? AND user_id = ? ORDER BY player_id ASC");
        mysqli_stmt_bind_param($query, "si", $role, $userId);
        mysqli_stmt_execute($query);
        $resultByRole[$role] = mysqli_fetch_all(mysqli_stmt_get_result($query), MYSQLI_ASSOC);
    }
}

// คำนวณจำนวนแถวสูงสุดที่ต้องใช้ (ไม่จำเป็นต้องใช้กับ Card View แล้ว แต่เก็บไว้เผื่อต้องการ)
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
        /* เพิ่ม CSS สำหรับการซ่อน scrollbar แต่ยังคง scroll ได้ */
        .overflow-x-auto::-webkit-scrollbar {
            display: none;
        }
        .overflow-x-auto {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <?php include '../includes/navbar.php'; /* */ ?>

        <main class="flex-1 px-10 py-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-semibold flex items-center gap-2 mb-6">
                    <i data-lucide="users" class="w-5 h-5 text-gray-600"></i>
                    จัดการนักเตะ
                </h1>
                <a href="add_player.php"
                    class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">เพิ่มนักเตะ</a>
            </div>

            <div class="text-sm text-gray-900 mb-6 flex flex-wrap gap-x-4 gap-y-2 font-semibold">
                <span class="text-gray-900">นักเตะทั้งหมดในทีม: <?php echo $totalPlayers; ?> คน</span>
                <span class="text-sky-500">อยู่กับทีม : <?php echo $status_counts['no'] ?? 0; ?> คน</span>
                <span class="text-red-500">เตรียมขาย : <?php echo $status_counts['sell'] ?? 0; ?> คน</span>
                <span class="text-pink-500">เตรียมปล่อยยืมตัว : <?php echo $status_counts['for_loan'] ?? 0; ?> คน</span>
                <span class="text-blue-700">กำลังปล่อยยืมตัว : <?php echo $status_counts['on_loan'] ?? 0; ?> คน</span>
                <span class="text-green-700">กำลังยืมตัว : <?php echo $status_counts['in_loan'] ?? 0; ?> คน</span>
            </div>

            <div class="grid grid-cols-4 gap-6">
                <div class="bg-white p-4 rounded-lg shadow col-span-4">
                    <h2 class="text-lg font-semibold mb-3">ตาม Role</h2>
                    <div class="flex flex-nowrap overflow-x-auto gap-4 py-2">
                        <?php
                        // กำหนดชื่อ Role ในภาษาไทย
                        $role_display_thai = [
                            'crucial' => 'ตัวหลัก',
                            'important' => 'สำคัญ',
                            'rotation' => 'หมุนเวียน',
                            'sporadic' => 'สำรอง',
                            'prospect' => 'ดาวรุ่ง'
                        ];

                        foreach ($roles as $role_key):
                            $playersInRole = $resultByRole[$role_key] ?? [];
                            $roleCount = count($playersInRole);
                            $roleThaiName = $role_display_thai[$role_key] ?? ucfirst($role_key);

                            // กำหนดสีจุดสำหรับแต่ละ Role category (เพื่อความสวยงามใน header)
                            $roleHeaderDotColor = 'bg-gray-400';
                            if ($role_key === 'crucial') $roleHeaderDotColor = 'bg-green-500';
                            if ($role_key === 'important') $roleHeaderDotColor = 'bg-blue-500';
                            if ($role_key === 'rotation') $roleHeaderDotColor = 'bg-yellow-500';
                            if ($role_key === 'sporadic') $roleHeaderDotColor = 'bg-orange-500';
                            if ($role_key === 'prospect') $roleHeaderDotColor = 'bg-purple-500';
                        ?>
                            <div class="flex-none w-72 p-4 border border-gray-200 rounded-lg shadow-sm bg-gray-50">
                                <h3 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full <?= $roleHeaderDotColor; ?>"></span>
                                    <?= htmlspecialchars($roleThaiName); ?> <span class="text-gray-500 text-sm">(<?= $roleCount; ?>)</span>
                                </h3>
                                <div class="flex flex-col gap-3">
                                    <?php if (!empty($playersInRole)): ?>
                                        <?php foreach ($playersInRole as $player): ?>
                                            <?php
                                            $initials = getInitials($player['name']);
                                            $injuredDotColor = $player['injured'] > 0 ? 'bg-red-500' : 'bg-gray-400';

                                            // กำหนดสีพื้นหลังของ Card ตามสถานะ
                                            $cardBgClass = 'bg-white'; // Default for 'no' status
                                            switch ($player['status']) {
                                                case 'no':
                                                    $cardBgClass = 'bg-white'; // อยู่กับทีม
                                                    break;
                                                case 'sell':
                                                    $cardBgClass = 'bg-red-100'; // เตรียมขาย
                                                    break;
                                                case 'for_loan':
                                                    $cardBgClass = 'bg-pink-100'; // เตรียมปล่อยยืมตัว
                                                    break;
                                                case 'on_loan':
                                                    $cardBgClass = 'bg-blue-100'; // กำลังปล่อยยืมตัว
                                                    break;
                                                case 'in_loan':
                                                    $cardBgClass = 'bg-green-100'; // กำลังยืมตัว
                                                    break;
                                            }

                                            // กำหนดสีและไอคอนของ performance_score
                                            $scoreColorClass = 'text-gray-500'; // Default
                                            $scoreIcon = 'arrow-right'; // Default icon for 0
                                            if ($player['performance_score'] > 0) {
                                                $scoreColorClass = 'text-green-500';
                                                $scoreIcon = 'arrow-up';
                                            } elseif ($player['performance_score'] < 0) {
                                                $scoreColorClass = 'text-red-500';
                                                $scoreIcon = 'arrow-down';
                                            }
                                            ?>
                                            <a href="edit_player.php?id=<?= $player['player_id']; /* */ ?>"
                                                class="flex flex-col p-3 <?= $cardBgClass; ?> border border-gray-200 rounded-md shadow-sm hover:shadow-md transition">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-800 font-bold text-sm flex-shrink-0">
                                                        <?= htmlspecialchars($initials); ?>
                                                    </div>
                                                    <div class="flex-1 overflow-hidden flex items-center justify-between gap-1">
                                                        <span class="font-semibold text-base truncate text-gray-800">
                                                            <?= htmlspecialchars($player['name']); ?>
                                                            <?php if ($player['is_academy_product'] == 1): // แสดงไอคอนถ้าเป็นนักเตะเยาวชน ?>
                                                                <i data-lucide="graduation-cap" class="inline-block w-4 h-4 ml-1 text-purple-600 fill-current"></i>
                                                            <?php endif; ?>
                                                        </span>
                                                        <?php if ($player['performance_score'] !== null): ?>
                                                            <span class="flex items-center text-sm font-medium flex-shrink-0 <?= $scoreColorClass; ?>">
                                                                <i data-lucide="<?= $scoreIcon; ?>" class="w-4 h-4 mr-0.5"></i>
                                                                <?= htmlspecialchars($player['performance_score']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-between gap-2 text-xs text-gray-500 pl-[52px]">
                                                    <div class="flex items-center gap-2">
                                                        <span class="uppercase flex-shrink-0"><?= htmlspecialchars($player['position']); ?></span>
                                                        <?php if ($player['jersey_number'] !== null): ?>
                                                            <span class="font-medium text-gray-600">#<?= htmlspecialchars($player['jersey_number']); ?></span>
                                                        <?php endif; ?>
                                                        <span class="w-2 h-2 rounded-full <?= $injuredDotColor; ?> flex-shrink-0"></span>
                                                    </div>

                                                    <div class="text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0
                                                        <?php
                                                        $statusTextColor = '';
                                                        $statusBgColor = '';
                                                        switch ($player['status']) {
                                                            case 'no':
                                                                $statusTextColor = 'text-green-800';
                                                                $statusBgColor = 'bg-green-200'; // ใช้สีเข้มขึ้นเล็กน้อยตามรูปตัวอย่าง
                                                                break;
                                                            case 'sell':
                                                                $statusTextColor = 'text-red-800';
                                                                $statusBgColor = 'bg-red-200';
                                                                break;
                                                            case 'for_loan':
                                                                $statusTextColor = 'text-pink-800';
                                                                $statusBgColor = 'bg-pink-200';
                                                                break;
                                                            case 'on_loan':
                                                                $statusTextColor = 'text-blue-800';
                                                                $statusBgColor = 'bg-blue-200';
                                                                break;
                                                            case 'in_loan':
                                                                $statusTextColor = 'text-purple-800';
                                                                $statusBgColor = 'bg-purple-200';
                                                                break;
                                                            default:
                                                                $statusTextColor = 'text-gray-700';
                                                                $statusBgColor = 'bg-gray-200';
                                                                break;
                                                        }
                                                        echo "$statusTextColor $statusBgColor";
                                                        ?>">
                                                        <?php
                                                        $displayStatus = '';
                                                        switch ($player['status']) {
                                                            case 'no': $displayStatus = 'อยู่กับทีม'; break;
                                                            case 'sell': $displayStatus = 'ขาย'; break;
                                                            case 'for_loan': $displayStatus = 'พร้อมยืม'; break;
                                                            case 'on_loan': $displayStatus = 'ถูกยืม'; break;
                                                            case 'in_loan': $displayStatus = 'ยืมตัวมา'; break;
                                                            default: $displayStatus = 'ไม่ระบุ'; break;
                                                        }
                                                        echo htmlspecialchars($displayStatus);
                                                        ?>
                                                    </div>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-500 text-center py-2">ไม่มีนักเตะใน Role นี้</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow mt-6">
                <h2 class="text-lg font-semibold mb-3">รายชื่อนักเตะตามเบอร์เสื้อ</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-2">
                    <?php
                    $players_per_column = 10;
                    $total_players_in_list = count($resultByJersey);

                    for ($col = 0; $col < 4; $col++) {
                        echo '<div class="flex flex-col gap-1">';
                        $start_index = $col * $players_per_column;
                        $end_index = min($start_index + $players_per_column, $total_players_in_list);

                        for ($i = $start_index; $i < $end_index; $i++) {
                            $player = $resultByJersey[$i];
                            ?>
                            <a href="edit_player.php?id=<?= $player['player_id']; /* */ ?>"
                               class="text-sm text-gray-800 hover:text-blue-600 hover:underline">
                                #<?= htmlspecialchars($player['jersey_number']); ?> <?= htmlspecialchars($player['name']); ?>
                            </a>
                            <?php
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>