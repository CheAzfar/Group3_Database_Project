<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isAdmin = isset($_SESSION['UserType']) && $_SESSION['UserType'] === 'admin';
$isStaff = isset($_SESSION['UserType']) && $_SESSION['UserType'] === 'staff';
// Get UserID for the welcome message
$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';

// Define pages where search should be hidden
$hideSearchPages = ['admin_menu.php', 'order_management.php', 'order_history.php','income_report.php','cart.php','about.php'];
$currentPage = basename($_SERVER['PHP_SELF']);
$hideSearch = in_array($currentPage, $hideSearchPages);
?>

<header>
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="/Group3_Database_Project/DB/content/pages/index.php">PUAN ZAI HIGHWAY</a>

            <!-- Toggler Button for Mobile -->
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center nav-spacing ms-lg-auto">
                    <?php if ($isAdmin): ?>
                    <!-- Admin Dropdown -->
                    <div class="dropdown me-lg-3 mb-2 mb-lg-0">
                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-gear me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/admin/admin_menu.php">
                                    <i class="fa-solid fa-gear menu-icon"></i> Admin Menu
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/pages/order_management.php">
                                    <i class="fa-solid fa-box-open menu-icon"></i> Order Management
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/admin/order_history.php">
                                    <i class="fa-solid fa-clock-rotate-left menu-icon"></i> Order History
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/admin/income_report.php">
                                    <i class="fa-solid fa-chart-line menu-icon"></i> Income Report
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/pages/logout.php">
                                    <i class="fa-solid fa-right-from-bracket menu-icon"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php if ($isStaff): ?>
                    <!-- Admin Dropdown -->
                    <div class="dropdown me-lg-3 mb-2 mb-lg-0">
                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-gear me-1"></i> Staff
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/pages/order_management.php">
                                    <i class="fa-solid fa-box-open menu-icon"></i> Order Management
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/admin/order_history.php">
                                    <i class="fa-solid fa-clock-rotate-left menu-icon"></i> Order History
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/Group3_Database_Project/DB/content/pages/logout.php">
                                    <i class="fa-solid fa-right-from-bracket menu-icon"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <a href="/Group3_Database_Project/DB/content/pages/index.php" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-house me-1"></i> Home</a>
                    <a href="/Group3_Database_Project/DB/content/pages/cart.php" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-cart-shopping me-1"></i> Cart</a>
                    <a href="/Group3_Database_Project/DB/content/pages/index.php#servicesSection" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-bell-concierge me-1"></i> Service</a>
                    <a href="/Group3_Database_Project/DB/content/pages/index.php#foodMenuAccordion" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-book-open me-1"></i> Menu</a>
                    <a href="/Group3_Database_Project/DB/content/pages/about.php" class="me-lg-3 mb-2 mb-lg-0"><i class="fa-solid fa-circle-info me-1"></i> About Us</a>

                    
                </div>

                <!-- Search form (visible only on non-admin pages) -->
                <?php if (!$hideSearch): ?>
                <form class="d-flex mt-3 mt-lg-0 ms-lg-3" role="search">
                    <div class="input-group">
                        <input class="form-control" type="search" placeholder="Search">
                        <button class="btn btn-outline-dark" type="submit">Search</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- Welcome message below the header -->
    <?php if ($userID): ?>
        <div class="container-fluid text-center py-2" style="background-color: #f8f9fa; border-top: 2px solid #e76f51; border-bottom: 2px solid #e76f51;">
            <p class="m-0" style="font-size: 18px; font-weight: 500; color: #343a40;">
                Welcome, <span style="color: #f4a261;"><?php echo htmlspecialchars($userID); ?>!</span>
            </p>
        </div>
    <?php endif; ?>
</header>
