<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php include ('../../assets/shared/connect.php') ?>
<?php session_start() ?>
<?php include ('process/addincomecategoryprocess.php')?>

<?php 
$getIncomeIconQuery = "SELECT icon from tbl_defaultcategories WHERE type = 'income'";
$incomeIconResult = executeQuery($getIncomeIconQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Add Income Category</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap-Select for Bootstrap 5 -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/addIncomeExpenseCategory.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="addIncome.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle">Add Income Category</h2>
            </div>
        </div>
    </nav>

    <!-- Content -->

    <div class="container-fluid mainContainer">
        <div class="container text-center py-3">
            <h4 class="title"><b>Add more income category</b></h4>
        </div>
        <form method="POST">

        <?php include ("process/successtag.php")?>

   
        <div class="container my-3 ">
            <label class="form-check-label" for="categoryName"><b>Enter Category Name:</b></label>
            <input type="text" class="form-control form-control-lg forms" id="categoryName" name="categoryName"
                placeholder="e.g Investments" required>
        </div>
        <!-- Category Icon -->
        <div class="container my-3">
            <label class="form-check-label" for="iconSelect"><b>Choose Category Icon:</b></label>
            <select id="iconSelect" class="selectpicker btn-lg selectForm" data-live-search="true"
                title="Select an icon" name="icon" required>
                <?php
                if (mysqli_num_rows($incomeIconResult) >0) {
                    while ($icons = mysqli_fetch_assoc($incomeIconResult)){
                        $icon = $icons['icon'];
                ?>
                <option
                    data-content="<img src='../../assets/img/shared/categories/income/<?php echo $icon ?>' width='40' height='40'>" >
                    <?php echo $icon ?>
                </option>
                <?php
                    }
                }    
                ?>
            </select>
        </div>
       

        <div class="container text-center py-5">
            <button class="btn btn-lg btnSave" type="submit" name="addIncomeCategory"><b>Save</b></button>
        </div>

         </form>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap-Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    <script>
        $(function () {
            $('.selectpicker').selectpicker();
        });
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