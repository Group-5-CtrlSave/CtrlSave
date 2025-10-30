<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave</title>
  <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap');

    html,
    body {
      background-color: #44B87D;
      font-family: 'Roboto', sans-serif;
      height: 100%;
      overflow: hidden; 
    }

    h2 {
      font-family: "Poppins", sans-serif;
      font-weight: 700;
      font-size: 24px;
      color: #fff;
      text-align: center;
      margin-top: 10px;
    }

    .desc,
    label,
    .section-header,
    input,
    p,
    button {
      font-family: "Roboto", sans-serif;
      font-size: 16px;
    }

    .desc p {
      color: #ffffff;
      text-align: center;
      margin: 0 auto;
    }

    /* ✅ Navbar */
    nav {
      background-color: white;
      padding: 1rem;
      height: 72px;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .main-container {
      height: calc(100vh - 120px);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }

    .input-group {
      background-color: #F0F1F6;
      border-radius: 20px;
      padding: 0.7rem 1.2rem;
      margin: 0.4rem auto;
      width: 80%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: 500;
      border: 2px solid #F6D25B;
    }

    .input-group label {
      margin: 0;
      font-size: 16px;
      color: black;
      flex: 1;
      text-align: left;
    }

    .input-group input {
      border: none;
      background: transparent;
      border-radius: 5px;
      text-align: center;
      width: 60px;
      font-weight: bold;
      color: #44B87D;
    }

    .input-group input:focus {
      outline: none;
    }

    .section-header {
      color: white;
      font-weight: 500;
      font-size: 16px;
      text-align: center;
      margin-top: 0.8rem;
      margin-bottom: 0.3rem;
    }

    .btn {
      background-color: #F6D25B;
      color: black;
      text-align: center;
      width: 150px;
      font-size: 16px;
      font-weight: bold;
      font-family: "Poppins", sans-serif;
      border-radius: 30px;
      cursor: pointer;
      text-decoration: none;
      border: none;
      margin-top: 10px;
    }

    .btn:hover {
      box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24),
        0 17px 50px 0 rgba(0, 0, 0, 0.19);
    }

    #warning-msg {
      color: #fff;
      font-weight: bold;
      font-size: 14px;
    }

    /* ✅ Savings section */
    .savings-section {
      background-color: #44B87D;
      margin-top: 0.3rem;
    }

    .savings-section .input-group label {
      text-align: left;
      justify-content: flex-start;
    }
  </style>
</head>

<body>

  <!-- No Logo Navigation Bar -->
  <nav class="d-flex align-items-center justify-content-between position-relative shadow">
    <a href="settings.php" class="text-decoration-none">
      <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
    </a>
  </nav>

  <!-- Main Content -->
  <div class="container-fluid d-flex flex-column justify-content-between main-container">

    <!-- Title & Description -->
    <div class="row w-100">
      <div class="col-12 title mt-2">
        <h2>Create your own <br>budgeting rule</h2>
      </div>
      <div class="col-12 desc mt-2">
        <p>Set up your own budgeting rule</p>
      </div>
    </div>

    <!-- Inputs -->
    <div class="col-12">
      <div class="input-group">
        <label for="dining">Dining Out</label>
        <input type="text" id="dining" value="0" />
      </div>
      <div class="input-group">
        <label for="electricity">Electricity</label>
        <input type="text" id="electricity" value="0" />
      </div>
      <div class="input-group">
        <label for="groceries">Groceries</label>
        <input type="text" id="groceries" value="0" />
      </div>
      <div class="input-group">
        <label for="transportation">Transportation</label>
        <input type="text" id="transportation" value="0" />
      </div>
    </div>

    <!-- Savings Section -->
    <div class="savings-section mt-2 w-100">
      <p class="section-header text-center">How much do you want to save?</p>
      <div class="input-group">
        <label for="savings">Savings</label>
        <input type="text" id="savings" value="0" />
      </div>
    </div>

    <!-- Warning -->
    <div class="col-12 d-flex justify-content-center">
      <p id="warning-msg"></p>
    </div>

    <!-- Button -->
    <div class="col-12 d-flex justify-content-center">
      <button onclick="location.href='#'" type="submit" class="btn">Next</button>
    </div>

  </div>

  <!-- Script -->
  <script>
    const inputs = document.querySelectorAll('.input-group input');
    const warning = document.getElementById('warning-msg');
    const nextBtn = document.querySelector('.btn');

    function validateInputs() {
      let valid = true;
      inputs.forEach(input => {
        let val = input.value.trim().replace(/,/g, '');
        if (val === "" || isNaN(val)) valid = false;
      });

      if (!valid) {
        warning.textContent = `Please enter valid numbers only.`;
        nextBtn.disabled = true;
        nextBtn.style.opacity = 0.5;
      } else {
        warning.textContent = '';
        nextBtn.disabled = false;
        nextBtn.style.opacity = 1;
      }
    }

    function formatNumber(val) {
      val = val.replace(/[^0-9]/g, '');
      return val ? parseInt(val, 10).toLocaleString() : "";
    }

    inputs.forEach(input => {
      input.addEventListener('input', () => {
        let cursorPos = input.selectionStart;
        let beforeLength = input.value.length;

        input.value = formatNumber(input.value);

        let afterLength = input.value.length;
        input.selectionEnd = cursorPos + (afterLength - beforeLength);

        validateInputs();
      });

      input.addEventListener('blur', () => {
        if (input.value.trim() === "") {
          input.value = "0";
        }
        validateInputs();
      });
    });

    validateInputs();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
