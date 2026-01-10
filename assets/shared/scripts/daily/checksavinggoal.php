<?php
include("../../connect.php");

date_default_timezone_set('Asia/Manila');

$today = date('Y-m-d');

// Fetch all saving goals that require reminders and have future deadlines
$savingGoalQuery = "SELECT savingGoalID, userID, goalName, icon, targetAmount, currentAmount, deadline, frequency, remind, createdAt
                    FROM tbl_savinggoals
                    WHERE remind = 1
                      AND deadline >= NOW()";

$result = executeQuery($savingGoalQuery);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    echo "Found " . mysqli_num_rows($result) . " saving goals for reminder.\n";

    while ($row = mysqli_fetch_assoc($result)) {

        $userID = $row['userID'];
        $goalName = mysqli_real_escape_string($conn, $row['goalName']); // Escape goalName
        $icon = mysqli_real_escape_string($conn, $row['icon']);
        $current = number_format($row['currentAmount'], 2);
        $target = number_format($row['targetAmount'], 2);
        $deadline = date('F j, Y', strtotime($row['deadline']));
        $frequency = strtolower($row['frequency']);
        $createdAt = $row['createdAt'];

        // Calculate days until deadline
        $daysDiff = ceil((strtotime($row['deadline']) - time()) / (60*60*24));

        $sendNotif = false;
        $prefix = "";

        // 1-day and 3-day reminders
        if ($daysDiff == 3) {
            $prefix = "Upcoming in 3 days: ";
            $sendNotif = true;
        } elseif ($daysDiff == 1) {
            $prefix = "Reminder: ";
            $sendNotif = true;
        } else {
            // Frequency-based reminders
            if ($frequency == 'daily') {
                $prefix = "Daily Reminder: ";
                $sendNotif = true;
            } elseif ($frequency == 'weekly') {
                $createdWeekday = date('N', strtotime($createdAt)); // 1 (Mon) - 7 (Sun)
                $todayWeekday = date('N');
                if ($todayWeekday == $createdWeekday) {
                    $prefix = "Weekly Reminder: ";
                    $sendNotif = true;
                }
            } elseif ($frequency == 'monthly') {
                $createdDay = date('d', strtotime($createdAt));
                $todayDay = date('d');
                if ($todayDay == $createdDay) {
                    $prefix = "Monthly Reminder: ";
                    $sendNotif = true;
                }
            }
        }

        if ($sendNotif) {
            $title = mysqli_real_escape_string($conn, "$prefix Saving Goal: $goalName");
            $message = mysqli_real_escape_string($conn, "Your saving goal '$goalName' is due on $deadline. Current: ₱$current / Target: ₱$target.");

            executeQuery("INSERT INTO tbl_notifications
                          (notificationTitle, message, icon, createdAt, isRead, userID, type)
                          VALUES ('$title', '$message', 'Savings.png', NOW(), 0, $userID, 'saving_goal')");
        }
    }
} else {
    echo "No saving goals found for reminders.\n";
}
