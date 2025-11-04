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
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
            text-align: center;
        }

        .desc {
            font-family: "Roboto", sans-serif;
            font-size: 16px;
            color: #ffff;
            text-align: center;
        }

        .form-control {
            border: 2px solid #F6D25B;
            height: 60px;
            width: 200px;
            text-align: center;
            font-size: 20px;
            background-color: white;
            border-radius: 20px;
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
            margin-top: 10px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

    </style>

</head>

<body>

    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="currency.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Set Balance</h2>
            </div>
        </div>
    </nav>

    <!-- Cash Balance -->
    <div class="container-fluid main-container d-flex justify-content-center align-items-center mt-5">
        <div class="row main-row">

            <!-- Title -->
            <div class="col-12 title">
                <h2>Set up your cash<br>balance</h2>
            </div>

            <!-- Description -->
            <div class="col-12 desc mt-3 mb-4">
                <p>How much cash do you have in<br>your wallet right now?</p>
            </div>
            
            <!-- Form -->
            <div class="col-12 amount mt-5 mb-5 d-flex justify-content-center align-items-center">
                <input type="number" placeholder="&#8369" class="form-control" style="color: #000000">
            </div>

            <!-- Button -->
            <div class="col-12 btNext mt-5 d-flex justify-content-center align-items-center">
                <a href="pickExpense.php"><button type="submit" class="btn btn-warning mb-3">Next</button></a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>