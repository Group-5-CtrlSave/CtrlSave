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

        case 7: // Weekly: Complete ANY full row of saving challenge

            // 1. Get active challenge (to read slotData)
            $q = mysqli_query($conn,
                "SELECT userSavingChallengeID, slotData 
                 FROM tbl_usersavingchallenge 
                 WHERE userID=$userID AND status='active' LIMIT 1"
            );

            if (!$q || mysqli_num_rows($q) == 0) {
                return ["current" => 0, "total" => 5];
            }

            $challenge = mysqli_fetch_assoc($q);

            // 2. Decode slotData (20 slot values)
            $slots = json_decode($challenge['slotData'], true);

            if (!$slots || count($slots) != 20) {
                return ["current" => 0, "total" => 5];
            }

            // 3. Break into rows of 5
            $rows = array_chunk($slots, 5);

            // 4. Get saved progress indexes
            $saved = [];
            $res = mysqli_query($conn,
                "SELECT itemIndex FROM tbl_savingchallenge_progress
                 WHERE userID=$userID"
            );

            while ($r = mysqli_fetch_assoc($res)) {
                $saved[] = intval($r['itemIndex']);
            }

            // 5. Check each row → all 5 clicked?
            foreach ($rows as $rowIndex => $rowSlots) {

                $rowComplete = true;

                for ($i = 0; $i < 5; $i++) {
                    $slotIndex = $rowIndex * 5 + $i;
                    if (!in_array($slotIndex, $saved)) {
                        $rowComplete = false;
                        break;
                    }
                }

                if ($rowComplete) {
                    return ["current" => 5, "total" => 5];
                }
            }

            // Nothing complete yet
            return ["current" => 0, "total" => 5];

        default:
            return null;
    }
}
?>
