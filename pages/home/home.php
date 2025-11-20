<?php
session_start();
include("../../assets/shared/connect.php");
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['userID'])) {
  header("Location: ../../pages/login&signup/login.php");
  exit;
}

$userID = $_SESSION['userID'];

// date today
$todayDate = date('Y-m-d');
$displayToday = "Today, " . date('M d D');

// total income
$incomeQuery = "
  SELECT SUM(amount) AS totalIncome
  FROM tbl_income
  WHERE userID = '$userID'
";
$incomeResult = executeQuery($incomeQuery);
$incomeRow = mysqli_fetch_assoc($incomeResult);
$todayIncome = $incomeRow['totalIncome'] !== null ? $incomeRow['totalIncome'] : 0;

// total expense
$expenseQuery = "
  SELECT SUM(amount) AS totalExpense
  FROM tbl_expense
  WHERE userID = '$userID'
";
$expenseResult = executeQuery($expenseQuery);
$expenseRow = mysqli_fetch_assoc($expenseResult);
$todayExpense = $expenseRow['totalExpense'] !== null ? $expenseRow['totalExpense'] : 0;

// total balance
$todayBalance = $todayIncome - $todayExpense;

// income and expense
//query here BOTH INCOME AND EXPENSE DAPAT SHOW HERE

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home Page</title>

  <!-- Bootstrap & Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
    rel="stylesheet">

  <!-- CSS Files -->
  <link rel="stylesheet" href="../../assets/css/home.css">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/income&expenses.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">

  <style>
    * {
      font-family: "Roboto", sans-serif;
      font-size: 16px;
      box-sizing: border-box;
    }

    .summary-card {
      background-color: white;
      border-radius: 20px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      width: 93%;
      max-width: none;
    }

    .vertical-divider {
      width: 1.5px;
      height: 60px;
      background-color: #44B87D;
    }

    .today-text {
      color: #FFFFFF;
      font-size: 16px;
      font-weight: bold;
    }

    .categoryImgContainer {
      margin-left: -10px;
    }

    .recommendation-card,
    .challenge-card {
      background-color: #F0F1F6;
      border-radius: 20px;
      width: 335px;
    }

    .recommendation-img {
      width: 300px;
      height: 85px;
      border-radius: 20px;
      object-fit: cover;
    }

    /* Bottom Tab Bar */
    .tab-bar {
      position: fixed;
      bottom: 0;
      width: 100%;
      height: auto;
      background-color: white;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.2);
      display: flex;
      z-index: 1000;
    }

    .tab-item {
      flex: 1;
      padding-top: 15px;
      text-align: center;
    }

    .tab-icon {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }

    .tab-label {
      font-size: 16px;
      font-weight: bold;
    }

    #plusBtn {
      position: fixed;
      bottom: 2.5rem;
      left: 50%;
      transform: translateX(-50%);
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
      z-index: 1000;
    }

    #plusBtn:hover {
      background: #3aa76e;
    }
  </style>

</head>

