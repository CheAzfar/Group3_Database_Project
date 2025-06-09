<?php
session_start();
$servername = "localhost";
$username = "root";
$password = ""; // your DB password
$dbname = "database_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['item_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];

    // Initialize cart if not already
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If item already in cart, increment quantity
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'image_url' => $image_url
        ];
    }

    // Redirect back to index.php
    header('Location: /Group3_Database_Project/DB/content/pages/index.php');
    exit();
}
?>
