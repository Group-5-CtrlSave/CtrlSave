<?php
include("../connect.php");

$today = date('Y-m-d');

$getBudgetVersion = "
    SELECT userID, userBudgetRuleID, totalIncome 
    FROM tbl_userbudgetversion 
    WHERE isActive = 1
";
$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {

    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {

        $userID           = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome      = floatval($budgetVersionRow['totalIncome']);

        // ===============================
        // GET SAVING ALLOCATIONS
        // ===============================
        $getAllocation = "
            SELECT userCategoryID, necessityType, limitType, value AS allocationValue
            FROM tbl_userallocation
            WHERE userBudgetRuleID = $userBudgetRuleID
            AND necessityType = 'saving'
        ";

        $allocationResult = executeQuery($getAllocation);

        if (mysqli_num_rows($allocationResult) > 0) {

            while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {

                $userCategoryID = $allocationRow['userCategoryID'];
                $limitType      = $allocationRow['limitType'];
                $value          = $allocationRow['allocationValue'];

                // Default saving (userCategoryID = 0) → percentage-based
                // Custom saving (userCategoryID != 0) → fixed amount
                $budgetLimit = ($userCategoryID == 0 && $limitType == 1) 
                    ? ($totalIncome * ($value / 100)) 
                    : $value;

                // Today's savings only
                $savingsQuery = "
                    SELECT COALESCE(SUM(gt.amount), 0) AS totalSaved
                    FROM tbl_goaltransactions gt
                    JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
                    WHERE sg.userID = $userID
                    AND gt.transaction = 'add'
                    AND DATE(gt.date) = '$today'
                ";

                if ($userCategoryID != 0) {
                    $savingsQuery .= " AND gt.savingGoalID = $userCategoryID";
                }

                $savingsResult = executeQuery($savingsQuery);
                $savingsRow = mysqli_fetch_assoc($savingsResult);
                $totalSaved = $savingsRow ? floatval($savingsRow['totalSaved']) : 0;

                // ===============================
                // OVERSAVING
                // ===============================
                if ($totalSaved > $budgetLimit) {
                    $oversavePercent = round(($totalSaved / $budgetLimit) * 100, 1);
                    $message = "You saved ₱" . number_format($totalSaved, 2) . 
                               " today, exceeding your planned savings of ₱" . number_format($budgetLimit, 2) . 
                               " (" . $oversavePercent . "%).";

                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_oversaving', '$message', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_oversaving'
                            AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                            AND DATE(date) = '$today'
                        )
                    ";
                    executeQuery($insertInsight);

                    $notifMessage = "You oversaved ₱" . number_format($totalSaved - $budgetLimit, 2) . " today.";
                    $insertNotif = "
                        INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'Daily Oversaving Alert', '$notifMessage', 'savings.png', $userID, NOW(), 'daily_oversaving'
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND message = '$notifMessage'
                            AND type = 'daily_oversaving'
                            AND DATE(createdAt) = '$today'
                        )
                    ";
                    executeQuery($insertNotif);

                } 
                // POSITIVE SAVING
                else if ($totalSaved > 0 && $totalSaved <= $budgetLimit) {
                    $message = "Great job! You saved ₱" . number_format($totalSaved, 2) . " today, staying within your planned savings.";

                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_positive_saving', '$message', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_positive_saving'
                            AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                            AND DATE(date) = '$today'
                        )
                    ";
                    executeQuery($insertInsight);

                    $notifMessage = "Congrats! You saved ₱" . number_format($totalSaved, 2) . " today.";
                    $insertNotif = "
                        INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'Daily Savings Success', '$notifMessage', 'savings.png', $userID, NOW(), 'daily_positive_saving'
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND message = '$notifMessage'
                            AND type = 'daily_positive_saving'
                            AND DATE(createdAt) = '$today'
                        )
                    ";
                    executeQuery($insertNotif);

                } 
                // NO SAVING
                else if ($totalSaved == 0) {
                    $message = "No savings recorded today. Try setting aside even a small amount.";

                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_no_saving', '$message', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_no_saving'
                            AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                            AND DATE(date) = '$today'
                        )
                    ";
                    executeQuery($insertInsight);

                    $notifMessage = "You did not save anything today.";

                    $insertNotif = "
                        INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'No Savings Recorded', '$notifMessage', 'savings.png', $userID, NOW(), 'daily_no_saving'
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND message = '$notifMessage'
                            AND type = 'daily_no_saving'
                            AND DATE(createdAt) = '$today'
                        )
                    ";
                    executeQuery($insertNotif);
                }

            } // allocation loop
        }

    } // budget version loop

    echo "Daily savings insights generated successfully!";
}
?>
