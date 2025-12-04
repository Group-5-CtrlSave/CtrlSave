<!-- Include DB connection -->
<?php
include("../connect.php");


?>
<!-- Set Range -->
<?php

$mode = 'monthly';

if ($mode === 'monthly') {
    $startDate = date('Y-m-01');
    $endDate = date('Y-m-t');
} else {
    $startDate = date('Y-m-d', strtotime('last monday'));
    $endDate = date('Y-m-d', strtotime('next sunday'));
}

?>

<?php

// Get Active Budget Version

$getBudgetVersion = "SELECT userID, userBudgetRuleID, totalIncome 
FROM tbl_userbudgetversion 
WHERE isActive = 1;";

$budgetVersionResult = executeQuery($getBudgetVersion);


if (mysqli_num_rows($budgetVersionResult) > 0) {
    while ($budgetVersionRow = mysqli_fetch_assoc($budgetVersionResult)) {

        $userID = $budgetVersionRow['userID'];
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


                //  Fetch Expenses

                if ($userCategoryID == 0) {
                    // For Default Allocation
                    $expenseQuery = "SELECT SUM(tbl_expense.amount) as totalSpent 
                    FROM tbl_expense 
                    JOIN tbl_usercategories ON tbl_usercategories.userCategoryID = tbl_expense.userCategoryID
                    WHERE tbl_expense.userID = $userID 
                    AND tbl_usercategories.userNecessityType = '$necessityType'
                    AND tbl_expense.dateSpent BETWEEN '$startDate' AND '$endDate'";
                } else {
                    // For User Allocation
                    $expenseQuery = "SELECT SUM(amount) AS totalSpent 
                    FROM tbl_expense JOIN tbl_usercategories 
                    ON tbl_usercategories.userCategoryID = tbl_expense.userCategoryID 
                    WHERE tbl_expense.userID = $userID
                    AND tbl_usercategories.userNecessityType = '$necessityType'
                    AND tbl_expense.dateSpent BETWEEN '$startDate' AND '$endDate'";

                }

                $expenseResult = executeQuery($expenseQuery);
                $expenseRow = mysqli_fetch_assoc($expenseResult);
                $totalSpent = floatval($expenseRow['totalSpent'] ?? 0);

                // Set the Limit
                if ($limitType == 1) {
                    $budgetLimit = ($totalIncome * $value) / 100;
                }
                if ($limitType == 2) {
                    $budgetLimit = $value;
                }

                if ($totalSpent > $budgetLimit) {
                    $overAmount = $totalSpent - $budgetLimit;

                    // Get the Category Label
                    if ($userCategoryID == 0) {
                        $categoryLabel = $necessityType;
                        $categoryCondition = "categoryA IS NULL AND necessityType = '$necessityType'";
                    } else {
                        $catQuery = "SELECT categoryName FROM tbl_usercategories WHERE userCategoryID = $userCategoryID LIMIT 1";
                        $catResult = executeQuery($catQuery);
                        $catRow = mysqli_fetch_assoc($catResult);
                        $categoryLabel = $catRow['categoryName'] ?? "Unknown Category";
                        $categoryCondition = "categoryA = $userCategoryID";
                    }

                    // Build The Message
                    $message = "You have overspent ₱$overAmount in $categoryLabel from $startDate to $endDate.";

                    // Determine necessityType value for the insight
                    $insightNecessity = ($userCategoryID == 0) ? "'$necessityType'" : "NULL";

                    // Check if the insight already exists
                    $checkInsight = "SELECT 1 FROM tbl_spendinginsights 
                    WHERE userID = $userID 
                    AND insightType = 'overspending'
                    AND " . ($userCategoryID == 0
                        ? "necessityType = '$necessityType'"
                        : "categoryA = $userCategoryID") . "
                    AND DATE(date) BETWEEN '$startDate' AND '$endDate'
                    LIMIT 1";

                    if (mysqli_num_rows(executeQuery($checkInsight)) == 0) {
                        // Insert insight
                        $insertInsight = "INSERT INTO tbl_spendinginsights 
                      (userID, categoryA, necessityType, insightType, message, date)
                      VALUES ($userID, " . ($userCategoryID == 0 ? "NULL" : $userCategoryID) . ", $insightNecessity, 'overspending', '$message', NOW())";
                        executeQuery($insertInsight);
                    }

                    // Check if notification already exists
                    $checkNotif = "SELECT 1 FROM tbl_notifications 
                                   WHERE userID = $userID 
                                   AND message = '$message'
                                   AND DATE(createdAt) BETWEEN '$startDate' AND '$endDate'
                                   LIMIT 1";
                    if (mysqli_num_rows(executeQuery($checkNotif)) == 0) {
                        // Insert notification
                        $insertNotif = "INSERT INTO tbl_notifications 
                                        (notificationTitle, message, icon, userID, createdAt, type)
                                        VALUES ('Monthly Overspending Alert','$message','alert.png',$userID,NOW(), 'overspending')";
                        executeQuery($insertNotif);
                    }


                }





            }
        }


    }
    echo "Overspending insights generated successfully!";
}



?>