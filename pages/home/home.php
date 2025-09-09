<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/home.css">
  <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
</head>

<body>

  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>
  <!-- Sidebar content-->
  <?php include ("../../assets/shared/sideBar.php")?>

  <!-- Page Background -->
  <div class="bg-green-custom" style="min-height: 100vh; padding-bottom: 80px;">
  <div class="flex-grow-1 overflow-y-auto">

    <!-- Main Content -->
    <div class="container py-4">

      <!-- Expense, Income, Balance Summary Card -->
      <div class="summary-card d-flex justify-content-around align-items-center mx-auto mt-4">

        <!-- Expenses -->
        <div class="text-center">
          <div class="fw-semibold text-black d-flex align-items-center justify-content-center">
            <span style="color: #d60000; font-size: 1.2rem;" class="me-1">↓</span>
            <i class="bi bi-arrow-down-circle-fill text-danger me-1"></i>
            Expenses
          </div>
          <div class="fw-bold" style="color: #F6D25B;">₱2,700</div>
        </div>

        <!-- Divider -->
        <div class="vertical-divider"></div>

        <!-- Income -->
        <div class="text-center">
          <div class="fw-semibold text-black d-flex align-items-center justify-content-center">
            <span style="color: #44B87D; font-size: 1.2rem;" class="me-1">↑</span>
            <i class="bi bi-arrow-up-circle-fill text-success me-1"></i>
            Income
            <i class="bi bi-caret-up-fill text-success ms-1 fs-5"></i>
          </div>
          <div class="fw-bold" style="color: #F6D25B;">₱10,200</div>
        </div>

        <!-- Divider -->
        <div class="vertical-divider"></div>

        <!-- Balance -->
        <div class="text-center">
          <div class="fw-semibold text-black">Balance</div>
          <div class="fw-bold" style="color: #F6D25B;">₱7,500</div>
        </div>
      </div>

      <!-- Date -->
      <div class="today-text">Today May 07 Wed</div>

      <!-- Expense and Income Cards -->
      <div class="d-flex flex-column align-items-center gap-3 mt-4">

        <!-- Allowance Card -->
        <div class="mini-card d-flex justify-content-between align-items-center px-3 py-2 rounded shadow-sm"
           style="width: 100%; max-width: 335px; height: 84px; background-color: #F3FEF5;">
          <div class="d-flex align-items-center">
            <img src="../../assets/img/home/allowance.png" alt="Allowance" style="width: 40px; height: 40px;" class="me-3">
            <div>
              <div class="fw-semibold text-dark">Allowance</div>
              <div style="font-size: 0.85rem; color: #CCCCCC;">Note: Thanks mama</div>
            </div>
          </div>
          <div class="text-end">
            <div style="color: #F6D25B; font-weight: 600; font-size: 1.1rem;">+ ₱200</div>
            <div style="font-size: 0.85rem; color: #77D09A;">7:40 AM</div>
          </div>
        </div>

        <!-- Dining Out Card -->
        <div class="mini-card d-flex justify-content-between align-items-center px-3 py-2 rounded shadow-sm"
           style="width: 100%; max-width: 335px; height: 84px; background-color: #F3FEF5;">
          <div class="d-flex align-items-center">
            <img src="../../assets/img/home/dining-out.png" alt="Dining Out" style="width: 40px; height: 40px;" class="me-3">
            <div>
              <div class="fw-semibold text-dark">Dining Out</div>
              <div style="font-size: 0.85rem; color: #CCCCCC;">Note: Eat in Jollibee</div>
            </div>
          </div>
          <div class="text-end">
            <div style="color: #F6D25B; font-weight: 600; font-size: 1.1rem;">- ₱500</div>
            <div style="font-size: 0.85rem; color: #77D09A;">7:40 PM</div>
          </div>
        </div>

        <!-- Transportation Card -->
        <div class="mini-card d-flex justify-content-between align-items-center px-3 py-2 rounded shadow-sm"
           style="width: 100%; max-width: 335px; height: 84px; background-color: #F3FEF5;">
          <div class="d-flex align-items-center">
            <img src="../../assets/img/home/bus.png" alt="Transportation" style="width: 40px; height: 40px;" class="me-3">
            <div>
              <div class="fw-semibold text-dark">Transportation</div>
              <div style="font-size: 0.85rem; color: #CCCCCC;">Note: Pamasahe otw Manila</div>
            </div>
          </div>
          <div class="text-end">
            <div style="color: #F6D25B; font-weight: 600; font-size: 1.1rem;">- ₱2,000</div>
            <div style="font-size: 0.85rem; color: #77D09A;">9:50 PM</div>
          </div>
        </div>

      <!-- More Cards -->
      <div id="more-cards" class="d-flex flex-column align-items-center gap-3 mt-3 d-none">
        <div class="mini-card"></div>
        <div class="mini-card"></div>
        <div class="mini-card"></div>
      </div>

      <!-- See More Button -->
      <div class="d-flex justify-content-end mt-3">
        <button id="seeMoreBtn" class="btn btn-link text-white fw-semibold">See more</button>
      </div>

      <!-- Recommendation Card -->
