<?php
// userID
$userID = '';
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
}

// Current month & year
$currentMonth = date('m');
$currentYear = date('Y');


// ===== BAR CHART =====
$getTotalExpensesPerMonth = "
    SELECT MONTH(dateSpent) AS monthNum, SUM(amount) AS totalSpending 
    FROM tbl_expense 
    WHERE userID = $userID AND YEAR(dateSpent) = $currentYear 
    GROUP BY MONTH(dateSpent);
";

$totalExpenseResult = executeQuery($getTotalExpensesPerMonth);

$monthlyData = array_fill(0, 12, 0);

if (mysqli_num_rows($totalExpenseResult) > 0) {
    while ($totalExpenseRow = mysqli_fetch_assoc($totalExpenseResult)) {

        
        $monthNum = (int)$totalExpenseRow['monthNum'];
        $monthlyData[$monthNum - 1] = (float)$totalExpenseRow['totalSpending'];
    }
}

$monthlyDataJSON = json_encode(array_values($monthlyData));


// ===== PIE CHART / TABLE =====
$getExpenseStructureQuery = "
    SELECT c.categoryName AS categoryName, SUM(e.amount) AS amount
    FROM tbl_expense e
    JOIN tbl_usercategories c ON e.userCategoryID = c.userCategoryID
    WHERE e.userID = $userID 
    AND YEAR(e.dateSpent) = $currentYear 
    AND MONTH(e.dateSpent) = $currentMonth
    GROUP BY c.categoryName;
";

$expenseStructureResult = executeQuery($getExpenseStructureQuery);

$expenseList = [];
$categories = [];
$data = [];
$overallTotal = 0;

if (mysqli_num_rows($expenseStructureResult) > 0) {
    while ($row = mysqli_fetch_assoc($expenseStructureResult)) {

        $categories[] = $row["categoryName"];
        $data[] = (float)$row["amount"];

        $expenseList[] = $row;
        $overallTotal += $row["amount"];
    }
}

$categoriesJSON = json_encode($categories);
$dataJSON = json_encode($data);  
?>
