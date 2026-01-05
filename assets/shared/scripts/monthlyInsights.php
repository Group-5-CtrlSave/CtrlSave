<?php
include("../connect.php");

// Monthly mode
$startDate = date('Y-m-01');
$endDate = date('Y-m-t');
$currentMonth = date('F');
$currentYear = date('Y');

// ===============================
// GET ACTIVE BUDGET VERSIONS
// ===============================
$getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
                     FROM tbl_userbudgetversion 
                     WHERE isActive = 1;";
$budgetVersionResult = executeQuery($getBudgetVersion);

if (mysqli_num_rows($budgetVersionResult) > 0) {

    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {
        $userID = $budgetVersionRow['userID'];
        $userBudgetRuleID = $budgetVersionRow['userBudgetRuleID'];
        $totalIncome = floatval($budgetVersionRow['totalIncome']);

        // ===============================
        // 1. OVERSAVING
        // ===============================
        $getAllocation = "SELECT userCategoryID, necessityType, limitType, value AS allocationValue
                          FROM tbl_userallocation
                          WHERE userBudgetRuleID = $userBudgetRuleID
                          AND necessityType = 'saving'";
        $allocationResult = executeQuery($getAllocation);

        if (mysqli_num_rows($allocationResult) > 0) {
            while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {
                $userCategoryID = $allocationRow['userCategoryID'];
                $limitType = $allocationRow['limitType'];
                $value = $allocationRow['allocationValue'];

                $budgetLimit = ($userCategoryID == 0 && $limitType == 1)
                    ? ($totalIncome * ($value / 100))
                    : $value;

                $savingsQuery = "SELECT COALESCE(SUM(gt.amount), 0) AS totalSaved
                                 FROM tbl_goaltransactions gt
                                 JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
                                 WHERE sg.userID = $userID
                                 AND gt.transaction = 'add'
                                 AND DATE(gt.date) BETWEEN '$startDate' AND '$endDate'";
                if ($userCategoryID != 0)
                    $savingsQuery .= " AND gt.savingGoalID = $userCategoryID";

                $savingsResult = executeQuery($savingsQuery);
                $totalSaved = floatval(mysqli_fetch_assoc($savingsResult)['totalSaved'] ?? 0);

                // Oversaving
                if ($totalSaved > $budgetLimit) {
                    $oversavePercent = round(($totalSaved / $budgetLimit) * 100, 1);
                    $message = "You saved ₱" . number_format($totalSaved, 2) .
                        " in $currentMonth $currentYear, exceeding your budget of ₱" . number_format($budgetLimit, 2) .
                        " (" . $oversavePercent . "%). Consider balancing savings with other needs.";
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

                } else if ($totalSaved > 0 && $totalSaved <= $budgetLimit) {
                    // Positive Saving
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

                } else {
                    // No saving
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
            }
        }

        // ===============================
        // 2. OVERSPENDING (default + custom)
        // ===============================
        // Default allocations
        $defaultAllocQuery = "SELECT necessityType, limitType, value 
                              FROM tbl_userallocation 
                              WHERE userBudgetRuleID = $userBudgetRuleID
                              AND userCategoryID = 0";
        $defaultAllocRes = executeQuery($defaultAllocQuery);

        while ($allocRow = mysqli_fetch_assoc($defaultAllocRes)) {
            $necessityType = $allocRow['necessityType'];
            $limitType = $allocRow['limitType'];
            $value = $allocRow['value'];
            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

            $catQuery = "SELECT userCategoryID, categoryName 
                         FROM tbl_usercategories 
                         WHERE userNecessityType = '$necessityType'
                         AND userisFlexible = 1";
            $catRes = executeQuery($catQuery);

            $totalSpent = 0;
            $categoryList = [];
            while ($catRow = mysqli_fetch_assoc($catRes)) {
                $userCategoryID = $catRow['userCategoryID'];
                $categoryName = $catRow['categoryName'];
                $spentRes = executeQuery("SELECT SUM(amount) AS spent 
                                          FROM tbl_expense 
                                          WHERE userID = $userID 
                                          AND userCategoryID = $userCategoryID
                                          AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'");
                $spent = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);
                if ($spent > 0) {
                    $totalSpent += $spent;
                    $categoryList[] = $categoryName;
                }
            }

            if ($totalSpent > $budgetLimit && !empty($categoryList)) {
                $overPercent = round(($totalSpent / $budgetLimit) * 100, 2);
                $message = "You have overspent {$overPercent}% in $necessityType (" . implode(", ", $categoryList) . ") from $startDate to $endDate.";
                executeQuery("INSERT INTO tbl_spendinginsights (userID, categoryA, necessityType, insightType, message, date)
                              SELECT $userID, NULL, '$necessityType', 'overspending', '$message', NOW()
                              FROM DUAL
                              WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_spendinginsights
                                WHERE userID = $userID
                                AND categoryA IS NULL
                                AND insightType = 'overspending'
                                AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                                AND message = '$message'
                              )");
                executeQuery("INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                              SELECT 'Monthly Overspending Alert','$message','alert.png',$userID,NOW(),'overspending'
                              FROM DUAL
                              WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_notifications
                                WHERE userID = $userID
                                AND type = 'overspending'
                                AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                                AND message = '$message'
                              )");
            }
        }

        // Custom allocations
        $customAllocQuery = "SELECT ua.userCategoryID, ua.necessityType, ua.limitType, ua.value, uc.userisFlexible, uc.categoryName
                             FROM tbl_userallocation ua
                             LEFT JOIN tbl_usercategories uc ON ua.userCategoryID = uc.userCategoryID
                             WHERE ua.userBudgetRuleID = $userBudgetRuleID
                             AND ua.userCategoryID != 0";
        $customAllocRes = executeQuery($customAllocQuery);
        while ($allocRow = mysqli_fetch_assoc($customAllocRes)) {
            $userCategoryID = $allocRow['userCategoryID'];
            $necessityType = $allocRow['necessityType'];
            $limitType = $allocRow['limitType'];
            $value = $allocRow['value'];
            $isFlexible = intval($allocRow['userisFlexible']);
            $categoryName = $allocRow['categoryName'] ?? "Unknown Category";

            if ($isFlexible == 0)
                continue;

            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;
            $spentRes = executeQuery("SELECT SUM(amount) AS spent 
                                      FROM tbl_expense
                                      WHERE userID = $userID
                                      AND userCategoryID = $userCategoryID
                                      AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'");
            $spent = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);

            if ($spent > $budgetLimit) {
                $overAmount = $spent - $budgetLimit;
                $message = "You have overspent ₱{$overAmount} in $categoryName from $startDate to $endDate.";
                executeQuery("INSERT INTO tbl_spendinginsights (userID, categoryA, necessityType, insightType, message, date)
                              SELECT $userID, $userCategoryID, '$necessityType', 'overspending', '$message', NOW()
                              FROM DUAL
                              WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_spendinginsights
                                WHERE userID = $userID
                                AND categoryA = $userCategoryID
                                AND insightType = 'overspending'
                                AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                                AND message = '$message'
                              )");
                executeQuery("INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                              SELECT 'Monthly Overspending Alert','$message','alert.png',$userID,NOW(),'overspending'
                              FROM DUAL
                              WHERE NOT EXISTS (
                                SELECT 1 FROM tbl_notifications
                                WHERE userID = $userID
                                AND type = 'overspending'
                                AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                                AND message = '$message'
                              )");
            }
        }

        // ===============================
        // 3. CORRELATION INSIGHTS
        // ===============================
        
        $userSpending = [];

        // Get all allocations (default + custom)
        $getAllocation = "
            SELECT 
                ua.userCategoryID,
                ua.necessityType,
                ua.limitType,
                ua.value AS allocationValue,
                uc.userisFlexible,
                uc.categoryName
            FROM tbl_userallocation ua
            LEFT JOIN tbl_usercategories uc 
                ON ua.userCategoryID = uc.userCategoryID
            WHERE ua.userBudgetRuleID = $userBudgetRuleID
        ";
        $allocationResult = executeQuery($getAllocation);

        while ($allocationRow = mysqli_fetch_assoc($allocationResult)) {
            $userCategoryID = $allocationRow['userCategoryID'];
            $necessityType = $allocationRow['necessityType'];
            $limitType = $allocationRow['limitType'];
            $value = $allocationRow['allocationValue'];
            $userisFlexible = intval($allocationRow['userisFlexible']);
            $categoryName = $allocationRow['categoryName'] ?? '';

            // Skip savings entirely
            if ($userCategoryID == 0 && $necessityType === 'saving') continue;

            // Calculate budget limit
            $budgetLimit = ($limitType == 1) ? ($totalIncome * $value / 100) : $value;

            // Fetch spent amount
            if ($userCategoryID == 0) {
                // Default allocation (need/want) – merge flexible categories
                $expenseQuery = "
                    SELECT tbl_usercategories.categoryName, COALESCE(SUM(tbl_expense.amount),0) as totalSpent
                    FROM tbl_expense 
                    JOIN tbl_usercategories 
                        ON tbl_usercategories.userCategoryID = tbl_expense.userCategoryID
                    WHERE tbl_expense.userID = $userID
                    AND tbl_usercategories.userNecessityType = '$necessityType'
                    AND tbl_usercategories.userisFlexible = 1
                    AND DATE(tbl_expense.dateSpent) BETWEEN '$startDate' AND '$endDate'
                    GROUP BY tbl_usercategories.categoryName
                ";
                $expenseResult = executeQuery($expenseQuery);

                $totalSpent = 0;
                $categoryBreakdown = [];
                while ($row = mysqli_fetch_assoc($expenseResult)) {
                    $spentAmount = floatval($row['totalSpent']);
                    $totalSpent += $spentAmount;
                    $categoryBreakdown[$row['categoryName']] = $spentAmount;
                }

                $userSpending[] = [
                    'type' => 'default',
                    'categoryID' => 0,
                    'necessityType' => $necessityType,
                    'totalSpent' => $totalSpent,
                    'budgetLimit' => $budgetLimit,
                    'categoryBreakdown' => $categoryBreakdown
                ];
            } else {
                // Custom allocation – track each category separately
                $expenseQuery = "
                    SELECT COALESCE(SUM(amount),0) AS totalSpent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = $userCategoryID
                    AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
                ";
                $row = mysqli_fetch_assoc(executeQuery($expenseQuery));
                $totalSpent = floatval($row['totalSpent']);

                $userSpending[] = [
                    'type' => 'custom',
                    'categoryID' => $userCategoryID,
                    'necessityType' => $necessityType,
                    'categoryName' => $categoryName,
                    'userisFlexible' => $userisFlexible,
                    'totalSpent' => $totalSpent,
                    'budgetLimit' => $budgetLimit
                ];
            }
        }

        // ===== Separate default and custom spending =====
        $defaultSpending = [];
        $customSpending = [];
        foreach ($userSpending as $sp) {
            if ($sp['type'] === 'default') $defaultSpending[] = $sp;
            else $customSpending[] = $sp;
        }

        // ===== Process default allocations (Need/Want) =====
        if (!empty($defaultSpending)) {
            $mostOverspentDefault = null;
            foreach ($defaultSpending as $sp) {
                $spentPercent = ($sp['budgetLimit'] > 0) ? round(($sp['totalSpent'] / $sp['budgetLimit']) * 100, 1) : 0;
                $sp['spentPercent'] = $spentPercent;
                $sp['spentPercentOfIncome'] = round(($sp['totalSpent'] / $totalIncome) * 100, 1);

                if ($spentPercent > 100) {
                    if (!$mostOverspentDefault || $spentPercent > $mostOverspentDefault['spentPercent']) {
                        $mostOverspentDefault = $sp;
                    }
                }
            }

            if ($mostOverspentDefault) {
                $breakdownParts = [];
                foreach ($mostOverspentDefault['categoryBreakdown'] as $cat => $amt) {
                    $breakdownParts[] = $cat . " (" . round(($amt / $totalIncome)*100,1) . "%)";
                }
                $breakdownText = implode(", ", $breakdownParts);

                $spentPercent = $mostOverspentDefault['spentPercent'];
                $spentPercentOfIncome = $mostOverspentDefault['spentPercentOfIncome'];
                $budgetPercentOfIncome = round(($mostOverspentDefault['budgetLimit']/$totalIncome)*100,1);

                $message = "You spent {$spentPercent}% of your allocated {$mostOverspentDefault['necessityType']} budget ({$spentPercentOfIncome}% of your total income) in $currentMonth $currentYear, which is higher than your target limit of {$budgetPercentOfIncome}% of income. This includes: $breakdownText. Because of this, you may have spent less on other categories and saved less.";

                $recommendation = "Recommendation: Consider reviewing your spending in {$mostOverspentDefault['necessityType']} to allocate and meet your budget properly.";

                $checkInsight = "SELECT 1 FROM tbl_spendinginsights 
                                 WHERE userID = $userID 
                                 AND insightType = 'correlation'
                                 AND categoryA IS NULL
                                 AND DATE_FORMAT(date,'%Y-%m') = '" . date('Y-m') . "' 
                                 LIMIT 1";
                if (mysqli_num_rows(executeQuery($checkInsight)) == 0) {
                    executeQuery("INSERT INTO tbl_spendinginsights 
                                  (userID, categoryA, insightType, message, date)
                                  VALUES ($userID, NULL, 'correlation', '$message', NOW())");

                    executeQuery("INSERT INTO tbl_spendinginsights 
                                  (userID, categoryA, insightType, message, date)
                                  VALUES ($userID, NULL, 'recommendation', '$recommendation', NOW())");
                }
            }
        }

        // ===== Process custom correlations =====
        foreach ($customSpending as $spA) {
            if ($spA['totalSpent'] <= 0) continue;

            $spentPercentA = ($spA['budgetLimit'] > 0) ? round(($spA['totalSpent'] / $spA['budgetLimit']) * 100, 1) : 0;
            $spentPercentOfIncomeA = round(($spA['totalSpent'] / $totalIncome) * 100, 1);

            if ($spentPercentA > 100) {
                $underspentCats = [];
                foreach ($customSpending as $spB) {
                    if ($spB['categoryID'] == $spA['categoryID']) continue;
                    $spentPercentB = ($spB['budgetLimit'] > 0) ? round(($spB['totalSpent'] / $spB['budgetLimit']) * 100, 1) : 0;
                    if ($spentPercentB < 100) $underspentCats[] = $spB['categoryName'];
                }

                if (!empty($underspentCats)) {
                    $underspentText = implode(", ", $underspentCats);
                    $messageCorr = "You spent {$spentPercentA}% on {$spA['categoryName']} ({$spentPercentOfIncomeA}% of your income) which exceeds your set budget of ₱" . number_format($spA['budgetLimit'],2) . ". This may have affected your spending in the following categories: $underspentText in $currentMonth $currentYear.";

                    $checkCorr = "SELECT 1 FROM tbl_spendinginsights 
                                  WHERE userID = $userID 
                                  AND insightType = 'correlation'
                                  AND categoryA = {$spA['categoryID']}
                                  AND DATE_FORMAT(date,'%Y-%m') = '" . date('Y-m') . "' 
                                  LIMIT 1";
                    if (mysqli_num_rows(executeQuery($checkCorr)) == 0) {
                        executeQuery("INSERT INTO tbl_spendinginsights 
                                      (userID, categoryA, insightType, message, date)
                                      VALUES ($userID, {$spA['categoryID']}, 'correlation', '$messageCorr', NOW())");
                    }
                }
            }
        }

        // ===== Merge overspent recommendations (only userisFlexible = 1) =====
        $flexibleOverspentCategories = [];
        foreach ($customSpending as $sp) {
            if ($sp['totalSpent'] > $sp['budgetLimit'] && $sp['userisFlexible'] == 1) {
                $flexibleOverspentCategories[] = $sp['categoryName'];
            }
        }

        if (!empty($flexibleOverspentCategories)) {
            $categoriesText = implode(", ", $flexibleOverspentCategories);
            $mergedRecommendation = "Recommendation: Consider reviewing your spending in $categoriesText.";

            executeQuery("INSERT INTO tbl_spendinginsights 
                          (userID, categoryA, insightType, message, date)
                          VALUES ($userID, NULL, 'recommendation', '$mergedRecommendation', NOW())");
        }

        // ===============================
        // 4. POSITIVE INSIGHTS
        // ===============================
        $allTrackingParts = [];

        // ============================
        // 1. DEFAULT NEED & WANT
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
            if (!$allocRow)
                continue;

            $budgetLimit = ($allocRow['limitType'] == 1)
                ? ($totalIncome * $allocRow['allocationValue'] / 100)
                : $allocRow['allocationValue'];

            // Flexible categories → positive insight
            $flexRes = executeQuery("
                SELECT userCategoryID, categoryName
                FROM tbl_usercategories
                WHERE userNecessityType = '$type'
                AND userisFlexible = 1
            ");
            $totalFlexibleSpent = 0;
            $categoryList = [];

            while ($catRow = mysqli_fetch_assoc($flexRes)) {
                $spentRes = executeQuery("
                    SELECT COALESCE(SUM(amount),0) AS spent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = {$catRow['userCategoryID']}
                    AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
                ");
                $spent = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);
                if ($spent > 0) {
                    $totalFlexibleSpent += $spent;
                    $categoryList[] = $catRow['categoryName'];
                }
            }

            if ($totalFlexibleSpent > 0 && $totalFlexibleSpent <= $budgetLimit && !empty($categoryList)) {
                $percent = ($budgetLimit > 0) ? round(($totalFlexibleSpent / $budgetLimit) * 100, 2) : 0;
                $message = "Great job! You spent wisely: {$percent}% on $type (" . implode(', ', $categoryList) . ") this month.";

                // INSERT INTO tbl_spendinginsights with duplicate check
                executeQuery("
                    INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                    SELECT $userID, 0, 'positive', '$message', NOW()
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_spendinginsights
                        WHERE userID = $userID 
                        AND categoryA = 0
                        AND insightType = 'positive'
                        AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ");

                // INSERT INTO tbl_notifications with duplicate check
                executeQuery("
                    INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                    SELECT 'Positive Insight', '$message', 'insight.png', $userID, NOW(), 'positive_insight'
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM tbl_notifications
                        WHERE userID = $userID
                        AND type = 'positive_insight'
                        AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                        AND message = '$message'
                    )
                ");
            }

            // Rigid categories → tracking only
            $rigidRes = executeQuery("
                SELECT userCategoryID, categoryName
                FROM tbl_usercategories
                WHERE userNecessityType = '$type'
                AND userisFlexible = 0
            ");
            while ($rigidRow = mysqli_fetch_assoc($rigidRes)) {
                $spentRes = executeQuery("
                    SELECT COALESCE(SUM(amount),0) AS spent
                    FROM tbl_expense
                    WHERE userID = $userID
                    AND userCategoryID = {$rigidRow['userCategoryID']}
                    AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
                ");
                $spent = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);

                if ($spent <= 0)
                    continue;

                $percent = ($budgetLimit > 0) ? round(($spent / $budgetLimit) * 100, 2) : 0;
                $allTrackingParts[] = "You spent {$percent}% on {$rigidRow['categoryName']} (Track Only)";
            }
        }

        // ============================
        // 2. CUSTOM ALLOCATIONS
        // ============================
        $customAllocRes = executeQuery("
            SELECT userCategoryID, limitType, value AS allocationValue
            FROM tbl_userallocation
            WHERE userBudgetRuleID = $userBudgetRuleID
            AND userCategoryID != 0
        ");

        while ($allocRow = mysqli_fetch_assoc($customAllocRes)) {
            $userCategoryID = $allocRow['userCategoryID'];

            $catRes = executeQuery("
                SELECT categoryName, userisFlexible
                FROM tbl_usercategories
                WHERE userCategoryID = $userCategoryID
            ");
            $catRow = mysqli_fetch_assoc($catRes);

            $categoryName = $catRow['categoryName'];
            $userisFlexible = intval($catRow['userisFlexible']);

            $spentRes = executeQuery("
                SELECT COALESCE(SUM(amount),0) AS spent
                FROM tbl_expense
                WHERE userID = $userID
                AND userCategoryID = $userCategoryID
                AND DATE(dateSpent) BETWEEN '$startDate' AND '$endDate'
            ");
            $actualAmount = floatval(mysqli_fetch_assoc($spentRes)['spent'] ?? 0);
            if ($actualAmount <= 0)
                continue;

            if ($userisFlexible == 1) {
                $budgetLimit = ($allocRow['limitType'] == 1)
                    ? ($totalIncome * $allocRow['allocationValue'] / 100)
                    : $allocRow['allocationValue'];

                if ($actualAmount <= $budgetLimit && $budgetLimit > 0) {
                    $percent = round(($actualAmount / $budgetLimit) * 100, 2);
                    $message = "Great job! You spent wisely: {$percent}% on {$categoryName} this month.";

                    // INSERT INTO tbl_spendinginsights with duplicate check
                    executeQuery("
                        INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                        SELECT $userID, $userCategoryID, 'positive', '$message', NOW()
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_spendinginsights
                            WHERE userID = $userID 
                            AND categoryA = $userCategoryID
                            AND insightType = 'positive'
                            AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                            AND message = '$message'
                        )
                    ");

                    // INSERT INTO tbl_notifications with duplicate check
                    executeQuery("
                        INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                        SELECT 'Positive Insight', '$message', 'insight.png', $userID, NOW(), 'positive_insight'
                        FROM DUAL
                        WHERE NOT EXISTS (
                            SELECT 1 FROM tbl_notifications
                            WHERE userID = $userID
                            AND type = 'positive_insight'
                            AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                            AND message = '$message'
                        )
                    ");
                }
            } else {
                $allTrackingParts[] = "You spent ₱" . number_format($actualAmount, 2) . " on {$categoryName} (Track Only)";
            }
        }

        // ============================
        // 3. INSERT TRACKING INSIGHT
        // ============================
        if (!empty($allTrackingParts)) {
            $insightMessage = "Tracking Categories Only: " . implode(", ", $allTrackingParts) . " this month.";
            $notifMessage = implode(", ", $allTrackingParts);

            // INSERT INTO tbl_spendinginsights with duplicate check
            executeQuery("
                INSERT INTO tbl_spendinginsights (userID, categoryA, insightType, message, date)
                SELECT $userID, 0, 'tracking', '$insightMessage', NOW()
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_spendinginsights
                    WHERE userID = $userID
                    AND categoryA = 0
                    AND insightType = 'tracking'
                    AND DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                    AND message = '$insightMessage'
                )
            ");

            // INSERT INTO tbl_notifications with duplicate check
            executeQuery("
                INSERT INTO tbl_notifications (notificationTitle, message, icon, userID, createdAt, type)
                SELECT 'Tracking Update', '$notifMessage', 'insight.png', $userID, NOW(), 'positive_insight'
                FROM DUAL
                WHERE NOT EXISTS (
                    SELECT 1 FROM tbl_notifications
                    WHERE userID = $userID
                    AND type = 'positive_insight'
                    AND DATE_FORMAT(createdAt, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
                    AND message = '$notifMessage'
                )
            ");
        }
    } // End of budgetVersion loop
}

echo "All monthly insights (savings, spending, positive, correlation) generated successfully!";
?>