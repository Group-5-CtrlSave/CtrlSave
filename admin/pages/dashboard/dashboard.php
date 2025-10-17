<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  
  <!-- Poppins Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../../assets/css/dashboard.css">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">

</head>

<body>

  <!-- Navigation Bar -->
  <?php include("../../assets/shared/navigationBar.php") ?>

  <div class="container py-4">
    <div class="name">Admin Dashboard</div>

    <div class="summary-card">
      <div class="summary-item">
        <i class="bi bi-people-fill"></i>
        <div class="label">Total<br>Users</div>
        <div class="value">100</div>
      </div>

      <div class="vertical-divider"></div>

      <div class="summary-item">
        <i class="bi bi-person-plus-fill"></i>
        <div class="label">New<br>Users</div>
        <div class="value">20</div>
      </div>

      <div class="vertical-divider"></div>

      <div class="summary-item">
        <i class="bi bi-person-check-fill"></i>
        <div class="label">Active<br>Users</div>
        <div class="value">70</div>
      </div>
    </div>
  </div>

</body>
</html>
