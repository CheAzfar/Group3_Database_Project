<?php
session_start();
function getPopularDishes($conn)
{
    // Get 3 random dishes as "popular" - modify query as needed
    $query = "SELECT * FROM menu_items ORDER BY popularity_score DESC LIMIT 3";
    $result = $conn->query($query);

    // Fallback if query fails
    if (!$result) {
        $query = "SELECT * FROM menu_items LIMIT 3";
        $result = $conn->query($query);
    }
    return $result;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="http://localhost/Group3_Database_Project/DB/content/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <?php
    include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/menu.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/header.php");
    function searchMenuItems($conn, $keyword) {
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE name LIKE ?");
        $like = '%' . $keyword . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        return $stmt->get_result();
    }
    ?>

    <main class="flex-fill">
        <!-- Hero Section -->
        <section class="hero-section py-5">
            <div class="container">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                    <!-- Text Content -->
                    <div class="text-content mb-4 mb-md-0">
                        <h1 class="display-3 fw-bold mb-3">PUAN ZAI HIGHWAY</h1>
                        <p class="lead mb-4">Raso Pahang, Ori Tok Tambah!</p>
                        <button class="btn btn-order btn-lg" onclick="window.location.href='/Group3_Database_Project/DB/content/pages/index.php#foodMenuAccordion'">ORDER NOW</button>
                    </div>
                    <!-- Image Content -->
                    <div class="hero-image ms-md-5">
                        <img src="/Group3_Database_Project/DB/assets/images/default.png" class="img-fluid" alt="Menu Image" style="max-width: 400px;">
                    </div>
                </div>
            </div>
        </section>
        <!-- Search Section -->
        <?php if (!empty($_GET['search'])): ?>
            <section id="searchResults" class="search-results-section py-5">
                <div class="container">
                    <h2 class="text-center mb-4" style="color: #f4a261;">
                        Search Results for: "<?= htmlspecialchars($_GET['search']) ?>"
                    </h2>
                    <div class="row g-3">
                        <?php
                        $results = searchMenuItems($conn, $_GET['search']);
                        if ($results->num_rows > 0):
                            while ($dish = $results->fetch_assoc()):
                                $imagePath = !empty($dish['image_url'])
                                    ? '/Group3_Database_Project/DB/assets/' . $dish['image_url']
                                    : '/Group3_Database_Project/DB/assets/images/menu-default.png';
                        ?>
                            <div class="col-md-4">
                                <div class="card dish-card">
                                    <img src="<?= htmlspecialchars($imagePath) ?>" class="card-img-top" alt="<?= htmlspecialchars($dish['name']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($dish['name']) ?></h5>
                                        <p class="card-text">RM <?= htmlspecialchars($dish['price']) ?></p>
                                        <div class="d-flex gap-3 justify-content-around">
                                            <form method="POST" action="/Group3_Database_Project/DB/content/pages/buy_now_handler.php">
                                                <input type="hidden" name="item_id" value="<?= $dish['item_id'] ?>">
                                                <input type="hidden" name="name" value="<?= htmlspecialchars($dish['name']) ?>">
                                                <input type="hidden" name="price" value="<?= $dish['price'] ?>">
                                                <input type="hidden" name="image_url" value="<?= $dish['image_url'] ?>">
                                                <button type="submit" class="btn btn-primary btn-buy">Buy Now</button>
                                            </form>

                                            <form method="POST" action="/Group3_Database_Project/DB/content/pages/add_to_cart.php">
                                                <input type="hidden" name="item_id" value="<?= $dish['item_id'] ?>">
                                                <input type="hidden" name="name" value="<?= htmlspecialchars($dish['name']) ?>">
                                                <input type="hidden" name="price" value="<?= $dish['price'] ?>">
                                                <input type="hidden" name="image_url" value="<?= $dish['image_url'] ?>">
                                                <button type="submit" class="btn btn-outline-primary btn-add">Add to Cart</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; else: ?>
                            <div class="col-12 text-center">
                                <p class="text-danger">No results found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Popular Dishes Section -->
        <section class="popular-dishes mb-5" style="margin-bottom: 2% !important;">
            <div class="container">
                <h2 class="text-center mb-5">OUR POPULAR DISH</h2>
                <div class="row g-3">
                    <?php
                    $popularDishes = getPopularDishes($conn);
                    while ($dish = $popularDishes->fetch_assoc()):
                        $imagePath = !empty($dish['image_url'])
                            ? '/Group3_Database_Project/DB/assets/' . $dish['image_url']
                            : '/Group3_Database_Project/DB/assets/images/menu-default.png';
                    ?>
                        <div class="col-md-4">
                            <div class="card dish-card">
                                <img src="<?= htmlspecialchars($imagePath) ?>" class="card-img-top" alt="<?= htmlspecialchars($dish['name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($dish['name']); ?></h5>
                                    <p class="card-text">RM <?= htmlspecialchars($dish['price']); ?></p>
                                    <div class="d-flex gap-5 justify-content-around">
                                        <!-- ✅ Buy Now Form -->
                                        <form method="POST" action="/Group3_Database_Project/DB/content/pages/buy_now_handler.php">
                                            <input type="hidden" name="item_id" value="<?= $dish['item_id'] ?>">
                                            <input type="hidden" name="name" value="<?= htmlspecialchars($dish['name']) ?>">
                                            <input type="hidden" name="price" value="<?= $dish['price'] ?>">
                                            <input type="hidden" name="image_url" value="<?= $dish['image_url'] ?>">
                                            <button type="submit" class="btn btn-primary btn-buy">Buy Now</button>
                                        </form>

                                        <!-- ✅ Add to Cart Form -->
                                        <form method="POST" action="/Group3_Database_Project/DB/content/pages/add_to_cart.php">
                                            <input type="hidden" name="item_id" value="<?= $dish['item_id'] ?>">
                                            <input type="hidden" name="name" value="<?= htmlspecialchars($dish['name']) ?>">
                                            <input type="hidden" name="price" value="<?= $dish['price'] ?>">
                                            <input type="hidden" name="image_url" value="<?= $dish['image_url'] ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-add">Add to Cart</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <!-- Food Menu Section -->
        <section id="foodMenuAccordion" class="food-menu-section py-5">
            <div class="container">
                <h2 class="text-center mb-5 text-warning">FOOD MENU</h2>
                <div class="accordion custom-accordion" id="foodMenuAccordion">
                    <!-- Rice & Noodles -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingRice">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRice">
                                Rice & Noodles
                            </button>
                        </h2>
                        <div id="collapseRice" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <?php renderDishes($conn, 'Rice & Noodles'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Proteins & Sides -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingProteins">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProteins">
                                Proteins & Sides
                            </button>
                        </h2>
                        <div id="collapseProteins" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <?php renderDishes($conn, 'Proteins & Sides'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Snacks & Appetizers -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSnacks">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSnacks">
                                Snacks & Appetizers
                            </button>
                        </h2>
                        <div id="collapseSnacks" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <?php renderDishes($conn, 'Snacks & Appetizers'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- DRINKS -->
                    <h2 class="text-center mt-5 mb-4 text-warning">DRINKS MENU</h2>

                    <!-- Traditional Beverages -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTraditional">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTraditional">
                                Traditional Beverages
                            </button>
                        </h2>
                        <div id="collapseTraditional" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <?php renderDishes($conn, 'Traditional Beverages'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Fresh & Cold -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCold">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCold">
                                Fresh & Cold
                            </button>
                        </h2>
                        <div id="collapseCold" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <?php renderDishes($conn, 'Fresh & Cold'); ?>
                            </div>
                        </div>
                    </div>

                    <!-- DESSERTS -->
                    <h2 class="text-center mt-5 mb-4 text-warning">DESSERTS</h2>

                    <!-- Desserts -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingDesserts">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDesserts">
                                Desserts
                            </button>
                        </h2>
                        <div id="collapseDesserts" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <?php renderDishes($conn, 'Desserts'); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <!-- Services Section -->
        <section id="servicesSection" class="services-section py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-5">Our Services</h2>

                <div class="row g-4">
                    <!-- Service 1 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card service-card h-100 border-0 text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="fas fa-calendar-check fa-3x text-warning"></i>
                            </div>
                            <h4 class="mb-3">Reserve Your Own Table</h4>
                            <p>Book in advance to guarantee your dining experience</p>
                            <!-- <a href="#" class="btn btn-outline-warning mt-auto">Book Now</a> -->
                        </div>
                    </div>

                    <!-- Service 2 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card service-card h-100 border-0 text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="fas fa-shopping-bag fa-3x text-warning"></i>
                            </div>
                            <h4 class="mb-3">Pickup Advance Order</h4>
                            <p>Order ahead and pick up your food without waiting</p>
                            <!-- <a href="#" class="btn btn-outline-warning mt-auto">Order Now</a> -->
                        </div>
                    </div>

                    <!-- Service 3 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card service-card h-100 border-0 text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="fas fa-utensils fa-3x text-warning"></i>
                            </div>
                            <h4 class="mb-3">Self Service From Your Table</h4>
                            <p>Use our app to order directly from your table</p>
                            <!-- <a href="#" class="btn btn-outline-warning mt-auto">Learn More</a> -->
                        </div>
                    </div>

                    <!-- Service 4 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card service-card h-100 border-0 text-center p-4">
                            <div class="service-icon mb-3">
                                <i class="fas fa-concierge-bell fa-3x text-warning"></i>
                            </div>
                            <h4 class="mb-3">Our Services</h4>
                            <p>Discover all the ways we can serve you better</p>
                            <!-- <a href="#" class="btn btn-outline-warning mt-auto">View All</a> -->
                        </div>
                    </div>
                </div>
            </div>x
        </section>
    </main>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/Group3_Database_Project/DB/content/pages/footer.php"); ?>

    <!-- Bootstrap JS Bundle with Popper (for navbar toggling) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    
</body>

</html>