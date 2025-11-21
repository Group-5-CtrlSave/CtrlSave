<?php
session_start();
include("../../assets/shared/connect.php");

/* ---------------- SESSION CHECK ---------------- */
if (!isset($_SESSION['userID'])) {
    header("Location: ../login&signup/login.php");
    exit;
}
$userID = (int) $_SESSION['userID'];

/* ---------------- FETCH DEFAULT RULE + ALLOCATIONS ---------------- */
$sql = "
    SELECT r.defaultBudgetruleID, r.ruleName, r.ruleDescription,
           a.defaultnecessityType AS category, a.value AS percentage
    FROM tbl_defaultbudgetrule r
    LEFT JOIN tbl_defaultallocation a
        ON r.defaultBudgetruleID = a.defaultBudgetruleID
    ORDER BY r.defaultBudgetruleID, a.defaultAllocationID
";
$res = $conn->query($sql);

$rules = [];
while ($row = $res->fetch_assoc()) {
    $id = $row['defaultBudgetruleID'];
    if (!isset($rules[$id])) {
        $rules[$id] = [
            'id' => $id,
            'ruleName' => $row['ruleName'],
            'ruleDescription' => $row['ruleDescription'],
            'allocations' => []
        ];
    }
    if ($row['category'] !== null) {
        $rules[$id]['allocations'][] = [
            'category' => $row['category'],
            'percentage' => (int)$row['percentage']
        ];
    }
}

