<?php
session_start();

include("../../assets/shared/connect.php");

$error = "";

if (isset($_POST['signup'])) {

    $username = trim($_POST['username']);
    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check for existing email
    $checkSql = "SELECT email FROM tbl_users WHERE email = '$email' LIMIT 1";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows > 0) {
        $error = "Email already registered.";
    } else {

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // START TRANSACTION
        $conn->begin_transaction();
        $success = false;

        try {

            // INSERT USER
            $insertUserSql = "INSERT INTO tbl_users (userName, firstName, lastName, email, password)
                VALUES ('$username', '$fname', '$lname', '$email', '$hashedPassword')";

            if (!$conn->query($insertUserSql)) {
                throw new Exception("User insertion failed.");
            }

            $newUserID = $conn->insert_id;
            $_SESSION['userID'] = $newUserID;

            // INITIALIZE USER CATEGORIES USING ONLY WHILE

            $categoriesToInsert = [];

            // Income categories 
            $sqlIncome = "SELECT categoryName, type, icon, defaultCategoryID, defaultNecessityType, defaultIsFlexible
                FROM tbl_defaultcategories
                WHERE type = 'income'
                ORDER BY defaultCategoryID ASC";
            $resIncome = $conn->query($sqlIncome);

            while ($row = $resIncome->fetch_assoc()) {
                $categoriesToInsert[] = [
                    $row['categoryName'],
                    $row['type'],
                    $row['icon'],
                    $row['defaultCategoryID'],
                    $row['defaultNecessityType'],
                    $row['defaultIsFlexible'],
                    1
                ];
            }

            // Savings category 
            $sqlSavings = "SELECT defaultCategoryID, icon, defaultnecessityType
                FROM tbl_defaultcategories
                WHERE categoryName = 'Savings' AND type = 'savings'
                LIMIT 1";
            $resSavings = $conn->query($sqlSavings);

            if ($row = $resSavings->fetch_assoc()) {
                $categoriesToInsert[] = [
                    "Savings",
                    "savings",
                    $row['icon'],
                    $row['defaultCategoryID'],
                    $row['defaultnecessityType'],
                    0,
                    1
                ];
            } else {
                // fallback
                $categoriesToInsert[] = [
                    "Savings",
                    "savings",
                    "Saving.png",
                    999,
                    "saving",
                    0,
                    1
                ];
            }

            // INSERT USER CATEGORIES USING QUERY
            foreach ($categoriesToInsert as $cat) {

                $catName = $conn->real_escape_string($cat[0]);
                $catType = $conn->real_escape_string($cat[1]);
                $catIcon = $conn->real_escape_string($cat[2]);
                $catDefaultID = (int) $cat[3];
                $catNecessity = $conn->real_escape_string($cat[4]);
                $catFlexible = (int) $cat[5];
                $catSelected = (int) $cat[6];

                $insertCatSql = "INSERT INTO tbl_usercategories (userID, categoryName, 
                    type, icon, userNecessityType, userisFlexible, defaultCategoryID, isSelected)
                    VALUES (
                        $newUserID,
                        '$catName',
                        '$catType',
                        '$catIcon',
                        '$catNecessity',
                        $catFlexible,
                        $catDefaultID,
                        $catSelected
                    )
                ";

                if (!$conn->query($insertCatSql)) {
                    throw new Exception("Category insert failed for $catName");
                }
            }

            // CREATE BUDGET VERSION
            $insertBudgetSql = "INSERT INTO tbl_userbudgetversion (versionNumber, userID, userBudgetRuleID, totalIncome, isActive)
            VALUES (1, $newUserID, 1, 0.00, 1)";

            if (!$conn->query($insertBudgetSql)) {
                throw new Exception("Budget version insertion failed.");
            }


            // Initialize user challenges 
            $challengeQuery = "SELECT challengeID FROM tbl_challenges";
            $challengeResult = $conn->query($challengeQuery);

            if ($challengeResult && $challengeResult->num_rows > 0) {
                while ($row = $challengeResult->fetch_assoc()) {
                    $challengeID = (int) $row['challengeID'];

                    $insertUserChallenge = "INSERT INTO tbl_userchallenges (challengeID, userID, status, assignedDate, completedAt, claimedAt)
                    VALUES ($challengeID, $newUserID, 'in progress', NOW(), NULL, NULL)";

                    if (!$conn->query($insertUserChallenge)) {
                        throw new Exception("Failed to insert user challenge ID $challengeID");
                    }
                }
            }

            // COMMIT
            $conn->commit();
            $success = true;

        } catch (Exception $e) {

            $conn->rollback();
            $error = "Setup failed: " . $e->getMessage();
        }

        if ($success) {
            header("Location: currency.php");
            exit();
        }
    }
}
?>