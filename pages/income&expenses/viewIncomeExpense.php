<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Expense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/viewIncomeExpense.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="income&expenses.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>


            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle">Dining Out</h2>
            </div>

        </div>


    </nav>


    <!-- Edit Button -->
    <button
        style="position:fixed;bottom:1rem;right:1rem;width:56px;height:56px;border-radius:50%;background:#F6D25B;border:none;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 6px rgba(0,0,0,0.1);transition:0.3s;"
        onmouseover="this.style.background='#3aa76e';" onmouseout="this.style.background='#F6D25B';"
        data-bs-toggle="modal" data-bs-target="#editModal">
        <img src="../../assets/img/shared/editIcon.png" alt="Add" style="width:24px;height:24px;">
    </button>





    <!-- Content -->

    <div class="container-fluid mainContainer d-inline-flex flex-column align-items-center justify-content-center">

        <!-- Modal -->
        <div class="container-fluid">

            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editIncomeExpenseModalLabel">Do you want to edit the details of
                                this expense?</h5>
                
                        </div>
                        <div class="modal-body d-flex justify-content-center align-items-center">
                            <a href="edit.php"><button type="button" class="btn btn-lg yesButton m-3"><b>Yes</b></button> </a>
                            <button type="button" class="btn btn-lg btn-secondary m-3"
                                data-bs-dismiss="modal"><b>Cancel</b></button>

                        </div>

                    </div>
                </div>
            </div>




        </div>

        <div class="container detailsContainer p-0">
            <div class="container text-center py-3">
                <img class="img-fluid categoryImage" src="../../assets/img/shared/categories/expense/Electricity.png">
            </div>
            <div class="container py-3">
                <h2 class="m-0">Amount:</h2>
                <p class="details m-0">₱600</p>
            </div>
            <div class="container py-3">
                <h2 class="m-0">Note:</h2>
                <p class="details m-0">Jollibee</p>
            </div>
            <div class="container py-3">
                <p class="details m-0">Not a recurring payment</p>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>