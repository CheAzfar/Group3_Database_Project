<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="http://localhost/DB/content/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT']."/DB/content/pages/header.php"); ?>
    <!-- Hero Section -->
    <section class="hero-section py-5">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                <!-- Text Content -->
                <div class="text-content mb-4 mb-md-0">
                    <h1 class="display-3 fw-bold mb-3">PUAN ZAI HIGHWAY</h1>
                    <p class="lead mb-4">Raso Pahang, Ori Tok Tambah!</p>
                    <button class="btn btn-order btn-lg">ORDER NOW</button>
                </div>
                <!-- Image Content -->
                <div class="hero-image ms-md-5">
                    <img src="/DB/assets/images/menu-default.png" class="img-fluid" alt="Menu Image" style="max-width: 400px;">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Popular Dishes Section -->
    <section class="popular-dishes mb-5" style="margin-bottom: 10% !important;">
        <div class="container">
            <h2 class="text-center mb-5">Our Popular Dishes</h2>
            
            <div class="row g-3">
                <!-- Dish 1 -->
                <div class="col-md-4">
                    <div class="card dish-card">
                        <img src="/DB/assets/images/menu-default.png" class="card-img-top" alt="Dish 1">
                        <div class="card-body">
                            <h5 class="card-title">Dish Name 1</h5>
                            <p class="card-text">Description of the dish.</p>
                            <div class="d-flex gap-5 justify-content-around">
                                <a href="#" class="btn btn-primary btn-buy">Buy Now</a>
                                <a href="#" class="btn btn-outline-primary btn-add">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Dish 2 -->
                <div class="col-md-4">
                    <div class="card dish-card">
                        <img src="/DB/assets/images/menu-default.png" class="card-img-top" alt="Dish 2">
                        <div class="card-body">
                            <h5 class="card-title">Dish Name 2</h5>
                            <p class="card-text">Description of the dish.</p>
                            <div class="d-flex gap-5 justify-content-around">
                                <a href="#" class="btn btn-primary btn-buy">Buy Now</a>
                                <a href="#" class="btn btn-outline-primary btn-add">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dish 3 -->
                <div class="col-md-4">
                    <div class="card dish-card">
                        <img src="/DB/assets/images/menu-default.png" class="card-img-top" alt="Dish 3">
                        <div class="card-body">
                            <h5 class="card-title">Dish Name 3</h5>
                            <p class="card-text">Description of the dish.</p>
                            <div class="d-flex gap-5 justify-content-around">
                                <a href="#" class="btn btn-primary btn-buy">Buy Now</a>
                                <a href="#" class="btn btn-outline-primary btn-add">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Services Section -->
    <section class="services-section py-5 bg-light">
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
                        <a href="#" class="btn btn-outline-warning mt-auto">Book Now</a>
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
                        <a href="#" class="btn btn-outline-warning mt-auto">Order Now</a>
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
                        <a href="#" class="btn btn-outline-warning mt-auto">Learn More</a>
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
                        <a href="#" class="btn btn-outline-warning mt-auto">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include($_SERVER['DOCUMENT_ROOT']."/DB/content/pages/footer.php"); ?>

    <!-- Bootstrap JS Bundle with Popper (for navbar toggling) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>