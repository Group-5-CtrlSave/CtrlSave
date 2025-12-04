<?php
session_start();
include("../../assets/shared/connect.php");
include("process/challengeProgress.php");

// ensure we have a user id
$userID = isset($_SESSION['userID']) ? intval($_SESSION['userID']) : 0;
?>

<?php
include("process/reassignChallenges.php");

if ($userID) {
    // Daily
    expireDailyChallenges($conn, $userID);
    reassignFailedDailyChallenges($conn, $userID);
    resetDailyChallengeSetIfCompleted($conn, $userID);

    // Weekly
    expireWeeklyChallenges($conn, $userID);
    reassignFailedWeeklyChallenges($conn, $userID);
    resetWeeklyChallengeSetIfCompleted($conn, $userID);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Challenges</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            background-color: #44B87D;
            overflow: hidden;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
            align-items: center;
            font-family: "Poppins", sans-serif;
            font-size: 15px;
        }

        .tab-buttons button {
            padding: 8px 16px;
            border: 2px solid #FFC727;
            border-radius: 20px;
            background: #fff;
            color: #000;
            font-weight: bold;
            cursor: pointer;
        }

        .tab-buttons button.active {
            background: #FFC727;
            color: black;
            border-color: #FFC727;
        }

        .tab-content {
            display: none;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
        }

        .challenge-card {
            background: #F0F1F6;
            border-radius: 20px;
            padding: 20px;
            margin: -10px 0;
            height: 250px;
            border: 2px solid #F6D25B;
        }

        .challenge-card h3 {
            margin-bottom: 20px;
            color: #000;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            font-size: 20px;
        }

        .challenge-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            background: #fff;
            padding: 10px 15px;
            border-radius: 20px;
            color: #44B87D;
            height: 100px;
            font-family: "Roboto", sans-serif;
        }

        .challenge-item button.claim-btn {
            border: none;
            background: #ffc107;
            color: black;
            font-weight: 500;
            text-align: center;
            border-radius: 20px;
            cursor: pointer;
            width: 70px;
            height: 30px;
            font-size: 14px;
            border-radius: 10px;
        }

        .challenge-item button.in-progress {
            background: #bfbfbfff;
            color: black;
            font-weight: 500;
            border: 2px solid #ffc107;
            cursor: default;
            font-size: 14px;
            width: 70px;
            border-radius: 10px;
        }

        .challenge-item button.failed-btn {
            background: #bfbfbfff;
            color: #ff4d4d;
            font-weight: 500;
            text-align: center;
            border: 2px solid #ffc107;
            cursor: not-allowed;
            width: 70px;
            height: 30px;
            font-size: 14px;
            border-radius: 10px;
            opacity: 0.8;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }

            10% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            70% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <?php include("../../assets/shared/navigationBar.php") ?>
    <!-- Sidebar content-->
    <?php include("../../assets/shared/sideBar.php") ?>

    <!-- READ CHALLENGES QUERY -->
    <?php

    $userID = intval($userID);

    $dailyquery = "
    SELECT c.challengeID, c.challengeName, c.exp, c.type, c.quantity,
           u.userChallengeID, u.userID, u.assignedDate, u.status, u.claimedAt, u.completedAt
    FROM tbl_challenges c
    INNER JOIN tbl_userchallenges u ON c.challengeID = u.challengeID
    WHERE u.userID = {$userID}
      AND c.type = 'Daily'
      AND u.status IN ('in progress', 'completed') 
";
    $dailyresult = executeQuery($dailyquery);

    $weeklyquery = "
    SELECT c.challengeID, c.challengeName, c.exp, c.type, c.quantity,
           u.userChallengeID, u.userID, u.assignedDate, u.status, u.claimedAt, u.completedAt
    FROM tbl_challenges c
    INNER JOIN tbl_userchallenges u ON c.challengeID = u.challengeID
    WHERE u.userID = {$userID}
      AND c.type = 'Weekly'
      AND u.status IN ('in progress', 'completed')
