<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$user_id = $_SESSION['user_id'];

$status_counts = [
    'sell' => 0,
    'for_loan' => 0,
    'on_loan' => 0,
    'in_loan' => 0,
    'no' => 0
];

$status_display = [
    'no' => 'อยู่กับทีม ',
    'sell' => 'เตรียมขาย ',
    'for_loan' => 'เตรียมปล่อยยืม ',
    'on_loan' => 'กำลังปล่อยยืม ',
    'in_loan' => 'กำลังยืมตัว '
];

// ดึงข้อมูลนักเตะ
$stmt_players = $conn->prepare("SELECT * FROM players WHERE user_id = ?");
$stmt_players->execute([$user_id]);
$players = $stmt_players->fetchAll();

$total_players = count($players);
$prospect_players = count(array_filter($players, fn($p) => $p['role'] === 'prospect'));

// รวมสถานะนักเตะ
foreach ($players as $p) {
    if (!empty($p['status']) && isset($status_counts[$p['status']])) {
        $status_counts[$p['status']]++;
    }
}

// ดึงข้อมูลอาคาเดมี่
$stmt_academy = $conn->prepare("SELECT COUNT(*) FROM academy_players WHERE user_id = ?");
$stmt_academy->execute([$user_id]);
$total_academy = $stmt_academy->fetchColumn();

// ดึงข้อมูลอดีตนักเตะ
$stmt_former = $conn->prepare("SELECT COUNT(*) FROM former_players WHERE user_id = ?");
$stmt_former->execute([$user_id]);
$total_former = $stmt_former->fetchColumn();

$roles = ['crucial', 'important', 'rotation', 'sporadic', 'prospect'];
$role_display = [
    'crucial' => 'ตัวหลัก ',
    'important' => 'สำคัญ ',
    'rotation' => 'หมุนเวียน ',
    'sporadic' => 'บางครั้ง ',
    'prospect' => 'ดาวรุ่ง '
];

$players_by_role = [];
foreach ($roles as $role) {
    $filtered = array_filter($players, fn($p) => $p['role'] === $role);
    usort($filtered, fn($a, $b) => strcmp($a['name'], $b['name']));
    $players_by_role[$role] = [
        'count' => count($filtered),
        'players' => array_slice($filtered, 0, 5)
    ];
}

// ดีเด่น (Top 5 เพิ่มขึ้นเยอะสุด)
$top_performers = array_filter($players, fn($p) => $p['score_change'] === 'increase' && $p['change_count'] > 0);
usort($top_performers, fn($a, $b) => $b['change_count'] <=> $a['change_count']);
$top_performers = array_slice($top_performers, 0, 5);

// ยอดแย่ (Bottom 5 ลดลงเยอะสุด)
$bottom_performers = array_filter($players, fn($p) => $p['score_change'] === 'decrease' && $p['change_count'] > 0);
usort($bottom_performers, fn($a, $b) => $b['change_count'] <=> $a['change_count']);
$bottom_performers = array_slice($bottom_performers, 0, 5);

