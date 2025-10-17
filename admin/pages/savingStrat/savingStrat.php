<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CtrlSave Admin | Saving Strategies</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../../assets/css/savingStrat.css">
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
</head>

<body>
  <?php include("../../assets/shared/navigationBar.php") ?>
  <div class="container mt-3">

    <h1>Saving Strategies</h1>
    <!-- Strat filter -->
    <div class="filter-section">
         <!-- Strat post -->
      <button class="addbtn">Add Post</button>
      <div class="dropdown">
        <button class="filter-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
           <img class="img-fluid" src="../../../admin/assets/img/filter_icon.png">
        </button>
        <ul class="dropdown-menu dropdown-menu-end filter-menu" id="filterMenu">
        <li class="dropdown-header">Filter</li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Video <span class="dot active"></span></a></li>
        <li><a class="dropdown-item" href="#">Book <span class="dot"></span></a></li>
        <li><a class="dropdown-item" href="#">Article <span class="dot"></span></a></li>
        <li><hr class="dropdown-divider"></li>
        <li><div class="clear-btn" id="clearFilter">Clear</div></li>
        </ul>
      </div>
    </div>

    <!-- Strat Cardss -->
    <div class="strat-card">
    <div class="image-container">
        <div class="overlay-top">
        <span class="strat-title">Ultimate Guide to Saving on a Budget</span>
         <iframe 
            src="https://www.youtube.com/embed/beJeJFHxnDI?si=wdUnNRUMfHUR8C9r"
            title="The Ultimate Guide to Save Money on a Tight Budget"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen>
        </iframe>
        <div class="dropdown three-dots-dropdown">
            <button class="three-dots-btn" data-bs-toggle="dropdown" aria-expanded="false">⋮</button>
            <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Edit</a></li>
            <li><a class="dropdown-item delete" href="#">Delete</a></li>
            </ul>
        </div>
        </div>
    </div>

    </div>
    <div class="strat-card">
      <span class="strat-title">Simple Ways to Save Money for the Future</span>
      <div class="dropdown three-dots-dropdown">
        <button class="three-dots-btn" data-bs-toggle="dropdown" aria-expanded="false">⋮</button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Edit</a></li>
          <li><a class="dropdown-item delete" href="#">Delete</a></li>
        </ul>
      </div>
    </div>

    <div class="strat-card">
      <span class="strat-title">28 Proven Ways to Save Money</span>
      <div class="dropdown three-dots-dropdown">
        <button class="three-dots-btn" data-bs-toggle="dropdown" aria-expanded="false">⋮</button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Edit</a></li>
          <li><a class="dropdown-item delete" href="#">Delete</a></li>
        </ul>
      </div>
    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
