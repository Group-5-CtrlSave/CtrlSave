<?php
include("../../connect.php");

// ============================
// Get all users
// ============================
$usersResult = $conn->query("SELECT userID FROM tbl_users WHERE isDisabled = 0"); // Adjust if you have a soft delete column

if ($usersResult->num_rows > 0) {

    $forecastTypes = [
        'expense_1m' => 1,
        'expense_3m' => 3,
        'expense_6m' => 6
    ];

    while ($userRow = $usersResult->fetch_assoc()) {
        $userID = (int)$userRow['userID'];

        // ============================
        // Function to generate predicted amount
        // ============================
        function generatePredictedAmount($userID, $monthOffset) {
            global $conn;

            $today = new DateTime();

            $amounts = [];

            // Get last 3 months expenses
            for ($i = 1; $i <= 3; $i++) {
                $date = clone $today;
                $date->modify("-$i month");
                $m = (int)$date->format("m");
                $y = (int)$date->format("Y");

                $query = "SELECT SUM(amount) AS total 
                          FROM tbl_expense 
                          WHERE userID = ? AND MONTH(dateSpent) = ? AND YEAR(dateSpent) = ? AND isDeleted = 0";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("iii", $userID, $m, $y);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                $amounts[] = isset($row['total']) ? (float)$row['total'] : 0;
            }

            // Average of last 3 months
            $avg = array_sum($amounts) / max(count($amounts), 1);

            // If all zero, use baseline
            if ($avg == 0) {
                $avg = rand(3000, 4000); // baseline if no history
            }

            // Apply ±10% variation for realism
            $variation = rand(-10, 10) / 100;
            $predicted = $avg * (1 + $variation);

            return round($predicted, 2);
        }

        // ============================
        // Generate forecasts
        // ============================
        $today = new DateTime();

        foreach ($forecastTypes as $type => $months) {
            for ($i = 1; $i <= $months; $i++) {

                $forecastDate = clone $today;
                $forecastDate->modify("+$i month");

                $month = (int)$forecastDate->format("m");
                $year = (int)$forecastDate->format("Y");

                $amount = generatePredictedAmount($userID, $i);

                $stmt = $conn->prepare("
                    INSERT INTO tbl_forecasts 
                    (userID, forecastType, forecastMonth, forecastYear, predictedAmount, confidence, dateForecast) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");

                $confidence = rand(85, 95); // example confidence
                $stmt->bind_param("issiid", $userID, $type, $month, $year, $amount, $confidence);
                $stmt->execute();
            }
        }
    }
}

echo "Forecasts generated for all users successfully!";
?>
