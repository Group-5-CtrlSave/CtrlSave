<?php include("../../assets/shared/connect.php"); ?>
<?php session_start() ?>
<?php include("process/updateCharts.php") ?>

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

        <div class="scrollableContainer">
            <!-- Spending Bar Chart -->
            <div class="container my-1 monthlySpendingContainer">
                <div class="container py-1 text-center">
                    <h5 class="visualTitle m-0 p-0" id="spendingReport"></h5>
                </div>
                <div>
                    <canvas id="monthlySpendingChart"></canvas>
                </div>

            </div>
            <!-- PrevNext Button Container -->
            <div class="container my-3 d-flex justify-content-around">
                <button class="btn btnCustom" onclick="changeMonth(-1)" type="button"><img class="img-fluid previousBtn"
                        src="../../assets/img/shared/previous.png"></button>
                <p class="title py-2 m-0" style="font-size: 18px" id="monthYear"></p>
                <button class="btn btnCustom" onclick="changeMonth(1)" type="button" id="nextBtn"><img
                        class="img-fluid nextBtn" src="../../assets/img/shared/next.png"></button>
            </div>
            <!-- Reset Button -->
            <div class="container" id="resetButton" style="text-align: center; display: none">
                <button class="btn btn-lg resetBtn" type="button"><b>RESET</b></button>
            </div>


            <!-- Expenses Pie Chart -->
            <div class="container my-3 py-3 expensesChart">
                <div class="container py-1 text-center">
                    <h5 class="visualTitle m-0 p-0">Expenses Chart</h5>
                </div>
                <div>
                    <canvas id="expensesChart" height="200" width="200"></canvas>
                </div>

            </div>

            <!-- Month Expenses -->
            <div class="container my-3 py-3 tableContainer">
                <div class="container py-1 text-center">
                    <h5 class="visualTitle m-0 p-0" id="expensesTable"></h5>

                </div>
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
                            <?php foreach ($expenseList as $expenseCategory) {
                                $categoryAmount = $expenseCategory["amount"];
                                $percentage = ($overallTotal > 0 ) ? $categoryAmount / $overallTotal * 100 : 0 ;
                                $percentage = round($percentage, 2 )
                                ?>
                                <tr>
                                    <td><strong><?php echo $expenseCategory['categoryName']?></strong></td>
                                    <td><?php echo $categoryAmount ?></td>
                                    <td><?php echo $percentage?>%</td>
                                </tr>
                                <?php

                            } ?>


                        </tbody>
                    </table>
                </div>



            </div>
            <!-- Analysis and Recommendations -->
            <div class="container analysisRecommendations my-3 py-3">
                <div class="container p-0 text-center">
                    <h5 class="visualTitle m-0 p-0" id="analysisForecast">Analysis and Recommendations</h5>

                </div>
                <p>Based on the current financial report, you have spent a total of 10,000 pesos for the month of May.
                </p>

                <p>Your top 3 spending categories are:</p>
                <p> 1. Groceries – 35%<br>
                    2. Dining Out – 20%<br>
                    3. Electricity – 15%</p>

                <p>You spent 20% on Dining Out, which is higher than your target limit of 15%. Because of this, you may
                    have
                    spent less on Groceries or saved less money.</p>

                <p>To stay within your budget, try reducing your Dining Out expenses and use the extra money to buy more
                    groceries or increase your savings.</p>

                <div class="container p-0 text-center">
                    <h5 class="visualTitle m-0 p-0">Additional Tips</h5>
                </div>

                <p>Try this additional tips based on your category:</p>

                <p>1. <strong>Eat Out During Promos or Student Discount Hours</strong><br>
                    2. <strong>Avoid including add-ons</strong><br>
                    3. <strong>Try to bring your own drink/water</strong></p>
            </div>

        </div>







    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Global Variables
        let count = 0;
        let nextBtn = document.getElementById("nextBtn");
        let resetBtn = document.getElementById("resetButton");
        let monthYear = document.getElementById("monthYear");
        let spendingReport = document.getElementById("spendingReport")
        let expensesTable = document.getElementById("expensesTable")
        let analysisForecast = document.getElementById("analysisForecast")

        function changeMonth(num) {
            // Get the presentDate
            let presentDate = new Date()
            // Pass the present Date in a newDate
            let newDate = new Date(presentDate)

            // Set the counter
            count += num;

            // Prevent from going above 6 months
            if (count >= 3) {
                nextBtn.setAttribute("disabled", true)

            } else {
                nextBtn.removeAttribute("disabled")
            }



            // Set the new Month to the newDate
            newDate.setMonth(newDate.getMonth() + count);

            // Get the Present Month and Year
            let presentMonth = presentDate.getMonth() + 1;
            let presentYear = presentDate.getFullYear();
            // Get the New Month and New Year
            let newMonth = newDate.getMonth() + 1;
            let newYear = newDate.getFullYear();

            // Check if not in the Present Date


            if (newMonth != presentMonth || newYear != presentYear) {

                resetBtn.style.display = "block";

            }
            if (newMonth == presentMonth && newYear == presentYear) {
                resetBtn.style.display = "none"
            }

            // Show the Months in Texts
            let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            if (count == 1) {
                monthYear.innerHTML = "1 Month Forecast"
                spendingReport.innerHTML = "1 Month Financial Forecast"
                expensesTable.innerHTML = "1 Month Forecast Expenses"
                analysisForecast.innerHTML = "Financial Forecast"
            } else if (count == 2) {
                monthYear.innerHTML = "3 Months Forecast"
                spendingReport.innerHTML = "3 Months Financial Forecast"
                expensesTable.innerHTML = "3 Months Forecast Expenses"
                analysisForecast.innerHTML = "Financial Forecast"
            } else if (count == 3) {
                monthYear.innerHTML = "6 Months Forecast"
                spendingReport.innerHTML = "6 Months Financial Forecast"
                expensesTable.innerHTML = "6 Months Forecast Expenses"
                analysisForecast.innerHTML = "Financial Forecast"
            } else {
                monthYear.innerHTML = months[newMonth - 1] + " " + newYear;
                spendingReport.innerHTML = "Monthly Spending Report"
                expensesTable.innerHTML = months[newMonth - 1] + " " + newYear + " " + "Expenses"
                analysisForecast.innerHTML = "Analysis and Recommendations"


            }














        }
    </script>

    <script>
        function getMonth() {
            let presentDate = new Date()
            presentMonth = presentDate.getMonth() + 1;
            presentYear = presentDate.getFullYear();

            let months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

            let monthYear = document.getElementById('monthYear')
            monthYear.innerHTML = months[presentMonth - 1] + " " + presentYear;

            spendingReport.innerHTML = "Monthly Spending Report"
            expensesTable.innerHTML = months[presentMonth - 1] + " " + presentYear + " " + "Expenses"

        }

        getMonth();
    </script>

    <!-- Bar Chart -->

    <script>
        const monthlySpending = <?php echo $monthlyDataJSON; ?>;

        const ctx = document.getElementById('monthlySpendingChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Spending',
                    data: monthlySpending,
                    backgroundColor: '#77D09A',
                    borderRadius: 5,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7
                }]
            },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1000
            },
            grid: {
                color: '#c0c0c0',
                lineWidth: 2
            }
        },
        x: {
            grid: {
                display: false
            },
            ticks: {
                autoSkip: false,      
                maxRotation: 45,      
                minRotation: 0,
                color: '#000',
                font: {
                    size: 14,        
                    family: 'Roboto, Arial, sans-serif'
                },
                padding: 5        
            }
        }
    }
}
        });
    </script>


    <!-- Pie Chart -->

    <script>
        const expensesCtx = document.getElementById('expensesChart');
        const expenseCategories = <?php echo $categoriesJSON; ?>;
        const dataCategories = <?php echo $dataJSON; ?>;

        new Chart(expensesCtx, {
            type: 'doughnut',
            data: {
                labels: expenseCategories,
                datasets: [{
                    data: dataCategories,
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

                },
                cutout: '60%',
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>



</body>

</html>