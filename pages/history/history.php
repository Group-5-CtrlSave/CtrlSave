<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <!-- Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #44B87D;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            font-family: "Roboto", Arial, sans-serif;
             padding-top: 72px;
        }

        .header {
            text-align: left;
            color: white;
            padding: 30px;
            width: 80%;
            max-width: 800px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header .days {
            margin: 5px 0;
            font-size: 14px;
        }

        .entry {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            width: 80%;
            max-width: 800px;
            padding: 10px;
            margin: 10px auto;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 2px solid #FFC107;
        }

        .entry .time {
            color: #666;
            font-size: 10px;
            align-self: flex-end;
            margin-bottom: 5px;
        }

        .entry .content {
            display: flex;
            justify-content: start;
            align-items: start;
            gap: 10px; /* space between icon, text, and amount */
            margin-top: -10px;
        }

        .entry .icon {
            font-size: 20px;
            color: #44B87D;
            position: relative;
            top: -5px;
        }

        .entry .text {
            font-size: 16px;
            color: #333;
        }

        .entry .amount {
            color: #FFD858;
            font-size: 16px;
            margin-left: auto;
            position: relative;
            top: 10px; 
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
     <nav class="bg-white px-4 py-3 d-flex justify-content-between align-items-center shadow" style="height: 72px; position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;">
     <button class="fs-2 d-md-none text-success border-0 bg-transparent" data-bs-toggle="offcanvas"
      data-bs-target="#sidebar" aria-controls="sidebar">☰</button>
    <div class="d-flex align-items-center gap-2">
       <img src="../../assets/img/shared/logo_L.png" style="width: 40px;" alt="App Icon" />
    </div>
  </nav>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
  <div class="offcanvas-body p-0" style="background-color: #77D09A;">
    <!-- Side bar content -->
    <div class="p-4 border-bottom bg-light shadow-sm">
      <div class="d-flex align-items-center gap-3 mb-2">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold"
             style="width: 48px; height: 48px; font-size: 1.25rem;">JD</div>
        <div>
          <p class="text-dark fw-semibold mb-0">John Doe</p>
          <p class="text-muted small mb-0">john@example.com</p>
        </div>
      </div>
      <p class="small text-dark fw-medium mb-1">Level 1</p>
      <div class="w-100 bg-secondary bg-opacity-25 rounded-pill" style="height: 8px;">
        <div class="bg-warning rounded-pill" style="width: 30%; height: 8px;"></div>
      </div>
      <p class="text-muted mt-1 mb-3 small">30 XP / 100 XP</p>
      <button onclick="location.href='../login&signup/login.php'" class="w-100 btn btn-sm btn-danger fw-medium">Logout</button>
    </div>

    <ul class="list-unstyled m-0 p-3">
      <li>
        <a href="../../pages/home/home.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/Home_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Home</span>
        </a>
      </li>

      <li>
        <a href="../../pages/income&expenses/income&expenses.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/I&E_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Income & Expense</span>
        </a>
      </li>

      <li>
        <a href="../../pages/savings/saving1.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/Savings_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Savings</span>
        </a>
      </li>

      <li>
        <a href="../../pages/cointrol/cointrol.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/Cointrol_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Cointrol</span>
        </a>
      </li>

      <li>
        <a href="../../pages/savingstrategies/savingstrat.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/SavingStrat_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Saving Strategies</span>
        </a>
      </li>

      <li>
        <a href="../../pages/history/history.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/History_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>History</span>
        </a>
      </li>

      <li>
        <a href="../../pages/notification/notification.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/Notif_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Notifications</span>
        </a>
      </li>

      <li>
        <a href="../../pages/challenge/challengeMain.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/Challenge_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Challenge</span>
        </a>
      </li>

      <li>
        <a href="../../pages/profile/profile.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/Profile_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Profile</span>
        </a>
      </li>

      <li>
        <a href="../../pages/settings/settings.php"
           class="d-flex align-items-center text-white fw-bold gap-2 p-2 text-decoration-none rounded"
           style="display:block; transition: background-color 0.25s, color 0.25s;"
           onmouseover="this.style.backgroundColor='#44B87D'; this.style.color='white';"
           onmouseout="this.style.backgroundColor=''; this.style.color='white';">
          <img src="../../assets/img/shared/sidebar/Settings_SB.png" class="me-2" style="width: 30px; height: 30px;" />
          <span>Settings</span>
        </a>
      </li>
    </ul>
  </div>
</div>

    <div class="header">
    <h1><strong>History</strong></h1>
    </div>
    <div class="entry">
        <span class="time">1 hour ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-cup-hot"></i></span>
            <span class="text">Coffee</span>
            <span class="amount">-P150</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">2 hours ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-egg-fried"></i></span>
            <span class="text">Dining</span>
            <span class="amount">+P500</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">Yesterday</span>
        <div class="content">
            <span class="icon"><i class="bi bi-cart"></i></span>
            <span class="text">Shopping</span>
            <span class="amount">-P1200</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">Yesterday</span>
        <div class="content">
            <span class="icon"><i class="bi bi-basket"></i></span>
            <span class="text">Groceries</span>
            <span class="amount">-P2500</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">3 days ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-fuel-pump"></i></span>
            <span class="text">Fuel</span>
            <span class="amount">-P800</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">5 days ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-lightbulb"></i></span>
            <span class="text">Utilities</span>
            <span class="amount">-P1800</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">1 week ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-cash-stack"></i></span>
            <span class="text">Salary</span>
            <span class="amount">+P15000</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">10 days ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-film"></i></span>
            <span class="text">Entertainment</span>
            <span class="amount">-P600</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">15 days ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-phone"></i></span>
            <span class="text">Phone Bill</span>
            <span class="amount">-P1000</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">20 days ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-wallet2"></i></span>
            <span class="text">Freelance Work</span>
            <span class="amount">+P5000</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">25 days ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-house-door"></i></span>
            <span class="text">Rent</span>
            <span class="amount">-P8000</span>
        </div>
    </div>
    <div class="entry">
        <span class="time">30 days ago</span>
        <div class="content">
            <span class="icon"><i class="bi bi-gift"></i></span>
            <span class="text">Gift</span>
            <span class="amount">-P500</span>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
</body>

</html>