<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="http://localhost/Group3_Database_Project/DB/content/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include($_SERVER['DOCUMENT_ROOT']."/Group3_Database_Project/DB/content/pages/header.php"); ?>
    
    <main class="flex-fill">
    <div class="container py-5">
        <h2 class="cart-title mb-4">Add To Cart</h2>
        <div class="row">
            <!-- Cart List -->
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
                    <tr>
                        <td><input type="checkbox" class="item-checkbox" checked></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="item-img me-2" alt="Nasi Lemak Ayam">
                                <span>Nasi Lemak Ayam</span>
                            </div>
                        </td>
                        <td>RM 4.00</td>
                        <td>
                            <div class="quantity-box">
                                <button>-</button>
                                <span>1</span>
                                <button>+</button>
                            </div>
                        </td>
                        <td class="remove-btn">X</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="item-checkbox"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="item-img me-2" alt="Nasi Lemak Ayam">
                                <span>Nasi Lemak Ayam</span>
                            </div>
                        </td>
                        <td>RM 4.00</td>
                        <td>
                            <div class="quantity-box">
                                <button>-</button>
                                <span>1</span>
                                <button>+</button>
                            </div>
                        </td>
                        <td class="remove-btn">X</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>RM 16.00</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total</span>
                        <span>RM 16.00</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span>RM 16.00</span>
                    </div>

                    <div class="order-type mt-4">
                        <h6 class="fw-bold mb-2">Order Type:</h6>
                        <div class="btn-group w-100" role="group" aria-label="Order Type">
                            <input type="radio" class="btn-check" name="orderType" id="pickupOption" value="pickup" autocomplete="off" checked>
                            <label class="btn btn-outline-warning" for="pickupOption">Pickup</label>

                            <input type="radio" class="btn-check" name="orderType" id="dineInOption" value="dine-in" autocomplete="off">
                            <label class="btn btn-outline-warning" for="dineInOption">Dine-In</label>
                        </div>
                    </div>
                    <button class="btn btn-order w-100 mt-3">Order Now</button>
                </div>
            </div>
        </div>
    </div>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT']."/Group3_Database_Project/DB/content/pages/footer.php"); ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select All Checkbox Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectAllCheckbox = document.getElementById("selectAll");
        const itemCheckboxes = document.querySelectorAll(".item-checkbox");

        selectAllCheckbox.addEventListener("change", function () {
            itemCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        });

        itemCheckboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                const allChecked = [...itemCheckboxes].every(c => c.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
    });
    </script>
</body>
</html>
