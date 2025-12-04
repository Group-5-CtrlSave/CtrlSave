<?php include ('../../assets/shared/connect.php')?>
<?php session_start() ?>
<?php include ('process/addExpenseprocess.php')?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Add Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/addIncomeExpense.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="income_expenses.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>


            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle">Add Expense</h2>
            </div>

        </div>
    </nav>


    <!-- Content -->

    <div class="container-fluid mainContainer d-flex flex-column">
        <div class="scrollable-container" id="scrollableContainer">
             <?php include ("process/successtag.php")?>
            <div class="row py-3">
            <?php 
            if (mysqli_num_rows($expenseCategoriesResult)>0){
                while ($expenseCategory = mysqli_fetch_assoc($expenseCategoriesResult)){
            ?>
            
            
            <div class="col-4 d-flex justify-content-center align-items-center">
                <button onclick="categoryButton(this); sendID(this)" type="button" class="btn p-0 m-0 text-center categoryButton" data-categoryID="<?php echo $expenseCategory['userCategoryID']?>">
                    <img class="img-fluid categoryPic" src="../../assets/img/shared/categories/expense/<?php echo $expenseCategory['icon']?>">
                    <p class="categoryName py-2"><b><?php echo $expenseCategory['categoryName']?></b></p>
                </button>
            </div>

            <?php
                 }
            }
            ?>

            
          
            <div class="col-4 d-flex justify-content-center align-items-center">
                <a style="text-decoration: none;" href="addExpensesCategory.php">
                    <div class="container p-0 mt-1 text-center">
                        <img class="img-fluid categoryPic" src="../../assets/img/shared/addCategory.png">
                        <p class="categoryName py-2"><b>Add More</b></p>
                </a>
            </div>
        </div>
        </div>
        
    </div>
    <form method="POST">
    <div class="container-fluid d-flex flex-column fixed-bottom p-0 m-0 d-none" id="incomeForm">
       
        <div class="container-fluid inputHover" id="formContent">
            <div class="container py-1">
                <label class="form-check-label label" for="amount"><b>Amount</b></label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text">₱</span>
                    <input type="number" step="0.01" class="form-control form-control-lg" id="amount" placeholder="Enter amount" name="amount" required>
                </div>
            </div>

            <div class="container py-1">
                <label class="form-check-label label" for="amount"><b>Notes</b></label>
                <input type="text" class="form-control form-control-lg" id="amount" placeholder="Enter Note" name="note">
            </div>

            <div class="container py-1">
                <label class="form-check-label label" for="date"><b>Date</b></label>
                <input type="date" class="form-control form-control-lg" id="date" name="date">
            </div>
            <div class="container py-2">
                <input class="checkBox" type="checkbox" id="recurringPayment" value="1" name="recurringPayment">
                <label class="checkBoxLabel" for="recurringPayment">Recurring Payment</label>
            </div>
             <div class="container py-1">
                <select class="form-select" id="frequencySelect" name="frequency" disabled required>

                <option value=" " disabled selected hidden>Choose Frequency</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>

                </select>
            </div>

            <input type="hidden" id="categoryID" name="categoryID" value="">


            <div class="container my-3 text-center">
                <button class="btn btn-lg btnSave" type="submit" name="addExpense"><b>Save</b></button>
            </div>
        </div>
    </div>
     </form>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function categoryButton(catButton) {
            const buttons = document.querySelectorAll('.categoryPic');
            const imgElement = catButton.querySelector('img');

            buttons.forEach(btn => btn.classList.remove('selected-category'));
            imgElement.classList.add('selected-category');

            const incomeForm = document.getElementById('incomeForm')
            incomeForm.classList.remove('d-none');
            incomeForm.classList.add('d-block')


            incomeForm.setAttribute('data-visible', 'true')
        }

        // Hide form when clicking outside of it or buttons
        document.addEventListener('click', function (event) {
            const form = document.getElementById('incomeForm');
            const isFormVisible = form.getAttribute('data-visible') === 'true';
            const scrollableContainer = document.getElementById ('scrollableContainer');

            if (isFormVisible){
                scrollableContainer.style.height ='40vh'
            }

            if (!isFormVisible) return;

            const clickedInsideForm = document.getElementById('formContent').contains(event.target);
            const clickedCategoryButton = event.target.closest('.categoryButton');

            if (!clickedInsideForm && !clickedCategoryButton) {
                form.classList.remove('d-block');
                form.classList.add('d-none');
                form.setAttribute('data-visible', 'false');
                scrollableContainer.style.height ='90vh'

                // Optional: remove selected-category from all buttons
                document.querySelectorAll('.categoryPic').forEach(btn =>
                    btn.classList.remove('selected-category')
                );
            }
        });

    </script>

    <script>
        function sendID(catButton){
            let categoryID = catButton.getAttribute('data-categoryID');
            document.getElementById('categoryID').value = categoryID;
        }
    </script>

    <script>
        let recurringPayment = document.getElementById('recurringPayment');
        let frequencySelect = document.getElementById('frequencySelect');

        recurringPayment.addEventListener("change", function(){
            if (recurringPayment.checked){
                frequencySelect.removeAttribute('disabled');
            } else {
                frequencySelect.setAttribute('disabled', true);
            }
        })

    </script>

    <script>
        setTimeout(function () {
            var alertElement = document.getElementById('myAlert');
            var alert = new bootstrap.Alert(alertElement);
            alert.close();
        }, 2000); 
    </script>

  



</body>

</html>