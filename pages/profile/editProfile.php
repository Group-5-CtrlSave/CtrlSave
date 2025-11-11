<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave | Edit Profile</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/editProfile.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
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
          <!-- Profile Image -->
          <div class="profile-wrapper">
            <img id="profilePreview" src="../../assets/img/shared/profile_Pic.png" alt="Profile" class="profile-pic">
            <div class="profile-overlay" data-bs-toggle="modal" data-bs-target="#profileImageModal">
              <i class="bi bi-pencil-fill"></i>
            </div>
          </div>

          <!-- Achievements -->
          <div>
            <button class="achievements-btn" type="button" data-bs-toggle="modal" data-bs-target="#badgeModal">
              Achievements
            </button>
            <div id="badgePreviewContainer" class="badge-preview-container mt-2 d-flex justify-content-center">
              <img src="../../assets/img/challenge/sample badge.png" alt="Badge" width="40" height="40">
              <img src="../../assets/img/challenge/sample badge2.png" alt="Badge" width="40" height="40">
              <img src="../../assets/img/challenge/sample badge3.png" alt="Badge" width="40" height="40">
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

      <div class="text-start">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" value="cassymondragon@gmail.com">
      </div>

      <button type="submit" class="btn-save">Save Changes</button>

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
  <div class="modal fade" id="profileImageModal" tabindex="-1" aria-labelledby="profileImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background-color: #44B87D; border-radius: 15px;">
        <div class="modal-header bg-white">
          <h5 class="modal-title fw-bold mx-auto" id="profileImageModalLabel">Choose Profile Picture</h5>
        </div>
        <div class="modal-body text-center text-white">
          <p class="fw-bold fs-5 mb-3">Pick an existing icon or upload your own</p>
          <div class="row row-cols-3 g-3 icon-container mb-3">

            <!-- Upload Icon -->
            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto"
                style="width: 80px; height: 80px; cursor: pointer;"
                onclick="selectUploadIcon(event); document.getElementById('fileInput').click();">
                <img src="../../assets/img/savings/uploadIcon.png" alt="Upload"
                  style="width: 50%; object-fit: contain; margin-top: 20px;">
              </div>
            </div>

            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;"
                onclick="selectProfile(event, '../../assets/img/shared/profile_Pic.png')">
                <img src="../../assets/img/shared/profile_Pic.png" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>

            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;"
                onclick="selectProfile(event, '../../assets/img/challenge/sample badge.png')">
                <img src="../../assets/img/challenge/sample badge.png" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>
            
            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;"
                onclick="selectProfile(event, '../../assets/img/challenge/sample badge2.png')">
                <img src="../../assets/img/challenge/sample badge2.png" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>

          </div>
          <input type="file" id="fileInput" class="d-none" accept="image/*" onchange="previewUpload(event)">
        </div>
        <div class="modal-footer bg-white justify-content-center">
          <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success px-4 rounded-pill" data-bs-dismiss="modal" onclick="applySelectedProfile()">Apply</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Badge Modal -->
  <div class="modal fade" id="badgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Choose Achievement Badges (max 3)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <div class="row row-cols-3 g-3 icon-container">
            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px; cursor: pointer;">
                <img src="../../assets/img/challenge/sample badge.png" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>
            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px; cursor: pointer;">
                <img src="../../assets/img/challenge/sample badge2.png" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>
            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px; cursor: pointer;">
                <img src="../../assets/img/challenge/sample badge3.png" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>
            <div class="col text-center">
              <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px; cursor: pointer;">
                <img src="../../assets/img/challenge/sample badge4.png" style="width: 100%; height: 100%; object-fit: contain;">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="saveBadges" class="btn btn-success" data-bs-dismiss="modal">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let selectedProfile = null;
    let uploadedImage = null;

    function selectProfile(event, src) {
      selectedProfile = src;
      uploadedImage = null;
      document.querySelectorAll('#profileImageModal .icon-option').forEach(el => el.classList.remove('selected'));
      event.currentTarget.classList.add('selected');
    }

    function selectUploadIcon(event) {
      document.querySelectorAll('#profileImageModal .icon-option').forEach(el => el.classList.remove('selected'));
      event.currentTarget.classList.add('selected');
    }

    function previewUpload(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          uploadedImage = e.target.result;
          selectedProfile = null;
          const uploadIcon = document.querySelector('#profileImageModal .icon-option[onclick*="fileInput"]');
          document.querySelectorAll('#profileImageModal .icon-option').forEach(el => el.classList.remove('selected'));
          if (uploadIcon) uploadIcon.classList.add('selected');
        };
        reader.readAsDataURL(file);
      }
    }

    function applySelectedProfile() {
      const profilePreview = document.getElementById('profilePreview');
      if (uploadedImage) {
        profilePreview.src = uploadedImage;
      } else if (selectedProfile) {
        profilePreview.src = selectedProfile;
      } else {
        profilePreview.src = "../../assets/img/shared/profile_Pic.png";
      }
    }

    const badgeOptions = document.querySelectorAll('#badgeModal .icon-option');
    const badgePreviewContainer = document.getElementById('badgePreviewContainer');
    let selectedBadges = [];

    badgeOptions.forEach(option => {
      option.addEventListener('click', () => {
        const imgSrc = option.querySelector('img').src;

        if (selectedBadges.includes(imgSrc)) {
          selectedBadges = selectedBadges.filter(src => src !== imgSrc);
          option.classList.remove('selected');
        } else if (selectedBadges.length < 3) {
          selectedBadges.push(imgSrc);
          option.classList.add('selected');
        } else {
          alert("You can select up to 3 badges only.");
        }
      });
    });

    document.getElementById('saveBadges').addEventListener('click', () => {
      badgePreviewContainer.innerHTML = '';
      selectedBadges.forEach(src => {
        const img = document.createElement('img');
        img.src = src;
        img.width = 40;
        img.height = 40;
        img.style.marginRight = '5px';
        badgePreviewContainer.appendChild(img);
      });
    });
  </script>
</body>
</html>