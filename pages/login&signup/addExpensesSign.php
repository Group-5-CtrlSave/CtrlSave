<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nanum+Myeongjo&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            background-color: #44B87D;
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            font-size: clamp(1.8rem, 1vw, 1rem);
            color: #ffff;
            text-align: center;
        }

       .btn {
            background-color: #F6D25B;
            color: black;
            text-align: center;
            width: 150px;
            font-size: clamp(1.3rem, 2vw, 1rem);
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            border-radius: 30px;
            cursor: pointer;
            z-index: 2;
            text-decoration: none;
            border: none;
            margin-top: 180px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        .label {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
            font-size: clamp(1.3rem, 1vw, 1rem);
            color: #ffff;
            display: flex;
        }

        .form-control {
            border: 2px solid #F6D25B;
            height: 50px;
            display: flex;
            font-family: "Roboto", sans-serif;
            background-color: #F0F1F6;
        }

        .custom-dropdown {
            position: relative;
            width: 100%;
            cursor: pointer;
        }

        .selected-option {
            background-color: #F0F1F6;
            border: 2px solid #F6D25B;
            padding: 10px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 5px;
        }

        .dropdown-options {
            display: none;
            position: absolute;
            background-color: #F0F1F6;
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
            background-color: #F0F1F6;
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
    </style>

</head>

<body>

<!-- No Logo Navigation Bar -->
    <nav class="bg-white px-4 d-flex align-items-center justify-content-between position-relative shadow"
        style="height: 72px;">
        <a href="pickExpense.php" class="text-decoration-none">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
        </a>
        <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark"
            style="font-family: Poppins, sans-serif;">
            Add Expenses
        </h5>
    </nav>


    <!-- Add New Expenses -->
    <div class="container-fluid main-container d-flex justify-content-center align-items-center mt-5">
        <div class="row main-row">

            <!-- Title -->
            <div class="col-12 title">
                <h2>Add more expense<br>category</h2>
            </div>

            <!-- New Expenses Name -->
            <div class="col-12 firstname mt-3">
                <label class="label">Enter category name:</label>
                <input type="text" class="form-control" placeholder="e.g. Netflix" required>
            </div>

            <!-- New Expenses Icons -->
                <div class="col-12 firstname mt-4">
                    <label class="label">Choose category icon:</label>
                    <div class="custom-dropdown" id="iconDropdown">
                        <div class="selected-option" id="selectedOption">
                            <span>Select icon</span>
                        </div>
                        <div class="dropdown-options" id="dropdownOptions">
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>