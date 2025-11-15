<?php
session_start();
include '../../assets/shared/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['goalName'] = $_POST['goalName'];
    $_SESSION['goalIcon'] = $_POST['goalIcon'];
    header("Location: addSaving2.php");
    exit();
}

$icons = [
    "../../assets/img/shared/categories/expense/Savings.png",
    "../../assets/img/shared/categories/expense/Phone.png",
    "../../assets/img/shared/categories/expense/Travel.png",
    "../../assets/img/shared/categories/expense/Car.png",
    "../../assets/img/shared/categories/expense/Dining out.png",
    "../../assets/img/shared/categories/expense/Entertainment.png",
    "../../assets/img/shared/categories/expense/House.png",
    "../../assets/img/shared/categories/expense/Party.png",
    "../../assets/img/shared/categories/expense/Transportation.png",
    "../../assets/img/shared/categories/expense/Gift.png",
    "../../assets/img/shared/categories/expense/Clothes.png",
    "../../assets/img/shared/categories/expense/Tuition.png",
    "../../assets/img/shared/categories/expense/School Needs.png",
    "../../assets/img/shared/categories/expense/Internet Connection.png",
    "../../assets/img/shared/categories/expense/Shopping.png"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Saving Goal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
  <style>
    .progress-line { height: 4px; background-color: #F6D25B; width: 50%; animation: fillToFull 1s ease-in-out forwards; }
    @keyframes fillToFull { from { width: 0%; } to { width: 50%; } }
    .icon-option.selected { box-shadow: 0 0 15px rgba(7,78,45,0.61); background-color: #F0f1f6; }
    .icon-option { cursor: pointer; transition: all 0.3s ease; }
    .icon-option:hover { transform: scale(1.05); }
    .icon-list::-webkit-scrollbar { width: 0px; background: transparent; }
    .icon-list { scrollbar-width: none; -ms-overflow-style: none; }
    .icon-list::-webkit-scrollbar-thumb { background: transparent; }
  </style>
</head>
<body class="m-0 overflow-hidden" style="background-color: #44B87D; height: 100vh;">

<!-- Navbar -->
<nav class="bg-white px-4 py-4 d-flex align-items-center shadow sticky-top" style="height: 72px;">
  <a href="savingGoal.php">
    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;">
  </a>
  <h5 class="m-0 fw-bold text-dark flex-grow-1 text-center" style="transform: translateX(-15px);">Add Goal</h5>
</nav>

<!-- Main Content -->
<form method="POST" class="d-flex flex-column justify-content-between" style="height: calc(100vh - 72px); overflow: hidden;">
  <div class="px-4 py-3" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
    <p class="fw-bold text-white fs-5 mb-2">What’s your saving goal?</p>
    <input type="text" name="goalName" id="goalNameInput" class="form-control mb-4 rounded-3" placeholder="e.g. House"
           style="height: 50px; font-size: 16px;" required>

    <p class="fw-bold text-white fs-5 mb-4">Pick an icon for your goal</p>
    <input type="hidden" name="goalIcon" id="goalIconInput">
    <div class="icon-list row row-cols-3 g-3 flex-grow-1 overflow-auto" style="max-height: 100%; padding-bottom: 120px;">
      <?php foreach($icons as $icon): ?>
        <div class="col text-center">
          <div class="icon-option border rounded-circle p-2 bg-white mx-auto" style="width: 80px; height: 80px; cursor: pointer;">
            <img src="<?= $icon ?>" style="width: 100%; height: 100%; object-fit: contain;">
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Fixed Footer -->
  <div class="fixed-footer" style="background: white; position: fixed; bottom: 0; width: 100%;">
    <div class="progress-line"></div>
    <div class="p-3">
      <button type="submit" id="continueBtn"
         class="btn w-100 fw-semibold d-flex justify-content-center align-items-center"
         style="background-color: #F6D25B; border-radius: 999px; height: 50px; font-size: 16px;">
        Continue
      </button>
    </div>
  </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const icons = document.querySelectorAll(".icon-option");
  const goalIconInput = document.getElementById("goalIconInput");

    icons.forEach(icon => {
      icon.addEventListener("click", () => {
          icons.forEach(i => i.classList.remove("selected"));
          icon.classList.add("selected");
          const src = icon.querySelector("img").getAttribute("src"); // relative path
          goalIconInput.value = src; // store relative path
      });
  });
</script>
</body>
</html>
