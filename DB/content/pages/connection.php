<?php
$servername = "localhost";
$username = "root";
$password = ""; // your DB password
$dbname = "database_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>