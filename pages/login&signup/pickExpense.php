<?php
include("../../assets/shared/connect.php");
include("../../pages/login&signup/process/pickExpenseBE.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        body,
        html {
            background-color: #44B87D;
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
            text-align: center;
            padding-top: 10px;
        }

        .main-container {
            height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .scrollable-container {
            overflow-y: hidden;
            padding: 0 15px;
            transition: max-height 0.3s ease;
        }

        .scrollable-container.scroll-active {
            overflow-y: auto;
            max-height: 440px;
        }

        .expense-option {
            background-color: white;
            border-radius: 20px;
            padding: 12px 15px;
            margin: 8px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 2px solid #F6D25B;
        }

        .expense-option input[type="checkbox"] {
            accent-color: #F6D25B;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        .expense-label {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-family: "Roboto", sans-serif;
        }

        .expense-label span.added {
            font-size: 12px;
            color: gray;
            margin-left: 5px;
        }

        .expense-icon {
            width: 50px;
            height: 40px;
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
            border: none;
            margin-top: 10px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px rgba(0, 0, 0, 0.24);
        }

        .addmoreLink {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            padding-bottom: 15px;
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

    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top" style="height: 74px;">
        <div class="container-fluid position-relative">
            <div class="position-absolute top-70 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Pick Expenses</h2>
            </div>
        </div>
    </nav>

    <div class="container-fluid d-flex flex-column justify-content-between main-container">
        <div class="row">
            <div class="col-12 title">
                <h2>What are your<br>expenses right now?</h2>
            </div>
        </div>

        <form method="POST" id="expenseForm">
            <div class="scrollable-container">
                <?php
                $categories = [];

                // Fetch user categories (including manually added ones)
                if ($userResult && $userResult->num_rows > 0) {
                    while ($row = $userResult->fetch_assoc()) {
                        $isUserAdded = empty($row['defaultCategoryID']); // ✅ True if manually added via addExpenseSign.php
                        $categories[] = [
                            'id' => $isUserAdded ? $row['userCategoryID'] : $row['defaultCategoryID'],
                            'name' => $row['categoryName'],
                            'icon' => $row['icon'],
                            'checked' => $row['isSelected'] ? 'checked' : '',
                            'isUserAdded' => $isUserAdded
                        ];
                    }
                }

                // Fetch default categories (below user-added)
                if ($defaultResult && $defaultResult->num_rows > 0) {
                    while ($row = $defaultResult->fetch_assoc()) {
                        $check = $conn->prepare("SELECT isSelected FROM tbl_usercategories WHERE userID = ? AND defaultCategoryID = ?");
                        $check->bind_param("ii", $userID, $row['defaultCategoryID']);
                        $check->execute();
                        $check->bind_result($isSelected);
                        $check->fetch();
                        $check->close();

                        $categories[] = [
                            'id' => $row['defaultCategoryID'],
                            'name' => $row['categoryName'],
                            'icon' => $row['icon'],
                            'checked' => ($isSelected ?? 0) ? 'checked' : '',
                            'isUserAdded' => false
                        ];
                    }
                }
                ?>

                <div class="row" id="mainCateg">
                    <?php
                    foreach ($categories as $index => $cat) {
                        if ($index < 5) {
                            echo '
                            <div class="col-12">
                                <div class="expense-option">
                                    <label class="expense-label">
                                        <input type="checkbox" name="categories[]" value="' . $cat['id'] . '" ' . $cat['checked'] . ' />
                                        ' . htmlspecialchars($cat['name']);
                            if ($cat['isUserAdded']) echo '<span class="added">(added)</span>';
                            echo '
                                    </label>
                                    <img src="../../assets/img/shared/categories/expense/' . htmlspecialchars($cat['icon']) . '" alt="' . htmlspecialchars($cat['name']) . '" class="expense-icon" />
                                </div>
                            </div>';
                        }
                    }
                    ?>
                    <?php if (count($categories) > 5): ?>
                        <div class="col-12">
                            <a id="seeMoreButton" onclick="seeMoreCateg()" style="color: #ffffffff; display: block;">
                                <span class="expense-label">See more...</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row" id="moreCateg" style="display: none;">
                    <?php
                    foreach ($categories as $index => $cat) {
                        if ($index >= 5) {
                            echo '
                            <div class="col-12">
                                <div class="expense-option">
                                    <label class="expense-label">
                                        <input type="checkbox" name="categories[]" value="' . $cat['id'] . '" ' . $cat['checked'] . ' />
                                        ' . htmlspecialchars($cat['name']);
                            if ($cat['isUserAdded']) echo '<span class="added">(added)</span>';
                            echo '
                                    </label>
                                    <img src="../../assets/img/shared/categories/expense/' . htmlspecialchars($cat['icon']) . '" alt="' . htmlspecialchars($cat['name']) . '" class="expense-icon" />
                                </div>
                            </div>';
                        }
                    }
                    ?>
                    <div class="col-12">
                        <a id="hideButton" onclick="hideMoreCateg()" style="color: #ffffffff; display: none;">
                            <span class="expense-label">Hide</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12 pb-3 d-flex justify-content-center">
                <button type="submit" class="btn btn-warning mt-4" id="nextBtn">Next</button>
            </div>
        </form>

        <div class="col-12 mt-1 d-flex justify-content-center align-items-center">
            <p style="color: #ffff; font-family: Poppins, sans-serif;">Can't find preferred expenses?</p>&nbsp;
            <a href="addExpensesSign.php" class="addmoreLink" style="color: black;">Add more...</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('expenseForm');
        const nextBtn = document.getElementById('nextBtn');

        form.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('input[name="categories[]"]:checked').length;
            if (checked === 0) {
                e.preventDefault();
                showToast("Please select at least one expense before proceeding.");
            }
        });

        function showToast(message) {
            const toast = document.createElement("div");
            toast.id = "errorToast";
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function seeMoreCateg() {
            document.getElementById('seeMoreButton').style.display = "none";
            document.getElementById('moreCateg').style.display = "block";
            document.getElementById('hideButton').style.display = "block";
            document.querySelector('.scrollable-container').classList.add('scroll-active');
        }

        function hideMoreCateg() {
            document.getElementById('seeMoreButton').style.display = "block";
            document.getElementById('moreCateg').style.display = "none";
            document.getElementById('hideButton').style.display = "none";
            document.querySelector('.scrollable-container').classList.remove('scroll-active');
        }
    </script>
</body>

</html>