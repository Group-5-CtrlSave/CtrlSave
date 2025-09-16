<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
            <button type="button" class="btn custom-btn sortButton allButton selected"
                onclick="selectButton(this)"><b>All</b></button>
            <button type="button" class="btn custom-btn sortButton incomeButton"
                onclick="selectButton(this)"><b>Income</b></button>
            <button type="button" class="btn custom-btn sortButton expenseButton"
                onclick="selectButton(this)"><b>Expenses</b></button>
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
            <div class="row">
                <a href="viewIncomeExpense.php" style="text-decoration: none; color:black">
                    <div class="col-12">
                        <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
                            <div class="container categoryImgContainer p-1">
                                <img class="img-fluid" src="../../assets/img/shared/categories/expense/Dining Out.png">
                            </div>
                            <div class="container categoryTextContainer p-1">
                                <p class="category m-0"><b>Dining Out</b></p>
                                <p class="notes m-0">Notes: Jollibee
                                <p>
                            </div>

                            <div class="container iePriceContainer p-1">
                                <h5 class="price m-0">- ₱300</h5>
                                <p class="time m-0"><b>6:40 PM</b>
                                <p>
                            </div>

                        </div>
                    </div>
                </a>

                <div class="col-12">
                    <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
                        <div class="container categoryImgContainer p-1">
                            <img class="img-fluid" src="../../assets/img/shared/categories/expense/Transportation.png">
                        </div>
                        <div class="container categoryTextContainer p-1">
                            <p class="category m-0"><b>Transportation</b></p>
                            <p class="notes m-0">Notes: Pamasahe tasdasdo...
                            <p>
                        </div>

                        <div class="container iePriceContainer p-1">
                            <h5 class="price m-0">- ₱200000000000</h5>
                            <p class="time m-0"><b>9:51 PM</b>
                            <p>
                        </div>

                    </div>
                </div>

                <div class="col-12">
                    <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
                        <div class="container categoryImgContainer p-1">
                            <img class="img-fluid" src="../../assets/img/shared/categories/income/Allowance.png">
                        </div>
                        <div class="container categoryTextContainer p-1">
                            <p class="category m-0"><b>Allowance</b></p>
                            <p class="notes m-0">Notes: Bigay ni Mama
                            <p>
                        </div>

                        <div class="container iePriceContainer p-1">
                            <h5 class="price m-0">+ ₱4000</h5>
                            <p class="time m-0"><b>12:51 PM</b>
                            <p>
                        </div>

                    </div>
                </div>

                <div class="col-12">
                    <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
                        <div class="container categoryImgContainer p-1">
                            <img class="img-fluid" src="../../assets/img/shared/categories/income/Allowance.png">
                        </div>
                        <div class="container categoryTextContainer p-1">
                            <p class="category m-0"><b>Allowance</b></p>
                            <p class="notes m-0">Notes: Bigay ni Papa
                            <p>
                        </div>

                        <div class="container iePriceContainer p-1">
                            <h5 class="price m-0">+ ₱5000</h5>
                            <p class="time m-0"><b>12:51 PM</b>
                            <p>
                        </div>

                    </div>
                </div>


            </div>
        </div>




    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectButton(clickedButton) {
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => btn.classList.remove('selected'));
            clickedButton.classList.add('selected');
        }
    </script>

</body>

</html>