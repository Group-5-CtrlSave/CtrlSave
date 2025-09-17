<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Edit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/edit.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid d-flex justify-content-between">
            <a href="viewIncomeExpense.php">
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

        <!-- Modal -->
        <div class="container-fluid">

            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Do you want to edit the delete this item?</h5>
                        </div>
                        <div class="modal-body d-flex justify-content-center align-items-center">
                            <a href="edit.php"><button type="button"
                                    class="btn btn-lg yesButton m-3"><b>Yes</b></button> </a>
                            <button type="button" class="btn btn-lg btn-secondary m-3"
                                data-bs-dismiss="modal"><b>Cancel</b></button>

                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="container editContainer my-2 p-3">
            <div class="form-group">
                <label for="formControlInput1" class="title"><b>Name:</b></label>
                <input type="text" class="form-control inputText" id="formControlInput1" placeholder="">
            </div>
        </div>

        <div class="container editContainer my-2 p-3">
            <div class="form-group">
                <label for="formControlInput2" class="title"><b>Amount(PHP):</b></label>
                <input type="text" class="form-control inputText" id="formControlInput2" placeholder="">
            </div>
        </div>

        <div class="container editContainer my-2 p-3">
            <label class="form-check-label title" for="date"><b>Date</b></label>
            <input type="date" class="form-control form-control-lg dateArea" id="date" name="date">
        </div>

        <div class="container editContainer my-2 p-3">
            <div class="form-group">
                <label for="FormControlTextarea1" class="title"><b>Notes:</b></label>
                <textarea class="form-control noteArea" id="FormControlTextarea1" rows="4" maxlength="120"></textarea>
            </div>
        </div>

        <div class="container my-2 py-3">
            <input class="checkBox" type="checkbox" id="recurringPayment" value="Yes">
            <label class="label" for="recurringPayment">Recurring Payment</label>
        </div>

         <div class="container fixed-bottom buttonContainer py-3">
           <button type="submit" class="btn btn-lg saveButton"><b>Save</b></button>
        </div>





    </div>












    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>