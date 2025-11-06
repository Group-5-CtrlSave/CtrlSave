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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>

  <?php include ("../../assets/shared/navigationBar.php") ?>
  <?php include ("../../assets/shared/sideBar.php") ?>

  <!-- Profile Content -->
  <div class="profile-container d-flex justify-content-center align-items-center w-100 flex-column">
    <div class="profile-card text-center">
      <h4 class="profile-name">Cassy Mondragon</h4>
      <img src="../../assets/img/shared/profile_Pic.png" alt="Avatar" class="profile-img">

      <p class="profile-username">@Casseyy</p>

      <div class="profile-section">
        <p class="profile-label">Achievements:</p>
        <img src="../../assets/img/challenge/sample badge.png" alt="Badge" class="badge-icon">
        <img src="../../assets/img/challenge/sample badge2.png" alt="Badge" class="badge-icon">
           <img src="../../assets/img/challenge/sample badge3.png" alt="Badge" class="badge-icon">
      </div>

      <div class="profile-section">
        <p class="profile-label">Email:</p>
        <p class="profile-answer">cassymondragon@gmail.com</p>
      </div>

    </div>

<!-- Buttons -->                                                                      
<div class="button-wrapper w-100 d-flex flex-column align-items-center" style="margin-top: 2px;">
  <img src="../../assets/img/shared/achievements.png" 
       alt="Achievements" 
       onclick="window.location.href='achievements.php'" 
       style="width: 100px; height: 100px; cursor: pointer; transition: transform 0.2s; margin-bottom: 2px;"
       onmouseover="this.style.transform='scale(1.1)'" 
       onmouseout="this.style.transform='scale(1)'">
       
  <button class="btn edit-btn mb-2" style="margin-top: 1px;" onclick="window.location.href='editProfile.php'">Edit Profile</button>
  <button class="btn logout-btn" style="margin-top: 1px;">Logout</button>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
