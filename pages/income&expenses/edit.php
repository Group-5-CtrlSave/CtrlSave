<?php include('../../assets/shared/connect.php'); ?>
<?php session_start();?>
<?php include("process/incomeandexpenseprocess.php") ?>
<?php include("process/viewincomeandexpenseprocess.php") ?>
<?php include('process/editIncomeExpense.php') ?>

<?php
$getuserCategoriesQuery = "SELECT userCategoryID, categoryName, icon
FROM `tbl_usercategories` WHERE userID = $userID AND type ='$type' AND isSelected = 1";
$userCategoriesResult = executeQuery($getuserCategoriesQuery);
?>

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

            <a data-bs-toggle="modal" data-bs-target="#deleteIncomeExpenseModal">
                <img class="img-fluid" src="../../assets/img/shared/trashcan.png" alt="Delete" style="height: 24px;" />
            </a>

        </div>
    </nav>






    <!-- Content -->

    <div class="container-fluid mainContainer">
        
        <!-- Modal -->

        <div class="container-fluid">

            <div class="modal fade" id="deleteIncomeExpenseModal" tabindex="-1" aria-labelledby="deleteIncomeExpenseLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addIncomeExpenseModalLabel">Do you want to delete this <?php echo $type?>?</h5>
                           
                        </div>
                        <div class="modal-body d-flex justify-content-center align-items-center">
                             <form method="POST">
                             <button
                              class="btn btn-success btn-lg  mx-3" type="submit" name="btnDelete"><b>Yes</b></button></a>
                             <button type="button"
                              class="btn btn-danger btn-lg  mx-3" data-bs-dismiss="modal" aria-label="Close"><b>No</b></button></a>
                              </form>
                        </div>

                    </div>
                </div>
            </div>


        </div>



        <form method="POST">


            <div class="container editContainer my-2 p-3">
                <label for="iconSelect" class="title"><b>Switch Category:</b></label>
                <select id="iconSelect" class="selectpicker btn-lg selectForm" data-live-search="true"
                    title="Select a Category" name="userCategory">

                    <?php if (mysqli_num_rows($userCategoriesResult) > 0) {
                        while ($userCategory = mysqli_fetch_assoc($userCategoriesResult)) {

                            $icon = $userCategory['icon'];
                            $categoryName = $userCategory['categoryName'];
                            ?>
                            <option
                                data-content="<img src='../../assets/img/shared/categories/<?php echo $type?>/<?php echo $icon ?>' width='40' height='40'> <?php echo $categoryName?>"
                                <?php echo ($userCategory['userCategoryID'] == $userCategoryID) ? 'selected' : '' ?>>

                                <?php echo $userCategory['userCategoryID'] ?>
                            </option>

                            <?php
                        }
                    } ?>


                </select>

            </div>

            <div class="container editContainer my-2 p-3">
                <div class="form-group">
                    <label for="formControlInput2" class="title"><b>Amount(PHP):</b></label>
                    <input type="number" class="form-control inputText" id="formControlInput2" value="<?php echo $amount?>"
                      name="amount" required>
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

            <?php if ($type == 'expense'){?>
            <div class="container my-2">
                <input class="checkBox" type="checkbox" id="recurringPayment" value="1" <?php echo ($isRecurring) ? 'checked' : '' ?> name="isRecurring">
                <label class="label" for="recurringPayment">Recurring Payment</label>
            </div>

            <div class="container my-2">
                <select class="form-select" id="frequencySelect" <?php echo ($isRecurring) ? '' : 'disabled'?> name="frequency" required>
                    <option value='' selected hidden disabled>Choose Frequency</option>
                    <option value="daily" <?php echo ($expenseFrequency === 'daily') ? 'selected' : ''?>>Daily</option>
                    <option value="weekly" <?php echo ($expenseFrequency === 'weekly') ? 'selected' : ''?>>Weekly</option>
                    <option value="monthly" <?php echo ($expenseFrequency === 'monthly') ? 'selected' : ''?>>Monthly</option>

                </select>

            </div>
            <?php } ?>

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

    <?php 
    if (isset($_GET['type']) && $_GET['type'] === 'income'){
    ?>
    
    <script>
        const today = new Date().toISOString().split('T')[0];
        let date = document.getElementById('date')
        date.setAttribute('max', today);
    </script>

        <?php
    }
   ?>
  
  



</body>

</html>