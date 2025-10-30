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
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<style>
    body {
        max-width: 100%;
        min-height: 100%;
    }

    h1 {
        font-family: "Poppins", sans-serif;
        font-weight: 650;
        color: #FFC727;
        text-align: center;
        align-items: center;
    }

    .addNewChallenge {
        justify-content: start;
        align-items: start;
    }

    .addbtn {
        background-color: #F6D25B;
        color: black;
        text-align: center;
        width: 200px;
        height: 45px;
        font-size: clamp(1rem, 1vw, 0.5rem);
        font-weight: bold;
        font-family: "Poppins", sans-serif;
        border-radius: 30px;
        cursor: pointer;
        z-index: 2;
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
        padding-top: 10px;
    }

    .filter-dropdown {
        background: none;
        border: none;
        cursor: pointer;
    }

    .filter-menu {
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 160px;
        padding: 8px 0;
    }

    .filter-menu .dropdown-header {
        font-weight: 600;
        text-align: center;
        font-size: 0.9rem;
        padding: 5px 10px 8px;
        color: #333;
    }

    .filter-menu .dropdown-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 14px;
        font-size: 0.9rem;
        color: #333;
        transition: background-color 0.2s ease;
    }

   

    .filter-menu .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #F0f1f6;
    }

    .filter-menu .dot.active {
        background-color: #FFC727;
    }

    .filter-menu .dropdown-divider {
        margin: 6px 0;
    }

    .filter-menu .clear-btn {
        text-align: center;
        color: #44B87D;
        font-weight: 500;
        padding: 5px 0;
        cursor: pointer;
    }

    .filter-menu .clear-btn:hover {
        text-decoration: underline;
    }

    .challengeTitle {
        color: white;
        font-size: clamp(1.2rem, 2vw, 1.5rem);
        text-align: start;
        align-items: start;
        justify-content: start;
        font-family: "Roboto", sans-serif;
        font-weight: 460;
    }

    .challengesForm {
        background-color: #44B87D;
        border-radius: 16px;
        margin-bottom: 15px;
        padding: 12px;
        position: relative;
    }

    .strat-title {
        font-size: 1rem;
        font-weight: 600;
        color: #F0f1f6;
    }

    .dropdown.three-dots-dropdown {
        position: absolute;
        top: 8px;
        right: 10px;
    }

    .three-dots-btn {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #333;
    }

    .dropdown-item:hover {
        background-color: #f1f1f1 !important;
        color: #000 !important;
    }

    .dropdown-menu-end {
        border-radius: 10px;
        min-width: 120px;
        text-align: center;
        z-index: 1;
    }

    .filter-menu .delete-btn {
        text-align: center;
        color: #D32626;
        font-weight: 500;
        padding: 5px 0;
        cursor: pointer;
    }

    .filter-menu .delete-btn:hover {
        text-decoration: underline;
    }
</style>

<body>

    <!-- Navigation Bar -->
    <?php include("../../assets/shared/navigationBar.php") ?>

    <!-- Content -->
    <div class="container-fluid mainContainer">

        <!--Title-->
        <h1 class="Title mt-5">Challenges</h1>

        <div class="container mt-2">
            <!--Buttons Section-->
            <div class="row buttons">
                
                <!--Button-->
                <div class="col-8 addNewChallenge">
                    <button class="addbtn">Add New Challenge</button>
                </div>

                <!--Filter-->
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
                            <li><a class="dropdown-item" href="#">Weekly<span class="hover active"></span></a></li>
                            <li><a class="dropdown-item" href="#">Monthly<span class="dot"></span></a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <div class="clear-btn" id="clearFilter">Clear</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Challenge Section -->
            <div class="challengeContainer" style="">

                <!-- Challenge Title -->
                <div class="row challengesForm">
                    <div class="col-9">
                        <span class="challengeTitle">Login to CtrlSave</span>
                    </div>

                    <!-- Challenge Status -->
                    <div class="col-3">
                        <div class="dropdown three-dots-dropdown">
                            <button class="three-dots-btn" data-bs-toggle="dropdown" aria-expanded="false">⋮</button>
                            <ul class="dropdown-menu dropdown-menu-end filter-menu" id="filterMenu">
                                <li class="dropdown-header">Status</li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Show<span class="dot active"></span></a>
                                </li>
                                <li><a class="dropdown-item" href="#">Hide<span class="dot"></span></a></li>
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
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>