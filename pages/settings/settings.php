<?php
session_start();
include("../../assets/shared/connect.php");

if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}

$currentUser = $_SESSION['userID'];

// Load or initialize settings
$sql = "SELECT * FROM tbl_settings WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentUser);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();

if (!$settings) {
    $settings = [
        'currency' => 'Peso',
        'needs_wants' => '',
        'budgetRuleType' => 'suggested',
        'custom_budget' => json_encode([])
    ];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveCurrency'])) {
        $settings['currency'] = $_POST['currency'];
        $stmt = $conn->prepare("UPDATE tbl_settings SET currency = ? WHERE userID = ?");
        $stmt->bind_param("si", $settings['currency'], $currentUser);
        $stmt->execute();
    }

    if (isset($_POST['saveNeedsWants'])) {
        $settings['needs_wants'] = isset($_POST['needsWants']) ? implode(',', $_POST['needsWants']) : '';
        $stmt = $conn->prepare("UPDATE tbl_settings SET needs_wants = ? WHERE userID = ?");
        $stmt->bind_param("si", $settings['needs_wants'], $currentUser);
        $stmt->execute();
    }

    if (isset($_POST['saveBudgetRule'])) {
        $settings['budgetRuleType'] = $_POST['ruleType'];
        $customBudget = ($settings['budgetRuleType'] === 'custom') ? json_encode([
            'dining' => $_POST['dining'] ?? 0,
            'electricity' => $_POST['electricity'] ?? 0,
            'groceries' => $_POST['groceries'] ?? 0,
            'transport' => $_POST['transport'] ?? 0,
            'savings' => $_POST['savings'] ?? 0
        ]) : json_encode([]);
        $settings['custom_budget'] = $customBudget;

        $stmt = $conn->prepare("UPDATE tbl_settings SET budgetRuleType = ?, custom_budget = ? WHERE userID = ?");
        $stmt->bind_param("ssi", $settings['budgetRuleType'], $customBudget, $currentUser);
        $stmt->execute();
    }
}

$needsWantsArr = !empty($settings['needs_wants']) ? explode(',', $settings['needs_wants']) : [];
$customBudgetArr = !empty($settings['custom_budget']) ? json_decode($settings['custom_budget'], true) : [];

$cards = [
    ['title'=>'Currency','desc'=>($settings['currency']=='Dollar')?'US Dollar (USD)':'Philippine Peso (PHP)','modal'=>'currency'],
    ['title'=>'Needs & Wants','desc'=>'Manage spending categories','modal'=>'needsWants'],
    ['title'=>'Budget Rule','desc'=>'Change preferred budgeting method','modal'=>'budgetRule']
];

