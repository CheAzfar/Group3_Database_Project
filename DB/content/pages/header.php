<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isAdmin = isset($_SESSION['UserType']) && $_SESSION['UserType'] === 'admin';

?>

<header>
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="#">PUAN ZAI HIGHWAY</a>

            <!-- Toggler Button for Mobile -->
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center nav-spacing ms-lg-auto">
                    <a href="index.php" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-house me-1"></i> Home</a>
                    <a href="cart.php" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-cart-shopping me-1"></i> Cart</a>
                    <a href="index.php#servicesSection" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-bell-concierge me-1"></i> Service</a>
                    <a href="index.php#foodMenuAccordion" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-book-open me-1"></i> Menu</a>
                    <a href="about.php" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-circle-info me-1"></i> About Us</a>

                    <?php if ($isAdmin): ?>
                    <!-- Admin Dropdown -->
                    <div class="dropdown me-lg-3 mb-2 mb-lg-0">
                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-gear me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/Test/admin_menu.php">
                                    <i class="fa-solid fa-gear menu-icon"></i> Admin Menu
                                </a>
                                </li>
                                <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/pages/order_management.php">
                                    <i class="fa-solid fa-box-open menu-icon"></i> Order Management
                                </a>
                                </li>
                                <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/pages/logout.php">
                                    <i class="fa-solid fa-right-from-bracket menu-icon"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Logout button for Admin -->
                    
                    <?php endif; ?>
                </div>

                <!-- Search form (mobile: stacks below links) -->
                <form class="d-flex mt-3 mt-lg-0 ms-lg-3" role="search">
                    <div class="input-group">
                        <input class="form-control" type="search" placeholder="Search">
                        <button class="btn btn-outline-dark" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </nav>
</header>
