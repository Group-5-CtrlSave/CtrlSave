<?php
include("../../pages/login&signup/process/loginBE.php");
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>CtrlSave Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link href="../../assets/css/login.css" rel="stylesheet">

</head>

<body>
  <?php if (isset($error)) { ?>
    <div id="errorToast">
      <?php echo $error; ?>
    </div>
  <?php } ?>

  <div class="container-fluid mainContainer p-0 m-0">
    <div class="container logoContainer d-flex justify-content-center align-items-center">
      <img class="img-fluid" src="../../assets/img/shared/logoName_M.svg" />
    </div>
    <form method="POST">
      <div class="container-fluid formContainer fixed-bottom d-flex flex-column justify-content-center align-items-center">
        <input class="form-control form-control-lg my-2 emailForm" name="emailUsername" type="text"
          placeholder="Username/Email" required>
        <div class="container-fluid password-wrapper p-0 m-0">
          <!-- Input -->
          <input class="form-control form-control-lg my-2 passwordForm" id="password" type="password" name="password"
            placeholder="Password" required>
          <!-- Show/Close Password Eye -->
          <button type="button" id="togglePassword" class="toggle-password" aria-label="Show password"
            title="Show password">
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              style="display:none;">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>

            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-5 0-9.27-3-11-8 1.13-3.15 3.67-5.67 6.6-6.77">
              </path>
              <path d="M1 1l22 22"></path>
            </svg>
          </button>
        </div>
        <div class="container-fluid d-flex justify-content-end">
          <a class="forgotPassword" href="resetPassword.php">Forgot Password?</a>
        </div>

        <button class="btn btn-lg loginBtn mt-5 mb-3" type="submit" name="btnLogin">Login</button>
        <div class="container d-flex justify-content-center align-items-center">
          <p class="p-0 m-0 noAccount">Don't have an account?</p>&nbsp;
          <a href="../landing&ads/firstAd.html" class="signUplink"><b>Sign Up</b></a>
        </div>
      </div>
    </form>


  </div>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

  <!-- Password Toggler -->
  <script>
    const pwd = document.getElementById("password");
    const toggle = document.getElementById("togglePassword");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    toggle.addEventListener("click", () => {
      if (pwd.type === "password") {
        pwd.type = "text";
        eyeOpen.style.display = "block";   // show open eye
        eyeClosed.style.display = "none";  // hide closed eye
      } else {
        pwd.type = "password";
        eyeOpen.style.display = "none";    // hide open eye
        eyeClosed.style.display = "block"; // show closed eye
      }
    });
  </script>
</body>

</html>