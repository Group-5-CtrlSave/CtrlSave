<?php
include("../../connect.php");
// Daily mode (only today)
    $startDate = date('Y-m-d');


    // Get active budget rule for this user
    $getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                         FROM tbl_userbudgetversion 
                         WHERE isActive = 1;";
    $budgetVersionResult = executeQuery($getBudgetVersion);

    if (mysqli_num_rows($budgetVersionResult) > 0) {

        while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {
            $userID = $budgetVersionRow ['userID'];
            $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
            $totalIncome = $budgetVersionRow['totalIncome'];

            // Get allocation rows
            $getAllocation = "SELECT userCategoryID, necessityType, limitType, value as allocationValue
                              FROM tbl_userallocation 
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
                          $deleteNoSaving = "DELETE FROM `tbl_spendinginsights` WHERE insightType = 'daily_no_saving' AND DATE(date) = '$startDate'";
                    executeQuery($deleteNoSaving);

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
                                            ('Daily Oversaving Alert', '$message', 'Savings.svg', 
                                            $userID, NOW(), 'daily_oversaving')";
                            executeQuery($insertNotif);
                        }

                        // Insight content
                   
                        $insightDescription = "You oversaved ₱" . number_format($overAmount, 2) ." today. Your savings limit was ₱" .
                        number_format($budgetLimit, 2) . ".";

                        $checkInsight = "SELECT 1 
                        FROM tbl_spendinginsights
                        WHERE userID = $userID
                        AND insightType = 'daily_oversaving'
                        AND date = '$startDate'
                        LIMIT 1";

                        if (mysqli_num_rows(executeQuery($checkInsight)) == 0) {

                        $insertInsight = "INSERT INTO tbl_spendinginsights
                        (userID, insightType, message, date)
                        VALUES($userID, 'daily_oversaving', '$insightDescription', $overAmount, NOW()";

                        executeQuery($insertInsight);
                    }else {
                        $updateInsight = "UPDATE `tbl_spendinginsights` SET `message` = '$message' WHERE `insightType` = 'daily_oversaving' AND DATE(date) = '$startDate'";
                            executeQuery($updateInsight);
                    }
                }
                // POSITIVE SAVING
                else if ($totalSaved > 0 && $totalSaved <= $budgetLimit) {
                    $message = "Great job! You saved ₱" . number_format($totalSaved, 2) . " today, staying within your planned savings.";

                     $deleteNoSaving = "DELETE FROM `tbl_spendinginsights` WHERE insightType = 'daily_no_saving' AND DATE(date) = '$startDate'";
                    executeQuery($deleteNoSaving);
                     
                     $checkInsight = "SELECT 1 
                        FROM tbl_spendinginsights
                        WHERE userID = $userID
                        AND insightType = 'daily_positive_saving'
                        AND DATE(date) = '$startDate'
                        LIMIT 1";

                    if (mysqli_num_rows(executeQuery($checkInsight)) == 0) {
                    $insertInsight = "INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        VALUES ($userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_positive_saving', '$message', NOW())";
                    executeQuery($insertInsight);

                    $notifMessage = "Congrats! You saved ₱" . number_format($totalSaved, 2) . " today.";
                    $insertNotif = "INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        VALUES ('Daily Savings Success', '$notifMessage', 'Savings.svg', $userID, NOW(), 'daily_positive_saving')";
                    executeQuery($insertNotif);
                    }else {
                    
                        $updateInsight = "UPDATE `tbl_spendinginsights` SET `message` = '$message' WHERE `insightType` = 'daily_positive_saving' AND DATE(date) = '$startDate' AND userID = $userID";
                        executeQuery($updateInsight);
                       

                    }
                }    
                // NO SAVING
                else if ($totalSaved == 0) {
                    $message = "No savings recorded today. Try setting aside even a small amount.";
                      $deleteSaving = "DELETE FROM `tbl_spendinginsights` WHERE insightType IN ('daily_positive_saving', 'daily_oversaving') AND DATE(date) = '$startDate'";
                    executeQuery($deleteSaving);
                    $insertInsight = "
                        INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        SELECT $userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", 'daily_no_saving', '$message', NOW()
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID
                            AND insightType = 'daily_no_saving'
                            AND categoryA " . ($userCategoryID == 0 ? "IS NULL" : "= $userCategoryID") . "
                            AND DATE(date) = '$startDate'
                        )
                    ";
                    executeQuery($insertInsight);

                    $notifMessage = "You did not save anything today.";

                    $insertNotif = "
                        INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'No Savings Recorded', '$notifMessage', 'Savings.svg', $userID, NOW(), 'daily_no_saving'
                        FROM dual
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND message = '$notifMessage'
                            AND type = 'daily_no_saving'
                            AND DATE(createdAt) = '$startDate'
                        )
                    ";
                    executeQuery($insertNotif);
                }
                }
            }
        }
    }
echo "Daily oversaving insights and notifications generated successfully!";
?>