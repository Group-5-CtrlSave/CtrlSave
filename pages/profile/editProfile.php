<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave | Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <style>
    body {
      background: #44B87D;
      font-family: 'Poppins', sans-serif;
    }

    .container-box {
      max-width: 500px;
      margin: 20px auto;
      padding: 20px;
    }
    
    .profile-wrapper {
      position: relative;
      display: inline-block;
    }

    .profile-pic {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #fff;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.25);
    }

    /* Overlay Effect */
    .profile-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: rgba(0, 0, 0, 0.35);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 1;
      transition: opacity 0.3s ease-in-out;
      cursor: pointer;
    }

    .profile-overlay i {
      color: #fff;
      font-size: 28px;
    }

    .achievements-btn {
      background: #eee;
      border: none;
      padding: 5px 12px;
      border-radius: 8px;
      font-weight: bold;
      margin: 10px 0;
      cursor: pointer;
    }

    .form-label {
      font-weight: bold;
      font-size: 14px;
      margin-top: 10px;
      color: #fff;
    }

    .form-control,
    .form-select {
      border-radius: 10px;
      padding: 10px;
    }

    .form-control,
    textarea.form-control,
    select.form-control {
      border: 2px solid #F6D25B !important;
      border-radius: 10px;
      background-color: white;
      color: black;
    }

    .form-select {
      border: 2px solid #F6D25B !important;
      border-radius: 8px;
      background-color: white;
      color: black;
    }

    .btn-save {
      background: #F6D25B;
      font-weight: bold;
      color: #000;
      border: none;
      border-radius: 50px;
      width: 100%;
      padding: 12px;
      margin-top: 15px;
    }

    .btn-save:hover {
      background: #e0a800;
    }

    .password-section {
      margin-top: 20px;
      color: #fff;
    }

    .btn-password {
      background: #E63946;
      color: #fff;
      font-weight: bold;
      border: none;
      border-radius: 50px;
      width: 100%;
      padding: 12px;
      margin-top: 15px;
    }

    .btn-password:hover {
      background: #b02a37;
    }
  </style>
</head>

<body>

  <!-- Navigation Bar -->
  <nav class="bg-white px-4 py-3 d-flex justify-content-center align-items-center shadow sticky-top">
    <div class="container-fluid position-relative">
      <div class="d-flex align-items-start justify-content-start">
        <a href="profile.php">
          <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;" />
        </a>
      </div>
      <div class="position-absolute top-50 start-50 translate-middle">
        <h5 class="m-0 text-center fw-bold">Edit Profile</h5>
      </div>
    </div>
  </nav>

  <!-- Edit Profile Form -->
  <div class="container-box">
    <form>
      <div class="row g-3 align-items-center">
        <div class="col-6 text-center">
          <!-- Profile Image with Overlay -->
          <div class="profile-wrapper">
            <img src="../../assets/img/shared/profile_Pic.png" alt="Profile" class="profile-pic">
            <div class="profile-overlay" data-bs-toggle="modal" data-bs-target="#profileImageModal">
              <i class="bi bi-pencil-fill"></i>
            </div>
          </div>

          <!-- Achievements -->
          <div>
            <button class="achievements-btn" type="button" data-bs-toggle="modal"
              data-bs-target="#badgeModal">Achievements</button>
            <div class="mt-2">
              <img src="../../assets/img/challenge/sample badge.png" alt="Badge" width="40" height="40">
            </div>
          </div>
        </div>

       
        <div class="col-6">
          <div class="text-start">
            <label class="form-label">First name</label>
            <input type="text" class="form-control" value="Cassy">
          </div>
          <div class="text-start">
            <label class="form-label">Last name</label>
            <input type="text" class="form-control" value="Mondragon">
          </div>
          <div class="text-start">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" value="Cassey">
          </div>
        </div>
      </div>

      <!-- Bio -->
      <div class="text-start">
        <label class="form-label">Bio</label>
        <textarea class="form-control" rows="2">21 | Money is life.</textarea>
      </div>

      <!-- Email -->
      <div class="text-start">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" value="cassymondragon@gmail.com">
      </div>

      <!-- Date of Birth -->
      <div class="text-start">
        <label class="form-label">Date of Birth</label>
        <input type="date" class="form-control" value="2003-10-20">
      </div>

      <!-- Gender -->
      <div class="text-start">
        <label class="form-label">Gender</label>
        <select class="form-select">
          <option selected>Non-Binary</option>
          <option>Male</option>
          <option>Female</option>
          <option>Prefer not to say</option>
        </select>
      </div>

      <!-- Save Button -->
      <button type="submit" class="btn-save">Save Changes</button>

      <!-- Password Section -->
      <div class="password-section text-start">
        <h6 class="fw-bold">PASSWORD CHANGE</h6>
        <p style="font-size: 13px; color: black">Leave password empty when you don’t want to change it.</p>

        <label class="form-label">New Password</label>
        <input type="password" class="form-control">

        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control">

        <button type="button" class="btn-password">Change Password</button>
      </div>
    </form>
  </div>

  <!-- Profile Image Modal -->
  <div class="modal fade" id="profileImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Change Profile Picture</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="file" class="form-control" accept="image/*">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Badge Modal -->
  <div class="modal fade" id="badgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Change Achievement Badge</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="file" class="form-control" accept="image/*">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
