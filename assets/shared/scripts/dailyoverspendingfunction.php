<?php

function checkDailyOverspending($userID) {
    // Daily mode: only today
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d');

    // Get Active Budget Version for this user
    $getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                         FROM tbl_userbudgetversion 
                         WHERE isActive = 1 AND userID = $userID";
    $budgetVersionResult = executeQuery($getBudgetVersion);

    if (mysqli_num_rows($budgetVersionResult) > 0) {
        while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {
            $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
            $totalIncome = $budgetVersionRow['totalIncome'];

            // Get Allocation for the Budget Version
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

                    // Fetch Expenses
                    if ($userCategoryID == 0) {
                        // Default Allocation
                        $expenseQuery = "SELECT COALESCE(SUM(tbl_expense.amount),0) as totalSpent
                                         FROM tbl_expense 
                                         JOIN tbl_usercategories 
                                         ON tbl_usercategories.userCategoryID = tbl_expense.userCategoryID
                                         WHERE tbl_expense.userID = $userID 
                                         AND tbl_usercategories.userNecessityType = '$necessityType'
                                         AND DATE(tbl_expense.dateSpent) = '$startDate'";
                    } else {
                        // User Allocation
                        $expenseQuery = "SELECT COALESCE(SUM(tbl_expense.amount), 0) AS totalSpent 
                                         FROM tbl_expense 
                                         JOIN tbl_usercategories 
                                         ON tbl_usercategories.userCategoryID = tbl_expense.userCategoryID 
                                         WHERE tbl_expense.userID = $userID
                                         AND tbl_usercategories.userNecessityType = '$necessityType'
                                         AND DATE(tbl_expense.dateSpent) = '$startDate'";
                    }

                    $expenseResult = executeQuery($expenseQuery);
                    $expenseRow = mysqli_fetch_assoc($expenseResult);
                    $totalSpent = floatval($expenseRow['totalSpent'] ?? 0);

                    // Set the Limit
                    if ($limitType == 1) {
                        $budgetLimit = ($totalIncome * $value) / 100;
                    } else {
                        $budgetLimit = $value;
                    }

                    if ($totalSpent > $budgetLimit) {
                        $overAmount = $totalSpent - $budgetLimit;

                        // Get the Category Label
                        if ($userCategoryID == 0) {
                            $categoryLabel = $necessityType;
                        } else {
                            $catQuery = "SELECT categoryName FROM tbl_usercategories WHERE userCategoryID = $userCategoryID LIMIT 1";
                            $catResult = executeQuery($catQuery);
                            $catRow = mysqli_fetch_assoc($catResult);
                            $categoryLabel = $catRow['categoryName'] ?? "Unknown Category";
                        }

                        // Build the message with current date and target limit
                        $todayDate = date('Y-m-d');
                        $message = "You have overspent ₱$overAmount in $categoryLabel today ($todayDate). Your target limit was ₱$budgetLimit.";

                        // Check if daily notification already exists
                        $checkNotif = "SELECT 1 FROM tbl_notifications 
                                       WHERE userID = $userID 
                                       AND message = '$message'
                                       AND DATE(createdAt) = '$startDate'
                                       LIMIT 1";

                        if (mysqli_num_rows(executeQuery($checkNotif)) == 0) {
                            // Insert daily notification
                            $insertNotif = "INSERT INTO tbl_notifications 
                                            (notificationTitle, message, icon, userID, createdAt, type)
                                            VALUES ('Daily Overspending Alert','$message','alert.png',$userID,NOW(), 'daily_overspending')";
                            executeQuery($insertNotif);
                        }
                    }
                }
            }
        }
    }
}
?>
