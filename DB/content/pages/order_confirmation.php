<?php
session_start();

// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$orderId = (int)$_GET['order_id'];

// Database connection
include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");

// Set MySQL timezone to match PHP timezone
$conn->query("SET time_zone = '+08:00'");

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_items WHERE OrderID = o.OrderID) AS item_count
    FROM orders o
    WHERE o.OrderID = ?
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die("Order not found");
}

// Get order items details - CORRECTED QUERY
$itemStmt = $conn->prepare("
    SELECT oi.*, m.name AS item_name 
    FROM order_items oi
    LEFT JOIN menu_items m ON oi.item_id = m.item_id
    WHERE oi.OrderID = ?
");
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$itemsResult = $itemStmt->get_result();
$orderItems = $itemsResult->fetch_all(MYSQLI_ASSOC);
$itemStmt->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/Group3_Database_Project/DB/content/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php"); ?>

    <main class="flex-fill">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm" style="background-color: #ffe9dd;">
                        <div class="card-header bg-warning text-white">
                            <h4 class="mb-0">Order Confirmation</h4>
                        </div>

                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-check-circle fa-5x text-warning mb-3"></i>
                                <h2>Thank You for Your Order!</h2>
                                <p class="lead">Your order has been placed successfully.</p>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Order Details</h5>
                                </div>
                                <div class="card-body text-start">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Order Number:</strong> #<?= $order['OrderID'] ?></p>
                                            <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['CustName']) ?></p>
                                            <p><strong>Contact Phone:</strong> <?= htmlspecialchars($order['ContactPhone']) ?></p>
                                            <p><strong>Order Date:</strong> <?= date('F j, Y - g:i A', strtotime($order['OrderDate'])) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Order Type:</strong> <?= ucfirst($order['DeliveryType']) ?></p>
                                            <?php if ($order['DeliveryType'] === 'dine-in' && $order['TableNo']): ?>
                                                <p><strong>Table Number:</strong> <?= $order['TableNo'] ?></p>
                                            <?php endif; ?>
                                            <p><strong>Order Status:</strong> <span class="badge bg-warning text-dark"><?= ucfirst($order['OrderStatus']) ?></span></p>
                                            <p><strong>Payment Status:</strong> <span class="badge bg-warning text-dark"><?= ucfirst($order['PaymentStatus']) ?></span></p>
                                        </div>
                                    </div>

                                    <?php if (!empty($order['Notes'])): ?>
                                        <div class="mt-3">
                                            <p><strong>Special Instructions:</strong></p>
                                            <p class="text-muted"><?= htmlspecialchars($order['Notes']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order Items Summary -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Items Ordered</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($orderItems)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-end">Price</th>
                                                        <th class="text-end">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($orderItems as $item): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($item['item_name'] ?? 'Item #' . $item['item_id']) ?></td>
                                                            <td class="text-center"><?= $item['Quantity'] ?></td>
                                                            <td class="text-end">RM <?= number_format($item['Price'], 2) ?></td>
                                                            <td class="text-end">RM <?= number_format($item['Subtotal'], 2) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-warning">
                                                        <th colspan="3">Total Amount</th>
                                                        <th class="text-end">RM <?= number_format($order['TotalAmount'], 2) ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No items found for this order.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Status Information -->
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-info-circle"></i> What's Next?</h6>
                                <p class="mb-0">
                                    <?php if ($order['DeliveryType'] === 'dine-in'): ?>
                                        Please proceed to your table (Table <?= $order['TableNo'] ?>). Your order is being prepared and will be served shortly.
                                    <?php else: ?>
                                        <p>Your takeaway order is being prepared. Please wait for your order to be ready for pickup.</p>
                                        <b><p>Please Remember to Print Receipt!!!</p></b>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="d-grid gap-2 col-md-6 mx-auto">
                                <a href="index.php" class="btn btn-warning">Back to Home</a>
                                <a href="index.php#foodMenuAccordion" class="btn btn-outline-warning">Order More Items</a>
                                <button onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="fas fa-print"></i> Print Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        @media print {

            .btn,
            .navbar,
            .footer {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</body>

</html>