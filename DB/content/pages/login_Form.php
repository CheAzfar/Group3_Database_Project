<?php
session_start();
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['UserID'];
    $password = $_POST['UserPwd'];

    // Use correct table: USERS
    $stmt = $conn->prepare("SELECT * FROM USERS WHERE UserID = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['UserPwd'];

        // Try password_verify first (for hashed passwords)
        // If that fails, try direct comparison (for plain text passwords)
        $passwordMatch = password_verify($password, $storedPassword) || ($storedPassword === $password);

        if ($passwordMatch) {
            $_SESSION['UserID'] = $username;
            $_SESSION['UserType'] = $row['UserType'];
            $_SESSION['role'] = $row['UserType'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Incorrect password.";
            header("Location: Login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Username not found.";
        header("Location: Login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
