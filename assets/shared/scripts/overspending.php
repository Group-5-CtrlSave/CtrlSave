<?php
include("../connect.php");

$startDate = date('Y-m-01');
$endDate = date('Y-m-t');

$getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                     FROM tbl_userbudgetversion 
                     WHERE isActive = 1;";
$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {
    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {
        $userID = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome = $budgetVersionRow['totalIncome'];

        // ==============================
        // DEFAULT ALLOCATION
        // ==============================
        $defaultAllocQuery = "SELECT necessityType, limitType, value 
                              FROM tbl_userAllocation 
                              WHERE userBudgetRuleID = $userBudgetRuleID
                              AND userCategoryID = 0";
        $defaultAllocRes = executeQuery($defaultAllocQuery);

        while ($allocRow = mysqli_fetch_assoc($defaultAllocRes)) {
            $necessityType = $allocRow['necessityType'];
            $limitType = $allocRow['limitType'];
            $value = $allocRow['value'];

            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

            $catQuery = "SELECT userCategoryID, categoryName 
                         FROM tbl_usercategories 
                         WHERE userNecessityType = '$necessityType'
                         AND userisFlexible = 1";
            $catRes = executeQuery($catQuery);

            $totalSpent = 0;
            $categoryList = [];

            while ($catRow = mysqli_fetch_assoc($catRes)) {
                $userCategoryID = $catRow['userCategoryID'];
                $categoryName = $catRow['categoryName'];

                $expenseQuery = "SELECT SUM(amount) AS spent 
                                 FROM tbl_expense
                                 WHERE userID = $userID
                                 AND userCategoryID = $userCategoryID
                                 AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'";
                $expenseRow = mysqli_fetch_assoc(executeQuery($expenseQuery));
                $spent = floatval($expenseRow['spent'] ?? 0);

                if ($spent > 0) {
                    $totalSpent += $spent;
                    $categoryList[] = $categoryName;
                }
            }

            if ($totalSpent > $budgetLimit && !empty($categoryList)) {
                $overPercent = round(($totalSpent / $budgetLimit) * 100, 2);

                $message = "You have overspent {$overPercent}% in $necessityType (" . implode(", ", $categoryList) . ") from $startDate to $endDate.";

                executeQuery("INSERT INTO tbl_spendinginsights
                              (userID, categoryA, necessityType, insightType, message, date)
                              VALUES ($userID, NULL, '$necessityType', 'overspending', '$message', NOW())");

                executeQuery("INSERT INTO tbl_notifications
                              (notificationTitle, message, icon, userID, createdAt, type)
                              VALUES ('Monthly Overspending Alert','$message','alert.png',$userID,NOW(), 'overspending')");
            }
        }

        // ==============================
        // CUSTOM ALLOCATIONS
        // ==============================
        $customAllocQuery = "SELECT userCategoryID, necessityType, limitType, value
                             FROM tbl_userAllocation 
                             WHERE userBudgetRuleID = $userBudgetRuleID
                             AND userCategoryID != 0";
        $customAllocRes = executeQuery($customAllocQuery);

        while ($allocRow = mysqli_fetch_assoc($customAllocRes)) {
            $userCategoryID = $allocRow['userCategoryID'];
            $necessityType = $allocRow['necessityType'];
            $limitType = $allocRow['limitType'];
            $value = $allocRow['value'];

            // Get actual category name
            $catQuery = "SELECT categoryName, userisFlexible 
                         FROM tbl_usercategories 
                         WHERE userCategoryID = $userCategoryID LIMIT 1";
            $catRow = mysqli_fetch_assoc(executeQuery($catQuery));
            $categoryName = $catRow['categoryName'] ?? "Unknown Category";
            $isFlexible = intval($catRow['userisFlexible']);

            // Skip rigid categories if you don’t want them monitored
            if ($isFlexible == 0) continue;

            // Calculate budget
            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

            // Get actual spending
            $expenseQuery = "SELECT SUM(amount) AS spent 
                             FROM tbl_expense 
                             WHERE userID = $userID
                             AND userCategoryID = $userCategoryID
                             AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'";
            $expenseRow = mysqli_fetch_assoc(executeQuery($expenseQuery));
            $spent = floatval($expenseRow['spent'] ?? 0);

            if ($spent > $budgetLimit) {
                $overAmount = $spent - $budgetLimit;
                $message = "You have overspent ₱{$overAmount} in $categoryName from $startDate to $endDate.";

                executeQuery("INSERT INTO tbl_spendinginsights
                              (userID, categoryA, necessityType, insightType, message, date)
                              VALUES ($userID, $userCategoryID, '$necessityType', 'overspending', '$message', NOW())");

                executeQuery("INSERT INTO tbl_notifications
                              (notificationTitle, message, icon, userID, createdAt, type)
                              VALUES ('Monthly Overspending Alert','$message','alert.png',$userID,NOW(), 'overspending')");
            }
        }
    }

    echo "Overspending insights per necessity type and custom categories generated successfully!";
}
?>
