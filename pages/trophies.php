<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ความสำเร็จ | FM25 Manager</title>
    <link rel="icon" type="image/png" href="../img/logo/fm25_logo_2.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Itim&family=Kanit:wght@100;400;700&display=swap" rel="stylesheet">
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
                    <i data-lucide="award" class="w-5 h-5 text-gray-600"></i>
                    ความสำเร็จ
                </h1>
                <a href="add_trophies.php" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    เพิ่ม
                </a>
            </div>

            <?php
            try {
                $sql = "SELECT * FROM team_trophies WHERE user_id = :user_id ORDER BY trophy_id DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $trophies = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($trophies) > 0) {
                    echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
                    foreach ($trophies as $row) {
                        echo '<div class="bg-white shadow-md rounded-lg p-6 hover:shadow-lg transition">';
                        echo '<h2 class="text-xl font-bold mb-2">' . htmlspecialchars($row['title']) . '</h2>';
                        if (!empty($row['season'])) {
                            echo '<p class="text-sm text-gray-500 mb-1">ฤดูกาล : ' . htmlspecialchars($row['season']) . '</p>';
                        }
                        if (!empty($row['description'])) {
                            echo '<p class="mt-3 text-gray-700">' . nl2br(htmlspecialchars($row['description'])) . '</p>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="text-gray-500">ยังไม่มีรายการความสำเร็จ</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="text-red-500">เกิดข้อผิดพลาด: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>