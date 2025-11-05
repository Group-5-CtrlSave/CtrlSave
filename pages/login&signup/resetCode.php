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

        .form-control {
            border: 2px solid #F6D25B;
            height: 50px;
            font-family: "Roboto", sans-serif;
            background-color: #F0F1F6;
            border-radius: 15px;
        }

        .label {
            color: #fff;
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            margin-top: 15px;
            margin-bottom: 5px;
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
            margin-top: 30px;
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
                <a href="resetPassword.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>
            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center" style="color:black;">Reset Password</h2>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Reset your password</h2>

        <form method="POST" class="mt-4">

            <label class="label">Enter Reset Code</label>
            <input type="text" name="reset_code" class="form-control" placeholder="6-digit code" required>

            <label class="label">New Password</label>
            <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>

            <label class="label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>

            <div class="d-flex justify-content-center align-items-center">
                <button type="submit" class="btn" name="submitNewPassword">Submit</button>
            </div>

        </form>
    </div>

</body>
</html>
