<?php

function getChallengeProgress($userID, $challengeId, $conn)
{
    switch ($challengeId) {

        case 9: // Add 3 expenses
            $sql = "SELECT COUNT(*) AS total FROM tbl_expense
                    WHERE userID=$userID AND YEARWEEK(dateAdded,1)=YEARWEEK(CURDATE(),1)";
            $r = mysqli_query($conn, $sql);
            return ["current" => intval(mysqli_fetch_assoc($r)['total']), "total" => 3];

        case 6: // Login 5 times
            $sql = "SELECT COUNT(DISTINCT DATE(loginDate)) AS total FROM tbl_loginhistory
                    WHERE userID=$userID AND YEARWEEK(loginDate,1)=YEARWEEK(CURDATE(),1)";
            $r = mysqli_query($conn, $sql);
            return ["current" => intval(mysqli_fetch_assoc($r)['total']), "total" => 3];

        case 7: // Saving row
            $rows = [
                [5,10,5,10,5],
                [20,5,10,20,10]
            ];

            $completed = 0;
            foreach ($rows as $row) {
                $count = 0;
                foreach ($row as $amt) {
                    $sql = "SELECT id FROM tbl_savingchallenge_progress
                            WHERE userID=$userID AND amount=$amt
                            AND YEARWEEK(dateAdded,1)=YEARWEEK(CURDATE(),1)";
                    $r = mysqli_query($conn, $sql);
                    if ($r && mysqli_num_rows($r) > 0) $count++;
                }
                if ($count === 5) { $completed = 5; break; }
            }

            return ["current" => $completed, "total" => 5];

        default:
            return null;
    }
}
