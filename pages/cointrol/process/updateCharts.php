<?php include("../../../assets/shared/connect.php") ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// userID
$userID = '';
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
}




// Current month & year
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date("m");
$currentYear = isset($_GET['year']) ? $_GET['year'] : date("Y");

// Get Total Income
$getBudgetVersion = "SELECT totalIncome 
                     FROM tbl_userbudgetversion 
                     WHERE isActive = 1 AND userID = $userID";

$budgetVersionResult = executeQuery($getBudgetVersion);

$totalIncome = 0;
if (mysqli_num_rows($budgetVersionResult) > 0) {
    $row = mysqli_fetch_assoc($budgetVersionResult);
    $totalIncome = (float) $row['totalIncome'];
}






// ===== BAR CHART =====
$getTotalExpensesPerMonth = "SELECT MONTH(dateSpent) AS monthNum, SUM(amount) AS totalSpending 
    FROM tbl_expense 
    WHERE userID = $userID AND isDeleted = 0 AND YEAR(dateSpent) = $currentYear
    GROUP BY MONTH(dateSpent);";

$totalExpenseResult = executeQuery($getTotalExpensesPerMonth);

$monthlyData = array_fill(0, 12, 0);

if (mysqli_num_rows($totalExpenseResult) > 0) {
    while ($totalExpenseRow = mysqli_fetch_assoc($totalExpenseResult)) {
        $monthNumber = (int) ($totalExpenseRow['monthNum']);
        $monthlyData[$monthNumber - 1] = (float) $totalExpenseRow['totalSpending'];

    }
}


$expenses = $expenses ?? [];
$categoryNames = $categoryNames ?? [];
$categoryAmount = $categoryAmount ?? [];
$overallTotal = $overallTotal ?? 0;


// ===== FORECAST =====

$count = isset($_GET['count']) ? (int)$_GET['count'] : 0;

// Decide forecast type
$forecastType = '';
if ($count === 1) $forecastType = 'expense_1m';
elseif ($count === 2) $forecastType = 'expense_3m';
elseif ($count === 3) $forecastType = 'expense_6m';

$getForecastQuery = "SELECT forecastMonth, forecastYear, SUM(predictedAmount) AS totalForecast
                     FROM tbl_forecasts
                     WHERE userID = $userID
                     AND forecastType = '$forecastType' -- change as needed for 1m, 3m, etc.
                     AND forecastYear >= $currentYear
                     GROUP BY forecastYear, forecastMonth";

$forecastResult = executeQuery($getForecastQuery);

$forecastData = array_fill(0, 12, 0);

if (mysqli_num_rows($forecastResult) > 0) {
    while ($row = mysqli_fetch_assoc($forecastResult)) {
        $monthNum = (int) $row['forecastMonth'];
        $forecastData[$monthNum - 1] = (float) $row['totalForecast'];
    }
}



// ===== PIE CHART / TABLE =====
$getExpenseStructureQuery = "SELECT tbl_usercategories.categoryName AS categoryName, SUM(tbl_expense.amount) AS amount 
FROM tbl_expense JOIN tbl_usercategories ON tbl_expense.userCategoryID = tbl_usercategories.userCategoryID 
WHERE tbl_expense.userID = $userID 
AND isDeleted = 0
AND YEAR(tbl_expense.dateSpent) = $currentYear 
AND MONTH(tbl_expense.dateSpent) = $currentMonth
GROUP BY tbl_usercategories.categoryName;
";

$expenseStructureResult = executeQuery($getExpenseStructureQuery);

if (mysqli_num_rows($expenseStructureResult) > 0) {
    while ($expenseRow = mysqli_fetch_assoc($expenseStructureResult)) {
        $categoryNames[] = $expenseRow['categoryName'];
        $categoryAmount[] = (float) $expenseRow['amount'];
        $overallTotal += (float) $expenseRow['amount'];
        $expenses[] = $expenseRow;
    }
}


