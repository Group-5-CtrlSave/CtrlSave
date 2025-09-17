<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge</title>
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #44B87D;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
            align-items: center;
            font-family: "Poppins", sans-serif;
        }

        .tab-buttons button {
            padding: 8px 16px;
            border: 2px solid #FFC727;
            border-radius: 20px;
            background-color: white;
            color: #000000ff;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease, transform 0.1s ease;
            font-family: "Poppins", sans-serif;
        }

        .tab-buttons button.active {
            background-color: #FFC727;
            color: black;
            border-color: #FFC727;
        }

        .tab-buttons button:active {
            transform: scale(0.95);
        }

        .tab-content {
            display: none;
            padding: 20px;
        }

        .tab-content.active {
            display: block;
        }

        .challenge-card {
            background: #F0F1F6;
            border-radius: 15px;
            padding: 20px;
            margin: -15px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: 250px;
        }

        .challenge-card h3 {
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
            font-family: "Poppins", sans-serif;
            font-size: clamp(1.2rem, 1vw, 1rem);
        }

        .challenge-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            background: #ffff;
            padding: 10px 15px;
            border-radius: 10px;
            color: #44B87D;
            font-weight: bold;
            border: 2px solid #F6D25B;
            height: 80px;
            font-weight: 600;
        }

        .challenge-item button.claim-btn {
            border: none;
            background-color: #ffc107;
            color: black;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
            cursor: pointer;
            transition: transform 0.1s ease, background-color 0.3s ease;
            width: 75px;
        }

        .challenge-item button.claim-btn:active {
            transform: scale(0.95);
        }

        .challenge-item button.in-progress {
            background-color: #F0F1F6;
            color: black;
            font-weight: 500;
            border: 2px solid #ffc107;
            cursor: default;
            font-size: 15px;
            width: 75px;
            text-align: center;
            border-radius: 10px;
        }
    </style>

</head>

