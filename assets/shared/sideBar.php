<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
  session_start();
}

include_once '../../assets/shared/connect.php';

// Redirect to login if userID is not set
if (!isset($_SESSION['userID'])) {
  if (!headers_sent()) {
    header('Location: ../../pages/login&signup/login.php');
    exit();
  }
}

$userID = $_SESSION['userID'] ?? 0;

$stmtUser = $conn->prepare("SELECT userName, email, displayedBadges, profilePicture FROM tbl_users WHERE userID = ? LIMIT 1");
$stmtUser->bind_param("i", $userID);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$user = $userResult->fetch_assoc() ?? ['userName' => 'User', 'email' => '', 'displayedBadges' => '', 'profilePicture' => 'profile_Pic.png'];
$stmtUser->close();

$stmtLevel = $conn->prepare("SELECT exp, lvl FROM tbl_userlvl WHERE userID = ? LIMIT 1");
$stmtLevel->bind_param("i", $userID);
$stmtLevel->execute();
$levelResult = $stmtLevel->get_result();
$level = $levelResult->fetch_assoc() ?? ['exp' => 0, 'lvl' => 1];
$stmtLevel->close();

$currentXP = intval($level['exp']);
$currentLevel = intval($level['lvl']);

if ($currentLevel < 1) { 
    $currentLevel = 1; 
}

// XP needed 
$xpNeeded = 100 + (($currentLevel - 1) * 20);

// Prevent overflow display
$progressPercent = min(100, ($currentXP / $xpNeeded) * 100);

