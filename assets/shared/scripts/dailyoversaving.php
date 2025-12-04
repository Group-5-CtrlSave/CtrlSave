<?php

function checkDailyOversaving($userID) {
    // Daily mode (only today)
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d');

    // Get active budget rule for this user
    $getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                         FROM tbl_userbudgetversion 
                         WHERE isActive = 1 AND userID = $userID";
    $budgetVersionResult = executeQuery($getBudgetVersion);

    if (mysqli_num_rows($budgetVersionResult) > 0) {

        while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {
            $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
            $totalIncome = $budgetVersionRow['totalIncome'];

            // Get allocation rows
            $getAllocation = "SELECT userCategoryID, necessityType, limitType, value as allocationValue
                              FROM tbl_userAllocation 
                              WHERE userBudgetRuleID = $userBudgetRuleID";
            $allocationResult = executeQuery($getAllocation);

            if (mysqli_num_rows($allocationResult) > 0) {
                while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {

                    $userCategoryID = $allocationRow['userCategoryID'];
                    $necessityType  = $allocationRow['necessityType'];
                    $limitType      = $allocationRow['limitType'];
                    $value          = $allocationRow['allocationValue'];

                    // Only check savings (default savings row)
                    if ($userCategoryID != 0 || $necessityType !== 'saving') {
                        continue;
                    }

                    // Calculate allocated savings limit
                    if ($limitType == 1) {
                        // Percentage-based
                        $budgetLimit = ($totalIncome * $value) / 100;
                    } else {
                        // Fixed amount
                        $budgetLimit = $value;
                    }

                    // Get total saved today in goaltransactions
                    $savingsQuery = "SELECT COALESCE(SUM(gt.amount),0) as totalSaved
                                     FROM tbl_goaltransactions gt
                                     JOIN tbl_savinggoals sg 
                                     ON sg.savingGoalID = gt.savingGoalID
                                     WHERE sg.userID = $userID
                                     AND DATE(gt.date) = '$startDate'
                                     AND gt.transaction = 'add'";
                    $savingsResult = executeQuery($savingsQuery);
                    $savingsRow = mysqli_fetch_assoc($savingsResult);

                    $totalSaved = floatval($savingsRow['totalSaved'] ?? 0);

                    // Oversaved?
                    if ($totalSaved > $budgetLimit) {

                        $overAmount = $totalSaved - $budgetLimit;
                        $todayDate  = date('Y-m-d');

                        $message = "You have oversaved ₱" . number_format($overAmount,2) . 
                                   " today ($todayDate). Your target savings limit was ₱" . 
                                   number_format($budgetLimit,2) . ".";

                        // Prevent duplicate daily notification
                        $checkNotif = "SELECT 1 FROM tbl_notifications 
                                       WHERE userID = $userID 
                                       AND message = '$message'
                                       AND DATE(createdAt) = '$startDate'
                                       LIMIT 1";

                        if (mysqli_num_rows(executeQuery($checkNotif)) == 0) {
                            // Insert notification
                            $insertNotif = "INSERT INTO tbl_notifications
                                            (notificationTitle, message, icon, userID, createdAt, type)
                                            VALUES 
                                            ('Daily Oversaving Alert', '$message', 'savings.png', 
                                            $userID, NOW(), 'daily_oversaving')";
                            executeQuery($insertNotif);
                        }
                    }
                }
            }
        }
    }
}
?>
