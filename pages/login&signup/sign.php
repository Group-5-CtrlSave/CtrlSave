<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>CtrlSave | Signup</title>
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
    rel="stylesheet">
  <link type="text/css" href="../../assets/css/signup.css" rel="stylesheet">
</head>

<body>
  <div class="container-fluid mainContainer p-0 m-0">
    <!-- Toast Message -->
    <?php if (!empty($error)) { ?>
      <div id="errorToast">
        <?php echo $error; ?>
      </div>
    <?php } ?>
    <div class="container logoContainer d-flex justify-content-end align-items-end">
      <img class="img-fluid my-2" src="../../assets/img/shared/logo_S.png" />
    </div>
    <form method="POST">
      <div class="container-fluid formContainer fixed-bottom d-flex justify-content-center align-items-center">
        <div class="container">
          <h3 class="signUpText">Sign Up</h3>
          <!-- Sign Up Form -->

          <div class="row">
            <div class="col-12">
              <input class="form-control my-2" placeholder="Username" name="username" required>
            </div>
            <div class="col-12">
              <input class="form-control my-2" placeholder="First Name" name="firstname" required>
            </div>
            <div class="col-12">
              <input class="form-control my-2" placeholder="Last Name" name="lastname" required>
            </div>
            <div class="col-12">
              <input class="form-control my-2" placeholder="Email" name="email" required>
            </div>
            <div class="col-12">
              <div class="my-1" id="passwordRequirements"
                style="font-size: 10px; color: white; font-family: 'Poppins', sans-serif;">
                <p class="m-0" id="reqLength">❌ At least 8 characters</p>
                <p class="m-0" id="reqUpper">❌ One uppercase letter (A–Z)</p>
                <p class="m-0" id="reqLower">❌ One lowercase letter (a–z)</p>
                <p class="m-0" id="reqNumber">❌ One number (0–9)</p>
                <p class="m-0" id="reqSpecial">❌ One special character (@$!%*?&)</p>
                <div class="password-wrapper">
                  <input id="password" type="password" class="form-control my-2" name="password" placeholder="Password"
                    required pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}"
                    title="Password must be at least 8 characters with uppercase, lowercase, number, and special character.">
                  <button type="button" id="togglePassword" class="toggle-password" aria-label="Show password"
                    title="Show password">
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                      fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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
              </div>

            </div>
            <div class="col-12 d-flex flex-column justify-content-center align-items-center ">
              <button class="btn btn-lg loginBtn my-3" type="submit" id="nextBtn" disabled>Next</button>
              <a class="backToLogin p-0 my-3" href="login.php"><- Back to Login</a>

            </div>

          </div>

        </div>



      </div>
    </form>


  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

  <!-- Password Toggle -->
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

  <!-- Password Checker -->
  <script>
    const password = document.getElementById("password");
    const nextBtn = document.getElementById("nextBtn");

    // Requirement text elements
    const reqLength = document.getElementById("reqLength");
    const reqUpper = document.getElementById("reqUpper");
    const reqLower = document.getElementById("reqLower");
    const reqNumber = document.getElementById("reqNumber");
    const reqSpecial = document.getElementById("reqSpecial");

    // Validation Regex
    function validatePassword(pwd) {
      return {
        length: pwd.length >= 8,
        upper: /[A-Z]/.test(pwd),
        lower: /[a-z]/.test(pwd),
        number: /\d/.test(pwd),
        special: /[^A-Za-z0-9]/.test(pwd)
      };
    }

    // Update UI on input
    password.addEventListener("input", () => {
      const val = password.value;
      const check = validatePassword(val);

      // Update icons only — ✔ white, ❌ black
      reqLength.innerHTML = (check.length ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " At least 8 characters";
      reqUpper.innerHTML = (check.upper ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One uppercase letter (A–Z)";
      reqLower.innerHTML = (check.lower ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One lowercase letter (a–z)";
      reqNumber.innerHTML = (check.number ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One number (0–9)";
      reqSpecial.innerHTML = (check.special ? "<span style='color:white;'>✔</span>" : "<span style='color:black;'>❌</span>") + " One special character (@$!%*?&)";

      // Enable button only if all valid
      if (check.length && check.upper && check.lower && check.number && check.special) {
        nextBtn.disabled = false;
        nextBtn.style.opacity = "1";
      } else {
        nextBtn.disabled = true;
        nextBtn.style.opacity = "0.6";
      }
    });
  </script>
</body>

</html>