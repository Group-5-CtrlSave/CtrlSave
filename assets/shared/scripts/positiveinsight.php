<?php
include("../connect.php");

// Monthly mode
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');

// Get Active Budget Versions
$getBudgetVersion = "
    SELECT userID, userBudgetRuleID, totalIncome 
    FROM tbl_userbudgetversion 
    WHERE isActive = 1;
";
$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {
    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {

        $userID = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome = $budgetVersionRow['totalIncome'];

        $allTrackingParts = [];

        // ============================
        // 1. DEFAULT NEED & WANT
        // ============================
        foreach (['need', 'want'] as $type) {

            $allocRes = executeQuery("
                SELECT value AS allocationValue, limitType
                FROM tbl_userAllocation
                WHERE userBudgetRuleID = $userBudgetRuleID
                AND userCategoryID = 0
                AND necessityType = '$type'
            ");
            $allocRow = mysqli_fetch_assoc($allocRes);
            if (!$allocRow) continue;

            $budgetLimit = ($allocRow['limitType'] == 1) 
                ? ($totalIncome * $allocRow['allocationValue'] / 100) 
                : $allocRow['allocationValue'];

            // Flexible categories → positive insight
            $flexRes = executeQuery("
                SELECT userCategoryID, categoryName
                FROM tbl_usercategories
                WHERE userNecessityType = '$type'
                AND userisFlexible = 1
            ");
            $totalFlexibleSpent = 0;
            $categoryList = [];

            while ($catRow = mysqli_fetch_assoc($flexRes)) {
                $spentRes = executeQuery("
                    SELECT COALESCE(SUM(amount),0) AS spent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = {$catRow['userCategoryID']}
                    AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
                ");
                $spent = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);
                if ($spent > 0) {
                    $totalFlexibleSpent += $spent;
                    $categoryList[] = $catRow['categoryName'];
                }
            }

            if ($totalFlexibleSpent > 0 && $totalFlexibleSpent <= $budgetLimit && !empty($categoryList)) {
                $percent = ($budgetLimit > 0) ? round(($totalFlexibleSpent / $budgetLimit) * 100, 2) : 0;
                $message = "Great job! You spent wisely: {$percent}% on $type (" . implode(', ', $categoryList) . ") this month.";

                // INSERT INTO tbl_spendinginsights with duplicate check
                executeQuery("
                    INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                    SELECT $userID, 0, 'positive', '$message', NOW()
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_spendinginsights
                        WHERE userID = $userID 
                        AND categoryA = 0
                        AND insightType = 'positive'
                        AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ");

                // INSERT INTO tbl_notifications with duplicate check
                executeQuery("
                    INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                    SELECT 'Positive Insight', '$message', 'insight.png', $userID, NOW(), 'positive_insight'
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_notifications
                        WHERE userID = $userID
                        AND type = 'positive_insight'
                        AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ");
            }

            // Rigid categories → tracking only
            $rigidRes = executeQuery("
                SELECT userCategoryID, categoryName
                FROM tbl_usercategories
                WHERE userNecessityType = '$type'
                AND userisFlexible = 0
            ");
            while ($rigidRow = mysqli_fetch_assoc($rigidRes)) {
                $spentRes = executeQuery("
                    SELECT COALESCE(SUM(amount),0) AS spent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = {$rigidRow['userCategoryID']}
                    AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
                ");
                $spent = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);

                if ($spent <= 0) continue;

                $percent = ($budgetLimit > 0) ? round(($spent / $budgetLimit) * 100, 2) : 0;
                $allTrackingParts[] = "You spent {$percent}% on {$rigidRow['categoryName']} (Track Only)";
            }
        }

        // ============================
        // 2. CUSTOM ALLOCATIONS
        // ============================
        $customAllocRes = executeQuery("
            SELECT userCategoryID, limitType, value AS allocationValue
            FROM tbl_userAllocation
            WHERE userBudgetRuleID = $userBudgetRuleID
            AND userCategoryID != 0
        ");

        while ($allocRow = mysqli_fetch_assoc($customAllocRes)) {
            $userCategoryID = $allocRow['userCategoryID'];

            $catRes = executeQuery("
                SELECT categoryName, userisFlexible
                FROM tbl_usercategories
                WHERE userCategoryID = $userCategoryID
            ");
            $catRow = mysqli_fetch_assoc($catRes);

            $categoryName = $catRow['categoryName'];
            $userisFlexible = intval($catRow['userisFlexible']);

            $spentRes = executeQuery("
                SELECT COALESCE(SUM(amount),0) AS spent
                FROM tbl_expense
                WHERE userID = $userID
                AND userCategoryID = $userCategoryID
                AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
            ");
            $actualAmount = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);
            if ($actualAmount <= 0) continue;

            if ($userisFlexible == 1) {
                $budgetLimit = ($allocRow['limitType'] == 1) 
                    ? ($totalIncome * $allocRow['allocationValue'] / 100) 
                    : $allocRow['allocationValue'];

                if ($actualAmount <= $budgetLimit && $budgetLimit > 0) {
                    $percent = round(($actualAmount / $budgetLimit) * 100, 2);
                    $message = "Great job! You spent wisely: {$percent}% on {$categoryName} this month.";

                    // INSERT INTO tbl_spendinginsights with duplicate check
                    executeQuery("
                        INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        SELECT $userID, $userCategoryID, 'positive', '$message', NOW()
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID 
                            AND categoryA = $userCategoryID
                            AND insightType = 'positive'
                            AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                            AND message = '$message'
                        )
                    ");

                    // INSERT INTO tbl_notifications with duplicate check
                    executeQuery("
                        INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'Positive Insight', '$message', 'insight.png', $userID, NOW(), 'positive_insight'
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND type = 'positive_insight'
                            AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                            AND message = '$message'
                        )
                    ");
                }
            } else {
                $allTrackingParts[] = "You spent ₱" . number_format($actualAmount, 2) . " on {$categoryName} (Track Only)";
            }
        }

        // ============================
        // 3. INSERT TRACKING INSIGHT
        // ============================
        if (!empty($allTrackingParts)) {
            $insightMessage = "Tracking Categories Only: " . implode(", ", $allTrackingParts) . " this month.";
            $notifMessage = implode(", ", $allTrackingParts);

            // INSERT INTO tbl_spendinginsights with duplicate check
            executeQuery("
                INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                SELECT $userID, 0, 'tracking', '$insightMessage', NOW()
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_spendinginsights
                    WHERE userID = $userID
                    AND categoryA = 0
                    AND insightType = 'tracking'
                    AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                    AND message = '$insightMessage'
                )
            ");

            // INSERT INTO tbl_notifications with duplicate check
            executeQuery("
                INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                SELECT 'Tracking Update', '$notifMessage', 'insight.png', $userID, NOW(), 'positive_insight'
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_notifications
                    WHERE userID = $userID
                    AND type = 'positive_insight'
                    AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                    AND message = '$notifMessage'
                )
            ");
        }
    }
}

echo "Monthly insights generated successfully!";
?>
