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

        if ($row['UserPwd'] === $password) { // simple password check, you can use password_hash() later
            $_SESSION['UserID'] = $username;
            $_SESSION['UserType'] = $row['UserType'];       // e.g., 'admin' or 'customer'
            $_SESSION['role'] = $row['UserType'];           // used by header.php logic
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
?>
