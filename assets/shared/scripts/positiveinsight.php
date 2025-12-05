<?php
include("../connect.php");

// Monthly mode
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');
$currentMonth = date('F');
$currentYear = date('Y');

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

        // Fetch ALL allocations (default + custom)
        $getAllocation = "SELECT userCategoryID, necessityType, limitType, value AS allocationValue
                          FROM tbl_userAllocation
                          WHERE userBudgetRuleID = $userBudgetRuleID";
        $allocationResult = executeQuery($getAllocation);

        if (mysqli_num_rows($allocationResult) > 0) {

            while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {

                $userCategoryID = $allocationRow['userCategoryID'];
                $necessityType  = $allocationRow['necessityType']; // needs, wants, saving, or custom
                $limitType      = $allocationRow['limitType'];     // 0 = fixed, 1 = %
                $value          = $allocationRow['allocationValue'];

                // Compute budget limit
                $budgetLimit = ($limitType == 1)
                                ? ($totalIncome * $value / 100)
                                : $value;

                /* ====================================================
                     1. PROCESS DEFAULT ALLOCATION (userCategoryID = 0)
                   ==================================================== */
                if ($userCategoryID == 0) {

                    // SAVINGS
                    if ($necessityType === 'saving') {
                        $query = "SELECT COALESCE(SUM(gt.amount),0) AS totalAmount
                                  FROM tbl_goaltransactions gt
                                  JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
                                  WHERE sg.userID = $userID
                                  AND gt.transaction = 'add'
                                  AND DATE(gt.date) BETWEEN '$startDate' AND '$endDate'";
                    }
                    // NEEDS / WANTS
                    else {
                        $query = "SELECT COALESCE(SUM(e.amount),0) AS totalAmount
                                  FROM tbl_expense e
                                  JOIN tbl_usercategories uc ON e.userCategoryID = uc.userCategoryID
                                  WHERE e.userID = $userID
                                  AND uc.userNecessityType = '$necessityType'
                                  AND DATE(e.dateSpent) BETWEEN '$startDate' AND '$endDate'";
                    }

                } 
                /* ====================================================
                        2. PROCESS CUSTOM ALLOCATION (userCategoryID != 0)
                   ==================================================== */
                else {

                    // Get flexible/limit type
                    $getCustom = "SELECT userisFlexible, categoryName
                                  FROM tbl_usercategories 
                                  WHERE userCategoryID = $userCategoryID";
                    $customRes = executeQuery($getCustom);
                    $customRow = mysqli_fetch_assoc($customRes);

                    $userisFlexible = intval($customRow['userisFlexible']); // 1 = limit, 0 = track
                    $categoryName   = $customRow['categoryName'];

                    // Custom category spending
                    $query = "SELECT COALESCE(SUM(amount),0) AS totalAmount
                              FROM tbl_expense
                              WHERE userID = $userID
                              AND userCategoryID = $userCategoryID
                              AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'";
                }

                /* ====================================================
                         FETCH ACTUAL AMOUNT
                   ==================================================== */
                $totalResult = executeQuery($query);
                $totalRow = mysqli_fetch_assoc($totalResult);
                $actualAmount = floatval($totalRow['totalAmount'] ?? 0);

                $insightMessage = "";
                $notifMessage = "";


                /* ====================================================
                  3. POSITIVE INSIGHTS — DEFAULT NEEDS/WANTS/SAVINGS
                   ==================================================== */
                if ($userCategoryID == 0) {

                    if ($necessityType === 'saving' && $actualAmount >= $budgetLimit) {
                        $percent = round(($actualAmount / $budgetLimit) * 100);
                        $insightMessage = "Excellent! You achieved $percent% of your Savings goal for $currentMonth $currentYear.";
                        $notifMessage = "Great job! You saved ₱".number_format($actualAmount,2)." this month!";
                    }
                    else if (($necessityType === 'needs' || $necessityType === 'wants') && $actualAmount <= $budgetLimit) {
                        $percent = round(($actualAmount / $budgetLimit) * 100);
                        $label = ucfirst($necessityType);
                        $insightMessage = "Great job! You only used $percent% of your $label budget this month.";
                        $notifMessage = "Nice! You stayed within your $label budget this month.";
                    }
                }


                /* ====================================================
                 4. POSITIVE INSIGHTS — CUSTOM CATEGORIES
                   ==================================================== */
                else {

                    // LIMITED CUSTOM CATEGORY (Budget applied)
                    if ($userisFlexible == 1) {

                        if ($actualAmount <= $budgetLimit) {
                            $percent = round(($actualAmount / $budgetLimit) * 100);

                            $insightMessage = 
                                "Good job! You used only $percent% of your \"$categoryName\" budget this month.";

                            $notifMessage = 
                                "You managed your \"$categoryName\" spending well — just ₱".number_format($actualAmount,2).".";
                        }
                    }

                    // TRACKED-ONLY (No limit enforcement)
                    else {

                        $percent = $budgetLimit > 0
                                   ? round(($actualAmount / $budgetLimit) * 100)
                                   : round(($actualAmount / $totalIncome) * 100);

                        $insightMessage = 
                            "Tracking update: You spent $percent% in your \"$categoryName\" category this month.";

                        $notifMessage = 
                            "You spent ₱".number_format($actualAmount,2)." on \"$categoryName\" this month (tracked only).";
                    }
                }


                /* ====================================================
                   5. INSERT INSIGHT + NOTIFICATION IF MESSAGE EXISTS
                   ==================================================== */
                if (!empty($insightMessage)) {

                    // Avoid duplicate insight
                    $checkInsight = "SELECT 1 FROM tbl_spendinginsights
                                     WHERE userID = $userID
                                     AND insightType = 'positive'
                                     AND message = '$insightMessage'
                                     AND DATE_FORMAT(date,'%Y-%m') = '".date('Y-m')."'
                                     LIMIT 1";
                    if (mysqli_num_rows(executeQuery($checkInsight)) == 0) {
                        executeQuery("INSERT INTO tbl_spendinginsights 
                                      (userID, categoryA, insightType, message, date)
                                      VALUES ($userID, $userCategoryID, 'positive', '$insightMessage', NOW())");
                    }

                    // Avoid duplicate notification
                    $checkNotif = "SELECT 1 FROM tbl_notifications
                                   WHERE userID = $userID
                                   AND message = '$notifMessage'
                                   AND type = 'positive_insight'
                                   AND DATE_FORMAT(createdAt,'%Y-%m') = '".date('Y-m')."'
                                   LIMIT 1";
                    if (mysqli_num_rows(executeQuery($checkNotif)) == 0) {
                        executeQuery("INSERT INTO tbl_notifications
                                      (notificationTitle, message, icon, userID, createdAt, type)
                                      VALUES 
                                      ('Positive Insight', '$notifMessage', 'insight.png', 
                                       $userID, NOW(), 'positive_insight')");
                    }
                }

            } // end allocation loop
        }
    }
}

echo "Monthly positive insights generated successfully!";
?>
