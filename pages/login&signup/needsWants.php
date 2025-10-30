<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave</title>
    <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
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
        }

        .desc {
            font-family: "Roboto", sans-serif;
            font-size: clamp(1.3rem, 1vw, 1rem);
            color: #ffff;
            text-align: center;
        }

        .main-container {
            display: flex;
            justify-content: center;
        }

        .titleCateg{
            font-family: "Poppins", sans-serif;
            font-weight: bold;
        }

        .expenseName {
            width: 100px;
            font-family: "Roboto", sans-serif;
            font-weight: 500;
        }

        .btn {
            background-color: #F6D25B;
            color: black;
            text-align: center;
            width: 150px;
            font-size: clamp(1.3rem, 2vw, 1rem);
            font-weight: bold;
            font-family: "Roboto", sans-serif;
            border-radius: 30px;
            margin-top: 40px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        input[type="checkbox"] {
            accent-color: #F6D25B;
            width: 20px;
            height: 20px;
            cursor: pointer;
            position: relative;
        
        }
    </style>
</head>

<body>

    <!-- No Logo Navigation Bar -->
    <nav class="bg-white px-4 d-flex align-items-center justify-content-between position-relative shadow"
        style="height: 72px;">
        <a href="pickExpense.php" class="text-decoration-none">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
        </a>
        <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark"
            style="font-family: Poppins, sans-serif;">
            Categorize
        </h5>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid d-flex flex-column justify-content-between main-container">

        <div class="row">

            <!-- Title -->
            <div class="col-12 title mt-5">
                <h2>Categorize Expenses</h2>
            </div>

            <!-- Description -->
            <div class="col-12 desc mt-2">
                <p>Categorize it based on Needs and Wants</p>
            </div>
        </div>

        <!-- Need and Wants -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                <div class="p-3 rounded" style="min-width: 300px; height: 250px; background-color: #F0F1F6; border: 2px solid #F6D25B;">

                    <!-- Table Header -->
                    <div class="d-flex justify-content-between mb-3" style="color: #44B87D;">
                        <div class="titleCateg" style="width: 100px;"><strong>Expense</strong></div>
                        <div class="titleCateg"><strong>Needs</strong></div>
                        <div class="titleCateg"><strong>Wants</strong></div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="row" style="overflow-x: scroll; min-width: 300px; height: 180px;">
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <div class="expenseName">Dining Out</div>
                            <input type="checkbox" name="diningOut"
                                onchange="selectOnlyOne(this)">
                            <input type="checkbox" name="diningOut"
                                onchange="selectOnlyOne(this)">
                        </div>

                        
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <div class="expenseName">Electricity</div>
                            <input type="checkbox" name="electricity"
                                onchange="selectOnlyOne(this)">
                            <input type="checkbox" name="electricity"
                                onchange="selectOnlyOne(this)">
                        </div>

                        
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <div class="expenseName">Groceries</div>
                            <input type="checkbox" name="groceries"
                                onchange="selectOnlyOne(this)">
                            <input type="checkbox" name="groceries"
                                onchange="selectOnlyOne(this)">
                        </div>

                        
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <div class="expenseName">Rent</div>
                            <input type="checkbox" name="rent" onchange="selectOnlyOne(this)">
                            <input type="checkbox" name="rent" onchange="selectOnlyOne(this)">
                        </div> 
                    </div>

                </div>
            </div>
        </div>

        <!-- Buttons at Bottom -->
        <div class="col-12 d-flex justify-content-center">
            <a href="budgetingRule.php"><button type="submit" class="btn btn-warning mb-3">Next</button></a>
        </div>

    </div>

    <script>
        function selectOnlyOne(checkbox) {
            const checkboxes = document.getElementsByName(checkbox.name);
            checkboxes.forEach((item) => {
                if (item !== checkbox) item.checked = false;
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>