<?php
session_start();
include '../../assets/shared/connect.php';

if (!isset($_SESSION['userID'])) {
  header("Location: ../../pages/login&signup/login.php");
  exit;
}

$userID = intval($_SESSION['userID']);
$query = "SELECT * FROM tbl_savinggoals WHERE userID = $userID";
$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave | Savings Goals</title>
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
    rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <style>
    body {
      background-color: #44B87D;
      margin: 0;
      padding: 0;
      height: 100%;
    }

    .bg-green-custom {
      position: fixed;
      top: 70px;
      left: 0;
      width: 100%;
      height: calc(100vh - 80px);
      background-color: #44B87D;
      display: flex;
      flex-direction: column;
    }

    .savings-header {
      position: sticky;
      background-color: #44B87D;
      padding: 20px 30px 10px 30px;
      color: #FFFFFF;
      font-family: "Poppins", sans-serif;
    }

    .savings-header h2 {
      font-weight: 700;
    }

    .savings-list {
      flex-grow: 1;
      overflow-y: auto;
      padding-top: 0.5rem;
      margin-bottom: 70px;
      padding: 1rem;
    }

    .savings-list::-webkit-scrollbar {
      width: 0px;
      background: transparent;
    }

    .savings-list {
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .savings-list::-webkit-scrollbar-thumb {
      background: transparent;
    }

    .plus-btn {
      position: fixed;
      bottom: 1rem;
      right: 1rem;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: #F6D25B;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
      z-index: 10;
    }

    .plus-btn:hover {
      background: #3aa76e;
    }

    .empty-state-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      text-align: center;
      color: #FFFFFF;
      font-family: "Roboto", sans-serif;
    }

    .empty-state-icon {
      width: 300px;
      height: 300px;
      margin-bottom: 20px;
    }

    .empty-state-heading {
      font-weight: bold;
      margin-bottom: 10px;
      font-size: 16px;
      font-family: "Roboto", sans-serif;

    }

    .empty-state-paragraph-14px {
      font-size: 14px;
      font-family: "Roboto", sans-serif;

    }
  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <?php include("../../assets/shared/navigationBar.php") ?>
  <!-- Sidebar content-->
  <?php include("../../assets/shared/sideBar.php") ?>

  <div class="bg-green-custom">
    <!-- Fixed Header -->
    <div class="savings-header">
      <h2>My Savings Goals</h2>
    </div>
    <!-- Scrollable Cards -->
    <div class="savings-list">
      <?php
      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $id = (int) $row['savingGoalID'];
          $goalName = htmlspecialchars($row['goalName']);
          $targetAmount = is_numeric($row['targetAmount']) ? (float) $row['targetAmount'] : 0;
          $currentAmount = is_numeric($row['currentAmount']) ? (float) $row['currentAmount'] : 0;

          if ($targetAmount > 0) {
            $progress = ($currentAmount / $targetAmount) * 100;
          } else {
            $progress = 0;
          }
          $progress = min(100, max(0, $progress));
          $iconFile = trim($row['icon'] ?? '');
          $iconFileEsc = $iconFile !== '' ? htmlspecialchars($iconFile) : "Default.png";
          $icon = "../../assets/img/shared/categories/expense/" . $iconFileEsc;
          ?>
          <a href="savingDetail.php?id=<?= $id ?>" class="text-decoration-none">
            <div class="bg-white rounded-4 p-3 mb-3 d-flex align-items-center" style="height: 100px;">
              <div class="d-flex align-items-center w-100">
                <div class="me-3 d-flex align-items-center justify-content-center rounded-circle"
                  style="background-color: #F0f1f6; width: 50px; height: 50px;">
                  <img src="<?= $icon ?>" alt="Goal Icon" style="width: 30px; height: 30px;">
                </div>
                <div class="flex-grow-1">
                  <p class="mb-0 fw-semibold text-dark text-truncate"><?= $goalName ?></p>
                  <p class="mb-1 small text-truncate" style="margin-bottom: 4px;">
                    <span class="fw-semibold" style="color: #44B87D;">P <?= number_format($currentAmount, 2) ?></span>
                    <span class="text-muted"> / P<?= number_format($targetAmount, 2) ?></span>
                  </p>
                  <div class="progress" style="height: 8px; background-color: #e9ecef; width: 150px;">
                    <div class="progress-bar" role="progressbar"
                      style="width: <?= $progress ?>%; background-color: #F6D25B;"></div>
                  </div>


                </div>

                <?php if ($progress >= 100): ?>
                  <div class="ms-3 small fw-semibold text-success">Complete</div>
                <?php else: ?>
                  <div class="ms-3 small fw-medium" style="color: #44B87D;"><?= round($progress) ?>%</div>
                <?php endif; ?>
              </div>
            </div>
          </a>
          <?php
        }
      } else {
        ?>
        <div class="empty-state-container">
          <img src="../../assets\img\savings\Jar.png" alt="Future Jar Icon" class="empty-state-icon">

          <h4 class="empty-state-heading" style="font-size: 16px;">Ready to Build Your Future?</h4>

          <p class="mb-4 empty-state-paragraph-14px" style="font-size: 14px;">
            Setting your first goal is like planting the first seed. Nurture it with small, consistent savings and watch
            your future grow!
          </p>

          <p class="mb-4 empty-state-paragraph-14px" style="font-size: 14px;">
            Tap the + button below to set your first goal.
          </p>
        </div>
        <?php
      }
      ?>
    </div>
  </div>

  <!-- Plus Button -->
  <a href="addSaving1.php">
    <button class="plus-btn">
      <img src="../../assets/img/shared/plus.png" alt="Add" style="width:24px;height:24px;">
    </button>
  </a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>