// ===== ANALYSIS MESSAGE =====
if ($overallTotal) {

    $monthName = date("F", mktime(0, 0, 0, $currentMonth, 1));

    $totalSpentMessage = "Based on the current financial report, you have spent a total of ₱"
        . number_format($overallTotal, 2) . " pesos for the month of "
        . $monthName . ".";

    // ⚠️ Debt detection: expenses greater than planned income
    if ($totalIncome > 0 && $overallTotal > $totalIncome) {
        $totalSpentMessage .= " You are under debt. Please review your current expenses.";
    }

    // Optional: If totalIncome in budget version is 0
    if ($totalIncome == 0) {
        $totalSpentMessage .= " No income set in your budget version.";
    }

} else {
    if ($count > 0){
        $totalSpentMessage = "";
    }else{
        $totalSpentMessage = "Analyzing Data...";

    }
    
}
$unallocatedMessage = '';
if ($totalIncome > $overallTotal && $overallTotal != 0){
    $unallocatedBudget = $totalIncome - $overallTotal;
    $messages = [
    "You still have ₱" . number_format($unallocatedBudget, 2) . " left in your budget. Why not give your savings a little boost?",
    "Heads up! ₱" . number_format($unallocatedBudget, 2) . " is waiting to be assigned. Your future self will thank you!",
    "Good news! You’ve got ₱" . number_format($unallocatedBudget, 2) . " unallocated. Time to flex that budget power!",
    "Whoa! ₱" . number_format($unallocatedBudget, 2) . " is still free. Maybe it’s the perfect moment for a small goal or treat?",
    "Budget check: ₱" . number_format($unallocatedBudget, 2) . " left. Don’t let it snooze—put it to work!",
    "Nice! ₱" . number_format($unallocatedBudget, 2) . " is still floating around. Savings or fun? The choice is yours!",
    "Look at that! ₱" . number_format($unallocatedBudget, 2) . " unallocated. Give it a job—your wallet deserves it.",
    "Extra cash alert: ₱" . number_format($unallocatedBudget, 2) . ". Allocate it wisely, or at least wisely-ish!",
    "You’ve got ₱" . number_format($unallocatedBudget, 2) . " leftover. A little action now makes a big difference later!",
    "Whoa there! ₱" . number_format($unallocatedBudget, 2) . " is still unassigned. Time to boss your budget around!"
];
// Pick random message
 $unallocatedMessage = $messages[array_rand($messages)];
    
    
}

foreach ($expenses as &$expense) {
    $expense['percentage'] = ($overallTotal > 0) ? round($expense['amount'] / $overallTotal * 100, 2) : 0;
}
unset($expense);



// ===== TOP 3 SPENDING CATEGORIES =====
$topCategories = [];
if (!empty($expenses)) {
    // Sort expenses by amount descending
    usort($expenses, function ($a, $b) {
        return $b['amount'] <=> $a['amount'];
    });

    // Take top 3 categories
    $top = array_slice($expenses, 0, 3);

    foreach ($top as $index => $cat) {
        $topCategories[] = ($index + 1) . ". " . $cat['categoryName'] . " – " . round($cat['percentage'], 2) . "%";
    }
}


$today = date('Y-m-d');

// No overspending message
$dailyNoOverSpendingMessage = []; // daily
$dailyNoOverspendingMessageQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_nooverspending_message' AND userID = $userID AND DATE(date) = '$today'";
$dailyNoOverspendingMessageResult = executeQuery($dailyNoOverspendingMessageQuery);
if (mysqli_num_rows($dailyNoOverspendingMessageResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyNoOverspendingMessageResult)) {
        $dailyNoOverSpendingMessage[] = $row['message'];
    }
}


// No overspending Insight
$dailyNoOverspending = []; // daily
$dailyNoOverspendingQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_nooverspending' AND userID = $userID AND DATE(date) = '$today'";
$dailyNoOverspendingResult = executeQuery($dailyNoOverspendingQuery);
if (mysqli_num_rows($dailyNoOverspendingResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyNoOverspendingResult)) {
        $dailyNoOverspending[] = $row['message'];
    }
}


// Overspending Message
$dailyOverspendingmessage = []; // daily
$dailyOverspendingmessageQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType NOT IN ('daily_overspending', 'daily_nooverspending', 'daily_nooverspending_message', 'correlation', 'recommendation', 'daily_positive_saving', 'daily_no_saving', 'forecast_increase', 'forecast_decrease', 'forecast_stable') AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$dailyOverspendingmessageResult = executeQuery($dailyOverspendingmessageQuery);
if (mysqli_num_rows($dailyOverspendingmessageResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyOverspendingmessageResult)) {
        $dailyOverspendingmessage[] = $row['message'];
    }
}

