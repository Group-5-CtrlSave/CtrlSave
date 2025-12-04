<?php
session_start();

// CHECK LOGIN
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}

include '../../assets/shared/connect.php';
$userID = $_SESSION['userID'];

// Fetch user currency
$userQuery = "SELECT currencyCode FROM tbl_users WHERE userID = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$currentCurrency = $result['currencyCode'] ?? 'PHP';

// Check if user has a budget rule and determine its type
$budgetRuleQuery = "SELECT ruleName FROM tbl_userbudgetrule WHERE userID = ? AND isSelected = 1";
$stmt = $conn->prepare($budgetRuleQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$budgetRuleResult = $stmt->get_result()->fetch_assoc();
$hasSuggestedRule = false;
$hasCustomRule = false;

if ($budgetRuleResult) {
    // Check if the rule name matches any default budget rules (suggested rules)
    $defaultRuleCheck = "SELECT COUNT(*) as count FROM tbl_defaultbudgetrule WHERE ruleName = ?";
    $stmtCheck = $conn->prepare($defaultRuleCheck);
    $stmtCheck->bind_param("s", $budgetRuleResult['ruleName']);
    $stmtCheck->execute();
    $checkResult = $stmtCheck->get_result()->fetch_assoc();
    $hasSuggestedRule = ($checkResult['count'] > 0);
    $hasCustomRule = !$hasSuggestedRule; // If not suggested, it's custom
    $stmtCheck->close();
}
$stmt->close();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update Currency
    if (isset($_POST['updateCurrency'])) {
        $newCurrency = $_POST['currency'] ?? 'PHP';
        $updateQuery = "UPDATE tbl_users SET currencyCode = ? WHERE userID = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $newCurrency, $userID);
        $stmt->execute();
        $stmt->close();
        header("Location: settings.php");
        exit;
    }

    // Update Needs & Wants
    if (isset($_POST['updateNeedsWants'])) {
        $necessities = $_POST['necessity'] ?? [];
        if (!empty($necessities)) {
            $stmt = $conn->prepare("UPDATE tbl_usercategories SET userNecessityType = ? WHERE userCategoryID = ? AND userID = ?");
            foreach ($necessities as $catID => $type) {
                if (in_array($type, ['need', 'want'])) {
                    $stmt->bind_param("sii", $type, $catID, $userID);
                    $stmt->execute();
                }
            }
            $stmt->close();
            header("Location: settings.php");
            exit;
        }
    }
}

// Currency options
$currencies = ["PHP", "USD"];

// Fetch expense categories for Needs & Wants
$categoryQuery = "SELECT userCategoryID, categoryName, userNecessityType 
                  FROM tbl_usercategories 
                  WHERE userID = ? AND type = 'expense' AND isSelected = 1 
                  ORDER BY categoryName ASC";
$stmt = $conn->prepare($categoryQuery);
$stmt->bind_param("i", $userID);
$stmt->execute();
$categoryResult = $stmt->get_result();
$expenseCategories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $expenseCategories[] = $row;
}
$stmt->close();

// Cards - disable based on which rule type is active
$cards = [
    ["title" => "Currency", "desc" => "Select your preferred currency", "modal" => "currency"],
    ["title" => "Needs & Wants", "desc" => "Manage spending categories", "modal" => "needsWants"],
    ["title" => "Budget Rule", "desc" => "Change preferred budgeting method", "modal" => "budgetRule"],
    ["title" => "Suggested Budget Rule", "desc" => "Recommended budgeting method for you", "link" => "suggestedBudgetrule.php", "disabled" => $hasCustomRule],
    ["title" => "Custom Budget Rule", "desc" => "Create your own budgeting method", "link" => "customBudgetrule.php", "disabled" => $hasSuggestedRule]
];
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
<style>
.settings-card.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.settings-card.disabled .btn {
    pointer-events: none;
    opacity: 0.6;
}
</style>
</head>
<body>

<?php include ("../../assets/shared/navigationBar.php") ?>
<?php include ("../../assets/shared/sideBar.php") ?>

<div class="bg-green-custom" style="position: fixed; top: 72px; left: 0; width: 100%; height: calc(100vh - 72px); display: flex; flex-direction: column;">
    <div class="container-fluid p-3" style="background-color:#44B87D; flex-shrink: 0;">
        <div class="settings-container">
            <h2 class="headerTitle">Settings</h2>
        </div>
    </div>

    <div style="flex: 1; overflow-y: auto; overflow-x: hidden;">
        <div class="settings-container mt-4 mb-4 px-3">
            <div class="d-flex flex-column gap-3">
                <?php foreach ($cards as $c): ?>
                <div class="settings-card d-flex justify-content-between align-items-center px-4 py-3 rounded-3 <?= isset($c['disabled']) && $c['disabled'] ? 'disabled' : '' ?>" style="background-color:#F0f1f6;">
                    <div style="flex: 1; padding-right: 15px;">
                        <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($c["title"]) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($c["desc"]) ?></div>
                    </div>
                    <?php if (isset($c['link'])): ?>
                        <?php if (isset($c['disabled']) && $c['disabled']): ?>
                            <button class="btn btn-sm fw-semibold px-4 py-2 bg-yellow-custom" style="flex-shrink: 0; white-space: nowrap;" disabled>
                                Edit
                            </button>
                        <?php else: ?>
                            <a href="<?= $c['link'] ?>" class="btn btn-sm fw-semibold px-4 py-2 bg-yellow-custom" style="flex-shrink: 0; white-space: nowrap;">
                                Edit
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-sm fw-semibold px-4 py-2 bg-yellow-custom" style="flex-shrink: 0; white-space: nowrap;" onclick="openModal('<?= $c['modal'] ?>')">
                            Edit
                        </button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->

