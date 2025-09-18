
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Saving Goal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
  <style>
   
    .progress-line {
      height: 4px;
      background-color: #F6D25B;
      width: 50%;
      animation: fillToFull 1s ease-in-out forwards;
    }

    @keyframes fillToFull {
      from {
        width: 0%;
      }

      to {
        width: 50%;
      }
    }

  .icon-option.selected {
    box-shadow: 0 0 15px rgba(7, 78, 45, 0.61);
    background-color: #F0f1f6;
  }

  .icon-option {
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .icon-option:hover {
    transform: scale(1.05);
  }

  </style>
</head>

<body class="m-0 overflow-hidden" style="background-color: #44B87D; height: 100vh;">
  <!-- Navbar -->
  <nav class="bg-white px-4 d-flex align-items-center justify-content-between position-relative"
    style="height: 72px;">
     <a href="saving1.php" class="text-decoration-none">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
        </a>
    <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark">
      Add Goal
    </h5>
  </nav>

  <!-- Main Content -->
  <div class="d-flex flex-column justify-content-between" style="height: calc(100vh - 72px);">
    <div class="px-4 py-3 overflow-auto">
      <p class="fw-bold text-white fs-5 mb-2">What’s your saving goal?</p>
      <input type="text" class="form-control mb-4 rounded-3" placeholder="e.g. House"
        style="height: 50px; font-size: 16px;">

      <p class="fw-bold text-white fs-5 mb-3">Pick an icon for your goal</p>

      <div class="row row-cols-3 g-3 icon-container">
        <div class="col text-center">
         <div class="icon-option border rounded-circle p-2 bg-white mx-auto" 
            style="width: 80px; height: 80px; cursor: pointer;" 
            data-bs-toggle="modal" data-bs-target="#uploadModal">
          <img src="../../assets/img/savings/uploadIcon.png" alt="Upload" 
              style="width: 50%; object-fit: contain; margin-top: 20px;">
        </div>
        </div>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;">
            <img src="../../assets/img/shared/categories/expense/Car.png" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;">
            <img src="../../assets/img/shared/categories/income/Money.png" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;">
            <img src="../../assets/img/shared/categories/expense/Rent.png" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;">
            <img src="../../assets/img/shared/categories/expense/Gift.png" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;">
            <img src="../../assets/img/shared/categories/expense/Clothes.png" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;">
            <img src="../../assets/img/shared/categories/expense/Shopping.png" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px;">
            <img src="../../assets/img/shared/categories/Savings.png" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
      </div>
    </div>

    <div class="fixed-footer" style="background: white; position: fixed; bottom: 0; width: 100%;">
    <div class="progress-line"></div>
    <div class="p-3">
      <a href="addSaving2.php" id="continueBtn"
        class="btn w-100 fw-semibold d-flex justify-content-center align-items-center"
        style="background-color: #F6D25B; border-radius: 999px; height: 50px; font-size: 16px; pointer-events: none; opacity: 0.6;">
        Continue
      </a>
    </div>
  </div>
  </div>
   <!-- upload modal Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel"
     aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content rounded-4" style="background-color: #44B87D;">

      <!-- Modal Header -->
      <div class="modal-header bg-white">
        <h5 class="modal-title fw-bold mx-auto" id="uploadModalLabel">Upload Icon</h5>
      </div>
      
      <!-- Modal Body -->
      <div class="modal-body text-center text-white" style="font-size: 1.1rem;">
        <p>Select an image to use as your custom goal icon.</p>
        <input type="file" class="form-control" accept="image/*">
      </div>
      
      <!-- Modal Footer -->
      <div class="modal-footer bg-white rounded-bottom justify-content-center">
        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success px-4 rounded-pill">Upload</button>
      </div>
    </div>
  </div>
</div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
   // selecting category funtion and and cant press continue
   const icons = document.querySelectorAll(".icon-option");
    const continueBtn = document.getElementById("continueBtn");

    icons.forEach(function(icon) {
      icon.addEventListener("click", function() {
        icons.forEach(function(i) {
          i.classList.remove("selected");
        });
        icon.classList.add("selected");
        continueBtn.style.pointerEvents = "auto";
        continueBtn.style.opacity = "1";
      });
    });
</script>
</body>

</html>