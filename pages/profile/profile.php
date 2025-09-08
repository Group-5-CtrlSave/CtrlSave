<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body>


<?php include ("../../assets/shared/navigationBar.php") ?>

<?php include ("../../assets/shared/sideBar.php")?>


    <!-- Content -->
      <div class="bg-green-custom min-vh-100 d-flex position-relative">
    
        <div id="overlay" class="d-none"></div>

        <div class="flex-grow-1 p-4">
            <h2 class="fs-2 fw-bold mb-4 text-white">User Profile</h2>
          <form aria-label="User profile form" novalidate onsubmit="handleFormSubmit(event)">
                <section class="profile-header mb-3" aria-label="User information">
                    <div class="profile-image" role="img" aria-label="profile">
                        <img
                            src= "../../assets/img/shared/profile_Pic.png"/>
                    </div>
                    
                    <div class="profile-names">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input id="firstName" type="text" value="Cassy" autocomplete="given-name" aria-required="true" required class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input id="lastName" type="text" value="Mondragon" autocomplete="family-name" aria-required="true" required class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="userName">User Name</label>
                            <input id="userName" type="text" value="Casseyy" autocomplete="username" aria-required="true" required class="form-control" />
                        </div>
                    </div>
                </section>

                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input id="email" type="email" value="cassymondragon@gmail.com" autocomplete="email" aria-required="true" required class="form-control" />
                </div>

                <div class="row">
                    <div class="form-group col-md-6 mb-3">
                        <label for="dob">Date of birth</label>
                        <input id="dob" type="date" value="" autocomplete="bday" aria-required="false" class="form-control" />
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label for="gender">Select gender</label>
                        <select id="gender" aria-required="false" aria-label="Select gender" class="form-control">
                            <option value="" selected>None</option>
                            <option value="female">Female</option>
                            <option value="male">Male</option>
                            <option value="nonbinary">Non-binary</option>
                            <option value="other">Other</option>
                            <option value="prefer-not-to-say">Prefer not to say</option>
                        </select>
                    </div>
                </div>

                <button type="button" class="btn logout mb-3" aria-label="Log out from the account" onclick="handleLogout()">LOGOUT</button>

                <section class="password-change-section" aria-label="Password change form">
                    <h2>PASSWORD CHANGE</h2>

                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input id="password" type="password" autocomplete="new-password" aria-describedby="pwHelp" class="form-control" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="confirm-password">Confirm password</label>
                        <input id="confirm-password" type="password" autocomplete="new-password" aria-describedby="pwHelp" class="form-control" />
                    </div>
                    <button type="submit" class="btn change-password" aria-label="Change password">CHANGE PASSWORD</button>
                </section>
            </form>
        </div> 
    </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  



</body>

</html>