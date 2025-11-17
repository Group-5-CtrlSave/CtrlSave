<?php
session_start();
include("../../assets/shared/connect.php");

if (!isset($_SESSION['userID'])) {
    $_SESSION['userID'] = 2; 
}

$userID = intval($_SESSION['userID']);
$createdAt = date('Y-m-d H:i:s');
$goalName = $_SESSION['goalName'] ?? '';
$goalIcon = $_SESSION['goalIcon'] ?? '';

$iconFilename = basename($goalIcon);
$displayIconRel = $iconFilename ? "../../assets/img/shared/categories/expense/" . $iconFilename : '';

if (isset($_POST['btnAddGoalConfirmed'])) {
    $goalName = mysqli_real_escape_string($conn, $_POST['goalName'] ?? $goalName);
    $goalIcon = basename(mysqli_real_escape_string($conn, $_POST['goalIcon'] ?? $iconFilename));
    $targetAmount = floatval($_POST['goalAmount']);
    $currentAmount = floatval($_POST['currentBalance']);
    $deadline = mysqli_real_escape_string($conn, $_POST['targetDate']);
    $remind = isset($_POST['reminder']) ? 1 : 0;
    $time = $_POST['reminderTime'] ?? null;
    $frequency = $_POST['repeatFrequency'] ?? null;
    $status = "In Progress";

    if ($currentAmount > $targetAmount) {
        echo "<script>alert('Current balance cannot exceed goal amount!'); window.history.back();</script>";
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
       $insertGoalQuery = "
        INSERT INTO tbl_savinggoals 
        (userID, goalName, icon, targetAmount, currentAmount, deadline, status, remind, time, frequency, createdAt)
        VALUES (
          '$userID', '$goalName', '$goalIcon', '$targetAmount', '$currentAmount', 
          '$deadline', '$status', '$remind',
          " . ($time ? "'$time'" : "NULL") . ", 
          " . ($frequency ? "'$frequency'" : "NULL") . ", 
          '$createdAt'
        )
      ";

        if (!mysqli_query($conn, $insertGoalQuery)) {
            throw new Exception("Error inserting saving goal: " . mysqli_error($conn));
        }

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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Saving — Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .progress-line { height: 4px; background-color: #F6D25B; width: 100%; animation: fillToFull 1s ease-in-out forwards; }
    @keyframes fillToFull { from { width: 50%; } to { width: 100%; } }
    
    .input-wrapper.error {
      border: 2px solid #dc3545;
    }

    .error-message {
      color: #ffcccc;
      font-size: 13px;
      margin-top: -8px;
      margin-bottom: 12px;
      font-weight: 500;
      text-align: center;
    }

    .save-btn {
      pointer-events: none;
      opacity: 0.6;
      transition: opacity .2s;
    }

    .save-btn.active {
      pointer-events: auto;
      opacity: 1;
    }

    .icon-list::-webkit-scrollbar { width: 0px; background: transparent; }
    .icon-list { scrollbar-width: none; -ms-overflow-style: none; }
    .icon-list::-webkit-scrollbar-thumb { background: transparent; }
  </style>
</head>
<body class="m-0 overflow-hidden" style="background-color: #44B87D; height: 100vh;">

<form method="POST" id="goalForm">
  <nav class="bg-white px-4 py-4 d-flex align-items-center shadow sticky-top" style="height: 72px;">
    <a href="addsaving1.php">
      <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;">
    </a>
    <h5 class="m-0 fw-bold text-dark flex-grow-1 text-center" style="transform: translateX(-15px);">Add Goal</h5>
  </nav>

  <div class="icon-list d-flex flex-column justify-content-between overflow-auto" style="height: calc(100vh - 72px - 88px); padding: 1rem 1rem 20px;">
    <div>
      <p class="fw-bold text-white fs-5 mb-2">What are the details of your saving goal?</p>
      <p class="fw-bold text-white fs-5 mb-2">Saving goal: <?= htmlspecialchars($goalName) ?></p>

      <?php if (!empty($iconFilename)): ?>
        <div class="text-center mb-3">
          <div class="bg-white rounded-circle mx-auto d-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
            <img src="<?= htmlspecialchars($displayIconRel) ?>" alt="<?= htmlspecialchars($iconFilename) ?>" style="width:80px; height:80px; object-fit:contain;">
          </div>
        </div>
      <?php endif; ?>

      <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Goal Amount</label>
      <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3" style="height: 50px;" id="goalAmountWrapper">
        <input type="number" name="goalAmount" id="goalAmount" placeholder="Enter amount" step="0.01" 
               class="border-0 bg-transparent fw-semibold flex-grow-1" style="outline: none; font-size: 15px;" required>
        <span class="text-warning fw-bold">PHP</span>
      </div>

      <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Current Balance</label>
      <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3" style="height: 50px;" id="currentBalanceWrapper">
        <input type="number" name="currentBalance" id="currentBalance" placeholder="Enter balance" step="0.01" 
               class="border-0 bg-transparent fw-semibold flex-grow-1" style="outline: none; font-size: 15px;" required>
        <span class="text-warning fw-bold">PHP</span>
      </div>
      <div id="balanceError" class="error-message" style="display:none;">Current balance cannot exceed goal amount</div>

      <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Target Date</label>
      <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-4" style="height: 50px;">
        <input type="date" name="targetDate" id="targetDate"
               class="border-0 bg-transparent fw-semibold w-100" style="outline: none; font-size: 15px;" required>
      </div>

      <p class="fw-bold text-white fs-5 mt-4 mb-3">Need a reminder for your savings?</p>
      <div class="d-flex justify-content-between align-items-center bg-white rounded-3 px-3 mb-3" style="height: 50px;">
        <span class="fw-semibold">Enable Reminder</span>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" name="reminder" role="switch" checked>
        </div>
      </div>

      <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Reminder Time</label>
      <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3" style="height: 50px;">
        <input type="time" name="reminderTime" value="22:00"
               class="border-0 bg-transparent fw-semibold w-100" style="outline: none; font-size: 15px;">
      </div>

      <label class="fw-semibold text-white mb-2" style="font-size: 14px;">Repeat Frequency</label>
      <div class="d-flex align-items-center justify-content-between bg-white rounded-3 px-3 mb-3" style="height: 50px;">
        <select name="repeatFrequency" class="border-0 bg-transparent fw-semibold w-100" style="outline: none; font-size: 15px;">
          <option value="daily">Every Day</option>
          <option value="weekly" selected>Every Week</option>
          <option value="biweekly">Every 2 Weeks</option>
          <option value="monthly">Every Month</option>
          <option value="yearly">Every Year</option>
        </select>
      </div>
    </div>
  </div>

  <div style="background: white; position: fixed; bottom: 0; width: 100%;">
    <div class="progress-line"></div>
    <div class="p-3">
      <button type="submit" name="btnAddGoalConfirmed" class="btn w-100 fw-semibold d-flex justify-content-center align-items-center save-btn"
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
  const currentBalanceWrapper = document.getElementById("currentBalanceWrapper");
  const goalForm = document.getElementById("goalForm");

  function validateBalance() {
    const goal = parseFloat(goalAmount.value) || 0;
    const current = parseFloat(currentBalance.value) || 0;
    
    if (current > goal && goal > 0) {
      balanceError.style.display = "block";
      currentBalanceWrapper.classList.add("error");
      return false;
    } else {
      balanceError.style.display = "none";
      currentBalanceWrapper.classList.remove("error");
      return true;
    }
  }

  function checkFields(){
    const allFilled = goalAmount.value.trim() !== "" && 
                     currentBalance.value.trim() !== "" && 
                     targetDate.value.trim() !== "";
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

  goalForm.addEventListener("submit", function(e) {
    if (!validateBalance()) {
      e.preventDefault();
      alert("Current balance cannot exceed goal amount!");
    }
  });

  checkFields();
</script>
</body>
</html>