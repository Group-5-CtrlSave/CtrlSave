<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Add Expenses</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nanum+Myeongjo&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            background-color: #44B87D;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
            text-align: center;
        }

        .label {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: 20px;
            color: #ffff;
            display: flex;
        }

        .form-control {
            border: 2px solid #F6D25B;
            height: 50px;
            display: flex;
            font-family: "Roboto", sans-serif;
            background-color: white;
            border-radius: 20px;
        }

        .expenseName {
            margin-top: 20px;
        }

        .expenseIcon {
            margin-top: 20px;
        }

        .custom-dropdown {
            position: relative;
            width: 100%;
            cursor: pointer;
        }

        .selected-option {
            background-color: white;
            border: 2px solid #F6D25B;
            padding: 10px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 20px;
        }

        .dropdown-options {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #F6D25B;
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            flex-direction: row;
            gap: 10px;
            padding: 10px;
            flex-wrap: wrap;
            text-align: center;
            align-items: center;
        }

        .dropdown-option {
            padding: 10px;
            display: flex;
            align-items: center;
        }

        .dropdown-option:hover {
            background-color: white;
        }

        .selected-option::after {
            content: '▼';
            font-size: 11px;
            color: #3a644eff;
            margin-left: 10px;
            transition: transform 0.3s ease;
        }

        .selected-option.open::after {
            transform: rotate(180deg);
        }

        .needWants {
            margin-top: 20px;
        }

        .limitTrack {
            margin-top: 20px;
        }

        p {
            color: white;
            font-family: bolder;
            font-family: "Poppins", sans-serif !important;
            font-size: 20px;
        }

        .form-check-label {
            color: white;
            font-weight: 5px;
            font-size: 20px;
            font-family: "Roboto", sans-serif;
        }

        .btn {
            background-color: #F6D25B;
            color: black;
            text-align: center;
            width: 125px;
            font-size: 20px;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            border-radius: 27px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            margin-top: 20px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        /* Media Queries of Every Mobile Screen */
        @media screen and (min-width:344px) {}

        @media screen and (min-width:360px) {}

        @media screen and (min-width:375px) {
            .label {
                font-size: 18px;
            }

            .expenseName {
                margin-top: 10px;
            }

            .expenseIcon {
                margin-top: 10px;
            }

            .form-control {
                height: 50px;
            }

            .needWants {
                margin-top: 10px;
            }

            .limitTrack {
                margin-top: 10px;
            }
        }

        @media screen and (min-width:390px) {
            .label {
                font-size: 20px;
            }

            .expenseName {
                margin-top: 30px;
            }

            .expenseIcon {
                margin-top: 30px;
            }

            .form-control {
                height: 50px;
            }

            .needWants {
                margin-top: 30px;
            }

            .limitTrack {
                margin-top: 30px;
            }

            .btn {
                margin-top: 50px;
            }
        }

        @media screen and (min-width:414px) {
            .label {
                font-size: 20px;
            }

            .expenseName {
                margin-top: 20px;
            }

            .expenseIcon {
                margin-top: 20px;
            }

            .form-control {
                height: 50px;
            }

            .needWants {
                margin-top: 20px;
            }

            .limitTrack {
                margin-top: 20px;
            }

            .btn {
                margin-top: 40px;
            }
        }

        @media screen and (min-width:430px) {
            .label {
                font-size: 20px;
            }

            .expenseName {
                margin-top: 30px;
            }

            .expenseIcon {
                margin-top: 40px;
            }

            .form-control {
                height: 50px;
            }

            .needWants {
                margin-top: 40px;
            }

            .limitTrack {
                margin-top: 40px;
            }

            .btn {
                margin-top: 60px;
            }
        }
    </style>

</head>

<body>

    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="pickExpense.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                        style="height: 24px;" />
                </a>
            </div>

            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Add Expenses</h2>
            </div>
        </div>
    </nav>


    <!-- Add New Expenses -->
    <div class="container-fluid main-container d-flex justify-content-center align-items-center mt-4">
        <div class="row main-row">

            <!-- Title -->
            <div class="col-12 title">
                <h2>Add more expense<br>category</h2>
            </div>

            <!-- New Expenses Name -->
            <div class="col-12 expenseName">
                <label class="label">Enter category name:</label>
                <input type="text" class="form-control" placeholder="e.g. Netflix" required>
            </div>

            <!-- New Expenses Icons -->
            <div class="col-12 expenseIcon">
                <label class="label">Choose category icon:</label>
                <div class="custom-dropdown" id="iconDropdown">
                    <div class="selected-option" id="selectedOption">
                        <span>Select icon</span>
                    </div>

                    <div class="dropdown-options" id="dropdownOptions">

                        <div class="dropdown-option" id="uploadIconOption">
                            <i class="bi bi-cloud-upload" style="font-size: 1.8rem; color: #3a644e; margin-left: 8px;"></i>
                            <input type="file" id="customIconInput" accept="image/*" style="display: none;">
                        </div>

                        <div class="dropdown-option" data-value="car">
                            <img src="../../assets/img/shared/categories/expense/Car.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="clothes">
                            <img src="../../assets/img/shared/categories/expense/Clothes.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="coffee">
                            <img src="../../assets/img/shared/categories/expense/Coffee.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="dinning">
                            <img src="../../assets/img/shared/categories/expense/Dining Out.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="electricity">
                            <img src="../../assets/img/shared/categories/expense/Electricity.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="entertainment">
                            <img src="../../assets/img/shared/categories/expense/Entertainment.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="gift">
                            <img src="../../assets/img/shared/categories/expense/Gift.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="groceries">
                            <img src="../../assets/img/shared/categories/expense/Groceries.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="health">
                            <img src="../../assets/img/shared/categories/expense/Health.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="house">
                            <img src="../../assets/img/shared/categories/expense/House.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="wifi">
                            <img src="../../assets/img/shared/categories/expense/Internet Connection.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="laundry">
                            <img src="../../assets/img/shared/categories/expense/Laundry.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="party">
                            <img src="../../assets/img/shared/categories/expense/Party.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="rent">
                            <img src="../../assets/img/shared/categories/expense/Rent.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="schoolNeeds">
                            <img src="../../assets/img/shared/categories/expense/School Needs.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="selfCare">
                            <img src="../../assets/img/shared/categories/expense/Selfcare.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="shopping">
                            <img src="../../assets/img/shared/categories/expense/Shopping.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="subscriptions">
                            <img src="../../assets/img/shared/categories/expense/Subscriptions.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="transportation">
                            <img src="../../assets/img/shared/categories/expense/Transportation.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="tuition">
                            <img src="../../assets/img/shared/categories/expense/Tuition.png" width="40">
                        </div>

                        <div class="dropdown-option" data-value="water">
                            <img src="../../assets/img/shared/categories/expense/Water.png" width="40">
                        </div>

                    </div>
                </div>

                <input type="hidden" id="selectedIcon" name="selectedIcon">

            </div>

            <!-- Needs or Wants -->
            <div class="col-12 needWants">
                <label class="label">Is this expense a Need or a Want?</label>
                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="category" id="needs" value="needs">
                    <label class="form-check-label" for="needs">
                        Needs
                    </label>
                </div>

                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="category" id="wants" value="wants">
                    <label class="form-check-label" for="wants">
                        Wants
                    </label>
                </div>
            </div>

            <!-- Limit or Track -->
            <div class="col-12 limitTrack">
                <label class="label">Do you want to Limit or Track this expense?</label>
                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="limits" id="limit" value="limit">
                    <label class="form-check-label" for="imit">
                        Limit
                    </label>
                </div>

                <div class="form-check fs-4">
                    <input class="form-check-input me-2 forms" type="radio" name="limits" id="track" value="track">
                    <label class="form-check-label" for="track">
                        Track
                    </label>
                </div>
            </div>

            <!-- Button -->
            <div class="col-12 btNext d-flex justify-content-center align-items-center">
                <a href="pickExpense.php"><button type="submit" class="btn btn-warning">Save</button></a>
            </div>

        </div>



    </div>


    <script>
        const iconDropdown = document.getElementById('iconDropdown');
        const selectedOption = document.getElementById('selectedOption');
        const dropdownOptions = document.getElementById('dropdownOptions');
        const options = document.querySelectorAll('.dropdown-option');
        const selectedIconInput = document.getElementById('selectedIcon');

        selectedOption.addEventListener('click', () => {
            const isVisible = dropdownOptions.style.display === 'flex';
            dropdownOptions.style.display = isVisible ? 'none' : 'flex';
            selectedOption.classList.toggle('open', !isVisible);
        });

        options.forEach(option => {
            option.addEventListener('click', () => {
                const img = option.querySelector('img').cloneNode();
                selectedOption.innerHTML = '';
                selectedOption.appendChild(img);
                selectedIconInput.value = option.getAttribute('data-value');
                dropdownOptions.style.display = 'none';
            });
        });

        // Close the dropdown if clicked outside
        window.addEventListener('click', function (e) {
            if (!iconDropdown.contains(e.target)) {
                dropdownOptions.style.display = 'none';
            }
        });
    </script>

    <script>
        const uploadIconOption = document.getElementById('uploadIconOption');
        const customIconInput = document.getElementById('customIconInput');

        // When user clicks upload option
        uploadIconOption.addEventListener('click', () => {
            customIconInput.click();
        });

        // Handle image upload
        customIconInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.width = 40;

                    selectedOption.innerHTML = '';
                    selectedOption.appendChild(img);
                    selectedIconInput.value = 'custom-' + file.name; // mark as custom upload
                    dropdownOptions.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>