<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$user_id = $_SESSION['user_id'];

// ถ้ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $season = trim($_POST['season']);
    $description = trim($_POST['description']);

    try {
        $sql = "INSERT INTO team_trophies (user_id, title, season, description) 
                VALUES (:user_id, :title, :season, :description)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'title' => $title,
            'season' => $season,
            'description' => $description
        ]);

        // บันทึกเสร็จ กลับไปหน้า trophies.php
        header("Location: trophies.php");
        exit;
    } catch (PDOException $e) {
        $error_message = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มความสำเร็จ | FM25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="flex h-screen">
        <?php include '../includes/navbar.php'; ?>

        <main class="flex-1 px-10 py-8 overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold flex items-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5 text-gray-600"></i>
                    เพิ่มความสำเร็จ
                </h1>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-6 bg-white p-8 rounded-lg shadow-md">
                <div>
                    <label class="block mb-2 font-medium">ชื่อแชมป์ *</label>
                    <select name="title" required class="w-full border border-gray-300 rounded-md p-2">
                        <option value="">-- เลือกชื่อถ้วย --</option>

                        <optgroup label="ลีกในอังกฤษ">
                            <option value="Premier League">Premier League</option>
                            <option value="Championship">Championship</option>
                            <option value="League One">League One</option>
                            <option value="League Two">League Two</option>
                        </optgroup>

                        <optgroup label="ถ้วยในอังกฤษ">
                            <option value="FA Cup">FA Cup</option>
                            <option value="EFL Cup (Carabao Cup)">EFL Cup (Carabao Cup)</option>
                            <option value="Community Shield">Community Shield</option>
                            <option value="BSM Trophy">BSM Trophy</option>
                        </optgroup>

                        <optgroup label="ถ้วยยุโรป">
                            <option value="UEFA Champions League (UCL)">UEFA Champions League (UCL)</option>
                            <option value="UEFA Europa League (UEL)">UEFA Europa League (UEL)</option>
                            <option value="UEFA Europa Conference League (UECL)">UEFA Europa Conference League (UECL)</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 font-medium">ฤดูกาล</label>
                    <input type="text" name="season" class="w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block mb-2 font-medium">รายละเอียดเพิ่มเติม P : W :  D :  L :  F :  A : </label>
                    <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-md p-2"></textarea>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="trophies.php" class="text-gray-600 hover:text-gray-900">ย้อนกลับ</a>
                    <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition">
                        บันทึก
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>