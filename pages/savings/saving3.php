<!-- Complete Saving Goal Example -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Savings Detail</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
  <style>
    body {
      background-color: #44B87D;
      overflow: hidden;
    }

    .transactions-card {
      background-color: white;
      border-radius: 20px;
      padding: 1rem;
      height: 280px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .transactions-header {
      flex-shrink: 0;
      position: sticky;
      top: 0;
      background-color: white;
      z-index: 2;
    }

    .transactions-list {
      flex-grow: 1;
      overflow-y: auto;
    }

    .transactions-list::-webkit-scrollbar {
      width: 0;
      background: transparent;
    }

    .transactions-list {
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
  </style>
</head>

<body>
  <!-- Nav Bar -->
  <nav class="bg-white px-4 d-flex justify-content-between align-items-center shadow" style="height: 73px;">
    <a href="saving1.php" class="text-decoration-none">
      <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
    </a>
    <h5 class="m-0 fw-bold text-dark"></h5>
    <button class="btn p-0" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
      <img src="../../assets/img/savings/deleteIcon.png" alt="Delete" style="width: 24px;">
    </button>
  </nav>

  <!-- Main Content -->
  <div class="bg-green-custom min-vh-100 p-3" style="background-color: #44B87D;">
    <div class="text-center mb-3">
      <h2 class="fs-5 fw-bold text-white">Car</h2>
      <p class="text-white mb-2">P 225,000 / P 225,000</p>
    </div>

    <div class="bg-white rounded-circle mx-auto mb-3 d-flex justify-content-center align-items-center position-relative"
      style="width: 140px; height: 140px;">
      <img src="../../assets/img/shared/categories/expense/Car.png" alt="Car" style="width: 100px;">
      <div class="position-absolute top-50 start-50 translate-middle fw-bold" style="color: #000000ff;">100%</div>
    </div>

    <div class="text-center mb-3">
      <span class="badge bg-white text-success fw-semibold px-4 py-2 rounded-pill">Complete</span>
    </div>

    <!-- Transactions List -->
    <div class="transactions-card">
      <div class="transactions-header">
        <h5 class="fw-semibold mb-3">Transactions</h5>
      </div>

      <div class="transactions-list">
        <div class="d-flex justify-content-between border-bottom py-2">
          <small class="text-muted">May 07, 2025</small>
          <span class="text-success fw-medium">+ P4,166</span>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
          <small class="text-muted">May 07, 2025</small>
          <span class="text-success fw-medium">+ P4,166</span>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
          <small class="text-muted">May 07, 2025</small>
          <span class="text-success fw-medium">+ P4,166</span>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
          <small class="text-muted">May 07, 2025</small>
          <span class="text-success fw-medium">+ P4,166</span>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
          <small class="text-muted">May 08, 2025</small>
          <span class="text-success fw-medium">+ P2,000</span>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
          <small class="text-muted">May 09, 2025</small>
          <span class="text-success fw-medium">+ P2,000</span>
        </div>
        <div class="d-flex justify-content-between border-bottom py-2">
          <small class="text-muted">May 10, 2025</small>
          <span class="text-success fw-medium">+ P1,334</span>
        </div>
        <div class="d-flex justify-content-between py-2">
          <small class="text-muted">May 11, 2025</small>
          <span class="text-success fw-medium">+ P1,500</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Goal Modal -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4" style="background-color: #44B87D;">
        <div class="modal-header border-0 bg-white rounded-top">
          <h5 class="modal-title fw-bold mx-auto">Delete Goal</h5>
        </div>
        <div class="modal-body text-center text-white fs-5">
          Are you sure you want to delete this saving goal?
        </div>
        <div class="modal-footer border-0 bg-white rounded-bottom justify-content-center">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <a href="saving1.php" class="btn btn-danger px-4 rounded-pill">Delete</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
