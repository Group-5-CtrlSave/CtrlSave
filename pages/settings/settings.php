<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/home.css">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
</head>

<body>
  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>

  <!-- Sidebar content-->
  <?php include ("../../assets/shared/sideBar.php")?>

  <!-- Main Page Wrapper -->
  <div class="bg-green-custom min-vh-100 d-flex position-relative">

    <div id="overlay" class="d-none"></div>

    <!-- ✅ Settings Page Content -->
    <div class="container py-4">
      <h1 class="text-white fw-bold">Settings</h1>

      <!-- Settings Cards -->
      <div class="mt-4 d-flex flex-column gap-3">

        <!-- Currency Card -->
        <div class="d-flex justify-content-between align-items-center px-3 py-3 rounded-3"
          style="background-color: #F3FEF5;">
          <div>
            <div class="fw-bold text-dark">Currency</div>
            <div class="text-muted small">Philippine Peso (PHP)</div>
          </div>
          <button class="btn btn-sm fw-semibold px-3" style="background-color: #F6D25B; color: black;"
            onclick="openModal('currency')">Edit</button>
        </div>

        <!-- Language Card -->
        <div class="d-flex justify-content-between align-items-center px-3 py-3 rounded-3"
          style="background-color: #F3FEF5;">
          <div>
            <div class="fw-bold text-dark">Language</div>
            <div class="text-muted small">English</div>
          </div>
          <button class="btn btn-sm fw-semibold px-3" style="background-color: #F6D25B; color: black;"
            onclick="openModal('language')">Edit</button>
        </div>


        <!-- Currency Modal -->
        <div id="currencyModal"
          class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none justify-content-center align-items-center"
          style="z-index: 9999;">
          <div class="bg-green-custom p-4 rounded shadow" style="width: 300px;">
            <h5 class="fw-bold text-white mb-3">Edit Currency</h5>
            <select class="form-select mb-3">
              <option>Peso</option>
              <option>Dollar</option>
            </select>
            <button class="btn btn-warning w-100 fw-semibold" onclick="closeModal('currency')">Save</button>
          </div>
        </div>

        <!-- Language Modal -->
        <div id="languageModal"
          class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none justify-content-center align-items-center"
          style="z-index: 9999;">
          <div class="bg-green-custom p-4 rounded shadow" style="width: 300px;">
            <h5 class="fw-bold text-white mb-3">Edit Language</h5>
            <select class="form-select mb-3">
              <option>English</option>
              <option>Tagalog</option>
            </select>
            <button class="btn btn-warning w-100 fw-semibold" onclick="closeModal('language')">Save</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS for Offcanvas & other components -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Sidebar Script -->
  <script>
  
   
    function openModal(type) {
      document.getElementById(`${type}Modal`).classList.remove('d-none');
      document.getElementById(`${type}Modal`).classList.add('d-flex');
    }

    function closeModal(type) {
      document.getElementById(`${type}Modal`).classList.remove('d-flex');
      document.getElementById(`${type}Modal`).classList.add('d-none');
    }
  </script>
</body>

</html>