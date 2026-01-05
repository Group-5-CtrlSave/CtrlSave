<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}
include '../../assets/shared/connect.php';

// Currency from session
$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'PHP') ? '₱' : '$';

if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit();
}

$userID = $_SESSION['userID'];
$error = "";
$show_tracking_prompt = false;

/* ----------------------------------------------------------
   FETCH ACTIVE USER BUDGET RULE
----------------------------------------------------------- */
$sql_rule = "SELECT userBudgetRuleID, ruleName FROM tbl_userbudgetrule WHERE userID = ? AND isSelected = 1 LIMIT 1";
$stmt_rule = $conn->prepare($sql_rule);
$stmt_rule->bind_param("i", $userID);
$stmt_rule->execute();
$res_rule = $stmt_rule->get_result();
$currentRule = $res_rule->fetch_assoc();
$stmt_rule->close();

if (!$currentRule) {
    die("Fatal Error: No active budget rule found.");
}

$currentRuleID = $currentRule['userBudgetRuleID'];
$currentRuleName = $currentRule['ruleName'];
$isCustomRule = ($currentRuleName === "Custom Budget Rule");

/* ----------------------------------------------------------
   FETCH EXPENSE CATEGORIES
----------------------------------------------------------- */
$categories = [];
$sql_cat = "
    SELECT userCategoryID, categoryName, userisFlexible, userNecessityType
    FROM tbl_usercategories
    WHERE userID = ? AND type = 'expense' AND isSelected = 1
";
$stmt_cat = $conn->prepare($sql_cat);
$stmt_cat->bind_param("i", $userID);
$stmt_cat->execute();
$res_cat = $stmt_cat->get_result();

while ($row = $res_cat->fetch_assoc()) {
    $categories[] = $row;
}
$stmt_cat->close();

/* ----------------------------------------------------------
   FETCH EXISTING ALLOCATIONS FOR PREFILLING
----------------------------------------------------------- */
$allocMap = [];
$savingsAlloc = null;

$sql_alloc = "SELECT * FROM tbl_userallocation WHERE userBudgetRuleID = ?";
$stmt_alloc = $conn->prepare($sql_alloc);
$stmt_alloc->bind_param("i", $currentRuleID);
$stmt_alloc->execute();
$res_alloc = $stmt_alloc->get_result();

while ($alloc = $res_alloc->fetch_assoc()) {
    if ($alloc["necessityType"] === "saving") {
        $savingsAlloc = $alloc; // ONLY for custom rules
    } else {
        $allocMap[$alloc["userCategoryID"]] = $alloc;
    }
}
$stmt_alloc->close();

/* ----------------------------------------------------------
   FETCH TOTAL INCOME
----------------------------------------------------------- */
$totalIncome = 0;

$sql_income = "
SELECT totalIncome 
FROM tbl_userbudgetversion 
WHERE userID = ? AND isActive = 1 
ORDER BY userBudgetversionID DESC LIMIT 1";

$stmt_income = $conn->prepare($sql_income);
$stmt_income->bind_param("i", $userID);
$stmt_income->execute();
$res_income = $stmt_income->get_result();
if ($row = $res_income->fetch_assoc()) {
    $totalIncome = (float) $row['totalIncome'];
}
$stmt_income->close();

/* Make income available to JS */
echo "<script>window.userIncome = {$totalIncome};</script>";

/* ----------------------------------------------------------
   PREFILL SAVINGS
----------------------------------------------------------- */

if ($isCustomRule && $savingsAlloc) {
    // Prefill saved amount (custom rule)
    $prefilledSavings = number_format($savingsAlloc["value"]);
} else {
    // DEFAULT RULE → Savings = 20% of income
    $defaultPercent = 20;
    $prefilledSavings = number_format(($defaultPercent / 100) * $totalIncome);
}

/* ----------------------------------------------------------
   Utility: clean currency
----------------------------------------------------------- */
function clean_currency($v)
{
    return preg_replace('/[^0-9.]/', '', $v);
}

