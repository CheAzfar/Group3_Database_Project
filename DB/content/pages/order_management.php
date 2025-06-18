<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");
// Authentication check
// Check UserID for both admin and staff
if (!isset($_SESSION['UserType']) || ($_SESSION['UserType'] !== 'admin' && $_SESSION['UserType'] !== 'staff')) {
    header("Location: login.php");
    exit();
}

// Status filter
$statusFilter = $_GET['status'] ?? 'all';
$whereClause = '';
if ($statusFilter !== 'all') {
    $whereClause = "WHERE o.OrderStatus = '" . $conn->real_escape_string($statusFilter) . "'";
}

// Get all orders with item count
$orders = $conn->query("
    SELECT o.*, 
           (SELECT COUNT(*) FROM order_items WHERE OrderID = o.OrderID) AS item_count,
           (SELECT SUM(Subtotal) FROM order_items WHERE OrderID = o.OrderID) AS calculated_total
    FROM orders o
    $whereClause
    ORDER BY o.OrderDate DESC
");

// Get status history for the first order (for demo)
$statusHistory = [];
if ($orders->num_rows > 0) {
    $orders->data_seek(0); // Reset pointer
    $firstOrderId = $orders->fetch_assoc()['OrderID'];
    $orders->data_seek(0); // Reset pointer again

    $historyStmt = $conn->prepare("SELECT * FROM order_status_history WHERE OrderID = ? ORDER BY ChangeDate DESC");
    $historyStmt->bind_param("i", $firstOrderId);
    $historyStmt->execute();
    $statusHistory = $historyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $historyStmt->close();
}

// Status badge colors
function getStatusColor($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'primary';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/Group3_Database_Project/DB/content/css/style.css">
    <style>
        .order-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
        }

        .order-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }

        .order-details.show {
            max-height: 1000px;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php");
    ?>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <main class="flex-fill">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order Management</h2>
                <div class="dropdown">
                    <button class="btn btn-outline-warning dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-filter"></i> Filter Orders
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?status=all">All Orders</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="?status=pending">Pending</a></li>
                        <li><a class="dropdown-item" href="?status=processing">Processing</a></li>
                        <li><a class="dropdown-item" href="?status=completed">Completed</a></li>
                        <li><a class="dropdown-item" href="?status=cancelled">Cancelled</a></li>
                    </ul>
                </div>
            </div>

            <!-- Orders List -->
            <div class="row">
                <div class="col-lg-8">
                    <?php if ($orders->num_rows > 0): ?>
                        <div class="accordion" id="ordersAccordion">
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <div class="card mb-3 order-card">
                                    <div class="card-header" id="heading<?= $order['OrderID'] ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button class="btn btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $order['OrderID'] ?>">
                                                <div>
                                                    <h5 class="mb-0">
                                                        Order #<?= $order['OrderID'] ?>
                                                        <span class="badge bg-<?= getStatusColor($order['OrderStatus']) ?> status-badge">
                                                            <?= ucfirst($order['OrderStatus']) ?>
                                                        </span>
                                                    </h5>
                                                    <small class="text-muted">
                                                        <?= date('M j, Y g:i A', strtotime($order['OrderDate'])) ?> |
                                                        <?= $order['DeliveryType'] ?> |
                                                        RM <?= number_format($order['calculated_total'], 2) ?>
                                                    </small>
                                                </div>
                                            </button>
                                            <div>
                                                <span class="badge bg-secondary">
                                                    <?= $order['item_count'] ?> item(s)
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapse<?= $order['OrderID'] ?>" class="collapse" data-bs-parent="#ordersAccordion">
                                        <div class="card-body">
                                            <!-- Order Details -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <p><strong>Customer:</strong> <?= htmlspecialchars($order['CustName']) ?></p>
                                                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['ContactPhone']) ?></p>
                                                    <?php if ($order['DeliveryType'] === 'dine-in' && $order['TableNo']): ?>
                                                        <p><strong>Table No:</strong> <?= $order['TableNo'] ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Order Type:</strong> <?= ucfirst($order['DeliveryType']) ?></p>
                                                    <p><strong>Payment Status:</strong>
                                                        <span class="badge bg-<?= $order['PaymentStatus'] === 'paid' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($order['PaymentStatus']) ?>
                                                        </span>
                                                    </p>
                                                    <?php if (!empty($order['Notes'])): ?>
                                                        <p><strong>Notes:</strong> <?= htmlspecialchars($order['Notes']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Order Items -->
                                            <h6>Order Items</h6>
                                            <div class="table-responsive">
                                                <?php
                                                $itemsStmt = $conn->prepare("
                                                    SELECT oi.*, mi.name 
                                                    FROM order_items oi
                                                    LEFT JOIN menu_items mi ON oi.item_id = mi.item_id
                                                    WHERE oi.OrderID = ?
                                                ");
                                                $itemsStmt->bind_param("i", $order['OrderID']);
                                                $itemsStmt->execute();
                                                $items = $itemsStmt->get_result();
                                                ?>

                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($item = $items->fetch_assoc()): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($item['name'] ?? 'Item #' . $item['item_id']) ?></td>
                                                                <td><?= $item['Quantity'] ?></td>
                                                                <td>RM <?= number_format($item['Price'], 2) ?></td>
                                                                <td>RM <?= number_format($item['Subtotal'], 2) ?></td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <!-- Status Update Form -->
                                            <div class="mt-3">
                                                <form method="POST" action="update_order_status.php">
                                                    <input type="hidden" name="order_id" value="<?= $order['OrderID'] ?>">
                                                    <div class="input-group mb-3">
                                                        <select class="form-select" name="new_status" required>
                                                            <option value="">Update Status</option>
                                                            <option value="pending" <?= $order['OrderStatus'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="processing" <?= $order['OrderStatus'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                                            <option value="completed" <?= $order['OrderStatus'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                                            <option value="cancelled" <?= $order['OrderStatus'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-warning">Update</button>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="notes" class="form-label">Status Notes (Optional)</label>
                                                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No orders found matching your criteria.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Status History Panel -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">Status History</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Fetch status history for all orders
                             $historyStmt = $conn->prepare("
                                SELECT osh.*, u.UserID, u.UserType 
                                FROM order_status_history osh
                                LEFT JOIN users u ON osh.ChangedBy = u.UserID
                                ORDER BY osh.ChangeDate DESC
                            ");
                            $historyStmt->execute();
                            $statusHistory = $historyStmt->get_result();
                            $historyStmt->close();

                            if ($statusHistory->num_rows > 0): ?>
                                <ul class="list-group list-group-flush">
                                    <?php while ($history = $statusHistory->fetch_assoc()): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="badge bg-<?= getStatusColor($history['OrderStatus']) ?>">
                                                    <?= ucfirst($history['OrderStatus']) ?>
                                                </span>
                                                <small class="text-muted"><?= date('M j, g:i A', strtotime($history['ChangeDate'])) ?></small>
                                            </div>
                                            <small>
                                                Order #<?= $history['OrderID'] ?> - Changed by:
                                                <span>
                                                    <?= htmlspecialchars($history['UserID']) ?> 
                                                    <!-- Displaying role in parentheses -->
                                                    <span style="color: <?= $history['UserType'] == 'admin' ? '#f4a261' : '#0D6EFD'; ?>; font-style: italic;">
                                                        (<?= ucfirst($history['UserType']) ?>)
                                                    </span>
                                                </span>
                                            </small>
                                            <?php if (!empty($history['Notes'])): ?>
                                                <div class="mt-1">
                                                    <small class="text-muted">Note: <?= htmlspecialchars($history['Notes']) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No status history found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 60 seconds
        setTimeout(function() {
            window.location.reload();
        }, 60000);

        // Show status notes when updating status
        document.querySelectorAll('select[name="new_status"]').forEach(select => {
            select.addEventListener('change', function() {
                const notesField = this.closest('form').querySelector('textarea[name="notes"]');
                if (this.value === 'cancelled') {
                    notesField.placeholder = "Please specify reason for cancellation...";
                    notesField.required = true;
                } else {
                    notesField.placeholder = "Optional notes...";
                    notesField.required = false;
                }
            });
        });
    </script>
</body>
</html>