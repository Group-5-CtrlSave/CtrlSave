<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/home.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/income&expenses.css">

</head>

<body>

  <!-- Navigation Bar -->
  <?php include ("../../assets/shared/navigationBar.php") ?>
  <!-- Sidebar content-->
  <?php include ("../../assets/shared/sideBar.php")?>

  <!-- Page Background -->
  <div style="min-height: 100vh; padding-bottom: 80px; background-color: #44B87D;">
  <div class="flex-grow-1 overflow-y-auto">

    <!-- Main Content -->
    <div class="container" style="margin-top: 120px; padding-bottom: 80px;">


      <!-- Summary Card -->
<div style="
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 160px; /* adjust height kung gusto mo mas mataas */
  background-color: #44B87D; /* same color as top background */
  z-index: 998;">
</div>

<!-- Expense, Income, Balance Summary Card -->
<div class="summary-card d-flex justify-content-around align-items-center mx-auto py-2 position-fixed top-0 start-50 translate-middle-x shadow"
     style="width: 90%; background-color: #F0F1F6; border-radius: 20px; margin-top: 80px; z-index: 1000;">
     
  <!-- Expenses -->
  <div class="summary-item text-center">
    <div class="fw-semibold text-black d-flex flex-column align-items-center">
      <div class="d-flex align-items-center justify-content-center mb-1">
        <span style="color: #d60000; font-size: 1rem;" class="me-1">↓</span>
        <i class="bi bi-arrow-down-circle-fill text-danger me-1"></i>
        Expenses
      </div>
      <div class="fw-bold" style="color: #F6D25B;">₱2,700</div>
    </div>
  </div>

  <!-- Divider -->
  <div class="vertical-divider"></div>

  <!-- Income -->
  <div class="summary-item text-center">
    <div class="fw-semibold text-black d-flex flex-column align-items-center">
      <div class="d-flex align-items-center justify-content-center mb-1">
        <span style="color: #44B87D; font-size: 1rem;" class="me-1">↑</span>
        <i class="bi bi-arrow-up-circle-fill text-success me-1"></i>
        Income
      </div>
      <div class="fw-bold" style="color: #F6D25B;">₱10,200</div>
    </div>
  </div>

  <!-- Divider -->
  <div class="vertical-divider"></div>

  <!-- Balance -->
  <div class="summary-item text-center">
    <div class="fw-semibold text-black mb-1">Balance</div>
    <div class="fw-bold" style="color: #F6D25B;">₱7,500</div>
  </div>
</div>

      <!-- Date -->
      <div class="today-text">Today May 07 Wed</div>

<!-- Income and Expense Row (styled like Income & Expenses page) -->
<div class="scrollable-container mt-4">
  <div class="row justify-content-center">

    <!-- Allowance (Mama) -->
    <div class="col-12 col-md-8">
      <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
        <div class="container categoryImgContainer p-1">
          <img class="img-fluid" src="../../assets/img/shared/categories/income/Allowance.png">
        </div>
        <div class="container categoryTextContainer p-1">
          <p class="category m-0"><b>Allowance</b></p>
          <p class="notes m-0">Notes: Bigay ni Mama</p>
        </div>
        <div class="container iePriceContainer p-1">
          <h5 class="price m-0">+ ₱4,000</h5>
          <p class="time m-0"><b>12:51 PM</b></p>
        </div>
      </div>
    </div>

    <!-- Dining Out -->
    <div class="col-12 col-md-8">
      <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
        <div class="container categoryImgContainer p-1">
          <img class="img-fluid" src="../../assets/img/shared/categories/expense/Dining Out.png">
        </div>
        <div class="container categoryTextContainer p-1">
          <p class="category m-0"><b>Dining Out</b></p>
          <p class="notes m-0">Notes: Jollibee</p>
        </div>
        <div class="container iePriceContainer p-1">
          <h5 class="price m-0">- ₱300</h5>
          <p class="time m-0"><b>6:40 PM</b></p>
        </div>
      </div>
    </div>

    <!-- Transportation -->
    <div class="col-12 col-md-8">
      <div class="container-fluid ieContainer d-flex justify-content-center align-items-center my-2">
        <div class="container categoryImgContainer p-1">
          <img class="img-fluid" src="../../assets/img/shared/categories/expense/Transportation.png">
        </div>
        <div class="container categoryTextContainer p-1">
          <p class="category m-0"><b>Transportation</b></p>
          <p class="notes m-0">Notes: Pamasahe otw Manila</p>
        </div>
        <div class="container iePriceContainer p-1">
          <h5 class="price m-0">- ₱2,000</h5>
          <p class="time m-0"><b>9:50 PM</b></p>
        </div>
      </div>
    </div>

    <!-- ✅ Move See More inside -->
    <div class="text-end" style="margin-top: -5px;">
      <a href="../income&expenses/income&expenses.php" class="btn btn-link text-white fw-semibold p-0"
        style="font-size: 0.9rem;">See more</a>
    </div>

  </div>
