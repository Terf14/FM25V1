<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$userId = $_SESSION['user_id'];
$player = null;
$suggestion = '';

function getAgeGroup($age)
{
    if ($age <= 20) return 'ดาวรุ่ง';
    if ($age <= 25) return 'นักเตะทั่วไป';
    if ($age <= 30) return 'นักเตะที่มีประสบการณ์';
    return 'นักเตะอาวุโส';
}

function analyzeDiscipline($yellow, $red)
{
    $note = "<p class='text-sm text-gray-700 mt-4'><strong>🔶 วิเคราะห์วินัย :</strong> ";
    if ($red >= 2) {
        $note .= "นักเตะได้รับใบแดงหลายครั้ง อาจสร้างความเสี่ยงในเกมใหญ่";
    } elseif ($yellow >= 5) {
        $note .= "มีใบเหลืองสะสมสูง ต้องระวังการฟาวล์มากเกินไป";
    } elseif ($yellow <= 2 && $red == 0) {
        $note .= "มีวินัยที่ดีเยี่ยม ไม่ค่อยโดนใบเหลืองหรือแดง";
    } else {
        $note .= "พอใช้ได้ แต่ยังควรระวังการเข้าปะทะ";
    }
    $note .= "</p>";
    return $note;
}

function analyzeRating($rating)
{
    $note = "<p class='text-sm text-gray-700'><strong>⭐ วิเคราะห์ Rating :</strong> ";
    if ($rating >= 7.5) {
        $note .= "โดดเด่นเกินระดับลีก ฟอร์มคงเส้นคงวา";
    } elseif ($rating >= 6.5) {
        $note .= "ถือว่าพอใช้ได้ มีความเสถียรในการเล่น อาจเหมาะเป็นตัวสำรองที่เชื่อถือได้";
    } elseif ($rating > 0) {
        $note .= "ยังต้องพัฒนาอีกมากในหลายด้าน";
    } else {
        $note .= "ไม่มีข้อมูล Rating";
    }
    $note .= "</p>";
    return $note;
}

function randomAdvice($action)
{
    $advices = [
        'keep' => [
            "ฟอร์มดีขนาดนี้ เก็บไว้ให้ไว!",
            "ยังไงก็ต้องเก็บไว้ใช้งาน!",
            "อยู่กับทีมต่อยาว ๆ",
            "เป็นกำลังหลักในอนาคตแน่นอน ควรเก็บไว้"
        ],
        'loan' => [
            "ควรปล่อยยืมเพื่อหาประสบการณ์",
            "ปล่อยยืมให้ไว เพื่อพัฒนาตัวเอง",
            "ยังไม่พร้อมเต็มที่ ควรปล่อยยืม",
            "การปล่อยยืมน่าจะเป็นทางเลือกที่เหมาะสม"
        ],
        'sell' => [
            "ควรขายเพื่อเปิดทางให้คนอื่น",
            "ผลงานไม่ตอบโจทย์ ควรปล่อยออกจากทีม",
            "ขายให้ไว ก่อนมูลค่าจะลด",
            "ไม่อยู่ในแผนระยะยาว ควรพิจารณาขาย"
        ]
    ];
    return $advices[$action][array_rand($advices[$action])];
}