// Overspending Insight
$dailyOverspending = []; // daily
$dailyOverspendingQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_overspending' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$dailyOverspendingResult = executeQuery($dailyOverspendingQuery);
if (mysqli_num_rows($dailyOverspendingResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyOverspendingResult)) {
        $dailyOverspending[] = $row['message'];
    }
}

// Forecasting 
$forecastingDecrease = []; // daily
$forecastingDecreaseQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'forecast_decrease' AND userID = $userID AND DATE(date) = '$today'";
$forecastingDecreaseResult = executeQuery($forecastingDecreaseQuery);
if (mysqli_num_rows($forecastingDecreaseResult) > 0) {
    while ($row = mysqli_fetch_assoc($forecastingDecreaseResult)) {
        $forecastingDecrease[] = $row['message'];
    }
}

$forecastingIncrease = [];
// Increase
$forecastingIncreaseQuery = "SELECT message 
                             FROM tbl_spendinginsights 
                             WHERE insightType = 'forecast_increase' 
                               AND userID = $userID 
                               AND DATE(date) = '$today'";
$forecastingIncreaseResult = executeQuery($forecastingIncreaseQuery);
if (mysqli_num_rows($forecastingIncreaseResult) > 0) {
    while ($row = mysqli_fetch_assoc($forecastingIncreaseResult)) {
        $forecastingIncrease[] = $row['message'];
    }
}
$forecastingStable = [];
// Stable
$forecastingStableQuery = "SELECT message 
                           FROM tbl_spendinginsights 
                           WHERE insightType = 'forecast_stable' 
                             AND userID = $userID 
                             AND DATE(date) = '$today'";
$forecastingStableResult = executeQuery($forecastingStableQuery);
if (mysqli_num_rows($forecastingStableResult) > 0) {
    while ($row = mysqli_fetch_assoc($forecastingStableResult)) {
        $forecastingStable[] = $row['message'];
    }
}






// ===== Get Spending Correlation Insights =====

$correlationInsight = []; // default
$getCorrelationInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'correlation' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$correlationInsightResult = executeQuery($getCorrelationInsightsQuery);
if (mysqli_num_rows($correlationInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($correlationInsightResult)) {
        $correlationInsight[] = $row['message'];
    }

}



// ===== Get Spending Overspending Insights =====
$overspendingInsight = []; // monthly
$getOverspendingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'overspending' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$overspendingInsightResult = executeQuery($getOverspendingInsightsQuery);
if (mysqli_num_rows($overspendingInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($overspendingInsightResult)) {
        $overspendingInsight[] = $row['message'];
    }
}

// ===== Get Recommendation Insights (Monthly) =====
$recommendationInsight = []; // monthly
$getRecommendationInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'recommendation' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$recommendationInsightResult = executeQuery($getRecommendationInsightsQuery);
if (mysqli_num_rows($recommendationInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($recommendationInsightResult)) {
        $recommendationInsight[] = $row['message'];
    }
}



// ===== Get Spending Oversaving Insights =====
$oversavingInsight = []; // monthly
$getOversavingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'oversaving' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$oversavingInsightResult = executeQuery($getOversavingInsightsQuery);
if (mysqli_num_rows($oversavingInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($oversavingInsightResult)) {
        $oversavingInsight[] = $row['message'];
    }
}

// ===== Get Spending Daily Oversaving Insights =====
$dailyOversaving = []; // daily
$dailyOversavingQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_oversaving' AND userID = $userID AND DATE(date) = '$today'";
$dailyOversavingResult = executeQuery($dailyOversavingQuery);
if (mysqli_num_rows($dailyOversavingResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyOversavingResult)) {
        $dailyOversaving[] = $row['message'];
    }
}

// ===== Get Spending Positive Insights =====
$positiveInsight = []; // monthly
$getpositiveInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'positive' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$positiveInsightResult = executeQuery($getpositiveInsightsQuery);
if (mysqli_num_rows($positiveInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($positiveInsightResult)) {
        $positiveInsight[] = $row['message'];
    }
}