$categories = ['Dining Out','Electricity','Groceries','Rent'];
$budgetFields = ['dining'=>'Dining Out','electricity'=>'Electricity','groceries'=>'Groceries','transport'=>'Transportation','savings'=>'Savings'];
$currencies = ['Peso'=>'Philippine Peso (PHP)','Dollar'=>'US Dollar (USD)'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link rel="stylesheet" href="../../assets/css/home.css">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
</head>

  <style>
    .headerTitle {
    font-family: "Poppins", sans-serif !important;
    font-weight: 700; 
    font-size: 1.7rem !important; 
    color: #f3f3f3; 
    }

    body, div, p, label, input, button, select {
      font-family: 'Roboto', sans-serif !important;
      font-size: 16px;
    }

    .small, .text-small, .form-text, .text-muted {
      font-size: 12px !important;
    }

    .card, .modal-content, .rounded-3, .btn {
      border-radius: 20px !important;
    }

    .bg-green-custom {
      background-color: #44B87D;
    }

    .bg-yellow-custom {
      background-color: #F6D25B;
    }

    button:focus {
      outline: none !important;
      box-shadow: none !important;
    }
  </style>

<body>
  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>

  <!-- Sidebar content-->
  <?php include ("../../assets/shared/sideBar.php")?>

  <!-- Main Page Wrapper -->
  <div class="bg-green-custom min-vh-100 p-3" style="background-color: #44B87D;">

    <div id="overlay" class="d-none"></div>

          <!-- Settings Page Content -->
      <div class="container-fluid p-3 sticky-top" style="background-color: #44B87D; z-index: 999;">
    <h2 class="headerTitle">Settings</h2>
</div>

         <!-- Settings Cards -->
  <div class="mt-4 d-flex flex-column gap-3">
    <?php foreach($cards as $c): ?>
    <div class="d-flex justify-content-between align-items-center px-3 py-3 rounded-3" style="background-color:#F0f1f6;">
      <div>
        <div class="fw-bold text-dark"><?= $c['title'] ?></div>
        <div class="text-muted small"><?= $c['desc'] ?></div>
      </div>
      <button class="btn btn-sm fw-semibold px-3 bg-yellow-custom" onclick="openModal('<?= $c['modal'] ?>')">Edit</button>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Modals -->
<?php foreach($cards as $c):
    $id = $c['modal'].'Modal';
    echo "<div id='$id' class='position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none justify-content-center align-items-center' onclick=\"overlayClose(event,'{$c['modal']}')\">
          <div class='bg-green-custom p-4 rounded shadow' style='width:320px;' onclick='event.stopPropagation()'>";
    echo "<h5 class='fw-bold text-white mb-3'>Edit {$c['title']}</h5>";
    echo "<form method='POST'>";
    if($c['modal']=='currency') {
        echo "<select class='form-select mb-3' name='currency'>";
        foreach($currencies as $val=>$label){
            $sel = ($settings['currency']==$val)?'selected':'';
            echo "<option value='$val' $sel>$label</option>";
        }
        echo "</select><button type='submit' name='saveCurrency' class='btn w-100 fw-semibold bg-yellow-custom'>Save</button>";
    } elseif($c['modal']=='needsWants') {
    echo "<div class='p-2 rounded' style='background-color:#F0f1f6; border:2px solid #F6D25B;'>";
    // Header row
    echo "<div class='row fw-bold text-center mb-2'>
            <div class='col-6 text-start'>Expense</div>
            <div class='col-3'>Needs</div>
            <div class='col-3'>Wants</div>
          </div>";
    foreach($categories as $cat){
        $chk = in_array($cat,$needsWantsArr)?'checked':'';
        echo "<div class='row align-items-center mb-2'>
                <div class='col-6 text-start'>$cat</div>
                <div class='col-3 text-center'><input type='checkbox' name='needsWants[]' value='Needs_$cat' $chk></div>
                <div class='col-3 text-center'><input type='checkbox' name='needsWants[]' value='Wants_$cat' $chk></div>
              </div>";
    }
    echo "</div>
          <button type='submit' name='saveNeedsWants' class='btn w-100 fw-semibold bg-yellow-custom mt-2'>Save</button>";
    } elseif($c['modal']=='budgetRule') {
    $checkedSuggested = ($settings['budgetRuleType']=='suggested')?'checked':'';
    $checkedCustom = ($settings['budgetRuleType']=='custom')?'checked':'';

    echo "<div class='mb-3'>
            <div class='form-check'>
                <input class='form-check-input' type='radio' name='ruleType' id='suggestedRule' value='suggested' $checkedSuggested>
                <label class='form-check-label fw-semibold text-white' for='suggestedRule'>Use Suggested Rule</label>
            </div>
            <div class='form-check mt-2'>
                <input class='form-check-input' type='radio' name='ruleType' id='customRule' value='custom' $checkedCustom>
                <label class='form-check-label fw-semibold text-white' for='customRule'>Create My Own</label>
            </div>
          </div>
          <div id='customRules' class='".($checkedCustom?'':'d-none')."'>
            <h6 class='fw-semibold mb-2 text-white'>Custom Budget Rule</h6>
            <p class='small mb-1 text-white'>Enter percentage for each category:</p>";

    foreach($budgetFields as $f=>$label){
        $val = $customBudgetArr[$f] ?? '';
        echo "<input type='number' class='form-control mb-2' name='$f' placeholder='$label (%)' value='$val'>";
    }

    echo "<p class='small text-warning'>Make sure your total adds up to 100%</p></div>
          <button type='submit' name='saveBudgetRule' class='btn btn-warning w-100 fw-semibold mt-2'>Save</button>";
    }
    echo "</form></div></div>";
endforeach; ?>

<script>
function openModal(type){document.getElementById(type+'Modal').classList.remove('d-none');document.getElementById(type+'Modal').classList.add('d-flex');}
function closeModal(type){document.getElementById(type+'Modal').classList.remove('d-flex');document.getElementById(type+'Modal').classList.add('d-none');}
function overlayClose(e,type){if(e.target.id===type+'Modal') closeModal(type);}
</script>
</body>

</html>