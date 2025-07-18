<?php
session_start();
include "../includes/db_config.php";
include "../includes/auth.php";

$user_id = $_SESSION['user_id'];

if ($conn instanceof PDO) {
    $query = $conn->prepare("
        SELECT * FROM Players 
        WHERE user_id = ? AND status != 'on_loan' 
        ORDER BY FIELD(COALESCE(role, 'prospect'), 'crucial', 'important', 'rotation', 'sporadic', 'prospect'), name ASC
    ");
    $query->execute([$user_id]);
    $players = $query->fetchAll(PDO::FETCH_ASSOC);
}

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
    <title>จัดการคะแนนนักเตะ | FM25 Manager</title>
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
        <main class="flex-1 overflow-y-auto px-10 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-semibold flex items-center gap-2">
                    <i data-lucide="chart-spline" class="w-5 h-5 text-gray-600"></i> จัดการคะแนนนักเตะ
                </h1>
                <button id="reset-score"
                    class="bg-red-500 text-white px-5 py-2 rounded-md hover:bg-red-600 transition text-sm font-semibold">
                    รีเซ็ตคะแนนทั้งหมด
                </button>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <?php foreach ($positions as $row): ?>
                    <?php foreach ($row as $pos): ?>
                        <div class="<?php echo $pos ? 'bg-gray-100' : 'bg-transparent'; ?> p-4 rounded-lg shadow-sm">
                            <?php if ($pos): ?>
                                <div class="text-center font-bold uppercase text-sm mb-3"><?php echo $pos; ?></div>

                                <div class="flex flex-col gap-2">
                                    <?php foreach ($players as $player): ?>
                                        <?php if ($player['position'] === $pos): ?>
                                            <div
                                                class="flex items-center justify-between bg-white border border-gray-200 shadow rounded-md px-4 py-3">
                                                <div class="text-gray-800 font-medium">
                                                    <?php echo $player['name']; ?>
                                                    <span id="change-<?php echo $player['player_id']; ?>"
                                                        class="ml-1 text-base font-bold text-gray-500"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button
                                                        class="decrease-score bg-gray-200 hover:bg-red-500 hover:text-white text-gray-700 font-bold w-8 h-8 rounded-md transition"
                                                        data-id="<?php echo $player['player_id']; ?>">−</button>

                                                    <span id="score-<?php echo $player['player_id']; ?>"
                                                        class="text-lg font-semibold text-gray-800 score"><?php echo $player['performance_score']; ?></span>

                                                    <button
                                                        class="increase-score bg-gray-200 hover:bg-green-500 hover:text-white text-gray-700 font-bold w-8 h-8 rounded-md transition"
                                                        data-id="<?php echo $player['player_id']; ?>">+</button>
                                                </div>
                                            </div>
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

    <script>
        let playerActions = JSON.parse(localStorage.getItem('playerActions')) || {};

        document.querySelectorAll('.increase-score').forEach(button => {
            button.addEventListener('click', function() {
                let playerId = this.getAttribute('data-id');
                updateScore(playerId, 'increase');
            });
        });

        document.querySelectorAll('.decrease-score').forEach(button => {
            button.addEventListener('click', function() {
                let playerId = this.getAttribute('data-id');
                updateScore(playerId, 'decrease');
            });
        });

        function updateScore(playerId, action) {
            fetch('update_score.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `player_id=${playerId}&action=${action}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`score-${playerId}`).innerText = data.new_score;

                        if (!playerActions[playerId]) {
                            playerActions[playerId] = {
                                count: 0,
                                lastAction: null
                            };
                        }

                        let player = playerActions[playerId];

                        if (player.lastAction === action) {
                            player.count++;
                        } else {
                            player.count = 1;
                        }

                        let colorClass = '';
                        let arrow = '';

                        if (action === 'increase') {
                            if (player.lastAction === 'decrease') {
                                player.count = 1;
                                colorClass = 'text-gray-500';
                                arrow = '↕';
                            } else {
                                colorClass = 'text-green-500';
                                arrow = '↑';
                            }
                        } else {
                            if (player.lastAction === 'increase') {
                                player.count = 1;
                                colorClass = 'text-gray-500';
                                arrow = '↕';
                            } else {
                                colorClass = 'text-red-500';
                                arrow = '↓';
                            }
                        }

                        player.lastAction = action;

                        document.getElementById(`change-${playerId}`).className = `ml-2 text-lg font-bold ${colorClass}`;
                        document.getElementById(`change-${playerId}`).innerText = `${arrow}${player.count}`;

                        localStorage.setItem('playerActions', JSON.stringify(playerActions));
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function loadSavedScores() {
            for (let playerId in playerActions) {
                let player = playerActions[playerId];
                let changeElement = document.getElementById(`change-${playerId}`);

                if (changeElement) {
                    let colorClass = '';
                    let arrow = '';

                    if (player.lastAction === 'increase') {
                        colorClass = 'text-green-500';
                        arrow = '↑';
                    } else if (player.lastAction === 'decrease') {
                        colorClass = 'text-red-500';
                        arrow = '↓';
                    }

                    changeElement.className = `ml-2 text-lg font-bold ${colorClass}`;
                    changeElement.innerText = `${arrow}${player.count}`;
                }
            }
        }

        document.getElementById('reset-score').addEventListener('click', function() {
            if (confirm('แน่ใจหรือไม่ว่าต้องการรีเซ็ตคะแนนทั้งหมด?')) {
                fetch('reset_score.php', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelectorAll('.score').forEach(el => el.innerText = '0');
                            document.querySelectorAll('[id^="change-"]').forEach(el => el.innerText = '');
                            playerActions = {};
                            localStorage.removeItem('playerActions');
                        } else {
                            alert('เกิดข้อผิดพลาด');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        document.addEventListener("DOMContentLoaded", loadSavedScores);

        lucide.createIcons();
    </script>
</body>

</html>