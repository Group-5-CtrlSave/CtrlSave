<?php
session_start();
include_once '../../assets/shared/connect.php';

// ✅ Check Login
if (!isset($_SESSION['userID'])) {
  header("Location: ../../pages/login&signup/login.php");
  exit();
}

$userID = $_SESSION['userID'];

// ✅ Fetch resources grouped by type
$videosQuery = "SELECT * FROM tbl_resources WHERE resourceType = 'video'";
$videosResult = mysqli_query($conn, $videosQuery);

$articlesQuery = "SELECT * FROM tbl_resources WHERE resourceType = 'article'";
$articlesResult = mysqli_query($conn, $articlesQuery);

$booksQuery = "SELECT * FROM tbl_resources WHERE resourceType = 'book'";
$booksResult = mysqli_query($conn, $booksQuery);

$totalResources = mysqli_num_rows($videosResult) + mysqli_num_rows($articlesResult) + mysqli_num_rows($booksResult);

$completedQuery = "SELECT COUNT(*) AS completedCount 
                   FROM tbl_user_resource_progress 
                   WHERE userID = $userID";
$completedResult = mysqli_query($conn, $completedQuery);
$completed = mysqli_fetch_assoc($completedResult)['completedCount'];

$percentage = $totalResources > 0 ? round(($completed / $totalResources) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave | Saving Strategies</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
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
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .badge {
      float: right;
      margin-top: 5px;
    }
    .video-caption {
      margin-top: 8px;
      margin-bottom: 18px;
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
      </header>

      <!-- ✅ Added Favorites/Archived buttons here -->
      

      <div class="container main-content">
        

        <!-- VIDEOS -->
        <div class="section">
          <h2>Watch: Learn the Basics of Saving</h2>

          <?php if (mysqli_num_rows($videosResult) > 0): ?>
            <?php while ($video = mysqli_fetch_assoc($videosResult)): ?>

              <?php
              $resourceID = (int)$video['resourceID'];
              $progressQuery = "SELECT isCompleted FROM tbl_user_resource_progress 
                                WHERE userID = $userID AND resourceID = $resourceID";
              $progressResult = mysqli_query($conn, $progressQuery);
              $isCompleted = mysqli_num_rows($progressResult) ? 1 : 0;

              $link = $video['link'];
              $yt_id = null;
              if (preg_match('/(?:v=|\/embed\/|youtu\.be\/)([A-Za-z0-9_\-]+)/', $link, $m)) {
                $yt_id = $m[1];
              }
              $thumb = $yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : null;
              $watchUrl = $yt_id ? "https://www.youtube.com/watch?v={$yt_id}" : $link;
              ?>

              <div class="resource-card" data-status="<?= $isCompleted ? 'completed' : 'notCompleted' ?>" id="resource-<?= $resourceID ?>">
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

            <?php endwhile; ?>
          <?php else: ?>
            <p class="text-muted">No videos available yet.</p>
          <?php endif; ?>
        </div>

        <!-- ARTICLES -->
        <div class="section">
          <h2>Read: Resources to Save Smarter</h2>
          <div class="resources">
            <?php if (mysqli_num_rows($articlesResult) > 0): ?>
              <?php while ($article = mysqli_fetch_assoc($articlesResult)): ?>

                <?php
                $resourceID = (int)$article['resourceID'];
                $progressQuery = "SELECT isCompleted FROM tbl_user_resource_progress 
                                  WHERE userID = $userID AND resourceID = $resourceID";
                $progressResult = mysqli_query($conn, $progressQuery);
                $isCompleted = mysqli_num_rows($progressResult) ? 1 : 0;
                ?>

                <div class="resource-card" data-status="<?= $isCompleted ? 'completed' : 'notCompleted' ?>" id="resource-<?= $resourceID ?>">
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

              <?php endwhile; ?>
            <?php else: ?>
              <p class="text-muted">No articles available yet.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- BOOKS -->
        <div class="section">
          <h2>Books: Learn Even More</h2>
          <div class="resources">
            <?php if (mysqli_num_rows($booksResult) > 0): ?>
              <?php while ($book = mysqli_fetch_assoc($booksResult)): ?>

                <?php
                $resourceID = (int)$book['resourceID'];
                $progressQuery = "SELECT isCompleted FROM tbl_user_resource_progress 
                                  WHERE userID = $userID AND resourceID = $resourceID";
                $progressResult = mysqli_query($conn, $progressQuery);
                $isCompleted = mysqli_num_rows($progressResult) ? 1 : 0;
                ?>

                <div class="resource-card" data-status="<?= $isCompleted ? 'completed' : 'notCompleted' ?>" id="resource-<?= $resourceID ?>">
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

    // ✅ Toggle Favorites / Archived buttons
    document.querySelectorAll(".fav-archive-btn").forEach(btn => {
      btn.addEventListener("click", function () {
        document.querySelectorAll(".fav-archive-btn").forEach(b => b.classList.remove("selected"));
        this.classList.add("selected");

        // Add filtering logic here if needed
        console.log("Selected:", this.id);
      });
    });
  </script>

</body>
</html>
