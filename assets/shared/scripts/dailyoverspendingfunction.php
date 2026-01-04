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

            // FIXED: JOIN tbl_usercategories to get userIsFlexible
            $getAllocation = "SELECT ua.userCategoryID, ua.necessityType, ua.limitType, 
                                     ua.value AS allocationValue,
                                     uc.userIsFlexible
                              FROM tbl_userallocation ua
                              LEFT JOIN tbl_usercategories uc 
                                     ON ua.userCategoryID = uc.userCategoryID
                              WHERE ua.userBudgetRuleID = $userBudgetRuleID";

            $allocationResult = executeQuery($getAllocation);

            if (mysqli_num_rows($allocationResult) > 0) {
                while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {

                    $userCategoryID = $allocationRow['userCategoryID'];
                    $necessityType = $allocationRow['necessityType'];
                    $limitType = $allocationRow['limitType'];
                    $value = $allocationRow['allocationValue'];
                    $userIsFlexible = $allocationRow['userIsFlexible']; // now correct

                    // Skip tracked-only custom categories (flexible = 0)
                    if ($userCategoryID != 0 && $userIsFlexible == 0) {
                        continue;
                    }

                    // Fetch Expenses
                    if ($userCategoryID == 0) {
                        // Default Allocation (need/want/saving)
                        $expenseQuery = "SELECT COALESCE(SUM(e.amount),0) AS totalSpent
                                         FROM tbl_expense e
                                         JOIN tbl_usercategories uc 
                                          ON uc.userCategoryID = e.userCategoryID
                                         WHERE e.userID = $userID 
                                         AND uc.userNecessityType = '$necessityType'
                                         AND DATE(e.dateSpent) = '$startDate'";
                    } else {
                        // User Allocation
                        $expenseQuery = "SELECT COALESCE(SUM(e.amount),0) AS totalSpent
                                         FROM tbl_expense e
                                         JOIN tbl_usercategories uc 
                                          ON uc.userCategoryID = e.userCategoryID
                                         WHERE e.userID = $userID
                                         AND uc.userCategoryID = $userCategoryID
                                         AND DATE(e.dateSpent) = '$startDate'";
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
                            $catQuery = "SELECT categoryName 
                                         FROM tbl_usercategories 
                                         WHERE userCategoryID = $userCategoryID LIMIT 1";
                            $catResult = executeQuery($catQuery);
                            $catRow = mysqli_fetch_assoc($catResult);
                            $categoryLabel = $catRow['categoryName'] ?? "Unknown Category";
                        }

                        // Build the message
                        $todayDate = date('Y-m-d');
                        $message = "You have overspent ₱$overAmount in $categoryLabel today ($todayDate). Your target limit was ₱$budgetLimit.";

                        // Check if daily notification exists
                        $checkNotif = "SELECT 1 FROM tbl_notifications 
                                       WHERE userID = $userID 
                                       AND message = '$message'
                                       AND DATE(createdAt) = '$startDate'
                                       LIMIT 1";

                        if (mysqli_num_rows(executeQuery($checkNotif)) == 0) {
                            // Insert daily notification
                            $insertNotif = "INSERT INTO tbl_notifications 
                                            (notificationTitle, message, icon, userID, createdAt, type)
                                            VALUES ('Daily Overspending Alert','$message','alert.png',$userID,NOW(),'daily_overspending')";
                            executeQuery($insertNotif);
                        }
                    }
                }
            }
        }
    }
}
?>
