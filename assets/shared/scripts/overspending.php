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
        // DEFAULT ALLOCATIONS (necessityType)
        // ==============================
        $defaultAllocQuery = "
            SELECT necessityType, limitType, value 
            FROM tbl_userallocation 
            WHERE userBudgetRuleID = $userBudgetRuleID
            AND userCategoryID = 0
        ";
        $defaultAllocRes = executeQuery($defaultAllocQuery);

        while ($allocRow = mysqli_fetch_assoc($defaultAllocRes)) {
            $necessityType = $allocRow['necessityType'];
            $limitType = $allocRow['limitType'];
            $value = $allocRow['value'];

            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

            $catQuery = "
                SELECT userCategoryID, categoryName 
                FROM tbl_usercategories 
                WHERE userNecessityType = '$necessityType'
                AND userisFlexible = 1
            ";
            $catRes = executeQuery($catQuery);

            $totalSpent = 0;
            $categoryList = [];

            while ($catRow = mysqli_fetch_assoc($catRes)) {
                $userCategoryID = $catRow['userCategoryID'];
                $categoryName = $catRow['categoryName'];

                $expenseQuery = "
                    SELECT SUM(amount) AS spent 
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = $userCategoryID
                    AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
                ";
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

                // Insert into tbl_spendinginsights if not exists
                $insightQuery = "
                    INSERT INTO tbl_spendinginsights (userID, categoryA, necessityType, insightType, message, date)
                    SELECT $userID, NULL, '$necessityType', 'overspending', '$message', NOW()
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_spendinginsights
                        WHERE userID = $userID
                        AND categoryA IS NULL
                        AND insightType = 'overspending'
                        AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ";
                executeQuery($insightQuery);

                // Insert into tbl_notifications if not exists
                $notifQuery = "
                    INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                    SELECT 'Monthly Overspending Alert','$message','alert.png',$userID,NOW(),'overspending'
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_notifications
                        WHERE userID = $userID
                        AND type = 'overspending'
                        AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ";
                executeQuery($notifQuery);
            }
        }

        // ==============================
        // CUSTOM ALLOCATIONS
        // ==============================
        $customAllocQuery = "
            SELECT ua.userCategoryID, ua.necessityType, ua.limitType, ua.value, uc.userisFlexible
            FROM tbl_userallocation ua
            LEFT JOIN tbl_usercategories uc ON ua.userCategoryID = uc.userCategoryID
            WHERE ua.userBudgetRuleID = $userBudgetRuleID
            AND ua.userCategoryID != 0
        ";
        $customAllocRes = executeQuery($customAllocQuery);

        while ($allocRow = mysqli_fetch_assoc($customAllocRes)) {
            $userCategoryID = $allocRow['userCategoryID'];
            $necessityType = $allocRow['necessityType'];
            $limitType = $allocRow['limitType'];
            $value = $allocRow['value'];
            $isFlexible = intval($allocRow['userisFlexible']);

            // Skip tracked-only rigid categories
            if ($isFlexible == 0) continue;

            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

            $expenseQuery = "
                SELECT SUM(amount) AS spent 
                FROM tbl_expense
                WHERE userID = $userID
                AND userCategoryID = $userCategoryID
                AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
            ";
            $expenseRow = mysqli_fetch_assoc(executeQuery($expenseQuery));
            $spent = floatval($expenseRow['spent'] ?? 0);

            if ($spent > $budgetLimit) {
                $overAmount = $spent - $budgetLimit;

                $catQuery = "SELECT categoryName FROM tbl_usercategories WHERE userCategoryID = $userCategoryID LIMIT 1";
                $catRow = mysqli_fetch_assoc(executeQuery($catQuery));
                $categoryName = $catRow['categoryName'] ?? "Unknown Category";

                $message = "You have overspent ₱{$overAmount} in $categoryName from $startDate to $endDate.";

                // Insert into tbl_spendinginsights if not exists
                $insightQuery = "
                    INSERT INTO tbl_spendinginsights (userID, categoryA, necessityType, insightType, message, date)
                    SELECT $userID, $userCategoryID, '$necessityType', 'overspending', '$message', NOW()
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_spendinginsights
                        WHERE userID = $userID
                        AND categoryA = $userCategoryID
                        AND insightType = 'overspending'
                        AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ";
                executeQuery($insightQuery);

                // Insert into tbl_notifications if not exists
                $notifQuery = "
                    INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                    SELECT 'Monthly Overspending Alert','$message','alert.png',$userID,NOW(),'overspending'
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_notifications
                        WHERE userID = $userID
                        AND type = 'overspending'
                        AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ";
                executeQuery($notifQuery);
            }
        }
    }

    echo "Monthly overspending insights and notifications generated successfully!";
}
?>
