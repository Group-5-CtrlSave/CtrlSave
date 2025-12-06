<?php
session_start();
include("../../assets/shared/connect.php");
date_default_timezone_set('Asia/Manila');

//to check if user is not (null) login
if (!isset($_SESSION['userID'])) {
    header("Location: ../../login.php");
    exit;
}

$userID = $_SESSION['userID'];

// to check the right achievement for the current user
if (isset($_POST['claimAchievementID'])) {
    //intval means to prevent sql error as this is number
    $achievementID = intval($_POST['claimAchievementID']);
    executeQuery("
        UPDATE tbl_userachievements
        SET isClaimed = 1, date = NOW()
        WHERE achievementID = $achievementID AND userID = $userID
    ");
    echo "success";
    exit;
}

//get all achievements ID from tbl_achievements
$allAchievements = executeQuery("SELECT achievementID FROM tbl_achievements");
//loop for all of the achievements (array)
while ($ach = mysqli_fetch_assoc($allAchievements)) {

    //to check if the achievements in tbl_achievement has the user, if not yet it inserts with isClaimed '0' 
    $check = executeQuery("
        SELECT * FROM tbl_userachievements
        WHERE achievementID = '{$ach['achievementID']}' AND userID = '$userID'
    ");
    if (mysqli_num_rows($check) == 0) {
        executeQuery("
            INSERT INTO tbl_userachievements(achievementID, userID, isClaimed, date)
            VALUES('{$ach['achievementID']}', '$userID', 0, NULL)
        ");
    }
}

//get the userLVL 
$getLvl = executeQuery("SELECT lvl FROM tbl_userlvl WHERE userID = '$userID'");
$lvlRow = mysqli_fetch_assoc($getLvl);
$userLevel = $lvlRow ? intval($lvlRow['lvl']) : 0;

//BADGE QUERIES
//get the total count of income transactions
$incomeCountQuery = executeQuery("
    SELECT COUNT(*) AS total
    FROM tbl_income
    WHERE userID = '$userID'
");
$incomeCount = mysqli_fetch_assoc($incomeCountQuery)['total'] ?? 0;

//get the total count of completed saving goals
$savingCountQuery = executeQuery("
    SELECT COUNT(*) AS total
    FROM tbl_savinggoals
    WHERE userID = '$userID' AND status = 'completed'
");
$savingCompleted = mysqli_fetch_assoc($savingCountQuery)['total'] ?? 0;

// count total daily challenges assigned today
$totalDailyChallengesTodayQuery = executeQuery("
    SELECT COUNT(*) AS total
    FROM tbl_userchallenges uc
    JOIN tbl_challenges c ON uc.challengeID = c.challengeID
    WHERE uc.userID = '$userID'
      AND c.type = 'daily'
      AND DATE(uc.assignedDate) = CURDATE()
");
$totalDailyChallengesToday = mysqli_fetch_assoc($totalDailyChallengesTodayQuery)['total'] ?? 0;

// count claimed daily challenges today
$completedDailyChallengesTodayQuery = executeQuery("
    SELECT COUNT(*) AS total
    FROM tbl_userchallenges uc
    JOIN tbl_challenges c ON uc.challengeID = c.challengeID
    WHERE uc.userID = '$userID'
      AND c.type = 'daily'
      AND DATE(uc.assignedDate) = CURDATE()
      AND uc.status = 'claimed'
");
$completedDailyChallengesToday = mysqli_fetch_assoc($completedDailyChallengesTodayQuery)['total'] ?? 0;

//GET ALL ACHIEVEMENTS & USER CLAIM STATUS
$query = "
    SELECT 
        a.achievementID,
        a.achievementName,
        a.achievementDescription,
        a.icon,
        a.lvl,
        a.type,
        ua.isClaimed,
        ua.date AS claimDate
    FROM tbl_achievements a
    LEFT JOIN tbl_userachievements ua
        ON a.achievementID = ua.achievementID AND ua.userID = '$userID'
    ORDER BY 
        CASE 
            WHEN a.type = 'title' THEN 1
            WHEN a.type = 'badge' THEN 2
        END ASC,
        CASE 
            WHEN a.type = 'title' THEN a.lvl
            WHEN a.type = 'badge' THEN a.achievementID
        END ASC
";

$result = executeQuery($query);
$achievements = [];

//loop for title and badges
while ($row = mysqli_fetch_assoc($result)) {

    if ($row['type'] == 'title') {
        // title unlock rule (if greater or equal to title's level then, it's true)
        $row['canClaim'] = ($userLevel >= intval($row['lvl']));
    } else if ($row['type'] == 'badge') {
        //specific rule per badge
        switch ($row['achievementID']) {

            case 5: // Newcomer (always true as already login)
                $row['canClaim'] = true;
                break;

            case 6: // Income Badge (if equal or greater to 20 income transactions)
                $row['canClaim'] = ($incomeCount >= 20);
                break;

            case 7: // Saving Badge (if completed 1 savinggoal)
                $row['canClaim'] = ($savingCompleted >= 1);
                break;

            case 8: // Challenge Pro
                // can claim if all daily challenges for today are completed
                $row['canClaim'] = ($totalDailyChallengesToday > 0 && $completedDailyChallengesToday >= $totalDailyChallengesToday);
                break;

            default:
                $row['canClaim'] = false;
                break;
        }
    }

    $achievements[$row['type']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #44B87D;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: "Roboto", sans-serif;
            margin: 0;
            padding: 0;
            max-width: 480px;
            margin: 0 auto;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .page-title {
            position: sticky;
            top: 72px;
            left: 0;
            right: 0;
            max-width: 480px;
            margin: 0 auto;
            z-index: 50;
            background-color: #44B87D;
            padding: 20px 30px;
            color: #FFFFFF;
            font-family: "Poppins", sans-serif;
            font-weight: 605;
            font-size: 25px;
        }

        .achievement-card {
            background: #F0F1F6;
            border-radius: 20px;
            padding: 20px;
            margin: 15px 20px;
            border: 2px solid #F6D25B;
        }

        .achievement-card h3 {
            margin-bottom: 15px;
            color: #000000;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            font-size: 20px;
            text-align: center;
        }

        .achievement-container {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .achievement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            background: #ffffff;
            padding: 15px;
            border-radius: 20px;
            color: #44B87D;
            min-height: 70px;
            font-family: "Roboto", sans-serif;
        }

        .achievement-info {
            flex: 1;
        }

        .achievement-title {
            font-weight: bold;
            font-size: 16px;
            color: #44B87D;
            margin-bottom: 3px;
        }

        .achievement-description {
            font-size: 12px;
            color: #888;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .achievement-item button.claim-btn {
            border: none;
            background-color: #ffc107;
            color: black;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: transform 0.1s ease, background-color 0.3s ease;
            font-size: 14px;
            min-width: 80px;
        }

        .achievement-item button.locked {
            background: #F0F1F6;
            color: #666;
            border: 2px solid #aaa;
            font-weight: bold;
            border-radius: 20px;
            font-size: 14px;
            cursor: not-allowed;
            min-width: 80px;
        }

        .achievement-item button.claim-btn:active {
            transform: scale(0.95);
        }

        .achievement-item button.claimed {
            background-color: transparent;
            color: #FFC727;
            border-radius: 20px;
            font-weight: 600;
            border: 2px solid #FFC727;
            cursor: default;
            font-size: 14px;
            min-width: 80px;
        }

        .emoji-icon {
            font-size: 16px;
            margin-right: 10px;
        }

        .achievement-icon {
            width: 40px;
            height: 40px;
            margin-right: 5px;
            object-fit: contain;
        }

        .level-badge {
            display: inline-block;
            width: 60px;
            height: auto;
            object-fit: contain;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        /* Scrollbar styling */
        .achievement-container::-webkit-scrollbar {
            width: 6px;
        }

        .achievement-container::-webkit-scrollbar-track {
            background: #e0e0e0;
            border-radius: 10px;
        }

        .achievement-container::-webkit-scrollbar-thumb {
            background: #FFC727;
            border-radius: 10px;
        }
    </style>

</head>

<body>
    <!-- Navigation Bar -->
    <?php include("../../assets/shared/navigationBar.php") ?>
    <!-- Sidebar content-->
    <?php include("../../assets/shared/sideBar.php") ?>

    <!-- Page Title -->
    <div class="page-title">
        Achievements
    </div>

    <!-- Titles Section -->
    <?php if (isset($achievements['title'])): ?>
        <section style="padding-bottom: 20px;">
            <div class="achievement-card">
                <h3>Titles</h3>
                <div class="achievement-container">
                    <?php foreach ($achievements['title'] as $ach): ?>
                        <div class="achievement-item" data-achievement-id="<?= $ach['achievementID'] ?>">
                            <div class="achievement-info">
                                <div class="achievement-title"><?= ($ach['achievementName']) ?></div>
                                <div class="achievement-description">
                                    <?= ($ach['achievementDescription']) ?>
                                    <?php if (!empty($ach['icon'])): ?>
                                        <img class="level-badge" src="../../assets/img/challenge/<?= $ach['icon'] ?>">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($ach['isClaimed'] == '1'): ?>
                                <button class="claimed" disabled>Claimed</button>
                            <?php elseif (!$ach['canClaim']): ?>
                                <button class="locked" disabled>Locked</button>
                            <?php else: ?>
                                <button class="claim-btn">Claim</button>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>


        <!-- Badges Section -->
        <?php if (isset($achievements['badge'])): ?>
            <div class="achievement-card">
                <h3>Badges</h3>
                <div class="achievement-container">
                    <?php foreach ($achievements['badge'] as $ach): ?>

                        <div class="achievement-item" data-achievement-id="<?= $ach['achievementID'] ?>">
                            <?php if (!empty($ach['icon'])): ?>
                                <img src="../../assets/img/challenge/<?= $ach['icon'] ?>" class="achievement-icon">
                            <?php endif; ?>
                            <div class="achievement-info">
                                <div class="achievement-title"><?= ($ach['achievementName']) ?></div>
                                <div class="achievement-description"><?= htmlspecialchars($ach['achievementDescription']) ?>
                                </div>
                            </div>

                            <?php if ($ach['isClaimed'] == '1'): ?>
                                <button class="claimed" disabled>Claimed</button>
                            <?php elseif (!$ach['canClaim']): ?>
                                <button class="locked" disabled>Locked</button>
                            <?php else: ?>
                                <button class="claim-btn">Claim</button>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>


    <!-- Reward Modal -->
    <div class="modal fade" id="rewardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4" style="background-color: #44B87D; border-radius: 20px;">

                <!-- Title -->
                <h5 class="fw-bold mb-2" style="color: #fff; font-size: 25px; font-family: Poppins, sans-serif;">
                    Achievement Unlocked!
                </h5>

                <!-- Image -->
                <img id="achievementImage" class="mb-2 mx-auto d-block"
                    style="width: 150px; height: 150px; object-fit: contain; border-radius: 10px;">


                <!-- Text -->
                <p id="achievementName" class="text-white mb-0"
                    style="font-size: 16px; font-family: Roboto, sans-serif;">
                </p>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    <script>
        function launchConfetti() {
            const duration = 1500;
            const end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 },
                    colors: ['#FFC727', '#44B87D', '#ffffff']
                });
                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 },
                    colors: ['#FFC727', '#44B87D', '#ffffff']
                });

                if (Date.now() < end) requestAnimationFrame(frame);
            })();
        }

        document.querySelectorAll('.claim-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.closest('.achievement-item');
                const achievementID = item.dataset.achievementId;
                const name = item.querySelector('.achievement-title').textContent;
                const icon = item.querySelector('img')?.src;

                document.getElementById('achievementName').textContent = name;
                document.getElementById('achievementImage').src = icon;

                launchConfetti();
                new bootstrap.Modal(document.getElementById('rewardModal')).show();

                btn.textContent = 'Claimed';
                btn.classList.remove('claim-btn');
                btn.classList.add('claimed');
                btn.disabled = true;

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'claimAchievementID=' + achievementID
                });
            });
        });
    </script>

</body>

</html>