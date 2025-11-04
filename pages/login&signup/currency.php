<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
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

        .form-group {
            margin-bottom: 10px;
            width: 100%;
            max-width: 300px;
        }

        .form-group label {
            display: block;
            font-size: 20px;
            margin-bottom: 5px;
            text-align: left;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #F6D25B;
            border-radius: 20px;
            background-color: white;
            font-size: 16px;
            color: #000000;
            appearance: none;
            background-repeat: no-repeat;
            background-position: right 10px center;
            font-family: "Roboto", sans-serif;
        }

        .form-select {
            --bs-form-select-bg-img: url("data:image/svg+xml;utf8,<svg fill='green' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path d='M1.5 5l6 6 6-6'/></svg>");
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
                <a href="signUp.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Set Currency</h2>
            </div>
        </div>
    </nav>

    <!-- Forms Content -->
    <div class="container-fluid main-container d-flex justify-content-center align-items-center mt-5">
        <div class="row main-row">

        <!-- Title -->
            <div class="col-12 title">
                <h2>Choose Your Main Currency</h2>
            </div>

        <!-- Description -->
            <div class="col-12 desc mt-4 mb-1">
                <p>Don't worry this can be changed later</p>
            </div>

            <div class="col-12 amount mt-5 mb-5 d-flex justify-content-center align-items-center">
               
                <!-- Forms -->
                <div class="form-group">
                    <label>Common Currency</label>
                    <select class="form-select" aria-label="Default select example">
                        <option selected>Currency</option>
                        <option value="1">Pesos</option>
                        <option value="2">Dollar</option>
                    </select>
                </div>
            </div>

            <!-- Button -->
            <div class="col-12 btNext mt-4 d-flex justify-content-center align-items-center">
                <a href="balance.php"><button type="submit" class="btn btn-warning mt-4">Next</button></a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>