</div>

<!-- Recommendation Card -->
<div class="d-flex justify-content-center position-relative" style="margin-top: -80px !important;">
  <div class="recommendation-card p-2 px-3 position-relative"
    style="background-color: #F0F1F6; border-radius: 20px; width: 335px;">
    
    <!-- Title -->
    <h2 class="fw-semibold mb-2 text-start" style="color: #44B87D;">Recommendation</h2>

    <!-- Image (centered) -->
    <div class="d-flex justify-content-center">
      <img src="../../assets/img/home/InsiteBg.png" alt="Recommendation Image"
        style="width: 300px; height: 85px; border-radius: 20px; object-fit: cover;">
    </div>
  </div>
</div>


  <!-- Watch. Read. Apply. Save Smart Section -->
<div class="d-flex justify-content-center mt-4">
  <div class="challenge-card p-3" style="background-color: #F3FEF5; border-radius: 20px; width: 335px;">
      <h2 class="fw-semibold mb-3" style="color: #44B87D;">Watch. Read. Apply. Save Smart</h2>


    <!-- Video thumbnail -->
    <div class="position-relative mb-3">
      <img src="../../assets/img/home/videosample.png" alt="Video" class="img-fluid rounded" style="height: 180px; width: 100%; object-fit: cover;">
      <span class="position-absolute top-50 start-50 translate-middle text-white fs-1">&#9658;</span>
    </div>

    <!-- Text Buttons -->
    <button class="btn bg-white border w-100 mb-2 text-start fw-semibold" style="border-radius: 20px;">Simple ways to save money for the future</button>
    <button class="btn bg-white border w-100 text-start fw-semibold" style="border-radius: 20px;">28 Proven Ways to Save Money</button>

    <!-- See More -->
    <div class="text-end mt-2">
      <a href="#" class="text-success fw-semibold text-decoration-none">See More...</a>
    </div>
  </div>
</div>


<!-- Daily Saving Challenge -->
<div class="d-flex justify-content-center mt-3">
  <div class="challenge-card p-3" 
       style="background-color: #F3FEF5; border-radius: 20px; width: 325px;"> 

      <h2 class="fw-semibold" style="color: #44B87D;">Daily Saving Challenge</h2>
    <div class="d-flex justify-content-between align-items-center bg-white px-3 py-2 rounded-pill shadow-sm mb-2" style="height: 45px;">
      <span class="fw-medium text-dark">Login to CtrlSave</span>
      <button class="btn btn-sm fw-bold" style="background-color: #F6D25B; border-radius: 20px; color: black;">Claim</button>
    </div>

    <!-- Show More -->
    <div class="d-flex justify-content-end mt-2">
      <a href="../challenge/challengeMain.html" 
         class="btn btn-link fw-semibold" 
         style="color: #44B87D; text-decoration: none; font-size: 0.9rem;">
        Show more
      </a>
    </div>

  </div>
</div>



  <!-- Bottom Tab Navigation -->
  <div class="tab-bar d-flex justify-content-around align-items-center position-fixed bottom-0 start-0 end-0 bg-white shadow" style="height: 65px; z-index: 999;">
  
  <!-- Cointrol -->
  <div class="tab-item text-center" style="margin-top: -10px;"> 
    <img src="../../assets/img/home/cointrol_Icon.png" 
         alt="Cointrol" 
         class="tab-icon mb-1"
         style="width: 36px; height: 36px;">
    <div class="tab-label fw-bold" style="font-size: 0.9rem;">Cointrol</div>
  </div>

  <!-- Add Button -->
  <a href="../income&expenses/income&expenses.php" style="text-decoration: none;">
    <div class="tab-center d-flex justify-content-center align-items-center">
      <div class="tab-add-btn">
        <span class="text-white" style="font-size: 3rem;">+</span>
      </div>
    </div>
  </a>

  <!-- Calculator -->
  <a href="../home/calculator.php" 
     class="tab-item text-center" 
     style="margin-top: -10px; text-decoration: none; color: inherit; flex: 1;">
    <img src="../../assets/img/home/calculator.png" 
         alt="Calculator" 
         class="tab-icon mb-1"
         style="width: 36px; height: 36px; object-fit: contain;"> 
  <div class="tab-label fw-bold" style="font-size: 0.9rem;">Calculator</div>
</a>

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
