
<?php
session_start();
include_once '../../assets/shared/connect.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
  if (!headers_sent()) {
    header('Location: ../../pages/login&signup/login.php');
    exit();
  }
}

$userID = $_SESSION['userID'] ?? 0;

// Handle AJAX request for updating badges
if (isset($_POST['updateBadges'])) {
  $badges = mysqli_real_escape_string($conn, $_POST['badges'] ?? '');
  $updateQuery = "UPDATE tbl_users SET displayedBadges = '$badges' WHERE userID = '$userID'";
  if (mysqli_query($conn, $updateQuery)) {
    echo 'success';
  } else {
    echo 'error';
  }
  exit();
}

// Check if displayedBadges column exists, add if not
$checkColumnQuery = "SHOW COLUMNS FROM tbl_users LIKE 'displayedBadges'";
$checkResult = mysqli_query($conn, $checkColumnQuery);
if (mysqli_num_rows($checkResult) == 0) {
  $addColumnQuery = "ALTER TABLE tbl_users ADD COLUMN displayedBadges VARCHAR(255) DEFAULT NULL";
  mysqli_query($conn, $addColumnQuery);
}

// Ensure displayedBadges allows NULL and has default NULL
$modifyColumnQuery = "ALTER TABLE tbl_users MODIFY COLUMN displayedBadges VARCHAR(255) DEFAULT NULL";
mysqli_query($conn, $modifyColumnQuery);

// Check if type column exists in tbl_achievements, add if not
$checkTypeQuery = "SHOW COLUMNS FROM tbl_achievements LIKE 'type'";
$checkTypeResult = mysqli_query($conn, $checkTypeQuery);
if (mysqli_num_rows($checkTypeResult) == 0) {
  $addTypeQuery = "ALTER TABLE tbl_achievements ADD COLUMN type ENUM('title', 'badge') DEFAULT 'badge'";
  mysqli_query($conn, $addTypeQuery);
}

// Check if claimedDate column exists in tbl_userAchievements, add if not
$checkClaimedDateQuery = "SHOW COLUMNS FROM tbl_userAchievements LIKE 'claimedDate'";
$checkClaimedDateResult = mysqli_query($conn, $checkClaimedDateQuery);
if (mysqli_num_rows($checkClaimedDateResult) == 0) {
  $addClaimedDateQuery = "ALTER TABLE tbl_userAchievements ADD COLUMN claimedDate DATETIME DEFAULT CURRENT_TIMESTAMP";
  mysqli_query($conn, $addClaimedDateQuery);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['updateBadges'])) {
  $firstName = mysqli_real_escape_string($conn, $_POST['firstName'] ?? '');
  $lastName = mysqli_real_escape_string($conn, $_POST['lastName'] ?? '');
  $userName = mysqli_real_escape_string($conn, $_POST['userName'] ?? '');
  $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');

  // Profile picture handling
  $profilePictureQuery = "SELECT profilePicture FROM tbl_users WHERE userID = '$userID'";
  $currentResult = mysqli_query($conn, $profilePictureQuery);
  $current = mysqli_fetch_assoc($currentResult);
  $profilePicture = $current['profilePicture'];

  if (isset($_POST['selectedProfile']) && !empty($_POST['selectedProfile'])) {
    $profilePicture = basename($_POST['selectedProfile']);
  }

  if (isset($_FILES['profileUpload']) && $_FILES['profileUpload']['error'] == 0) {
    $targetDir = "../../assets/img/profile/";
    $fileName = time() . '_' . basename($_FILES["profileUpload"]["name"]); // Add timestamp to avoid duplicates
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Basic validation: image file
    $check = getimagesize($_FILES["profileUpload"]["tmp_name"]);
    if ($check !== false && in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
      if (move_uploaded_file($_FILES["profileUpload"]["tmp_name"], $targetFile)) {
        $profilePicture = $fileName;
      }
    }
  }

  // Badges
  $displayedBadges = mysqli_real_escape_string($conn, $_POST['selectedBadges'] ?? '');

  // Update user details
  $updateQuery = "UPDATE tbl_users SET 
                    firstName = '$firstName', 
                    lastName = '$lastName', 
                    userName = '$userName', 
                    email = '$email', 
                    profilePicture = '$profilePicture',
                    displayedBadges = '$displayedBadges'
                  WHERE userID = '$userID'";
  mysqli_query($conn, $updateQuery);

  // Password change
  $newPassword = $_POST['newPassword'] ?? '';
  $confirmPassword = $_POST['confirmPassword'] ?? '';
  if (!empty($newPassword) && $newPassword === $confirmPassword) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Assuming password column exists in tbl_users
    $updatePassQuery = "UPDATE tbl_users SET password = '$hashedPassword' WHERE userID = '$userID'";
    mysqli_query($conn, $updatePassQuery);
  }

  // Redirect to profile
  header('Location: profile.php');
  exit();
}