<body>
    <!-- Navigation Bar -->
    <?php include("../../assets/shared/navigationBar.php") ?>
    <!-- Sidebar content-->
    <?php include("../../assets/shared/sideBar.php") ?>


    <!-- Categories -->
    <header>
        <div class="tab-buttons d-flex justify-content-center align-items-center">
            <button class="tab-btn active" data-tab="daily">Daily & Weekly</button>
            <button class="tab-btn" data-tab="saving">Saving Challenge</button>
        </div>
    </header>

    <!-- Daily & Weekly Section -->
    <section id="daily" class="tab-content active">
        <!-- Daily Saving Challenge Card -->
        <div class="challenge-card">
            <h3>Daily Saving Challenge</h3>
            <div class="row" style="overflow-x: scroll; height: 160px;">
                <div class="challenge-item">Login to CtrlSave <button class="claim-btn">Claim</button></div>
                <div class="challenge-item">Add ₱5 to saving challenge <button class="claim-btn">Claim</button></div>
                <div class="challenge-item">No-coffee spend day <button class="in-progress">In Progress</button></div>
            </div>
        </div>

        <!-- Weekly Saving Challenge Card -->
        <div class="challenge-card mt-4">
            <h3>Weekly Saving Challenge</h3>
            <div class="row" style="overflow-x: scroll; height: 160px;">
                <div class="challenge-item">Save 500 this week <button class="in-progress">In Progress</button></div>
                <div class="challenge-item">Log 5 expenses <button class="in-progress">In Progress</button></div>
                <div class="challenge-item">Login to CtrlSave for a week <button class="claim-btn">Claim</button></div>
            </div>
        </div>
    </section>

    <!-- Saving Challenge Section -->
    <section id="saving" class="tab-content">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="position: relative; display: inline-block;">
                <img src="../../assets/img/challenge/alkansya.png" alt="Alkansya" style="width: 100px;">
                <div id="totalSaved" style="position: absolute; top: 70%; left: 48%; transform: translate(-50%, -50%);
                       font-weight: bold; font-size: 16px; font-weight: bold; color: #333;">
                    ₱0/200
                </div>
            </div>
            <div style="font-weight: bold; margin-top: 10px; color: #ffff;">Lvl. 1</div>
            <p style="font-weight: bold; margin-top: 10px; color: #ffff;">Click the amount to mark</p>
        </div>

        <div id="savingGrid"
            style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; text-align: center; max-width: 300px; margin: auto;">
        </div>
    </section>

    <!-- Daily Reward Modal -->
    <div class="modal fade" id="dailyRewardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4" style="background-color: #7DBF9E; border-radius: 20px;">
                <h5 class="fw-bold" style="color: #ffff; font-weight: bold; font-size: 30px;">Daily Reward</h5>
                <div class="position-relative d-inline-block my-3">
                    <img src="../../assets/img/challenge/greenXp.png" alt="Green XP Bottle" style="width: 150px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Reward Modal -->
    <div class="modal fade" id="weeklyRewardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4" style="background-color: #7DBF9E; border-radius: 20px;">
                <h5 class="fw-bold" style="color: #ffff; font-weight: bold; font-size: 30px;">Weekly Reward</h5>
                <div class="position-relative d-inline-block my-3">
                    <img src="../../assets/img/challenge/yellowXp.png" alt="Yellow XP Bottle" style="width: 150px;">
                </div>
            </div>
        </div>
    </div>

    <script>
        const buttons = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).classList.add('active');
            });
        });

        const claimButtons = document.querySelectorAll('.claim-btn');
        claimButtons.forEach(button => {
            button.addEventListener('click', () => {
                const parentText = button.parentElement.parentElement.querySelector('h3')?.textContent || '';
                const isDaily = parentText.includes('Daily');
                const modalId = isDaily ? 'dailyRewardModal' : 'weeklyRewardModal';

                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            });
        });

        // Saving Challenge Logic
        const savingAmounts = [5, 10, 5, 10, 5, 20, 5, 10, 20, 10];
        const targetAmount = 100;
        let totalSaved = 0;

        const grid = document.getElementById('savingGrid');
        const totalDisplay = document.getElementById('totalSaved');

        // Load state from localStorage
        let savedState = JSON.parse(localStorage.getItem('savingChallengeState')) || {
            clicked: Array(savingAmounts.length).fill(false),
            total: 0
        };

        totalSaved = savedState.total;
        totalDisplay.textContent = `₱${totalSaved}/${targetAmount}`;

        savingAmounts.forEach((amount, index) => {
            const btn = document.createElement('button');
            btn.textContent = amount;
            btn.style.border = '2px solid #FFC727';
            btn.style.borderRadius = '50%';
            btn.style.width = '50px';
            btn.style.height = '50px';
            btn.style.fontWeight = 'bold';
            btn.dataset.amount = amount;
            btn.dataset.index = index;

            // Initial state
            const isClicked = savedState.clicked[index];
            if (isClicked) {
                btn.style.backgroundColor = '#FFC727';
                btn.style.color = 'black';
            } else {
                btn.style.backgroundColor = 'white';
                btn.style.color = 'black';
            }

            btn.addEventListener('click', () => {
                const clicked = savedState.clicked[index];
                const amt = parseInt(amount);

                if (clicked) {
                    totalSaved -= amt;
                    savedState.clicked[index] = false;
                    btn.style.backgroundColor = 'white';
                    btn.style.color = 'black';
                } else {
                    totalSaved += amt;
                    savedState.clicked[index] = true;
                    btn.style.backgroundColor = '#FFC727';
                    btn.style.color = 'black';
                }

                savedState.total = totalSaved;
                totalDisplay.textContent = `₱${totalSaved}/${targetAmount}`;
                localStorage.setItem('savingChallengeState', JSON.stringify(savedState));
            });

            grid.appendChild(btn);
        });




    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>