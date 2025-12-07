<?php
include("../connect.php");

// Monthly mode
$startDate   = date('Y-m-01');
$endDate     = date('Y-m-t');
$currentMonth = date('F');
$currentYear  = date('Y');

// ===============================
// GET ACTIVE BUDGET VERSIONS
// ===============================
$getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                     FROM tbl_userbudgetversion 
                     WHERE isActive = 1";

$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {

    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {

        $userID           = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome      = floatval($budgetVersionRow['totalIncome']);

        // ===============================
        // GET SAVING ALLOCATIONS
        // ===============================
        $getAllocation = "SELECT userCategoryID, necessityType, limitType, value AS allocationValue
                          FROM tbl_userallocation
                          WHERE userBudgetRuleID = $userBudgetRuleID
                          AND necessityType = 'saving'";

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

                $savingsQuery = "SELECT COALESCE(SUM(gt.amount), 0) AS totalSaved
                                 FROM tbl_goaltransactions gt
                                 JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
                                 WHERE sg.userID = $userID
                                 AND gt.transaction = 'add'
                                 AND DATE(gt.date) BETWEEN '$startDate' AND '$endDate'";

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
                               " in $currentMonth $currentYear, exceeding your budget of ₱" . number_format($budgetLimit, 2) . 
                               " (" . $oversavePercent . "%). Consider balancing savings with other needs.";

                    // Insert insight only if not exists
                    $insertInsight = "INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                                      SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'oversaving', '$message', NOW()
                                      FROM dual
                                      WHERE NOT EXISTS (
                                        SELECT 1 FROM tbl_spendinginsights
                                        WHERE userID = $userID
                                        AND insightType = 'oversaving'
                                        AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                                        AND DATE_FORMAT(date, '%Y-%m') = '" . date('Y-m') . "'
                                      )";
                    executeQuery($insertInsight);

                    $notifMessage = "You oversaved ₱" . number_format($totalSaved - $budgetLimit, 2) . " this month.";

                    // Insert notification only if not exists
                    $insertNotif = "INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                                    SELECT 'Monthly Oversaving Alert', '$notifMessage', 'savings.png', $userID, NOW(), 'monthly_oversaving'
                                    FROM dual
                                    WHERE NOT EXISTS (
                                        SELECT 1 FROM tbl_notifications
                                        WHERE userID = $userID
                                        AND message = '$notifMessage'
                                        AND type = 'monthly_oversaving'
                                        AND DATE_FORMAT(createdAt, '%Y-%m') = '" . date('Y-m') . "'
                                    )";
                    executeQuery($insertNotif);

                } 
                // POSITIVE SAVING
                else if ($totalSaved > 0 && $totalSaved <= $budgetLimit) {
                    $message = "Excellent Saver! You saved ₱" . number_format($totalSaved, 2) . 
                               " in $currentMonth $currentYear. You are staying within your planned savings budget!";

                    $insertInsight = "INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                                      SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'positive_saving', '$message', NOW()
                                      FROM dual
                                      WHERE NOT EXISTS (
                                        SELECT 1 FROM tbl_spendinginsights
                                        WHERE userID = $userID
                                        AND insightType = 'positive_saving'
                                        AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                                        AND DATE_FORMAT(date, '%Y-%m') = '" . date('Y-m') . "'
                                      )";
                    executeQuery($insertInsight);

                    $notifMessage = "Congrats! You saved ₱" . number_format($totalSaved, 2) . " this month.";

                    $insertNotif = "INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                                    SELECT 'Savings Success', '$notifMessage', 'savings.png', $userID, NOW(), 'monthly_saving_positive'
                                    FROM dual
                                    WHERE NOT EXISTS (
                                        SELECT 1 FROM tbl_notifications
                                        WHERE userID = $userID
                                        AND message = '$notifMessage'
                                        AND type = 'monthly_saving_positive'
                                        AND DATE_FORMAT(createdAt, '%Y-%m') = '" . date('Y-m') . "'
                                    )";
                    executeQuery($insertNotif);

                } 
                // NO SAVING
                else if ($totalSaved == 0) {
                    $message = "You have no recorded savings for $currentMonth $currentYear. Try setting aside even a small amount to build financial stability.";

                    $insertInsight = "INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                                      SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'no_saving', '$message', NOW()
                                      FROM dual
                                      WHERE NOT EXISTS (
                                        SELECT 1 FROM tbl_spendinginsights
                                        WHERE userID = $userID
                                        AND insightType = 'no_saving'
                                        AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                                        AND DATE_FORMAT(date, '%Y-%m') = '" . date('Y-m') . "'
                                      )";
                    executeQuery($insertInsight);

                    $notifMessage = "You did not save anything this month.";

                    $insertNotif = "INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                                    SELECT 'No Savings Recorded', '$notifMessage', 'savings.png', $userID, NOW(), 'monthly_no_saving'
                                    FROM dual
                                    WHERE NOT EXISTS (
                                        SELECT 1 FROM tbl_notifications
                                        WHERE userID = $userID
                                        AND message = '$notifMessage'
                                        AND type = 'monthly_no_saving'
                                        AND DATE_FORMAT(createdAt, '%Y-%m') = '" . date('Y-m') . "'
                                    )";
                    executeQuery($insertNotif);
                }

            } // allocation loop
        }

    } // budget version loop

    echo "Monthly savings insights generated successfully!";
}
?>
