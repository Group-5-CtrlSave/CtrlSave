<?php
session_start();
include("../../assets/shared/connect.php");

if (!isset($_SESSION['userID'])) {
  header("Location: ../../pages/login&signup/login.php");
  exit;
}

$userID = $_SESSION['userID'] ?? 0;

$userQuery = "SELECT firstName, lastName, userName, email, profilePicture, displayedBadges 
              FROM tbl_users 
              WHERE userID = '$userID' 
              LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult) ?? [
  'firstName' => 'User',
  'lastName' => '',
  'userName' => '',
  'email' => '',
  'profilePicture' => 'profile1.png',
  'displayedBadges' => ''
];

$fullName = trim($user['firstName'] . ' ' . $user['lastName']);

// Insert user level if not exists
$levelQuery = "SELECT exp, lvl FROM tbl_userlvl WHERE userID = '$userID' LIMIT 1";
$levelResult = mysqli_query($conn, $levelQuery);
$level = mysqli_fetch_assoc($levelResult) ?? null;
if (!$level) {
  mysqli_query($conn, "INSERT INTO tbl_userlvl (userID, exp, lvl) VALUES ('$userID', 0, 1)");
  $level = ['exp' => 0, 'lvl' => 1];
}

$currentXP = $level['exp'];
$currentLevel = $level['lvl'];
$xpNeeded = 100;
$progressPercent = min(100, ($currentXP / $xpNeeded) * 100);

// Fetch all claimed achievements for map and auto-select
$achievementsQuery = "
  SELECT a.icon, a.achievementName, a.type, ua.date
  FROM tbl_userachievements ua
  JOIN tbl_achievements a ON ua.achievementID = a.achievementID
  WHERE ua.userID = '$userID' AND ua.isClaimed = 1
  ORDER BY ua.date ASC
";
$achievementsResult = mysqli_query($conn, $achievementsQuery);
$achievements = [];
$achievementMap = [];
while ($row = mysqli_fetch_assoc($achievementsResult)) {
  $achievements[] = $row;
  $achievementMap[$row['icon']] = $row['achievementName'];
}

// Get displayed badges
$displayedBadgesArray = explode(',', $user['displayedBadges'] ?? '');
$displayedBadgesArray = array_filter(array_map('trim', $displayedBadgesArray));

if (empty($displayedBadgesArray) && !empty($achievements)) {
  $titles = array_filter($achievements, function ($a) {
    return $a['type'] === 'title'; });
  $badges = array_filter($achievements, function ($a) {
    return $a['type'] === 'badge'; });
  $selected = [];
  if (!empty($titles)) {
    $selected[] = reset($titles)['icon'];
  }
  $badges = array_values($badges);
  for ($i = 0; $i < 2 && $i < count($badges); $i++) {
    $selected[] = $badges[$i]['icon'];
  }
  $displayedBadgesArray = $selected;
}

$hasAchievements = !empty($displayedBadgesArray);

// Fetch income count
$incomeCountQuery = "SELECT COUNT(*) AS total FROM tbl_income WHERE userID = '$userID'";
$incomeCountResult = mysqli_query($conn, $incomeCountQuery);
$incomeCount = mysqli_fetch_assoc($incomeCountResult)['total'] ?? 0;

// Fetch completed savings
$savingCountQuery = "SELECT COUNT(*) AS total FROM tbl_savinggoals WHERE userID = '$userID' AND status = 'completed'";
$savingCountResult = mysqli_query($conn, $savingCountQuery);
$savingCompleted = mysqli_fetch_assoc($savingCountResult)['total'] ?? 0;

// Insert user achievements if not exists
$allAchievements = mysqli_query($conn, "SELECT achievementID FROM tbl_achievements");
while ($ach = mysqli_fetch_assoc($allAchievements)) {
  $check = mysqli_query($conn, "SELECT * FROM tbl_userachievements WHERE achievementID = '{$ach['achievementID']}' AND userID = '$userID'");
  if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO tbl_userachievements(achievementID, userID, isClaimed, date) VALUES('{$ach['achievementID']}', '$userID', 0, NULL)");
  }
}

// Fetch unclaimed achievements count (only claimable ones)
$unclaimedQuery = "
  SELECT a.achievementID, a.type, a.lvl
  FROM tbl_userachievements ua
  JOIN tbl_achievements a ON ua.achievementID = a.achievementID
  WHERE ua.userID = '$userID' AND ua.isClaimed = 0