<!-- Currency Modal -->
<div id="currencyModal" class="modalOverlay" onclick="overlayClose(event,'currency')" style="backdrop-filter: none !important; -webkit-backdrop-filter: none !important;">
    <div class="modalBox" onclick="event.stopPropagation()">
        <h5 class="fw-bold text-white mb-3">Edit Currency</h5>
        <form method="POST" action="">
            <select class="form-select mb-3" name="currency" required>
                <?php foreach ($currencies as $code): ?>
                    <option value="<?= $code ?>" <?= $currentCurrency === $code ? 'selected' : '' ?>><?= $code ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="updateCurrency" class="btn w-100 fw-semibold bg-yellow-custom">Save Changes</button>
        </form>
    </div>
</div>

<!-- Needs & Wants Modal -->
<div id="needsWantsModal" class="modalOverlay" onclick="overlayClose(event,'needsWants')" style="backdrop-filter: none !important; -webkit-backdrop-filter: none !important;">
    <div class="modalBox" onclick="event.stopPropagation()">
        <h5 class="fw-bold text-white mb-3">Edit Needs & Wants</h5>
        <form method="POST" action="">
            <div class="needs-wants-table" style="max-height: 400px; overflow-y: auto;">
                <div class="row fw-bold text-center mb-2 pb-2" style="border-bottom: 2px solid #F6D25B; position: sticky; top: 0; background-color: #F0f1f6; z-index: 1;">
                    <div class="col-6 text-start">Expense</div>
                    <div class="col-3">Needs</div>
                    <div class="col-3">Wants</div>
                </div>
                <?php foreach ($expenseCategories as $cat): ?>
                    <div class="row align-items-center table-row">
                        <div class="col-6 text-start fw-medium"><?= htmlspecialchars($cat['categoryName']) ?></div>
                        <div class="col-3 text-center">
                            <input type="radio" name="necessity[<?= $cat['userCategoryID'] ?>]" value="need" <?= $cat['userNecessityType']==='need' ? 'checked' : '' ?>>
                        </div>
                        <div class="col-3 text-center">
                            <input type="radio" name="necessity[<?= $cat['userCategoryID'] ?>]" value="want" <?= $cat['userNecessityType']==='want' ? 'checked' : '' ?>>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" name="updateNeedsWants" class="btn w-100 fw-semibold bg-yellow-custom mt-3">Save Changes</button>
        </form>
    </div>
</div>

<!-- Budget Rule Modal -->
<div id="budgetRuleModal" class="modalOverlay" onclick="overlayClose(event,'budgetRule')" style="backdrop-filter: none !important; -webkit-backdrop-filter: none !important;">
    <div class="modalBox" onclick="event.stopPropagation()">
        <h5 class="fw-bold text-white mb-3">Edit Budget Rule</h5>
        <div class="form-check mb-2 p-3 rounded" style="background-color:#F0F1F6;">
            <input class="form-check-input" type="radio" name="ruleType" value="suggested" id="suggestedRule" <?= $hasSuggestedRule ? 'checked' : '' ?> onchange="redirectBudgetRule()">
            <label class="form-check-label fw-semibold text-dark" for="suggestedRule">Use Suggested Rule</label>
        </div>
        <div class="form-check p-3 rounded" style="background-color:#F0F1F6;">
            <input class="form-check-input" type="radio" name="ruleType" value="custom" id="customRule" <?= $hasCustomRule ? 'checked' : '' ?> onchange="redirectBudgetRule()">
            <label class="form-check-label fw-semibold text-dark" for="customRule">Create My Own</label>
        </div>
        <button type="button" class="btn w-100 fw-semibold bg-yellow-custom mt-3" onclick="closeModal('budgetRule')">Close</button>
    </div>
</div>

<script>
function openModal(type) {
    const modal = document.getElementById(type + "Modal");
    modal.classList.add("d-flex"); modal.classList.remove("d-none");
    document.body.style.overflow = 'hidden';
}
function closeModal(type) {
    const modal = document.getElementById(type + "Modal");
    modal.classList.remove("d-flex"); modal.classList.add("d-none");
    document.body.style.overflow = '';
}
function overlayClose(e, type) {
    if (e.target.id === type + "Modal") closeModal(type);
}
function redirectBudgetRule() {
    const suggested = document.getElementById('suggestedRule');
    const custom = document.getElementById('customRule');
    if (suggested.checked) window.location.href = 'suggestedBudgetrule.php';
    else if (custom.checked) window.location.href = 'customBudgetrule.php';
}
document.addEventListener('keydown', function(e){
    if(e.key==='Escape'){
        document.querySelectorAll('.modalOverlay.d-flex').forEach(m=>{
            m.classList.remove('d-flex'); m.classList.add('d-none');
        });
        document.body.style.overflow = '';
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>