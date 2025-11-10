<?php

include("../../assets/shared/connect.php");
include("../../pages/login&signup/process/addExpenseSignBE.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CtrlSave | Add Expense</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
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
            font-family: "Roboto", sans-serif;
            background-color: white;
            border-radius: 20px;
        }

        .expenseName,
        .expenseIcon,
        .needWants,
        .limitTrack {
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

        .dropdown-arrow {
            transition: transform 0.3s ease;
            font-size: 1.25rem;
            color: #44B87D;
        }

        .dropdown-arrow.rotated {
            transform: rotate(180deg);
        }

        .dropdown-options {
            display: none;
            position: absolute;
            background-color: white;
            border: 2px solid #F6D25B;
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            flex-wrap: wrap;
            padding: 10px;
            border-radius: 20px;
        }

        .dropdown-option {
            padding: 10px;
            display: inline-block;
            cursor: pointer;
        }

        .dropdown-option img {
            width: 40px;
            height: 40px;
        }

        .btn {
            background-color: #F6D25B;
            color: black;
            width: 125px;
            font-size: 20px;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            border-radius: 27px;
            border: none;
            margin-top: 30px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px rgba(0, 0, 0, 0.24);
        }

        #errorToast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #E63946;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            width: 300px;
            font-family: "Poppins", sans-serif;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
            animation: fadeInOut 3s ease forwards;
            text-align: center;
        }

        @keyframes fadeInOut {
            0% {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }

            10% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            70% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }
        }
    </style>
</head>

<body>
    <?php if (!empty($error)) { ?>
        <div id="errorToast"><?= htmlspecialchars($error) ?></div>
    <?php } ?>

    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="pickExpense.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;" />
                </a>
            </div>
            <div class="position-absolute top-50 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Add Expenses</h2>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-container d-flex justify-content-center align-items-center mt-4">
        <div class="row main-row">
            <form method="POST" id="addExpenseForm">
                <div class="col-12 title text-center">
                    <h2>Add more expense<br>category</h2>
                </div>

                <div class="col-12 expenseName">
                    <label class="label">Enter category name:</label>
                    <input type="text" class="form-control" name="categoryName" id="categoryName" placeholder="e.g. Netflix" required>
                </div>

                <div class="col-12 expenseIcon">
                    <label class="label">Choose category icon:</label>
                    <div class="custom-dropdown" id="iconDropdown">
                        <div class="selected-option" id="selectedOption">
                            <span id="selectedIconDisplay">Select icon</span>
                            <span id="dropdownArrow" class="dropdown-arrow bi bi-caret-down-fill"></span>
                        </div>
                        <div class="dropdown-options" id="dropdownOptions">
                            <?php
                            $icons = ['Car', 'Clothes', 'Coffee', 'Dining Out', 'Electricity', 'Entertainment', 'Gift', 'Groceries', 'Health', 'House', 'Internet Connection', 'Laundry', 'Party', 'Rent', 'School Needs', 'Selfcare', 'Shopping', 'Subscriptions', 'Transportation', 'Tuition', 'Water'];
                            foreach ($icons as $icon) {
                                echo '<div class="dropdown-option" data-value="' . htmlspecialchars($icon) . '">
                                        <img src="../../assets/img/shared/categories/expense/' . htmlspecialchars($icon) . '.png" alt="' . htmlspecialchars($icon) . '">
                                      </div>';
                            }
                            ?>
                        </div>
                    </div>
                    <input type="hidden" id="selectedIcon" name="selectedIcon">
                </div>

                <div class="col-12 needWants">
                    <label class="label">Is this expense a Need or a Want?</label>
                    <div class="form-check fs-4">
                        <input class="form-check-input" type="radio" name="category" id="needs" value="need">
                        <label class="form-check-label" style="color:white;" for="needs">Needs</label>
                    </div>
                    <div class="form-check fs-4">
                        <input class="form-check-input" type="radio" name="category" id="wants" value="want">
                        <label class="form-check-label" style="color:white;" for="wants">Wants</label>
                    </div>
                </div>

                <div class="col-12 limitTrack">
                    <label class="label">Do you want to Limit or Track this expense?</label>
                    <div class="form-check fs-4">
                        <input class="form-check-input" type="radio" name="limits" id="limit" value="1">
                        <label class="form-check-label" style="color:white;" for="limit">Limit</label>
                    </div>
                    <div class="form-check fs-4">
                        <input class="form-check-input" type="radio" name="limits" id="track" value="0">
                        <label class="form-check-label" style="color:white;" for="track">Track</label>
                    </div>
                </div>

                <div class="col-12 btNext d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-warning">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const iconDropdown = document.getElementById('iconDropdown');
        const selectedOption = document.getElementById('selectedOption');
        const dropdownOptions = document.getElementById('dropdownOptions');
        const selectedIconInput = document.getElementById('selectedIcon');
        const dropdownArrow = document.getElementById('dropdownArrow');

        selectedOption.addEventListener('click', () => {
            const isVisible = dropdownOptions.style.display === 'flex';
            dropdownOptions.style.display = isVisible ? 'none' : 'flex';
            dropdownArrow.classList.toggle('rotated', !isVisible);
        });

        dropdownOptions.querySelectorAll('.dropdown-option').forEach(option => {
            option.addEventListener('click', () => {
                const iconValue = option.getAttribute('data-value');
                const img = option.querySelector('img').cloneNode();

                selectedIconInput.value = iconValue + ".png";
                selectedOption.innerHTML = '';
                selectedOption.appendChild(img);
                selectedOption.appendChild(dropdownArrow);

                dropdownOptions.style.display = 'none';
                dropdownArrow.classList.remove('rotated');
            });
        });

        window.addEventListener('click', (e) => {
            if (!iconDropdown.contains(e.target)) {
                dropdownOptions.style.display = 'none';
                dropdownArrow.classList.remove('rotated');
            }
        });

        document.getElementById('addExpenseForm').addEventListener('submit', function(e) {
            const name = document.getElementById('categoryName').value.trim();
            const icon = document.getElementById('selectedIcon').value.trim();
            const needWant = document.querySelector('input[name="category"]:checked');
            const limitTrack = document.querySelector('input[name="limits"]:checked');

            if (!name || !icon || !needWant || !limitTrack) {
                e.preventDefault();
                showToast("Please fill in all fields before saving.");
            }
        });

        function showToast(message) {
            const toast = document.createElement("div");
            toast.id = "errorToast";
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>
</html>
