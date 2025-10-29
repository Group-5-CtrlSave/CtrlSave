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
    background-color: #44B87D;
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

  html, body {
    height: 100%;
    overflow: hidden; /* prevent body scroll */
  }

  .content-list {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
  }

  .content-list::-webkit-scrollbar {
    width: 0px;
    background: transparent;
  }

  .content-list {
    scrollbar-width: none; 
    -ms-overflow-style: none; 
  }

  .content-list::-webkit-scrollbar-thumb {
    background: transparent;
  }

</style>

</head>

<body>
  <!-- Navbar (Fixed) -->
  <nav class="px-4 d-flex align-items-center justify-content-between position-fixed top-0 w-100"
    style="height: 72px; background-color: #F0f1f6; z-index: 20;">
    <a href="addsaving1.php" class="text-decoration-none">
      <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
    </a>
    <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark">
      Add Goal
    </h5>
  </nav>

  <!-- Fixed Header Text -->
  <div class="px-4 pt-3 position-fixed w-100" 
       style="top: 72px; background-color: #44B87D; z-index: 15;">
    <p class="fw-bold text-white fs-5 mb-3">
      What are the details of your saving goal?
    </p>
  </div>

  <!-- Scrollable Content -->
  <div class="px-4 content-list" style="height: calc(100vh - 200px); margin-top: 160px; padding-bottom: 60px;">

    <!-- your form fields here -->
    <label>Goal Amount</label>
    <div class="input-wrapper">
      <input type="text" id="goalAmount" placeholder="Enter amount" class="text-success">
      <span class="text-warning fw-bold">PHP</span>
    </div>

    <label>Current Balance</label>
    <div class="input-wrapper">
      <input type="text" id="currentBalance" placeholder="Enter balance" class="text-success">
      <span class="text-warning fw-bold">PHP</span>
    </div>

    <label>Target Date</label>
    <div class="input-wrapper">
      <input type="date" id="targetDate" class="text-success">
    </div>

    <p class="fw-bold text-white fs-5 mt-4 mb-3">Need a reminder for your savings?</p>

    <div class="d-flex justify-content-between align-items-center px-3 py-2 mb-3"
      style="height: 56px; background-color: #F0f1f6; border-radius: 12px;">
      <span class="fw-semibold text-dark">Enable Reminders</span>
      <div class="form-check form-switch m-0">
        <input class="form-check-input toggle-switch" type="checkbox" role="switch" checked>
      </div>
    </div>

    <label>Time</label>
    <div class="input-wrapper">
      <input type="time" id="reminderTime" value="22:00" class="text-success">
    </div>

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