function generateSummary($position, $age, $matches, $goals, $assists, $cleansheets, $yellow, $red, $rating)
{
    $position = strtolower($position);
    $age_group = getAgeGroup($age);
    $goal_rate = $matches > 0 ? $goals / $matches : 0;
    $assist_rate = $matches > 0 ? $assists / $matches : 0;
    $cs_rate = $matches > 0 ? $cleansheets / $matches : 0;
    $contrib = $goals + $assists;

    $summary = "";
    $action = "";
    $table = "";
    $title = "<p class='text-lg font-semibold mb-2 mt-4'>🧠 วิเคราะห์เชิงเทคนิค</p>";

    $table .= "<table class='w-full text-sm border border-gray-300 mb-4'><thead class='bg-gray-100'><tr>";
    $table .= "<th class='border px-3 py-1'>เงื่อนไข</th>";
    $table .= "<th class='border px-3 py-1'>เกณฑ์</th>";
    $table .= "<th class='border px-3 py-1'>ผลลัพธ์</th>";
    $table .= "</tr></thead><tbody>";

    switch ($position) {
        case 'st':
        case 'cf':
            $table .= "<tr><td class='border px-3 py-1'>ยิงเฉลี่ย ≥ 0.4</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($goal_rate >= 0.4 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($goal_rate >= 0.4 ? 'ดี' : 'ต่ำ') . "</td></tr>";
            $table .= "<tr><td class='border px-3 py-1'>แอสซิสต์เฉลี่ย ≥ 0.2</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($assist_rate >= 0.2 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($assist_rate >= 0.2 ? 'พอใช้' : 'ต่ำ') . "</td></tr>";
            $table .= "<tr><td class='border px-3 py-1'>รวมยิง+แอส ≥ 10</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($contrib >= 10 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($contrib >= 10 ? 'ดี' : 'น้อยไป') . "</td></tr>";

            if ($goal_rate >= 0.5 || $contrib >= 12) {
                $summary = "กองหน้าฟอร์มดี ยิงประตูได้ต่อเนื่อง";
                $action = 'keep';
            } elseif ($goal_rate >= 0.3 || $contrib >= 8) {
                $summary = "ถือว่าพอใช้ได้ อาจเหมาะสำหรับบทบาทตัวสำรองหรือเปลี่ยนเกม";
                $action = 'keep';
            } elseif ($goal_rate < 0.2 && $matches > 15) {
                $summary = "มีโอกาสเยอะแต่ผลงานต่ำ อาจต้องพิจารณา";
                $action = 'sell';
            } else {
                $summary = "ยังพอมีแวว แต่ต้องพัฒนาอีกมาก";
                $action = 'loan';
            }
            break;

        case 'rw':
        case 'lw':
            $table .= "<tr><td class='border px-3 py-1'>แอสซิสต์เฉลี่ย ≥ 0.3</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($assist_rate >= 0.3 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($assist_rate >= 0.3 ? 'เด่น' : 'ยังน้อย') . "</td></tr>";
            if ($assist_rate >= 0.4 || $contrib >= 10) {
                $summary = "ปีกสร้างโอกาสได้อย่างต่อเนื่อง มีประโยชน์กับเกมรุก";
                $action = 'keep';
            } elseif ($assist_rate >= 0.2 || $contrib >= 6) {
                $summary = "ฟอร์มพอใช้ได้ มีศักยภาพเป็นตัวสำรองในบางเกม";
                $action = 'keep';
            } elseif ($assist_rate < 0.15 && $matches > 20) {
                $summary = "แอสซิสต์ต่ำเกินไป อาจไม่เหมาะกับระบบ";
                $action = 'sell';
            } else {
                $summary = "ต้องพัฒนาเกมรุกและการจ่ายบอลให้มากขึ้น";
                $action = 'loan';
            }
            break;

        case 'gk':
            $table .= "<tr><td class='border px-3 py-1'>คลีนชีตเฉลี่ย ≥ 0.3</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($cs_rate >= 0.3 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($cs_rate >= 0.3 ? 'ดีมาก' : 'ยังน้อย') . "</td></tr>";
            $table .= "<tr><td class='border px-3 py-1'>Rating ≥ 7.0</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($rating >= 7.0 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($rating >= 7.0 ? 'ยอดเยี่ยม' : 'พอใช้/ต้องปรับปรุง') . "</td></tr>";

            if ($cs_rate >= 0.4 && $rating >= 7.2) {
                $summary = "ผู้รักษาประตูฟอร์มดีมาก มีความเสถียรสูง และช่วยทีมได้บ่อยครั้ง";
                $action = 'keep';
            } elseif ($cs_rate >= 0.25 && $rating >= 6.5) {
                $summary = "ผลงานพอใช้ มีศักยภาพเป็นมือสองหรือพัฒนาต่อได้";
                $action = 'keep';
            } elseif ($rating < 6.2 && $matches >= 10) {
                $summary = "ฟอร์มไม่น่าพอใจ อาจพิจารณาปล่อย";
                $action = 'sell';
            } else {
                $summary = "ยังต้องพัฒนาอีกมาก ควรพิจารณาปล่อยยืม";
                $action = 'loan';
            }
            break;

        case 'cb':
            $table .= "<tr><td class='border px-3 py-1'>คลีนชีตเฉลี่ย ≥ 0.5</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($cs_rate >= 0.5 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($cs_rate >= 0.5 ? 'ยอดเยี่ยม' : 'ยังไม่ดีพอ') . "</td></tr>";
            $table .= "<tr><td class='border px-3 py-1'>Rating ≥ 7.0</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($rating >= 7.0 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($rating >= 7.0 ? 'ยอดเยี่ยม' : 'ต้องปรับปรุง') . "</td></tr>";
            if ($cs_rate >= 0.5 && $rating >= 7.0) {
                $summary = "กองหลังตัวหลักที่ยอดเยี่ยม ป้องกันได้อย่างดี";
                $action = 'keep';
            } elseif ($cs_rate >= 0.35 && $rating >= 6.5) {
                $summary = "กองหลังมีผลงานพอใช้ สามารถพัฒนาให้ดียิ่งขึ้น";
                $action = 'keep';
            } elseif ($rating < 6.0 && $matches >= 10) {
                $summary = "ฟอร์มกองหลังไม่ค่อยดี ควรปรับปรุงหรือพิจารณาเปลี่ยนตัว";
                $action = 'sell';
            } else {
                $summary = "ยังมีศักยภาพ แต่ต้องพัฒนาเพิ่มเติม";
                $action = 'loan';
            }
            break;

        case 'lb':
        case 'rb':
            $table .= "<tr><td class='border px-3 py-1'>คลีนชีตเฉลี่ย ≥ 0.4</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($cs_rate >= 0.4 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($cs_rate >= 0.4 ? 'ดีมาก' : 'ยังน้อย') . "</td></tr>";
            $table .= "<tr><td class='border px-3 py-1'>Rating ≥ 6.8</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($rating >= 6.8 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($rating >= 6.8 ? 'น่าพอใจ' : 'ต้องปรับปรุง') . "</td></tr>";
            if ($cs_rate >= 0.4 && $rating >= 6.8) {
                $summary = "นักรับแนวข้างมีผลงานดี ช่วยทีมได้ในเกมรับ";
                $action = 'keep';
            } elseif ($cs_rate >= 0.3 && $rating >= 6.5) {
                $summary = "มีศักยภาพพอใช้ แต่ยังต้องพัฒนาเพิ่มเติม";
                $action = 'keep';
            } elseif ($rating < 6.0 && $matches >= 10) {
                $summary = "ฟอร์มนักรับแนวข้างต้องปรับปรุงอย่างมาก";
                $action = 'sell';
            } else {
                $summary = "ยังมีโอกาสพัฒนา แต่ควรฝึกฝนเพิ่มเติม";
                $action = 'loan';
            }
            break;

        case 'lm':
        case 'rm':
        case 'cm':
        case 'cam':
        case 'cdm':
            $table .= "<tr><td class='border px-3 py-1'>ผลงานรวม (ยิง+แอส) ≥ 5</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($contrib >= 5 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($contrib >= 5 ? 'ดี' : 'ต้องพัฒนา') . "</td></tr>";
            $table .= "<tr><td class='border px-3 py-1'>Rating ≥ 6.5</td>"
                . "<td class='border px-3 py-1 text-center'>" . ($rating >= 6.5 ? '✅' : '❌') . "</td>"
                . "<td class='border px-3 py-1'>" . ($rating >= 6.5 ? 'พอใช้' : 'ต่ำ') . "</td></tr>";
            if (($contrib >= 8 || $assist_rate >= 0.3) && $rating >= 7.0) {
                $summary = "นักเตะกลางมีความสามารถสร้างสรรค์และควบคุมเกมได้ดี";
                $action = 'keep';
            } elseif (($contrib >= 5 || $assist_rate >= 0.25) && $rating >= 6.5) {
                $summary = "นักเตะกลางมีผลงานพอใช้ และสามารถพัฒนาให้ดีขึ้นได้";
                $action = 'keep';
            } elseif ($rating < 6.0 && $matches >= 10) {
                $summary = "ฟอร์มนักเตะกลางต้องปรับปรุงอย่างมาก";
                $action = 'sell';
            } else {
                $summary = "ยังมีศักยภาพ แต่ต้องพัฒนาเพิ่มเติม";
                $action = 'loan';
            }
            break;

        default:
            $summary = "ไม่สามารถวิเคราะห์ตำแหน่งนี้ได้";
            break;
    }

    $table .= "</tbody></table>";
    $advice = $action ? randomAdvice($action) : "";
    $discipline = analyzeDiscipline($yellow, $red);
    $rating_result = analyzeRating($rating);

    return $title . $table
        . "<p class='text-lg font-bold text-green-700 mt-2'>" . $summary . "</p>"
        . $discipline
        . $rating_result
        . ($advice ? "<p class='text-base mt-2 text-blue-600'>📌 $advice</p>" : "");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_id = intval($_POST['player_id']);
    $stmt = $conn->prepare("SELECT * FROM players WHERE player_id = ? AND user_id = ?");
    $stmt->execute([$player_id, $userId]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($player) {
        $age = intval($_POST['age']);
        $matches = intval($_POST['matches']);
        $goals = intval($_POST['goals']);
        $assists = intval($_POST['assists']);
        $cleansheets = intval($_POST['cleansheets']);
        $yellow = intval($_POST['yellow']);
        $red = intval($_POST['red']);
        $rating = floatval($_POST['rating']);

        $suggestion = generateSummary($player['position'], $age, $matches, $goals, $assists, $cleansheets, $yellow, $red, $rating);
    }
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>AI วิเคราะห์นักเตะ | FM25 Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/logo/fm25_logo_2.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <?php include '../includes/navbar.php'; ?>

        <main class="flex-1 px-10 py-8 overflow-y-auto">
            <h1 class="text-2xl font-semibold flex items-center gap-2 mb-6">
                <i data-lucide="bot" class="w-5 h-5 text-gray-600"></i>
                AI วิเคราะห์นักเตะ
            </h1>

            <div class="bg-white border rounded-xl shadow-sm p-6 max-w-3xl">
                <form method="POST" class="space-y-4">
                    <label class="block font-medium mb-1">เลือกนักเตะ:</label>
                    <select name="player_id" required class="w-full border rounded px-3 py-2">
                        <option value="">-- เลือก --</option>
                        <?php
                        $stmt = $conn->prepare("SELECT player_id, name, position FROM players WHERE user_id = ? ORDER BY name");
                        $stmt->execute([$userId]);
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = (isset($_POST['player_id']) && $_POST['player_id'] == $row['player_id']) ? 'selected' : '';
                            echo "<option value='{$row['player_id']}' $selected>{$row['name']} ({$row['position']})</option>";
                        }
                        ?>
                    </select>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm mb-1">อายุ</label>
                            <input name="age" type="number" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">จำนวนครั้งที่ลงเล่น</label>
                            <input name="matches" type="number" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['matches'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">ยิงประตู</label>
                            <input name="goals" type="number" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['goals'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">แอสซิสต์</label>
                            <input name="assists" type="number" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['assists'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">คลีนชีต</label>
                            <input name="cleansheets" type="number" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['cleansheets'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">ใบเหลือง</label>
                            <input name="yellow" type="number" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['yellow'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">ใบแดง</label>
                            <input name="red" type="number" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['red'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">เรตติ้งเฉลี่ย</label>
                            <input name="rating" type="number" step="0.01" required class="w-full border rounded px-3 py-2"
                                value="<?= htmlspecialchars($_POST['rating'] ?? '') ?>">
                        </div>
                    </div>

                    <button type="submit"
                        class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                        🔍 วิเคราะห์
                    </button>
                </form>

                <?php if (!empty($suggestion)): ?>
                    <div class="mt-6 border-t pt-4">
                        <?= $suggestion ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>