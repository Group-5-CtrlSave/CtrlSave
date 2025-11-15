<?php
// Start the session (essential for tracking the logged-in user)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the correct path to the connection file
include("../../assets/shared/connect.php");

// Check for logged-in user
if (!isset($_SESSION['userID'])) {
    // If not logged in, redirect to login page (adjust path as needed)
    header("Location: ../login.php");
    exit();
}

$userID = $_SESSION['userID']; // Get the logged-in user ID
$categories = []; // Will hold expense categories from tbl_usercategories
$savingsCategory = null; // Will hold the details for the 'Savings' category
$error = ''; // Used for displaying the error toast
$show_tracking_prompt = false; // Flag to show the 'Tracking Only' modal

/**
 * Cleans the input value by removing currency symbols (₱, $), and commas.
 * This is crucial for server-side validation and database insertion.
 * @param string $value The raw input string from the user.
 * @return string The cleaned, numeric string.
 */
function clean_currency_input($value) {
    // Remove all commas, spaces, the Philippine Peso sign (₱), and Dollar sign ($)
    // The pattern /[,\s\x{20B1}\$]/u handles commas, spaces, Peso, and Dollar signs
    $cleaned_value = preg_replace('/[,\s\x{20B1}\$]/u', '', $value); 
    return trim($cleaned_value);
}


// --- 1. Fetch User Categories for the Form (READ from tbl_usercategories) ---

// A. Fetch Expense Categories (type = 'expense')
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

// B. Fetch Savings Category (type = 'savings') - Must be automatically added
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


