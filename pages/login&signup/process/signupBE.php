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

    // ============================
    // PASSWORD VALIDATION
    // ============================
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters with uppercase, lowercase, number, and special character.";
    }

    // ============================
    // CHECK EMAIL DUPLICATION
    // ============================
    $checkSql = "SELECT email FROM tbl_users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $error = "Email already registered.";
    }

    // If error exists, stop here
    if (!empty($error)) {
        return;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // START TRANSACTION
    $conn->begin_transaction();
    $success = false;

    try {

        // ============================
        // INSERT USER
        // ============================
        $insertUserSql = "
            INSERT INTO tbl_users (userName, firstName, lastName, email, password)
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($insertUserSql);
        $stmt->bind_param("sssss", $username, $fname, $lname, $email, $hashedPassword);

        if (!$stmt->execute()) {
            throw new Exception("User insertion failed.");
        }

        $newUserID = $stmt->insert_id;
        $_SESSION['userID'] = $newUserID;
        $stmt->close();


        // ============================
        // INSERT USER CATEGORIES
        // ============================

        $categoriesToInsert = [];

        // --- Income categories ---
        $sqlIncome = "
            SELECT categoryName, type, icon, defaultCategoryID, defaultNecessityType, defaultIsFlexible
            FROM tbl_defaultcategories
            WHERE type = 'income'
            ORDER BY defaultCategoryID ASC
        ";
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

        // --- Savings category (single) ---
        $sqlSavings = "
            SELECT defaultCategoryID, icon, defaultNecessityType 
            FROM tbl_defaultcategories 
            WHERE categoryName = 'Savings' AND type = 'savings' LIMIT 1
        ";
        $resSavings = $conn->query($sqlSavings);

        if ($row = $resSavings->fetch_assoc()) {
            $categoriesToInsert[] = [
                "Savings",
                "savings",
                $row['icon'],
                $row['defaultCategoryID'],
                $row['defaultNecessityType'],
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

        // --- Insert into tbl_usercategories ---
        foreach ($categoriesToInsert as $cat) {

            $insertCatSql = "
                INSERT INTO tbl_usercategories (
                    userID, categoryName, type, icon, userNecessityType, 
                    userisFlexible, defaultCategoryID, isSelected
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($insertCatSql);
            $stmt->bind_param(
                "issssiii",
                $newUserID,
                $cat[0],
                $cat[1],
                $cat[2],
                $cat[4],
                $cat[5],
                $cat[3],
                $cat[6]
            );

            if (!$stmt->execute()) {
                throw new Exception("Category insert failed for " . $cat[0]);
            }
            $stmt->close();
        }


        // ============================
        // INITIALIZE USER CHALLENGES
        // ============================

        $challengeQuery = "SELECT challengeID FROM tbl_challenges";
        $challengeResult = $conn->query($challengeQuery);

        if ($challengeResult && $challengeResult->num_rows > 0) {
            while ($row = $challengeResult->fetch_assoc()) {

                $insertUserChallenge = "
                    INSERT INTO tbl_userchallenges 
                    (challengeID, userID, status, assignedDate, completedAt, claimedAt)
                    VALUES (?, ?, 'in progress', NOW(), NULL, NULL)
                ";

                $stmt = $conn->prepare($insertUserChallenge);
                $stmt->bind_param("ii", $row['challengeID'], $newUserID);

                if (!$stmt->execute()) {
                    throw new Exception("Failed to insert user challenge.");
                }

                $stmt->close();
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
?>
