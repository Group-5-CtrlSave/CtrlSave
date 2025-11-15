<?php
include '../../assets/shared/connect.php';
$savingGoalID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($savingGoalID <= 0) {
  header("Location: savingGoal.php");
  exit();
}

// Handle Add Amount
$errorMessage = ''; 

if (isset($_POST['add_amount'])) {
  $amount = (float)$_POST['amount'];
  $goalCheck = mysqli_query($conn, "SELECT currentAmount, targetAmount FROM tbl_savinggoals WHERE savingGoalID = $savingGoalID");
  $goalData = mysqli_fetch_assoc($goalCheck);
  $currentAmount = $goalData['currentAmount'];
  $targetAmount = $goalData['targetAmount'];
  $newAmount = $currentAmount + $amount;

  // Prevent exceeding or completed goalsy
  if ($currentAmount >= $targetAmount) {
    $errorMessage = "This goal is already complete.";
  } elseif ($newAmount > $targetAmount) {
    $errorMessage = "Amount exceeds your goal limit.";
  }

  // Only insert if no error
  if (empty($errorMessage)) {
    $insertQuery = "INSERT INTO tbl_goaltransactions (savingGoalID, amount, transaction, `date`)
                    VALUES ($savingGoalID, $amount, 'add', NOW())";
    mysqli_query($conn, $insertQuery);

    $updateQuery = "UPDATE tbl_savinggoals 
                    SET currentAmount = currentAmount + $amount 
                    WHERE savingGoalID = $savingGoalID";
    mysqli_query($conn, $updateQuery);

    header("Location: savingDetail.php?id=$savingGoalID");
    exit();
  }
}

// Handle Edit Goal Form Submit
if (isset($_POST['edit_goal'])) {
  $newGoalName = mysqli_real_escape_string($conn, $_POST['goal_name']);
  $newTargetAmount = (float)$_POST['target_amount'];

  $updateGoalQuery = "UPDATE tbl_savinggoals 
                      SET goalName = '$newGoalName', targetAmount = $newTargetAmount 
                      WHERE savingGoalID = $savingGoalID";
  mysqli_query($conn, $updateGoalQuery);
  header("Location: savingDetail.php?id=$savingGoalID");
  exit();
}

// Delete the goal
if (isset($_GET['delete']) && $_GET['delete'] == 1) {
  mysqli_query($conn, "DELETE FROM tbl_goaltransactions WHERE savingGoalID = $savingGoalID");
  mysqli_query($conn, "DELETE FROM tbl_savinggoals WHERE savingGoalID = $savingGoalID");
  header("Location: savingGoal.php");
  exit();
}

// Fetch Saving Goal Details
$query = "SELECT * FROM tbl_savinggoals WHERE savingGoalID = $savingGoalID";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $goal = mysqli_fetch_assoc($result);

  $goalName = htmlspecialchars($goal['goalName']);
  $targetAmount = (float)$goal['targetAmount'];
  $currentAmount = (float)$goal['currentAmount'];
  $progress = $targetAmount > 0 ? ($currentAmount / $targetAmount) * 100 : 0;
  $progress = min(100, max(0, $progress));
  $iconFile = trim($goal['icon'] ?? '');
  $icon = "../../assets/img/shared/categories/expense/" . ($iconFile !== '' ? htmlspecialchars($iconFile) : "Default.png");
  $isComplete = $progress >= 100;
} else {
  header("Location: savingGoal.php");
  exit();
}
?>

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
  <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
  <div class="container-fluid position-relative">
    <div class="d-flex align-items-start justify-content-start">
      <a href="savingGoal.php">
        <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;">
      </a>
    </div>
    <h5 class="m-0 fw-bold text-dark text-center"></h5>
    <button class="btn p-0 position-absolute end-0 top-0" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
      <img src="../../assets/img/savings/deleteIcon.png" alt="Delete" style="width: 24px;">
    </button>
  </div>
