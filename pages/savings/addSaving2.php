<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Goal Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
  <style>
    body {
      background-color: #77D09A;
    }

    .input-wrapper {
      background-color: white;
      border-radius: 12px;
      padding: 10px 16px;
      margin-bottom: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .input-wrapper input {
      border: none;
      background: transparent;
      outline: none;
      width: 100%;
      font-weight: 600;
    }

    .save-btn {
      background-color: #F6D25B;
      border: none;
      border-radius: 999px;
      height: 50px;
      font-size: 16px;
      font-weight: 600;
      width: 100%;
      pointer-events: none;
      opacity: 0.6;
      transition: opacity 0.3s ease;
    }

    .fixed-footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: white;
      z-index: 10;
    }

    .fixed-footer .progress-line {
      height: 4px;
      background-color: #F6D25B;
      width: 0%;
      animation: fillLine 1s ease-in-out forwards;
    }

    @keyframes fillLine {
      from {
        width: 50%;
      }

      to {
        width: 100%;
      }
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
      background-color: #F0f1f6;
      padding: 10px 16px;
      border-radius: 12px;
      height: 56px;
      margin-bottom: 1rem;
    }

    .input-wrapper input {
      background: transparent;
      border: none;
      outline: none;
      flex: 1;
    }

    .input-wrapper input::placeholder {
      color: #999;
    }

    .input-wrapper span,
    .input-wrapper img {
      margin-left: 10px;
    }

    .form-check-input:checked {
    background-color: #F6D25B;
    border-color: #F6D25B;
  }

  .form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(246, 210, 91, 0.4);
  }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="px-4 d-flex align-items-center justify-content-between position-relative"
    style="height: 72px; background-color: #F0f1f6;">
    <a href="saving1.php" class="text-decoration-none">
      <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
    </a>
    <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark">
      Add Goal
    </h5>
  </nav>

  <div class="px-4 pt-3 pb-5 mb-5" style="overflow-y: auto;">
    <p class="fw-bold text-white fs-5 mb-3">What are the details of your saving goal?</p>

    <!-- Goal Amount -->
    <label>Goal Amount</label>
    <div class="input-wrapper">
      <input type="text" id="goalAmount" placeholder="Enter amount" class="text-success">
      <span class="text-warning fw-bold">PHP</span>
    </div>

    <!-- Current Balance -->
    <label>Current Balance</label>
    <div class="input-wrapper">
      <input type="text" id="currentBalance" placeholder="Enter balance" class="text-success">
      <span class="text-warning fw-bold">PHP</span>
    </div>

    <!-- Target Date -->
    <label>Target Date</label>
    <div class="input-wrapper">
      <input type="date" id="targetDate" class="text-success">
    </div>

    <p class="fw-bold text-white fs-5 mt-4 mb-3">Need a reminder for your savings?</p>

    <!-- Enable Reminder Toggle -->
    <div class="d-flex justify-content-between align-items-center px-3 py-2 mb-3"
    style="height: 56px; background-color: #F0f1f6; border-radius: 12px;">
    <span class="fw-semibold text-dark">Enable Reminders</span>
    <div class="form-check form-switch m-0">
        <input class="form-check-input toggle-switch" type="checkbox" role="switch" checked>
    </div>
    </div>
    <!-- Time -->
    <label>Time</label>
    <div class="input-wrapper">
      <input type="time" id="reminderTime" value="22:00" class="text-success">
    </div>

    <!-- Repeat Frequency -->
    <label>Repeat Frequency</label>
    <div class="input-wrapper">
    <select id="repeatFrequency" class="text-success border-0 bg-transparent fw-semibold w-100">
        <option value="daily">Every Day</option>
        <option value="weekly" selected>Every Week</option>
        <option value="biweekly">Every 2 Weeks</option>
        <option value="monthly">Every Month</option>
        <option value="yearly">Every Year</option>
    </select>
    </div>
  </div>

  <!-- Fixed Save Button -->
  <div class="fixed-footer">
    <div class="progress-line"></div>
    <div class="p-3">
      <a href="saving1.php" class="d-block text-decoration-none">
        <button id="saveBtn" class="save-btn d-flex justify-content-center align-items-center w-100">
          Save
        </button>
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const saveBtn = document.getElementById("saveBtn");
    const requiredFields = [
      document.getElementById("goalAmount"),
      document.getElementById("currentBalance"),
      document.getElementById("targetDate")
    ];

    function checkFields() {
      let allFilled = requiredFields.every(function (input) {
        return input.value.trim() !== "";
      });

      if (allFilled) {
        saveBtn.style.pointerEvents = "auto";
        saveBtn.style.opacity = "1";
      } else {
        saveBtn.style.pointerEvents = "none";
        saveBtn.style.opacity = "0.6";
      }
    }

    requiredFields.forEach(function (input) {
      input.addEventListener("input", checkFields);
    });

    checkFields(); 
  </script>
</body>

</html>
