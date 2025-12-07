<?php
include("../connect.php");

$today = date('Y-m-d');
$startOfMonth = date('Y-m-01');
$endOfMonth = date('Y-m-t');

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

            // Monthly budget for default category type
            $allocRes = executeQuery("
                SELECT value AS allocationValue, limitType
                FROM tbl_userAllocation
                WHERE userBudgetRuleID = $userBudgetRuleID
                AND userCategoryID = 0
                AND necessityType = '$type'
            ");
            $allocRow = mysqli_fetch_assoc($allocRes);
            if (!$allocRow) continue;

            $monthlyBudget = ($allocRow['limitType'] == 1) 
                ? ($totalIncome * $allocRow['allocationValue'] / 100) 
                : $allocRow['allocationValue'];

            // Fetch all categories for this type
            $catRes = executeQuery("
                SELECT userCategoryID, categoryName, userisFlexible
                FROM tbl_usercategories
                WHERE userNecessityType = '$type'
            ");

            while ($catRow = mysqli_fetch_assoc($catRes)) {
                $userCategoryID = $catRow['userCategoryID'];
                $categoryName = $catRow['categoryName'];
                $userisFlexible = intval($catRow['userisFlexible']);

                // Spending today
                $todaySpentRes = executeQuery("
                    SELECT COALESCE(SUM(amount),0) AS todaySpent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = $userCategoryID
                    AND DATE(dateSpent) = '$today'
                ");
                $todaySpent = floatval(mysqli_fetch_assoc($todaySpentRes)['todaySpent'] ?? 0);
                if ($todaySpent <= 0) continue;

                if ($userisFlexible == 1) {
                    // Month-to-date spending
                    $monthSpentRes = executeQuery("
                        SELECT COALESCE(SUM(amount),0) AS monthSpent
                        FROM tbl_expense
                        WHERE userID = $userID
                        AND userCategoryID = $userCategoryID
                        AND DATE(dateSpent) BETWEEN '$startOfMonth' AND '$today'
                    ");
                    $monthSpent = floatval(mysqli_fetch_assoc($monthSpentRes)['monthSpent'] ?? 0);

                    // Only insert positive insight if within monthly budget
                    if ($monthSpent <= $monthlyBudget) {
                        $percentUsed = ($monthlyBudget > 0) ? round(($monthSpent / $monthlyBudget) * 100, 2) : 0;
                        $message = "You spent ₱" . number_format($todaySpent,2) . " on $categoryName today. You are on track: {$percentUsed}% of your monthly limit used.";

                        // Spending insight
                        executeQuery("
                            INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                            SELECT $userID, $userCategoryID, 'daily_positive_insight', '$message', NOW()
                            FROM DUAL
                            WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_spendinginsights
                                WHERE userID = $userID
                                AND categoryA = $userCategoryID
                                AND insightType = 'daily_positive_insight'
                                AND DATE(date) = '$today'
                                AND message = '$message'
                            )
                        ");

                        // Notification
                        executeQuery("
                            INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                            SELECT 'Daily Positive Insight', '$message', 'insight.png', $userID, NOW(), 'daily_positive_insight'
                            FROM DUAL
                            WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_notifications
                                WHERE userID = $userID
                                AND type = 'daily_positive_insight'
                                AND DATE(createdAt) = '$today'
                                AND message = '$message'
                            )
                        ");
                    }
                    // Overspent flexible → do nothing
                } else {
                    // Rigid/tracking category
                    $allTrackingParts[] = "You spent ₱" . number_format($todaySpent, 2) . " on {$categoryName} (Track Only)";
                }
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

            // Spending today
            $todaySpentRes = executeQuery("
                SELECT COALESCE(SUM(amount),0) AS todaySpent
                FROM tbl_expense
                WHERE userID = $userID
                AND userCategoryID = $userCategoryID
                AND DATE(dateSpent) = '$today'
            ");
            $todaySpent = floatval(mysqli_fetch_assoc($todaySpentRes)['todaySpent'] ?? 0);
            if ($todaySpent <= 0) continue;

            if ($userisFlexible == 1) {
                // Month-to-date spending
                $monthSpentRes = executeQuery("
                    SELECT COALESCE(SUM(amount),0) AS monthSpent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = $userCategoryID
                    AND DATE(dateSpent) BETWEEN '$startOfMonth' AND '$today'
                ");
                $monthSpent = floatval(mysqli_fetch_assoc($monthSpentRes)['monthSpent'] ?? 0);

                // Only insert positive insight if within monthly budget
                $monthlyBudget = ($allocRow['limitType'] == 1) 
                    ? ($totalIncome * $allocRow['allocationValue'] / 100) 
                    : $allocRow['allocationValue'];

                if ($monthSpent <= $monthlyBudget) {
                    $percentUsed = ($monthlyBudget > 0) ? round(($monthSpent / $monthlyBudget) * 100, 2) : 0;
                    $message = "You spent ₱" . number_format($todaySpent,2) . " on $categoryName today. You are on track: {$percentUsed}% of your monthly limit used.";

                    executeQuery("
                        INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        SELECT $userID, $userCategoryID, 'daily_positive_insight', '$message', NOW()
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND categoryA = $userCategoryID
                            AND insightType = 'daily_positive_insight'
                            AND DATE(date) = '$today'
                            AND message = '$message'
                        )
                    ");

                    executeQuery("
                        INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'Daily Positive Insight', '$message', 'insight.png', $userID, NOW(), 'daily_positive_insight'
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND type = 'daily_positive_insight'
                            AND DATE(createdAt) = '$today'
                            AND message = '$message'
                        )
                    ");
                }
                // Overspent flexible → do nothing
            } else {
                // Rigid/tracking category
                $allTrackingParts[] = "You spent ₱" . number_format($todaySpent, 2) . " on {$categoryName} (Track Only)";
            }
        }

        // ============================
        // 3. INSERT TRACKING INSIGHT
        // ============================
        if (!empty($allTrackingParts)) {
            $insightMessage = "Tracking Categories Only: " . implode(", ", $allTrackingParts) . " today.";
            $notifMessage = implode(", ", $allTrackingParts);

            // Spending insight for rigid categories
            executeQuery("
                INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                SELECT $userID, 0, 'daily_tracking', '$insightMessage', NOW()
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_spendinginsights
                    WHERE userID = $userID
                    AND categoryA = 0
                    AND insightType = 'daily_tracking'
                    AND DATE(date) = '$today'
                    AND message = '$insightMessage'
                )
            ");

            executeQuery("
                INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                SELECT 'Daily Tracking', '$notifMessage', 'insight.png', $userID, NOW(), 'daily_tracking'
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_notifications
                    WHERE userID = $userID
                    AND type = 'daily_tracking'
                    AND DATE(createdAt) = '$today'
                    AND message = '$notifMessage'
                )
            ");
        }
    }
}

echo "Daily insights generated successfully!";
?>