/* ----------------------------------------------------------
   HANDLE FORM SUBMISSION
----------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $valid = true;
    $allocations = [];
    $limitCount = 0;
    $allTracking = true;

    /* ---------------- EXPENSE CATEGORIES ---------------- */
    foreach ($categories as $cat) {

        $id = $cat['userCategoryID'];
        $mode = $_POST["category_{$id}_mode"] ?? "track";
        $valueRaw = $_POST["category_{$id}_value"] ?? "";
        $cleanValue = clean_currency($valueRaw);

        $entry = [
            "userCategoryID" => $id,
            "necessityType" => $cat["userNecessityType"],
            "limitType" => ($mode === "limit") ? 1 : 0,
            "value" => 0
        ];

        if ($mode === "limit") {
            $allTracking = false;
            $limitCount++;

            if ($cleanValue === "" || !is_numeric($cleanValue) || floatval($cleanValue) <= 0) {
                $error = "Limit for " . htmlspecialchars($cat['categoryName']) . " must be a positive number.";
                $valid = false;
                break;
            }
            $entry["value"] = floatval($cleanValue);
        }

        $allocations[] = $entry;
    }

    /* ---------------- SAVINGS ---------------- */
    $savingsRaw = $_POST["savings_value"] ?? "";
    $cleanSavings = clean_currency($savingsRaw);

    if ($cleanSavings === "" || !is_numeric($cleanSavings) || floatval($cleanSavings) <= 0) {
        $error = "Savings must be a positive amount.";
        $valid = false;
    } else {
        $allocations[] = [
            "userCategoryID" => null,
            "necessityType" => "saving",
            "limitType" => 1,
            "value" => floatval($cleanSavings)
        ];
        $limitCount++;
    }

    /* ---------------- TRACKING ONLY PROMPT ---------------- */
    if ($valid && $allTracking && $limitCount === 1) {
        if (!isset($_POST["tracking_confirm"])) {
            $show_tracking_prompt = true;
            $valid = false;
        }
    }

    /* ---------------- INCOME VALIDATION ---------------- */
    if ($valid && !$show_tracking_prompt) {
        if ($totalIncome <= 0) {
            $error = "Please set a valid monthly income before creating limits.";
            $valid = false;
        } else {
            $sumLimits = 0;
            foreach ($allocations as $a) {
                if ($a["limitType"] == 1)
                    $sumLimits += $a["value"];
            }

            if ($sumLimits > $totalIncome) {
                $error = "Your limits exceed your monthly income.";
                $valid = false;
            }
        }
    }

    /* ----------------------------------------------------------
       SAVE CHANGES (UPDATE MODE)
    ----------------------------------------------------------- */
    if ($valid && !$show_tracking_prompt) {

        $conn->begin_transaction();

        try {

            /* UPDATE rule name */
            $sql_ruleUpdate = "UPDATE tbl_userbudgetrule 
                               SET ruleName='Custom Budget Rule', isSelected=1 
                               WHERE userBudgetRuleID=?";
            $stmt = $conn->prepare($sql_ruleUpdate);
            $stmt->bind_param("i", $currentRuleID);
            $stmt->execute();
            $stmt->close();

            /* UPDATE category modes */
            $sql_updateCat = "UPDATE tbl_usercategories 
                              SET userisFlexible=? 
                              WHERE userCategoryID=? AND userID=?";
            $stmt_upd = $conn->prepare($sql_updateCat);

            foreach ($categories as $cat) {
                $id = $cat["userCategoryID"];
                $mode = $_POST["category_{$id}_mode"] ?? "track";
                $flex = ($mode === "limit") ? 1 : 0;

                $stmt_upd->bind_param("iii", $flex, $id, $userID);
                $stmt_upd->execute();
            }

            $stmt_upd->close();

            /* DELETE old allocations */
            $sql_del = "DELETE FROM tbl_userallocation WHERE userBudgetRuleID=?";
            $stmt_del = $conn->prepare($sql_del);
            $stmt_del->bind_param("i", $currentRuleID);
            $stmt_del->execute();
            $stmt_del->close();

            /* INSERT updated allocations */
            $sql_ins = "
                INSERT INTO tbl_userallocation 
                (userBudgetRuleID, userCategoryID, necessityType, limitType, value)
                VALUES (?, ?, ?, ?, ?)
            ";
            $stmt_ins = $conn->prepare($sql_ins);

            foreach ($allocations as $a) {
                $stmt_ins->bind_param(
                    "iisid",
                    $currentRuleID,
                    $a["userCategoryID"],
                    $a["necessityType"],
                    $a["limitType"],
                    $a["value"]
                );
                $stmt_ins->execute();
            }
            $stmt_ins->close();

            $conn->commit();
            header("Location: settings.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to save changes: " . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CtrlSave | Own Budget Rule</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
        /* --- Error Toast CSS --- */
        #errorToast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #E63946;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            width: 300px;
            font-family: "Poppins", sans-serif;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
            animation: fadeInOut 3s ease forwards;
            text-align: center;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }

            10% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            70% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }
        }

        body {
            background-color: #44B87D;
            overflow: hidden;
        }

        /* Title & Description */

        .titleContainer {
            margin-top: 10px;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .descContainer {
            margin-top: 10px;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h2 {
            color: white;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
        }

        p {
            color: white;
            font-family: "Roboto", sans-serif;
            font-size: 16px;
        }

        /* White Table Content */
        .tableContainer {
            background-color: white;
            border: 2px solid #F6D25B;
            border-radius: 20px;
        }

        /* Categories Section */
        .categories {
            margin-top: 15px;
        }

        .expenses {
            color: black;
            justify-content: start;
            align-items: start;
            text-align: start;
        }

        .titleCateg {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            font-size: 20px;
        }

        .track {
            color: black;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .limit {
            color: black;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .scrollable-container {
            overflow-y: auto;
            height: 200px;
            overflow-x: hidden;
            margin-top: 1px;
        }

        /* Expense Css */

        .expensesTab {
            margin-top: 20px;
            height: 40px;
        }

        .expenseName {
            color: black;
            font-size: 16px;
            font-family: "Roboto", sans-serif;
            font-weight: 500px;
        }

        .checkboxCol {
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .labelLimit {
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        input[type="checkbox"] {
            accent-color: #F6D25B;
            width: 17px;
            height: 17px;
            cursor: pointer;
            position: relative;
            justify-content: center;
            align-items: center;
            text-align: center;
            margin-top: 7px;
        }

        .amountForm {
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 2px solid #F6D25B;
            width: 80px;
            font-family: "Roboto", sans-serif;
        }

        /* Savings Css */
        .savingsContainer {
            background-color: white;
            border: 2px solid #F6D25B;
            border-radius: 20px;
            height: 50px;
        }

        .savingsTab {
            margin-top: 8px;
        }

        .savingsCol {
            margin-top: 3px;
        }

        .savings {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            text-align: start;
        }

        .savingsForm {
            text-align: center;
            border: 2px solid #F6D25B;
            width: 135px;
            font-family: "Roboto", sans-serif;
        }

        /* Button */
        .btn {
            background-color: #F6D25B;
            color: black;
            border-radius: 27px;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            margin-top: 20px;
        }

        /* Modal/Prompt Styling */
        .prompt-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .prompt-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
            text-align: center;
        }

        .prompt-content button {
            margin: 10px;
        }

        /* Media Queries of Every Mobile Screen */
        @media screen and (min-width:375px) {
            .scrollable-container {
                height: 200px;
            }
        }

        @media screen and (min-width:414px) {
            .scrollable-container {
                height: 370px;
            }
        }

        @media screen and (min-width:390px) {
            .scrollable-container {
                height: 300px;
            }
        }

        @media screen and (min-width:430px) {
            .scrollable-container {
                height: 330px;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($error) && !empty($error)) { ?>
        <div id="errorToast">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <div id="trackingPromptModal" class="prompt-modal"
        style="display: <?php echo $show_tracking_prompt ? 'block' : 'none'; ?>;">
        <div class="prompt-content">
            <p style="color:black;">
                ⚠️ You haven’t set any budget limits for your expenses.<br>
                Do you want to continue in **Tracking Only** mode (no budget caps)?
            </p>
            <form method="POST" id="trackingForm">
                <input type="hidden" name="tracking_confirm" value="1">
            </form>
            <button type="button" class="btn btn-secondary" onclick="closePrompt()">Go Back</button>
            <button type="button" class="btn btn-warning" onclick="confirmTracking()">Continue</button>
        </div>
    </div>


    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="settings.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

        </div>
    </nav>

    <div class="container-fluid mainContainer">

        <form method="POST" id="budgetRuleForm">

            <div class="titleContainer">
                <h2>
                    Edit Track or Limit
                </h2>
            </div>

            <div class="descContainer">
                <p>
                    If you chose limit always provide a limit amount
                <p>
            </div>

            <!-- CURRENT BALANCE ONLY -->
            <div id="balanceBox"
                style="padding:12px;margin-bottom:15px;border:2px solid #F6D25B;border-radius:12px;background:white;">
                <strong>Current Balance:</strong>
                <span id="currentBalance" style="color:green;"><?php echo $symbol; ?>0</span>
            </div>


            <div class="container tableContainer">

                <div class="row categories">
                    <div class="col-4 expenses">
                        <h5 class="titleCateg">
                            Expenses
                        </h5>
                    </div>

                    <div class="col-3 track">
                        <h5 class="titleCateg">
                            Track
                        </h5>
                    </div>

                    <div class="col-5 limit">
                        <h5 class="titleCateg">
                            Limit
                        </h5>
                    </div>
                </div>

                <div class="scrollable-container">
                    <?php if (empty($categories)): ?>
                        <div class="row expensesTab">
                            <div class="col-12 text-center">
                                <p class="expenseName" style="color:black;">No expense categories are selected for
                                    budgeting.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($categories as $category):
                            $id = $category['userCategoryID'];
                            $name_base = "category_" . $id;
                            // PRE-FILL Checkboxes based on userisFlexible: 0 (Track) or 1 (Limit)
                            $default_track = $category['userisFlexible'] == 0 ? 'checked' : '';
                            $default_limit = $category['userisFlexible'] == 1 ? 'checked' : '';
                            // Initial state of input box
                            $input_disabled = $category['userisFlexible'] == 0 ? 'disabled' : '';
                            $input_opacity = $category['userisFlexible'] == 0 ? 'style="opacity:0.5"' : '';
                            ?>
                            <div class="row expensesTab" data-id="<?= $id ?>">
                                <div class="col-4 expensesCol">
                                    <p class="expenseName">
                                        <?= htmlspecialchars($category['categoryName']) ?>
                                    </p>
                                    <input type="hidden" name="<?= $name_base ?>_necessity"
                                        value="<?= htmlspecialchars($category['userNecessityType']) ?>">
                                </div>

                                <div class="col-3 checkboxCol">
                                    <input type="checkbox" name="<?= $name_base ?>_mode_checkbox_track" value="track"
                                        <?= $default_track ?>>
                                </div>

                                <div class="col-1 checkboxCol">
                                    <input type="checkbox" name="<?= $name_base ?>_mode_checkbox_limit" value="limit"
                                        <?= $default_limit ?>>
                                </div>

                                <div class="col-4 labelLimit">
                                    <?php
                                    $prefill = isset($allocMap[$id]) ? number_format($allocMap[$id]['value']) : '';
                                    ?>
                                    <input class="form-control form-control-sm amountForm limit-input" type="text"
                                        name="<?= $name_base ?>_value" placeholder="Amount" value="<?= $prefill ?>"
                                        data-limit-input data-catid="<?= $id ?>" <?= $input_disabled ?>         <?= $input_opacity ?>>
                                </div>

                                <input type="hidden" name="<?= $name_base ?>_mode"
                                    value="<?= $category['userisFlexible'] == 0 ? 'track' : 'limit' ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>

            <div class="descContainer">
                <p>
                    How much do you want to save per month?
                <p>
            </div>

            <div class="container savingsContainer">

                <div class="row savingsTab">
                    <div class="col-6 savingsCol">
                        <h5 class="savings">
                            Savings
                        </h5>
                    </div>

                    <div class="col-6 savingsLimit">
                        <?php
                        $savePrefill = $savingsAlloc ? number_format($savingsAlloc['value']) : '';
                        ?>
                        <input class="form-control form-control-sm savingsForm" type="text" name="savings_value"
                            placeholder="Amount" value="<?= $prefilledSavings ?>" data-limit-input data-catid="savings"
                            required>



                    </div>
                </div>

            </div>

            <div class="container buttonContainer">

                <div class="col-12 btnNext d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-warning mb-3" id="nextButton" disabled>Save changes</button>
                </div>

            </div>
        </form>
    </div>

    <script>
        const currencySymbol = "<?= $symbol ?>";
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // Total income output by backend in percentageBE.php
            const income = window.userIncome ?? 0;

            const balanceEl = document.getElementById("currentBalance");

            function recalcBalance() {
                let remaining = income;

                // Loop through all limit-type inputs
                document.querySelectorAll("[data-limit-input]").forEach(input => {

                    let catid = input.dataset.catid;

                    // Savings always uses limit mode
                    let mode = "limit";

                    // For categories, use their hidden mode input
                    if (catid !== "savings") {
                        let modeInput = document.querySelector(`input[name="category_${catid}_mode"]`);
                        mode = modeInput ? modeInput.value : "track";
                    }

                    if (mode === "limit") {
                        let clean = input.value.replace(/[^0-9.]/g, "");
                        let amount = parseFloat(clean) || 0;
                        remaining -= amount;
                    }
                });

                balanceEl.innerText = currencySymbol + remaining.toLocaleString();

                balanceEl.style.color = (remaining < 0) ? "red" : "green";
            }

            // Recalculate on typing
            document.querySelectorAll("[data-limit-input]").forEach(input => {
                input.addEventListener("input", recalcBalance);
            });

            // Recalculate when switching Track / Limit mode
            document.querySelectorAll("input[name$='_mode_checkbox_track'], input[name$='_mode_checkbox_limit']")
                .forEach(chk => {
                    chk.addEventListener("change", recalcBalance);
                });

            recalcBalance(); // Initial calculation
        });
    </script>


    <script>

        function formatNumber(n) {
            let clean = n.replace(/[^0-9.]/g, '');
            let parts = clean.split('.');
            let integerPart = parts[0];
            let decimalPart = parts.length > 1 ? '.' + parts.slice(1).join('') : '';

            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            return integerPart + decimalPart;
        }

        function addFormattingListener(inputElement) {
            inputElement.addEventListener('input', function () {
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const oldLength = this.value.length;

                this.value = formatNumber(this.value);

                const newLength = this.value.length;
                const diff = newLength - oldLength;

                const newStart = Math.min(start + diff, newLength);
                const newEnd = Math.min(end + diff, newLength);

                this.setSelectionRange(newStart, newEnd);

                // Call validation after input
                checkFormValidity();
            });
        }

        // Checkbox & Input Logic (Modified to use unique names and call validation) 
        const nextButton = document.getElementById('nextButton');

        function checkFormValidity() {
            let isValid = true;
            let totalExpenses = 0;

            // 1. Check Savings Input
            const savingsInput = document.querySelector('.savingsForm');
            const cleanSavings = savingsInput ? savingsInput.value.replace(/[^0-9.]/g, '') : '0';
            if (!savingsInput || cleanSavings.length === 0 || parseFloat(cleanSavings) <= 0) {
                isValid = false;
            }

            // 2. Check Expense Limits
            document.querySelectorAll('.expensesTab').forEach(row => {
                const id = row.getAttribute('data-id');
                const modeHiddenInput = row.querySelector(`input[name="category_${id}_mode"]`);
                const limitInput = row.querySelector('.limit-input');

                if (modeHiddenInput && modeHiddenInput.value === 'limit') {
                    const cleanLimit = limitInput.value.replace(/[^0-9.]/g, '');

                    if (cleanLimit.length === 0 || parseFloat(cleanLimit) <= 0) {
                        isValid = false; // Limit mode requires a positive value
                    }
                    totalExpenses++;
                }
            });

            // 3. Enable/Disable Next button
            nextButton.disabled = !isValid;
        }

        document.addEventListener('DOMContentLoaded', () => {

            // Function to manage the state of a single expense row
            function setupRowLogic(row) {
                const id = row.getAttribute('data-id');
                // Use the unique names for the checkboxes
                const trackCheckbox = row.querySelector(`input[name="category_${id}_mode_checkbox_track"]`);
                const limitCheckbox = row.querySelector(`input[name="category_${id}_mode_checkbox_limit"]`);
                const limitInput = row.querySelector('.limit-input');
                const modeHiddenInput = row.querySelector(`input[name="category_${id}_mode"]`);

                if (!trackCheckbox || !limitCheckbox || !limitInput || !modeHiddenInput) return;

                // Add formatting listener to the limit input
                addFormattingListener(limitInput);

                function updateRowState(sourceCheckbox) {
                    let selectedMode = 'track'; // Default 

                    // Ensure only one is selected. If the source is checked, uncheck the other.
                    if (sourceCheckbox === trackCheckbox && trackCheckbox.checked) {
                        limitCheckbox.checked = false;
                        selectedMode = 'track';
                    } else if (sourceCheckbox === limitCheckbox && limitCheckbox.checked) {
                        trackCheckbox.checked = false;
                        selectedMode = 'limit';
                    }

                    // Fallback: If both are somehow unchecked, re-check the last one clicked
                    if (!trackCheckbox.checked && !limitCheckbox.checked) {
                        if (sourceCheckbox === trackCheckbox) {
                            trackCheckbox.checked = true; // Stay in track mode
                            selectedMode = 'track';
                        } else {
                            limitCheckbox.checked = true; // Stay in limit mode
                            selectedMode = 'limit';
                        }
                    }

                    // Update the hidden field for BE submission
                    modeHiddenInput.value = selectedMode;

                    // Enable/Disable limit input based on selection
                    if (selectedMode === 'limit') {
                        limitInput.disabled = false;
                        limitInput.style.opacity = '1';
                        limitInput.setAttribute('required', 'required');
                        // No need to clear value if switching from track to limit, user might have pre-filled it
                    } else {
                        limitInput.disabled = true;
                        limitInput.style.opacity = '0.5';
                        limitInput.value = ''; // Clear value if tracking is chosen
                        limitInput.removeAttribute('required');
                    }

                    checkFormValidity(); // Check validity on mode change
                }

                // Attach event listeners
                trackCheckbox.addEventListener('change', () => updateRowState(trackCheckbox));
                limitCheckbox.addEventListener('change', () => updateRowState(limitCheckbox));

                // Initial state update on load
                // Determines initial state from the PHP rendering
                updateRowState(limitCheckbox.checked ? limitCheckbox : trackCheckbox);
            }

            // Apply logic to all expense rows
            document.querySelectorAll('.expensesTab').forEach(setupRowLogic);

            // Add formatting listener to the Savings input
            const savingsInput = document.querySelector('.savingsForm');
            if (savingsInput) {
                addFormattingListener(savingsInput);
            }

            // Initial check on load
            checkFormValidity();
        });

        // Tracking Only Prompt Logic 
        const budgetForm = document.getElementById('budgetRuleForm');
        const promptModal = document.getElementById('trackingPromptModal');
        const trackingForm = document.getElementById('trackingForm');

        function closePrompt() {
            promptModal.style.display = 'none';
        }

        function confirmTracking() {

            const formData = new FormData(budgetForm);

            // Clear existing data inputs in the tracking form (except the confirm flag)
            Array.from(trackingForm.elements).forEach(el => {
                if (el.type === 'hidden' && el.name !== 'tracking_confirm') el.remove();
            });

            // Replicate all main form inputs to the tracking form for submission
            for (const [key, value] of formData.entries()) {

                if (key.includes('_mode_checkbox')) continue;

                if (key !== 'tracking_confirm') {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = key;
                    hiddenInput.value = value;
                    trackingForm.appendChild(hiddenInput);
                }
            }

            // Submit the tracking form with the 'tracking_confirm' flag
            trackingForm.submit();
        }

    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>