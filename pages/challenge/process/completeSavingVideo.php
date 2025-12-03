<?php
session_start();
include("../../../assets/shared/connect.php");
include("challengeController.php");

$userID = $_SESSION['userID'] ?? 0;

if ($userID > 0) {
    updateSavingVideoChallenge($userID, $conn);
}

echo "ok";