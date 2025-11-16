<?php
include("../../assets/shared/connect.php");
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}

$userID = $_SESSION['userID'];

// GET TOTAL EXPENSES
$sqlExpense = "SELECT SUM(amount) AS totalExpense FROM tbl_expense WHERE userID = ?";
$stmtExpense = $conn->prepare($sqlExpense);
$stmtExpense->bind_param("i", $userID);
$stmtExpense->execute();
$resultExpense = $stmtExpense->get_result()->fetch_assoc();
$totalExpense = $resultExpense['totalExpense'] ?? 0;

// GET TOTAL INCOME
$sqlIncome = "SELECT SUM(amount) AS totalIncome FROM tbl_income WHERE userID = ?";
$stmtIncome = $conn->prepare($sqlIncome);
$stmtIncome->bind_param("i", $userID);
$stmtIncome->execute();
$resultIncome = $stmtIncome->get_result()->fetch_assoc();
$totalIncome = $resultIncome['totalIncome'] ?? 0;

// COMPUTE BALANCE
$balance = $totalIncome - $totalExpense;

// Get user ID
$userID = $_SESSION['userID'];

// Get today's date
$today = date('Y-m-d');

// Fetch today's income
$sqlIncome = "SELECT * FROM tbl_income WHERE userID = ? AND DATE(dateReceived) = ?";
$stmtIncome = $conn->prepare($sqlIncome);
$stmtIncome->bind_param("is", $userID, $today);
$stmtIncome->execute();
$incomeResult = $stmtIncome->get_result();

// Fetch today's expense
$sqlExpense = "SELECT * FROM tbl_expense WHERE userID = ? AND DATE(dateSpent) = ?";
$stmtExpense = $conn->prepare($sqlExpense);
$stmtExpense->bind_param("is", $userID, $today);
$stmtExpense->execute();
$expenseResult = $stmtExpense->get_result();

// Combine transactions and sort by time 
$transactions = [];

while($row = $incomeResult->fetch_assoc()){
    $row['type'] = 'income';
    $row['date_field'] = $row['dateReceived']; 
    $transactions[] = $row;
}

while($row = $expenseResult->fetch_assoc()){
    $row['type'] = 'expense';
    $row['date_field'] = $row['dateSpent']; 
    $transactions[] = $row;
}

// Sort by time
usort($transactions, function($a, $b){
    return strtotime($b['date_field']) - strtotime($a['date_field']);
});

$cardsTopMargin = count($transactions) > 0 ? '-80px' : '-40px';

// Get current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// Fetch recommendation for this month
$sqlRecommendation = "SELECT * FROM tbl_spendinginsights 
                      WHERE userID = ? 
                      AND MONTH(`date`) = ? 
                      AND YEAR(`date`) = ? 
                      LIMIT 1";
$stmtRecommendation = $conn->prepare($sqlRecommendation);
$stmtRecommendation->bind_param("iii", $userID, $currentMonth, $currentYear);
$stmtRecommendation->execute();
$recommendationResult = $stmtRecommendation->get_result();
$recommendation = $recommendationResult->fetch_assoc();

// Fetch resources from the database
$sqlResources = "SELECT * FROM tbl_resources ORDER BY dateAdded DESC LIMIT 5";
$resultResources = $conn->query($sqlResources);

