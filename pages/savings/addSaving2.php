<?php
session_start();
include("../../assets/shared/connect.php");

// Set MySQL session timezone to Manila
$conn->query("SET time_zone = '+08:00'");
if (!isset($_SESSION['goalName']) || !isset($_SESSION['goalIcon'])) {
  header("Location: addsaving1.php");
  exit();
}

if (!isset($_SESSION['userID'])) {
  header("Location: ../../login/login.php");
  exit();
}

$userID = intval($_SESSION['userID']);

$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'USD') ? '$' : '₱';

$createdAt = date('Y-m-d H:i:s');
$goalName = $_SESSION['goalName'] ?? '';
$goalIcon = $_SESSION['goalIcon'] ?? '';


$currentMonth = date('m');
$currentYear  = date('Y');


$iconFilename = basename($goalIcon);
$displayIconRel = $iconFilename ? "../../assets/img/shared/categories/expense/" . $iconFilename : '';

// Get user's total income
$incomeQuery = "SELECT SUM(amount) AS totalIncome FROM tbl_income WHERE userID = '$userID' AND MONTH(dateReceived) = '$currentMonth' AND YEAR(dateReceived) = '$currentYear' AND isDeleted = 0";
$incomeResult = mysqli_query($conn, $incomeQuery);
$incomeRow = mysqli_fetch_assoc($incomeResult);
$totalIncome = $incomeRow['totalIncome'] !== null ? $incomeRow['totalIncome'] : 0;

// Get user's total expense
$expenseQuery = "SELECT SUM(amount) AS totalExpense FROM tbl_expense WHERE userID = '$userID' AND MONTH(dateSpent) = '$currentMonth' AND YEAR(dateSpent) = '$currentYear' AND isDeleted = 0";
$expenseResult = mysqli_query($conn, $expenseQuery);
$expenseRow = mysqli_fetch_assoc($expenseResult);
$totalExpense = $expenseRow['totalExpense'] !== null ? $expenseRow['totalExpense'] : 0;

// Get total amount in ALL savings goals
$savingsQuery = "SELECT SUM(currentAmount) AS totalSavings FROM tbl_savinggoals WHERE userID = '$userID' AND MONTH(createdAt) = '$currentMonth' AND YEAR(createdAt) = '$currentYear' AND status != 'Deleted'";
$savingsResult = mysqli_query($conn, $savingsQuery);
$savingsRow = mysqli_fetch_assoc($savingsResult);
$totalSavings = $savingsRow['totalSavings'] !== null ? $savingsRow['totalSavings'] : 0;

// Available Balance
$availableBalance = $totalIncome - $totalExpense - $totalSavings;