// --- Fetch all claimed achievements for auto-equip logic ---
$stmtAllAchievements = $conn->prepare("
  SELECT a.icon, a.achievementName, a.type, ua.date
  FROM tbl_userachievements ua
  JOIN tbl_achievements a ON ua.achievementID = a.achievementID
  WHERE ua.userID = ? AND ua.isClaimed = 1
  ORDER BY ua.date ASC
");
$stmtAllAchievements->bind_param("i", $userID);
$stmtAllAchievements->execute();
$allAchievementsResult = $stmtAllAchievements->get_result();
$allAchievements = [];
while ($row = $allAchievementsResult->fetch_assoc()) {
  $allAchievements[] = $row;
}
$stmtAllAchievements->close();

// Get displayed badges array from database
$displayedBadgesString = trim($user['displayedBadges'] ?? '');
$displayedBadgesArray = [];
if (!empty($displayedBadgesString)) {
  $displayedBadgesArray = explode(',', $displayedBadgesString);
  $displayedBadgesArray = array_filter(array_map('trim', $displayedBadgesArray));
}

if (empty($displayedBadgesArray) && !empty($allAchievements)) {
  $titles = array_filter($allAchievements, function($a) { return $a['type'] === 'title'; });
  $badges = array_filter($allAchievements, function($a) { return $a['type'] === 'badge'; });
  $selected = [];
  
  if (!empty($titles)) {
    $titlesArray = array_values($titles);
    $selected[] = $titlesArray[0]['icon'];
  }

  $badgesArray = array_values($badges);
  for ($i = 0; $i < 2 && $i < count($badgesArray); $i++) {
    $selected[] = $badgesArray[$i]['icon'];
  }
  
  $displayedBadgesArray = $selected;
  
  if (!empty($selected)) {
    $newDisplayedBadges = implode(',', $selected);
    $stmtUpdate = $conn->prepare("UPDATE tbl_users SET displayedBadges = ? WHERE userID = ?");
    $stmtUpdate->bind_param("si", $newDisplayedBadges, $userID);
    $stmtUpdate->execute();
    $stmtUpdate->close();
  }
}

// --- Fetch only the equipped badges (max 3) for display ---
$displayedBadges = [];
if (!empty($displayedBadgesArray)) {
  // Create placeholders for IN clause
  $placeholders = implode(',', array_fill(0, count($displayedBadgesArray), '?'));

  $stmtBadges = $conn->prepare("
    SELECT achievementName, icon
    FROM tbl_achievements
    WHERE icon IN ($placeholders)
    LIMIT 3
  ");

  // Bind parameters dynamically
  $types = str_repeat('s', count($displayedBadgesArray));
  $stmtBadges->bind_param($types, ...$displayedBadgesArray);
  $stmtBadges->execute();
  $badgesResult = $stmtBadges->get_result();

  while ($badge = $badgesResult->fetch_assoc()) {
    $displayedBadges[] = $badge;
  }
  $stmtBadges->close();
}
?>
<!-- Sidebar UI -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel" style="width:250px;overflow-y:auto;transition:transform .3s ease;background-color:#44B87D;
  font-family:'Poppins',sans-serif!important;font-size:14px!important;">

  <div class="offcanvas-body p-0" style="background-color:#44B87D;height:100vh;overflow-y:auto;
    font-family:'Poppins',sans-serif!important;font-size:14px!important;">

    <!-- User Info Section -->
    <div class="p-4 border-bottom bg-light shadow-sm position-sticky top-0"
      style="z-index:1055;background-color:white;flex-shrink:0;box-sizing:border-box;">

      <div class="d-flex align-items-center gap-3 mb-2" style="flex-wrap:nowrap;">

        <!-- Profile Picture -->
        <div class="rounded-circle overflow-hidden flex-shrink-0 bg-light"
          style="width:48px;height:48px;min-width:48px;min-height:48px;">
          <?php
          $profilePicturePath = !empty($user['profilePicture'])
            ? '../../assets/img/profile/' . htmlspecialchars($user['profilePicture'])
            : '../../assets/img/shared/profile_Pic.png';
          ?>
          <img src="<?= $profilePicturePath; ?>" alt="Profile" style="width:100%;height:100%;object-fit:cover;">
        </div>

        <!-- User Details -->
        <div class="flex-grow-1" style="min-width:0;">

          <p class="text-dark fw-semibold mb-0" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
            font-family:'Poppins',sans-serif!important;font-size:16px!important;">
            <?= htmlspecialchars($user['userName']); ?>
          </p>

          <p class="text-muted mb-0" style="white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
          font-family: 'Roboto', sans-serif !important;
          font-size: 12px !important;
          font-weight: normal !important;">
            <?= htmlspecialchars($user['email']); ?>
          </p>


          <!-- Equipped Badges -->
          <div class="d-flex align-items-center gap-1 mt-1 flex-wrap" style="max-width:150px;">

            <?php if (!empty($displayedBadges)): ?>
              <?php foreach ($displayedBadges as $badge): ?>
                <img src="../../assets/img/challenge/<?= htmlspecialchars($badge['icon']); ?>"
                  alt="<?= htmlspecialchars($badge['achievementName']); ?>"
                  title="<?= htmlspecialchars($badge['achievementName']); ?>"
                  style="width:25px;height:25px;object-fit:contain;flex-shrink:0;">
              <?php endforeach; ?>

            <?php else: ?>
              <!-- BADGES TEXT -->
              <p class="text-muted mb-0" style="font-family: 'Roboto', sans-serif !important;
          font-size: 12px !important;
          font-weight: normal !important;">
                No badges equipped
              </p>

            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Level and XP -->
      <p class="text-dark mb-1" style="font-family:'Poppins',sans-serif !important;
          font-size:16px !important;
          font-weight:bold !important;">
        Level <?= $currentLevel; ?>
      </p>


      <div class="w-100 bg-secondary bg-opacity-25 rounded-pill" style="height:8px;">
        <div class="bg-warning rounded-pill" style="width:<?= $progressPercent; ?>%;height:8px;"></div>
      </div>

      <p class="text-muted mt-1 mb-3" style="font-family:'Poppins',sans-serif!important;font-size:12px!important;">
        <?= $currentXP; ?> XP / <?= $xpNeeded; ?> XP
      </p>

      <form method="post" action="../../pages/logout/logout.php">
        <button type="submit" class="w-100 btn btn-sm btn-danger fw-medium"
          style="font-family:'Poppins',sans-serif!important;font-size:14px!important;border-radius:20px;">
          Logout
        </button>
      </form>

    </div>


    <!-- Sidebar Links -->
    <ul class="list-unstyled m-0 p-3"
      style="font-family:'Roboto',sans-serif!important;font-size:16px;line-height:5px!important;">
      <?php
      $links = [
        ["Home", "home/home.php", "Home_SB.png"],
        ["Income & Expenses", "income&expenses/income_expenses.php", "I&E_SB.png"],
        ["Savings Goals", "savings/savingDetail.php", "Savings_SB.png"],
        ["Cointrol", "cointrol/cointrol.php", "Cointrol_SB.png"],
        ["Saving Strategies", "savingstrategies/savingstrat.php", "SavingStrat_SB.png"],
        ["History", "history/history.php", "History_SB.png"],
        ["Notifications", "notification/notification.php", "Notif_SB.png"],
        ["Challenges", "challenge/challengeMain.php", "Challenge_SB.png"],
        ["Profile", "profile/profile.php", "Profile_SB.png"],
        ["Settings", "settings/settings.php", "Settings_SB.png"]
      ];

      foreach ($links as [$name, $href, $icon]) {
        echo "
          <li>
            <a href='../../pages/$href'
              class='d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded'
              style='display:block; transition: background-color 0.25s, color 0.25s;'
              onmouseover=\"this.style.backgroundColor='#3ca771'; this.style.color='white';\" 
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