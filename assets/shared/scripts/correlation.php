<?php
include("../connect.php");

// Monthly mode
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');
$currentMonth = date('F'); // Full month name
$currentYear = date('Y');  // Current year

// Get Active Budget Versions
$getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                     FROM tbl_userbudgetversion 
                     WHERE isActive = 1;";
$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {
    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {
        $userID = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome = $budgetVersionRow['totalIncome'];

        // Get Allocation
        $getAllocation = "SELECT userCategoryID, necessityType, limitType, value as allocationValue
                          FROM tbl_userAllocation 
                          WHERE userBudgetRuleID = $userBudgetRuleID";
        $allocationResult = executeQuery($getAllocation);

        $userSpending = [];

        if (mysqli_num_rows($allocationResult) > 0) {
            while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {
                $userCategoryID = $allocationRow['userCategoryID'];
                $necessityType = $allocationRow['necessityType'];
                $limitType = $allocationRow['limitType'];
                $value = $allocationRow['allocationValue'];

                // Skip savings entirely
                if ($userCategoryID == 0 && $necessityType === 'saving') continue;

                // Fetch total spent
                if ($userCategoryID == 0) {
                    // Default allocation (need/want)
                    $expenseQuery = "SELECT tbl_usercategories.categoryName, COALESCE(SUM(tbl_expense.amount),0) as totalSpent
                                     FROM tbl_expense 
                                     JOIN tbl_usercategories 
                                     ON tbl_usercategories.userCategoryID = tbl_expense.userCategoryID
                                     WHERE tbl_expense.userID = $userID
                                     AND tbl_usercategories.userNecessityType = '$necessityType'
                                     AND DATE(tbl_expense.dateSpent) BETWEEN '$startDate' AND '$endDate'
                                     GROUP BY tbl_usercategories.categoryName";
                    $categoryLabel = $necessityType;
                } else {
                    // Custom allocation
                    $expenseQuery = "SELECT COALESCE(SUM(amount),0) as totalSpent
                                     FROM tbl_expense 
                                     WHERE userID = $userID
                                     AND userCategoryID = $userCategoryID
                                     AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'";
                    $catRow = mysqli_fetch_assoc(executeQuery("SELECT categoryName FROM tbl_usercategories WHERE userCategoryID = $userCategoryID"));
                    $categoryLabel = $catRow['categoryName'] ?? "Unknown Category";
                }

                $expenseResult = executeQuery($expenseQuery);

                // For default allocation, store breakdown by category
                $categoryBreakdown = [];
                $totalSpent = 0;

                if ($userCategoryID == 0) {
                    while ($row = mysqli_fetch_assoc($expenseResult)) {
                        $spentAmount = floatval($row['totalSpent']);
                        $categoryBreakdown[$row['categoryName']] = $spentAmount;
                        $totalSpent += $spentAmount;
                    }
                } else {
                    $row = mysqli_fetch_assoc($expenseResult);
                    $totalSpent = floatval($row['totalSpent'] ?? 0);
                }

                // Calculate budget limit
                $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

                // Calculate spent percentage
                $spentPercent = ($totalSpent / $budgetLimit) * 100;
                $spentPercentOfIncome = ($totalSpent / $totalIncome) * 100;

                $userSpending[] = [
                    'categoryID' => $userCategoryID,
                    'necessityType' => $necessityType,
                    'categoryLabel' => $categoryLabel,
                    'totalSpent' => $totalSpent,
                    'budgetLimit' => $budgetLimit,
                    'spentPercent' => $spentPercent,
                    'spentPercentOfIncome' => $spentPercentOfIncome,
                    'categoryBreakdown' => $categoryBreakdown
                ];
            }

            // Find the most overspent allocation
            $mostOverspent = null;
            foreach ($userSpending as $sp) {
                if ($sp['spentPercent'] > 100) {
                    if (!$mostOverspent || $sp['spentPercent'] > $mostOverspent['spentPercent']) {
                        $mostOverspent = $sp;
                    }
                }
            }

            if ($mostOverspent) {
                // Build list of other categories/necessities
                $otherCategories = [];
                foreach ($userSpending as $sp) {
                    if ($mostOverspent['categoryID'] == 0) {
                        if (($sp['necessityType'] === 'need' || $sp['necessityType'] === 'want') &&
                            $sp['necessityType'] != $mostOverspent['necessityType']) {
                            $otherCategories[] = $sp['necessityType'];
                        }
                    } else {
                        if ($sp['categoryID'] != $mostOverspent['categoryID']) {
                            $otherCategories[] = $sp['categoryLabel'];
                        }
                    }
                }
                $othersText = !empty($otherCategories) ? implode(', ', array_unique($otherCategories)) : "";

                // Build category breakdown text
                $breakdownText = '';
                if (!empty($mostOverspent['categoryBreakdown'])) {
                    arsort($mostOverspent['categoryBreakdown']); // sort descending
                    $parts = [];
                    foreach ($mostOverspent['categoryBreakdown'] as $cat => $amt) {
                        $percentOfIncome = round(($amt / $totalIncome) * 100, 1);
                        $parts[] = "$cat ({$percentOfIncome}%)";
                    }
                    $breakdownText = implode(', ', $parts);
                }

                // Build correlation message
                if ($mostOverspent['categoryID'] == 0) {
                    $message = "You spent " . round($mostOverspent['spentPercent'], 1) . "% of your allocated {$mostOverspent['categoryLabel']} budget (" . 
                               round($mostOverspent['spentPercentOfIncome'],1) . "% of your total income) in $currentMonth $currentYear, which is higher than your target limit of " . 
                               round(($mostOverspent['budgetLimit'] / $totalIncome) * 100, 1) . "% of income.";
                    if ($breakdownText) {
                        $message .= " This includes: $breakdownText.";
                    }
                    $message .= " Because of this, you may have spent less on $othersText and saved less.";
                } else {
                    $message = "You spent ₱" . number_format($mostOverspent['totalSpent'], 2) . " on {$mostOverspent['categoryLabel']} (" .
                               round($mostOverspent['spentPercentOfIncome'],1) . "% of your total income) in $currentMonth $currentYear, exceeding your target limit of ₱" .
                               number_format($mostOverspent['budgetLimit'], 2) . ". Because of this, you may have spent less on $othersText and saved less.";
                }

                // Avoid duplicates for this month
                $checkInsight = "SELECT 1 FROM tbl_spendinginsights 
                                 WHERE userID = $userID 
                                 AND insightType = 'correlation'
                                 AND categoryA " . ($mostOverspent['categoryID'] == 0 ? "IS NULL" : "= {$mostOverspent['categoryID']}") . "
                                 AND DATE_FORMAT(date,'%Y-%m') = '" . date('Y-m') . "'
                                 LIMIT 1";

                if (mysqli_num_rows(executeQuery($checkInsight)) == 0) {
                    $insertInsight = "INSERT INTO tbl_spendinginsights 
                                      (userID, categoryA, insightType, message, date)
                                      VALUES ($userID, " . ($mostOverspent['categoryID'] == 0 ? "NULL" : $mostOverspent['categoryID']) . 
                                      ", 'correlation', '$message', NOW())";
                    executeQuery($insertInsight);
                }
            }
        }
    }
    echo "Monthly correlation insights generated successfully!";
}
?>
