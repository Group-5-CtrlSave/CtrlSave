<?php
session_start();
// userID
$userID = '';
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
}
?>


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
                <button onclick="window.location.href='cointrol.php'" class="btn btn-lg resetBtn"
                    type="button"><b>RESET</b></button>
            </div>


            <!-- Expenses Pie Chart -->
            <div class="container my-3 py-3 expensesChart">
                <div class="container py-1 text-center">
                    <h5 class="visualTitle m-0 p-0">Expenses Structure</h5>
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
                    <table class="table table-bordered align-middle mb-0" id="monthlyTable">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Category</th>
                                <th>Expenses</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">


                        </tbody>
                    </table>
                </div>



            </div>
            <!-- Analysis and Recommendations -->
            <div class="container analysisRecommendations my-3 py-3">
                <div class="container p-0 text-center">
                    <h5 class="visualTitle m-0 p-0" id="analysisForecast">Analysis and Recommendations</h5>
                    <div class="container" id="testFetch"></div>

                </div>
                <p>Based on the current financial report, you have spent a total of 10,000 pesos for the month of May.
                </p>

                <!-- Top Spending Category -->

                <div class="container" id="topSpendingCateg">

                </div>




                <!-- Correlation of Categories -->

                <p class="correlation">You spent 20% on Dining Out, which is higher than your target limit of 15%.
                    Because of this, you may
                    have spent less on Groceries or saved less money. To stay within your budget,
                    try reducing your Dining Out expenses and use the extra money to buy more
                    groceries or increase your savings.</p>


            </div>

        </div>







    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- No Data for Charts -->
    <script>
        Chart.register({
            id: 'noDataPlugin',
            beforeDraw(chart) {
                const data = chart.data.datasets[0].data;

                if (!data || data.length === 0 || data.every(v => v === 0)) {
                    const ctx = chart.ctx;
                    const width = chart.width;
                    const height = chart.height;

                    chart.clear();

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = '18px Roboto';
                    ctx.fillStyle = '#666';
                    ctx.fillText('No data available', width / 2, height / 2);
                    ctx.restore();
                }
            }
        });

    </script>



    <!-- Bar Chart -->
    <script>


        const ctx = document.getElementById('monthlySpendingChart');

        let monthlyBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Spending',
                    data: Array(12).fill(0),
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


        let monthlyPieChart = new Chart(expensesCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [],
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

    <!-- Generate Random Colors -->

    <script>
        function generateRandomColors(num) {
            const colors = [];
            const step = 360 / num;
            for (let i = 0; i < num; i++) {
                const hue = i * step;
                colors.push(`hsl(${hue}, 40%, 60%)`);
            }
            return colors;
        }
    </script>


    <!-- Fetch Month via AJAX -->
    <script>
        function fetchMonthYear(mnth, yr) {
            let fetchMonth = mnth;
            let fetchYear = yr;

            fetch(`process/updateCharts.php?month=${fetchMonth}&year=${fetchYear}`)
                .then(response => response.json())
                .then(data => {


                    // Update the Bar Chart
                    document.getElementById('testFetch').innerHTML = data.pieChartLabels;
                    monthlyBarChart.data.datasets[0].data = data.barChartData;
                    monthlyBarChart.update();

                    // Update the Pie Chart

                    const catNum = data.pieChartLabels.length;
                    monthlyPieChart.data.labels = data.pieChartLabels;
                    monthlyPieChart.data.datasets[0].data = data.pieChartData;
                    monthlyPieChart.data.datasets[0].backgroundColor = generateRandomColors(catNum);
                    monthlyPieChart.update();

                    // Update the Table

                    const tbody = document.querySelector("#monthlyTable tbody");
                    tbody.innerHTML = '';

                    if (!data.tableData || data.tableData.length === 0) {
                        tbody.innerHTML = `<tr class="text-center"><td colspan=3>No Data Available</td></tr>`;
                    } else {
                        data.tableData.forEach(expense => {
                            const row = document.createElement('tr');
                            row.classList.add('text-center');
                            row.innerHTML = `
                            <td><strong>${expense.categoryName}<strong></td> 
                            <td>${expense.amount}</td> 
                            <td>${expense.percentage}%</td> 
                            `;
                            tbody.appendChild(row);
                        });
                    }

                    // Update the Top Spending Categories
                    let topCategories = document.getElementById("topSpendingCateg");
                     topCategories.innerHTML = "";
                    if (data.topCategories && data.topCategories.length > 0) {
                         topCategories.innerHTML = "<p>Your Top Spending Categories for this month:</p><p>" + data.topCategories.join("<br>") + "</p>";
                    }



                })
                .catch(error => console.error('Error', error))
        }
    </script>


    <!-- Change Month -->
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

            fetchMonthYear(newMonth, newYear);

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


    <!-- Get Present Month -->
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

            fetchMonthYear(presentMonth, presentYear);

        }

        getMonth();
    </script>



</body>

</html>