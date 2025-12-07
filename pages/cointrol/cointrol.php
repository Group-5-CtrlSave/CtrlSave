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


                </div>
                <!-- Total Spent -->
                <div class="container my-3" id="monthlyTotalSpent">

                </div>


                <!-- Top Spending Category -->

                <div class="container my-3" id="topSpendingCateg">

                </div>

                <!-- Positive Category -->

                <div class="container my-3" id="positive">

                </div>




                <!-- Tracking Category -->

                <div class="container my-3" id="tracking">

                </div>



                <!-- Positive Category -->

                <div class="container my-3" id="positiveSaving">

                </div>

                <!-- No Saving Category -->

                <div class="container my-3" id="noSaving">

                </div>




                <!-- Overspent Category -->
                <div class="container my-3" id="overSpentCateg">

                </div>

                <!-- Oversave Category -->
                <div class="container my-3" id="overSaveCateg">

                </div>


                <!-- Correlation of Categories -->
                <div class="container my-3" id="correlationInsight">

                </div>


                <!-- Correlation of Categories -->
                <div class="container my-3" id="recommendation">

                </div>



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

                    // Update the Total Spent
                    let monthlyTotalSpent = document.getElementById("monthlyTotalSpent");
                    monthlyTotalSpent.innerHTML = "";
                    if (data.analysis && data.analysis.length > 0) {
                        monthlyTotalSpent.innerHTML = "<p>" + data.analysis + "</p>"
                    }

                    // Update the Top Spending Categories
                    let topCategories = document.getElementById("topSpendingCateg");
                    topCategories.innerHTML = "";
                    if (data.topCategories && data.topCategories.length > 0) {
                        topCategories.innerHTML = "<b>Your Top Spending Categories for this month:</b><p>" + data.topCategories.join("<br>") + "</p>";
                    }

                    // Update Correlation Insight 
                    let correlationInsight = document.getElementById("correlationInsight");
                    correlationInsight.innerHTML = "";
                    if (data.correlationInsight && data.correlationInsight.length > 0) {
                        data.correlationInsight.forEach(element => {
                            correlationInsight.innerHTML += `<p> ${element} </p>`
                        });

                    }

                    // Update Overspending Insight
                    let overSpentCateg = document.getElementById("overSpentCateg");
                    overSpentCateg.innerHTML = "";
                    if (data.overspendingInsight && data.overspendingInsight.length > 0) {
                        overSpentCateg.innerHTML += `<b>Your Overspending Categories:</b>`;
                        data.overspendingInsight.forEach(element => {
                            overSpentCateg.innerHTML += `<p>${element}</p>`;
                        });

                        // Add daily insights if available
                        if (data.daily_overspending && data.daily_overspending.length > 0) {
                            data.daily_overspending.forEach(element => {
                                overSpentCateg.innerHTML += `<p style="font-style: italic; color: #555;">(Today) ${element}</p>`;
                            });
                        }
                    }

                    // Positive Insight
                    let positive = document.getElementById("positive");
                    positive.innerHTML = "";
                    if (data.positiveInsight && data.positiveInsight.length > 0) {
                        data.positiveInsight.forEach(element => {
                            positive.innerHTML += `<p>${element}</p>`;
                        });

                        // Add daily insights if available
                        if (data.daily_positive && data.daily_positive.length > 0) {
                            data.daily_positive.forEach(element => {
                                positive.innerHTML += `<p style="font-style: italic; color: #555;">(Today) ${element}</p>`;
                            });
                        }
                    }

                    // Tracking Insight
                    let tracking = document.getElementById("tracking");
                    tracking.innerHTML = "";
                    if (data.trackingInsight && data.trackingInsight.length > 0) {
                        tracking.innerHTML = "<p>" + data.trackingInsight.join("<br>") + "</p>";

                        // Add daily insights if available
                        if (data.daily_tracking && data.daily_tracking.length > 0) {
                            data.daily_tracking.forEach(element => {
                                tracking.innerHTML += `<p style="font-style: italic; color: #555;">(Today) ${element}</p>`;
                            });
                        }
                    }

                    // Oversaving Insight
                    let overSaveCateg = document.getElementById("overSaveCateg");
                    overSaveCateg.innerHTML = "";
                    if (data.oversavingInsight && data.oversavingInsight.length > 0) {
                        overSaveCateg.innerHTML = "<p>" + data.oversavingInsight.join("<br>") + "</p>";

                        // Add daily insights if available
                        if (data.daily_oversaving && data.daily_oversaving.length > 0) {
                            data.daily_oversaving.forEach(element => {
                                overSaveCateg.innerHTML += `<p style="font-style: italic; color: #555;">(Today) ${element}</p>`;
                            });
                        }
                    }

                    // Positive Saving Insight
                    let positiveSaving = document.getElementById("positiveSaving");
                    positiveSaving.innerHTML = "";
                    if (data.positiveSavingInsight && data.positiveSavingInsight.length > 0) {
                        positiveSaving.innerHTML = "<b>Saving Goals:</b>";
                        data.positiveSavingInsight.forEach(element => {
                            positiveSaving.innerHTML += `<p>${element}</p>`;
                        });

                        // Add daily positive saving insights if available
                        if (data.daily_positiveSaving && data.daily_positiveSaving.length > 0) {
                            data.daily_positiveSaving.forEach(element => {
                                positiveSaving.innerHTML += `<p style="font-style: italic; color: #555;">(Today) ${element}</p>`;
                            });
                        }
                    }

                    // No Saving Insight
                    let noSaving = document.getElementById("noSaving");
                    noSaving.innerHTML = "";
                    if (data.noSavingInsight && data.noSavingInsight.length > 0) {
                        noSaving.innerHTML = "<b>Saving Goals:</b>";
                        data.noSavingInsight.forEach(element => {
                            noSaving.innerHTML += `<p>${element}</p>`;
                        });

                        // Add daily no saving insights if available
                        if (data.daily_noSaving && data.daily_noSaving.length > 0) {
                            data.daily_noSaving.forEach(element => {
                                noSaving.innerHTML += `<p style="font-style: italic; color: #555;">(Today) ${element}</p>`;
                            });
                        }
                    }


                    // Recommendation 
                    let recommendation = document.getElementById("recommendation")
                    recommendation.innerHTML = "";
                    if (data.recommendationInsight && data.recommendationInsight.length > 0) {
                        recommendation.innerHTML += `<b>Recommendations:</b>`;
                        recommendation.innerHTML += "<p>" + data.recommendationInsight + "</p>"
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