<body>

  <!-- Navigation Bar -->
  <?php include("../../assets/shared/navigationBar.php") ?>

  <!-- Sidebar -->
  <?php include("../../assets/shared/sideBar.php") ?>

  <div style="min-height: 100vh; padding-bottom: 20px; background-color: #44B87D;">
    <div class="flex-grow-1 overflow-y-auto">

      <div class="container" style="margin-top: 120px; padding-bottom: 120px;">

        <!-- Bg -->
        <div
          style="position: fixed; top: 0; left: 0; width: 100%; height: 230px; background-color: #44B87D; z-index: 998;">
        </div>

        <!-- Income|Expense|Balance -->
        <div class="summary-card position-fixed top-0 start-50 translate-middle-x mx-auto"
          style="margin-top: 80px; z-index: 1000;">
          <div class="d-flex justify-content-around align-items-end w-100">
            <div class="text-center">
              <span class="text-danger" style="font-size: 25px;">↓</span>
              <span class="fw-bold" style="font-size: 16px; font-family: 'Poppins', sans-serif;">Expenses</span>
              <div class="fw-medium text-warning" style="font-size: 16px; font-family: 'Roboto', sans-serif;">
                ₱<?php echo number_format($todayExpense, 2); ?>
              </div>
            </div>
            <div class="vertical-divider"></div>
            <div class="text-center">
              <span class="text-success" style="font-size: 25px;">↑</span>
              <span class="fw-bold" style="font-size: 16px; font-family: 'Poppins', sans-serif;">Income</span>
              <div class="fw-medium text-warning" style="font-size: 16px; font-family: 'Roboto', sans-serif;">
                ₱<?php echo number_format($todayIncome, 2); ?>
              </div>
            </div>
            <div class="vertical-divider"></div>
            <div class="text-center">
              <span style="font-size: 25px; visibility: hidden;">↑</span>
              <span class="fw-bold" style="font-size: 16px; font-family: 'Poppins', sans-serif;">Balance</span>
              <div class="fw-medium text-warning" style="font-size: 16px; font-family: 'Roboto', sans-serif;">
                ₱<?php echo number_format($todayBalance, 2); ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Date -->
        <div class="position-fixed" style="top: 200px; left: 20px; z-index: 999;">
          <span class="today-text"><?php echo $displayToday; ?></span>
        </div>

        <!-- WALA PANG BE -->
        <!-- Income & Expense Items -->
        <div class="scrollable-container mt-4" style="margin-top: 160px !important;">
          <div class="row justify-content-center">

            <!-- Allowance -->
            <div class="col-12 col-md-8">
              <div class="container-fluid ieContainer d-flex align-items-center my-2">
                <div class="container categoryImgContainer p-1">
                  <img src="../../assets/img/shared/categories/income/Allowance.png" class="img-fluid">
                </div>
                <div class="container categoryTextContainer p-1">
                  <p class="category m-0"><b>Allowance</b></p>
                  <p class="notes m-0">Notes: Bigay ni Mama</p>
                </div>
                <div class="container iePriceContainer p-1">
                  <h5 class="price m-0">+ ₱4,000</h5>
                  <p class="time m-0"><b>12:51 PM</b></p>
                </div>
              </div>
            </div>

            <!-- Dining Out -->
            <div class="col-12 col-md-8">
              <div class="container-fluid ieContainer d-flex align-items-center my-2">
                <div class="container categoryImgContainer p-1">
                  <img src="../../assets/img/shared/categories/expense/Dining Out.png" class="img-fluid">
                </div>
                <div class="container categoryTextContainer p-1">
                  <p class="category m-0"><b>Dining Out</b></p>
                  <p class="notes m-0">Notes: Jollibee</p>
                </div>
                <div class="container iePriceContainer p-1">
                  <h5 class="price m-0">- ₱300</h5>
                  <p class="time m-0"><b>6:40 PM</b></p>
                </div>
              </div>
            </div>

            <!-- Transportation -->
            <div class="col-12 col-md-8">
              <div class="container-fluid ieContainer d-flex align-items-center my-2">
                <div class="container categoryImgContainer p-1">
                  <img src="../../assets/img/shared/categories/expense/Transportation.png" class="img-fluid">
                </div>
                <div class="container categoryTextContainer p-1">
                  <p class="category m-0"><b>Transportation</b></p>
                  <p class="notes m-0">Notes: Pamasahe otw Manila</p>
                </div>
                <div class="container iePriceContainer p-1">
                  <h5 class="price m-0">- ₱2,000</h5>
                  <p class="time m-0"><b>9:50 PM</b></p>
                </div>
              </div>
            </div>

            <!-- See more -->
            <div class="text-end" style="margin-top: -5px;">
              <a href="../income&expenses/income&expenses.php" class="btn btn-link text-white fw-semibold p-0"
                style="font-size: 16px;">See more</a>
            </div>

          </div>
        </div>

        <!-- Recommendation Card -->
        <div class="d-flex justify-content-center position-relative" style="margin-top: -90px;">
          <div class="recommendation-card p-3">
            <h2 class="fw-semibold mb-2" style="color: #44B87D; font-size: 16px;">Recommendation</h2>
            <div class="d-flex justify-content-center">
              <img src="../../assets/img/home/InsiteBg.png" class="recommendation-img">
            </div>
          </div>
        </div>

        <!-- Watch / Challenge Section -->
        <div class="d-flex justify-content-center align-items-start flex-wrap gap-3 mt-4">

          <!-- Watch Section -->
          <div class="challenge-card p-3">
            <h2 class="fw-semibold mb-3" style="color: #44B87D; font-size: 16px;">Watch. Read. Apply. Save Smart</h2>

            <div class="position-relative mb-3">
              <img src="../../assets/img/home/videosample.png" class="img-fluid rounded"
                style="height: 180px; width: 100%; object-fit: cover;">
              <span class="position-absolute top-50 start-50 translate-middle text-white fs-1">&#9658;</span>
            </div>

            <button class="btn bg-white border w-100 mb-3 text-start fw-semibold" style="border-radius: 20px;">Simple
              ways to save money for the future</button>
            <button class="btn bg-white border w-100 text-start fw-semibold" style="border-radius: 20px;">28 Proven Ways
              to Save Money</button>

            <div class="text-end mt-2">
              <a href="../savingstrategies/savingstrat.php" class="text-success fw-semibold text-decoration-none">See
                More...</a>
            </div>
          </div>

          <!-- Daily Challenge -->
          <div class="challenge-card p-3">
            <h2 class="fw-semibold mb-3" style="color: #44B87D; font-size: 16px;">Daily Saving Challenge</h2>

            <div
              class="d-flex justify-content-between align-items-center bg-white px-3 py-2 rounded-pill shadow-sm mb-2"
              style="height: 45px;">
              <span class="fw-medium text-dark">Login to CtrlSave</span>
              <button class="btn btn-sm fw-bold"
                style="background-color: #F6D25B; border-radius: 20px; color: black;">Claim</button>
            </div>

            <div class="text-end mt-2">
              <a href="../challenge/challengeMain.php" class="text-success fw-semibold text-decoration-none">See
                more...</a>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>

  <!-- Bottom Nav Bar -->
  <div class="tab-bar d-flex align-items-center">
    <div class="tab-item">
      <a href="../cointrol/cointrol.php" class="text-decoration-none text-dark d-block">
        <img src="../../assets/img/home/cointrol_Icon.png" class="tab-icon">
        <div class="tab-label">Cointrol</div>
      </a>
    </div>

    <div class="tab-item">
      <a href="../home/calculator.php" class="text-decoration-none text-dark d-block">
        <img src="../../assets/img/home/calculator.png" class="tab-icon">
        <div class="tab-label">Calculator</div>
      </a>
    </div>
  </div>

  <!-- Plus btn -->
  <button id="plusBtn" data-bs-toggle="modal" data-bs-target="#plusModal">
    <img src="../../assets/img/shared/plus.png" style="width:24px;height:24px;">
  </button>

  <!-- Modal -->
  <div class="modal fade" id="plusModal" tabindex="-1" aria-labelledby="addIncomeExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addIncomeExpenseModalLabel">Add Income or Expense</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-flex justify-content-center align-items-center">
          <a href="../income&expenses/addIncome.php">
            <button type="button" class="btn custom-btn btn-lg addIncomebtn mx-3"><b>Add Income</b></button>
          </a>
          <a href="../income&expenses/addExpenses.php">
            <button type="button" class="btn custom-btn btn-lg addExpensebtn mx-3"><b>Add Expense</b></button>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>