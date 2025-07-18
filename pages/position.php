<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

// รับ `user_id` จาก session หรือการล็อกอิน
$user_id = $_SESSION['user_id']; // สมมติว่า session เก็บ `user_id`

// คำนวณจำนวนของนักเตะในแต่ละสถานะที่ผู้ใช้เพิ่ม
$status_counts = [
  'sell' => 0,
  'for_loan' => 0,
  'on_loan' => 0,
  'in_loan' => 0,
  'no' => 0
];

if ($conn instanceof PDO) {
  $statusQuery = $conn->prepare("SELECT status, COUNT(*) as count FROM Players WHERE user_id = ? GROUP BY status");
  $statusQuery->execute([$user_id]);
  $statusResults = $statusQuery->fetchAll(PDO::FETCH_ASSOC);
  foreach ($statusResults as $row) {
    if (isset($status_counts[$row['status']])) {
      $status_counts[$row['status']] = $row['count'];
    }
  }
} else {
  $statusQuery = mysqli_prepare($conn, "SELECT status, COUNT(*) as count FROM Players WHERE user_id = ? GROUP BY status");
  mysqli_stmt_bind_param($statusQuery, "i", $user_id);
  mysqli_stmt_execute($statusQuery);
  $statusResult = mysqli_stmt_get_result($statusQuery);
  while ($row = mysqli_fetch_assoc($statusResult)) {
    if (isset($status_counts[$row['status']])) {
      $status_counts[$row['status']] = $row['count'];
    }
  }
}

// คำนวณจำนวนนักเตะทั้งหมดที่ผู้ใช้เพิ่ม
$totalPlayers = 0;
if ($conn instanceof PDO) {
  $queryTotal = $conn->prepare("SELECT COUNT(*) as total FROM Players WHERE user_id = ?");
  $queryTotal->execute([$user_id]);
  $totalPlayers = $queryTotal->fetch(PDO::FETCH_ASSOC)['total'];
} else {
  $queryTotal = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM Players WHERE user_id = ?");
  mysqli_stmt_bind_param($queryTotal, "i", $user_id);
  mysqli_stmt_execute($queryTotal);
  $rowTotal = mysqli_stmt_get_result($queryTotal);
  $totalPlayers = mysqli_fetch_assoc($rowTotal)['total'];
}

// ดึงข้อมูลนักเตะทั้งหมดที่ผู้ใช้เพิ่ม และเรียงตาม role
$players = [];
$position_counts = [];

if ($conn instanceof PDO) {
  $query = $conn->prepare("
        SELECT * FROM Players 
        WHERE user_id = ? 
        ORDER BY FIELD(COALESCE(role, 'prospect'), 'crucial', 'important', 'rotation', 'sporadic', 'prospect'), name ASC
    ");
  $query->execute([$user_id]);
  $players = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
  $query = mysqli_prepare($conn, "
        SELECT * FROM Players 
        WHERE user_id = ? 
        ORDER BY FIELD(COALESCE(role, 'prospect'), 'crucial', 'important', 'rotation', 'sporadic', 'prospect'), name ASC
    ");
  mysqli_stmt_bind_param($query, "i", $user_id);
  mysqli_stmt_execute($query);
  $result = mysqli_stmt_get_result($query);
  while ($row = mysqli_fetch_assoc($result)) {
    $players[] = $row;
  }
}

// นับจำนวนของนักเตะในแต่ละตำแหน่ง
foreach ($players as $player) {
  $position_counts[$player['position']] = isset($position_counts[$player['position']]) ? $position_counts[$player['position']] + 1 : 1;
}

// ฟังก์ชันกำหนดสีปุ่มตามสถานะ
function getStatusClass($status)
{
  switch ($status) {
    case 'sell':
      return 'bg-red-500 hover:bg-red-600';
    case 'for_loan':
      return 'bg-pink-500 hover:bg-pink-600';
    case 'on_loan':
      return 'bg-blue-700 hover:bg-blue-800';
    case 'in_loan':
      return 'bg-green-500 hover:bg-green-600';
    default:
      return 'bg-sky-300 hover:bg-blue-300';
  }
}

// กำหนดตำแหน่งให้ตรงกับ Grid
$positions = [
  ['lw', 'st', 'rw'],
  ['', 'cf', ''],
  ['lm', 'cam', 'rm'],
  ['', 'cm', ''],
  ['', 'cdm', ''],
  ['lb', 'cb', 'rb'],
  ['', 'gk', '']
];

?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ตำแหน่ง | FM25 Manager</title>
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
      <h1 class="text-2xl font-semibold flex items-center gap-2 mb-6">
        <i data-lucide="file-badge" class="w-5 h-5 text-gray-600"></i>
        ตำแหน่ง
      </h1>

      <!-- Filter / Stats Inline -->
      <div class="text-sm text-gray-900 mb-6 flex flex-wrap gap-x-4 gap-y-2 font-semibold">
        <span class="text-gray-900">นักเตะทั้งหมดในทีม: <?php echo $totalPlayers; ?> คน</span>
        <span class="text-sky-500">อยู่กับทีม: <?php echo $status_counts['no'] ?? 0; ?> คน</span>
        <span class="text-red-500">เตรียมขาย: <?php echo $status_counts['sell'] ?? 0; ?> คน</span>
        <span class="text-pink-500">เตรียมปล่อยยืมตัว: <?php echo $status_counts['for_loan'] ?? 0; ?> คน</span>
        <span class="text-blue-700">กำลังปล่อยยืมตัว: <?php echo $status_counts['on_loan'] ?? 0; ?> คน</span>
        <span class="text-green-700">กำลังยืมตัว: <?php echo $status_counts['in_loan'] ?? 0; ?> คน</span>
      </div>

      <!-- Grid Layout -->
      <div class="grid grid-cols-3 gap-6">
        <?php foreach ($positions as $row): ?>
          <?php foreach ($row as $pos): ?>
            <div class="<?php echo $pos ? 'bg-gray-100' : 'bg-transparent'; ?> p-4 rounded-lg shadow-sm">
              <?php if ($pos): ?>
                <div class="text-center font-bold uppercase text-sm mb-2">
                  <?php
                  $count = $position_counts[$pos] ?? 0;
                  echo "$pos ($count)";
                  ?>
                </div>

                <!-- Players in this position -->
                <div class="flex flex-col gap-2">
                  <?php foreach ($players as $player): ?>
                    <?php if ($player['position'] === $pos): ?>
                      <a href="edit_player1.php?id=<?= $player['player_id']; ?>"
                        class="text-white font-semibold text-sm px-3 py-1 rounded text-center <?= getStatusClass($player['status']); ?>">
                        <?= $player['name']; ?>
                      </a>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <!-- Lucide Icons -->
  <script>
    lucide.createIcons();
  </script>
</body>

</html>