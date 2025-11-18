<?php
//Get All Income Data 
$getIncomeQuery = "SELECT tbl_income.incomeID, tbl_income.amount, tbl_income.dateReceived, tbl_income.note, tbl_usercategories.categoryName, tbl_usercategories.icon, tbl_usercategories.type 
FROM tbl_income 
LEFT JOIN tbl_userCategories 
ON tbl_income.userCategoryID = tbl_usercategories.userCategoryID 
WHERE tbl_income.userID = 1";

$incomeResult = executeQuery($getIncomeQuery);


// Get All Expense Data
$getExpenseQuery = "SELECT tbl_expense.expenseID, tbl_expense.amount, tbl_expense.dateSpent, tbl_expense.dueDate, tbl_expense.isRecurring, tbl_usercategories.userCategoryID ,tbl_expense.note, tbl_expense.recurringID, tbl_usercategories.categoryName, tbl_usercategories.icon, tbl_usercategories.type   
FROM tbl_expense LEFT JOIN tbl_usercategories 
ON tbl_expense.userCategoryID = tbl_usercategories.userCategoryID 
WHERE tbl_expense.userID = 1";


$expenseResult = executeQuery($getExpenseQuery);
?>