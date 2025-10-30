<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History</title>
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

    body {
      font-family: "Roboto", sans-serif;
      background-color: #44B87D !important;
    }

    .mainHeader {
      position: sticky;
      background-color: #44B87D;
      padding: 20px 30px;
      color: #FFFFFF;
      font-family: "Poppins", sans-serif;

    }

    .mainHeader h2 {
      font-weight: 700;
    }

    .filter-buttons {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin: 0px 0 15px 0;
    }

    .filter-buttons button {
      background-color: #FFFFFF;
      border: none;
      border-radius: 20px;
      padding: 8px 20px;
      font-size: 16px;
      color: #44B87D;
      font-weight: 700;
    }

    .scrollable-container {
      height: 70dvh;
      overflow-y: auto;
      padding-bottom: 20px;
    }

    .entry {
      background-color: #FFFFFF;
      border-radius: 20px;
      width: 85%;
      padding: 10px;
      margin: 10px auto;
      display: flex;
      flex-direction: column;
      border: 2px solid #FFC107;
    }

    .entry .time {
      color: #666;
      font-size: 12px;
      align-self: flex-end;
      margin-bottom: 5px;
    }

    .entry .content {
      display: flex;
      justify-content: start;
      align-items: center;
      gap: 10px;
      margin-top: -25px;
      min-height: 50px;
    }

    .entry .icon img {
      width: 60px;
      height: 60px;
    }

    .entry .text {
      font-size: 16px;
      color: #000000;
    }

    .entry .amount {
      color: #FFD858;
      font-size: 16px;
      margin-left: auto;
      position: relative;
      top: 10px;
    }

    /* Modal */
    .modal.modal-bottom-sheet .modal-dialog {
      position: fixed;
      margin: 0;
      width: 100%;
      bottom: 0;
      left: 0;
      right: 0;
      transform: translateY(100%);
      transition: transform 0.4s ease-in-out;
    }

    .modal.modal-bottom-sheet.show .modal-dialog {
      transform: translateY(0);
    }

    .modal-bottom-sheet .modal-content {
      border-radius: 20px 20px 0 0;
      border: none;
      padding: 15px 0;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
    }

    .modal-backdrop.show {
      opacity: 0.3;
    }

    .modal.modal-bottom-sheet.show .modal-dialog {
      transform: translateY(0);
      transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
  </style>
</head>

<body>
  <?php include("../../assets/shared/navigationBar.php") ?>
  <?php include("../../assets/shared/sideBar.php") ?>

  <div class="mainHeader">
    <h2>History</h2>
  </div>

  <div class="filter-buttons">
    <button type="button" data-bs-toggle="modal" data-bs-target="#selectRangeModal">
      <i class="fa-solid fa-filter"></i> Last 30 Days
    </button>
    <button type="button" data-bs-toggle="modal" data-bs-target="#selectTypesModal">
      <i class="fa-solid fa-filter"></i> All Types
    </button>
  </div>

  <!-- Scrollable Entries -->
  <div class="scrollable-container">
    <div class="entry">
      <span class="time">1 hour ago</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Coffee</span>
        <span class="amount">-P150</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">2 hours ago</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Dining</span>
        <span class="amount">+P500</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">Yesterday</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Shopping</span>
        <span class="amount">-P1200</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">Yesterday</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Groceries</span>
        <span class="amount">-P2500</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">3 days ago</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Fuel</span>
        <span class="amount">-P800</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">5 days ago</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Utilities</span>
        <span class="amount">-P1800</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">1 week ago</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Salary</span>
        <span class="amount">+P15000</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">10 days ago</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Entertainment</span>
        <span class="amount">-P600</span>
      </div>
    </div>

    <div class="entry">
      <span class="time">15 days ago</span>
      <div class="content">
        <span class="icon">
          <img src="../../assets/img/shared/categories/expense/Coffee.png" alt="Coffee" class="img-fluid">
        </span>
        <span class="text">Phone Bill</span>
        <span class="amount">-P1000</span>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade modal-bottom-sheet" id="selectRangeModal" tabindex="-1"
      aria-labelledby="selectRangeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="selectRangeModalLabel">Select Range</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">Last 7 Days</li>
              <li class="list-group-item">Last 30 Days</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Types -->
    <div class="modal fade modal-bottom-sheet" id="selectTypesModal" tabindex="-1"
      aria-labelledby="selectTypesModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="selectTypesModalLabel">Select Types</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item">All Types</li>
              <li class="list-group-item">Savings</li>
              <li class="list-group-item">Income</li>
              <li class="list-group-item">Expenses</li>
              <li class="list-group-item">Challenges</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>