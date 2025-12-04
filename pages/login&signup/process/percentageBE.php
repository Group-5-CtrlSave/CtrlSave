<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include("../../assets/shared/connect.php");

// Check for logged-in user
if (!isset($_SESSION['userID'])) {
    header("Location: ../login.php");
    exit();
}

$userID = $_SESSION['userID'];
$categories = [];
$savingsCategory = null;
$error = '';
$show_tracking_prompt = false;


function clean_currency_input($value)
{
    $cleaned_value = preg_replace('/[,\s\x{20B1}\$]/u', '', $value);
    return trim($cleaned_value);
}

$sql_categories = "SELECT userCategoryID, categoryName, userisFlexible, userNecessityType FROM tbl_usercategories WHERE userID = ? AND type = 'expense' AND isSelected = 1 ORDER BY userNecessityType DESC, categoryName ASC";
$stmt_categories = $conn->prepare($sql_categories);
if ($stmt_categories) {
    $stmt_categories->bind_param("i", $userID);
    $stmt_categories->execute();
    $result_categories = $stmt_categories->get_result();
    while ($row = $result_categories->fetch_assoc()) {
        $categories[] = $row;
    }
    $stmt_categories->close();
} else {
    $error = "Database error fetching expense categories: " . $conn->error;
}

$sql_savings_id = "SELECT userCategoryID, categoryName FROM tbl_usercategories WHERE userID = ? AND type = 'savings' AND categoryName = 'Savings'";
$stmt_savings = $conn->prepare($sql_savings_id);
if ($stmt_savings) {
    $stmt_savings->bind_param("i", $userID);
    $stmt_savings->execute();
    $result_savings = $stmt_savings->get_result();
    $savingsCategory = $result_savings->fetch_assoc();
    $stmt_savings->close();

    if (!$savingsCategory) {
        // Critical error: Savings category setup is missing for the user
        $error = "CRITICAL ERROR: 'Savings' category not found. Cannot proceed.";
    }
} else {
    $error = "Database error fetching Savings category: " . $conn->error;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {

    $all_tracking = true;
    $valid_allocation = true;
    $allocations = [];

    $total_limit_count = 0;


    if (!empty($categories)) {
        foreach ($categories as $category) {
            $id = $category['userCategoryID'];
            $name_base = "category_" . $id;

            // Get mode and value from POST
            $mode = $_POST[$name_base . "_mode"] ?? '';
            $value = trim($_POST[$name_base . "_value"] ?? '');

            // Clean the input before validation
            $cleaned_value = clean_currency_input($value);

            if (empty($mode)) {
                $valid_allocation = false;
                $error = "Internal error: Missing budget mode for an expense category.";
                break;
            }

            $allocation_data = [
                'userCategoryID' => $id,
                'necessityType' => $category['userNecessityType'],
                'limitType' => ($mode == 'limit') ? 1 : 0,
                'value' => 0.00
            ];

            if ($mode == 'limit') {
                $all_tracking = false;
                $total_limit_count++;

                // Validation for amount (must be a positive number after cleaning)
                if (empty($value) || !is_numeric($cleaned_value) || (float) $cleaned_value <= 0) {
                    $valid_allocation = false;
                    $error = "Limit value for " . htmlspecialchars($category['categoryName']) . " must be a positive monetary amount.";
                    break;
                }
                $allocation_data['value'] = (float) $cleaned_value;
            }

            $allocations[] = $allocation_data;
        }
    }


    $savings_value = trim($_POST['savings_value'] ?? '');
    $cleaned_savings_value = clean_currency_input($savings_value);

    if ($valid_allocation && $savingsCategory) {
        if (empty($savings_value) || !is_numeric($cleaned_savings_value) || (float) $cleaned_savings_value <= 0) {
            $valid_allocation = false;
            $error = "Savings amount must be a positive monetary amount.";
        } else {
            // Savings is always a Limit
            $savings_allocation = [
                'userCategoryID' => $savingsCategory['userCategoryID'],
                'necessityType' => 'saving',
                'limitType' => 1,
                'value' => (float) $cleaned_savings_value
            ];
            $allocations[] = $savings_allocation;
            $total_limit_count++;
        }
    }


    if ($valid_allocation) {
        if ($all_tracking && $total_limit_count == 1) {
            if (!isset($_POST['tracking_confirm'])) {
                // Set flag to display the prompt on the FE
                $show_tracking_prompt = true;
                $valid_allocation = false; // Stop further processing until confirmed
            }
        }
    }

    // percentage-based validation using actual amounts
    if ($valid_allocation && !$show_tracking_prompt) {

        // Get user's active total income
        $totalIncome = 0.00;
        $sql_budget = "
        SELECT totalIncome
        FROM tbl_userbudgetversion
        WHERE userID = ?
          AND isActive = 1
        ORDER BY userBudgetversionID DESC
        LIMIT 1
    ";
        $stmt_budget = $conn->prepare($sql_budget);
        if ($stmt_budget) {
            $stmt_budget->bind_param("i", $userID);
            $stmt_budget->execute();
            $res_budget = $stmt_budget->get_result();
            if ($rowBudget = $res_budget->fetch_assoc()) {
                $totalIncome = (float) $rowBudget['totalIncome'];
            }
            $stmt_budget->close();
        }

        if ($totalIncome <= 0) {
            $valid_allocation = false;
            $error = "Cannot set budget limits because your total monthly income is missing or zero.";
        } else {
            // Sum all LIMIT amounts (expenses + savings)
            $totalLimitAmount = 0.0;
            foreach ($allocations as $allocation) {
                if ((int) $allocation['limitType'] === 1) { // only 'limit' mode
                    $totalLimitAmount += (float) $allocation['value']; // actual amount
                }
            }

            // Convert to percent of income
            $usedPercent = ($totalLimitAmount / $totalIncome) * 100;

            // Block if > 100%
            if ($usedPercent > 100.0) {
                $valid_allocation = false;

                $error = "Your total budget limits exceed 100% of your monthly income (₱" . number_format($totalIncome, 2) . ")";
            }
        }
    }

    // Only start transaction if still valid after all checks
    if ($valid_allocation && !$show_tracking_prompt) {

        $conn->begin_transaction();
        try {
            $ruleName = "Custom-Rule";
            $isSelected = 1;

            $sql_unselect = "UPDATE tbl_userbudgetrule SET isSelected = 0 WHERE userID = ?";
            $stmt_unselect = $conn->prepare($sql_unselect);
            $stmt_unselect->bind_param("i", $userID);
            $stmt_unselect->execute();
            $stmt_unselect->close();

            $sql_rule = "INSERT INTO tbl_userbudgetrule (userID, ruleName, isSelected) VALUES (?, ?, ?)";
            $stmt_rule = $conn->prepare($sql_rule);
            $stmt_rule->bind_param("isi", $userID, $ruleName, $isSelected);
            $stmt_rule->execute();
            $userBudgetRuleID = $conn->insert_id;
            $stmt_rule->close();


            $sql_delete_allocations = "DELETE FROM tbl_userallocation WHERE userBudgetruleID = (SELECT userBudgetruleID FROM tbl_userbudgetrule WHERE userID = ? AND ruleName = 'Custom-Rule' AND isSelected = 1)";


            $sql_allocation = "INSERT INTO tbl_userallocation (userBudgetruleID, userCategoryID, necessityType, limitType, value) VALUES (?, ?, ?, ?, ?)";
            $stmt_allocation = $conn->prepare($sql_allocation);

            foreach ($allocations as $allocation) {
                $userCategoryID = $allocation['userCategoryID'];
                $necessityType = $allocation['necessityType'];
                $limitType = $allocation['limitType'];
                $value = $allocation['value'];


                $stmt_allocation->bind_param("iisid", $userBudgetRuleID, $userCategoryID, $necessityType, $limitType, $value);
                $stmt_allocation->execute();
            }

            $stmt_allocation->close();


            $sql_update_flex = "UPDATE tbl_usercategories SET userisFlexible = ? WHERE userCategoryID = ? AND userID = ?";
            $stmt_update_flex = $conn->prepare($sql_update_flex);

            foreach ($categories as $category) {
                $id = $category['userCategoryID'];
                $name_base = "category_" . $id;
                $mode = $_POST[$name_base . "_mode"] ?? 'track';

                $new_isFlexible = ($mode == 'limit') ? 1 : 0;

                $stmt_update_flex->bind_param("iii", $new_isFlexible, $id, $userID);
                $stmt_update_flex->execute();
            }

            $stmt_update_flex->close();


            $conn->commit();

            // Successful completion, proceed to the next step
            header("Location: done.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Transaction failed: Please try again. System Error: " . $e->getMessage();
        }
    }
}