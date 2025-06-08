<?php
$servername = "localhost";
$username = "root";
$password = ""; // your DB password
$dbname = "database_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to load dishes by category
function renderDishes($conn, $category) {
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<div class="row g-4">';
    while ($row = $result->fetch_assoc()) {
        // Construct image path
        $img = !empty($row['image_url']) 
            ? '/Group3_Database_Project/DB/assets/' . $row['image_url'] 
            : '/Group3_Database_Project/DB/assets/images/menu-default.png';

        echo '
        <div class="col-md-4">
            <div class="card dish-card">
                <img src="' . htmlspecialchars($img) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>
                    <p class="card-text">RM ' . number_format($row['price'], 2) . '</p>
                    <div class="d-flex gap-5 justify-content-around">
                        <a href="#" class="btn btn-primary btn-buy">Buy Now</a>
                        <a href="#" class="btn btn-outline-primary btn-add">Add to Cart</a>
                    </div>
                </div>
            </div>
        </div>';
    }
    echo '</div>';
    $stmt->close();
}

?>