// Dynamic bottom navigation items
$navItems = [
    [
        "name" => "Cointrol",
        "href" => "../cointrol/cointrol.php",
        "icon" => "../../assets/img/home/cointrol_Icon.png"
    ],
    [
        "name" => "Calculator",
        "href" => "../home/calculator.php",
        "icon" => "../../assets/img/home/calculator.png"
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/home.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/income&expenses.css">

</head>

<body style="background-color: #F0F1F6;">

  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>
  <!-- Sidebar content-->
  <?php include ("../../assets/shared/sideBar.php")?>

 <!-- Green Header Background (only at top) -->
  <div style="
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 150px;
    background-color: #44B87D;
    z-index: 0;">
  </div>
  
  <!-- Green Background Behind Summary Card -->
<div class="green-bg"></div>

  <!-- Summary Card -->
  <div class="summary-card d-flex justify-content-around align-items-center mx-auto py-2 position-fixed start-50 translate-middle-x"
       style="top: 85px; width: 90%; background-color: #F0F1F6; border-radius: 20px; z-index: 1000; border: none; box-shadow: none;">

    <!-- Expenses -->
    <div class="summary-item text-center">
      <div class="fw-semibold text-black d-flex flex-column align-items-center">
        <div class="d-flex align-items-center justify-content-center mb-1">
          <span style="color: #d60000; font-size: 1rem;" class="me-1">↓</span>
          Expenses
        </div>
        <div class="fw-bold" style="color: #F6D25B;">₱<?= number_format($totalExpense) ?></div>
      </div>
    </div>

    <div class="vertical-divider" style="width: 1px; background-color: #ccc; height: 40px;"></div>

    <!-- Income -->
    <div class="summary-item text-center">
      <div class="fw-semibold text-black d-flex flex-column align-items-center">
        <div class="d-flex align-items-center justify-content-center mb-1">
          <span style="color: #44B87D; font-size: 1rem;" class="me-1">↑</span>
          Income
        </div>
        <div class="fw-bold" style="color: #F6D25B;">₱<?= number_format($totalIncome) ?></div>
      </div>
    </div>

    <div class="vertical-divider" style="width: 1px; background-color: #ccc; height: 40px;"></div>

    <!-- Balance -->
    <div class="summary-item text-center">
      <div class="fw-semibold text-black mb-1">Balance</div>
      <div class="fw-bold" style="color: #F6D25B;">₱<?= number_format($balance) ?></div>
    </div>
  </div>
<!-- Main Content -->
<div class="container" style="padding-top: 140px; padding-bottom: 100px; position: relative; z-index: 1;">
  <!-- Date -->
  <div class="today-text">Today <?= date('M d, D') ?></div>

  <!-- Income and Expense Row -->
  <div class="scrollable-container mt-4">
    <div class="row justify-content-center">

      <style>
        .categoryImgContainer { margin-left: -10px; }
        .empty-card {
          background-color: #f8f9fa;
          border-radius: 15px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.1);
          padding: 40px 20px;
          text-align: center;
          color: #6c757d;
          width: 100%;
          max-width: 400px;
          margin: 10px auto;
        }
        .empty-card p {
          margin: 0;
          font-size: 1.1rem;
          font-weight: 600;
        }
        .empty-card i {
          font-size: 3rem;
          color: #adb5bd;
          margin-bottom: 15px;
        }
      </style>

      <?php if(count($transactions) > 0): ?>
        <?php foreach($transactions as $t): 
          $sign = ($t['type'] == 'income') ? '+' : '-';
          $color = ($t['type'] == 'income') ? '#44B87D' : '#d60000';
          $iconPath = "../../assets/img/shared/categories/" . ($t['type'] == 'income' ? 'income' : 'expense') . "/" . $t['category'] . ".png";
        ?>
        <div class="col-12 col-md-8">
          <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
            <div class="container categoryImgContainer p-1">
              <img class="img-fluid" src="<?= $iconPath ?>" alt="<?= $t['category'] ?>">
            </div>
            <div class="container categoryTextContainer p-1">
              <p class="category m-0"><b><?= $t['category'] ?></b></p>
              <p class="notes m-0">Notes: <?= $t['notes'] ?></p>
            </div>
            <div class="container iePriceContainer p-1">
              <h5 class="price m-0" style="color: <?= $color ?>"><?= $sign ?> ₱<?= number_format($t['amount']) ?></h5>
              <p class="time m-0"><b><?= date('h:i A', strtotime($t['date_field'])) ?></b></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- See More -->
        <div class="text-end" style="margin-top: -5px;">
          <a href="../income&expenses/income&expenses.php" class="btn btn-link text-white fw-semibold p-0"
             style="font-size: 0.9rem;">See more</a>
        </div>

      <?php else: ?>
        <div class="col-12">
          <div class="empty-card">
            <i class="bi bi-inbox"></i> 
            <p>No data available</p>
            <small class="text-muted">Once you add income or expenses, they'll appear here.</small>
          </div>
        </div>

        <!-- Recommendation Card -->
<div class="col-12 d-flex justify-content-center mt-3">
  <div class="recommendation-card p-3">
      <h2 class="fw-semibold mb-2 text-start text-success">Recommendation</h2>
      <div class="recommendation-content d-flex flex-column align-items-center">
          <?php if($recommendation): ?>
              <img src="<?= htmlspecialchars($recommendation['imagePath'] ?? '../../assets/img/home/InsiteBg.png') ?>" 
                   alt="Recommendation Image" class="recommendation-img">
              <p class="text-center mt-2"><?= htmlspecialchars($recommendation['message'] ?? '') ?></p>
          <?php else: ?>
              <p class="text-center text-muted fw-semibold mt-2">No data available for this month's recommendation.</p>
          <?php endif; ?>
      </div>
  </div>
</div>

        <!-- Watch / Challenge Cards + Daily Saving Challenge -->
        <div class="col-12 d-flex justify-content-center align-items-start flex-wrap gap-3 mt-3">
          <?php if ($resultResources && $resultResources->num_rows > 0): ?>
              <?php while ($resource = $resultResources->fetch_assoc()): ?>
                  <div class="challenge-card p-3" style="background-color: #F0F1F6; border-radius: 20px; width: 335px;">
                      <h2 class="fw-semibold mb-3" style="color: #44B87D;"><?= htmlspecialchars($resource['title']) ?></h2>
                      <?php if(!empty($resource['imagePath'])): ?>
                          <div class="position-relative mb-3">
                              <img src="<?= htmlspecialchars($resource['imagePath']) ?>" alt="<?= htmlspecialchars($resource['title']) ?>" class="img-fluid rounded"
                                   style="height: 180px; width: 100%; object-fit: cover;">
                              <span class="position-absolute top-50 start-50 translate-middle text-white fs-1">&#9658;</span>
                          </div>
                      <?php endif; ?>
                      <button class="btn bg-white border w-100 mb-2 text-start fw-semibold" style="border-radius: 20px;">
                          <?= htmlspecialchars($resource['shortDescription']) ?>
                      </button>
                      <div class="text-end mt-2">
                          <a href="<?= htmlspecialchars($resource['link']) ?>" class="text-success fw-semibold text-decoration-none" target="_blank">See More...</a>
                      </div>
                  </div>
              <?php endwhile; ?>
          <?php else: ?>
              <!-- Placeholder card if no DB data -->
              <div class="challenge-card p-3" style="background-color: #F0F1F6; border-radius: 20px; width: 335px;">
                  <h2 class="fw-semibold mb-3" style="color: #44B87D;">No Resources Available</h2>
                  <p class="text-center text-muted">Check back later for new challenges or resources.</p>
              </div>
          <?php endif; ?>

          <!-- Daily Saving Challenge card always shows -->
          <div class="challenge-card p-3" style="background-color: #F0F1F6; border-radius: 20px; width: 335px;">
              <h2 class="fw-semibold mb-3" style="color: #44B87D;">Daily Saving Challenge</h2>
              <div class="d-flex justify-content-between align-items-center bg-white px-3 py-2 rounded-pill shadow-sm mb-2"
                   style="height: 45px;">
                  <span class="fw-medium text-dark">Login to CtrlSave</span>
                  <button class="btn btn-sm fw-bold"
                          style="background-color: #F6D25B; border-radius: 20px; color: black;">Claim</button>
              </div>
              <div class="text-end mt-2">
                  <a href="../challenge/challengeMain.php" class="text-success fw-semibold text-decoration-none">Show more...</a>
              </div>
          </div>
        </div>

      <?php endif; ?>
    </div>
  
  </div>

</div>

  <!-- Bottom Tab Navigation -->
<div class="tab-bar d-flex justify-content-around align-items-center position-fixed bottom-0 start-0 end-0 bg-white shadow"
     style="height: 65px; z-index: 999;">

    <?php foreach ($navItems as $item): ?>
        <div class="tab-item text-center" style="margin-top: -10px;">
            <a href="<?= $item['href'] ?>" class="text-decoration-none text-dark d-block">
                <img src="<?= $item['icon'] ?>" 
                     alt="<?= $item['name'] ?>" 
                     class="tab-icon mb-1"
                     style="width: 36px; height: 36px;">
                <div class="tab-label fw-bold" style="font-size: 0.9rem;">
                    <?= $item['name'] ?>
                </div>
            </a>
        </div>
    <?php endforeach; ?>

</div>

<!-- Floating Plus Button -->
<button
    style="position:fixed;bottom:2.5rem;left:50%;transform:translateX(-50%);
           width:56px;height:56px;border-radius:50%;background:#F6D25B;border:none;
           display:flex;align-items:center;justify-content:center;
           box-shadow:0 4px 6px rgba(0,0,0,0.1);transition:0.3s;z-index:9999;"
    onmouseover="this.style.background='#3aa76e';"
    onmouseout="this.style.background='#F6D25B';"
    data-bs-toggle="modal" data-bs-target="#plusModal">
    <img src="../../assets/img/shared/plus.png" alt="Add" style="width:24px;height:24px;">
</button>

  <!-- Bootstrap JS for Offcanvas & other components -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <!-- Scripts -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const seeMoreBtn = document.getElementById("seeMoreBtn");
    const moreCards = document.getElementById("more-cards");
    let expanded = false;

    if (seeMoreBtn && moreCards) {
      seeMoreBtn.addEventListener("click", () => {
        expanded = !expanded;
        moreCards.classList.toggle("d-none", !expanded);
        seeMoreBtn.textContent = expanded ? "See less" : "See more";
      });
    }
  });
</script>

</body>

</html>
