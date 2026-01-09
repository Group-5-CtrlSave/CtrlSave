<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php include("../../assets/shared/connect.php"); ?>
<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}

$type = '';
if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = 'all';
}
?>

<?php
$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$currencySymbol = ($currencyCode === 'USD') ? '$' : '₱';
?>

<?php include("process/incomeandexpenseprocess.php"); ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CtrlSave | Income and Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/income&expenses.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body>

    <?php include("../../assets/shared/navigationBar.php") ?>

    <?php include("../../assets/shared/sideBar.php") ?>

    <?php include("../../assets/shared/plusButton.php") ?>





    <!-- Content -->

    <div class="container-fluid mainContainer">

        <div class="container-fluid p-3">
            <h2 class="title m-0">Income and Expenses</h2>
        </div>
        <div class="container-fluid d-flex align-items-center justify-content-center p-2">
            <button type="button"
                class="btn custom-btn sortButton allButton <?php echo ($type == 'all') ? 'selected' : '' ?>"
                onclick="window.location.href='income_expenses.php?type=all';"><b>All</b></button>
            <button type="button"
                class="btn custom-btn sortButton incomeButton <?php echo ($type == 'income') ? 'selected' : '' ?>"
                onclick="window.location.href='income_expenses.php?type=income';"><b>Income</b></button>
            <button type="button"
                class="btn custom-btn sortButton expenseButton <?php echo ($type == 'expense') ? 'selected' : '' ?>"
                onclick="window.location.href='income_expenses.php?type=expense';"><b>Expenses</b></button>
        </div>


        <!-- Modal -->

        <div class="container-fluid">

            <div class="modal fade" id="plusModal" tabindex="-1" aria-labelledby="addIncomeExpenseModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addIncomeExpenseModalLabel">Add Income or Expense</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body d-flex justify-content-center align-items-center">
                            <a href="addIncome.php"><button type="button"
                                    class="btn custom-btn btn-lg addIncomebtn mx-3"><b>Add Income</b></button></a>
                            <a href="addExpenses.php"><button type="button"
                                    class="btn custom-btn btn-lg addExpensebtn mx-3"><b>Add Expense</b></button></a>
                        </div>

                    </div>
                </div>
            </div>


        </div>

        <!-- Income and Expense Row -->
        <div class="scrollable-container">
            <?php include("process/successtag.php") ?>


            <!-- Sorting Logic -->
            <?php

            $mergedResults = [];
            $hasIncome = false;
            $hasExpense = false;

            if (mysqli_num_rows($incomeResult) > 0) {
                $hasIncome = true;
                while ($income = mysqli_fetch_assoc($incomeResult)) {
                    $mergedResults[] = $income;

                }
            } else {
                $hasIncome = false;
            }

            if (mysqli_num_rows($expenseResult) > 0) {
                $hasExpense = true;
                while ($expense = mysqli_fetch_assoc($expenseResult)) {
                    $mergedResults[] = $expense;
                }
            } else {
                $hasExpense = false;
            }

            usort($mergedResults, function ($a, $b) {
                $hasDueDateA = isset($a['dueDate']);
                $hasDueDateB = isset($b['dueDate']);

                // If both have dueDate, sort by soonest due date first (ascending)
                if ($hasDueDateA && $hasDueDateB) {
                    return strtotime($a['dueDate']) <=> strtotime($b['dueDate']);
                }

                // If only A has dueDate, it comes first
                if ($hasDueDateA && !$hasDueDateB) {
                    return -1;
                }

                // If only B has dueDate, it comes first
                if (!$hasDueDateA && $hasDueDateB) {
                    return 1;
                }

                // Neither has dueDate: then sort by dateReceived or dateSpent, newest first (descending)
                $dateA = $a['dateReceived'] ?? $a['dateSpent'] ?? null;
                $dateB = $b['dateReceived'] ?? $b['dateSpent'] ?? null;

                $timeA = $dateA ? strtotime($dateA) : 0;
                $timeB = $dateB ? strtotime($dateB) : 0;

                return $timeB <=> $timeA; // newest first
            });

            ?>



            <?php

            if (!empty($mergedResults)) {
                foreach ($mergedResults as $entry) {
                    if ($type == 'all' || $entry['type'] == $type) {
                        ?>
                        <a style="text-decoration: none; color: black;"
                            href="viewIncomeExpense.php?type=<?php echo $entry['type'] ?>&id=<?php echo $entry[$entry['type'] . '' . 'ID'] ?>">

                            <div class="container ieContainer my-3">
                                <div class="row ieRow">
                                    <div class="col-4  d-flex justify-content-center align-items-center">
                                        <img class="img-fluid"
                                            src="../../assets/img/shared/categories/<?php echo $entry['type']; ?>/<?php echo $entry['icon'] ?>">
                                    </div>
                                    <div class="col-4    d-flex flex-column justify-content-center text-start">
                                        <p class="category m-0"><b>
                                                <?php echo $entry['categoryName'] ?>
                                            </b></p>
                                        <p class="notes m-0">Notes:
                                            <?php echo $entry['note'] ?>
                                        </p>

                                    </div>
                                    <div class="col-4 d-flex flex-column justify-content-center text-end">
                                        <p class="price text-truncate m-0 ">
                                            <?php
                                            echo ($entry['type'] == 'income' ? '+ ' : '- ') . $currencySymbol . number_format($entry['amount'], 2);
                                            ?>

                                        </p>
                                        <?php if ($entry['type'] == 'expense' && $entry['dueDate'] != '') { ?>
                                            <p class="dueDate m-0">
                                                Due Date:
                                            </p>
                                            <?php
                                        } ?>
                                        <p class="time m-0 p-0" id='time' <?php echo ($entry['type'] == 'income') ? 'data-datetime="' . $entry['dateReceived'] . '"' : (empty($entry['dueDate']) ? 'data-datetime="' . $entry['dateSpent'] . '"' : 'data-duedate="' . $entry['dueDate'] . '"') ?>>

                                        </p>


                                    </div>
                                </div>
                            </div>
                        </a>

                        <?php
                    }
                }
            }
            ?>
            <?php
            if (!$hasIncome && $type == 'income') {
                ?>
                <div class="col-12 text-center">
                    <p class="errorHandling my-5">
                        "No Income found."
                    </p>
                </div>
                <?php
            } else if (!$hasExpense && $type == 'expense') {
                ?>
                    <div class="col-12 text-center">
                        <p class="errorHandling my-5">
                            "No Expenses found."
                        </p>
                    </div>
                <?php
            } else if ((!$hasIncome && !$hasExpense) && $type == 'all') {
                ?>
                        <div class="col-12 text-center">
                            <p class="errorHandling my-5">
                                "No Income or Expenses found."
                            </p>
                        </div>
                <?php
            }
            ?>


        </div>







    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="../../assets/js/calculateElapsedTime.js"></script>
    <script src="../../assets/js/calculateRemainingDays.js"></script>

    <script>
        setTimeout(function () {
            var alertElement = document.getElementById('myAlert');
            var alert = new bootstrap.Alert(alertElement);
            alert.close();
        }, 2000); 
    </script>
    <script>
        // Push a fake history state so back swipe hits this first
        history.pushState(null, "", location.href);

        // Handle back swipe / back button
        window.addEventListener("popstate", function (event) {
            // Redirect to home page
            location.replace("../../pages/home/home.php"); // use replace to avoid stacking history
        });
    </script>










</body>

</html>