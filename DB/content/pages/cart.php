<?php
session_start();

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart = $_SESSION['cart'];

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['action'])) {
    $itemId = $_POST['item_id'];
    $action = $_POST['action'];

    if (isset($cart[$itemId])) {
        if ($action === 'increase') {
            $cart[$itemId]['quantity']++;
        } elseif ($action === 'decrease') {
            $cart[$itemId]['quantity']--;
            if ($cart[$itemId]['quantity'] < 1) {
                unset($cart[$itemId]);
            }
        }
        $_SESSION['cart'] = $cart; // Save back to session
        header("Location: cart.php");
        exit();
    }
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/Group3_Database_Project/DB/content/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php"); ?>

    <main class="flex-fill">
        <div class="container py-5">
            <h2 class="cart-title mb-4">Add To Cart</h2>
            <div class="row">
                <div class="col-lg-8">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $id => $item): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="item-checkbox" checked
                                            data-price="<?= $item['price'] ?>"
                                            data-quantity="<?= $item['quantity'] ?>">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/Group3_Database_Project/DB/assets/<?= htmlspecialchars($item['image_url']) ?>"
                                                class="item-img me-2" alt="<?= htmlspecialchars($item['name']) ?>">
                                            <span><?= htmlspecialchars($item['name']) ?></span>
                                        </div>
                                    </td>
                                    <td>RM <?= number_format($item['price'], 2) ?></td>
                                    <td>
                                        <div class="quantity-box">
                                            <form method="POST" style="display: flex; align-items: center; gap: 5px;">
                                                <input type="hidden" name="item_id" value="<?= $id ?>">
                                                <button type="submit" name="action" value="decrease"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    <?= $item['quantity'] <= 1 ? '' : '' ?>>-</button>
                                                <span class="mx-2"><?= $item['quantity'] ?></span>
                                                <button type="submit" name="action" value="increase"
                                                    class="btn btn-sm btn-outline-secondary">+</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>RM <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary">
                        <h5 class="fw-bold mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal</span>
                            <span id="cart-total">RM <?= number_format($total, 2) ?></span>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>Total</span>
                            <span id="cart-total-final">RM <?= number_format($total, 2) ?></span>
                        </div>

                        <div class="order-type mt-4">
                            <h6 class="fw-bold mb-2">Order Type:</h6>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="orderType" id="dineInOption" value="dine-in" checked>
                                <label class="btn btn-outline-warning" for="dineInOption">Dine-In</label>

                                <input type="radio" class="btn-check" name="orderType" id="takeawayOption" value="takeaway">
                                <label class="btn btn-outline-warning" for="takeawayOption">Takeaway</label>
                            </div>
                        </div>
                        <button onclick="location.href='payment.php';" class="btn btn-order w-100 mt-3">Order Now</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectAllCheckbox = document.getElementById("selectAll");
            const itemCheckboxes = document.querySelectorAll(".item-checkbox");
            const cartTotalDisplay = document.getElementById("cart-total");
            const cartTotalFinal = document.getElementById("cart-total-final");

            function updateTotal() {
                let total = 0;
                itemCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        const price = parseFloat(cb.dataset.price);
                        const quantity = parseInt(cb.dataset.quantity);
                        total += price * quantity;
                    }
                });
                cartTotalDisplay.textContent = "RM " + total.toFixed(2);
                cartTotalFinal.textContent = "RM " + total.toFixed(2);
            }

            selectAllCheckbox.addEventListener("change", function() {
                itemCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
                updateTotal();
            });

            itemCheckboxes.forEach(cb => {
                cb.addEventListener("change", function() {
                    const allChecked = [...itemCheckboxes].every(c => c.checked);
                    selectAllCheckbox.checked = allChecked;
                    updateTotal();
                });
            });

            updateTotal();
        });
    </script>
</body>

</html>