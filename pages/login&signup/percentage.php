<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nanum+Myeongjo&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            background-color: #44B87D;
            overflow: hidden;
        }

        /* Title & Description */

        .titleContainer {
            margin-top: 10px;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .descContainer {
            margin-top: 10px;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h2 {
            color: white;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
        }

        p {
            color: white;
            font-family: "Roboto", sans-serif;
            font-size: 16px;
        }

        /* White Table Content */
        .tableContainer {
            background-color: white;
            border: 2px solid #F6D25B;
            border-radius: 20px;
        }

        /* Categories Section */
        .categories {
            margin-top: 15px;
        }

        .expenses {
            color: black;
            justify-content: start;
            align-items: start;
            text-align: start;
        }

        .titleCateg {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            font-size: 20px;
        }

        .track {
            color: black;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .limit {
            color: black;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .scrollable-container {
            overflow-y: auto;
            height: 300px;
            overflow-x: hidden;
            margin-top: 1px;
        }

        /* Expense Css */

        .expensesTab {
            margin-top: 20px;
            height: 40px;
        }

        .expenseName {
            color: black;
            font-size: 16px;
            font-family: "Roboto", sans-serif;
            font-weight: 500px;
        }

        .checkboxCol {
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .labelLimit {
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        input[type="checkbox"] {
            accent-color: #F6D25B;
            width: 17px;
            height: 17px;
            cursor: pointer;
            position: relative;
            justify-content: center;
            align-items: center;
            text-align: center;
            margin-top: 7px;
        }

        .amountForm {
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 2px solid #F6D25B;
            width: 80px;
            font-family: "Roboto", sans-serif;
        }

        /* Savings Css */
        .savingsContainer {
            background-color: white;
            border: 2px solid #F6D25B;
            border-radius: 20px;
            height: 50px;
        }

        .savingsTab {
            margin-top: 8px;
        }

        .savingsCol {
            margin-top: 3px;
        }

        .savings {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            text-align: start;
        }

        .savingsForm {
            text-align: center;
            border: 2px solid #F6D25B;
            width: 135px;
            font-family: "Roboto", sans-serif;
        }

        /* Button */
        .btn {
            background-color: #F6D25B;
            color: black;
            text-align: center;
            width: 125px;
            font-size: 20px;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            border-radius: 27px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            margin-top: 20px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }


        /* Media Queries of Every Mobile Screen */
        @media screen and (min-width:375px) {
            .scrollable-container {
                overflow-y: auto;
                height: 200px;
                overflow-x: hidden;
                margin-top: 1px;
            }
        }

        @media screen and (min-width:414px) {
            .scrollable-container {
                overflow-y: auto;
                height: 370px;
                overflow-x: hidden;
                margin-top: 1px;
            }
        }

        @media screen and (min-width:390px) {
            .scrollable-container {
                overflow-y: auto;
                height: 300px;
                overflow-x: hidden;
                margin-top: 1px;
            }
        }

        @media screen and (min-width:430px) {
            .scrollable-container {
                overflow-y: auto;
                height: 330px;
                overflow-x: hidden;
                margin-top: 1px;
            }
        }
    </style>

</head>

<body>

    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="budgetingRule.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Own Budget Rule</h2>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mainContainer">

        <!-- Title Container -->
        <div class="titleContainer">
            <h2>
                Create your own<br>budget rule
            </h2>
        </div>

        <!-- Description Container -->
        <div class="descContainer">
            <p>
                How much do you want to spend per month?
            <p>
        </div>

        <!-- Expenses Container -->
        <div class="container tableContainer">

            <!-- Row for Categories -->
            <div class="row categories">
                <div class="col-4 expenses">
                    <h5 class="titleCateg">
                        Expenses
                    </h5>
                </div>

                <div class="col-3 track">
                    <h5 class="titleCateg">
                        Track
                    </h5>
                </div>

                <div class="col-5 limit">
                    <h5 class="titleCateg">
                        Limit
                    </h5>
                </div>
            </div>

            <!-- Row for Expenses -->
            <div class="scrollable-container">
                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            Groceries
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="grocery" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="grocery" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            Rent
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="rent" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="rent" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            Water
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="water" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="water" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            School Supplies
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="schoolSup" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="schoolSup" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            Electricity
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="electricity" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="electricity" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            Car
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="car" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="car" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            Subscription
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="sub" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="sub" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

                <div class="row expensesTab">
                    <div class="col-4 expensesCol">
                        <p class="expenseName">
                            Internet Connection
                        </p>
                    </div>

                    <div class="col-3 checkboxCol">
                        <input type="checkbox" name="internet" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-1 checkboxCol">
                        <input type="checkbox" name="internet" onchange="selectOnlyOne(this)">
                    </div>

                    <div class="col-4 labelLimit">
                        <input class="form-control form-control-sm amountForm" type="text" placeholder="Php/%">
                    </div>
                </div>

            </div>

        </div>

        <!-- Savings Description Container -->
        <div class="descContainer">
            <p>
                How much do you want to save per month?
            <p>
        </div>

        <!-- Savings Container -->
        <div class="container savingsContainer">

            <div class="row savingsTab">
                <div class="col-6 savingsCol">
                    <h5 class="savings">
                        Savings
                    </h5>
                </div>

                <div class="col-6 savingsLimit">
                    <input class="form-control form-control-sm savingsForm" type="text" placeholder="Php/%">
                </div>
            </div>

        </div>

        <!-- Button Container -->
        <div class="container buttonContainer">

            <div class="col-12 btnNext d-flex justify-content-center align-items-center">
                <a href="done.php"><button type="submit" class="btn btn-warning mb-3">Next</button></a>
            </div>

        </div>


    </div>

    <script>
        // Disable all limit inputs by default
        document.querySelectorAll('.labelLimit input').forEach(input => {
            input.disabled = true;
            input.style.opacity = '0.5'; // make it look grayed out
        });

        // Add event listeners for each expense row
        document.querySelectorAll('.expensesTab').forEach(row => {
            const checkboxes = row.querySelectorAll('input[type="checkbox"]');
            const trackCheckbox = checkboxes[0]; // first checkbox = Track
            const limitCheckbox = checkboxes[1]; // second checkbox = Limit
            const limitInput = row.querySelector('.labelLimit input');

            if (trackCheckbox && limitCheckbox && limitInput) {
                // When Track checkbox is clicked
                trackCheckbox.addEventListener('change', () => {
                    if (trackCheckbox.checked) {
                        // Disable input when tracking is chosen
                        limitInput.disabled = true;
                        limitInput.style.opacity = '0.5';
                        limitInput.value = ''; // clear value
                        limitCheckbox.checked = false; // uncheck limit if it was checked
                    }
                });

                // When Limit checkbox is clicked
                limitCheckbox.addEventListener('change', () => {
                    if (limitCheckbox.checked) {
                        // Enable input when limit is chosen
                        limitInput.disabled = false;
                        limitInput.style.opacity = '1';
                        trackCheckbox.checked = false; // uncheck track if it was checked
                    } else {
                        // Disable and gray out input if limit unchecked
                        limitInput.disabled = true;
                        limitInput.style.opacity = '0.5';
                        limitInput.value = '';
                    }
                });
            }
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>