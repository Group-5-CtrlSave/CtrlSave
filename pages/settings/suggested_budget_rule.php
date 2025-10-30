<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Suggested Budget Rule</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/sideBar.css">
  <link rel="stylesheet" href="../../assets/css/suggestedBudgetRule.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <!-- Top Navbar -->
  <nav class="bg-white px-4 d-flex align-items-center justify-content-between shadow sticky-top"
     style="height: 72px; z-index: 1000;">
    <a href="../../pages/settings/settings.php" class="text-decoration-none">
      <img src="../../assets/img/shared/backArrow.png" alt="Back" style="width: 32px;">
    </a>
  </nav>

  <!-- Main Content -->
  <div class="container py-4">
    <h2>Do you follow a budgeting rule?</h2>
    <p class="desc mb-4">Here are some budgeting rules to help<br>you get started on your saving journey.</p>

    <div class="row">

      <div class="accordion" id="budgetingRulesAccordion">

        <!-- Rule 1 -->
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
                The 50/30/20 rule allocates your income: 50% for needs, 30% for wants, and 20% for savings.
                It’s great for beginners because of its simplicity and balance.
              </p>
            </div>
          </div>
        </div>

        <!-- Rule 2 -->
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
                The 60/20/20 rule suggests using 60% of your income for essentials, and 20% each for savings
                and discretionary spending.
              </p>
            </div>
          </div>
        </div>

        <!-- Rule 3 -->
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
                The 80/20 rule is simple: save 20% of your income and spend the remaining 80%.
                Ideal for flexible budgeting styles.
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Buttons -->
    <div class="text-center mt-4">
      <a href="#"><button class="btn btn-warning next-btn">Next</button></a>
      <a href="custom_budget_rule.php" class="preferMineLink d-block" style="text-decoration: underline; text-underline-offset: 3px; margin-top: 5px;">I prefer mine</a>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          datasets: [{ data: config.data, backgroundColor: config.colors }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: true, position: 'bottom' } }
        }
      });
    }

    document.getElementById('budgetingRulesAccordion').addEventListener('shown.bs.collapse', (event) => {
      const canvas = event.target.querySelector('canvas');
      if (canvas) createPieChart(canvas.id);
    });

    // Allow only one checkbox at a time
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="ruleOption"]');
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        checkboxes.forEach(cb => { if (cb !== checkbox) cb.checked = false; });
      });
    });
  </script>
</body>
</html>
