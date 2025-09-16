<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        body,
        html {
            background-color: #44B87D;
        }

        h2 {
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            font-size: clamp(2rem, 1vw, 1rem);
            color: #ffff;
            text-align: center;
        }

        .desc {
            font-family: "Roboto", sans-serif;
            font-size: clamp(1.2rem, 1vw, 1rem);
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

        .preferMineLink {
            color: black;
            font-family: "Poppins", sans-serif;
            font-weight: bold;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <!-- No Logo Navigation Bar -->
    <nav class="bg-white px-4 d-flex align-items-center justify-content-between position-relative shadow"
        style="height: 72px;">
        <a href="needsWants.php" class="text-decoration-none">
            <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
        </a>
        <h5 class="position-absolute start-50 translate-middle-x m-0 fw-bold text-dark"
            style="font-family: Poppins, sans-serif;">
            Budget Rule
        </h5>
    </nav>

    <!-- Title -->
    <div class="container py-4">
        <h2>Do you follow a budgeting rule?</h2>

        <!-- Description -->
        <p class="desc mb-4">Here are some budgeting rules to help<br />you get started on your saving journey.</p>

        <!-- Accordion for Budgeting Rules -->
        <div class="row" style="overflow:scroll; height: 290px;">
            <div class="accordion" id="budgetingRulesAccordion">

                <!-- 50/30/20 -->
                <div class="accordion-item rule-card">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#rule1" aria-expanded="false">
                            <input class="form-check-input me-2" type="checkbox" name="ruleOption" />
                            50/30/20 Rule
                        </button>
                    </h2>
                    <div id="rule1" class="accordion-collapse collapse" data-bs-parent="#budgetingRulesAccordion">
                        <div class="accordion-body text-center">
                            <canvas id="chart1"></canvas>
                            <p class="mt-3">
                                The 50/30/20 rule allocates your income: 50% for needs, 30% for wants, and 20% for
                                savings.
                                It’s popular for beginners because of its simplicity and balance.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 60/20/20 -->
                <div class="accordion-item rule-card">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#rule2" aria-expanded="false">
                            <input class="form-check-input me-2" type="checkbox" name="ruleOption" />
                            60/20/20 Rule
                        </button>
                    </h2>
                    <div id="rule2" class="accordion-collapse collapse" data-bs-parent="#budgetingRulesAccordion">
                        <div class="accordion-body text-center">
                            <canvas id="chart2"></canvas>
                            <p class="mt-3">
                                The 60/20/20 rule suggests using 60% of your income for essential expenses, and 20% each
                                for
                                savings and discretionary spending.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 80/20 -->
                <div class="accordion-item rule-card">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#rule3" aria-expanded="false">
                            <input class="form-check-input me-2" type="checkbox" name="ruleOption" />
                            80/20 Rule
                        </button>
                    </h2>
                    <div id="rule3" class="accordion-collapse collapse" data-bs-parent="#budgetingRulesAccordion">
                        <div class="accordion-body text-center">
                            <canvas id="chart3"></canvas>
                            <p class="mt-3">
                                The 80/20 rule is simple: save 20% of your income and spend the remaining 80% on
                                everything
                                else.
                                It's ideal for those who want a flexible plan.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Button -->
        <div class="col-12 btNext mt-4 d-flex justify-content-center align-items-center">
            <a href="done.php"><button type="submit" class="btn btn-warning mt-4">Next</button></a>
        </div>

        <div class="col-12 mb-3 mt-2 noAccount d-flex justify-content-center align-items-center">
            <a href="percentage.php" class="preferMineLink" style="color: black;">I prefer mine</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const chartConfigs = {
            chart1: { data: [50, 30, 20], labels: ['Needs', 'Wants', 'Savings'], colors: ['#F6D25B', '#C0C0C0', '#44B87D'] },
            chart2: { data: [60, 20, 20], labels: ['Needs', 'Wants', 'Savings'], colors: ['#F6D25B', '#C0C0C0', '#44B87D'] },
            chart3: { data: [80, 20], labels: ['Spending', 'Savings'], colors: ['#F6D25B', '#44B87D'] }
        };

        const chartInstances = {};

        function createPieChart(id) {
            if (chartInstances[id]) return;
            const config = chartConfigs[id];
            chartInstances[id] = new Chart(document.getElementById(id), {
                type: 'pie',
                data: {
                    labels: config.labels,
                    datasets: [{
                        data: config.data,
                        backgroundColor: config.colors
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: true, position: 'bottom' } }
                }
            });
        }

        const accordion = document.getElementById('budgetingRulesAccordion');
        accordion.addEventListener('shown.bs.collapse', (event) => {
            const canvas = event.target.querySelector('canvas');
            if (canvas) createPieChart(canvas.id);
        });

        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="ruleOption"]');
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                checkboxes.forEach(cb => {
                    if (cb !== checkbox) cb.checked = false;
                });
            });
        });
    </script>
</body>

</html>