<div class="d-flex justify-content-center mt-4">
  <div class="recommendation-card p-2 px-3 position-relative"
    style="background-color: #F3FEF5; border-radius: 10px; width: 315px;">
    
    <!-- Title -->
    <div class="fw-semibold mb-2 text-center" style="color: #44B87D;">Recommendation</div>
    
    <!-- Image (centered) -->
    <div class="d-flex justify-content-center">
      <img src="../../assets/img/home/InsiteBg.png" alt="Recommendation Image"
        style="width: 300px; height: 85px; border-radius: 10px; object-fit: cover;">
    </div>
  </div>
</div>

  <!-- Watch. Read. Apply. Save Smart Section -->
<div class="d-flex justify-content-center mt-4">
  <div class="challenge-card p-3" style="background-color: #F3FEF5; border-radius: 12px; width: 335px;">
    <h6 class="fw-semibold mb-3" style="color: #44B87D;">Watch. Read. Apply. Save Smart</h6>

    <!-- Video thumbnail -->
    <div class="position-relative mb-3">
      <img src="../../assets/img/home/videosample.png" alt="Video" class="img-fluid rounded" style="height: 180px; width: 100%; object-fit: cover;">
      <span class="position-absolute top-50 start-50 translate-middle text-white fs-1">&#9658;</span>
    </div>

    <!-- Text Buttons -->
    <button class="btn bg-white border w-100 mb-2 text-start fw-semibold" style="border-radius: 10px;">Simple ways to save money for the future</button>
    <button class="btn bg-white border w-100 text-start fw-semibold" style="border-radius: 10px;">28 Proven Ways to Save Money</button>

    <!-- See More -->
    <div class="text-end mt-2">
      <a href="#" class="text-success fw-semibold text-decoration-none">See More...</a>
    </div>
  </div>
</div>

    <!-- Daily and Weekly Saving Challenges -->
<div class="d-flex justify-content-center mt-3">
  <div class="challenge-card p-3" style="background-color: #F3FEF5; border-radius: 12px; width: 335px;">

    <!-- Daily Saving Challenge -->
    <h6 class="fw-semibold" style="color: #44B87D;">Daily Saving Challenge</h6>
    <div class="d-flex justify-content-between align-items-center bg-white px-3 py-2 rounded-pill shadow-sm mb-3" style="height: 45px;">
      <span class="fw-medium text-dark">Login to CtrlSave</span>
      <button class="btn btn-sm fw-bold" style="background-color: #F6D25B; border-radius: 20px; color: black;">Claim</button>
    </div>

    <!-- Weekly Saving Challenge -->
    <h6 class="fw-semibold" style="color: #44B87D;">Weekly Saving Challenge</h6>
    <div class="d-flex justify-content-between align-items-center bg-white px-3 py-2 rounded-pill shadow-sm" style="height: 45px;">
      <span class="fw-medium text-dark">Save 500 for the Week</span>
      <button class="btn btn-sm fw-bold" style="background-color: #F6D25B; border-radius: 20px; color: black;">Claim</button>
    </div>
     <div style="height: 80px;"></div>
    </div>
  </div>


  <!-- Bottom Tab Navigation -->
  <div class="tab-bar d-flex justify-content-around align-items-center position-fixed bottom-0 start-0 end-0 bg-white shadow" style="height: 65px; z-index: 999;">
    <div class="tab-item text-center" style="margin-top: -4px;">
      <img src="../../assets/img/home/calculator.png" alt="Calculator" class="tab-icon mb-1">
      <div class="tab-label fw-bold" style="font-size: 0.85rem;">Calculator</div>
    </div>
    <a href="../income&expenses/income&expenses.html" style="text-decoration: none;">
    <div class="tab-center d-flex justify-content-center align-items-center">
      <div class="tab-add-btn">
        <span class="text-white fs-3">+</span>
      </div>
    </div>
    </a>

    <div class="tab-item text-center" style="margin-top: -4px;">
      <img src="../../assets/img/home/calendar.png" alt="Calendar" class="tab-icon mb-1">
       <div class="tab-label fw-bold" style="font-size: 0.85rem;">Calendar</div>
    </div>
  </div>

  <!-- Bootstrap JS for Offcanvas & other components -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Scripts -->
  <script>
    fetch('sideBar.html')
      .then(res => res.text())
      .then(data => {
        document.getElementById('sidebarContainer').innerHTML = data;
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("sidebarToggle");
        const overlay = document.getElementById("overlay");

        toggleBtn.addEventListener("click", () => {
          sidebar.classList.toggle("show");
          overlay.classList.toggle("d-none");
          document.body.style.overflow = sidebar.classList.contains("show") ? "hidden" : "";
        });

        overlay.addEventListener("click", () => {
          sidebar.classList.remove("show");
          overlay.classList.add("d-none");
          document.body.style.overflow = "";
        });
      });

    document.addEventListener("DOMContentLoaded", function () {
      const seeMoreBtn = document.getElementById("seeMoreBtn");
      const moreCards = document.getElementById("more-cards");
      let expanded = false;

      seeMoreBtn.addEventListener("click", () => {
        expanded = !expanded;
        moreCards.classList.toggle("d-none", !expanded);
        seeMoreBtn.textContent = expanded ? "See less" : "See more";
      });
    });
  </script>

</body>

</html>