";
    $weeklyresult = executeQuery($weeklyquery);
    ?>

    <!-- Challenge Notification Toast -->
    <div id="challengeToast" style="display:none; position:fixed; top:20px; left:50%; transform:translateX(-50%);
        background-color:#FFC727; color:black; padding:15px 5px; border-radius:20px;
        font-family:Poppins, sans-serif; font-weight:600; font-size:13px; text-align:center; z-index:9999;
        animation: fadeInOut 3s ease forwards;">
    </div>

    <!-- Categories -->
    <header>
        <div class="tab-buttons d-flex justify-content-center align-items-center">
            <button class="tab-btn active" data-tab="daily">Daily & Weekly</button>
            <button class="tab-btn" data-tab="saving">Saving Challenge</button>
        </div>
    </header>

    <!-- Daily & Weekly Section -->
    <section id="daily" class="tab-content active">
        <!-- Daily Saving Challenge Card -->
        <div class="challenge-card">
            <h3>Daily Challenges</h3>
            <div class="row" style="overflow-x: scroll; height: 160px;">
                <?php
                if ($dailyresult && mysqli_num_rows($dailyresult) > 0) {
                    while ($dailyrows = mysqli_fetch_assoc($dailyresult)) {
                        $dailyname = htmlspecialchars($dailyrows['challengeName']);
                        $dailystatus = $dailyrows['status'];
                        $assigned = $dailyrows['assignedDate'];

                        // compute time left safely
                        $timeLeft = "Expired";
                        if (!empty($assigned)) {
                            try {
                                $now = new DateTime();
                                $start = new DateTime($assigned);
                                $diff = $now->diff($start);
                                $hoursPassed = ($diff->days * 24) + $diff->h;
                                $minutesPassed = $diff->i;
                                $hoursLeft = 23 - $hoursPassed;
                                $minutesLeft = 59 - $minutesPassed;
                                if ($hoursLeft >= 0) {
                                    $timeLeft = "⏳ {$hoursLeft}h {$minutesLeft}m left";
                                }
                            } catch (Exception $e) {
                                $timeLeft = "⏳ unknown";
                            }
                        }
                        ?>
                        <div class="challenge-item">
                            <div>
                                <div><?php echo $dailyname; ?></div>
                                <small style="font-size:12px;color:#666;"><?php echo $timeLeft; ?></small>
                            </div>

                            <?php
                            if ($dailystatus === "claimed") {
                                echo "<button class='in-progress' disabled>Claimed</button>";
                            } elseif ($dailystatus === "completed") {
                                echo "<button class='claim-btn' data-challengeid='{$dailyrows['userChallengeID']}'>Claim</button>";
                            } elseif ($dailystatus === "failed") {
                                echo "<button class='failed-btn' disabled>Failed</button>";
                            } else {
                                echo "<button class='in-progress'>In Progress</button>";
                            }
                            ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

        <!-- Weekly Saving Challenge Card -->
        <div class="challenge-card mt-4">
            <h3>Weekly Challenges</h3>
            <div class="row" style="overflow-x: scroll; height: 160px;">
                <?php
                if ($weeklyresult && mysqli_num_rows($weeklyresult) > 0) {
                    while ($weeklyrows = mysqli_fetch_assoc($weeklyresult)) {
                        $weeklyname = htmlspecialchars($weeklyrows['challengeName']);
                        $weeklystatus = $weeklyrows['status'];
                        $assigned = $weeklyrows['assignedDate'];

                        // compute weekly time left safely
                        $timeLeft = "Expired";
                        if (!empty($assigned)) {
                            try {
                                $now = new DateTime();
                                $start = new DateTime($assigned);
                                $diff = $now->diff($start);
                                $totalHoursPassed = ($diff->days * 24) + $diff->h;
                                $hoursLeftTotal = (7 * 24) - $totalHoursPassed;
                                if ($hoursLeftTotal >= 0) {
                                    $daysLeft = floor($hoursLeftTotal / 24);
                                    $hoursLeft = $hoursLeftTotal % 24;
                                    $timeLeft = "⏳ {$daysLeft}d {$hoursLeft}h left";
                                }
                            } catch (Exception $e) {
                                $timeLeft = "⏳ unknown";
                            }
                        }

                        $challengeId = intval($weeklyrows['challengeID']);
                        $progress = getChallengeProgress($userID, $challengeId, $conn);

                        $progressText = "";
                        if ($progress && $weeklystatus !== "claimed") {
                            $current = $progress['current'];
                            $total = $progress['total'];

                            if ($current > $total)
                                $current = $total;

                            $progressText = "Progress: {$current}/{$total}";
                        }



                        ?>

                        <div class="challenge-item">
                            <div>
                                <div><?php echo $weeklyname; ?></div>
                                <small style="font-size:12px; color:#666;"><?php echo $timeLeft; ?></small>
                                <?php if ($progressText !== ""): ?>
                                    <small
                                        style="font-size:9px; color:#555; display:block; margin-top:1px;"><?php echo $progressText; ?></small>
                                <?php endif; ?>
                            </div>

                            <?php
                            if ($weeklystatus === "claimed") {
                                echo "<button class='in-progress' disabled>Claimed</button>";
                            } elseif ($weeklystatus === "completed") {
                                echo "<button class='claim-btn' data-challengeid='{$weeklyrows['userChallengeID']}'>Claim</button>";
                            } elseif ($weeklystatus === "failed") {
                                echo "<button class='failed-btn' disabled>Failed</button>";
                            } else {
                                echo "<button class='in-progress'>In Progress</button>";
                            }
                            ?>
                        </div>

                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Saving Challenge Section -->

    <?php
    $userID = intval($_SESSION['userID']);
    $progress = [];

    // Fetch saved progress from DB
    $sql = "SELECT itemIndex, amount FROM tbl_savingchallenge_progress WHERE userID = $userID";
    $res = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($res)) {
        $progress[intval($row['itemIndex'])] = intval($row['amount']);
    }
    ?>

    <section id="saving" class="tab-content">
        <div style="text-align:center; margin-bottom:20px;">
            <div style="position:relative; display:inline-block;">
                <img src="../../assets/img/challenge/alkansya.png" alt="Alkansya" style="width:100px;">
                <div id="totalSaved"
                    style="position:absolute; top:70%; left:48%; transform:translate(-50%,-50%); font-weight:bold; font-size:16px; color:#333;">
                    ₱0/100
                </div>
            </div>
            <div style="font-weight:bold; margin-top:10px; color:#fff;">Lvl. 1</div>
            <p style="font-weight:bold; margin-top:10px; color:#fff;">Add an amount to the saving jar</p>
        </div>

        <div id="savingGrid"
            style="display:grid; grid-template-columns:repeat(5,1fr); gap:10px; text-align:center; max-width:300px; margin:auto;">
        </div>

        <div style="text-align:center; margin-top:40px;">
            <button id="addSelectedBtn" style="background:#FFC727; border:none; padding:10px 30px;
           border-radius:20px; font-weight:bold; font-size:14px;
           color:black; cursor:pointer;">
                Add
            </button>
        </div>
    </section>

    <!-- Reward modals (kept intact) -->
    <div class="modal fade" id="dailyRewardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4" style="background-color:#44B87D; border-radius:20px;">
                <h5 class="fw-bold" style="color:#fff; font-weight:bold; font-size:30px;">Daily Reward</h5>
                <div class="position-relative d-inline-block my-3"><img src="../../assets/img/challenge/greenXp.png"
                        alt="Green XP Bottle" style="width:150px;"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="weeklyRewardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4" style="background-color:#44B87D; border-radius:20px;">
                <h5 class="fw-bold" style="color:#fff; font-weight:bold; font-size:30px;">Weekly Reward</h5>
                <div class="position-relative d-inline-block my-3"><img src="../../assets/img/challenge/yellowXp.png"
                        alt="Yellow XP Bottle" style="width:150px;"></div>
            </div>
        </div>
    </div>

    <script>
        const buttons = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');
        buttons.forEach(btn => btn.addEventListener('click', () => {
            buttons.forEach(b => b.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        }));

        const claimButtons = document.querySelectorAll('.claim-btn');
        claimButtons.forEach(button => {
            button.addEventListener('click', () => {
                const challengeItem = button.closest('.challenge-item');
                const challengeID = button.dataset.challengeid;
                fetch("process/claimChallengeBE.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `challengeID=${challengeID}`
                })
                    .then(res => res.text())
                    .then(response => {
                        if (response === "success") {
                            button.classList.remove("claim-btn");
                            button.classList.add("in-progress");
                            button.textContent = "Claimed";
                            const parentCard = button.closest('.challenge-card');
                            const title = parentCard.querySelector("h3").textContent;
                            const isDaily = title.toLowerCase().includes("daily");
                            const modalId = isDaily ? "dailyRewardModal" : "weeklyRewardModal";
                            new bootstrap.Modal(document.getElementById(modalId)).show();
                        } else {
                            alert("Error claiming challenge.");
                        }
                    });
            });
        });

    //    SAVING CHALLENGE SYSTEM (DATABASE BASED)

        const savingAmounts = [5, 10, 5, 10, 5, 20, 5, 10, 20, 10];
        const targetAmount = 100;

        let totalSaved = 0;
        let selectedToAdd = [];

        // Inject DB progress into JavaScript
        let dbProgress = {};
        <?php
        foreach ($progress as $index => $amount) {
            echo "dbProgress[$index] = $amount;";
        }
        ?>

        // Convert DB data → UI state
        let savedState = {
            clicked: Array(savingAmounts.length).fill(false),
            total: 0
        };

        // Mark coins that belong to this user
        for (let idx in dbProgress) {
            idx = parseInt(idx);
            savedState.clicked[idx] = true;
            savedState.total += savingAmounts[idx];
        }

        totalSaved = savedState.total;

        const grid = document.getElementById('savingGrid');
        const totalDisplay = document.getElementById('totalSaved');
        const addSelectedBtn = document.getElementById('addSelectedBtn');

        // Update display
        totalDisplay.textContent = `₱${totalSaved}/${targetAmount}`;
        
        // BUILD GRID BUTTONS

        savingAmounts.forEach((amount, index) => {
            const btn = document.createElement('button');
            btn.textContent = amount;
            btn.style.border = '2px solid #FFC727';
            btn.style.borderRadius = '50%';
            btn.style.width = '50px';
            btn.style.height = '50px';
            btn.style.fontWeight = 'bold';
            btn.style.cursor = 'pointer';

            if (savedState.clicked[index]) {
                // Already saved in DB → lock
                btn.style.backgroundColor = '#FFC727';
                btn.style.color = 'black';
                btn.disabled = true;
            } else {
                // Allow selection
                btn.addEventListener('click', () => {
                    if (selectedToAdd.includes(index)) {
                        selectedToAdd = selectedToAdd.filter(i => i !== index);
                        btn.style.backgroundColor = 'white';
                    } else {
                        selectedToAdd.push(index);
                        btn.style.backgroundColor = '#FFE58A';
                    }
                });
            }

            grid.appendChild(btn);
        });

        // PROCESS ADD BUTTON

        addSelectedBtn.addEventListener('click', () => {
            if (selectedToAdd.length === 0) {
                alert("Please select at least one amount before clicking ADD.");
                return;
            }

            selectedToAdd.forEach(idx => {
                const amt = savingAmounts[idx];

                savedState.clicked[idx] = true;
                totalSaved += amt;

                const btn = grid.children[idx];
                btn.style.backgroundColor = '#FFC727';
                btn.style.color = 'black';
                btn.disabled = true;

                // Save to DB
                fetch("process/saveSavingProgress.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `index=${idx}&amount=${amt}`
                });
            });

            selectedToAdd = [];
            savedState.total = totalSaved;

            totalDisplay.textContent = `₱${totalSaved}/${targetAmount}`;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
    <script>
        function launchConfetti() {
            const duration = 1500;
            const end = Date.now() + duration;
            (function frame() {
                confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#FFC727', '#44B87D', '#ffffff'] });
                confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#FFC727', '#44B87D', '#ffffff'] });
                if (Date.now() < end) requestAnimationFrame(frame);
            })();
        }
        const dailyModal = document.getElementById('dailyRewardModal');
        const weeklyModal = document.getElementById('weeklyRewardModal');
        if (dailyModal) dailyModal.addEventListener('shown.bs.modal', launchConfetti);
        if (weeklyModal) weeklyModal.addEventListener('shown.bs.modal', launchConfetti);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>