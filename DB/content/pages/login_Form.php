<?php
session_start();

$user_ID = $_POST['UserID'];
$user_Pwd = $_POST['UserPwd'];

$host = "localhost";
$user = "root";
$pass = "";
$db = "database_project";
    
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("CONNECTION FAILED: ". $conn->connect_error);
} else {
    $queryCheck = "SELECT * FROM USERS WHERE UserID = '".$user_ID."'";
    $resultCheck = $conn->query($queryCheck); 
    
    if ($resultCheck->num_rows == 0) { 
        $_SESSION['login_error'] = "Wrong Password or Username! Please try again.";
        header("Location: login.php");
        exit();
    } else {
        while($row = $resultCheck->fetch_assoc()) {
            if($row["UserPwd"] == $user_Pwd) {
                $_SESSION["UID"] = $user_ID;
                $_SESSION["UserType"] = $row["UserType"];
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['login_error'] = "Wrong Password or Username! Please try again.";
                header("Location: login.php");
                exit();
            }
        }
    }
}
$conn->close();
?>