";
$unclaimedResult = mysqli_query($conn, $unclaimedQuery);
$unclaimedCount = 0;
if ($unclaimedResult) {
  while ($row = mysqli_fetch_assoc($unclaimedResult)) {
    if ($row['type'] == 'title') {
      if ($currentLevel >= intval($row['lvl']))
        $unclaimedCount++;
    } else if ($row['type'] == 'badge') {
      switch ($row['achievementID']) {
        case 5:
          $unclaimedCount++;
          break;
        case 6:
          if ($incomeCount >= 20)
            $unclaimedCount++;
          break;
        case 7:
          if ($savingCompleted >= 1)
            $unclaimedCount++;
          break;
        default:
          break;
      }
    }
  }
}

// Determine profile picture src with cache busting
$profilePic = $user['profilePicture'];
if (!empty($profilePic)) {
  $imageServerPath = __DIR__ . '/../../assets/img/profile/' . $profilePic;
  $src = '../../assets/img/profile/' . htmlspecialchars($profilePic);
} else {
  $imageServerPath = __DIR__ . '/../../assets/img/profile/profile1.png';
  $src = '../../assets/img/profile/profile1.png';
}

if (file_exists($imageServerPath)) {
  $src .= '?v=' . filemtime($imageServerPath);
} else {
  $src .= '?v=' . time();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>CtrlSave | Profile</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/profile.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
  #sidebar,
  #sidebar * {
    font-family: 'Roboto', sans-serif !important;
  }
</style>


</head>

<body>
  <?php include("../../assets/shared/navigationBar.php") ?>
  <?php include("../../assets/shared/sideBar.php") ?>

  <!-- Profile Content -->
  <div class="profile-container d-flex justify-content-center align-items-center w-100 flex-column">

    <button class="btn rounded-pill mb-3 align-self-end me-2 position-relative"
      style="background-color:#F6D25B; border-color:#F6D25B; color:#000;"
      onclick="window.location.href='achievements.php'">
      <i class="bi bi-trophy"></i> Claim Achievements
      <?php if ($unclaimedCount > 0): ?>
        <i class="fa-solid fa-circle text-danger" style="font-size: 10px; position: absolute; top: 1px; left: 0px;"></i>
      <?php endif; ?>
    </button>

    <div class="profile-card text-center">
      <h4 class="profile-name"><?= htmlspecialchars($fullName); ?></h4>

      <!-- Profile Picture -->
      <img src="<?= $src; ?>" alt="Avatar" class="profile-img">

      <p class="profile-username" style="color: #44B87D;">@<?= htmlspecialchars($user['userName']); ?></p>

      <div class="profile-section">
        <p class="profile-label">Achievements:</p>

        <?php if ($hasAchievements): ?>
          <?php foreach ($displayedBadgesArray as $icon): ?>
            <img src="../../assets/img/challenge/<?= htmlspecialchars($icon); ?>"
              alt="<?= htmlspecialchars($achievementMap[$icon] ?? 'Achievement'); ?>"
              title="<?= htmlspecialchars($achievementMap[$icon] ?? 'Achievement'); ?>" class="badge-icon">
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted small">No achievements yet</p>
        <?php endif; ?>
      </div>

      <div class="profile-section">
        <p class="profile-label">Email:</p>
        <p class="profile-answer" style="color: black;">
          <?= htmlspecialchars($user['email']); ?>
        </p>

      </div>

      <!-- Level and XP -->
      <div class="profile-section">
        <p class="profile-label">Level: <?= $currentLevel; ?></p>
        <div class="progress" style="height: 15px;">
          <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $progressPercent; ?>%;"
            aria-valuenow="<?= $progressPercent; ?>" aria-valuemin="0" aria-valuemax="100">
          </div>
        </div>
        <p class="small text-muted mt-1"><?= $currentXP; ?> XP / <?= $xpNeeded; ?> XP</p>
      </div>
    </div>

    <!-- Buttons -->
    <div class="button-wrapper w-100 d-flex flex-column align-items-center" style="margin-top: 2px;">

      <button class="btn edit-btn mb-2" style="margin-top: 1px;" onclick="window.location.href='editProfile.php'">Edit
        Profile</button>

      <form method="post" action="../../pages/logout/logout.php" class="d-flex justify-content-center">
        <button type="submit" class="btn logout-btn">Logout</button>
      </form>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Push a fake history state so back swipe hits this first
    history.pushState(null, "", location.href);

    // Handle back swipe / back button
    window.addEventListener("popstate", function (event) {
      // Redirect to home page
      location.replace("/pages/home/home.php"); // use replace to avoid stacking history
    });
  </script>
</body>

</html>