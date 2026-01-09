<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php include("../../assets/shared/connect.php") ?>
<?php session_start(); 
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}
?>

<?php
//Currency from session
$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'PHP') ? '₱' : '$';
?>

<?php include("process/addexpensecategory.php") ?>

<?php $getExpenseCategoriesIconQuery = "SELECT `icon` FROM `tbl_defaultcategories` WHERE type = 'expense' ";
$expenseCategoriesIconResult = executeQuery($getExpenseCategoriesIconQuery);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CtrlSave | Add Expense Category</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap-Select -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">


    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/addIncomeExpenseCategory.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">


</head>

<body>
    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="addExpenses.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle">Add Expense Category</h2>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container-fluid mainContainer">
        <?php include("process/successtag.php") ?>
        <div class="container text-center py-3">
            <h4 class="title"><b>Add more expenses category</b></h4>
        </div>

        <form method="POST">

            <!-- Category Name -->
            <div class="container my-2">
                <label class="form-check-label" for="categoryName"><b>Enter Category Name:</b></label>
                <input type="text" class="form-control form-control-lg forms" id="categoryName"
                    placeholder="e.g Entertainment" name="categoryName" required>
            </div>

            <!-- Category Icon -->
            <div class="container my-3">
                <label class="form-check-label" for="iconSelect"><b>Choose Category Icon:</b></label>
                <select id="iconSelect" class="selectpicker btn-lg selectForm" data-live-search="true"
                    title="Select an icon" name="icon">
                    <?php
                    if (mysqli_num_rows($expenseCategoriesIconResult) > 0) {
                        while ($expenseCategoryIcon = mysqli_fetch_assoc($expenseCategoriesIconResult)) {
                            $icon = $expenseCategoryIcon["icon"];

                            ?>
                            <option
                                data-content="<img src='../../assets/img/shared/categories/expense/<?php echo $icon ?>' width='40' height='40'>">
                                <?php echo $icon ?>
                            </option>
                            <?php
                        }
                    }
                    ?>

                </select>
            </div>

            <!-- Need or Want -->
            <div class="container my-2">
                <p class="question p-0 m-0"><b>Is this expense a Need or a Want?</b></p>
                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="necessityType" id="needs"
                        value="needs" required>
                    <label class="form-check-label choiceLabel" for="needs">Needs</label>
                </div>
                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="necessityType" id="wants"
                        value="wants">
                    <label class="form-check-label choiceLabel" for="wants">Wants</label>
                </div>
            </div>

            <!-- Limit or Track -->
            <div class="container my-2">
                <p class="question p-0 m-0"><b>Do you want to Limit or Track this expense?</b></p>
                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="limitTrack" id="limit" value="1"
                        required>
                    <label class="form-check-label choiceLabel" for="limit">Limit</label>
                </div>
                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="limitTrack" id="track" value="0">
                    <label class="form-check-label choiceLabel" for="track">Track</label>
                </div>
            </div>

            <!-- Target Limit -->
            <div class="container my-2">
                <label class="form-check-label" for="percentage"><b>Target Limit:</b></label>
                <input type="text" class="form-control form-control-lg forms" id="targetlimit"
                    placeholder="e.g <?php echo $symbol; ?>2,000" name="targetLimit" required>

            </div>

            <!-- Save Button -->
            <div class="container text-center py-2">
                <button class="btn btn-lg btnSave" name="btnSaveExpense"><b>Save</b></button>
            </div>

        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap-Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <script>
        const currencySymbol = "<?= $symbol ?>";
    </script>


    <script>

        const limit = document.getElementById("limit");
        const track = document.getElementById("track")
        const targetlimit = document.getElementById("targetlimit")
        limit.addEventListener('change', () => {
            if (limit.checked) {
                targetlimit.removeAttribute("disabled")
            }
        })

        track.addEventListener('change', () => {
            if (track.checked) {
                targetlimit.setAttribute("disabled", true)
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