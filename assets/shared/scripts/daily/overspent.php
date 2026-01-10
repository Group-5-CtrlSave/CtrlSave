<?php
include("../../connect.php");

// Daily Mode
$startDate = date('Y-m-d');

// Get the Active Budget Version
$getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
FROM tbl_userbudgetversion 
WHERE isActive = 1";



$budgetVersionResult = executeQuery($getBudgetVersion);
if (mysqli_num_rows($budgetVersionResult) > 0) {
    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {
        $userID = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome = floatval($budgetVersionRow['totalIncome']);

        // Fetch Track Only Expenses

        $getTrackOnlyExpenses = "SELECT COALESCE(SUM(e.amount), 0) as totalTrackOnly FROM tbl_expense e 
            LEFT JOIN tbl_usercategories uc 
            ON e.userCategoryID = uc.userCategoryID
             WHERE e.userID = $userID AND DATE(e.dateSpent) = '$startDate' AND isDeleted = 0 AND uc.userisFlexible = 0";

        $totalTrackResult = executeQuery($getTrackOnlyExpenses);
        $totalTrackOnlyRow = mysqli_fetch_assoc($totalTrackResult);
        $totalTrackSpent = floatval($totalTrackOnlyRow['totalTrackOnly'] ?? 0);

        // Get Allocation
        $getUserAllocation = "SELECT 
                ua.userCategoryID,
                ua.necessityType,
                ua.limitType,
                ua.value AS allocationValue,
                uc.userisFlexible,
                uc.userID
            FROM tbl_userallocation ua
            LEFT JOIN tbl_usercategories uc 
                ON ua.userCategoryID = uc.userCategoryID
            WHERE ua.userBudgetRuleID = $userBudgetRuleID and ua.necessityType != 'saving'";

        $userAllocationResult = executeQuery($getUserAllocation);

        if (mysqli_num_rows($userAllocationResult) > 0) {
            while ($userAllocationRow = mysqli_fetch_assoc($userAllocationResult)) {
                $userCategoryID = $userAllocationRow['userCategoryID'];
                $userNecessityType = $userAllocationRow['necessityType'];
                $limitType = $userAllocationRow['limitType'];
                $userAllocationValue = $userAllocationRow['allocationValue'];
                $userisFlexible = $userAllocationRow['userisFlexible'];





                if ($userCategoryID == 0) {
                    // If Needs / Wants 
                    $expenseQuery = " SELECT COALESCE(SUM(e.amount),0) AS totalSpent
                        FROM tbl_expense e
                        JOIN tbl_usercategories c ON c.userCategoryID = e.userCategoryID
                        WHERE e.userID = $userID
                        AND c.userNecessityType = '$userNecessityType'
                        AND DATE(e.dateSpent) = '$startDate'
                        AND c.userisFlexible = 1
                        AND e.isDeleted = 0;
                        
                        
                    ";
                } else {
                    // Custom Budget Rule
                    $expenseQuery = "
                        SELECT COALESCE(SUM(e.amount),0) AS totalSpent
                        FROM tbl_expense e
                        JOIN tbl_usercategories c ON c.userCategoryID = e.userCategoryID
                        WHERE e.userID = $userID
                        AND e.userCategoryID = $userCategoryID
                        AND DATE(e.dateSpent) = '$startDate'
                        AND e.isDeleted = 0
                        AND c.userisFlexible = 1;
                    ";
                }
                $expenseResult = executeQuery($expenseQuery);
                $expenseRow = mysqli_fetch_assoc($expenseResult);
                $totalSpent = floatval($expenseRow['totalSpent'] ?? 0);

                if ($totalSpent == 0) {
                    $categoryLabel = $userNecessityType ?? "Unknown Category";
                    // Delete Notification
                    $categoryCheck = ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID");

                //     $deleteNotif = "
                // DELETE FROM tbl_notifications
                // WHERE userID = $userID
                // AND type = 'daily_overspending'
                // AND DATE(createdAt) = '$startDate'
                // // ";
                //     executeQuery($deleteNotif);

                    // Delete Insights
                    $deleteInsights = "
                DELETE FROM tbl_spendinginsights
                WHERE userID = $userID
                AND insightType IN ('daily_overspending', 'daily_overspending_message')
                AND categoryA $categoryCheck
                AND DATE(date) = '$startDate'
                ";

                    executeQuery($deleteInsights);
                continue;
                }

                $totalBalance = $totalIncome - $totalTrackSpent;


                // Percentage or Amount 
                $budgetLimit = ($limitType == 1) ? ($totalBalance * $userAllocationValue / 100) : $userAllocationValue;

                // Check overspending
                if ($totalSpent > $budgetLimit) {
                    $overamount = $totalSpent - $budgetLimit;
                    // Determine Label
                    if ($userCategoryID == 0) {
                        $categoryLabel = $userNecessityType ?? "Unknown Category";
                    } else {
                        $catQuery = "SELECT categoryName FROM tbl_usercategories WHERE userCategoryID = $userCategoryID LIMIT 1";
                        $catResult = executeQuery($catQuery);
                        $catRow = mysqli_fetch_assoc($catResult);
                        $categoryLabel = $catRow['categoryName'] ?? "Unknown Category";
                    }

                    // Build the message
                    $overspentmessage = "Oh No! You have spent your allotted budget for the whole month. 
                You have overspent $overamount in $categoryLabel today ($startDate). Your target limit was ₱$budgetLimit";

                    $overspentnotif = "You have overspent ₱$overamount in $categoryLabel today ($startDate). Your target limit for the month was ₱$budgetLimit.";

                    $overspentInsight = "Spending your monthly budget this early suggest a spending surge. if this was unexpected, consider spreading expenses more 
                unevenly across the month.";

                    // Insert Notification
                    $insertNotif = " INSERT INTO tbl_notifications(notificationTitle, message, icon, userID, createdAt, type)
                SELECT 'Daily Overspending Alert', '$overspentnotif', 'alert.svg', $userID, NOW(), 'daily_overspending'
                FROM dual
                WHERE NOT EXISTS (
                SELECT 1 FROM tbl_notifications
                WHERE userID = $userID
                AND message = '$overspentnotif'
                AND DATE(createdAt) = '$startDate')";
                executeQuery($insertNotif);


                    // Insert Insight
                    $categoryCheck = ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID");
                    
                    

                    $insertInsight = " INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
                    SELECT $userID, NULL, 'daily_overspending', '$overspentInsight', NOW()
                    FROM dual
                    WHERE NOT EXISTS (
                    SELECT 1
                    FROM tbl_spendinginsights
                    WHERE userID = $userID
                    AND insightType = 'daily_overspending'
                    AND DATE(date) = '$startDate')";

                    executeQuery($insertInsight);

                    // Insert Message

                    $categoryCheck = ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID");

                    // Check if Message already exists

                    $checkExistingMessage = "SELECT 1 from tbl_spendinginsights 
                    WHERE userID = $userID AND insightType = 'daily_overspending_message' AND necessityType = '$userNecessityType' LIMIT 1";

                    $checkExistingResult = executeQuery($checkExistingMessage);
                    if (mysqli_num_rows($checkExistingResult)> 0){
                        $updateInsight ="UPDATE `tbl_spendinginsights` SET `message`='$overspentmessage' 
                        WHERE userID = $userID AND insightType = 'daily_overspending_message' AND necessityType = '$userNecessityType' AND DATE(date)";

                        executeQuery($updateInsight);
                    }else{
                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, necessityType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_overspending_message', '$userNecessityType', '$overspentmessage', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_overspending_message'
                            AND categoryA  $categoryCheck
                            AND message = '$overspentmessage'
                            AND DATE(date) = '$startDate'
                        )
                    ";
                    executeQuery($insertInsight);


                    // Delete Notification
                    $categoryCheck = ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID");

                    $deleteNotif = "
                DELETE FROM tbl_notifications
                WHERE userID = $userID
                AND type = 'daily_nooverspending'
                AND DATE(createdAt) = '$startDate'
                ";
                    executeQuery($deleteNotif);

                    // Delete Insights
                    $deleteInsights = "
                DELETE FROM tbl_spendinginsights
                WHERE userID = $userID
                AND insightType IN ('daily_nooverspending', 'daily_nooverspending_message')
                AND categoryA $categoryCheck
                AND DATE(date) = '$startDate'
                ";

                    executeQuery($deleteInsights);
                 }



                } else {
                    // Build the message
                    $nooverspentmessage = "Great Job! You have no overspent budget for today ($startDate) Keep Spending Smart.";
                    if  ($totalSpent == 0){
                        continue;
                    }else if ($totalBalance == $totalSpent) {
                        $nooverspentInsight = "You are on track of your budget. Keep going and achieve your saving goal!";
                    } else if ($totalBalance > $totalSpent) {
                        $nooverspentInsight = "You managed your spending well today ($startDate). The remaining balance could be added to savings or kept as a buffer for next month.";
                    }


                    // Insert Insight
                    $categoryCheck = ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID");

                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_nooverspending', '$nooverspentInsight', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_nooverspending'
                            AND categoryA $categoryCheck
                            AND message = '$nooverspentInsight'
                            AND DATE(date) = '$startDate'
                        )
                    ";
                    executeQuery($insertInsight);

                    // Insert Message

                    $categoryCheck = ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID");

                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights(userID, categoryA, insightType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_nooverspending_message', '$nooverspentmessage', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_nooverspending_message'
                            AND categoryA $categoryCheck
                            AND message = '$nooverspentmessage'
                            AND DATE(date) = '$startDate'
                        )
                    ";
                    executeQuery($insertInsight);

                    // Delete Notification
                   
                    $deleteNotif = "
                DELETE FROM tbl_notifications
                WHERE userID = $userID
                AND type = 'daily_overspending'
                AND DATE(createdAt) = '$startDate'
                ";
                    executeQuery($deleteNotif);

                    // Delete Insights
                        $deleteInsights = "DELETE FROM tbl_spendinginsights
                        WHERE userID = $userID
                        AND insightType IN ('daily_overspending', 'daily_overspending_message')
                        AND DATE(date) = '$startDate'";
                        executeQuery($deleteInsights);






                }



            }
        }
    }

}
echo "Daily overspending insights and notifications generated successfully!";


?>