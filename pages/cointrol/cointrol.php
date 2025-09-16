<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CtrlSave | Cointrol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link rel="stylesheet" href="../../assets/css/cointrol.css">
    <link rel="icon" href="../../assets/img/shared/logo_s.png">
</head>

<body>

    <?php include("../../assets/shared/navigationBar.php") ?>

    <?php include("../../assets/shared/sideBar.php") ?>

  


    <!-- Content -->

    <div class="container-fluid mainContainer">

        <div class="container-fluid p-3">
            <h2 class="title m-0">Cointrol</h2>
        </div>

        <div class="container my-1 monthlySpendingContainer">
            <div>
                <canvas id="monthlySpendingChart"></canvas>
            </div>

        </div>

        <div class="container my-3 py-3 expensesChart">
            <div>
                <canvas id="expensesChart" height="200" width="200"></canvas>
            </div>

        </div>

        <div class="container my-3 py-3 tableContainer">
            <h5 class="mb-4 text-success fw-bold">Last Month Expenses</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>Category</th>
                            <th>Expenses</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td><strong>Groceries</strong></td>
                            <td>3,500</td>
                            <td>35%</td>
                        </tr>
                        <tr>
                            <td><strong>Dining Out</strong></td>
                            <td>2,000</td>
                            <td>20%</td>
                        </tr>
                        <tr>
                            <td><strong>Electricity</strong></td>
                            <td>2,000</td>
                            <td>15%</td>
                        </tr>
                        <tr>
                            <td><strong>Transportation</strong></td>
                            <td>1,500</td>
                            <td>15%</td>
                        </tr>
                        <tr>
                            <td><strong>Savings</strong></td>
                            <td>1,000</td>
                            <td>10%</td>
                        </tr>
                    </tbody>
                </table>
            </div>



        </div>

        <div class="container analysisRecommendations my-3 py-3">
            <h5 class="my-1 text-success fw-bold">Analysis and Recommendations</h5>

            <p>Based on the current financial report, you have spent a total of 10,000 pesos for the month of May.</p>

            <p>Your top 3 spending categories are:</p>
            <p> 1. Groceries – 35%<br>
                2. Dining Out – 20%<br>
                3. Electricity – 15%</p>

            <p>You spent 20% on Dining Out, which is higher than your target limit of 15%. Because of this, you may have
                spent less on Groceries or saved less money.</p>

            <p>To stay within your budget, try reducing your Dining Out expenses and use the extra money to buy more
                groceries or increase your savings.</p>

            <h5 class="my-1 text-success fw-bold">Additional Tips:</h5>

            <p>Try this additional tips based on your category:</p>

            <p>1. <strong>Eat Out During Promos or Student Discount Hours</strong><br>
                2. <strong>Avoid including add-ons</strong><br>
                3. <strong>Try to bring your own drink/water</strong></p>
        </div>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('monthlySpendingChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'Monthly Spending',
                    data: [30, 15, 60, 65, 60, 65, 43],
                    backgroundColor: '#77D09A',
                    borderRadius: 5,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Monthly Spending Report',
                        color: '#2E7D32',
                        font: {
                            size: 24,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 30
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        },
                        grid: {
                            color: '#c0c0c0',
                            lineWidth: 2
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

    <script>
        const expensesCtx = document.getElementById('expensesChart');

        new Chart(expensesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Savings', 'Dining out', 'Electricity', 'Transportation', 'Groceries'],
                datasets: [{
                    data: [10, 20, 20, 15, 35],
                    backgroundColor: ['#2a9d8f', '#f4a261', '#2ecc71', '#45a29e', '#208b8d'],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            color: '#000',
                            font: {
                                size: 14
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Expenses Structure',
                        font: {
                            size: 20,
                            weight: 'bold'
                        },
                        color: '#2E7D32',
                        padding: {
                            bottom: 20
                        }
                    }
                },
                cutout: '60%',
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>



</body>

</html>