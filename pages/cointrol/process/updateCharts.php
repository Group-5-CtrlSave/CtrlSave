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

// Total Income
$getBudgetVersion = "SELECT totalIncome 
                     FROM tbl_userbudgetversion 
                     WHERE isActive = 1 AND userID = $userID";

$budgetVersionResult = executeQuery($getBudgetVersion);

$totalIncome = 0;
if (mysqli_num_rows($budgetVersionResult) > 0) {
    $row = mysqli_fetch_assoc($budgetVersionResult);
    $totalIncome = (float)$row['totalIncome']; 
}





// ===== BAR CHART =====
$getTotalExpensesPerMonth = "SELECT MONTH(dateSpent) AS monthNum, SUM(amount) AS totalSpending 
    FROM tbl_expense 
    WHERE userID = $userID AND YEAR(dateSpent) = $currentYear
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
// ===== PIE CHART / TABLE =====
$getExpenseStructureQuery = "SELECT tbl_usercategories.categoryName AS categoryName, SUM(tbl_expense.amount) AS amount 
FROM tbl_expense JOIN tbl_usercategories ON tbl_expense.userCategoryID = tbl_usercategories.userCategoryID 
WHERE tbl_expense.userID = $userID 
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

    $analysisMessage = "Based on the current financial report, you have spent a total of "
        . number_format($overallTotal, 2) . " pesos for the month of "
        . $monthName . ".";

    // ⚠️ Debt detection: expenses greater than planned income
    if ($totalIncome > 0 && $overallTotal > $totalIncome) {
        $analysisMessage .= " You are under debt. Please review your current expenses.";
    }

    // Optional: If totalIncome in budget version is 0
    if ($totalIncome == 0) {
        $analysisMessage .= " No income set in your budget version.";
    }

} else {
    $analysisMessage = "Analyzing Data...";
}

foreach ($expenses as &$expense) {
    $expense['percentage'] = ($overallTotal > 0) ? round($expense['amount'] / $overallTotal * 100, 2) : 0;
}
unset($expense);



// ===== TOP SPENDING CATEGORIES =====
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

// ===== Get Spending Correlation Insights =====

$correlationInsight = [] ; // default
$getCorrelationInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'correlation' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$correlationInsightResult = executeQuery($getCorrelationInsightsQuery);
if (mysqli_num_rows($correlationInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($correlationInsightResult)){
            $correlationInsight[] = $row['message'];
    }

}


// ===== Get Spending Overspending Insights =====

$overspendingInsight = []; // default
$getOverspendingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'overspending' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$overspendingInsightResult = executeQuery($getOverspendingInsightsQuery);
if (mysqli_num_rows($overspendingInsightResult) > 0) {
    while ($row = mysqli_fetch_assoc($overspendingInsightResult)) {
        $overspendingInsight[] = $row['message'];
    }

}

// ===== Get Spending Oversaving Insights =====
$oversavingInsight = ""; // default
$getOversavingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'oversaving' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$oversavingInsightResult = executeQuery($getOversavingInsightsQuery);
if (mysqli_num_rows($oversavingInsightResult) > 0) {
    $row = mysqli_fetch_assoc($oversavingInsightResult);
    $oversavingInsight = $row['message'];
}

// ===== Get Spending Positive Insights =====
$positiveInsight = ""; // default
$getpositiveInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'positive' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$positiveInsightResult = executeQuery($getpositiveInsightsQuery);
if (mysqli_num_rows($positiveInsightResult) > 0) {
    $row = mysqli_fetch_assoc($positiveInsightResult);
    $positiveInsight = $row['message'];
}

// ===== Get Spending Tracking Insights =====
$trackingInsight = ""; // default
$gettrackingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'tracking' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$trackingInsightResult = executeQuery($gettrackingInsightsQuery);
if (mysqli_num_rows($trackingInsightResult) > 0) {
    $row = mysqli_fetch_assoc($trackingInsightResult);
    $trackingInsight = $row['message'];
}

// ===== Get Spending Tracking Insights =====
$recommendationInsight = ""; // default
$getrecommendationInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'recommendation' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$recommendationInsightResult = executeQuery($getrecommendationInsightsQuery);
if (mysqli_num_rows($recommendationInsightResult) > 0) {
    $row = mysqli_fetch_assoc($recommendationInsightResult);
    $recommendationInsight = $row['message'];
}

// ===== Get Spending Oversaving Insights =====
   $positiveSavingInsight= ""; // default
$getpositiveSavingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'positive_saving' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$positiveSavingInsightResult = executeQuery($getpositiveSavingInsightsQuery);
if (mysqli_num_rows($positiveSavingInsightResult) > 0) {
    $row = mysqli_fetch_assoc($positiveSavingInsightResult);
    $positiveSavingInsight = $row['message'];
}
 $noSavingInsight= ""; // default
$getnoSavingInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'no_saving' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$noSavingInsightResult = executeQuery($getnoSavingInsightsQuery);
if (mysqli_num_rows($noSavingInsightResult) > 0) {
    $row = mysqli_fetch_assoc($noSavingInsightResult);
    $noSavingInsight = $row['message'];
  
}


echo json_encode([
    "barChartData" => $monthlyData,
    "pieChartLabels" => $categoryNames,
    "pieChartData" => $categoryAmount,
    "tableData" => $expenses,
    "analysis" => $analysisMessage,
    "topCategories" => $topCategories,
    "overspendingInsight" => $overspendingInsight,
    "oversavingInsight" => $oversavingInsight,
    "positiveInsight" => $positiveInsight,
    "positiveSavingInsight" => $positiveSavingInsight,
    "noSavingInsight" => $noSavingInsight,
    "trackingInsight" => $trackingInsight,
    "recommendationInsight" => $recommendationInsight,
    "correlationInsight" => $correlationInsight
]);
?>