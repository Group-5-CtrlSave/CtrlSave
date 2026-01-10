<?php
include("../../connect.php");

date_default_timezone_set('Asia/Manila');

// Dates for 1 day and 3 days ahead (YYYY-MM-DD format)
$oneDay = date('Y-m-d', strtotime('+1 day'));
$threeDays = date('Y-m-d', strtotime('+3 days'));

// Query using DATE() on DATETIME column
$recurringQuery = "SELECT recurringID, userID, type, userCategoryID, amount, note, nextDuedate
                   FROM tbl_recurringtransactions
                   WHERE DATE(nextDuedate) = '$oneDay'
                      OR DATE(nextDuedate) = '$threeDays'";

$result = executeQuery($recurringQuery);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    echo "Found " . mysqli_num_rows($result) . " recurring transactions.\n";
    while ($row = mysqli_fetch_assoc($result)) {
        $userID = $row['userID'];
        $recurringID =  $row['recurringID'];
        $amount = number_format($row['amount'], 2);
        $type = $row['type'];
        $note = $row['note'] ?: '';
        $dueDate = date('F j, Y', strtotime($row['nextDuedate']));

        // Calculate days ahead
        $daysDiff = ceil((strtotime($row['nextDuedate']) - time()) / (60*60*24));
        $prefix = ($daysDiff == 3) ? "Upcoming in 3 days: " : "Reminder: ";

        $title = "$prefix Recurring Payment";
        $message = "Your recurring payment of ₱$amount is due on $dueDate" . ($note ? ". $note" : "");

        executeQuery("INSERT INTO tbl_notifications 
                      (notificationTitle, message, icon, createdAt, isRead, userID, type, userCategoryID) 
                      VALUES ('$title', '$message', 'bell.png', NOW(), 0, $userID, 'recurring', $recurringID)");
    }
} else {
    echo "No recurring transactions found for 1 or 3 days ahead.\n";
}
