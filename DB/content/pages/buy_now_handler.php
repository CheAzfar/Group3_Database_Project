<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image_url = $_POST['image_url'] ?? '';

    if ($item_id !== null) {
        $_SESSION['buy_now'] = [
            'item_id' => $item_id,
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'image_url' => $image_url
        ];

        // ✅ FIX: Redirect to payment page
        header("Location: payment.php");
        exit();
    }
}

// Optional: Fallback if no valid POST data
header("Location: index.php");
exit();
