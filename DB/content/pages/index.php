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
<body>
    <?php include($_SERVER['DOCUMENT_ROOT']."/Group3_Database_Project/DB/content/pages/header.php"); ?>
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
                    <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="img-fluid" alt="Menu Image" style="max-width: 400px;">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Popular Dishes Section -->
    <section class="popular-dishes mb-5" style="margin-bottom: 2% !important;">
        <div class="container">
            <h2 class="text-center mb-5">OUR POPULAR DISH</h2>
            
            <div class="row g-3">
                <!-- Dish 1 -->
                <div class="col-md-4">
                    <div class="card dish-card">
                        <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="card-img-top" alt="Dish 1">
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
                        <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="card-img-top" alt="Dish 2">
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
                        <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="card-img-top" alt="Dish 3">
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

    <!-- Food Menu Section -->
    <section class="food-menu-section py-5">
        <div class="container">
            <h2 class="text-center mb-5 text-warning">FOOD MENU</h2>
            <div class="accordion custom-accordion" id="foodMenuAccordion">
                <!-- Johor Signature Dishes -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingJohor">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseJohor" aria-expanded="true" aria-controls="collapseJohor">
                            Johor Signature Dishes
                        </button>
                    </h2>
                    <div id="collapseJohor" class="accordion-collapse collapse show" aria-labelledby="headingJohor" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <!-- Add dish list here -->
                           <div class="accordion-body">
                                <div class="row g-4">
                                    <!-- Dessert 1 -->
                                    <div class="col-md-4">
                                        <div class="card dish-card">
                                            <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="card-img-top" alt="Laksa Johor">
                                            <div class="card-body">
                                                <h5 class="card-title">Laksa Johor</h5>
                                                <p class="card-text">RM 12.90</p>
                                                <div class="d-flex gap-5 justify-content-around">
                                                    <a href="#" class="btn btn-primary btn-buy">Buy Now</a>
                                                    <a href="#" class="btn btn-outline-primary btn-add">Add to Cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dessert 2 -->
                                    <div class="col-md-4">
                                        <div class="card dish-card">
                                            <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="card-img-top" alt="Mee Bandung Muar">
                                            <div class="card-body">
                                                <h5 class="card-title">Mee Bandung Muar</h5>
                                                <p class="card-text">RM 13.90</p>
                                                <div class="d-flex gap-5 justify-content-around">
                                                    <a href="#" class="btn btn-primary btn-buy">Buy Now</a>
                                                    <a href="#" class="btn btn-outline-primary btn-add">Add to Cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dessert 3 -->
                                    <div class="col-md-4">
                                        <div class="card dish-card">
                                            <img src="/Group3_Database_Project/DB/assets/images/menu-default.png" class="card-img-top" alt="Asam Pedas Johor">
                                            <div class="card-body">
                                                <h5 class="card-title">Asam Pedas Johor</h5>
                                                <p class="card-text">RM 16.90/fish</p>
                                                <div class="d-flex gap-5 justify-content-around">
                                                    <a href="#" class="btn btn-primary btn-buy">Buy Now</a>
                                                    <a href="#" class="btn btn-outline-primary btn-add">Add to Cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rice & Noodles -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingRice">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRice" aria-expanded="false" aria-controls="collapseRice">
                            Rice & Noodles
                        </button>
                    </h2>
                    <div id="collapseRice" class="accordion-collapse collapse" aria-labelledby="headingRice" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li>Nasi Lemak</li>
                                <li>Nasi Goreng Kampung</li>
                                <li>Char Kuey Teow</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Proteins & Sides -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingProteins">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProteins" aria-expanded="false" aria-controls="collapseProteins">
                            Proteins & Sides
                        </button>
                    </h2>
                    <div id="collapseProteins" class="accordion-collapse collapse" aria-labelledby="headingProteins" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li>Ayam Goreng Berempah</li>
                                <li>Ikan Bakar</li>
                                <li>Sambal Sotong</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Snacks & Appetizers -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSnacks">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSnacks" aria-expanded="false" aria-controls="collapseSnacks">
                            Snacks & Appetizers
                        </button>
                    </h2>
                    <div id="collapseSnacks" class="accordion-collapse collapse" aria-labelledby="headingSnacks" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li>Keropok Lekor</li>
                                <li>Spring Rolls</li>
                                <li>Popiah Goreng</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- DRINKS MENU -->
                <h2 class="text-center mt-5 mb-4 text-warning">DRINKS MENU</h2>

                <!-- Traditional Beverages -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTraditional">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTraditional" aria-expanded="false" aria-controls="collapseTraditional">
                            Traditional Beverages
                        </button>
                    </h2>
                    <div id="collapseTraditional" class="accordion-collapse collapse" aria-labelledby="headingTraditional" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li>Teh Tarik</li>
                                <li>Sirap Bandung</li>
                                <li>Air Janda Pulang</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Fresh & Cold -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingCold">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCold" aria-expanded="false" aria-controls="collapseCold">
                            Fresh & Cold
                        </button>
                    </h2>
                    <div id="collapseCold" class="accordion-collapse collapse" aria-labelledby="headingCold" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li>Fresh Watermelon Juice</li>
                                <li>Iced Lemon Tea</li>
                                <li>Air Kelapa</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Modern Favorites -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingModern">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseModern" aria-expanded="false" aria-controls="collapseModern">
                            Modern Favorites
                        </button>
                    </h2>
                    <div id="collapseModern" class="accordion-collapse collapse" aria-labelledby="headingModern" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li>Iced Coffee Latte</li>
                                <li>Matcha Frappé</li>
                                <li>Sparkling Passionfruit</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- DESSERTS -->
                <h2 class="text-center mt-5 mb-4 text-warning">DESSERTS</h2>

                <!-- Dessert Menu -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDesserts">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDesserts" aria-expanded="false" aria-controls="collapseDesserts">
                            Desserts
                        </button>
                    </h2>
                    <div id="collapseDesserts" class="accordion-collapse collapse" aria-labelledby="headingDesserts" data-bs-parent="#foodMenuAccordion">
                        <div class="accordion-body">
                            <ul>
                                <li>Cendol</li>
                                <li>ABC (Ais Batu Campur)</li>
                                <li>Mango Sticky Rice</li>
                            </ul>
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
    <?php include($_SERVER['DOCUMENT_ROOT']."/Group3_Database_Project/DB/content/pages/footer.php"); ?>

    <!-- Bootstrap JS Bundle with Popper (for navbar toggling) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>