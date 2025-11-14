<?php
session_start();
include_once '../../assets/shared/connect.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['userID'])) {
  if (!headers_sent()) {
    header('Location: ../../pages/login&signup/login.php');
    exit();
  }
}

$userID = $_SESSION['userID'] ?? 0;

$userQuery = "SELECT firstName, lastName, userName, email, profilePicture 
              FROM tbl_users 
              WHERE userID = '$userID' 
              LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult) ?? [
  'firstName' => 'User',
  'lastName' => '',
  'userName' => '',
  'email' => '',
  'profilePicture' => 'profile1.png'
];

// ✅ Combine full name
$fullName = trim($user['firstName'] . ' ' . $user['lastName']);

// ✅ Fetch level and XP info
$levelQuery = "SELECT exp, lvl FROM tbl_userLvl WHERE userID = '$userID' LIMIT 1";
$levelResult = mysqli_query($conn, $levelQuery);
$level = mysqli_fetch_assoc($levelResult) ?? ['exp' => 0, 'lvl' => 1];

$currentXP = $level['exp'];
$currentLevel = $level['lvl'];
$xpNeeded = 100;
$progressPercent = min(100, ($currentXP / $xpNeeded) * 100);

// ✅ Fetch achievements
$achievementsQuery = "
  SELECT a.achievementName, a.icon
  FROM tbl_userAchievements ua
  JOIN tbl_achievements a ON ua.achievementID = a.achievementID
  WHERE ua.userID = '$userID' AND ua.isClaimed = 1
";
$achievementsResult = mysqli_query($conn, $achievementsQuery);

// ✅ Determine profile picture src with cache busting
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave | Profile</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/profile.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

</head>

<body>
  <?php include("../../assets/shared/navigationBar.php") ?>
  <?php include("../../assets/shared/sideBar.php") ?>

  <!-- Profile Content -->
  <div class="profile-container d-flex justify-content-center align-items-center w-100 flex-column">
    
<button 
  class="btn rounded-pill mb-3 align-self-end"
  style="background-color:#F6D25B; border-color:#F6D25B; color:#000;"
  onclick="window.location.href='achievements.php'">
    <i class="bi bi-trophy"></i> Claim Achievements
</button>
    
    <div class="profile-card text-center">
      <h4 class="profile-name"><?= htmlspecialchars($fullName); ?></h4>

      <!-- Profile Picture -->
      <img src="<?= $src; ?>" 
           alt="Avatar" 
           class="profile-img">

      <p class="profile-username">@<?= htmlspecialchars($user['userName']); ?></p>

      <div class="profile-section">
        <p class="profile-label">Achievements:</p>

        <?php if (mysqli_num_rows($achievementsResult) > 0): ?>
          <?php while ($badge = mysqli_fetch_assoc($achievementsResult)): ?>
            <img src="../../assets/img/challenge/<?= htmlspecialchars($badge['icon']); ?>" 
                 alt="<?= htmlspecialchars($badge['achievementName']); ?>" 
                 title="<?= htmlspecialchars($badge['achievementName']); ?>" 
                 class="badge-icon">
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-muted small">No achievements yet</p>
        <?php endif; ?>
      </div>

      <div class="profile-section">
        <p class="profile-label">Email:</p>
        <p class="profile-answer"><?= htmlspecialchars($user['email']); ?></p>
      </div>

      <!-- Level and XP -->
      <div class="profile-section">
        <p class="profile-label">Level: <?= $currentLevel; ?></p>
        <div class="progress" style="height: 15px;">
          <div class="progress-bar bg-warning" role="progressbar"
               style="width: <?= $progressPercent; ?>%;" 
               aria-valuenow="<?= $progressPercent; ?>" 
               aria-valuemin="0" 
               aria-valuemax="100">
          </div>
        </div>
        <p class="small text-muted mt-1"><?= $currentXP; ?> XP / <?= $xpNeeded; ?> XP</p>
      </div>
    </div>

    <!-- Buttons -->
    <div class="button-wrapper w-100 d-flex flex-column align-items-center" style="margin-top: 2px;">
      
      <button class="btn edit-btn mb-2" style="margin-top: 1px;" onclick="window.location.href='editProfile.php'">Edit Profile</button>
      
      <form method="post" action="../../pages/login&signup/login.php" class="d-flex justify-content-center">
      <button type="submit" class="btn logout-btn">Logout</button>
      </form>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>