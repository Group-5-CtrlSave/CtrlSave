<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include("../../assets/shared/connect.php");

$userID = $_SESSION['userID'] ?? 0;
$error = "";

// ---------------- FETCH USER DATA ----------------

// Fetch categories that user has selected (isSelected = 1) and are expenses
$stmt = $conn->prepare("SELECT * FROM tbl_usercategories WHERE userID = ? AND isSelected = 1 AND type = 'expense'");
$stmt->bind_param("i", $userID);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get user's budget rule ID
$stmt = $conn->prepare("SELECT userBudgetRuleID FROM tbl_userbudgetrule WHERE userID = ? AND isSelected = 1");
$stmt->bind_param("i", $userID);
$stmt->execute();
$ruleResult = $stmt->get_result()->fetch_assoc();
$userBudgetRuleID = $ruleResult['userBudgetRuleID'] ?? null;
$stmt->close();

// Fetch user allocations (expenses + savings)
$allocations = [];
$allocMap = [];
if ($userBudgetRuleID) {
    $stmt = $conn->prepare("SELECT * FROM tbl_userallocation WHERE userBudgetruleID = ?");
    $stmt->bind_param("i", $userBudgetRuleID);
    $stmt->execute();
    $allocations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Map allocations for easy access
    foreach ($allocations as $alloc) {
        if ($alloc['userCategoryID']) {
            $allocMap[$alloc['userCategoryID']] = [
                'limitType' => $alloc['limitType'],
                'value' => $alloc['value'],
                'necessityType' => $alloc['necessityType']
            ];
        } else if ($alloc['necessityType'] === 'saving') {
            $allocMap['saving'] = $alloc['value'];
        }
    }
}

