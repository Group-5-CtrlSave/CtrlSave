<?php
session_start();
include_once '../../assets/shared/connect.php';

// Check Login
if (!isset($_SESSION['userID'])) {
  header("Location: ../../pages/login&signup/login.php");
  exit();
}

$userID = $_SESSION['userID'];
$type = $_GET['type'] ?? 'all';

// Base query for resources
$baseQuery = "SELECT r.*, COALESCE(p.isCompleted, 0) AS isCompleted, 
              COALESCE(p.isFavorited, 0) AS isFavorited, 
              COALESCE(p.isArchived, 0) AS isArchived 
              FROM tbl_resources r 
              LEFT JOIN tbl_user_resource_progress p 
              ON p.resourceID = r.resourceID AND p.userID = $userID";

// Modify query based on type
$whereClause = "";
if ($type === 'all') {
  $whereClause = " WHERE COALESCE(p.isFavorited, 0) = 0 AND COALESCE(p.isArchived, 0) = 0";
} elseif ($type === 'favorite') {
  $whereClause = " WHERE p.isFavorited = 1";
} elseif ($type === 'archive') {
  $whereClause = " WHERE p.isArchived = 1";
}

// Fetch resources grouped by type
$videosQuery = "$baseQuery $whereClause " . ($whereClause ? "AND" : "WHERE") . " r.resourceType = 'video'";
$videosResult = mysqli_query($conn, $videosQuery);

$articlesQuery = "$baseQuery $whereClause " . ($whereClause ? "AND" : "WHERE") . " r.resourceType = 'article'";
$articlesResult = mysqli_query($conn, $articlesQuery);

$booksQuery = "$baseQuery $whereClause " . ($whereClause ? "AND" : "WHERE") . " r.resourceType = 'book'";
$booksResult = mysqli_query($conn, $booksQuery);

// Total resources is always all, regardless of type (for percentage)
$allResourcesQuery = "SELECT COUNT(*) AS total FROM tbl_resources";
$allResourcesResult = mysqli_query($conn, $allResourcesQuery);
$totalResources = mysqli_fetch_assoc($allResourcesResult)['total'];

$completedQuery = "SELECT COUNT(*) AS completedCount 
                   FROM tbl_user_resource_progress 
                   WHERE userID = $userID AND isCompleted = 1";
$completedResult = mysqli_query($conn, $completedQuery);
$completed = mysqli_fetch_assoc($completedResult)['completedCount'];

