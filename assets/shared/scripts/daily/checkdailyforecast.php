<?php
include("../../connect.php"); // adjust path

// Get all users
$userQuery = "SELECT userID FROM tbl_users";
$userResult = mysqli_query($conn, $userQuery);

while ($userRow = mysqli_fetch_assoc($userResult)) {
    $userID = $userRow['userID'];

    // Forecast periods in months
    $periods = [1, 3, 6];

    foreach ($periods as $period) {

        // Calculate forecast month/year
        $forecastMonth = date('n') + $period;
        $forecastYear = date('Y');
        while ($forecastMonth > 12) {
            $forecastMonth -= 12;
            $forecastYear++;
        }

        // Previous month for comparison
        $prevMonth = date('n');
        $prevYear = date('Y');

        // Fetch total actual spending of previous month
        $actualQuery = "SELECT SUM(amount) AS totalAmount 
                        FROM tbl_expense
                        WHERE userID = $userID
                        AND MONTH(dateSpent) = $prevMonth
                        AND YEAR(dateSpent) = $prevYear
                        AND isDeleted = 0";
        $actualResult = mysqli_query($conn, $actualQuery);
        $actualRow = mysqli_fetch_assoc($actualResult);
        $prevTotal = $actualRow['totalAmount'] ?? 0;

        // Forecast type
        $forecastType = "expense_{$period}m";

        // Get forecasted total for this period (userCategoryID = 0)
        $forecastQuery = "SELECT predictedAmount 
                          FROM tbl_forecasts
                          WHERE userID = $userID
                          AND forecastMonth = $forecastMonth
                          AND forecastYear = $forecastYear
                          AND forecastType = '$forecastType'
                          AND userCategoryID = 0";
        $forecastResult = mysqli_query($conn, $forecastQuery);

        $forecastRow = mysqli_fetch_assoc($forecastResult);
        if (!$forecastRow) continue; // Skip if no forecast exists

        $forecastAmount = $forecastRow['predictedAmount'];

        // Calculate percent change
        if ($prevTotal == 0) {
            $percentChange = 100; // No previous data
        } else {
            $percentChange = (($forecastAmount - $prevTotal) / $prevTotal) * 100;
        }
        $percentChange = round($percentChange, 1);

        // Generate message
        if ($percentChange > 0) {
            $message = "Your total spending is expected to increase by $percentChange% in the next $period month(s).";
            $insightType = 'forecast_increase';
        } elseif ($percentChange < 0) {
            $message = "Your total spending is expected to decrease by " . abs($percentChange) . "% in the next $period month(s).";
            $insightType = 'forecast_decrease';
        } else {
            $message = "Your total spending is expected to remain stable in the next $period month(s).";
            $insightType = 'forecast_stable';
        }

        // Insert into tbl_spendinginsights
        $insertQuery = "INSERT INTO tbl_spendinginsights 
                        (userID, categoryA, categoryB, insightType, necessityType, message, date)
                        VALUES (
                            $userID,
                            0,
                            NULL,
                            '$insightType',
                            'general',
                            '".mysqli_real_escape_string($conn, $message)."',
                            NOW()
                        )";
        mysqli_query($conn, $insertQuery);
    }
}

echo "Spending insights generated for 1, 3, 6 month forecasts.\n";
?>
