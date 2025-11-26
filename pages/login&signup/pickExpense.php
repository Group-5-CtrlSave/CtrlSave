<?php include("../../assets/shared/connect.php") ?>
<?php session_start() ?>

<?php include('process/pickExpenseBE.php') ?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Pick Expenses</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">

    <style>
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
            padding-bottom: 12px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="balance.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Pick Expenses</h2>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid d-flex flex-column justify-content-between main-container">

        <div class="row">
            <div class="col-12 title">
                <h2>What are your<br>expenses right now?</h2>
            </div>
        </div>

        <form method="POST">
            <div class="scrollable-container">
                <div class="row">
                    <?php
                    $counter = 0;
                    $extraCategories = [];

                    foreach ($allCategories as $category) {
                        $checked = '';
                        if (isset($category['source']) && $category['source'] === 'user' && $category['isSelected']) {
                            $checked = 'checked';
                        }

                        if ($counter < 4) {
                            ?>
                            <div class="col-12">
                                <div class="expense-option">
                                    <label class="expense-label">
                                        <input type="checkbox" name="userCategories[]"
                                            value="<?php echo $category['categoryID']; ?>" <?php echo $checked; ?> />
                                        <?php echo $category['categoryName']; ?>
                                    </label>
                                    <img src="../../assets/img/shared/categories/expense/<?php echo $category['icon']; ?>"
                                        alt="<?php echo $category['categoryName']; ?>" class="expense-icon" />
                                </div>
                            </div>
                            <?php
                        } else {
                            $extraCategories[] = $category;
                        }

                        $counter++;
                    }
                    ?>

                    <?php if (!empty($extraCategories)) { ?>
                        <div class="col-12">
                            <a id="seeMoreButton" onclick="seeMoreCateg()" style="color: #fff; display: block;">
                                <span class="expense-label">See more...</span>
                            </a>
                        </div>
                    <?php } ?>
                </div>

                <!-- Extra Expense Section -->
                <div class="row" id="moreCateg" style="display: none;">
                    <?php foreach ($extraCategories as $category) {
                        $checked = '';
                        if (isset($category['source']) && $category['source'] === 'user' && $category['isSelected']) {
                            $checked = 'checked';
                        }
                        ?>
                        <div class="col-12">
                            <div class="expense-option">
                                <label class="expense-label">
                                    <input type="checkbox" name="userCategories[]"
                                        value="<?php echo $category['categoryID']; ?>" <?php echo $checked; ?> />
                                    <?php echo $category['categoryName']; ?>
                                </label>
                                <img src="../../assets/img/shared/categories/expense/<?php echo $category['icon']; ?>"
                                    alt="<?php echo $category['categoryName']; ?>" class="expense-icon" />
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-12">
                        <a id="hideButton" onclick="hideMoreCateg()" style="color: #fff; display: none;">
                            <span class="expense-label">Hide</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 pb-3 d-flex justify-content-center">
                <button type="submit" name="btnSaveCategories" class="btn btn-warning mt-4">Next</button>
            </div>
        </form>


        <!-- Add more Expenses -->
        <div class="col-12 mt-1 d-flex justify-content-center align-items-center">
            <p style="color: #ffff;">Can't find preffered expenses?</p>&nbsp;<a href="addExpensesSign.php"
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