</nav>

  <!-- Main Content -->
  <div class="bg-green-custom min-vh-100 p-3" style="background-color: #44B87D;">
    <div class="text-center mb-3">
      <h2 class="fs-5 fw-bold text-white"><?php echo $goalName; ?></h2>
      <p class="text-white mb-2">
        P <?php echo number_format($currentAmount, 2); ?> / P <?php echo number_format($targetAmount, 2); ?>
      </p>
    </div>

    <div class="bg-white rounded-circle mx-auto mb-3 d-flex justify-content-center align-items-center position-relative"
      style="width: 140px; height: 140px;">
      <img src="<?php echo $icon; ?>" alt="Goal Icon" style="width: 100px;">
      <div class="position-absolute top-50 start-50 translate-middle fw-bold" style="color: #000000ff;">
        <?php echo $isComplete ? 'Complete' : round($progress) . '%'; ?>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex flex-column align-items-center mb-4">
      <?php if (!$isComplete): ?>
        <div class="text-center mb-2">
          <button class="btn fw-bold text-white" style="background-color: #F6D25B; border-radius: 40px; padding: 10px 30px;"
            data-bs-toggle="modal" data-bs-target="#addAmountModal">
            Add Amount
          </button>
        </div>
      <?php endif; ?>
      <button class="btn fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#editGoalModal"
        style="background-color: #fff; border-radius: 30px; padding: 6px 22px;">Edit Goal</button>
    </div>

    <!-- Transactions List -->
    <div class="transactions-card">
      <div class="transactions-header">
        <h5 class="fw-semibold mb-3">Transactions</h5>
      </div>
      <div class="transactions-list">
        <?php
        $transactionQuery = "SELECT * FROM tbl_goaltransactions WHERE savingGoalID = $savingGoalID ORDER BY `date` DESC";
        $transactionResult = mysqli_query($conn, $transactionQuery);

        if ($transactionResult && mysqli_num_rows($transactionResult) > 0) {
          while ($transaction = mysqli_fetch_assoc($transactionResult)) {
            $amount = number_format((float)$transaction['amount'], 2);
            $type = htmlspecialchars($transaction['transaction']); //add
            $date = date("M d, Y g:i A", strtotime($transaction['date']));
            $color = ($type === 'add') ? 'text-success' : 'text-danger';
            $sign = ($type === 'add') ? '+ ' : '- ';

            echo "
              <div class='d-flex justify-content-between border-bottom py-2'>
                <small class='text-muted'>$date</small>
                <span class='$color fw-medium'>$sign P$amount</span>
              </div>
            ";
          }
        } else {
          echo "<div class='d-flex justify-content-center align-items-center h-100'>
                  <p class='text-muted fw-semibold mb-0'>No transactions yet.</p>
                </div>";
        }
        ?>
      </div>
    </div>
  </div>

 <!-- Add Amount Modal -->
  <div class="modal fade" id="addAmountModal" tabindex="-1" aria-labelledby="addAmountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4" style="background-color: #44B87D;">
        <form method="POST" action="">
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
              <input 
                type="number" 
                name="amount" 
                step="0.01" 
                required 
                class="form-control border-0 bg-transparent fw-semibold text-black" 
                placeholder="0.00">
              <span class="input-group-text border-0 bg-transparent text-warning fw-bold">PHP</span>
            </div>

            <?php if (!empty($errorMessage)): ?>
              <p class="text-warning fw-semibold mt-1 mb-0">
                <?php echo htmlspecialchars($errorMessage); ?>
              </p>
            <?php endif; ?>
          </div>

          <div class="modal-footer border-0 bg-white rounded-bottom justify-content-center">
            <button type="submit" name="add_amount" class="btn fw-bold text-dark"
              style="background-color: #F6D25B; padding: 10px 30px; border-radius: 30px;">
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Goal Modal -->
  <div class="modal fade" id="editGoalModal" tabindex="-1" aria-labelledby="editGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4" style="background-color: #44B87D;">
        <form method="POST" action="">
          <div class="modal-header border-0 bg-white rounded-top">
            <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close">
              <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 24px; height: 24px;">
            </button>
            <h5 class="modal-title mx-auto fw-bold">Edit Goal</h5>
            <div style="width: 24px;"></div>
          </div>

          <div class="modal-body p-4">
            <label class="form-label fw-semibold text-white">Change Goal Name</label>
            <input type="text" name="goal_name" class="form-control mb-3 border-0 rounded-3" style="background-color: #F0f1f6;"
              value="<?php echo $goalName; ?>" required>

            <label class="form-label fw-semibold text-white">Change Goal Amount</label>
            <div class="input-group mb-4 rounded-3" style="background-color: #F0f1f6;">
              <input type="number" name="target_amount" step="0.01"
                class="form-control border-0 bg-transparent text-black fw-semibold"
                value="<?php echo $targetAmount; ?>" required>
              <span class="input-group-text border-0 bg-transparent text-warning fw-bold">PHP</span>
            </div>
          </div>

          <div class="modal-footer border-0 bg-white rounded-bottom justify-content-center">
            <button type="submit" name="edit_goal" class="btn fw-bold text-dark"
              style="background-color: #F6D25B; padding: 10px 30px; border-radius: 30px;">
              Save Changes
            </button>
          </div>
        </form>
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
          <a href="savingDetail.php?id=<?php echo $savingGoalID; ?>&delete=1" class="btn btn-danger px-4 rounded-pill">Delete</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.js"></script>
  
  <?php if (!empty($errorMessage)): ?>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const addAmountModal = new bootstrap.Modal(document.getElementById('addAmountModal'));
      addAmountModal.show();
    });
  </script>
<?php endif; ?>

</body>
</html>