$percentage = $totalResources > 0 ? round(($completed / $totalResources) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>CtrlSave | Saving Strategies</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/savingStrategies.css">

  <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">

  <style>
    .video-thumb {
      position: relative;
      display: block;
      width: 100%;
      border-radius: 6px;
      overflow: hidden;
      background: #000;
      text-decoration: none;
    }

    .video-thumb img {
      display: block;
      width: 100%;
      height: auto;
    }

    .video-thumb .play-overlay {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      font-size: 48px;
      opacity: 0.95;
      pointer-events: none;
    }

    .resource-card {
      padding: 12px;
      border-radius: 8px;
      background: #fff;
      margin-bottom: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      position: relative;
    }

    .badge {
      float: right;
      margin-top: 5px;
    }

    .video-caption {
      margin-top: 8px;
      margin-bottom: 18px;
    }

    .section h4 {
      color: #F6D25B;
      font-size: 20px;
      margin-bottom: 15px;
      border-bottom: 2px solid #44B87D;
      display: inline-block;
      font-family: 'Poppins', sans-serif;
    }

    .section {
      background: white;
      border-radius: 20px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }

    /* Favorite + Archive Button Styling OUTSIDE CARD */
    .resource-actions {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      justify-content: center;
    }

    .action-btn {
      border: none;
      background: #F6D25B;
      color: white;
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      transition: 0.2s;
    }

    .action-btn:hover {
      background: #44B87D;
      color: white;
    }

    .action-btn i {
      margin-right: 6px;
    }

    .action-btn.active {
      background: #44B87D;
    }

    .action-btn.active:hover {
      background: #F6D25B;
    }

    /* Base styles for custom buttons */
    .custom-btn {
      border-color: #F3F3F3 !important;
      border-style: solid;
      color: #F3F3F3 !important;
      min-width: 80px;
    }

    .custom-btn:hover {
      background-color: #F3F3F3 !important;
      color: #F6D25B !important;
    }

    .custom-btn.selected {
      background-color: #F3F3F3;
      border-color: #007bff;
      color: #F6D25B !important;
      font-weight: bold;
    }

    .allButton {
      border-radius: 20px 0px 0px 20px;
    }

    .favoriteButton {
      position: relative;
      border-radius: 0px;
    }

    .archiveButton {
      border-radius: 0px 20px 20px 0px;
    }
  </style>
</head>

<body>
  <?php include("../../assets/shared/navigationBar.php"); ?>
  <?php include("../../assets/shared/sideBar.php"); ?>

  <div class="bg-green-custom d-flex position-relative">
    <div id="overlay" class="d-none"></div>

    <div class="flex-grow-1 p-4 content-scroll">
      <header>
        <h1>Smart Saving Strategies</h1>
        <p class="tagline">Watch. Read. Apply. Save Smart.</p>

        <div class="container-fluid d-flex align-items-center justify-content-center p-2">
          <button type="button"
            class="btn custom-btn sortButton allButton <?php echo ($type == 'all') ? 'selected' : '' ?>"
            onclick="window.location.href='?type=all';"><b>All</b></button>

          <button type="button"
            class="btn custom-btn sortButton favoriteButton <?php echo ($type == 'favorite') ? 'selected' : '' ?>"
            onclick="window.location.href='?type=favorite';"><b>Favorite</b></button>

          <button type="button"
            class="btn custom-btn sortButton archiveButton <?php echo ($type == 'archive') ? 'selected' : '' ?>"
            onclick="window.location.href='?type=archive';"><b>Archive</b></button>
        </div>
      </header>

      <div class="container main-content">

        <!-- VIDEOS -->
        <div class="section">
          <h4>Watch: Learn the Basics of Saving</h4>

          <?php if (mysqli_num_rows($videosResult) > 0): ?>
            <?php while ($video = mysqli_fetch_assoc($videosResult)): ?>

              <?php
              $resourceID = (int) $video['resourceID'];
              $isCompleted = $video['isCompleted'];
              $isFavorited = $video['isFavorited'];
              $isArchived = $video['isArchived'];

              $link = $video['link'];
              $yt_id = null;
              if (preg_match('/(?:v=|\/embed\/|youtu\.be\/)([A-Za-z0-9_\-]+)/', $link, $m)) {
                $yt_id = $m[1];
              }
              $thumb = $yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : null;
              $watchUrl = $yt_id ? "https://www.youtube.com/watch?v={$yt_id}" : $link;
              ?>

              <div class="resource-card" data-status="<?= $isCompleted ? 'completed' : 'notCompleted' ?>"
                id="resource-<?= $resourceID ?>">

                <a class="video-thumb" href="<?= htmlspecialchars($watchUrl) ?>" target="_blank"
                  onclick="markCompleted(<?= $resourceID ?>);">

                  <?php if ($thumb): ?>
                    <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($video['title']) ?>">
                  <?php else: ?>
                    <div style="padding:56% 0 0 0;"></div>
                  <?php endif; ?>

                  <div class="play-overlay">▶</div>
                </a>

                <h5 class="video-caption">
                  <?= htmlspecialchars($video['title']) ?>
                  <small class="text-muted d-block"><?= htmlspecialchars($video['description'] ?? '') ?></small>

                  <?php if ($isCompleted): ?>
                    <span class="badge bg-success" id="badge-<?= $resourceID ?>">Completed</span>
                  <?php else: ?>
                    <span class="badge bg-secondary" id="badge-<?= $resourceID ?>">Not Viewed</span>
                  <?php endif; ?>
                </h5>
              </div>

              <!-- Buttons OUTSIDE the clickable card -->
              <div class="resource-actions">
                <button class="action-btn <?= $isFavorited ? 'active' : '' ?>"
                  onclick="toggleFavorite(<?= $resourceID ?>);">
                  <i class="fa<?= $isFavorited ? '-solid' : '' ?> fa-heart"></i>
                  <?= $isFavorited ? 'Unfavorite' : 'Favorite' ?>
                </button>
                <button class="action-btn <?= $isArchived ? 'active' : '' ?>" onclick="toggleArchive(<?= $resourceID ?>);">
                  <i class="fa<?= $isArchived ? '-solid' : '' ?> fa-box-archive"></i>
                  <?= $isArchived ? 'Unarchive' : 'Archive' ?>
                </button>
              </div>

            <?php endwhile; ?>
          <?php else: ?>
            <p class="text-muted">No videos available yet.</p>
          <?php endif; ?>
        </div>

        <!-- ARTICLES -->
        <div class="section">
          <h4>Read: Resources to Save Smarter</h4>
          <div class="resources">
            <?php if (mysqli_num_rows($articlesResult) > 0): ?>
              <?php while ($article = mysqli_fetch_assoc($articlesResult)): ?>

                <?php
                $resourceID = (int) $article['resourceID'];
                $isCompleted = $article['isCompleted'];
                $isFavorited = $article['isFavorited'];
                $isArchived = $article['isArchived'];
                ?>

                <div class="resource-card" data-status="<?= $isCompleted ? 'completed' : 'notCompleted' ?>"
                  id="resource-<?= $resourceID ?>">

                  <a href="<?= htmlspecialchars($article['link']); ?>" target="_blank"
                    onclick="markCompleted(<?= $resourceID ?>);">
                    <?= htmlspecialchars($article['title']); ?>
                  </a>

                  <p><?= htmlspecialchars($article['description']); ?></p>

                  <?php if ($isCompleted): ?>
                    <span class="badge bg-success" id="badge-<?= $resourceID ?>">Completed</span>
                  <?php else: ?>
                    <span class="badge bg-secondary" id="badge-<?= $resourceID ?>">Not Viewed</span>
                  <?php endif; ?>
                </div>

                <!-- Buttons OUTSIDE the clickable card -->
                <div class="resource-actions">
                  <button class="action-btn <?= $isFavorited ? 'active' : '' ?>"
                    onclick="toggleFavorite(<?= $resourceID ?>);">
                    <i class="fa<?= $isFavorited ? '-solid' : '' ?> fa-heart"></i>
                    <?= $isFavorited ? 'Unfavorite' : 'Favorite' ?>
                  </button>
                  <button class="action-btn <?= $isArchived ? 'active' : '' ?>"
                    onclick="toggleArchive(<?= $resourceID ?>);">
                    <i class="fa<?= $isArchived ? '-solid' : '' ?> fa-box-archive"></i>
                    <?= $isArchived ? 'Unarchive' : 'Archive' ?>
                  </button>
                </div>

              <?php endwhile; ?>
            <?php else: ?>
              <p class="text-muted">No articles available yet.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- BOOKS -->
        <div class="section">
          <h4>Books: Learn Even More</h4>
          <div class="resources">
            <?php if (mysqli_num_rows($booksResult) > 0): ?>
              <?php while ($book = mysqli_fetch_assoc($booksResult)): ?>

                <?php
                $resourceID = (int) $book['resourceID'];
                $isCompleted = $book['isCompleted'];
                $isFavorited = $book['isFavorited'];
                $isArchived = $book['isArchived'];
                ?>

                <div class="resource-card" data-status="<?= $isCompleted ? 'completed' : 'notCompleted' ?>"
                  id="resource-<?= $resourceID ?>">

                  <a href="<?= htmlspecialchars($book['link']); ?>" target="_blank"
                    onclick="markCompleted(<?= $resourceID ?>);">
                    <?= htmlspecialchars($book['title']); ?>
                  </a>

                  <p><?= htmlspecialchars($book['description']); ?></p>

                  <?php if ($isCompleted): ?>
                    <span class="badge bg-success" id="badge-<?= $resourceID ?>">Completed</span>
                  <?php else: ?>
                    <span class="badge bg-secondary" id="badge-<?= $resourceID ?>">Not Viewed</span>
                  <?php endif; ?>
                </div>

                <!-- Buttons OUTSIDE the clickable card -->
                <div class="resource-actions">
                  <button class="action-btn <?= $isFavorited ? 'active' : '' ?>"
                    onclick="toggleFavorite(<?= $resourceID ?>);">
                    <i class="fa<?= $isFavorited ? '-solid' : '' ?> fa-heart"></i>
                    <?= $isFavorited ? 'Unfavorite' : 'Favorite' ?>
                  </button>
                  <button class="action-btn <?= $isArchived ? 'active' : '' ?>"
                    onclick="toggleArchive(<?= $resourceID ?>);">
                    <i class="fa<?= $isArchived ? '-solid' : '' ?> fa-box-archive"></i>
                    <?= $isArchived ? 'Unarchive' : 'Archive' ?>
                  </button>
                </div>

              <?php endwhile; ?>
            <?php else: ?>
              <p class="text-muted">No books available yet.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function markCompleted(resourceID) {
      fetch('markCompleted.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'resourceID=' + encodeURIComponent(resourceID)
      })
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            const badge = document.getElementById('badge-' + resourceID);
            const card = document.getElementById('resource-' + resourceID);
            if (badge) {
              badge.textContent = 'Completed';
              badge.classList.remove('bg-secondary');
              badge.classList.add('bg-success');
            }
            if (card) {
              card.setAttribute('data-status', 'completed');
            }
          }
        })
        .catch(err => {
          console.error('markCompleted error', err);
        });
    }

    function toggleFavorite(resourceID) {
      fetch('toggleFavorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'resourceID=' + encodeURIComponent(resourceID)
      })
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            window.location.reload();
          }
        })
        .catch(err => {
          console.error('toggleFavorite error', err);
        });
    }

    function toggleArchive(resourceID) {
      fetch('toggleArchive.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'resourceID=' + encodeURIComponent(resourceID)
      })
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            window.location.reload();
          }
        })
        .catch(err => {
          console.error('toggleArchive error', err);
        });
    }

    document.querySelectorAll(".resource-card").forEach(card => {
      card.addEventListener("click", function (e) {
        if (e.target.tagName.toLowerCase() === "a") return;
        const link = this.querySelector("a");
        if (!link) return;
        link.click();
      });
    });
  </script>
   <script>
        // Push a fake history state so back swipe hits this first
        history.pushState(null, "", location.href);

        // Handle back swipe / back button
        window.addEventListener("popstate", function (event) {
            // Redirect to home page
            location.replace("../../pages/home/home.php"); // use replace to avoid stacking history
        });
    </script>

</body>

</html>