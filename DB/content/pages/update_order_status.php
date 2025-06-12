<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserID'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['new_status'];
    $notes = trim($_POST['notes'] ?? '');
    
    // Validate status
    $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (!in_array($newStatus, $allowedStatuses)) {
        $_SESSION['error_message'] = "Invalid status selected";
        header("Location: order_management.php");
        exit();
    }
    
    // Special handling for cancelled orders
    if ($newStatus === 'cancelled' && empty($notes)) {
        $_SESSION['error_message'] = "Cancellation reason is required";
        header("Location: order_management.php");
        exit();
    }
    
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET OrderStatus = ? WHERE OrderID = ?");
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    
    // Add to status history
    $historyStmt = $conn->prepare("
        INSERT INTO order_status_history 
        (OrderID, OrderStatus, ChangedBy, ChangeDate, Notes)
        VALUES (?, ?, ?, NOW(), ?)
    ");
    $historyStmt->bind_param("isss", $orderId, $newStatus, $_SESSION['UserID'], $notes);
    $historyStmt->execute();
    
    // Redirect back with success message
    $_SESSION['success_message'] = "Order #$orderId status updated to " . ucfirst($newStatus);
    header("Location: order_management.php");
    exit();
} else {
    header("Location: order_management.php");
    exit();
}
?>