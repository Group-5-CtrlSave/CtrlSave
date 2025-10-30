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

<body>
    <!-- Navigation Bar -->
    <?php include("../../assets/shared/navigationBar.php") ?>

    <!-- Content -->
    <div class="container-fluid mainContainer">

        <!-- Page Title -->
        <h1 class="Title mt-3">Challenges</h1>

        <!-- Buttons Section -->
        <div class="row mt-4">

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

        <!-- Challenge Section -->
        <div class="container-fluid challengeSection mt-5">

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

            <!-- Challenge Row -->
            <div class="row challenges mt-2">
                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>Save ₱5 to saving challenge</h3>
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

            <!-- Challenge Row -->
            <div class="row challenges mt-2">

                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>No-coffee spend day</h3>
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

            <!-- Challenge Row -->
            <div class="row challenges mt-2">

                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>Save ₱40 to saving challenge</h3>
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
            
            <!-- Challenge Row -->
            <div class="row challenges mt-2">

                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>1-day no fast-food spend </h3>
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

            <!-- Challenge Row -->
            <div class="row challenges mt-2">

                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>Read financial tip for today</h3>
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

            <!-- Challenge Row -->
            <div class="row challenges mt-2">

                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>Read financial tip for today</h3>
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

            <!-- Challenge Row -->
            <div class="row challenges mt-2">

                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>Read financial tip for today</h3>
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

            <!-- Challenge Row -->
            <div class="row challenges mt-2">

                <!-- Challenge Button -->
                <div class="col-9">
                    <h3>Read financial tip for today</h3>
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



    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>