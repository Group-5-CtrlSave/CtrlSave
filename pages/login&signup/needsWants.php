<?php
include("../../pages/login&signup/process/needsWantsBE.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Needs or Wants</title>
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    
    <style>

        body,
        html {
            background-color: #44B87D;
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: #ffff;
            text-align: center;
        }

        .desc {
            font-family: "Roboto", sans-serif;
            font-size: 16px;
            color: #ffff;
            text-align: center;
        }

        .main-container {
            display: flex;
            justify-content: center;
        }

        .titleCateg {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            color: black;
            font-size: 20px;
        }

        .expenseName {
            width: 100px;
            font-family: "Roboto", sans-serif;
            font-weight: 500;
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
            margin-top: 30px;
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        input[type="checkbox"] {
            accent-color: #F6D25B;
            width: 20px;
            height: 20px;
            cursor: pointer;
            position: relative;
        }

        /* --- Toast Error Handling --- */
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

    <?php if (!empty($error)) : ?>
        <div id="errorToast">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

     <!-- Navigation Bar -->
   <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top" style="height: 75px;">
        <div class="container-fluid position-relative">
            <div class="position-absolute top-70 start-50 translate-middle">
                <h2 class="m-0 text-center navigationBarTitle" style="color:black;">Categorize</h2>
            </div>
        </div>
    </nav>

    <div class="container-fluid d-flex flex-column justify-content-between main-container">

        <div class="row">
            <div class="col-12 title mt-5">
                <h2>Categorize Expenses</h2>
            </div>
            <div class="col-12 desc mt-2">
                <p>Categorize it based on Needs and Wants</p>
            </div>
        </div>

        <form id="needWantsForm" method="post">
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    <div class="p-3 rounded" style="min-width: 300px; height: 350px; background-color: white; border: 2px solid #F6D25B; border-radius: 20px;">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="titleCateg" style="width: 100px;">Expense</div>
                            <div class="titleCateg">Needs</div>
                            <div class="titleCateg">Wants</div>
                        </div>

                        <div class="row" style="overflow-x: scroll; min-width: 300px; height: 280px;">
                            <?php if (empty($categories)) : ?>
                                <div class="text-center">No expenses found.</div>
                            <?php else : ?>
                                <?php foreach ($categories as $cat) :
                                    $id = (int)$cat['userCategoryID'];
                                    $name = htmlspecialchars($cat['categoryName']);
                                    $current = strtolower($cat['userNecessityType']);
                                ?>
                                    <div class="d-flex justify-content-between mb-2 align-items-center">
                                        <div class="expenseName"><?= $name ?></div>
                                        <input type="checkbox" name="check_<?= $id ?>" value="need" onchange="selectOnlyOne(this)" <?= $current === 'need' ? 'checked' : '' ?>>
                                        <input type="checkbox" name="check_<?= $id ?>" value="want" onchange="selectOnlyOne(this)" <?= $current === 'want' ? 'checked' : '' ?>>
                                        <input type="hidden" id="hidden_<?= $id ?>" name="necessity[<?= $id ?>]" value="<?= $current ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-center">
                <a id="nextBtn" href="#">
                    <button type="button" class="btn btn-warning mb-3">Next</button>
                </a>
            </div>
        </form>
    </div>

    <script>
        function selectOnlyOne(checkbox) {
            const boxes = document.getElementsByName(checkbox.name);
            boxes.forEach(item => {
                if (item !== checkbox) item.checked = false;
            });

            const id = checkbox.name.split('_')[1];
            const hidden = document.getElementById('hidden_' + id);
            hidden.value = checkbox.checked ? checkbox.value : '';
        }

        document.getElementById('nextBtn').addEventListener('click', function(e) {
            e.preventDefault();
            let valid = true;

            document.querySelectorAll('[id^="hidden_"]').forEach(h => {
                if (h.value === '') valid = false;
            });

            if (!valid) {
                showErrorToast("Please select Needs or Wants for all expenses.");
                return;
            }

            document.getElementById('needWantsForm').submit();
        });

        // Reusable toast (matches login style)
        function showErrorToast(message) {
            const existing = document.getElementById('errorToast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'errorToast';
            toast.textContent = message;
            document.body.appendChild(toast);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>