if (isset($_POST['btnAddGoalConfirmed'])) {

  $goalName = mysqli_real_escape_string($conn, $_POST['goalName'] ?? $goalName);
  $goalIcon = basename(mysqli_real_escape_string($conn, $_POST['goalIcon'] ?? $iconFilename));
  $targetAmount = floatval($_POST['goalAmount']);
  $currentAmount = floatval($_POST['currentBalance']);
  $deadline = mysqli_real_escape_string($conn, $_POST['targetDate']);

  $remind = isset($_POST['reminder']) ? 1 : 0;
  $time = $_POST['reminderTime'] ?? null;
  $frequency = $_POST['repeatFrequency'] ?? null;

  if ($remind == 0) {
    $time = null;
    $frequency = null;
  }

  $status = "In Progress";

  if ($currentAmount > $targetAmount) {
    echo "<script>alert('Current balance cannot exceed goal amount!'); window.history.back();</script>";
    exit();
  }

  if ($currentAmount > $availableBalance) {
    echo "<script>alert('Insufficient balance! Your available balance is " . $symbol . number_format($availableBalance, 2) . "'); window.history.back();</script>";
    exit();
}

  mysqli_begin_transaction($conn);

  try {
    $insertGoalQuery = "
      INSERT INTO tbl_savinggoals 
      (userID, goalName, icon, targetAmount, currentAmount, deadline, status, remind, time, frequency, createdAt)
      VALUES (
        '$userID',
        '$goalName',
        '$goalIcon',
        '$targetAmount',
        '$currentAmount',
        '$deadline',
        '$status',
        '$remind',
        " . ($remind == 1 ? ($time ? "'$time'" : "NULL") : "NULL") . ",
        " . ($remind == 1 ? ($frequency ? "'$frequency'" : "NULL") : "NULL") . ",
        '$createdAt'
      )
    ";

    if (!mysqli_query($conn, $insertGoalQuery)) {
      throw new Exception("Error inserting saving goal: " . mysqli_error($conn));
    }

    $goalID = mysqli_insert_id($conn);

    if ($currentAmount >= $targetAmount) {
      mysqli_query($conn, "
        UPDATE tbl_savinggoals
        SET status = 'Completed'
        WHERE savingGoalID = '$goalID'
      ");
    }

    include_once "../challenge/process/challengeController.php";
    updateSavingGoalDailyChallenge($userID, $conn);

    mysqli_commit($conn);

    unset($_SESSION['goalName'], $_SESSION['goalIcon']);

    header("Location: savingGoal.php");
    exit();

  } catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
  }
}
?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>Add Saving — Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .progress-line {
      height: 4px;
      background-color: #F6D25B;
      width: 100%;
      animation: fillToFull 1s ease-in-out forwards;
    }

    @keyframes fillToFull {
      from { width: 50%; }
      to { width: 100%; }
    }

    .input-wrapper.error { border: 2px solid #E63946; }

    .error-message {
      color: #ffcccc;
      font-size: 13px;
      margin-top: -8px;
      margin-bottom: 12px;
      font-weight: 500;
      text-align: center;
    }

    .save-btn { pointer-events: none; opacity: 0.6; transition: opacity .2s; }
    .save-btn.active { pointer-events: auto; opacity: 1; }

    .icon-list::-webkit-scrollbar { width: 0px; background: transparent; }
    .icon-list { scrollbar-width: none; -ms-overflow-style: none; }
    .icon-list::-webkit-scrollbar-thumb { background: transparent; }

    .balance-info {
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      padding: 8px 12px;
      margin-bottom: 15px;
    }

    .disabled-card {
      background-color: #e9ecef !important;
      cursor: not-allowed;
    }
  </style>
</head>

<body class="m-0 overflow-hidden" style="background-color: #44B87D; height: 100vh;">

  <form method="POST" id="goalForm">
    <nav class="bg-white px-4 py-4 d-flex align-items-center shadow sticky-top" style="height: 72px;">
      <a href="javascript:window.location.href='addSaving1.php';">
        <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;">
      </a>
      <h5 class="m-0 fw-bold text-dark flex-grow-1 text-center" style="transform: translateX(-15px);">Add Goal</h5>
    </nav>

    <div class="icon-list d-flex flex-column justify-content-between overflow-auto"
      style="height: calc(100vh - 72px - 88px); padding: 1rem 1rem 20px;">
      <div>
        <p class="fw-bold text-white fs-5 mb-2">What are the details of your saving goal?</p>
        <p class="fw-bold text-white mb-3">Saving goal: <?= htmlspecialchars($goalName) ?></p>

        <?php if (!empty($iconFilename)): ?>
          <div class="text-center mb-3">
            <div class="bg-white rounded-circle mx-auto d-flex justify-content-center align-items-center"
              style="width: 100px; height: 100px;">
              <img src="<?= htmlspecialchars($displayIconRel) ?>" alt="<?= htmlspecialchars($iconFilename) ?>"
                style="width:80px; height:80px; object-fit:contain;">
            </div>
          </div>
        <?php endif; ?>

        <!-- Available Balance Display -->
        <div class="balance-info text-center mb-3">
          <small class="text-white">Available Balance</small>
          <h5 class="text-white fw-bold mb-0"><?= $symbol . number_format($availableBalance, 2); ?></h5>
        </div>

        <input type="hidden" name="goalName" value="<?= htmlspecialchars($goalName) ?>">
        <input type="hidden" name="goalIcon" value="<?= htmlspecialchars($iconFilename) ?>">

        <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Goal Amount</label>
        <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3"
          style="height: 50px;" id="goalAmountWrapper">
          <input type="number" name="goalAmount" id="goalAmount" placeholder="Enter amount" step="0.01"
            class="border-0 bg-transparent fw-semibold flex-grow-1" style="outline: none; font-size: 15px;" required>
          <span class="text-warning fw-bold"><?= $symbol ?></span>
        </div>

        <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Current Balance</label>
        <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3"
          style="height: 50px;" id="currentBalanceWrapper">
          <input type="number" name="currentBalance" id="currentBalance" placeholder="Enter balance" step="0.01"
            max="<?php echo $availableBalance; ?>" class="border-0 bg-transparent fw-semibold flex-grow-1"
            style="outline: none; font-size: 15px;" required>
          <span class="text-warning fw-bold"><?= $symbol ?></span>
        </div>
        <div id="balanceError" class="error-message" style="display:none;">Current balance cannot exceed goal amount</div>
        <div id="insufficientBalanceError" class="error-message" style="display:none;">
          Insufficient balance. Available: ₱<?php echo number_format($availableBalance, 2); ?>
        </div>

        <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Target Date</label>
        <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-4"
          style="height: 50px;">
          <input type="date" name="targetDate" id="targetDate" class="border-0 bg-transparent fw-semibold w-100"
            style="outline: none; font-size: 15px;" required>
        </div>

        <p class="fw-bold text-white fs-5 mt-4 mb-3">Need a reminder for your savings?</p>
        <div class="d-flex justify-content-between align-items-center bg-white rounded-3 px-3 mb-3"
          style="height: 50px;">
          <span class="fw-semibold">Enable Reminder</span>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" name="reminder" role="switch" id="reminderSwitch" checked>
          </div>
        </div>

        <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Reminder Time</label>
        <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3"
          style="height: 50px;">
          <input type="time" name="reminderTime" id="reminderTime" value="22:00"
            class="border-0 bg-transparent fw-semibold w-100" style="outline: none; font-size: 15px;">
        </div>

        <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Repeat Frequency</label>
        <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3"
          style="height: 50px;">
          <select name="repeatFrequency" id="repeatFrequency" class="border-0 bg-transparent fw-semibold w-100"
            style="outline: none; font-size: 15px;">
            <option value="daily">Every Day</option>
            <option value="weekly" selected>Every Week</option>
            <option value="monthly">Every Month</option>
          </select>
        </div>
      </div>
    </div>

    <div style="background: white; position: fixed; bottom: 0; width: 100%;">
      <div class="progress-line"></div>
      <div class="p-3">
        <button type="submit" name="btnAddGoalConfirmed"
          class="btn w-100 fw-semibold d-flex justify-content-center align-items-center save-btn"
          style="background-color: #F6D25B; border-radius: 999px; height: 50px; font-size: 16px;">
          Save
        </button>
      </div>
    </div>
  </form>

  <script>
    const saveBtn = document.querySelector(".save-btn");
    const goalAmount = document.getElementById("goalAmount");
    const currentBalance = document.getElementById("currentBalance");
    const targetDate = document.getElementById("targetDate");
    const balanceError = document.getElementById("balanceError");
    const insufficientBalanceError = document.getElementById("insufficientBalanceError");
    const currentBalanceWrapper = document.getElementById("currentBalanceWrapper");
    const goalForm = document.getElementById("goalForm");
    const availableBalance = <?php echo $availableBalance; ?>;

    const reminderSwitch = document.getElementById("reminderSwitch");
    const reminderTime = document.getElementById("reminderTime");
    const repeatFrequency = document.getElementById("repeatFrequency");

    function toggleReminderFields() {
      const enabled = reminderSwitch.checked;
      reminderTime.disabled = !enabled;
      repeatFrequency.disabled = !enabled;

      if (!enabled) {
        reminderTime.style.backgroundColor = "#e9ecef";
        repeatFrequency.style.backgroundColor = "#e9ecef";
        reminderTime.style.cursor = "not-allowed";
        repeatFrequency.style.cursor = "not-allowed";
      } else {
        reminderTime.style.backgroundColor = "white";
        repeatFrequency.style.backgroundColor = "white";
        reminderTime.style.cursor = "auto";
        repeatFrequency.style.cursor = "auto";
      }
    }

    function validateBalance() {
      const goal = parseFloat(goalAmount.value) || 0;
      const current = parseFloat(currentBalance.value) || 0;

      balanceError.style.display = "none";
      insufficientBalanceError.style.display = "none";
      currentBalanceWrapper.classList.remove("error");

      if (currentBalance.value.trim() === "") return true;

      if (current > availableBalance) {
        insufficientBalanceError.style.display = "block";
        currentBalanceWrapper.classList.add("error");
        return false;
      }

      if (current > goal && goal > 0) {
        balanceError.style.display = "block";
        currentBalanceWrapper.classList.add("error");
        return false;
      }

      return true;
    }

    function checkFields() {
      const goalFilled = goalAmount.value.trim() !== "";
      const balanceFilled = currentBalance.value.trim() !== "";
      const dateFilled = targetDate.value.trim() !== "";

      const allFilled = goalFilled && balanceFilled && dateFilled;
      const balanceValid = validateBalance();

      if (allFilled && balanceValid) {
        saveBtn.classList.add("active");
      } else {
        saveBtn.classList.remove("active");
      }
    }

    goalAmount.addEventListener("input", checkFields);
    currentBalance.addEventListener("input", checkFields);
    targetDate.addEventListener("input", checkFields);
    reminderSwitch.addEventListener("change", toggleReminderFields);

    goalForm.addEventListener("submit", function (e) {
      const current = parseFloat(currentBalance.value) || 0;

      if (current > availableBalance) {
        e.preventDefault();
        alert("Insufficient balance! Your available balance is <?= $symbol ?>" + availableBalance.toFixed(2));
        return;
      }

      if (!validateBalance()) {
        e.preventDefault();
        alert("Current balance cannot exceed goal amount!");
      }
    });

    // Initialize on page load
    checkFields();
    toggleReminderFields();
  </script>

</body>
</html>