// ---------------- HANDLE FORM SUBMISSION ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    
    try {
        // Create or update user budget rule with custom name
        $customRuleName = "Custom Budget Rule";
        
        if ($userBudgetRuleID) {
            // Update existing rule
            $stmt = $conn->prepare("UPDATE tbl_userbudgetrule SET ruleName = ?, isSelected = 1 WHERE userBudgetRuleID = ?");
            $stmt->bind_param("si", $customRuleName, $userBudgetRuleID);
            $stmt->execute();
            $stmt->close();
        } else {
            // Create new rule
            $stmt = $conn->prepare("INSERT INTO tbl_userbudgetrule (userID, ruleName, createdAt, isSelected) VALUES (?, ?, NOW(), 1)");
            $stmt->bind_param("is", $userID, $customRuleName);
            $stmt->execute();
            $userBudgetRuleID = $conn->insert_id;
            $stmt->close();
        }

        // Delete existing allocations
        $stmt = $conn->prepare("DELETE FROM tbl_userallocation WHERE userBudgetruleID = ?");
        $stmt->bind_param("i", $userBudgetRuleID);
        $stmt->execute();
        $stmt->close();

        // Insert savings allocation
        $savingValue = intval(str_replace(',', '', $_POST['savings_value'] ?? 0));
        $stmt = $conn->prepare("INSERT INTO tbl_userallocation (userBudgetruleID, userCategoryID, necessityType, limitType, value) VALUES (?, NULL, 'saving', 0, ?)");
        $stmt->bind_param("ii", $userBudgetRuleID, $savingValue);
        $stmt->execute();
        $stmt->close();

        // Insert each category allocation
        foreach ($categories as $cat) {
            $id = $cat['userCategoryID'];
            $mode = $_POST["category_{$id}_mode"] ?? 'track';
            $value = intval(str_replace(',', '', $_POST["category_{$id}_value"] ?? 0));
            $limitType = ($mode === 'limit') ? 1 : 0;
            $necessityType = $cat['userNecessityType'] ?? 'need';

            $stmt = $conn->prepare("INSERT INTO tbl_userallocation (userBudgetruleID, userCategoryID, necessityType, limitType, value) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisii", $userBudgetRuleID, $id, $necessityType, $limitType, $value);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        header("Location: settings.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error saving budget rule: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Custom Budget Rule</title>
<link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
<link rel="stylesheet" href="../../assets/css/sideBar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color:#44B87D; font-family:'Roboto', sans-serif; margin:0; }
h2 { font-family:'Poppins',sans-serif;font-weight:700;color:white; }
p { color:white; }
.mainContainer { padding:15px; }
.titleContainer, .descContainer { text-align:center; margin-top:10px; }
.tableContainer { background-color:white; border:2px solid #F6D25B; border-radius:20px; padding:15px; }
.categories { font-weight:bold; margin-bottom:5px; }
.scrollable-container { overflow: visible; }
.expensesTab { display:flex; align-items:center; margin-bottom:12px; }
.amountForm { text-align:center; border:2px solid #F6D25B; width:80px; }
input[type="checkbox"] { accent-color:#F6D25B; width:17px; height:17px; cursor:pointer; }
.savingsContainer { background-color:white; border:2px solid #F6D25B; border-radius:20px; padding:10px; margin-top:15px; display:flex; justify-content:space-between; align-items:center; }
.savingsForm { text-align:center; width:135px; border:2px solid #F6D25B; }
.btn { background-color:#F6D25B; color:black; width:130px; font-weight:bold; border-radius:27px; margin-top:15px; }
.btn:hover:not(:disabled) { box-shadow:0 12px 16px rgba(0,0,0,0.24);}
.btn:disabled { opacity:0.6; cursor:not-allowed; }
.error { color: #ff4444; background: white; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>
</head>
<body>

<nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
    <div class="container-fluid position-relative">
        <a href="settings.php">
            <img src="../../assets/img/shared/BackArrow.png" alt="Back" style="height:24px;">
        </a>
    </div>
</nav>

<div class="container-fluid mainContainer">
<form method="POST" id="budgetRuleForm">
    <div class="titleContainer"><h2>Create Your Own Budget Rule</h2></div>
    <div class="descContainer"><p>Adjust your monthly spending limits</p></div>

    <?php if($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="tableContainer">
        <div class="row categories">
            <div class="col-4">Expenses</div>
            <div class="col-3 text-center">Track</div>
            <div class="col-5 text-center">Limit</div>
        </div>

        <div class="scrollable-container">
            <?php if(empty($categories)): ?>
            <div class="row expensesTab"><div class="col-12 text-center">No expense categories found.</div></div>
            <?php else: ?>
            <?php foreach($categories as $category):
                $id = $category['userCategoryID'];
                $name_base = "category_".$id;
                $alloc = $allocMap[$id] ?? ['limitType'=>0,'value'=>0];
                $default_track = ($alloc['limitType']==0)?'checked':'';
                $default_limit = ($alloc['limitType']==1)?'checked':'';
                $input_disabled = ($alloc['limitType']==0)?'disabled':'';
                $input_opacity = ($alloc['limitType']==0)?'style="opacity:0.5"':'';
            ?>
            <div class="row expensesTab" data-id="<?= $id ?>">
                <div class="col-4"><?= htmlspecialchars($category['categoryName']) ?></div>
                <div class="col-3 text-center">
                    <input type="checkbox" name="<?= $name_base ?>_mode_checkbox_track" value="track" <?= $default_track ?>>
                </div>
                <div class="col-1 text-center">
                    <input type="checkbox" name="<?= $name_base ?>_mode_checkbox_limit" value="limit" <?= $default_limit ?>>
                </div>
                <div class="col-4 text-center">
                    <input class="form-control form-control-sm amountForm limit-input" type="text" name="<?= $name_base ?>_value" placeholder="Amount" <?= $input_disabled ?> <?= $input_opacity ?> value="<?= $alloc['value'] ?>">
                </div>
                <input type="hidden" name="<?= $name_base ?>_mode" value="<?= ($alloc['limitType']==1)?'limit':'track' ?>">
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="descContainer"><p>How much do you want to save per month?</p></div>
    <div class="savingsContainer">
        <h5>Savings</h5>
        <input class="form-control form-control-sm savingsForm" type="text" name="savings_value" placeholder="Amount" required value="<?= $allocMap['saving'] ?? 0 ?>">
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-warning">Save Changes</button>
    </div>
</form>
</div>

<script>
function formatNumber(n){ let clean = n.replace(/[^0-9]/g,''); return clean?parseInt(clean,10).toLocaleString():''; }
function addFormattingListener(input){ input.addEventListener('input',()=>{ let start=input.selectionStart; let oldLen=input.value.length; input.value=formatNumber(input.value); input.selectionEnd=start+(input.value.length-oldLen); }); }
document.querySelectorAll('.limit-input, .savingsForm').forEach(addFormattingListener);

// Toggle track/limit checkboxes
document.querySelectorAll('.expensesTab').forEach(row=>{
    const id = row.getAttribute('data-id');
    const track = row.querySelector(`[name="category_${id}_mode_checkbox_track"]`);
    const limit = row.querySelector(`[name="category_${id}_mode_checkbox_limit"]`);
    const input = row.querySelector('.limit-input');
    const hidden = row.querySelector(`[name="category_${id}_mode"]`);

    function updateRow(src){
        if(src===track && track.checked){limit.checked=false; hidden.value='track'; input.disabled=true; input.style.opacity='0.5'; input.value='';}
        if(src===limit && limit.checked){track.checked=false; hidden.value='limit'; input.disabled=false; input.style.opacity='1';}
        if(!track.checked && !limit.checked){track.checked=true; hidden.value='track'; input.disabled=true; input.style.opacity='0.5'; input.value='';}
    }
    track.addEventListener('change',()=>updateRow(track));
    limit.addEventListener('change',()=>updateRow(limit));
    updateRow(limit.checked?limit:track);
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>