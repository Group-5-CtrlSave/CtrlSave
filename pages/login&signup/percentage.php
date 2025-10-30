<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave</title>
    <link rel="icon" href="../assets/imgs/ctrlsaveLogo.png">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
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

        .desc {
            font-family: "Roboto", sans-serif;
            font-size: clamp(1.3rem, 1vw, 1rem);
            color: #ffff;
            text-align: center;
        }

        nav {
            background-color: white;
            padding: 1rem;
        }

        .main-container {
            height: calc(100vh - 120px);
            /* full height minus nav + buttons */
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .input-group {
            background-color: #F0F1F6;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            margin: 0.5rem auto;
            width: 80%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            border: 2px solid #F6D25B;
        }

        .input-group label {
            margin: 0;
            font-size: 1rem;
            color: black;
            flex: 1;
        }

        .input-group input {
            border: none;
            background: transparent;
            border-radius: 5px;
            text-align: center;
            width: 50px;
            font-weight: bold;
            color: #44B87D;
        }

        .input-group input:focus {
            outline: none;
        }

        .section-header {
            color: white;
            font-weight: 500;
            font-size: 1rem;
            text-align: center;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
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
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

    </style>
</head>

<body>

    <!-- No Logo Navigation Bar -->
    <nav class="bg-white px-4 d-flex align-items-center justify-content-between position-relative shadow"
        style="height: 72px;">
        <a href="budgetingRule.php" class="text-decoration-none">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
        </a>
        <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark"
            style="font-family: Poppins, sans-serif;">
            Own Budget Rule
        </h5>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid d-flex flex-column justify-content-between main-container">

    
        <div class="row">
            <!-- Title -->
            <div class="col-12 title mt-2">
                <h2>Create your own <br>budgeting rule</h2>
            </div>

            <!-- Description -->
            <div class="col-12 desc mt-2">
                <p>Set up your own budgeting rule</p>
            </div>

        </div>

        <!-- Set Percentage -->
        <!-- Budget Inputs -->
        <div class="row" style="overflow:scroll; height: 300px;">
            <div class="col-12">
                <div class="input-group">
                    <label for="dining">Dining Out</label>
                    <input type="text" id="dining" value="0" />
                </div>
                <div class="input-group">
                    <label for="electricity">Electricity</label>
                    <input type="text" id="electricity" value="0" />
                </div>
                <div class="input-group">
                    <label for="groceries">Groceries</label>
                    <input type="text" id="groceries" value="0" />
                </div>
                <div class="input-group">
                    <label for="transportation">Transportation</label>
                    <input type="text" id="transportation" value="0" />
                </div>

                <p class="section-header">How much do you want to save?</p>

                <div class="input-group">
                    <label for="savings">Savings</label>
                    <input type="text" id="savings" value="0" />
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-center">
            <p id="warning-msg" style="color: #fff; font-weight: bold; size: 1.4rem;"></p>
        </div>


        <!-- Buttons -->
        <div class="col-12 d-flex justify-content-center">
           <button onclick="location.href='done.php'" type="submit" class="btn btn-warning mb-3">Next</button>
        </div>

    </div>


    <script>
    const inputs = document.querySelectorAll('.input-group input');
    const warning = document.getElementById('warning-msg');
    const nextBtn = document.querySelector('.btn');

    function validateInputs() {
        let valid = true;
        inputs.forEach(input => {
            let val = input.value.trim().replace(/,/g, ''); // remove commas for check
            if (val === "" || isNaN(val)) {
                valid = false;
            }
        });

        if (!valid) {
            warning.textContent = `Please enter valid numbers only.`;
            nextBtn.disabled = true;
            nextBtn.style.opacity = 0.5;
        } else {
            warning.textContent = '';
            nextBtn.disabled = false;
            nextBtn.style.opacity = 1;
        }
    }

    function formatNumber(val) {
        // Remove non-digits and commas, then reformat
        val = val.replace(/[^0-9]/g, '');
        return val ? parseInt(val, 10).toLocaleString() : "";
    }

    inputs.forEach(input => {
        input.addEventListener('input', () => {
            let cursorPos = input.selectionStart;
            let beforeLength = input.value.length;

            input.value = formatNumber(input.value);

            // Keep cursor position after formatting
            let afterLength = input.value.length;
            input.selectionEnd = cursorPos + (afterLength - beforeLength);

            validateInputs();
        });

        input.addEventListener('blur', () => {
            if (input.value.trim() === "") {
                input.value = "0";
            }
            validateInputs();
        });
    });

    // Initial check
    validateInputs();
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>