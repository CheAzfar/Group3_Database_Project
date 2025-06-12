<?php
session_start();

// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Database connection
include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");

// Set MySQL timezone to match PHP timezone
$conn->query("SET time_zone = '+08:00'");

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get current date for display
$currentDate = date('Y-m-d H:i:s');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $errors = [];
    $custName = trim($_POST['custName'] ?? '');
    $contactPhone = trim($_POST['contactPhone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $deliveryType = $_POST['deliveryType'] ?? 'dine-in';
    $tableNo = ($deliveryType === 'dine-in') ? ($_POST['tableNo'] ?? null) : null;

    if (empty($custName)) {
        $errors[] = "Customer name is required";
    }
    if (empty($contactPhone)) {
        $errors[] = "Contact phone is required";
    }
    if ($deliveryType === 'dine-in' && empty($tableNo)) {
        $errors[] = "Table number is required for dine-in";
    }

    if (empty($errors)) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert into orders table
            $stmt = $conn->prepare("
                INSERT INTO orders 
                (CustName, TableNo, OrderDate, TotalAmount, DeliveryType, OrderStatus, PaymentStatus, ContactPhone, Notes)
                VALUES (?, ?, NOW(), ?, ?, 'processing', 'paid', ?, ?)
            ");

            // Parameters: CustName(s), TableNo(i), TotalAmount(d), DeliveryType(s), ContactPhone(s), Notes(s)
            // OrderDate uses NOW() so no parameter needed for it
            $stmt->bind_param(
                "sidsss",
                $custName,
                $tableNo,
                $total,
                $deliveryType,
                $contactPhone,
                $notes
            );
            $stmt->execute();
            $orderId = $conn->insert_id;
            $stmt->close();

            // Insert order items
            $itemStmt = $conn->prepare("
                INSERT INTO order_items 
                (OrderID, item_id, Quantity, Price, Subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($_SESSION['cart'] as $itemId => $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $itemStmt->bind_param(
                    "iiidd",
                    $orderId,
                    $itemId,
                    $item['quantity'],
                    $item['price'],
                    $subtotal
                );
                $itemStmt->execute();

                // Update popularity_score
                $popularityStmt = $conn->prepare("
                    UPDATE menu_items 
                    SET popularity_score = popularity_score + ? 
                    WHERE item_id = ?
                ");
                $popularityStmt->bind_param("ii", $item['quantity'], $itemId);
                $popularityStmt->execute();
                $popularityStmt->close();
            }
            $itemStmt->close();

            // Commit transaction
            $conn->commit();

            // Clear cart
            unset($_SESSION['cart']);

            // Redirect to confirmation page
            header("Location: order_confirmation.php?order_id=$orderId");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Error processing order: " . $e->getMessage();
        }
    }
}

// Get order type from session or default to 'dine-in'
$orderType = $_SESSION['orderType'] ?? 'dine-in';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/Group3_Database_Project/DB/content/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php"); ?>

    <main class="flex-fill">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card shadow-sm" style="background-color: #ffe9dd;">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0">Payment Information</h4>
                        </div>

                        <div class="card-body">
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label for="custName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="custName" name="custName"
                                        value="<?= htmlspecialchars($_POST['custName'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="contactPhone" class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control" id="contactPhone" name="contactPhone"
                                        value="<?= htmlspecialchars($_POST['contactPhone'] ?? '') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Order Type</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="deliveryType" id="dineInOption"
                                            value="dine-in" <?= ($orderType === 'dine-in') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-warning" for="dineInOption">Dine-In</label>

                                        <input type="radio" class="btn-check" name="deliveryType" id="takeawayOption"
                                            value="takeaway" <?= ($orderType === 'takeaway') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-warning" for="takeawayOption">Takeaway</label>
                                    </div>
                                </div>

                                <div class="mb-3" id="tableNoGroup" style="<?= ($orderType === 'takeaway') ? 'display: none;' : '' ?>">
                                    <label for="tableNo" class="form-label">Table Number</label>
                                    <select class="form-select" id="tableNo" name="tableNo">
                                        <option value="">Select table</option>
                                        <?php for ($i = 1; $i <= 20; $i++): ?>
                                            <option value="<?= $i ?>" <?= (($_POST['tableNo'] ?? '') == $i ? 'selected' : '') ?>>
                                                Table <?= $i ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Special Instructions</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"><?=
                                                                                                    htmlspecialchars($_POST['notes'] ?? '')
                                                                                                    ?></textarea>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Order Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Display Order Date -->
                                        <div class="mb-3">
                                            <strong>Order Date:</strong> <?= date('F j, Y - g:i A') ?>
                                        </div>

                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                                                    <span>RM <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                            <li class="list-group-item d-flex justify-content-between fw-bold">
                                                <span>Total</span>
                                                <span>RM <?= number_format($total, 2) ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-warning btn-lg">Complete Payment</button>
                                    <a href="cart.php" class="btn btn-outline-secondary">Back to Cart</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dineInOption = document.getElementById("dineInOption");
            const takeawayOption = document.getElementById("takeawayOption");
            const tableNoGroup = document.getElementById("tableNoGroup");

            function toggleTableNo() {
                tableNoGroup.style.display = dineInOption.checked ? "block" : "none";
                if (dineInOption.checked) {
                    document.getElementById("tableNo").setAttribute("required", "");
                } else {
                    document.getElementById("tableNo").removeAttribute("required");
                }
            }

            dineInOption.addEventListener("change", toggleTableNo);
            takeawayOption.addEventListener("change", toggleTableNo);

            // Initialize on page load
            toggleTableNo();
        });
    </script>
</body>

</html>