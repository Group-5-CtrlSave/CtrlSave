<?php
session_start();

//CHECK LOGIN
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}

//Include database connection
include '../../assets/shared/connect.php';

$userID = $_SESSION['userID'];

//Fetch user data
$userQuery = "SELECT currencyCode FROM tbl_users WHERE userID = '$userID'";
$userResult = executeQuery($userQuery);
$userData = mysqli_fetch_assoc($userResult);
$currentCurrency = $userData['currencyCode'] ?? 'PHP';

//HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Update Currency
    if (isset($_POST['updateCurrency'])) {
        $newCurrency = mysqli_real_escape_string($conn, $_POST['currency'] ?? 'PHP');
        
        $updateQuery = "UPDATE tbl_users SET currencyCode = '$newCurrency' WHERE userID = '$userID'";
        
        if (executeQuery($updateQuery)) {
            header("Location: settings.php");
            exit;
        }
    }
}

// ================= STATIC DATA =================
$cards = [
    ["title" => "Currency", "desc" => "Select your preferred currency", "modal" => "currency"],
    ["title" => "Needs & Wants", "desc" => "Manage spending categories", "modal" => "needsWants"],
    ["title" => "Budget Rule", "desc" => "Change preferred budgeting method", "modal" => "budgetRule"]
];

$categories = ["Dining Out", "Electricity", "Groceries", "Rent"];

$budgetFields = [
    "dining" => "Dining Out",
    "electricity" => "Electricity",
    "groceries" => "Groceries",
    "transport" => "Transportation",
    "savings" => "Savings"
];

