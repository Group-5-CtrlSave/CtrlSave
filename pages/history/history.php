<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History</title>
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- Bootstrap Icons CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      font-family: "Roboto", sans-serif;
      background-color: #44B87D !important;
    }

    .mainHeader {
      position: sticky;
      background-color: #44B87D;
      padding: 20px 30px;
      color: #FFFFFF;
    }

    .mainHeader h2 {
      font-size: 27px;
      font-weight: 600;
    }

    .scrollable-container {
      height: 75dvh;
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
      font-size: 15px;
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
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .entry .text {
      font-size: 20px;
      color: #333;
    }

    .entry .amount {
      color: #FFD858;
      font-size: 20px;
      margin-left: auto;
      position: relative;
      top: 10px;
    }
  </style>
</head>

<body>
  <?php include("../../assets/shared/navigationBar.php") ?>
  <?php include("../../assets/shared/sideBar.php") ?>

  <!-- Sticky Page Header -->
  <div class="mainHeader">
    <h2>History</h2>
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

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>