// Fetch user data
$userQuery = "SELECT firstName, lastName, userName, email, profilePicture, displayedBadges 
              FROM tbl_users 
              WHERE userID = '$userID' 
              LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult) ?? [
  'firstName' => '',
  'lastName' => '',
  'userName' => '',
  'email' => '',
  'profilePicture' => 'profile_Pic.png',
  'displayedBadges' => ''
];

// Fetch claimed achievements sorted by claimedDate ASC
$achievementsQuery = "
  SELECT a.achievementID, a.achievementName, a.icon, a.type
  FROM tbl_userAchievements ua
  JOIN tbl_achievements a ON ua.achievementID = a.achievementID
  WHERE ua.userID = '$userID' AND ua.isClaimed = 1
  ORDER BY ua.claimedDate ASC
";
$achievementsResult = mysqli_query($conn, $achievementsQuery);
$achievements = [];
while ($row = mysqli_fetch_assoc($achievementsResult)) {
  $achievements[] = $row;
}

// Auto-select first claimed if displayedBadges is NULL
if (is_null($user['displayedBadges']) && !empty($achievements)) {
  $titles = array_filter($achievements, function($a) { return $a['type'] === 'title'; });
  $badges = array_filter($achievements, function($a) { return $a['type'] === 'badge'; });
  $selected = [];
  if (!empty($titles)) {
    $selected[] = reset($titles)['icon'];
  }
  $badges = array_values($badges); // Reindex
  for ($i = 0; $i < 2 && $i < count($badges); $i++) {
    $selected[] = $badges[$i]['icon'];
  }
  $displayedBadges = implode(',', $selected);
  $updateQuery = "UPDATE tbl_users SET displayedBadges = '$displayedBadges' WHERE userID = '$userID'";
  mysqli_query($conn, $updateQuery);
  $user['displayedBadges'] = $displayedBadges;
}

