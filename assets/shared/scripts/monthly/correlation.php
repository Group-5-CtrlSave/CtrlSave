<?php
include("../../connect.php");

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
            FROM tbl_userAllocation ua
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
    }
}

echo "Monthly correlation insights with merged recommendations (flexible only) generated successfully!";
?>
