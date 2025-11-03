<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        body,
        html {
            background-color: #44B87D;
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
            text-align: center;
            padding-top: 10px;
        }

        .main-container {
            height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .scrollable-container {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0 15px;
        }

        .expense-option {
            background-color: white;
            border-radius: 20px;
            padding: 12px 15px;
            margin: 8px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 2px solid #F6D25B;
            background-color: white;
        }

        .expense-option input[type="checkbox"] {
            accent-color: #F6D25B;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
            position: relative;
        }

        .expense-label {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-family: "Roboto", sans-serif;
        }

        .expense-icon {
            width: 50px;
            height: 40x;
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
            margin-top: 10px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        .addmoreLink {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            padding-bottom: 15px;
        }

    </style>
</head>

<body>

    <!-- No Logo Navigation Bar -->
    <nav class="bg-white px-4 d-flex align-items-center justify-content-between position-relative shadow"
        style="height: 72px;">
        <a href="balance.php" class="text-decoration-none">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
        </a>
        <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark"
            style="font-family: Poppins, sans-serif;">
            Pick Expenses
        </h5>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid d-flex flex-column justify-content-between main-container">

        <div class="row">
            <div class="col-12 title">
                <h2>What are your<br>expenses right now?</h2>
            </div>
        </div>

        <!-- Expense Section -->
        <div class="scrollable-container">
            <div class="row">
                <div class="col-12">
                    <div class="expense-option">
                        <label class="expense-label"><input type="checkbox" /> Dining Out</label>
                        <img src="../../assets/img/shared/categories/expense/Dining Out.png" alt="Dining"
                            class="expense-icon" />
                    </div>
                </div>

                <div class="col-12">
                    <div class="expense-option">
                        <label class="expense-label"><input type="checkbox" /> Electricity</label>
                        <img src="../../assets/img/shared/categories/expense/Electricity.png" alt="Electricity"
                            class="expense-icon" />
                    </div>
                </div>

                <div class="col-12">
                    <div class="expense-option">
                        <label class="expense-label"><input type="checkbox" /> Groceries</label>
                        <img src="../../assets/img/shared/categories/expense/Groceries.png" alt="Groceries"
                            class="expense-icon" />
                    </div>
                </div>

                <div class="col-12">
                    <div class="expense-option">
                        <label class="expense-label"><input type="checkbox" /> Rent</label>
                        <img src="../../assets/img/shared/categories/expense/Rent.png" alt="Rent"
                            class="expense-icon" />
                    </div>
                </div>

                <div class="col-12">
                    <a id="seeMoreButton" onclick="seeMoreCateg()" style="color: #ffffffff; display: block;"><span
                            class="expense-label">See
                            more...</span></a>
                </div>
            </div>

            <!-- Extra Expense Section -->
            <div class="row" id="moreCateg" style="display: none;">
                <div class="col-12">
                    <div class="expense-option">
                        <label class="expense-label"><input type="checkbox" /> Wifi</label>
                        <img src="../../assets/img/shared/categories/expense/Internet Connection.png" alt="Dining"
                            class="expense-icon" />
                    </div>
                </div>

                <div class="col-12 pt-1">
                    <div class="expense-option">
                        <label class="expense-label"><input type="checkbox" /> Water</label>
                        <img src="../../assets/img/shared/categories/expense/Water.png" alt="Electricity"
                            class="expense-icon" />
                    </div>
                </div>

                <div class="col-12">
                    <a id="hideButton" onclick="hideMoreCateg()" style="color: #ffffffff; display: none;"><span
                            class="expense-label">Hide</span></a>
                </div>
            </div>
        </div>

        <!-- Button -->
        <div class="col-12 pb-3 d-flex justify-content-center">
            <a href="needsWants.php"><button type="submit" class="btn btn-warning mt-4">Next</button></a>
        </div>

        <!-- Add more Expenses -->
        <div class="col-12 mt-1 d-flex justify-content-center align-items-center">
            <p style="color: #ffff; font-family: Poppins, sans-serif;">Can't find preffered expenses?</p>&nbsp;<a href="addExpensesSign.php"
                class="addmoreLink" style="color: black;">Add more...</a>
        </div>

    </div>

    <script>
        function seeMoreCateg() {
            document.getElementById('seeMoreButton').style.display = "none";
            document.getElementById('moreCateg').style.display = "block";
            document.getElementById('hideButton').style.display = "block";
        }

        function hideMoreCateg() {
            document.getElementById('seeMoreButton').style.display = "block";
            document.getElementById('moreCateg').style.display = "none";
            document.getElementById('hideButton').style.display = "none";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>