// Current displayed badges
$displayedBadgesArray = explode(',', $user['displayedBadges'] ?? '');
?>

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
        <h4 class="m-0 text-center fw-bold">Edit Profile</h4>
      </div>
    </div>
  </nav>

  <!-- Edit Profile Form -->
  <div class="container-box">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="selectedProfile" id="selectedProfileInput">
      <input type="hidden" name="selectedBadges" id="selectedBadgesInput">
      <input type="file" name="profileUpload" id="fileInput" class="d-none" accept="image/*" onchange="previewUpload(event)">

      <div class="row g-3 align-items-center">
        <div class="col-6 text-center">
          <!-- Profile Image -->
          <div class="profile-wrapper">
            <img id="profilePreview" src="<?php echo !empty($user['profilePicture']) ? '../../assets/img/profile/' . htmlspecialchars($user['profilePicture']) : '../../assets/img/shared/profile_Pic.png'; ?>" alt="Profile" class="profile-pic">
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
              <?php
              if (!empty($displayedBadgesArray) && $displayedBadgesArray[0] !== '') {
                foreach ($displayedBadgesArray as $icon) {
                  if (!empty($icon)) {
                    echo '<img src="../../assets/img/challenge/' . htmlspecialchars(trim($icon)) . '" alt="Badge" width="40" height="40">';
                  }
                }
              } else {
                echo '<p class="text-muted small">No achievements yet</p>';
              }
              ?>
            </div>
          </div>
        </div>

        <div class="col-6">
          <div class="text-start">
            <label class="form-label">First name</label>
            <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>">
          </div>
          <div class="text-start">
            <label class="form-label">Last name</label>
            <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>">
          </div>
          <div class="text-start">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="userName" value="<?php echo htmlspecialchars($user['userName']); ?>">
          </div>
        </div>
      </div>

      <div class="text-start">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
      </div>

      <button type="submit" class="btn-save">Save Changes</button>

      <div class="password-section text-start">
        <h6 class="fw-bold">PASSWORD CHANGE</h6>
        <p style="font-size: 13px; color: black">Leave password empty when you don’t want to change it.</p>

        <label class="form-label">New Password</label>
        <input type="password" class="form-control" name="newPassword">

        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" name="confirmPassword">

        <button type="submit" class="btn-password">Change Password</button>
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
              <div class="icon-option border rounded-circle bg-white mx-auto"
                style="width: 80px; height: 80px; cursor: pointer; overflow: hidden;"
                onclick="selectUploadIcon(event); document.getElementById('fileInput').click();">
                <img id="uploadImg" src="../../assets/img/savings/uploadIcon.png" alt="Upload"
                  style="width: 50%; object-fit: contain; margin-top: 20px;">
              </div>
            </div>

            <div class="col text-center">
              <div class="icon-option border rounded-circle bg-white mx-auto" style="width: 80px; height: 80px; overflow: hidden;"
                onclick="selectProfile(event, '../../assets/img/profile/profile1.png')">
                <img src="../../assets/img/profile/profile1.png" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </div>

            <div class="col text-center">
              <div class="icon-option border rounded-circle bg-white mx-auto" style="width: 80px; height: 80px; overflow: hidden;"
                onclick="selectProfile(event, '../../assets/img/profile/profile2.png')">
                <img src="../../assets/img/profile/profile2.png" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </div>
            
            <div class="col text-center">
              <div class="icon-option border rounded-circle bg-white mx-auto" style="width: 80px; height: 80px; overflow: hidden;"
                onclick="selectProfile(event, '../../assets/img/profile/profile3.png')">
                <img src="../../assets/img/profile/profile3.png" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </div>
            <div class="col text-center">
              <div class="icon-option border rounded-circle bg-white mx-auto" style="width: 80px; height: 80px; overflow: hidden;"
                onclick="selectProfile(event, '../../assets/img/profile/profile4.png')">
                <img src="../../assets/img/profile/profile4.png" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </div>

          </div>
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
      <div class="modal-content" style="background-color: #44B87D; border-radius: 15px;">
        <div class="modal-header bg-white">
          <h5 class="modal-title">Choose Achievements (1 title and 2 badges max)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-white" style="max-height: 60vh; overflow-y: auto;">
          <h6>Titles</h6>
          <div class="row row-cols-3 g-3 mb-4">
            <?php foreach ($achievements as $achievement): ?>
              <?php if ($achievement['type'] === 'title'): ?>
                <div class="col text-center">
                  <div class="icon-option border p-2 bg-white mx-auto" style="width: 80px; height: 80px;" data-icon="<?php echo htmlspecialchars($achievement['icon']); ?>" data-type="<?php echo htmlspecialchars($achievement['type']); ?>" onclick="selectBadge(event, '<?php echo htmlspecialchars($achievement['icon']); ?>')">
                    <img src="../../assets/img/challenge/<?php echo htmlspecialchars($achievement['icon']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                  </div>
                  <p class="mt-1 small"><?php echo htmlspecialchars($achievement['achievementName']); ?></p>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>

          <h6>Badges</h6>
          <div class="row row-cols-3 g-3">
            <?php foreach ($achievements as $achievement): ?>
              <?php if ($achievement['type'] === 'badge'): ?>
                <div class="col text-center">
                  <div class="icon-option border p-2 bg-white mx-auto" style="width: 80px; height: 80px;" data-icon="<?php echo htmlspecialchars($achievement['icon']); ?>" data-type="<?php echo htmlspecialchars($achievement['type']); ?>" onclick="selectBadge(event, '<?php echo htmlspecialchars($achievement['icon']); ?>')">
                    <img src="../../assets/img/challenge/<?php echo htmlspecialchars($achievement['icon']); ?>" style="width: 100%; height: 100%; object-fit: contain;">
                  </div>
                  <p class="mt-1 small"><?php echo htmlspecialchars($achievement['achievementName']); ?></p>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="modal-footer bg-white">
          <button type="button" id="saveBadges" class="btn px-4" style="border-radius: 50px; background-color: #F6D25B; color: black;" data-bs-dismiss="modal">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Alert Modal -->
  <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background-color: #44B87D; border-radius: 15px;">
        <div class="modal-header bg-white">
          <h5 class="modal-title">Alert</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-white">
          <p id="alertMessage"></p>
        </div>
        <div class="modal-footer bg-white">
          <button type="button" class="btn px-4" style="border-radius: 50px; background-color: #F6D25B; color: black;" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let selectedProfile = null;
    let uploadedImage = null;
    let selectedBadges = [];

    let achievementTypes = {
      <?php 
      foreach ($achievements as $ach) {
        echo "'" . htmlspecialchars($ach['icon']) . "': '" . htmlspecialchars($ach['type']) . "',";
      }
      ?>
    };

    function selectProfile(event, src) {
      selectedProfile = src;
      uploadedImage = null;
      document.querySelectorAll('#profileImageModal .icon-option').forEach(el => el.classList.remove('selected'));
      event.currentTarget.classList.add('selected');

      // Reset upload image to default
      const uploadImg = document.getElementById('uploadImg');
      uploadImg.src = '../../assets/img/savings/uploadIcon.png';
      uploadImg.style.width = '50%';
      uploadImg.style.height = 'auto';
      uploadImg.style.objectFit = 'contain';
      uploadImg.style.marginTop = '20px';
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
          document.querySelectorAll('#profileImageModal .icon-option').forEach(el => el.classList.remove('selected'));
          const uploadOption = document.querySelector('#profileImageModal .icon-option[onclick*="fileInput"]');
          if (uploadOption) uploadOption.classList.add('selected');
          const uploadImg = document.getElementById('uploadImg');
          uploadImg.src = e.target.result;
          uploadImg.style.width = '100%';
          uploadImg.style.height = '100%';
          uploadImg.style.objectFit = 'cover';
          uploadImg.style.marginTop = '0';
        };
        reader.readAsDataURL(file);
      }
    }

    function applySelectedProfile() {
      const profilePreview = document.getElementById('profilePreview');
      const selectedInput = document.getElementById('selectedProfileInput');
      if (uploadedImage) {
        profilePreview.src = uploadedImage;
        selectedInput.value = '';
      } else if (selectedProfile) {
        profilePreview.src = selectedProfile;
        selectedInput.value = selectedProfile;
      }
    }

    function selectBadge(event, icon) {
      const option = event.currentTarget;
      const type = achievementTypes[icon];
      if (selectedBadges.includes(icon)) {
        selectedBadges = selectedBadges.filter(i => i !== icon);
        option.classList.remove('selected');
      } else {
        const selectedTitles = selectedBadges.filter(i => achievementTypes[i] === 'title').length;
        const selectedBadgeCount = selectedBadges.filter(i => achievementTypes[i] === 'badge').length;
        let message = '';
        if (type === 'title' && selectedTitles >= 1) {
          message = 'Only 1 title is allowed.';
        } else if (type === 'badge' && selectedBadgeCount >= 2) {
          message = 'Only 2 badges are allowed.';
        }
        if (message) {
          document.getElementById('alertMessage').textContent = message;
          const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
          alertModal.show();
        } else {
          selectedBadges.push(icon);
          option.classList.add('selected');
        }
      }
    }

    document.getElementById('saveBadges').addEventListener('click', () => {
      const badgePreviewContainer = document.getElementById('badgePreviewContainer');
      const selectedBadgesInput = document.getElementById('selectedBadgesInput');
      badgePreviewContainer.innerHTML = '';
      if (selectedBadges.length > 0) {
        selectedBadges.forEach(icon => {
          const img = document.createElement('img');
          img.src = `../../assets/img/challenge/${icon}`;
          img.alt = 'Badge';
          img.width = 40;
          img.height = 40;
          img.style.marginRight = '5px';
          badgePreviewContainer.appendChild(img);
        });
      } else {
        badgePreviewContainer.innerHTML = '<p class="text-muted small">No achievements yet</p>';
      }
      selectedBadgesInput.value = selectedBadges.join(',');

      // Auto-save via AJAX
      fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'updateBadges=1&badges=' + encodeURIComponent(selectedBadges.join(','))
      })
      .then(response => response.text())
      .then(data => {
        if (data !== 'success') {
          console.error('Failed to auto-save badges');
        }
      })
      .catch(error => console.error('Error:', error));
    });

    // Preselect badges and profile on load
    window.addEventListener('load', () => {
      const currentDisplayed = '<?php echo $user['displayedBadges']; ?>'.split(',').filter(d => d);
      selectedBadges = currentDisplayed;
      document.querySelectorAll('#badgeModal .icon-option').forEach(opt => {
        const icon = opt.dataset.icon;
        if (selectedBadges.includes(icon)) {
          opt.classList.add('selected');
        }
      });

      const currentProfile = '<?php echo htmlspecialchars($user['profilePicture']); ?>';
      let found = false;
      document.querySelectorAll('#profileImageModal .icon-option:not([onclick*="fileInput"])').forEach(el => {
        const imgSrc = el.querySelector('img').src;
        if (imgSrc.endsWith(currentProfile)) {
          el.classList.add('selected');
          found = true;
        }
      });
      if (!found && currentProfile !== '') {
        // Assume uploaded
        const uploadOption = document.querySelector('#profileImageModal .icon-option[onclick*="fileInput"]');
        uploadOption.classList.add('selected');
        const uploadImg = document.getElementById('uploadImg');
        uploadImg.src = `../../assets/img/profile/${currentProfile}`;
        uploadImg.style.width = '100%';
        uploadImg.style.height = '100%';
        uploadImg.style.objectFit = 'cover';
        uploadImg.style.marginTop = '0';
        uploadedImage = uploadImg.src;
      }
    });
  </script>
</body>
</html>
