<?php
include("../connect.php");

// Daily mode: only today
$startDate = date('Y-m-d');

// Get Active Budget Versions
$getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                     FROM tbl_userbudgetversion 
                     WHERE isActive = 1";

$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {
    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {

        $userID           = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome      = floatval($budgetVersionRow['totalIncome']);

        // Get allocations with flexible info
        $getAllocation = "
            SELECT 
                ua.userCategoryID,
                ua.necessityType,
                ua.limitType,
                ua.value AS allocationValue,
                uc.userisFlexible
            FROM tbl_userallocation ua
            LEFT JOIN tbl_usercategories uc 
                ON ua.userCategoryID = uc.userCategoryID
            WHERE ua.userBudgetRuleID = $userBudgetRuleID
        ";

        $allocationResult = executeQuery($getAllocation);

        if (mysqli_num_rows($allocationResult) > 0) {
            while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {

                $userCategoryID  = $allocationRow['userCategoryID'];
                $necessityType   = $allocationRow['necessityType'];
                $limitType       = $allocationRow['limitType'];
                $value           = $allocationRow['allocationValue'];
                $userisFlexible  = $allocationRow['userisFlexible'] ?? 1; // default flexible

                // Skip tracked-only custom categories
                if ($userCategoryID != 0 && $userisFlexible == 0) continue;

                // Fetch Expenses
                if ($userCategoryID == 0) {
                    // Default allocation (necessityType)
                    $expenseQuery = "
                        SELECT COALESCE(SUM(e.amount),0) AS totalSpent
                        FROM tbl_expense e
                        JOIN tbl_usercategories c ON c.userCategoryID = e.userCategoryID
                        WHERE e.userID = $userID
                        AND c.userNecessityType = '$necessityType'
                        AND DATE(e.dateSpent) = '$startDate'
                    ";
                } else {
                    // Custom category
                    $expenseQuery = "
                        SELECT COALESCE(SUM(amount),0) AS totalSpent
                        FROM tbl_expense
                        WHERE userID = $userID
                        AND userCategoryID = $userCategoryID
                        AND DATE(dateSpent) = '$startDate'
                    ";
                }

                $expenseResult = executeQuery($expenseQuery);
                $expenseRow    = mysqli_fetch_assoc($expenseResult);
                $totalSpent    = floatval($expenseRow['totalSpent'] ?? 0);

                // Set the budget limit
                $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

                // Check overspending
                if ($totalSpent > $budgetLimit) {
                    $overAmount = $totalSpent - $budgetLimit;

                    // Determine category label
                    if ($userCategoryID == 0) {
                        $categoryLabel = $necessityType;
                    } else {
                        $catQuery   = "SELECT categoryName FROM tbl_usercategories WHERE userCategoryID = $userCategoryID LIMIT 1";
                        $catResult  = executeQuery($catQuery);
                        $catRow     = mysqli_fetch_assoc($catResult);
                        $categoryLabel = $catRow['categoryName'] ?? "Unknown Category";
                    }

                    // Build message
                    $message = "You have overspent ₱$overAmount in $categoryLabel today ($startDate). Your target limit was ₱$budgetLimit.";

                    // ========================
                    // 1. Insert notification safely
                    // ========================
                    $insertNotif = "
                        INSERT INTO tbl_notifications(notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'Daily Overspending Alert', '$message', 'alert.png', $userID, NOW(), 'daily_overspending'
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND message = '$message'
                            AND DATE(createdAt) = '$startDate'
                        )
                    ";
                    executeQuery($insertNotif);

                    // ========================
                    // 2. Insert insight safely
                    // ========================
                    $categoryCheck = ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID");

                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_overspending', '$message', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_overspending'
                            AND categoryA $categoryCheck
                            AND message = '$message'
                            AND DATE(date) = '$startDate'
                        )
                    ";
                    executeQuery($insertInsight);
                }

            } // end allocation loop
        }
    }
}

echo "Daily overspending insights and notifications generated successfully!";
?>
