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
    $reminder = isset($_POST['reminder']) ? 1 : 0;
    $reminderTime = $_POST['reminderTime'] ?? null;
    $repeatFrequency = $_POST['repeatFrequency'] ?? null;
    $status = "In Progress";

    mysqli_begin_transaction($conn);

    try {
       $insertGoalQuery = "
        INSERT INTO tbl_savinggoals 
        (userID, goalName, icon, targetAmount, currentAmount, deadline, status, remind, time, frequency, createdAt)
        VALUES (
          '$userID', '$goalName', '$goalIcon', '$targetAmount', '$currentAmount', 
          '$deadline', '$status', '$reminder',
          " . ($reminderTime ? "'$reminderTime'" : "NULL") . ", 
          " . ($repeatFrequency ? "'$repeatFrequency'" : "NULL") . ", 
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
  body {
    background: #44B87D;
    margin: 0;
    padding: 0;
    height: 100%;
  }

  label {
    font-size: 14px;
    color: white;
    margin-bottom: 4px;
    font-weight: 600;
  }

  .input-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #F0f1f6;
    padding: 14px 20px; 
    border-radius: 14px; 
    height: 64px; 
    margin-bottom: 1.2rem; 
    width: 100%;
    max-width: 520px; 
    margin-left: auto;
    margin-right: auto;
  }

  .input-wrapper input,
  .input-wrapper select {
    background: transparent;
    border: none;
    outline: none;
    flex: 1;
    font-weight: 600;
    font-size: 15px;
  }

  .save-btn {
    background: #F6D25B;
    border: none;
    border-radius: 999px;
    height: 54px;
    font-size: 16px;
    font-weight: 600;
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
    display: block;
    pointer-events: none;
    opacity: 0.6;
    transition: opacity .2s;
  }

  .fixed-footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background: #fff;
    z-index: 10;
    padding: 1rem 0;
  }

  .content-list {
    overflow-y: auto;
    height: calc(100vh - 72px - 88px - 72px);
    padding: 1rem 1rem 80px;
    -webkit-overflow-scrolling: touch;
  }

  .icon-option {
    width: 100px; 
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #F0f1f6;
    margin: 0 auto;
  }

  @media (max-width: 576px) {
    .input-wrapper, .save-btn {
      max-width: 100%;
    }
  }
</style>

</head>
<body>

<form method="POST">
 <nav class="bg-white px-4 py-4 d-flex align-items-center shadow sticky-top" style="height: 72px;">
  <a href="addsaving1.php">
    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;">
  </a>
  <h5 class="m-0 fw-bold text-dark flex-grow-1 text-center" style="transform: translateX(-15px);">Add Goal</h5>
</nav>

  <div class="px-4 pt-3 position-fixed w-100" style="top:72px; background:#44B87D; z-index:15;">
    <p class="fw-bold text-white fs-5 mb-3">What are the details of your saving goal?</p>
  </div>

  <div class="px-4 content-list" style="margin-top:100px;">
    <p class="fw-bold text-white fs-5 mb-2">Saving goal: <?= htmlspecialchars($goalName) ?></p>

    <?php if (!empty($iconFilename)): ?>
      <div class="text-center mb-3">
        <div class="icon-option mx-auto">
          <img src="<?= htmlspecialchars($displayIconRel) ?>" alt="<?= htmlspecialchars($iconFilename) ?>" style="width:100%; height:100%; object-fit:contain;">
        </div>
      </div>
    <?php endif; ?>

    <label>Goal Amount</label>
    <div class="input-wrapper">
      <input type="number" name="goalAmount" placeholder="Enter amount" step="0.01" required>
      <span class="text-warning fw-bold">PHP</span>
    </div>

    <label>Current Balance</label>
    <div class="input-wrapper">
      <input type="number" name="currentBalance" placeholder="Enter balance" step="0.01" required>
      <span class="text-warning fw-bold">PHP</span>
    </div>

    <label>Target Date</label>
    <div class="input-wrapper">
      <input type="date" name="targetDate" required>
    </div>

    <p class="fw-bold text-white fs-5 mt-4 mb-3">Need a reminder for your savings?</p>
    <div class="d-flex justify-content-between align-items-center px-3 py-2 mb-3" style="height:56px; background:#F0f1f6; border-radius:12px;">
      <span class="fw-semibold text-dark">Enable Reminder</span>
      <div class="form-check form-switch m-0">
        <input class="form-check-input" type="checkbox" name="reminder" role="switch" checked>
      </div>
    </div>

    <label>Reminder Time</label>
    <div class="input-wrapper">
      <input type="time" name="reminderTime" value="22:00">
    </div>

    <label>Repeat Frequency</label>
    <div class="input-wrapper">
      <select name="repeatFrequency" class="border-0 bg-transparent fw-semibold w-100">
        <option value="daily">Every Day</option>
        <option value="weekly" selected>Every Week</option>
        <option value="biweekly">Every 2 Weeks</option>
        <option value="monthly">Every Month</option>
        <option value="yearly">Every Year</option>
      </select>
    </div>
  </div>

  <div class="fixed-footer">
    <div class="p-3">
      <button type="submit" name="btnAddGoalConfirmed" class="save-btn d-flex justify-content-center align-items-center w-100">Save</button>
    </div>
  </div>
</form>

<script>
  const saveBtn = document.querySelector(".save-btn");
  const inputs = document.querySelectorAll('input[name="goalAmount"], input[name="currentBalance"], input[name="targetDate"]');

  function checkFields(){
    const allFilled = Array.from(inputs).every(i => i.value.trim() !== "");
    saveBtn.style.pointerEvents = allFilled ? "auto" : "none";
    saveBtn.style.opacity = allFilled ? "1" : "0.6";
  }

  inputs.forEach(i => i.addEventListener("input", checkFields));
  checkFields();
</script>
</body>
</html>
