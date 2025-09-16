<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Savings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
</head>

<body>

  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>
  <!-- Sidebar content-->
  <?php include ("../../assets/shared/sideBar.php")?>

  <!-- Main Content -->
  <div class="bg-green-custom min-vh-100 p-3">
    <h2 class="fs-4 fw-bold mb-4 text-white">My Savings</h2>

    <a href="saving2.php" class="text-decoration-none">
      <div class="bg-white rounded-4 p-3 mb-3 d-flex align-items-center" style="height: 100px;">
        <div class="d-flex align-items-center w-100">
          <div class="me-3 d-flex align-items-center justify-content-center rounded-circle"
            style="background-color: #F0f1f6; width: 50px; height: 50px;">
            <img src="../../assets/img/shared/categories/Savings.png" alt="Piggy Bank" style="width: 30px; height: 30px;">
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
            <img src="../../assets/img/shared/categories/expense/Car.png" alt="House" style="width: 30px; height: 30px;">
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
    </a>
  </div>

  <!-- Plus Button -->
  <a href="addSaving1.php">
  <div style="
    position: fixed;
    bottom: 1rem;
    right: 1rem;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #F6D25B;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  ">
    <span style="color: white; font-size: 24px; font-weight: bold;">+</span>
  </div>
</a>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>