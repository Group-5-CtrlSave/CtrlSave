<?php include('../../assets/shared/connect.php'); ?>
<?php include("process/incomeandexpenseprocess.php") ?>
<?php include("process/viewincomeandexpenseprocess.php") ?>
<?php include('process/updateexpense.php') ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Edit</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap-Select for Bootstrap 5 -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/edit.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid d-flex justify-content-between">
            <a href="viewIncomeExpense.php?type=<?php echo $type ?>&id=<?php echo $id ?>">
                <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;" />
            </a>

            <h2 class="m-0 text-center navigationBarTitle">Edit</h2>

            <a data-bs-toggle="modal" data-bs-target="#deleteModal">
                <img class="img-fluid" src="../../assets/img/shared/trashcan.png" alt="Delete" style="height: 24px;" />
            </a>

        </div>
    </nav>






    <!-- Content -->

    <div class="container-fluid mainContainer">
        <form method="POST">


            <div class="container editContainer my-2 p-3">
                <label for="iconSelect" class="title"><b>Switch Category:</b></label>
                <select id="iconSelect" class="selectpicker btn-lg selectForm" data-live-search="true"
                    title="Select a Category" name="userCategory">

                    <?php if (mysqli_num_rows($expenseCategoriesResult) > 0) {
                        while ($expenseCategory = mysqli_fetch_assoc($expenseCategoriesResult)) {

                            $icon = $expenseCategory['icon'];
                            $categoryName = $expenseCategory['categoryName'];
                            ?>
                            <option
                                data-content="<img src='../../assets/img/shared/categories/expense/<?php echo $icon ?>' width='40' height='40'> <?php echo $categoryName ?>"
                                <?php echo ($expenseCategory['userCategoryID'] == $userCategoryID) ? 'selected' : '' ?>>

                                <?php echo $expenseCategory['userCategoryID'] ?>
                            </option>

                            <?php
                        }
                    } ?>


                </select>

            </div>

            <div class="container editContainer my-2 p-3">
                <div class="form-group">
                    <label for="formControlInput2" class="title"><b>Amount(PHP):</b></label>
                    <input type="text" class="form-control inputText" id="formControlInput2" value="<?php echo $amount?>"
                      name="amount">
                </div>
            </div>

            <div class="container editContainer my-2 p-3">
                <label class="form-check-label title"
                    for="date"><b><?php echo (!empty($dueDate) ? 'Due Date:' : 'Date:') ?></b></label>
                <input type="date" class="form-control form-control-lg dateArea" id="date" name="date"
                    value="<?php echo (!empty($dueDate) ? $dueDate : date('Y-m-d', strtotime($date))); ?>">

            </div>

            <div class="container editContainer my-2 p-3">
                <div class="form-group">
                    <label for="FormControlTextarea1" class="title"><b>Notes:</b></label>
                    <textarea class="form-control noteArea" id="FormControlTextarea1" rows="4"
                        maxlength="120" name="note"><?php echo $note ?></textarea>
                </div>
            </div>

            <div class="container my-2">
                <input class="checkBox" type="checkbox" id="recurringPayment" value="1" <?php echo ($isRecurring) ? 'checked' : '' ?> name="isRecurring">
                <label class="label" for="recurringPayment">Recurring Payment</label>
            </div>

            <div class="container my-2">
                <select class="form-select" id="frequencySelect" <?php echo ($isRecurring) ? '' : 'disabled'?> name="frequency">
                    <option selected hidden disabled>Choose Frequency</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>

                </select>

            </div>

            <div class="container fixed-bottom buttonContainer py-3">
                <button type="submit" name="saveButton" class="btn btn-lg saveButton"><b>Save</b></button>
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
        let recurringPayment = document.getElementById('recurringPayment');
        let frequencySelect = document.getElementById('frequencySelect');

        recurringPayment.addEventListener("change", function(){
            if (recurringPayment.checked){
                frequencySelect.removeAttribute('disabled');
                console.log('checked');
            } else {
                frequencySelect.setAttribute('disabled', true);
                console.log('notchecked')
            }
        })

    </script>



</body>

</html>