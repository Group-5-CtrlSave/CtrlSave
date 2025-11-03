<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave Admin | Challenge </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/challenges.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<Style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #fff;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    /* Page Title */
    .title {
        margin-top: 20px;
    }

    h1 {
        font-weight: 650;
        color: #FFC727;
        text-align: center;
        align-items: center;
    }

    /* Filters */

    .filters {
        margin-top: 40px;
        justify-content: start;
        align-items: start;
    }

    .addbtn {
        background-color: #F6D25B;
        color: black;
        text-align: center;
        width: 190px;
        height: 45px;
        font-size: 15px;
        font-weight: bold;
        font-family: "Poppins", sans-serif;
        border-radius: 27px;
        cursor: pointer;
        text-decoration: none;
        border: none;
        box-shadow: 0 9px 5px #999;
    }

    .addbtn:hover {
        background-color: #F6D25B;
    }

    .addbtn:active {
        background-color: #f1c225;
        box-shadow: 0 5px 5px #323232;
        transform: translateY(4px);
    }

    /* Filter */
    .filter {
        justify-content: end;
        align-items: end;
        text-align: end;
    }

    .filter-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 15px 0 25px;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .filter-dropdown {
        background: none;
        border: none;
        cursor: pointer;
    }

    .filter-menu {
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 130px;
        padding: 8px 0;
    }

    .filter-menu .dropdown-header {
        font-weight: 600;
        text-align: center;
        font-size: 0.9rem;
        padding: 5px 10px 8px;
        color: #333;
    }

    .filter-menu .dropdown-item:hover {
        background-color: #FFC727 !important;
    }
</Style>

<body>
    <!-- Navigation Bar -->
    <?php include("../../assets/shared/navigationBar.php") ?>

    <!-- Filter Container -->
    <div class="container-fluid">

        <div class="row title">
            <div class="col-12">
                <h1>Challenges</h1>
            </div>
        </div>

        <div class="row filters">
            <!-- Add Button -->
            <div class="col-8 addNewChallenge">
                <button class="addbtn" href="addChallenge.html">Add New Challenge</button>
            </div>

            <!-- Filter Button -->
            <div class="col-4 filter">
                <div class="dropdown">
                    <button class="filter-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="img-fluid" src="../../../admin/assets/img/filter_icon.png">
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end filter-menu" id="filterMenu">
                        <li class="dropdown-header">Filter</li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">all</a></li>
                        <li><a class="dropdown-item" href="#">Weekly</a></li>
                        <li><a class="dropdown-item" href="#">Monthly</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container-fluid challengeSection mt-2 p-4">

        <!-- Challenge Row -->
        <div class="row challenges ">
            <!-- Challenge Button -->
            <div class="col-9 ">
                <h3>Login to CtrlSave</h3>
            </div>
            <!-- 3 dots Button -->
            <div class="col-3">
                <div class="dropdown three-dots-dropdown">
                    <button class="three-dots-btn" data-bs-toggle="dropdown" aria-expanded="false">⋮</button>
                    <ul class="dropdown-menu dropdown-menu-end status-menu" id="statusMenu">
                        <li class="dropdown-header">Status</li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item">Show<span class="hover active"></span></a>
                        </li>
                        <li><a class="dropdown-item">Hide</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <div class="delete-btn" id="deleteFilter">Delete</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>