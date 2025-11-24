<?php
session_start();

include("../../assets/shared/connect.php");

$error = "";

if (!isset($_SESSION['userID'])) {
    header("Location: ../login&signup/login.php");
    exit;
}

$userID = (int) $_SESSION['userID'];

// ---------- PROCESS FORM SUBMISSION ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['necessity']) || !is_array($_POST['necessity'])) {
        $error = "Please categorize all expenses before proceeding.";
    } else {
        $necessities = $_POST['necessity'];
        $invalid = false;

        foreach ($necessities as $catID => $type) {
            if (!in_array($type, ['need', 'want'])) {
                $invalid = true;
                break;
            }
        }

        if ($invalid) {
            $error = "Invalid data received. Please try again.";
        } else {
            $stmt = $conn->prepare("UPDATE tbl_usercategories SET userNecessityType = ? WHERE userCategoryID = ? AND userID = ?");
            foreach ($necessities as $catID => $type) {
                $stmt->bind_param("sii", $type, $catID, $userID);
                $stmt->execute();
            }
            header("Location: budgetingRule.php");
            exit;
        }
    }
}

// ---------- FETCH EXPENSE CATEGORIES ----------
$stmt = $conn->prepare("SELECT userCategoryID, categoryName, userNecessityType 
                        FROM tbl_usercategories 
                        WHERE userID = ? AND type = 'expense' AND isSelected = 1 
                        ORDER BY userCategoryID ASC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>