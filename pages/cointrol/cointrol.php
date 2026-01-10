<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../../pages/login&signup/login.php");
    exit;
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
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
                <!-- Forecasting -->
                <div class="container my-3" id="forecastingDecrease">

                </div>
                <div class="container my-3" id="forecastingIncrease">

                </div>
                <div class="container my-3" id="forecastingStable">

                </div>


                <!-- Total Spent -->
                <div class="container my-3" id="monthlyTotalSpent">

                </div>

                <!-- Unallocated Budget -->
                <div class="container my-3" id="unallocatedBudget">

                </div>

                <!-- Top Spending Category -->

                <div class="container my-3" id="topSpendingCateg">

                </div>



                <!-- No Overspending (Positive) -->

                <div class="container my-3" id="noOverSpendingMessage">

                </div>


                <div class="container my-3" id="noOverSpending">

                </div>

                <!-- Overspending (Negative) -->

                <div class="container my-3" id="overSpendingMessage">

                </div>

                <div class="container my-3" id="overSpending">

                </div>

                <!-- No Saving Category -->

                <div class="container my-3" id="noSaving">

                </div>

                <!-- Positive Saving -->

                <div class="container my-3" id="positiveSaving">

                </div>

                <!-- Correlation of Categories -->
                <div class="container my-3" id="correlationInsight">

                </div>


                <!-- Correlation of Categories -->
                <div class="container my-3" id="recommendation">

                </div>





                <!-- Positive Category -->

                <div class="container my-3" id="positive">

                </div>




                <!-- Tracking Category -->

                <div class="container my-3" id="tracking">

                </div>











                <!-- Overspent Category -->
                <div class="container my-3" id="overSpentCateg">

                </div>

                <!-- Oversave Category -->
                <div class="container my-3" id="overSaveCateg">

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


            fetch(`process/updateCharts.php?month=${fetchMonth}&year=${fetchYear}&count=${count}`)
                .then(response => response.json())
                .then(data => {

                    actualBarData = data.barChartData;
                    forecastBarData = data.forecastBarData;

                    console.log(count);

                    if (count > 0) {
                        // Update the Bar Chart
                        monthlyBarChart.data.datasets[0].data = data.forecastBarData;
                        monthlyBarChart.update();
                    } else {
                        monthlyBarChart.data.datasets[0].data = data.barChartData;
                        monthlyBarChart.update();
                    }




                    // Update the Pie Chart

                    // Update the Pie Chart ONLY if count == 0 (actual month)
                    const pieChartContainer = document.querySelector('.expensesChart');

                    if (count === 0 && data.pieChartLabels && data.pieChartLabels.length > 0) {
                        pieChartContainer.style.display = "block"; // show
                        const catNum = data.pieChartLabels.length;
                        monthlyPieChart.data.labels = data.pieChartLabels;
                        monthlyPieChart.data.datasets[0].data = data.pieChartData;
                        monthlyPieChart.data.datasets[0].backgroundColor = generateRandomColors(catNum);
                        monthlyPieChart.update();
                    } else {
                        pieChartContainer.style.display = "none"; // hide when forecast
                    }

                    // Update the Table

                    const tbody = document.querySelector("#monthlyTable tbody");
                    tbody.innerHTML = '';

                    if (count > 0) {
                        // === FORECAST MODE ===
                        let actualData = data.barChartData;      // actual data for comparison
                        let forecastData = data.forecastBarData; // forecasted values

                        // Find the last actual month before forecast
                        let lastActualIndex = actualData.findIndex(v => v > 0);
                        if (lastActualIndex === -1) lastActualIndex = 0;

                        for (let i = 0; i < 12; i++) {
                            if (forecastData[i] === 0) continue; // skip months without forecast

                            let monthName = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"][i];

                            // Determine previous month value for percentage comparison
                            let prevValue;
                            if (i === 0 || actualData[i - 1] === 0) {
                                // Use last available actual month if no previous month
                                prevValue = actualData[lastActualIndex] || forecastData[i];
                            } else {
                                prevValue = actualData[i - 1];
                            }

                            let currValue = forecastData[i];

                            // Calculate percentage change safely
                            let percentChange = '-';
                            if (prevValue && prevValue != 0) {
                                percentChange = ((currValue - prevValue) / prevValue) * 100;
                                percentChange = percentChange.toFixed(1);
                            }

                            const row = document.createElement('tr');
                            row.classList.add('text-center');
                            row.innerHTML = `
            <td><strong>${monthName} ${(new Date().getFullYear())}</strong></td>
            <td>${currValue}</td>
            <td>${percentChange}%</td>
        `;
                            tbody.appendChild(row);
                        }

                        if (!tbody.hasChildNodes()) {
                            tbody.innerHTML = `<tr class="text-center"><td colspan=3>No Data Available</td></tr>`;
                        }

                    } else {
                        // === ACTUAL DATA MODE ===
                        if (!data.tableData || data.tableData.length === 0) {
                            tbody.innerHTML = `<tr class="text-center"><td colspan=3>No Data Available</td></tr>`;
                        } else {
                            data.tableData.forEach(expense => {
                                const row = document.createElement('tr');
                                row.classList.add('text-center');
                                row.innerHTML = `
                <td><strong>${expense.categoryName}</strong></td> 
                <td>${expense.amount}</td> 
                <td>${expense.percentage}%</td> 
            `;
                                tbody.appendChild(row);
                            });
                        }
                    }
                    // Forecasting
                    let forecastingDecrease = document.getElementById("forecastingDecrease");

                    // Hide first by default
                    forecastingDecrease.style.display = "none";
                    forecastingDecrease.innerHTML = "";

                    // Show only for 1-month forecast
                    if (count === 1 && data.forecastingDecrease && data.forecastingDecrease.length > 0) {
                        forecastingDecrease.style.display = "block";

                        // Only show the first message
                        forecastingDecrease.innerHTML = `<p>${data.forecastingDecrease[0]}</p>`;
                    }  else if (count === 2 && data.forecastingDecrease && data.forecastingDecrease.length > 0) {
                        forecastingDecrease.style.display = "block";

                        // Only show the first message
                        forecastingDecrease.innerHTML = `<p>${data.forecastingDecrease[1]}</p>`;
                    } else if (count === 3 && data.forecastingDecrease && data.forecastingDecrease.length > 0) {
                        forecastingDecrease.style.display = "block";

                        // Only show the first message
                        forecastingDecrease.innerHTML = `<p>${data.forecastingDecrease[2]}</p>`;
                    } 

                    // Update the Total Spent
                    let monthlyTotalSpent = document.getElementById("monthlyTotalSpent");
                    monthlyTotalSpent.innerHTML = "";
                    if (data.totalSpent && data.totalSpent.length > 0) {
                        monthlyTotalSpent.innerHTML = "<p>" + data.totalSpent + "</p>"
                    }

                    // Unallocated Budget message
                    let unallocatedBudget = document.getElementById("unallocatedBudget");
                    unallocatedBudget.innerHTML = "";
                    if (data.unallocatedBudget && data.unallocatedBudget.length > 0) {
                        unallocatedBudget.innerHTML = "<p>" + data.unallocatedBudget + "</p>"
                    }


                    // Update the Top Spending Categories
                    let topCategories = document.getElementById("topSpendingCateg");
                    topCategories.innerHTML = "";
                    if (data.topCategories && data.topCategories.length > 0) {
                        topCategories.innerHTML = "<b>Your Top Spending Categories for this month:</b><p>" + data.topCategories.join("<br>") + "</p>";
                    }

                    // No Overspending Message 
                    let noOverSpendingMessage = document.getElementById("noOverSpendingMessage");
                    noOverSpendingMessage.innerHTML = "";
                    if (data.noOverSpendingMessage && data.noOverSpendingMessage.length > 0) {
                        noOverSpendingMessage.innerHTML += "<p>" + data.noOverSpendingMessage + "</p>"
                    }

                    // No Overspending Insight
                    let noOverSpending = document.getElementById("noOverSpending");
                    noOverSpending.innerHTML = "";
                    if (data.noOverSpending && data.noOverSpending.length > 0) {
                        noOverSpending.innerHTML += "<p>" + data.noOverSpending + "</p>"
                    }

                    // Overspending Message
                    let overSpendingMessage = document.getElementById("overSpendingMessage");
                    overSpendingMessage.innerHTML = "";
                    if (data.dailyOverspendingmessage && data.dailyOverspendingmessage.length > 0) {
                        data.dailyOverspendingmessage.forEach(element => {
                            overSpendingMessage.innerHTML += `<p>${element}</p>`;
                        });
                    }



                    //Overspending Insight
                    let overSpending = document.getElementById("overSpending");
                    overSpending.innerHTML = "";
                    if (data.dailyOverspending && data.dailyOverspending.length > 0) {
                        overSpending.innerHTML += "<p>" + data.dailyOverspending + "</p>";
                    }


                    // Recommendation 
                    let recommendation = document.getElementById("recommendation")
                    recommendation.innerHTML = "";
                    if (data.recommendationInsight && data.recommendationInsight.length > 0) {
                        recommendation.innerHTML += `<b>Recommendations:</b>`;
                        recommendation.innerHTML += "<p>" + data.recommendationInsight + "</p>"
                    }

                    // Correlation Insight 
                    let correlationInsight = document.getElementById("correlationInsight");
                    correlationInsight.innerHTML = "";
                    if (data.correlationInsight && data.correlationInsight.length > 0) {
                        data.correlationInsight.forEach(element => {
                            correlationInsight.innerHTML += `<p> ${element} </p>`
                        });

                    }







                    // Oversaving Insight
                    let overSaveCateg = document.getElementById("overSaveCateg");
                    overSaveCateg.innerHTML = "";
                    if (data.oversavingInsight && data.oversavingInsight.length > 0) {
                        overSaveCateg.innerHTML = "<p>" + data.oversavingInsight + "</p>";
                    } else if (data.dailyOversaving && data.dailyOversaving.length > 0) {
                        data.dailyOversaving.forEach(element => {
                            overSaveCateg.innerHTML += `<p>Today: ${element}</p>`;
                        });
                    }

                    // Positive Insight
                    let positive = document.getElementById("positive");
                    positive.innerHTML = "";
                    if (data.positiveInsight && data.positiveInsight.length > 0) {
                        data.positiveInsight.forEach(element => {
                            positive.innerHTML += `<p>${element}</p>`;
                        });
                    } else if (data.dailyPositive && data.dailyPositive.length > 0) {
                        data.dailyPositive.forEach(element => {
                            positive.innerHTML += `<p>Today: ${element}</p>`;
                        });
                    }

                    // Tracking Insight
                    let tracking = document.getElementById("tracking");
                    tracking.innerHTML = "";
                    if (data.trackingInsight && data.trackingInsight.length > 0) {
                        tracking.innerHTML = "<p>" + data.trackingInsight + "</p>";
                    } else if (data.dailyTracking && data.dailyTracking.length > 0) {
                        data.dailyTracking.forEach(element => {
                            tracking.innerHTML += `<p>Today: ${element}</p>`;
                        });
                    }

                    // Positive Saving Insight
                    let positiveSaving = document.getElementById("positiveSaving");
                    positiveSaving.innerHTML = "";
                    if (data.positiveSavingInsight && data.positiveSavingInsight.length > 0) {
                        positiveSaving.innerHTML += "<b>Saving Goals:</b>";
                        positiveSaving.innerHTML += "<p>" + data.positiveSavingInsight + "</p>";
                    } else if (data.dailyPositiveSaving && data.dailyPositiveSaving.length > 0) {
                        positiveSaving.innerHTML += "<b>Today Saving Goals:</b>";
                        data.dailyPositiveSaving.forEach(element => {
                            positiveSaving.innerHTML += `<p>${element}</p>`;
                        });
                    }

                    // No Saving Insight
                    let noSaving = document.getElementById("noSaving");
                    noSaving.innerHTML = "";
                    if (data.noSavingInsight && data.noSavingInsight.length > 0) {
                        noSaving.innerHTML += "<b>Saving Goals:</b>";
                        noSaving.innerHTML += "<p>" + data.noSavingInsight + "</p>";
                    } else if (data.dailyNoSaving && data.dailyNoSaving.length > 0) {
                        noSaving.innerHTML += "<b>Today Saving Goals:</b>";
                        data.dailyNoSaving.forEach(element => {
                            noSaving.innerHTML += `<p>${element}</p>`;
                        });
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

    <script>
        // Push a fake history state so back swipe hits this first
        history.pushState(null, "", location.href);

        // Handle back swipe / back button
        window.addEventListener("popstate", function (event) {
            // Redirect to home page
            location.replace("../../pages/home/home.php"); // use replace to avoid stacking history
        });
    </script>



</body>

</html>