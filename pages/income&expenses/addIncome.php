<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Add Income</title>
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
                <a href="income&expenses.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>


            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle">Add Income</h2>
            </div>

        </div>
    </nav>






    <!-- Content -->

    <div class="container-fluid mainContainer">
        <div class="scrollable-container" id="scrollableContainer">
            <div class="row py-2">
                <div class="col-4 d-flex justify-content-center align-items-center">
                    <button onclick="categoryButton(this)" type="button" class="btn p-0 m-0 text-center categoryButton">
                        <img class="img-fluid categoryPic"
                            src="../../assets/img/shared/categories/income/Allowance.png">
                        <p class="categoryName py-2"><b>Allowance</b></p>
                    </button>
                </div>
                <div class="col-4 d-flex justify-content-center align-items-center">
                    <button onclick="categoryButton(this)" type="button" class="btn p-0 m-0 text-center categoryButton">
                        <img class="img-fluid categoryPic" src="../../assets/img/shared/categories/income/Money.png">
                        <p class="categoryName py-2"><b>Salary</b></p>
                    </button>
                </div>
                <div class="col-4 d-flex justify-content-center align-items-center">
                    <button onclick="categoryButton(this)" type="button" class="btn p-0 m-0 text-center categoryButton">
                        <img class="img-fluid categoryPic"
                            src="../../assets/img/shared/categories/income/Scholarship.png">
                        <p class="categoryName py-2"><b>Scholarship</b></p>
                    </button>
                </div>
              
                <div class="col-4 d-flex justify-content-center align-items-center">
                    <a style="text-decoration: none;" href="addIncomeCategory.php">
                        <div class="container p-0 mt-1 text-center">
                            <img class="img-fluid categoryPic" src="../../assets/img/shared/addCategory.png">
                            <p class="categoryName py-2"><b>Add More</b></p>
                    </a>
                </div>
            </div>

        </div>


        <div class="container-fluid incomeForm fixed-bottom flex-grow-1 p-0 m-0 d-none" id="incomeForm">

            <div class="container-fluid inputHover" id="formContent">
                <div class="container py-2">
                    <label class="form-check-label label" for="amount"><b>Amount</b></label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">₱</span>
                        <input type="text" class="form-control form-control-lg" id="amount" placeholder="Enter amount">
                    </div>
                </div>

                <div class="container py-2">
                    <label class="form-check-label label" for="amount"><b>Notes</b></label>
                    <input type="text" class="form-control form-control-lg" id="amount" placeholder="Enter Note">
                </div>

                <div class="container py-2">
                    <label class="form-check-label label" for="date"><b>Date</b></label>
                    <input type="date" class="form-control form-control-lg" id="date" name="date">
                </div>


                <div class="container py-5 text-center">
                    <button class="btn btn-lg btnSave"><b>Save</b></button>
                </div>
            </div>

        </div>








    </div>












    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function categoryButton(catButton) {
            const buttons = document.querySelectorAll('.categoryPic');
            const imgElement = catButton.querySelector('img')
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
            const scrollableContainer = document.getElementById('scrollableContainer');

            if (isFormVisible){
                scrollableContainer.style.height= '40vh';
            }

            if (!isFormVisible) return;

            const clickedInsideForm = document.getElementById('formContent').contains(event.target);
            const clickedCategoryButton = event.target.closest('.categoryButton');

            if (!clickedInsideForm && !clickedCategoryButton) {
                form.classList.remove('d-block');
                form.classList.add('d-none');
                form.setAttribute('data-visible', 'false');
                scrollableContainer.style.height= '90vh';
                

                document.querySelectorAll('.categoryPic').forEach(btn =>
                    btn.classList.remove('selected-category')
                );
            }
        });

    </script>

</body>

</html>