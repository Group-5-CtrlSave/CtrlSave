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
        <h2 class="fs-5 fw-bold text-white">Ipon ka gurl</h2>
        <p class="text-white mb-2">P 12,500 / P 25,000</p>
        </div>

        <div class="bg-white rounded-circle mx-auto mb-3 d-flex justify-content-center align-items-center position-relative"
        style="width: 140px; height: 140px;">
        <img src="../../assets/img/shared/categories/Savings.png" alt="Piggy" style="width: 100px;">
        <div class="position-absolute top-50 start-50 translate-middle fw-bold" style="color: #000000ff;">50%</div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-column align-items-center mb-4">
        <div class="text-center mb-2">
    <button 
        class="btn fw-bold text-white" 
        style="background-color: #F6D25B; border-radius: 40px; padding: 10px 30px;"
        data-bs-toggle="modal" data-bs-target="#addAmountModal">
        Add Amount
    </button>
    </div>

      <button class="btn fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#editGoalModal"
        style="background-color: #fff; border-radius: 30px; padding: 6px 22px;">Edit Goal</button>
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

      <div class="d-flex justify-content-between border-bottom py-2">
        <small class="text-muted">May 11, 2025</small>
        <span class="text-success fw-medium">+ P1,100</span>
      </div>
    </div>
  </div>

  <!-- Add Amount Modal -->
  <div class="modal fade" id="addAmountModal" tabindex="-1" aria-labelledby="addAmountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4" style="background-color: #44B87D;">
        <div class="modal-header border-0 bg-white rounded-top">
          <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 24px; height: 24px;">
          </button>
          <h5 class="modal-title mx-auto fw-bold">Add Amount</h5>
          <div style="width: 24px;"></div>
        </div>
        <div class="modal-body p-4">
          <label class="form-label fw-semibold text-white">Amount</label>
          <div class="input-group mb-3 rounded-3" style="background-color: #F0f1f6;">
            <input type="number" class="form-control border-0 bg-transparent fw-semibold text-black" placeholder="0.00">
            <span class="input-group-text border-0 bg-transparent text-warning fw-bold">PHP</span>
          </div>

          <label class="form-label fw-semibold text-white">Date</label>
          <div class="input-group mb-4 rounded-3" style="background-color: #F0f1f6;">
            <input type="date" class="form-control border-0 bg-transparent text-success fw-semibold">
          </div>
        </div>
        <div class="modal-footer border-0 bg-white rounded-bottom justify-content-center">
          <button class="btn fw-bold text-dark"
            style="background-color: #F6D25B; padding: 10px 30px; border-radius: 30px;">
            Save
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Goal Modal -->
  <div class="modal fade" id="editGoalModal" tabindex="-1" aria-labelledby="editGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4" style="background-color: #44B87D;">
        <div class="modal-header border-0 bg-white rounded-top">
          <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 24px; height: 24px;">
          </button>
          <h5 class="modal-title mx-auto fw-bold">Edit Goal</h5>
          <div style="width: 24px;"></div>
        </div>
        <div class="modal-body p-4">
          <label class="form-label fw-semibold text-white">Change Goal Name</label>
          <input type="text" class="form-control mb-3 border-0 rounded-3" style="background-color: #F0f1f6;"
            placeholder="Enter goal name">

          <label class="form-label fw-semibold text-white">Change Goal Amount</label>
          <div class="input-group mb-4 rounded-3" style="background-color: #F0f1f6;">
            <input type="number" class="form-control border-0 bg-transparent text-black fw-semibold"
              placeholder="0.00">
            <span class="input-group-text border-0 bg-transparent text-warning fw-bold">PHP</span>
          </div>
        </div>
        <div class="modal-footer border-0 bg-white rounded-bottom justify-content-center">
          <button class="btn fw-bold text-dark"
            style="background-color: #F6D25B; padding: 10px 30px; border-radius: 30px;">
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Goal Modal -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
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