$players_by_status = [];
foreach ($status_display as $status => $label) {
    $filtered = array_filter($players, fn($p) => $p['status'] === $status);
    usort($filtered, fn($a, $b) => strcmp($a['name'], $b['name']));
    $players_by_status[$status] = [
        'count' => count($filtered),
        'players' => array_slice($filtered, 0, 5)
    ];
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>แดชบอร์ด | FM25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../includes/navbar.php'; ?>

        <!-- เพิ่มในส่วน <main> -->
        <main class="flex-1 px-10 py-8 overflow-y-auto">
            <h1 class="text-2xl font-semibold flex items-center gap-2 mb-6">
                <i data-lucide="layout-dashboard" class="w-5 h-5 text-gray-600"></i>
                แดชบอร์ด
            </h1>

            <p class="text-gray-500 mb-6">ยินดีต้อนรับเข้าสู่ระบบจัดการทีม FC25!</p>

            <!-- Filter / Stats Inline -->
            <div class="text-sm text-gray-900 mb-6 flex flex-wrap gap-x-4 gap-y-2 font-semibold">
                <span class="text-gray-900">นักเตะทั้งหมดในทีม : <?= $total_players ?> คน</span>
                <span class="text-sky-500">อยู่กับทีม : <?= $status_counts['no'] ?> คน</span>
                <span class="text-red-500">เตรียมขาย : <?= $status_counts['sell'] ?> คน</span>
                <span class="text-pink-500">เตรียมปล่อยยืมตัว : <?= $status_counts['for_loan'] ?> คน</span>
                <span class="text-blue-700">กำลังปล่อยยืมตัว : <?= $status_counts['on_loan'] ?> คน</span>
                <span class="text-green-700">กำลังยืมตัว : <?= $status_counts['in_loan'] ?> คน</span>
            </div>

            <!-- Row 1: จำนวนรวม -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                <div class="bg-white border rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">นักเตะทั้งหมด</p>
                    <p class="text-2xl font-semibold mt-1"><?= $total_players ?> คน</p>
                </div>
                <div class="bg-white border rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">อาคาเดมี่</p>
                    <p class="text-2xl font-semibold mt-1"><?= $total_academy ?> คน</p>
                </div>
                <div class="bg-white border rounded-xl shadow-sm p-5 text-center">
                    <p class="text-sm text-gray-500">อดีตนักเตะ</p>
                    <p class="text-2xl font-semibold mt-1"><?= $total_former ?> คน</p>
                </div>
            </div>

            <!-- Row 2: ดีเด่น / ยอดแย่ -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <!-- ดีเด่น -->
                <div class="bg-white border rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500 mb-3">นักเตะดีเด่น (Top 5)</p>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <?php foreach ($top_performers as $p): ?>
                            <li class="flex justify-between">
                                <span><?= htmlspecialchars($p['name']) ?></span>
                                <span class="font-bold text-green-600">+<?= $p['change_count'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- ยอดแย่ -->
                <div class="bg-white border rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500 mb-3">นักเตะยอดแย่ (Bottom 5)</p>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <?php foreach ($bottom_performers as $p): ?>
                            <li class="flex justify-between">
                                <span><?= htmlspecialchars($p['name']) ?></span>
                                <span class="font-bold text-red-600">-<?= $p['change_count'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Row 3: แยกตามบทบาท -->
            <h2 class="text-lg font-semibold mb-4 mt-8">แยกตามบทบาท (Role)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                <?php foreach ($roles as $role): ?>
                    <div class="bg-white border rounded-xl shadow-sm p-5">
                        <p class="text-sm text-gray-500 mb-1">
                            <?= $role_display[$role] ?>: <span class="font-bold"><?= $players_by_role[$role]['count'] ?></span> คน
                        </p>
                        <ul class="text-sm text-gray-800 mt-2 space-y-1">
                            <?php foreach ($players_by_role[$role]['players'] as $p): ?>
                                <li class="flex justify-between">
                                    <span><?= htmlspecialchars($p['name']) ?></span>
                                    <span class="text-xs text-gray-500">#<?= $p['jersey_number'] ?? '-' ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php if ($players_by_role[$role]['count'] > 5): ?>
                                <li class="text-xs text-blue-500 mt-1">+ <?= $players_by_role[$role]['count'] - 5 ?> คน</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Row 4: แยกตามสถานะ -->
            <h2 class="text-lg font-semibold mb-4 mt-10">แยกตามสถานะ</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                <?php foreach ($status_display as $status => $label): ?>
                    <div class="bg-white border rounded-xl shadow-sm p-5">
                        <p class="text-sm text-gray-500 mb-1"><?= $label ?>: <span class="font-bold"><?= $players_by_status[$status]['count'] ?></span> คน</p>
                        <ul class="text-sm text-gray-800 mt-2 space-y-1">
                            <?php foreach ($players_by_status[$status]['players'] as $p): ?>
                                <li class="flex justify-between">
                                    <span><?= htmlspecialchars($p['name']) ?></span>
                                    <span class="text-xs text-gray-500">#<?= $p['jersey_number'] ?? '-' ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php if ($players_by_status[$status]['count'] > 5): ?>
                                <li class="text-xs text-blue-500 mt-1">+ <?= $players_by_status[$status]['count'] - 5 ?> คน</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

        </main>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>