// --- 2. Process Form Submission (POST Request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {
    
    $all_tracking = true;
    $valid_allocation = true;
    $allocations = [];
    // The total_limit_count helps confirm if only the Savings allocation (count = 1) 
    // is set to 'limit' (or track/limit combined) while all expenses are 'track'.
    $total_limit_count = 0; 

    // --- A. Validate Expense Allocations ---
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $id = $category['userCategoryID'];
            $name_base = "category_" . $id;

            // Get mode and value from POST
            $mode = $_POST[$name_base . "_mode"] ?? ''; // The hidden input field value ('track' or 'limit')
            $value = trim($_POST[$name_base . "_value"] ?? ''); // The limit amount input

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
                'limitType' => ($mode == 'limit') ? 1 : 0, // 1 for 'amount' limit, 0 for 'track'
                'value' => 0.00 // Default value
            ];

            if ($mode == 'limit') {
                $all_tracking = false; 
                $total_limit_count++;

                // Validation for amount (must be a positive number after cleaning)
                if (empty($value) || !is_numeric($cleaned_value) || (float)$cleaned_value <= 0) {
                    $valid_allocation = false;
                    $error = "Limit value for " . htmlspecialchars($category['categoryName']) . " must be a positive monetary amount.";
                    break;
                }
                $allocation_data['value'] = (float)$cleaned_value; // Store the cleaned numeric value
            }

            $allocations[] = $allocation_data;
        }
    }


    // --- B. Validate Savings Allocation (Must always be treated as a Limit) ---
    $savings_value = trim($_POST['savings_value'] ?? '');
    $cleaned_savings_value = clean_currency_input($savings_value);

    if ($valid_allocation && $savingsCategory) {
        if (empty($savings_value) || !is_numeric($cleaned_savings_value) || (float)$cleaned_savings_value <= 0) {
            $valid_allocation = false;
            $error = "Savings amount must be a positive monetary amount.";
        } else {
            // Savings is always a Limit
            $savings_allocation = [
                'userCategoryID' => $savingsCategory['userCategoryID'],
                'necessityType' => 'saving', // Or whatever is appropriate for a savings category in your schema
                'limitType' => 1, // Savings is always limited/capped
                'value' => (float)$cleaned_savings_value // Store the cleaned numeric value
            ];
            $allocations[] = $savings_allocation;
            $total_limit_count++; // Increment as Savings is a form of limit
        }
    }

    // --- C. Handle Tracking Only Prompt ---
    // The prompt shows if:
    // 1. All expense categories are set to 'track' ($all_tracking is true).
    // 2. ONLY the Savings limit is set (i.e., $total_limit_count is 1).
    // 3. The confirmation flag is NOT present.
    if ($valid_allocation) {
        if ($all_tracking && $total_limit_count == 1) { 
            if (!isset($_POST['tracking_confirm'])) {
                // Set flag to display the prompt on the FE
                $show_tracking_prompt = true; 
                $valid_allocation = false; // Stop further processing until confirmed
            }
        }
    }

    // --- D. Database Insertions (If Valid and Tracking Prompt Passed) ---
    if ($valid_allocation && !$show_tracking_prompt) {
        
        $conn->begin_transaction();
        try {
            // 1. Create/Select Custom Budget Rule (tbl_userbudgetrule)
            $ruleName = "Custom-Rule";
            $isSelected = 1;
            
            // Unselect all current rules for the user
            // ANTI-SQL INJECTION: Prepared statement
            $sql_unselect = "UPDATE tbl_userbudgetrule SET isSelected = 0 WHERE userID = ?";
            $stmt_unselect = $conn->prepare($sql_unselect);
            $stmt_unselect->bind_param("i", $userID);
            $stmt_unselect->execute();
            $stmt_unselect->close();

            // Insert the new Custom Rule
            // ANTI-SQL INJECTION: Prepared statement
            // NOTE: Consider checking if a 'Custom-Rule' already exists and updating it instead of inserting a new one every time.
            $sql_rule = "INSERT INTO tbl_userbudgetrule (userID, ruleName, isSelected) VALUES (?, ?, ?)";
            $stmt_rule = $conn->prepare($sql_rule);
            $stmt_rule->bind_param("isi", $userID, $ruleName, $isSelected);
            $stmt_rule->execute();
            $userBudgetRuleID = $conn->insert_id;
            $stmt_rule->close();
            
            // 2. Insert into tbl_userallocation (Expenses + Savings)
            // The constraint: limitType = 'amount' means we use '1' (limit) for savings and expenses set to limit.
            // For expenses set to 'track', we use '0'. The value is stored as the limit amount or 0.
            
            // ANTI-SQL INJECTION: Prepared statement
            // NOTE: Check if a prior allocation entry exists and delete/update as needed. Assuming for Initialization flow, DELETE is safe.
            $sql_delete_allocations = "DELETE FROM tbl_userallocation WHERE userBudgetruleID = (SELECT userBudgetruleID FROM tbl_userbudgetrule WHERE userID = ? AND ruleName = 'Custom-Rule' AND isSelected = 1)";
            // (If the INSERT logic above is always creating a NEW rule, deletion is not strictly needed for the old allocations tied to the OLD rule, but you should clean up any old rules/allocations.)
            // For simplicity and matching the existing code's flow: we insert into the new rule ID.

            $sql_allocation = "INSERT INTO tbl_userallocation (userBudgetruleID, userCategoryID, necessityType, limitType, value) VALUES (?, ?, ?, ?, ?)";
            $stmt_allocation = $conn->prepare($sql_allocation);

            foreach ($allocations as $allocation) {
                $userCategoryID = $allocation['userCategoryID'];
                $necessityType = $allocation['necessityType'];
                $limitType = $allocation['limitType']; // 1 for limit, 0 for track
                $value = $allocation['value']; 

                // Binding as 'd' (double) for the numeric value
                // ANTI-SQL INJECTION: All user inputs are bound here
                $stmt_allocation->bind_param("iisid", $userBudgetRuleID, $userCategoryID, $necessityType, $limitType, $value);
                $stmt_allocation->execute();
            }

            $stmt_allocation->close();

            // 3. Update tbl_usercategories 'userisFlexible' based on POST for future pre-fill
            // This is crucial for the pre-fill requirement.
            $sql_update_flex = "UPDATE tbl_usercategories SET userisFlexible = ? WHERE userCategoryID = ? AND userID = ?";
            $stmt_update_flex = $conn->prepare($sql_update_flex);

            foreach ($categories as $category) {
                $id = $category['userCategoryID'];
                $name_base = "category_" . $id;
                $mode = $_POST[$name_base . "_mode"] ?? 'track'; 
                
                // 1 if 'limit' (flexible/can be limited), 0 if 'track' (inflexible/just tracking)
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
// DO NOT ADD CLOSING PHP TAG if there is no need for raw HTML content after