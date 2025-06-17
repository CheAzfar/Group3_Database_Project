<?php
session_start();

// Clear Buy Now session if set
if (isset($_SESSION['buy_now'])) {
    unset($_SESSION['buy_now']);
}

// Redirect back to cart
header("Location: cart.php");
exit();
