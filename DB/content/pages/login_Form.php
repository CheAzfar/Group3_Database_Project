<?php
session_start();
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['UserID'];
    $password = $_POST['UserPwd'];

    // Always return a generic error message
    $loginErrorMsg = "Invalid username or password.";

    // Prepare query
    $stmt = $conn->prepare("SELECT * FROM USERS WHERE UserID = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Default to failed
    $loginSuccess = false;

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['UserPwd'];

        // Check using password_verify (preferred), fallback only if needed
        if (password_verify($password, $storedPassword) || $password === $storedPassword) {
            // Password matches
            $loginSuccess = true;

            $_SESSION['UserID'] = $username;
            $_SESSION['UserType'] = $row['UserType'];
            $_SESSION['role'] = $row['UserType'];
            header("Location: index.php");
            exit();
        }
    }

    // On failure
    $_SESSION['login_error'] = $loginErrorMsg;
    header("Location: Login.php");
    exit();

    $stmt->close();
    $conn->close();
}
?>
