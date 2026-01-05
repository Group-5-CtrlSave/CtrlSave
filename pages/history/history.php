<?php
session_start();
include("../../assets/shared/connect.php");
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['userID'])) {
  header("Location: ../../pages/login&signup/login.php");
  exit;
}

$userID = $_SESSION['userID'];

$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$currencySymbol = ($currencyCode === 'USD') ? '$' : '₱';


// filters
$daysFilter = isset($_GET['days']) ? intval($_GET['days']) : 30;
$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';

// query for income, expenses, savings
$query = "
  SELECT 'expenses' AS type, uc.categoryName AS name, e.amount AS amount, e.dateAdded AS date, uc.type AS categoryType, uc.icon AS icon
  FROM tbl_expense e
  JOIN tbl_usercategories uc ON e.userCategoryID = uc.userCategoryID
  WHERE e.userID = '$userID'

  UNION ALL

  SELECT 'income' AS type, uc.categoryName AS name, i.amount AS amount, i.dateReceived AS date, uc.type AS categoryType, uc.icon AS icon
  FROM tbl_income i
  JOIN tbl_usercategories uc ON i.userCategoryID = uc.userCategoryID
  WHERE i.userID = '$userID'

  UNION ALL

  SELECT 'savings' AS type, sg.goalName AS name, gt.amount AS amount, gt.date AS date, 'savings' AS categoryType, sg.icon AS icon
  FROM tbl_goaltransactions gt
  JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
  WHERE sg.userID = '$userID'

  UNION ALL

  SELECT 'savings' AS type, sg.goalName AS name, sg.currentAmount AS amount, sg.createdAt AS date, 'savings' AS categoryType, sg.icon AS icon
  FROM tbl_savinggoals sg
  LEFT JOIN tbl_goaltransactions gt ON sg.savingGoalID = gt.savingGoalID
  WHERE sg.userID = '$userID' AND gt.goalTransactionID IS NULL
";

$query = "SELECT * FROM ($query) AS all_data";

// filters
$filterParts = [];
if ($daysFilter > 0) {
  $filterParts[] = "all_data.date >= DATE_SUB(NOW(), INTERVAL $daysFilter DAY)";
}
if ($typeFilter !== 'all') {
  $filterParts[] = "all_data.type = '$typeFilter'";
}

if (!empty($filterParts)) {
  $query .= " WHERE " . implode(" AND ", $filterParts);
}

// Desc order
$query .= " ORDER BY all_data.date DESC";

// execute query using mysqli
$result = mysqli_query($conn, $query);
if (!$result) {
  die("Query failed: " . mysqli_error($conn));
}

// function to format time ago
function formatTimeAgo($datetime)
{
  $givenTime = strtotime($datetime);
  $currentTime = time();
  $timeDifference = $currentTime - $givenTime;

  if ($timeDifference < 60)
    return 'Just now';
  elseif ($timeDifference < 3600) {
    $minutes = floor($timeDifference / 60);
    return $minutes . " " . ($minutes > 1 ? 'mins' : 'min') . " ago";
  } elseif ($timeDifference < 86400) {
    $hours = floor($timeDifference / 3600);
    return $hours . " " . ($hours > 1 ? 'hours' : 'hour') . " ago";
  } else {
    return date('F d, Y', $givenTime);
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>CtrlSave | History</title>
  <link rel="stylesheet" href="../../assets/css/history.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
    rel="stylesheet">

</head>

<body>
  <?php include("../../assets/shared/navigationBar.php") ?>
  <?php include("../../assets/shared/sideBar.php") ?>

  <div class="mainHeader">
    <h2 style="font-weight: bold !important;">
      History
    </h2>
  </div>


  <div class="filter-buttons">
    <button type="button" data-bs-toggle="modal" data-bs-target="#selectRangeModal" id="rangeBtn">
      <i class="fa-solid fa-filter"></i>
      <?php echo $daysFilter == 7 ? 'Last 7 Days' : 'Last 30 Days'; ?>
    </button>
    <button type="button" data-bs-toggle="modal" data-bs-target="#selectTypesModal" id="typeBtn">
      <i class="fa-solid fa-filter"></i>
      <?php echo ($typeFilter == 'all') ? 'All Types' : ucfirst($typeFilter); ?>
    </button>
  </div>

  <div class="scrollable-container">
    <?php if (mysqli_num_rows(result: $result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php
        $basePath = "../../assets/img/shared/categories/";
        if ($row['categoryType'] == 'income') {
          $iconPath = $basePath . "income/" . $row['icon'];
        } elseif ($row['categoryType'] == 'expense') {
          $iconPath = $basePath . "expense/" . $row['icon'];
        } else { // savings
          $iconPath = $basePath . "expense/" . $row['icon'];
        }
        ?>
        <div class="entry">
          <span class="time"><?php echo formatTimeAgo($row['date']); ?></span>
          <div class="content">
            <span class="icon">
              <img src="<?php echo $iconPath; ?>" alt="<?php echo $row['categoryType']; ?>" class="img-fluid">
            </span>
            <span class="text"><?php echo ($row['name']); ?></span>
            <span class="amount" style="color: <?= $row['type'] == 'expenses' ? '#FF5C5C' : '#44B87D' ?>">
              <?php echo ($row['type'] == 'expenses' ? '-' : '+') . $currencySymbol . number_format($row['amount'], 2); ?>
            </span>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="text-center text-white mt-5">
        <h5 style="font-size:16px;font-family: Roboto, sans-serif;">Your complete transaction history (Income, Expenses,
          and Savings Goals) will appear here.</h5>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal -->
  <div class="modal fade modal-bottom-sheet" id="selectRangeModal" tabindex="-1" aria-labelledby="selectRangeModalLabel"
    aria-hidden="true">
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
  <div class="modal fade modal-bottom-sheet" id="selectTypesModal" tabindex="-1" aria-labelledby="selectTypesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="selectTypesModalLabel">Select Types</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">All Types</li>
            <li class="list-group-item">Income</li>
            <li class="list-group-item">Expenses</li>
            <li class="list-group-item">Savings</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>

    // filter modal function js
    function applyFilter(name, value) {
      const currentUrl = new URL(window.location.href);
      currentUrl.searchParams.set(name, value);
      window.location.href = currentUrl.toString();
    }

    // date filter
    const dateItems = document.querySelectorAll('#selectRangeModal .list-group-item');
    dateItems.forEach(item => {
      item.addEventListener('click', () => {
        const selectedText = item.textContent.trim();
        let days = 30; // def
        if (selectedText.includes('7')) {
          days = 7;
        }
        applyFilter('days', days);
      });
    });

    // type filter
    const typeItems = document.querySelectorAll('#selectTypesModal .list-group-item');
    typeItems.forEach(item => {
      item.addEventListener('click', () => {
        const selectedText = item.textContent.trim().toLowerCase();
        let type = 'all'; // def

        if (selectedText.includes('income')) type = 'income';
        else if (selectedText.includes('expense')) type = 'expenses';
        else if (selectedText.includes('saving')) type = 'savings';

        applyFilter('type', type);
      });
    });
  </script>

  <script>
    // Push a fake history state so back swipe hits this first
    history.pushState(null, "", location.href);

    // Handle back swipe / back button
    window.addEventListener("popstate", function (event) {
      // Redirect to home page
      location.replace("/pages/home/home.php"); // use replace to avoid stacking history
    });
  </script>
</body>

</html>