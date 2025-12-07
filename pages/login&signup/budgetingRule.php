<?php
// Include backend logic for default budgeting rule
include("../../pages/login&signup/process/budgetingRuleBE.php");

// Currency setup (consistent with app-wide behavior)
$currencyCode = $_SESSION['currencyCode'] ?? 'PHP';
$symbol = ($currencyCode === 'PHP') ? '₱' : '$';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CtrlSave | Pick Budget Rule</title>
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;700&display=swap"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body,
        html {
            background-color: #44B87D;
            overflow: hidden;
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

        .rule-card {
            background: #F0F1F6;
            border: 2px solid #F6D25B;
            border-radius: 10px;
            padding: 2px;
            margin-bottom: 1rem;
            transition: border 0.3s;
        }

        input[type="checkbox"] {
            accent-color: #F6D25B;
            width: 20px;
            height: 20px;
            cursor: pointer;
            position: relative;
            border-color: #141313;
        }

        .form-check-input:checked {
            background-color: #F6D25B;
            border-color: #F6D25B;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='black' d='M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z'/%3E%3C/svg%3E");
        }

        .accordion-button::after {
            flex-shrink: 0;
            width: 1rem;
            height: 1rem;
            margin-left: auto;
            content: "";
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='green' viewBox='0 0 16 16'%3E%3Cpath d='M1 5l7 7 7-7H1z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: 1rem;
            transition: transform .2s ease-in-out;
        }

        .accordion-button:not(.collapsed)::after {
            transform: rotate(180deg);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='green' viewBox='0 0 16 16'%3E%3Cpath d='M1 5l7 7 7-7H1z'/%3E%3C/svg%3E");
        }

        .accordion-button:not(.collapsed) {
            background-color: #F0F1F6;
        }

        canvas {
            max-width: 200px;
            margin: auto;
        }

        /* Button */
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
        }

        .btn:hover {
            box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24),
                        0 17px 50px 0 rgba(0, 0, 0, 0.19);
        }

        .preferMineLink {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            margin-top: 15px;
        }

        /* ERROR TOAST */
        #errorToast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #E63946;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            width: 320px;
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
    <!-- show server-side error toast -->
    <?php if (!empty($error)) : ?>
        <div id="errorToast"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <!-- Navigation Bar -->
        <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
            <div class="container-fluid position-relative">
                <div class="d-flex align-items-start justify-content-start">
                    <a href="needsWants.php">
                        <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back"
                             style="height: 24px;" />
                    </a>
                </div>

                <div class="position-absolute top-50 start-50 translate-middle">
                    <h2 class="m-0 text-center navigationBarTitle" style="color:black;">
                        Budget Rule
                    </h2>
                </div>
            </div>
        </nav>

        <!-- Title -->
        <div class="container py-4">
            <h2>Do you follow a budgeting rule?</h2>

            <!-- Description -->
            <p class="desc mb-4">
                Here are some budgeting rules to help<br />
                you get started on your saving journey.
            </p>

            <!-- Accordion for Budgeting Rules -->
            <div class="row" style="overflow:scroll; height: 290px;">
                <div class="accordion" id="budgetingRulesAccordion">

                    <?php foreach ($rules as $r): ?>
                        <div class="accordion-item rule-card">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#rule<?= $r['id'] ?>"
                                        aria-expanded="false">
                                    <input class="form-check-input me-2 ruleCheck"
                                           type="checkbox"
                                           name="ruleOption"
                                           value="<?= $r['id'] ?>" />
                                    <?= htmlspecialchars($r['ruleName']) ?>
                                </button>
                            </h2>
                            <div id="rule<?= $r['id'] ?>"
                                 class="accordion-collapse collapse"
                                 data-bs-parent="#budgetingRulesAccordion">
                                <div class="accordion-body text-center">
                                    <canvas id="chart<?= $r['id'] ?>"></canvas>
                                    <p class="mt-3">
                                        <?= htmlspecialchars($r['ruleDescription']) ?>
                                    </p>

                                    <?php if (!empty($r['allocations'])): ?>
                                        <div class="mt-2">
                                            <?php foreach ($r['allocations'] as $alloc): ?>
                                                <p style="margin: 0; font-family:'Roboto', sans-serif;">
                                                    <?= htmlspecialchars($alloc['category']) ?>:
                                                    <?= (int)$alloc['percentage'] ?>%
                                                </p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>

            <!-- Next (use default rule) -->
            <div class="col-12 btNext d-flex justify-content-center align-items-center">
                <button id="nextBtn"
                        type="submit"
                        name="useDefault"
                        value="1"
                        class="btn btn-warning mt-4"
                        disabled>
                    Next
                </button>
            </div>

            <!-- Prefer my own (no default saving) -->
            <div class="col-12 noAccount d-flex justify-content-center align-items-center">
                <button type="submit"
                        name="preferMine"
                        value="1"
                        class="preferMineLink"
                        style="color: black; background:none; border:none;">
                    I prefer mine
                </button>
            </div>
        </div>
    </form>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Currency symbol available for any future UI needs
        const currencySymbol = "<?= $symbol ?>";

        const ruleData = <?= json_encode(array_values($rules)) ?>;
        const chartInstances = {};

        function createPieChart(id, data) {
            if (chartInstances[id]) return;

            const labels = data.map(d => d.category);
            const values = data.map(d => d.percentage);
            const colors = ['#F6D25B', '#C0C0C0', '#44B87D', '#8AB4F8', '#E86C6C'];

            const ctx = document.getElementById('chart' + id);
            if (!ctx) return;

            chartInstances[id] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors.slice(0, values.length)
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Render chart when accordion item is opened
        document.getElementById('budgetingRulesAccordion')
            .addEventListener('shown.bs.collapse', (event) => {
                const id = event.target.id.replace('rule', '');
                const rule = ruleData.find(r => r.id == id);
                if (rule && rule.allocations) {
                    createPieChart(id, rule.allocations);
                }
            });

        // Only one checkbox allowed & enable Next button when one selected
        const checks = document.querySelectorAll('input[type="checkbox"][name="ruleOption"]');
        const nextBtn = document.getElementById('nextBtn');

        checks.forEach(cb => {
            cb.addEventListener('change', () => {
                // Uncheck other checkboxes
                checks.forEach(other => {
                    if (other !== cb) other.checked = false;
                });
                const any = Array.from(checks).some(x => x.checked);
                nextBtn.disabled = !any;
            });
        });

        // Handle form submit: distinguish which button was used
        const form = document.querySelector('form');
        form.addEventListener('submit', (e) => {
            const submitter = e.submitter || document.activeElement;

            // 1) If "I prefer mine" clicked -> allow submit, BE will redirect to custom
            if (submitter && submitter.name === 'preferMine') {
                // Optional: clear rule selection so BE sees a clean POST
                checks.forEach(cb => cb.checked = false);
                return;
            }

            // 2) For "Next" button: require a checked rule
            const any = Array.from(checks).some(x => x.checked);
            if (!any) {
                e.preventDefault();
                showErrorToast('Please select a budget rule before proceeding.');
            }
        });

        function showErrorToast(msg) {
            const existing = document.getElementById('errorToast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'errorToast';
            toast.textContent = msg;
            document.body.appendChild(toast);
        }
    </script>
</body>

</html>
