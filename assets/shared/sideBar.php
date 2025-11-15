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

$stmtLevel = $conn->prepare("SELECT exp, lvl FROM tbl_userLvl WHERE userID = ? LIMIT 1");
$stmtLevel->bind_param("i", $userID);
$stmtLevel->execute();
$levelResult = $stmtLevel->get_result();
$level = $levelResult->fetch_assoc() ?? ['exp' => 0, 'lvl' => 1];
$stmtLevel->close();

$currentXP = $level['exp'];
$currentLevel = $level['lvl'];
$xpNeeded = 100;
$progressPercent = min(100, ($currentXP / $xpNeeded) * 100);

// --- Fetch only the equipped badges (max 3) ---
$displayedBadges = [];
if (!empty($user['displayedBadges'])) {
    $badgeIcons = explode(',', $user['displayedBadges']);
    $badgeIcons = array_filter(array_map('trim', $badgeIcons)); // Remove empty values
    
    if (!empty($badgeIcons)) {
        // Create placeholders for IN clause
        $placeholders = implode(',', array_fill(0, count($badgeIcons), '?'));
        
        $stmtBadges = $conn->prepare("
            SELECT achievementName, icon
            FROM tbl_achievements
            WHERE icon IN ($placeholders)
            LIMIT 3
        ");
        
        // Bind parameters dynamically
        $types = str_repeat('s', count($badgeIcons));
        $stmtBadges->bind_param($types, ...$badgeIcons);
        $stmtBadges->execute();
        $badgesResult = $stmtBadges->get_result();
        
        while ($badge = $badgesResult->fetch_assoc()) {
            $displayedBadges[] = $badge;
        }
        $stmtBadges->close();
    }
}
?>

<!-- Sidebar UI -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel"
     style="width: 250px; overflow-y: auto; transition: transform 0.3s ease; background-color: #44B87D;">
  <div class="offcanvas-body p-0" style="background-color: #44B87D; height: 100vh; overflow-y: auto;">

    <!-- User Info Section -->
    <div class="p-4 border-bottom bg-light shadow-sm position-sticky top-0" 
         style="z-index: 1055; background-color: white; flex-shrink: 0; box-sizing: border-box;">

      <div class="d-flex align-items-center gap-3 mb-2" style="flex-wrap: nowrap;">
        <!-- Profile Picture -->
        <div class="rounded-circle overflow-hidden flex-shrink-0 bg-light"
             style="width: 48px; height: 48px; min-width: 48px; min-height: 48px;">
          <?php 
          $profilePicturePath = !empty($user['profilePicture']) 
              ? '../../assets/img/profile/' . htmlspecialchars($user['profilePicture']) 
              : '../../assets/img/shared/profile_Pic.png';
          ?>
          <img src="<?= $profilePicturePath; ?>" 
               alt="Profile" 
               style="width: 100%; height: 100%; object-fit: cover;">
        </div>

        <!-- User Details -->
        <div class="flex-grow-1" style="min-width: 0;">
          <p class="text-dark fw-semibold mb-0" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?= htmlspecialchars($user['userName']); ?>
          </p>
          <p class="text-muted small mb-0" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <?= htmlspecialchars($user['email']); ?>
          </p>

          <!-- Equipped Badges (Max 3) -->
          <div class="d-flex align-items-center gap-1 mt-1 flex-wrap" style="max-width: 150px;">
            <?php if (!empty($displayedBadges)): ?>
              <?php foreach ($displayedBadges as $badge): ?>
                <img src="../../assets/img/challenge/<?= htmlspecialchars($badge['icon']); ?>"
                     alt="<?= htmlspecialchars($badge['achievementName']); ?>"
                     title="<?= htmlspecialchars($badge['achievementName']); ?>"
                     style="width: 16px; height: 16px; object-fit: contain; flex-shrink: 0;">
              <?php endforeach; ?>
            <?php else: ?>
              <p class="small text-muted mb-0">No badges equipped</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Level and XP -->
      <p class="small text-dark fw-medium mb-1">Level <?= $currentLevel; ?></p>
      <div class="w-100 bg-secondary bg-opacity-25 rounded-pill" style="height: 8px;">
        <div class="bg-warning rounded-pill" style="width: <?= $progressPercent; ?>%; height: 8px;"></div>
      </div>
      <p class="text-muted mt-1 mb-3 small">
        <?= $currentXP; ?> XP / <?= $xpNeeded; ?> XP
      </p>

      <form method="post" action="../../pages/login&signup/login.php">
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