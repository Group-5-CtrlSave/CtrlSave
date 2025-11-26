<?php
$type = '';
$id = '';
if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = $_GET['id'];
}
?>

<?php
$getIncomeQuery .= " AND tbl_income.incomeID = $id;";
$incomeResult2 = executeQuery($getIncomeQuery);
$getExpenseQuery .= " AND tbl_expense.expenseID = $id;";
$expenseResult2 = executeQuery($getExpenseQuery);
?>
<?php

$amount = '';
$date = '';
$note = '';
$category = '';
$icon = '';
$dueDate = '';
$isRecurring = '';
$recurringID = '';
$userCategoryID = '';

switch ($type) {
    case 'income':
        if (mysqli_num_rows($incomeResult2) > 0) {
            $income = mysqli_fetch_assoc($incomeResult2);
            $userCategoryID = $income['userCategoryID'];
            $amount = $income['amount'];
            $date = $income['dateReceived'];
            $note = $income['note'];
            $category = $income['categoryName'];
            $icon = $income['icon'];
        }
        break;

    case 'expense':
        if (mysqli_num_rows($expenseResult2) > 0) {
            $expense = mysqli_fetch_assoc($expenseResult2);
            $userCategoryID = $expense['userCategoryID'];
            $amount = $expense['amount'];
            $date = $expense['dateSpent'];
            $dueDate = $expense['dueDate'];
            $isRecurring = $expense['isRecurring'];
            $note = $expense['note'];
            $recurringID = $expense['recurringID'];
            $category = $expense['categoryName'];
            $icon = $expense['icon'];

        }
        break;
}
?>


