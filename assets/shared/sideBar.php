<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userID'])) {
  header('Location: ../../pages/login&signup/login.php');
  exit();
}

$userID = $_SESSION['userID'];

// Get user info
$userQuery = "SELECT userName, email FROM tbl_users WHERE userID = '$userID' LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// Get XP & Level
$levelQuery = "SELECT exp, lvl FROM tbl_userLvl WHERE userID = '$userID' LIMIT 1";
$levelResult = mysqli_query($conn, $levelQuery);
$level = mysqli_fetch_assoc($levelResult);

$currentXP = $level['exp'] ?? 0;
$currentLevel = $level['lvl'] ?? 1;
$xpNeeded = 100; 
$progressPercent = min(100, ($currentXP / $xpNeeded) * 100);

// Get Achievements (badges)
$achievementsQuery = "
  SELECT a.achievementName, a.icon
  FROM tbl_userAchievements ua
  JOIN tbl_achievements a ON ua.achievementID = a.achievementID
  WHERE ua.userID = '$userID' AND ua.isClaimed = 1
";
$achievementsResult = mysqli_query($conn, $achievementsQuery);
?>

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
  <div class="offcanvas-body p-0" style="background-color: #44B87D;">

    <!-- User Info Section -->
    <div class="p-4 border-bottom bg-light shadow-sm">
      <div class="d-flex align-items-center gap-3 mb-2">
        <!-- Initials -->
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold"
             style="width: 48px; height: 48px; font-size: 1.25rem;">
          <?php echo strtoupper(substr($user['userName'], 0, 2)); ?>
        </div>

        <!-- User Details -->
        <div>
          <p class="text-dark fw-semibold mb-0"><?php echo htmlspecialchars($user['userName']); ?></p>
          <p class="text-muted small mb-0"><?php echo htmlspecialchars($user['email']); ?></p>

          <!-- Badges -->
          <?php if (mysqli_num_rows($achievementsResult) > 0): ?>
            <?php while ($badge = mysqli_fetch_assoc($achievementsResult)): ?>
              <img src="../../assets/img/challenge/<?php echo htmlspecialchars($badge['icon']); ?>"
                   alt="<?php echo htmlspecialchars($badge['achievementName']); ?>"
                   title="<?php echo htmlspecialchars($badge['achievementName']); ?>"
                   style="width: 16px; height: 16px; object-fit: contain;">
            <?php endwhile; ?>
          <?php else: ?>
            <p class="small text-muted mb-0">...</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Level and XP -->
      <p class="small text-dark fw-medium mb-1">Level <?php echo $currentLevel; ?></p>
      <div class="w-100 bg-secondary bg-opacity-25 rounded-pill" style="height: 8px;">
        <div class="bg-warning rounded-pill" style="width: <?php echo $progressPercent; ?>%; height: 8px;"></div>
      </div>
      <p class="text-muted mt-1 mb-3 small">
        <?php echo $currentXP; ?> XP / <?php echo $xpNeeded; ?> XP
      </p>

      <form method="post" action="../../pages/login&signup/logout.php">
        <button type="submit" class="w-100 btn btn-sm btn-danger fw-medium">Logout</button>
      </form>
    </div>

    <!-- Sidebar Links -->
    <ul class="list-unstyled m-0 p-3">
     <?php
      $links = [
        ["Home", "home/home.php", "Home_SB.png"],
        ["Income & Expense", "income&expenses/income&expenses.php", "I&E_SB.png"],
        ["Savings", "savings/savingDetail.php", "Savings_SB.png"],
        ["Cointrol", "cointrol/cointrol.php", "Cointrol_SB.png"],
        ["Saving Strategies", "savingstrategies/savingstrat.php", "SavingStrat_SB.png"],
        ["History", "history/history.php", "History_SB.png"],
        ["Notifications", "notification/notification.php", "Notif_SB.png"],
        ["Challenge", "challenge/challengeMain.php", "Challenge_SB.png"],
        ["Profile", "profile/profile.php", "Profile_SB.png"],
        ["Settings", "settings/settings.php", "Settings_SB.png"]
      ];

      foreach ($links as [$name, $href, $icon]) {
        echo "
        <li>
          <a href='../../pages/$href'
            class='d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded'
            style='display:block; transition: background-color 0.25s, color 0.25s;'
            onmouseover=\"this.style.backgroundColor='#44B87D'; this.style.color='white';\"
            onmouseout=\"this.style.backgroundColor=''; this.style.color='white';\">
            <img src='../../assets/img/shared/sidebar/$icon' class='me-2' style='width:30px;height:30px;' />
            <span>$name</span>
          </a>
        </li>";
      }
      ?>
    </ul>
  </div>
</div>
