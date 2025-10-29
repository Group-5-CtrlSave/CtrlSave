<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Savings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
  <style>
    body {
      overflow: hidden;
      background-color: #44B87D;
    }

    .bg-green-custom {
      position: fixed;
      top: 70px;
      left: 0;
      width: 100%;
      height: calc(100vh - 70px);
      background-color: #44B87D;
      padding: 1rem;
      display: flex;
      flex-direction: column;
    }

    .savings-header {
      flex-shrink: 0;
    }

    .savings-list {
      flex-grow: 1;
      overflow-y: auto;
      padding-top: 0.5rem;
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

    .savings-list {
      margin-bottom: 70px; 
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
  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>

  <!-- Sidebar -->
  <?php include ("../../assets/shared/sideBar.php") ?>

  <!-- Green Section -->
  <div class="bg-green-custom">
    <!-- Fixed Header -->
    <h2 class="savings-header fs-4 fw-bold text-white mb-3">My Savings</h2>

    <!-- Scrollable Cards -->
    <div class="savings-list">

      <a href="saving2.php" class="text-decoration-none">
        <div class="bg-white rounded-4 p-3 mb-3 d-flex align-items-center" style="height: 100px;">
          <div class="d-flex align-items-center w-100">
            <div class="me-3 d-flex align-items-center justify-content-center rounded-circle"
              style="background-color: #F0f1f6; width: 50px; height: 50px;">
              <img src="../../assets/img/shared/categories/Savings.png" alt="Piggy Bank"
                style="width: 30px; height: 30px;">
            </div>
            <div class="flex-grow-1">
              <p class="mb-0 fw-semibold text-dark text-truncate">Ipon ka gurl</p>
              <p class="mb-1 small text-truncate">
                <span class="fw-semibold" style="color: #44B87D;">P 12,500</span>
                <span class="text-muted"> / P25,000</span>
              </p>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar" role="progressbar" style="width: 50%; background-color: #F6D25B;"></div>
              </div>
            </div>
            <div class="ms-3 fw-medium small" style="color: #44B87D;">50%</div>
          </div>
        </div>
      </a>

      <a href="saving3.php" class="text-decoration-none">
        <div class="bg-white rounded-4 p-3 mb-3 d-flex align-items-center" style="height: 100px;">
          <div class="d-flex align-items-center w-100">
            <div class="me-3 d-flex align-items-center justify-content-center rounded-circle"
              style="background-color: #F0f1f6; width: 50px; height: 50px;">
              <img src="../../assets/img/shared/categories/expense/Car.png" alt="Car"
                style="width: 30px; height: 30px;">
            </div>
            <div class="flex-grow-1">
              <p class="mb-0 fw-semibold text-dark text-truncate">Car</p>
              <p class="mb-1 small text-truncate">
                <span class="fw-semibold" style="color: #44B87D;">P 225,000</span>
                <span class="text-muted"> / P225,000</span>
              </p>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #F6D25B;"></div>
              </div>
            </div>
            <div class="ms-3 fw-medium small" style="color: #44B87D;">100%</div>
          </div>
        </div>
      <!-- Add more cards here; they’ll scroll -->
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
