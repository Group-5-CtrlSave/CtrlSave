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

        if (mysqli_num_rows($allocationResult) > 0) {
            while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {
                $userCategoryID = $allocationRow['userCategoryID'];
                $necessityType = $allocationRow['necessityType'];
                $limitType = $allocationRow['limitType'];
                $value = $allocationRow['allocationValue'];

                // Only process savings
                if ($userCategoryID != 0 || $necessityType !== 'saving') {
                    continue;
                }

                // Calculate allocated savings
                $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

                // Fetch total savings this month from goaltransactions
                $savingsQuery = "SELECT COALESCE(SUM(gt.amount),0) as totalSaved
                                 FROM tbl_goaltransactions gt
                                 JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
                                 WHERE sg.userID = $userID
                                 AND DATE(gt.date) BETWEEN '$startDate' AND '$endDate'
                                 AND gt.transaction = 'add'";
                $savingsResult = executeQuery($savingsQuery);
                $savingsRow = mysqli_fetch_assoc($savingsResult);
                $totalSaved = floatval($savingsRow['totalSaved'] ?? 0);

                // Check oversaving
                if ($totalSaved > $budgetLimit) {
                    $oversavePercent = ($totalSaved / $budgetLimit) * 100;

                    $message = "You saved ₱" . number_format($totalSaved,2) . 
                               " in $currentMonth $currentYear, which is " . round($oversavePercent,1) . 
                               "% of your allocated savings budget of ₱" . number_format($budgetLimit,2) . 
                               ". Consider redirecting excess savings to needs or wants if necessary.";

                    // Avoid duplicate entry (INSIGHTS)
                    $checkInsight = "SELECT 1 FROM tbl_spendinginsights 
                                     WHERE userID = $userID 
                                     AND insightType = 'oversaving'
                                     AND categoryA IS NULL
                                     AND DATE_FORMAT(date,'%Y-%m') = '" . date('Y-m') . "'
                                     LIMIT 1";

                    if (mysqli_num_rows(executeQuery($checkInsight)) == 0) {
                        $insertInsight = "INSERT INTO tbl_spendinginsights 
                                          (userID, categoryA, insightType, message, date)
                                          VALUES ($userID, NULL, 'oversaving', '$message', NOW())";
                        executeQuery($insertInsight);
                    }

                    /* ===========================================================
                       ✅ NEW: MONTHLY OVERSAVING NOTIFICATION (NO DUPLICATES)
                    ============================================================ */

                    $notifMessage = "You oversaved ₱" . number_format(($totalSaved - $budgetLimit),2) .
                                    " from $startDate to $endDate.";

                    // Check duplicate notification
                    $checkNotif = "SELECT 1 FROM tbl_notifications
                                   WHERE userID = $userID
                                   AND message = '$notifMessage'
                                   AND type = 'monthly_oversaving'
                                   AND DATE_FORMAT(createdAt,'%Y-%m') = '" . date('Y-m') . "'
                                   LIMIT 1";

                    if (mysqli_num_rows(executeQuery($checkNotif)) == 0) {
                        $insertNotif = "INSERT INTO tbl_notifications
                                        (notificationTitle, message, icon, userID, createdAt, type)
                                        VALUES 
                                        ('Monthly Oversaving Alert', '$notifMessage', 'savings.png',
                                         $userID, NOW(), 'monthly_oversaving')";
                        executeQuery($insertNotif);
                    }

                }
            }
        }
    }
    echo "Monthly oversaving insights generated successfully!";
}
?>
