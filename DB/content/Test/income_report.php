<?php
session_start();
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");

// Defaults
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$paymentStatus = $_GET['payment_status'] ?? '';
$deliveryType = $_GET['delivery_type'] ?? '';

// Query
$query = "SELECT * FROM orders WHERE OrderDate BETWEEN ? AND ?";
$params = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];
$types = 'ss';

if ($paymentStatus !== '') {
    $query .= " AND PaymentStatus = ?";
    $params[] = $paymentStatus;
    $types .= 's';
}
if ($deliveryType !== '') {
    $query .= " AND DeliveryType = ?";
    $params[] = $deliveryType;
    $types .= 's';
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total income
$totalIncome = 0;
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
    $totalIncome += $row['TotalAmount'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Income Report</title>
    <link rel="stylesheet" href="http://localhost/Group3_Database_Project/DB/content/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .income-table th {
            background-color: #F4A261 !important;
            color: white !important;
            font-weight: bold !important;
        }
        .btn-custom {
            background-color: #F4A261;
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: #d65b3f;
            color: white;
        }
        @media print {
            body * {
                visibility: hidden;
            }

            #incomeReportTable, #incomeReportTable * {
                visibility: visible;
            }

            #incomeReportTable {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<main class="container py-5 flex-fill">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center">Income Report</h2>
        <div class="text-end mb-3">
            <button type="button" class="btn btn-custom" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
    </div>

    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Payment Status</label>
            <select name="payment_status" class="form-select">
                <option value="">All</option>
                <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="unpaid" <?= $paymentStatus === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Delivery Type</label>
            <select name="delivery_type" class="form-select">
                <option value="">All</option>
                <option value="dine-in" <?= $deliveryType === 'dine-in' ? 'selected' : '' ?>>Dine-In</option>
                <option value="takeaway" <?= $deliveryType === 'takeaway' ? 'selected' : '' ?>>Takeaway</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-warning w-100">Filter</button>
        </div>
    </form>

    <div id="incomeReportTable">
        <h5>Total Income: <span class="text-success fw-bold">RM <?= number_format($totalIncome, 2) ?></span></h5>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped income-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Order Date</th>
                        <th>Amount (RM)</th>
                        <th>Payment</th>
                        <th>Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['OrderID']) ?></td>
                                <td><?= htmlspecialchars($order['CustName']) ?></td>
                                <td><?= htmlspecialchars($order['OrderDate']) ?></td>
                                <td><?= number_format($order['TotalAmount'], 2) ?></td>
                                <td><?= htmlspecialchars($order['PaymentStatus']) ?></td>
                                <td><?= htmlspecialchars($order['DeliveryType']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
