<?php
include("../../assets/shared/connect.php");
date_default_timezone_set('Asia/Manila');

// filters (intval = dbl check if legit number )
$daysFilter = isset($_GET['days']) ? intval($_GET['days']) : 30;
$typeFilter = isset($_GET['type']) ? $_GET['type'] : 'all';

// query for income, expenses, savings
$query = "
  SELECT 
    'expenses' AS type,
    uc.categoryName AS name,
    e.amount AS amount,
    e.dateSpent AS date,
    uc.type AS categoryType,
    uc.icon AS icon
  FROM tbl_expense e
  JOIN tbl_usercategories uc ON e.userCategoryID = uc.userCategoryID

  UNION ALL

  SELECT 
    'income' AS type,
    uc.categoryName AS name,
    i.amount AS amount,
    i.dateReceived AS date,
    uc.type AS categoryType,
    uc.icon AS icon
  FROM tbl_income i
  JOIN tbl_usercategories uc ON i.userCategoryID = uc.userCategoryID

  UNION ALL

  SELECT 
    'savings' AS type,
    sg.goalName AS name,
    gt.amount AS amount,
    gt.date AS date,
    'savings' AS categoryType,
    sg.icon AS icon
  FROM tbl_goaltransactions gt
  JOIN tbl_savinggoals sg ON gt.savingGoalID = sg.savingGoalID
";

$query = "SELECT * FROM ($query) AS all_data";

// filters
$filterParts = [];

// Days 
if (isset($_GET['days']) && intval($_GET['days']) > 0) {
  $daysFilter = intval($_GET['days']);
  $filterParts[] = "all_data.date >= DATE_SUB(NOW(), INTERVAL $daysFilter DAY)";
}

// Type 
if (isset($_GET['type']) && $_GET['type'] !== 'all') {
  $typeFilter = strtolower($_GET['type']);
  $filterParts[] = "all_data.type = '$typeFilter'";
}

if (!empty($filterParts)) {
  $query .= " WHERE " . implode(" AND ", $filterParts);
}

// Desc order
$query = $query . " ORDER BY all_data.date DESC";
$result = executeQuery($query);

function formatTimeAgo($datetime)
{
  $givenTime = strtotime($datetime);
  $currentTime = time();
  $timeDifference = $currentTime - $givenTime;
  // If less than 60 secs
  if ($timeDifference < 60) {
    return 'Just now';
  }
  // If less than 1 hour ago
  elseif ($timeDifference < 3600) {
    $minutes = floor($timeDifference / 60);
    $label = $minutes > 1 ? 'mins' : 'min';
    return $minutes . " $label ago";
  }
  // If less than 24 hours ago 
  elseif ($timeDifference < 86400) {
    $hours = floor($timeDifference / 3600);
    $label = $hours > 1 ? 'hours' : 'hour';
    return $hours . " $label ago";
  }
  // If more than 1 day ago
  else {
    return date('F d, Y | H:i', $givenTime);
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History</title>
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/history.css">
  <link rel="icon" href="../../assets/img/shared/logo_s.png">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>

<body>
  <?php include("../../assets/shared/navigationBar.php") ?>
  <?php include("../../assets/shared/sideBar.php") ?>

  <div class="mainHeader">
    <h2>History</h2>
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
          $iconPath = $basePath . "savings/" . $row['icon'];
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
              <?php echo ($row['type'] == 'expenses' ? '-' : '+') . '₱' . number_format($row['amount'], 2); ?>
            </span>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="text-center text-white mt-5">
        <h5 style="font-size:16px;">You haven’t made any transactions here yet.</h5>
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
</body>

</html>