/* ---------------- HANDLE FORM SUBMIT ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['ruleOption'])) {
        $selectedRule = (int) $_POST['ruleOption'];

        if (isset($rules[$selectedRule])) {
            $conn->begin_transaction();

            try {
                $ruleName = $rules[$selectedRule]['ruleName'];

                // Check if user already has a rule
                $check = $conn->prepare("SELECT userBudgetRuleID FROM tbl_userbudgetrule WHERE userID = ?");
                $check->bind_param("i", $userID);
                $check->execute();
                $chkRes = $check->get_result();

                if ($chkRes->num_rows > 0) {
                    $existingRow = $chkRes->fetch_assoc();
                    $userBudgetRuleID = (int) $existingRow['userBudgetRuleID'];

                    // Update existing user rule
                    $upd = $conn->prepare("UPDATE tbl_userbudgetrule SET ruleName = ?, isSelected = 1 WHERE userBudgetRuleID = ?");
                    $upd->bind_param("si", $ruleName, $userBudgetRuleID);
                    $upd->execute();
                } else {
                    // Insert new rule
                    $ins = $conn->prepare("INSERT INTO tbl_userbudgetrule (userID, ruleName, createdAt, isSelected) VALUES (?, ?, NOW(), 1)");
                    $ins->bind_param("is", $userID, $ruleName);
                    $ins->execute();
                    $userBudgetRuleID = $conn->insert_id;
                }

                // Delete existing allocations to prevent duplicates
                $del = $conn->prepare("DELETE FROM tbl_userallocation WHERE userBudgetruleID = ?");
                $del->bind_param("i", $userBudgetRuleID);
                $del->execute();

                // Insert new allocations
                $insertAlloc = $conn->prepare("INSERT INTO tbl_userallocation (userBudgetruleID, userCategoryID, necessityType, limitType, value) VALUES (?, NULL, ?, 0, ?)");
                foreach ($rules[$selectedRule]['allocations'] as $alloc) {
                    $necessity = $alloc['category'];
                    $value = (int) $alloc['percentage'];
                    $insertAlloc->bind_param("isi", $userBudgetRuleID, $necessity, $value);
                    $insertAlloc->execute();
                }

                $conn->commit();
                header("Location: settings.php");
                exit;
            } catch (Exception $ex) {
                $conn->rollback();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Suggested Budget Rule</title>
    <link rel="icon" href="../../assets/img/shared/ctrlsaveLogo.png">
    <link rel="stylesheet" href="../../assets/css/sideBar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
        background-color: #44B87D;
        overflow: hidden;
    }

    h2, .desc {
        font-family: "Poppins", sans-serif;
        color: #fff;
        text-align: center;
    }

    .rule-card {
        background: #F0F1F6;
        border: 2px solid #F6D25B;
        border-radius: 10px;
        padding: 2px;
        margin-bottom: 1rem;
    }

    .btn {
        background-color: #F6D25B;
        color: black;
        border-radius: 27px;
        font-weight: bold;
        font-family: "Poppins", sans-serif;
    }
</style>
</head>

<body>
<form method="POST">
    <!-- Navigation Bar -->
    <nav class="bg-white px-4 py-4 d-flex justify-content-center align-items-center shadow sticky-top">
        <div class="container-fluid position-relative">
            <div class="d-flex align-items-start justify-content-start">
                <a href="../settings/settings.php">
                    <img class="img-fluid" src="../../assets/img/shared/BackArrow.png" alt="Back" style="height: 24px;" />
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Body -->
    <div class="container py-4">
        <h2>Do you follow a budgeting rule?</h2>
        <p class="desc mb-4">Here are some budgeting rules to help<br>you get started on your saving journey.</p>

        <div class="row" style="overflow:scroll; height: 290px;">
            <div class="accordion" id="budgetingRulesAccordion">
                <?php foreach ($rules as $r): ?>
                    <div class="accordion-item rule-card">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#rule<?= $r['id'] ?>" aria-expanded="false">
                                <input class="form-check-input me-2 ruleCheck" type="checkbox" name="ruleOption"
                                    value="<?= $r['id'] ?>"/>
                                <?= htmlspecialchars($r['ruleName']) ?>
                            </button>
                        </h2>
                        <div id="rule<?= $r['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#budgetingRulesAccordion">
                            <div class="accordion-body text-center">
                                <canvas id="chart<?= $r['id'] ?>"></canvas>
                                <p class="mt-3"><?= htmlspecialchars($r['ruleDescription']) ?></p>
                                <?php if (!empty($r['allocations'])): ?>
                                    <div class="mt-2">
                                        <?php foreach ($r['allocations'] as $alloc): ?>
                                            <p><?= htmlspecialchars($alloc['category']) ?>: <?= (int)$alloc['percentage'] ?>%</p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" id="saveBtn" class="btn btn-warning" disabled>Save Changes</button>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ruleData = <?= json_encode(array_values($rules)) ?>;
    const chartInstances = {};

    function createPieChart(id, data) {
        if (chartInstances[id]) return;
        const labels = data.map(d => d.category);
        const values = data.map(d => d.percentage);
        const colors = ['#F6D25B', '#44B87D', '#C0C0C0', '#8AB4F8', '#E86C6C'];
        const ctx = document.getElementById('chart' + id);
        if (!ctx) return;
        chartInstances[id] = new Chart(ctx, {
            type: 'pie',
            data: { labels, datasets: [{ data: values, backgroundColor: colors.slice(0, values.length) }] },
            options: { responsive: true, plugins: { legend: { display: true, position: 'bottom' } } }
        });
    }

    const checks = document.querySelectorAll('input[type="checkbox"][name="ruleOption"]');
    const saveBtn = document.getElementById('saveBtn');
    const accordion = document.getElementById('budgetingRulesAccordion');

    // Enable Save Changes button if any checkbox is selected
    function updateSaveButton() {
        saveBtn.disabled = !Array.from(checks).some(cb => cb.checked);
    }

    checks.forEach(cb => {
        cb.addEventListener('change', () => {
            // Only allow one checkbox
            checks.forEach(other => { if (other !== cb) other.checked = false; });
            updateSaveButton();
        });
    });

    // When an accordion item expands, auto-check its checkbox and draw chart
    accordion.addEventListener('shown.bs.collapse', (event) => {
        const id = event.target.id.replace('rule', '');
        const rule = ruleData.find(r => r.id == id);

        if (rule) {
            // Auto-check this rule's checkbox and uncheck others
            checks.forEach(cb => cb.checked = (cb.value == id));
            updateSaveButton();

            if (rule.allocations) createPieChart(id, rule.allocations);
        }
    });
</script>
</body>
</html>
