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
if ($overallTotal) {
    $monthName = date("F", mktime(0, 0, 0, $currentMonth, 1));
    $analysisMessage = "Based on the current financial report, you have spent a total of "
        . number_format($overallTotal, 2) . " pesos for the month of "
        . $monthName . ".";

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

$correlationInsight = ""; // default
$getCorrelationInsightsQuery = "SELECT message FROM tbl_spendinginsights WHERE insightType = 'correlation' AND userID = $userID AND YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
$correlationInsightResult = executeQuery($getCorrelationInsightsQuery);
if (mysqli_num_rows($correlationInsightResult) > 0) {
    $row = mysqli_fetch_assoc($correlationInsightResult);
    $correlationInsight = $row['message'];
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


echo json_encode([
    "barChartData" => $monthlyData,
    "pieChartLabels" => $categoryNames,
    "pieChartData" => $categoryAmount,
    "tableData" => $expenses,
    "analysis" => $analysisMessage,
    "topCategories" => $topCategories,
    "overspendingInsight" => $overspendingInsight,
    "oversavingInsight" => $oversavingInsight,
    "correlationInsight" => $correlationInsight
]);
?>