$currencies = ["PHP", "USD"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings</title>

  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link rel="stylesheet" href="../../assets/css/home.css">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/settings.css">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>

  <!-- Sidebar -->
  <?php include ("../../assets/shared/sideBar.php") ?>

  <!-- Page Wrapper -->
  <div class="bg-green-custom" style="position: fixed; top: 72px; left: 0; width: 100%; height: calc(100vh - 72px); display: flex; flex-direction: column;">

    <!-- Header -->
    <div class="container-fluid p-3" style="background-color:#44B87D; flex-shrink: 0;">
      <div class="settings-container">
        <h2 class="headerTitle">Settings</h2>
      </div>
    </div>

    <!-- Scrollable Content Area -->
    <div style="flex: 1; overflow-y: auto; overflow-x: hidden;">

      <!-- Cards -->
      <div class="settings-container mt-4 mb-4 px-3">
        <div class="d-flex flex-column gap-3">
          <?php foreach ($cards as $c): ?>
          <div class="settings-card d-flex justify-content-between align-items-center px-4 py-3 rounded-3" style="background-color:#F0f1f6;">
            <div style="flex: 1; padding-right: 15px;">
              <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($c["title"]) ?></div>
              <div class="text-muted small"><?= htmlspecialchars($c["desc"]) ?></div>
            </div>
            <button class="btn btn-sm fw-semibold px-4 py-2 bg-yellow-custom" style="flex-shrink: 0; white-space: nowrap;" onclick="openModal('<?= $c['modal'] ?>')">Edit</button>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

  </div>

  <!-- ================= MODALS ================= -->

  <!-- CURRENCY MODAL -->
  <div id="currencyModal" class="modalOverlay" onclick="overlayClose(event,'currency')">
    <div class="modalBox" onclick="event.stopPropagation()">
      <h5 class="fw-bold text-white mb-3">Edit Currency</h5>

      <form method="POST" action="">
        <select class="form-select mb-3" name="currency" id="currencySelect" required>
          <?php foreach ($currencies as $code): ?>
            <option value="<?= $code ?>" <?= $currentCurrency === $code ? 'selected' : '' ?>>
              <?= $code ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" name="updateCurrency" class="btn w-100 fw-semibold bg-yellow-custom">
          Save Changes
        </button>
      </form>
    </div>
  </div>

  <!-- NEEDS & WANTS MODAL -->
  <div id="needsWantsModal" class="modalOverlay" onclick="overlayClose(event,'needsWants')">
    <div class="modalBox" onclick="event.stopPropagation()">

      <h5 class="fw-bold text-white mb-3">Edit Needs & Wants</h5>

      <div class="needs-wants-table">
        <div class="row fw-bold text-center mb-2 pb-2" style="border-bottom: 2px solid #F6D25B;">
          <div class="col-6 text-start">Expense</div>
          <div class="col-3">Needs</div>
          <div class="col-3">Wants</div>
        </div>

        <?php foreach ($categories as $cat): 
          $catKey = strtolower(str_replace(' ', '_', $cat));
        ?>
          <div class="row align-items-center table-row">
            <div class="col-6 text-start fw-medium"><?= htmlspecialchars($cat) ?></div>
            <div class="col-3 text-center">
              <input type="checkbox" name="needs[]" value="<?= $catKey ?>">
            </div>
            <div class="col-3 text-center">
              <input type="checkbox" name="wants[]" value="<?= $catKey ?>">
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <button type="button" class="btn w-100 fw-semibold bg-yellow-custom mt-3" onclick="closeModal('needsWants')">
        Save Changes
      </button>
    </div>
  </div>

  <!-- BUDGET RULE MODAL -->
  <div id="budgetRuleModal" class="modalOverlay" onclick="overlayClose(event,'budgetRule')">
    <div class="modalBox" onclick="event.stopPropagation()">

      <h5 class="fw-bold text-white mb-3">Edit Budget Rule</h5>

      <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="ruleType" value="suggested" id="suggestedRule" checked onchange="toggleCustomBudget()">
        <label class="form-check-label fw-semibold text-white" for="suggestedRule">
          Use Suggested Rule (50/30/20)
        </label>
      </div>

      <div class="form-check">
        <input class="form-check-input" type="radio" name="ruleType" value="custom" id="customRule" onchange="toggleCustomBudget()">
        <label class="form-check-label fw-semibold text-white" for="customRule">
          Create My Own
        </label>
      </div>

      <div class="budget-section" id="customBudgetSection" style="display: none;">
        <h6 class="fw-semibold mb-2 text-white">Custom Budget Rule</h6>
        <p class="small mb-3 text-white-50">Enter percentage for each category:</p>

        <?php foreach ($budgetFields as $key => $label): ?>
          <input type="number" 
                 class="form-control mb-2" 
                 name="budget_<?= $key ?>"
                 placeholder="<?= htmlspecialchars($label) ?> (%)"
                 min="0"
                 max="100"
                 id="budget_<?= $key ?>">
        <?php endforeach; ?>

        <p class="small text-warning fw-semibold mt-2 mb-0">
          ⚠ Make sure your total adds up to 100%
        </p>
      </div>

      <button type="button" class="btn w-100 fw-semibold bg-yellow-custom mt-3" onclick="closeModal('budgetRule')">
        Save Changes
      </button>
    </div>
  </div>

  <!-- ===== JS ===== -->
  <script>
    function openModal(type) {
      const modal = document.getElementById(type + "Modal");
      modal.classList.add("d-flex");
      modal.classList.remove("d-none");
      document.body.style.overflow = 'hidden';
    }

    function closeModal(type) {
      const modal = document.getElementById(type + "Modal");
      modal.classList.remove("d-flex");
      modal.classList.add("d-none");
      document.body.style.overflow = '';
    }

    function overlayClose(e, type) {
      if (e.target.id === type + "Modal") {
        closeModal(type);
      }
    }

    function toggleCustomBudget() {
      const customSection = document.getElementById('customBudgetSection');
      const customRadio = document.getElementById('customRule');
      
      if (customRadio.checked) {
        customSection.style.display = 'block';
      } else {
        customSection.style.display = 'none';
      }
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modalOverlay.d-flex');
        modals.forEach(modal => {
          modal.classList.remove('d-flex');
          modal.classList.add('d-none');
        });
        document.body.style.overflow = '';
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>