// ===== Get Spending Daily Positive Insights =====
$dailyPositive = []; // daily
$dailyPositiveQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_positive' AND userID = $userID AND DATE(date) = '$today'";
$dailyPositiveResult = executeQuery($dailyPositiveQuery);
if (mysqli_num_rows($dailyPositiveResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyPositiveResult)) {
        $dailyPositive[] = $row['message'];
    }
}

// ===== Get Spending Tracking Insights =====
$trackingInsight = []; // monthly
$gettrackingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'tracking' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$trackingInsightResult = executeQuery($gettrackingInsightsQuery);
if (mysqli_num_rows($trackingInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($trackingInsightResult)) {
        $trackingInsight[] = $row['message'];
    }
}

// ===== Get Spending Daily Tracking Insights =====
$dailyTracking = []; // daily
$dailyTrackingQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_tracking' AND userID = $userID AND DATE(date) = '$today'";
$dailyTrackingResult = executeQuery($dailyTrackingQuery);
if (mysqli_num_rows($dailyTrackingResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyTrackingResult)) {
        $dailyTracking[] = $row['message'];
    }
}
// Positive Saving Insight
$positiveSavingInsight = [];
$getpositiveInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'positive_saving' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$positiveInsightResult = executeQuery($getpositiveInsightsQuery);
if (mysqli_num_rows($positiveInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($positiveInsightResult)) {
        $positiveInsight[] = $row['message'];
    }
}

// ===== Get Daily Positive Saving Insights =====
$dailyPositiveSaving = []; // daily
$dailyPositiveSavingQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_positive_saving' AND userID = $userID AND DATE(date) = '$today'";
$dailyPositiveSavingResult = executeQuery($dailyPositiveSavingQuery);
if (mysqli_num_rows($dailyPositiveSavingResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyPositiveSavingResult)) {
        $dailyPositiveSaving[] = $row['message'];
    }
}
// ===== Get Spending No Saving Insights (Monthly) =====
$noSavingInsight = []; // monthly
$getNoSavingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'no_saving' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$noSavingInsightResult = executeQuery($getNoSavingInsightsQuery);
if (mysqli_num_rows($noSavingInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($noSavingInsightResult)) {
        $noSavingInsight[] = $row['message'];
    }
}

// ===== Get Daily No Saving Insights =====
$dailyNoSaving = []; // daily
$dailyNoSavingQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'daily_no_saving' AND userID = $userID AND DATE(date) = '$today'";
$dailyNoSavingResult = executeQuery($dailyNoSavingQuery);
if (mysqli_num_rows($dailyNoSavingResult) > 0) {
    while ($row = mysqli_fetch_assoc($dailyNoSavingResult)) {
        $dailyNoSaving[] = $row['message'];
    }
}



echo json_encode([
    "barChartData" => $monthlyData,
    "forecastBarData" => $forecastData,
    "pieChartLabels" => $categoryNames,
    "pieChartData" => $categoryAmount,
    "tableData" => $expenses,
    "totalSpent" => $totalSpentMessage,
    "unallocatedBudget" =>  $unallocatedMessage,
    "topCategories" => $topCategories,
    // Overspending
    "noOverSpending" => $dailyNoOverspending,
    "noOverSpendingMessage" => $dailyNoOverSpendingMessage,
    "dailyOverspending" => $dailyOverspending,
    "dailyOverspendingmessage" => $dailyOverspendingmessage,

    // Forecasting 
    "forecastingDecrease" => $forecastingDecrease,
    "forecastingIncrease" => $forecastingIncrease,
    "forecastingStable" => $forecastingStable,
    //  Correlation
    "recommendationInsight" => $recommendationInsight,
    "correlationInsight" => $correlationInsight,
    
    
    // Insights
    "overspendingInsight" => $overspendingInsight,
    "oversavingInsight" => $oversavingInsight,
    "positiveInsight" => $positiveInsight,
    "positiveSavingInsight" => $positiveSavingInsight,
    "noSavingInsight" => $noSavingInsight,
    "trackingInsight" => $trackingInsight,
    
    // Daily insights
    
    "dailyOversaving" => $dailyOversaving,
    "dailyPositive" => $dailyPositive,
    "dailyTracking" => $dailyTracking,
    // Daily saving insights
    "dailyPositiveSaving" => $dailyPositiveSaving,
    "dailyNoSaving" => $dailyNoSaving
]);
?>