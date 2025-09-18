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
  <div class="bg-green-custom min-vh-100 p-3" style="background-color: #44B87D;">

    <div id="overlay" class="d-none"></div>

          <!-- Settings Page Content -->
      <div class="container py-4">
        <h1 class="text-white fw-bold">Settings</h1>

        <!-- Settings Cards -->
        <div class="mt-4 d-flex flex-column gap-3">

          <!-- Currency Card -->
          <div class="d-flex justify-content-between align-items-center px-3 py-3 rounded-3"
            style="background-color: #F0f1f6;">
            <div>
              <div class="fw-bold text-dark">Currency</div>
              <div class="text-muted small">Philippine Peso (PHP)</div>
            </div>
            <button class="btn btn-sm fw-semibold px-3" style="background-color: #F6D25B; color: black;"
              onclick="openModal('currency')">Edit</button>
          </div>

          <!-- Needs & Wants Card -->
          <div class="d-flex justify-content-between align-items-center px-3 py-3 rounded-3"
            style="background-color: #F0f1f6;">
            <div>
              <div class="fw-bold text-dark">Needs & Wants</div>
              <div class="text-muted small">Manage spending categories</div>
            </div>
            <button class="btn btn-sm fw-semibold px-3" style="background-color: #F6D25B; color: black;"
              onclick="openModal('needsWants')">Edit</button>
          </div>

          <!-- Budget Rule Card -->
          <div class="d-flex justify-content-between align-items-center px-3 py-3 rounded-3"
            style="background-color: #F0f1f6;">
            <div>
              <div class="fw-bold text-dark">Budget Rule</div>
              <div class="text-muted small">Change preferred budgeting method</div>
            </div>
            <button class="btn btn-sm fw-semibold px-3" style="background-color: #F6D25B; color: black;"
              onclick="openModal('budgetRule')">Edit</button>
          </div>
        </div>
      </div>

      <!-- Currency Modal -->
      <div id="currencyModal"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none justify-content-center align-items-center"
        style="z-index: 1;" onclick="overlayClose(event, 'currency')">
        <div class="bg-green-custom p-4 rounded shadow" style="width: 320px; background-color: #44B87D;" onclick="event.stopPropagation()">
          <h5 class="fw-bold text-white mb-3">Edit Currency</h5>
          <select class="form-select mb-3">
            <option>Peso</option>
            <option>Dollar</option>
          </select>
          <button class="btn btn-warning w-100 fw-semibold" onclick="closeModal('currency')">Save</button>
        </div>
      </div>

      <!-- Needs & Wants Modal -->
      <div id="needsWantsModal"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none justify-content-center align-items-center"
        style="z-index: 1;" onclick="overlayClose(event, 'needsWants')">
        <div class="bg-green-custom p-4 rounded shadow" style="width: 320px; background-color: #44B87D;" onclick="event.stopPropagation()">
          <h5 class="fw-bold text-white mb-3">Edit Needs & Wants</h5>

          <!-- Categorize Expenses -->
          <div class="p-2 rounded" style="background-color: #F0f1f6; border: 2px solid #F6D25B;">

            <!-- Header Row -->
            <div class="row fw-bold text-success text-center mb-2">
              <div class="col-6 text-start">Expense</div>
              <div class="col-3">Needs</div>
              <div class="col-3">Wants</div>
            </div>

            <!-- Expense Rows Wrapper -->
            <div id="categoryList">
              <!-- Expense Rows -->
              <div class="row align-items-center mb-2">
                <div class="col-6">Dining Out</div>
                <div class="col-3 text-center"><input type="checkbox" name="diningOut" onchange="selectOnlyOne(this)"></div>
                <div class="col-3 text-center"><input type="checkbox" name="diningOut" onchange="selectOnlyOne(this)"></div>
                <div class="col-12 text-end mt-1 d-none delete-btn">
                  <button class="btn btn-sm btn-danger" onclick="deleteCategory(this)">Delete</button>
                </div>
              </div>

              <div class="row align-items-center mb-2">
                <div class="col-6">Electricity</div>
                <div class="col-3 text-center"><input type="checkbox" name="electricity" onchange="selectOnlyOne(this)"></div>
                <div class="col-3 text-center"><input type="checkbox" name="electricity" onchange="selectOnlyOne(this)"></div>
                <div class="col-12 text-end mt-1 d-none delete-btn">
                  <button class="btn btn-sm btn-danger" onclick="deleteCategory(this)">Delete</button>
                </div>
              </div>

              <div class="row align-items-center mb-2">
                <div class="col-6">Groceries</div>
                <div class="col-3 text-center"><input type="checkbox" name="groceries" onchange="selectOnlyOne(this)"></div>
                <div class="col-3 text-center"><input type="checkbox" name="groceries" onchange="selectOnlyOne(this)"></div>
                <div class="col-12 text-end mt-1 d-none delete-btn">
                  <button class="btn btn-sm btn-danger" onclick="deleteCategory(this)">Delete</button>
                </div>
              </div>

              <div class="row align-items-center mb-2">
                <div class="col-6">Rent</div>
                <div class="col-3 text-center"><input type="checkbox" name="rent" onchange="selectOnlyOne(this)"></div>
                <div class="col-3 text-center"><input type="checkbox" name="rent" onchange="selectOnlyOne(this)"></div>
                <div class="col-12 text-end mt-1 d-none delete-btn">
                  <button class="btn btn-sm btn-danger" onclick="deleteCategory(this)">Delete</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Add Category Input (hidden by default) -->
          <div id="addCategoryRow" class="input-group mt-3 d-none">
            <input type="text" id="newCategory" class="form-control" placeholder="New Category">
            <button class="btn btn-success" onclick="addCategory()">Add</button>
          </div>

          <!-- Manage Button -->
          <button id="manageBtn" class="btn btn-outline-light w-100 fw-semibold mt-3" onclick="toggleManage()">Manage Categories</button>

          <!-- Save Button -->
          <button class="btn btn-warning w-100 fw-semibold mt-2" onclick="closeModal('needsWants')">Save</button>
        </div>
      </div>

      <!-- Budget Rule Modal No Function yet-->
      <div id="budgetRuleModal"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none justify-content-center align-items-center"
        style="z-index: 9999;" onclick="overlayClose(event, 'budgetRule')">
        <div class="bg-green-custom p-4 rounded shadow" style="width: 320px; background-color: #44B87D;" onclick="event.stopPropagation()">
          <h5 class="fw-bold text-white mb-3">Edit Budget Rule</h5>
          <!-- Blank module for now -->
          <div class="text-white"> content is place here</div>
          <button class="btn btn-warning w-100 fw-semibold mt-3" onclick="closeModal('budgetRule')">Save</button>
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

    function deleteCategory(btn) {
    btn.closest(".row").remove();

    }

    function deleteCategory(btn) {
    btn.closest(".row").remove();
    }

 function addCategory() {
  const categoryName = document.getElementById("newCategory").value.trim();
  if (categoryName === "") return;

  const categoryList = document.getElementById("categoryList");
  const newRow = document.createElement("div");
  newRow.className = "row align-items-center mb-2";
  newRow.innerHTML = `
    <div class="col-6">${categoryName}</div>
    <div class="col-3 text-center"><input type="checkbox" name="${categoryName}" onchange="selectOnlyOne(this)"></div>
    <div class="col-3 text-center"><input type="checkbox" name="${categoryName}" onchange="selectOnlyOne(this)"></div>
    <div class="col-12 text-end mt-1 d-none delete-btn">
      <button class="btn btn-sm btn-danger" onclick="deleteCategory(this)">Delete</button>
    </div>
  `;
  categoryList.appendChild(newRow);
  document.getElementById("newCategory").value = "";
}
  function toggleManage() {
    const deleteBtns = document.querySelectorAll(".delete-btn");
    const addCategoryRow = document.getElementById("addCategoryRow");
    const manageBtn = document.getElementById("manageBtn");

    let isManaging = !addCategoryRow.classList.contains("d-none");

    if (isManaging) {
      // Hide manage mode
      deleteBtns.forEach(btn => btn.classList.add("d-none"));
      addCategoryRow.classList.add("d-none");
      manageBtn.textContent = "Manage Categories";
    } else {
      // Show manage mode
      deleteBtns.forEach(btn => btn.classList.remove("d-none"));
      addCategoryRow.classList.remove("d-none");
      manageBtn.textContent = "Done Managing";
    }
  }
  </script>
</body>

</html>