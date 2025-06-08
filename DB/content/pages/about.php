<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Clayport Ikan Bakar</title>
    <link rel="stylesheet" href="http://localhost/Group3_Database_Project/DB/content/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .hero-about {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('http://localhost/Group3_Database_Project/DB/assets/images/AboutBanner.jpg');
            background-size: cover;
            background-position: center;
            height: 60vh;
            display: flex;
            align-items: center;
            color: white;
        }
        .flavor-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .flavor-card:hover {
            transform: translateY(-10px);
        }
        .flavor-icon {
            font-size: 2.5rem;
            color: #ff6b35;
            margin-bottom: 1rem;
        }
        .about-img {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .signature-dish {
            border-left: 5px solid #ff6b35;
            padding-left: 20px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include($_SERVER['DOCUMENT_ROOT']."/Group3_Database_Project/DB/content/pages/header.php"); ?>
    <main class="flex-fill">

    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container text-center">
            <h1 class="display-3 fw-bold mb-4">Puan Zai Highway</h1>
            <p class="lead fs-3">Raso Pahang, Ori Tok Tambah!</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <img src="/Group3_Database_Project/DB/assets/images/Dish.jpg" alt="Clayport Ikan Bakar Restaurant" class="img-fluid about-img">
                </div>
                <div class="col-lg-6">
                    <h2 class="mb-4 fw-bold">Our Story</h2>
                    <p class="lead text-muted">Serving authentic flavors since 2010</p>
                    <p>Welcome to Puan Zai Highway Restaurant, a culinary haven where the rich traditions of Malaysian seafood meet contemporary dining. Our journey began with a simple passion - to share the authentic taste of perfectly grilled fish (ikan bakar) Patin Tempoyak with our community.</p>
                    <p>What started as a humble eatery has blossomed into a beloved destination for food enthusiasts seeking genuine flavors and warm hospitality.</p>
                    
                    <div class="signature-dish my-4">
                        <h4 class="fw-bold">Our Signature Dish</h4>
                        <p>The legendary Patin Tempoyak Ikan Bakar - marinated with our secret blend of spices, fermented durian and grilled to perfection over charcoal, served with our homemade sambal that has customers coming back for more.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flavor Highlights -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">The Claypot Experience</h2>
                <p class="lead text-muted">What makes us special</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="flavor-card card h-100 text-center p-4">
                        <div class="flavor-icon">
                            <i class="fas fa-fish"></i>
                        </div>
                        <h4>Fresh Ingredients</h4>
                        <p>Daily catches from local fishermen, ensuring the freshest seafood for our dishes.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="flavor-card card h-100 text-center p-4">
                        <div class="flavor-icon">
                            <i class="fas fa-mortar-pestle"></i>
                        </div>
                        <h4>Authentic Recipes</h4>
                        <p>Traditional recipes passed down through generations, prepared with care.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="flavor-card card h-100 text-center p-4">
                        <div class="flavor-icon">
                            <i class="fas fa-ice-cream"></i>
                        </div>
                        <h4>Sweet Endings</h4>
                        <p>Complete your meal with our selection of traditional Malaysian desserts and refreshing drinks.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Preview -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">  <!-- Added justify-content-center -->
                <div class="col-lg-8 text-center">  <!-- Changed width to col-lg-8 and added text-center -->
                    <h2 class="fw-bold mb-4">Our Culinary Offerings</h2>
                    <div class="accordion" id="menuAccordion">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    Signature Grilled Seafood
                                </button>
                            </h3>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#menuAccordion">
                                <div class="accordion-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Claypot Tempoyak Ikan Bakar</strong> - Our famous grilled fish</li>
                                        <li class="mb-2"><strong>Sambal Udang</strong> - Spicy chili prawns</li>
                                        <li class="mb-2"><strong>Sotong Panggang</strong> - Grilled squid</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    Traditional Desserts
                                </button>
                            </h3>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                                <div class="accordion-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Ais Kacang</strong> - Shaved ice with sweet toppings</li>
                                        <li class="mb-2"><strong>Cendol</strong> - Coconut milk with palm sugar</li>
                                        <li class="mb-2"><strong>Kuih-Muih</strong> - Assorted traditional cakes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-warning text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">Ready to Taste the Difference?</h2>
            <p class="lead mb-4">Visit us today and experience authentic Malaysian seafood at its finest</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#" class="btn btn-dark btn-lg px-4">View Menu</a>
            </div>
        </div>
    </section>

    <!-- Services Section (original) -->
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
                    </div>
                </div>
            </div>
        </div>
    </section>
    </main>
    <?php include($_SERVER['DOCUMENT_ROOT']."/Group3_Database_Project/DB/content/pages/footer.php"); ?>

    <!-- Bootstrap JS Bundle with Popper (for navbar toggling) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>