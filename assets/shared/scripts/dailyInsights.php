<?php
include("../connect.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$today = date('Y-m-d');
$startOfMonth = date('Y-m-01');
$endOfMonth = date('Y-m-t');

// Get Active Budget Versions
$getBudgetVersion = "
    SELECT userID, userBudgetRuleID, totalIncome 
    FROM tbl_userbudgetversion 
    WHERE isActive = 1
";
$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {
    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {

        $userID = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome = floatval($budgetVersionRow['totalIncome']);

        $allTrackingParts = [];

        // ============================
        // 1. POSITIVE SPENDING INSIGHTS
        // ============================
        foreach (['need', 'want'] as $type) {
           $allocRes = executeQuery("
    SELECT value AS allocationValue, limitType
    FROM tbl_userallocation
    WHERE userBudgetRuleID = $userBudgetRuleID
    AND userCategoryID = 0
    AND necessityType = '$type'
");

            $allocRow = mysqli_fetch_assoc($allocRes);
            if (!$allocRow) continue;

            $monthlyBudget = ($allocRow['limitType'] == 1) 
                ? ($totalIncome * $allocRow['allocationValue'] / 100) 
                : $allocRow['allocationValue'];

            $catRes = executeQuery("
                SELECT userCategoryID, categoryName, userisFlexible
                FROM tbl_usercategories
                WHERE userNecessityType = '$type'
            ");

            while ($catRow = mysqli_fetch_assoc($catRes)) {
                $userCategoryID = $catRow['userCategoryID'];
                $categoryName = $catRow['categoryName'];
                $userisFlexible = intval($catRow['userisFlexible']);

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
                    $monthSpentRes = executeQuery("
                        SELECT COALESCE(SUM(amount),0) AS monthSpent
                        FROM tbl_expense
                        WHERE userID = $userID
                        AND userCategoryID = $userCategoryID
                        AND DATE(dateSpent) BETWEEN '$startOfMonth' AND '$today'
                    ");
                    $monthSpent = floatval(mysqli_fetch_assoc($monthSpentRes)['monthSpent'] ?? 0);

                    if ($monthSpent <= $monthlyBudget) {
                        $percentUsed = ($monthlyBudget > 0) ? round(($monthSpent / $monthlyBudget) * 100, 2) : 0;
                        $message = "You spent ₱" . number_format($todaySpent,2) . " on $categoryName today. You are on track: {$percentUsed}% of your monthly limit used.";

                        // Insert insight
                        executeQuery("
                            INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
                            SELECT $userID, $userCategoryID, 'daily_positive', '$message', NOW()
                            FROM DUAL
                            WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_spendinginsights
                                WHERE userID = $userID
                                AND categoryA = $userCategoryID
                                AND insightType = 'daily_positive'
                                AND DATE(date) = '$today'
                                AND message = '$message'
                            )
                        ");

                        // Insert notification
                        executeQuery("
                            INSERT INTO tbl_notifications(notificationTitle, message, icon, userID, createdAt, type)
                            SELECT 'Daily Positive Insight', '$message', 'insight.png', $userID, NOW(), 'daily_positive_insight'
                            FROM DUAL
                            WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_notifications
                                WHERE userID = $userID
                                AND type = 'daily_positive'
                                AND DATE(createdAt) = '$today'
                                AND message = '$message'
                            )
                        ");
                    }
                } else {
                    $allTrackingParts[] = "You spent ₱" . number_format($todaySpent, 2) . " on $categoryName (Track Only)";
                }
            }
        }

        // ============================
        // 2. CUSTOM ALLOCATIONS (POSITIVE SPENDING)
        // ============================
    $customAllocRes = executeQuery("
    SELECT userCategoryID, limitType, value AS allocationValue
    FROM tbl_userallocation
    WHERE userBudgetRuleID = $userBudgetRuleID
    AND userCategoryID != 0
");

        while ($allocRow = mysqli_fetch_assoc($customAllocRes)) {
            $userCategoryID = $allocRow['userCategoryID'];
            $catRes = executeQuery("SELECT categoryName, userisFlexible FROM tbl_usercategories WHERE userCategoryID = $userCategoryID");
            $catRow = mysqli_fetch_assoc($catRes);
            $categoryName = $catRow['categoryName'];
            $userisFlexible = intval($catRow['userisFlexible']);

            $todaySpentRes = executeQuery("
                SELECT COALESCE(SUM(amount),0) AS todaySpent
                FROM tbl_expense
                WHERE userID = $userID
                AND userCategoryID = $userCategoryID
                AND DATE(dateSpent) = '$today'
            ");
            $todaySpent = floatval(mysqli_fetch_assoc($todaySpentRes)['todaySpent'] ?? 0);
            if ($todaySpent <= 0) continue;

            $monthlyBudget = ($allocRow['limitType'] == 1) ? ($totalIncome * $allocRow['allocationValue'] / 100) : $allocRow['allocationValue'];

            if ($userisFlexible == 1) {
                $monthSpentRes = executeQuery("
                    SELECT COALESCE(SUM(amount),0) AS monthSpent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = $userCategoryID
                    AND DATE(dateSpent) BETWEEN '$startOfMonth' AND '$today'
                ");
                $monthSpent = floatval(mysqli_fetch_assoc($monthSpentRes)['monthSpent'] ?? 0);

                if ($monthSpent <= $monthlyBudget) {
                    $percentUsed = ($monthlyBudget > 0) ? round(($monthSpent / $monthlyBudget) * 100, 2) : 0;
                    $message = "You spent ₱" . number_format($todaySpent,2) . " on $categoryName today. You are on track: {$percentUsed}% of your monthly limit used.";

                    executeQuery("
                        INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
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
                        INSERT INTO tbl_notifications(notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'Daily Positive Insight', '$message', 'insight.png', $userID, NOW(), 'daily_positive_insight'
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND type = 'daily_positive'
                            AND DATE(createdAt) = '$today'
                            AND message = '$message'
                        )
                    ");
                }
            } else {
                $allTrackingParts[] = "You spent ₱" . number_format($todaySpent, 2) . " on $categoryName (Track Only)";
            }
        }

        // ============================
        // 3. TRACKING-ONLY INSIGHTS
        // ============================
        if (!empty($allTrackingParts)) {
            $insightMessage = "Tracking Categories Only: " . implode(", ", $allTrackingParts) . " today.";
            $notifMessage = implode(", ", $allTrackingParts);

            executeQuery("
                INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
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
                INSERT INTO tbl_notifications(notificationTitle, message, icon, userID, createdAt, type)
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

        // ============================
        // 4. DAILY SAVINGS INSIGHTS
        // ============================
     $getAllocation = "
    SELECT userCategoryID, limitType, value AS allocationValue
    FROM tbl_userallocation
    WHERE userBudgetRuleID = $userBudgetRuleID
    AND necessityType = 'saving'
";

        $allocationResult = executeQuery($getAllocation);

        while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {
            $userCategoryID = $allocationRow['userCategoryID'];
            $limitType = $allocationRow['limitType'];
            $value = $allocationRow['allocationValue'];

            $budgetLimit = ($userCategoryID == 0 && $limitType == 1) ? ($totalIncome * $value / 100) : $value;

            $savingsQuery = "
                SELECT COALESCE(SUM(gt.amount), 0) AS totalSaved
                FROM tbl_goaltransactions gt
                JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
                WHERE sg.userID = $userID
                AND gt.transaction = 'add'
            ";
            if ($userCategoryID != 0) $savingsQuery .= " AND gt.savingGoalID = $userCategoryID";
            $savingsQuery .= " AND DATE(gt.date) = '$today'";

            $savingsResult = executeQuery($savingsQuery);
            $totalSaved = floatval(mysqli_fetch_assoc($savingsResult)['totalSaved'] ?? 0);

            if ($totalSaved > $budgetLimit) {
                $oversavePercent = round(($totalSaved / $budgetLimit) * 100, 1);
                $message = "You saved ₱" . number_format($totalSaved,2) . " today, exceeding your planned savings of ₱" . number_format($budgetLimit,2) . " ({$oversavePercent}%).";
                $notifMessage = "You oversaved ₱" . number_format($totalSaved - $budgetLimit, 2) . " today.";
                $insightType = 'daily_oversaving';
            } else if ($totalSaved > 0) {
                $message = "Great job! You saved ₱" . number_format($totalSaved,2) . " today, staying within your planned savings.";
                $notifMessage = "Congrats! You saved ₱" . number_format($totalSaved,2) . " today.";
                $insightType = 'daily_positive_saving';
            } else {
                $message = "No savings recorded today. Try setting aside even a small amount.";
                $notifMessage = "You did not save anything today.";
                $insightType = 'daily_no_saving';
            }

            executeQuery("
                INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
                SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", '$insightType', '$message', NOW()
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_spendinginsights
                    WHERE userID = $userID
                    AND insightType = '$insightType'
                    AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                    AND DATE(date) = '$today'
                )
            ");
            executeQuery("
                INSERT INTO tbl_notifications(notificationTitle, message, icon, userID, createdAt, type)
                SELECT 'Daily Oversaving Alert!', '$notifMessage', 'savings.png', $userID, NOW(), '$insightType'
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_notifications
                    WHERE userID = $userID
                    AND message = '$notifMessage'
                    AND type = '$insightType'
                    AND DATE(createdAt) = '$today'
                )
            ");
        }

        // ============================
        // 5. DAILY OVERSPENDING INSIGHTS
        // ============================
  $getAllocation = "
    SELECT ua.userCategoryID, ua.necessityType, ua.limitType, ua.value AS allocationValue, uc.userisFlexible
    FROM tbl_userallocation ua
    LEFT JOIN tbl_usercategories uc ON ua.userCategoryID = uc.userCategoryID
    WHERE ua.userBudgetRuleID = $userBudgetRuleID
";
        $allocationResult = executeQuery($getAllocation);

        while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {
            $userCategoryID = $allocationRow['userCategoryID'];
            $necessityType = $allocationRow['necessityType'];
            $limitType = $allocationRow['limitType'];
            $value = $allocationRow['allocationValue'];
            $userisFlexible = $allocationRow['userisFlexible'] ?? 1;

            if ($userCategoryID != 0 && $userisFlexible == 0) continue;

            if ($userCategoryID == 0) {
                $expenseQuery = "
                    SELECT COALESCE(SUM(e.amount),0) AS totalSpent
                    FROM tbl_expense e
                    JOIN tbl_usercategories c ON c.userCategoryID = e.userCategoryID
                    WHERE e.userID = $userID
                    AND c.userNecessityType = '$necessityType'
                    AND DATE(e.dateSpent) = '$today'
                ";
            } else {
                $expenseQuery = "
                    SELECT COALESCE(SUM(amount),0) AS totalSpent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = $userCategoryID
                    AND DATE(dateSpent) = '$today'
                ";
            }

            $totalSpent = floatval(mysqli_fetch_assoc(executeQuery($expenseQuery))['totalSpent'] ?? 0);
            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

            if ($totalSpent > $budgetLimit) {
                $overAmount = $totalSpent - $budgetLimit;
                $categoryLabel = ($userCategoryID == 0) ? $necessityType : (mysqli_fetch_assoc(executeQuery("SELECT categoryName FROM tbl_usercategories WHERE userCategoryID = $userCategoryID LIMIT 1"))['categoryName'] ?? 'Unknown');

                $message = "You have overspent ₱$overAmount in $categoryLabel today ($today). Your target limit was ₱$budgetLimit.";

                executeQuery("
                    INSERT INTO tbl_notifications(notificationTitle, message, icon, userID, createdAt, type)
                    SELECT 'Daily Overspending Alert!', '$message', 'alert.png', $userID, NOW(), 'daily_overspending'
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_notifications
                        WHERE userID = $userID
                        AND message = '$message'
                        AND DATE(createdAt) = '$today'
                    )
                ");
                executeQuery("
                    INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
                    SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_overspending', '$message', NOW()
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_spendinginsights
                        WHERE userID = $userID
                        AND insightType = 'daily_overspending'
                        AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                        AND message = '$message'
                        AND DATE(date) = '$today'
                    )
                ");
            }
        }

    } // end budget loop
}

echo "All daily insights and notifications generated successfully!";
?>
