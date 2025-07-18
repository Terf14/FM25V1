<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ดึงปีที่มีอยู่ในฐานข้อมูล
$year_query = "SELECT DISTINCT year FROM stats ORDER BY year DESC";
$year_stmt = $conn->prepare($year_query);
$year_stmt->execute();
$years = $year_stmt->fetchAll(PDO::FETCH_COLUMN);

$year = $_GET['year'] ?? ($years[0] ?? null); // ใช้ปีล่าสุดถ้าไม่มีค่า

if ($year) {
    // ดึงข้อมูลนักเตะ
    $query = "SELECT p.name, p.position, s.ovr, s.rating, s.matches, s.goals, s.assists, 
                     s.clean_sheets, s.yellow_cards, s.red_cards, s.injured, s.age
              FROM stats s
              JOIN players p ON s.player_id = p.player_id
              WHERE s.year = :year
              ORDER BY s.rating DESC, s.goals DESC"; // เรียงตาม rating และ goals
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แยกตำแหน่งและคำนวณค่าเฉลี่ย
    $positions = ['st', 'cf', 'lw', 'rw', 'lm', 'rm', 'cam', 'cm', 'cdm', 'cb', 'lb', 'rb', 'gk'];
    $position_stats = [];

    foreach ($positions as $pos) {
        $position_stats[$pos] = [
            'count' => 0, 'ovr' => 0, 'rating' => 0, 'goals' => 0, 
            'assists' => 0, 'clean_sheets' => 0, 'injured' => 0
        ];
    }

    foreach ($players as $player) {
        $pos = strtolower($player['position']);
        if (isset($position_stats[$pos])) {
            $position_stats[$pos]['count']++;
            $position_stats[$pos]['ovr'] += $player['ovr'];
            $position_stats[$pos]['rating'] += $player['rating'];
            $position_stats[$pos]['goals'] += $player['goals'];
            $position_stats[$pos]['assists'] += $player['assists'];
            $position_stats[$pos]['clean_sheets'] += $player['clean_sheets'];
            $position_stats[$pos]['injured'] += $player['injured'];
        }
    }

    // คำนวณค่าเฉลี่ย
    foreach ($position_stats as $pos => $stats) {
        if ($stats['count'] > 0) {
            foreach ($stats as $key => $value) {
                if ($key != 'count') {
                    $position_stats[$pos][$key] = $value / $stats['count'];
                }
            }
        }
    }
}

// ฟังก์ชั่นให้คำแนะนำ
function getAdvice($player, $position_stats) {
    $pos = strtolower($player['position']);
    if (!isset($position_stats[$pos])) return 'ข้อมูลไม่เพียงพอ';

    $avg = $position_stats[$pos];
    $ovr = $player['ovr'];
    $rating = $player['rating'];
    $goals = $player['goals'];
    $assists = $player['assists'];
    $clean_sheets = $player['clean_sheets'];
    $injured = $player['injured'];
    $age = $player['age'];

    // นักเตะอายุ 32+ ถ้าฟอร์มตก ให้ขาย
    if ($age >= 32 && $rating < $avg['rating'] * 0.9) return 'ขาย';

    // นักเตะอายุ 30+ ที่ยังเล่นดี เป็น Mentor ได้
    if ($age >= 30 && $rating >= $avg['rating']) return 'เก็บไว้ (กัปตัน)';

    // ✅ เงื่อนไขกองหน้า (ST, CF)
    if (in_array($pos, ['st', 'cf'])) {
        if ($goals >= $avg['goals'] && $rating >= $avg['rating']) return 'เก็บไว้';
        if ($age <= 23 && $rating < $avg['rating']) return 'ปล่อยยืม';
        return 'ขาย';
    }

    // ✅ เงื่อนไขปีก (LW, RW, LM, RM)
    if (in_array($pos, ['lw', 'rw', 'lm', 'rm'])) {
        if ($assists >= $avg['assists'] && $rating >= $avg['rating']) return 'เก็บไว้';
        if ($age <= 24 && $rating < $avg['rating']) return 'ปล่อยยืม';
        return 'ขาย';
    }

    // ✅ เงื่อนไขกองกลาง (CM, CDM, CAM)
    if (in_array($pos, ['cm', 'cdm', 'cam'])) {
        if ($assists >= $avg['assists'] / 2 && $rating >= $avg['rating']) return 'เก็บไว้';
        if ($age <= 25 && $rating < $avg['rating']) return 'ปล่อยยืม';
        return 'ขาย';
    }

    // ✅ เงื่อนไขกองหลัง (CB, LB, RB)
    if (in_array($pos, ['cb', 'lb', 'rb'])) {
        if ($clean_sheets >= $avg['clean_sheets'] && $rating >= $avg['rating']) return 'เก็บไว้';
        if ($age <= 26 && $rating < $avg['rating']) return 'ปล่อยยืม';
        return 'ขาย';
    }

    // ✅ เงื่อนไขผู้รักษาประตู (GK)
    if ($pos == 'gk') {
        if ($clean_sheets >= $avg['clean_sheets'] && $rating >= $avg['rating']) return 'เก็บไว้';
        if ($age <= 26 && $rating < $avg['rating']) return 'ปล่อยยืม';
        return 'ขาย';
    }

    return 'เก็บไว้';
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คำแนะนำเกี่ยวกับนักเตะ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6 ">
    <?php include '../includes/navbar.php'; ?>

    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md mt-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">คำแนะนำเกี่ยวกับนักเตะ</h2>

        <!-- Dropdown เลือกปี -->
        <form method="GET" class="mb-4">
            <select name="year" onchange="this.form.submit()" class="p-2 border rounded">
                <?php foreach ($years as $y): ?>
                    <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>>ปี <?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">ชื่อ</th>
                    <th class="border px-4 py-2">ตำแหน่ง</th>
                    <th class="border px-4 py-2">OVR</th>
                    <th class="border px-4 py-2">อายุ</th>
                    <th class="border px-4 py-2">คำแนะนำ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td class="border px-4 py-2"><?= htmlspecialchars($player['name']); ?></td>
                        <td class="border px-4 py-2"><?= strtoupper($player['position']); ?></td>
                        <td class="border px-4 py-2"><?= $player['ovr']; ?></td>
                        <td class="border px-4 py-2"><?= $player['age']; ?></td>
                        <td class="border px-4 py-2"><?= getAdvice($player, $position_stats); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
