<?php
session_start();

// Restrict access to admin only
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filter handling
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$typeFilter = isset($_GET['delivery']) ? $_GET['delivery'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchFilter = isset($_GET['search']) ? $_GET['search'] : '';

include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");

$filterSql = "WHERE 1=1";
if (!empty($statusFilter)) {
    $filterSql .= " AND OrderStatus = '" . $conn->real_escape_string($statusFilter) . "'";
}
if (!empty($typeFilter)) {
    $filterSql .= " AND DeliveryType = '" . $conn->real_escape_string($typeFilter) . "'";
}
if (!empty($dateFilter)) {
    $filterSql .= " AND DATE(OrderDate) = '" . $conn->real_escape_string($dateFilter) . "'";
}
if (!empty($searchFilter)) {
    $filterSql .= " AND CustName LIKE '%" . $conn->real_escape_string($searchFilter) . "%'";
}

$totalResult = $conn->query("SELECT COUNT(*) as total FROM orders $filterSql");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$query = "SELECT * FROM orders $filterSql ORDER BY OrderDate DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <link rel="stylesheet" href="http://localhost/Group3_Database_Project/DB/content/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        table.table thead tr th {
            background-color: #F4A261 !important;
            color: white !important;
            font-weight: bold;
        }
        .badge.completed {
            background-color: #28a745;
        }
        .badge.processing {
            background-color: #ffc107;
            color: black;
        }
        .badge.cancelled {
            background-color: #dc3545;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php"); ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center">Order History</h2>
        <div class="text-end mb-3">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Search customer..." value="<?= htmlspecialchars($searchFilter) ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="processing" <?= $statusFilter === 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="delivery" class="form-select">
                <option value="">All Delivery Types</option>
                <option value="dine-in" <?= $typeFilter === 'dine-in' ? 'selected' : '' ?>>Dine-In</option>
                <option value="takeaway" <?= $typeFilter === 'takeaway' ? 'selected' : '' ?>>Takeaway</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($dateFilter) ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-warning w-100">Filter</button>
        </div>
    </form>

    <div id="printArea">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Table No</th>
                    <th>Order Date</th>
                    <th>Amount (RM)</th>
                    <th>Delivery</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Contact</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $statusClass = strtolower($row['OrderStatus']); ?>
                    <tr>
                        <td><?= htmlspecialchars($row['OrderID']) ?></td>
                        <td><?= htmlspecialchars($row['CustName']) ?></td>
                        <td><?= htmlspecialchars($row['TableNo']) ?></td>
                        <td><?= htmlspecialchars($row['OrderDate']) ?></td>
                        <td><?= number_format($row['TotalAmount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['DeliveryType']) ?></td>
                        <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($row['OrderStatus']) ?></span></td>
                        <td><?= htmlspecialchars($row['PaymentStatus']) ?></td>
                        <td><?= htmlspecialchars($row['ContactPhone']) ?></td>
                        <td><?= htmlspecialchars($row['Notes']) ?></td>
                    </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="10" class="text-center">No order history found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>&delivery=<?= urlencode($typeFilter) ?>&date=<?= urlencode($dateFilter) ?>&search=<?= urlencode($searchFilter) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
