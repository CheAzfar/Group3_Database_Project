<?php
$host = 'localhost';
$dbname = 'database_project';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// File upload directory constants
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/Group3_Database_Project/DB/assets/menu/');
define('UPLOAD_URL', '/Group3_Database_Project/DB/assets/menu/');
define('UPLOAD_BASE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/Group3_Database_Project/DB/assets/menu/');
define('UPLOAD_BASE_URL', '/Group3_Database_Project/DB/assets/menu/');

/**
 * Handles file uploads with category-specific folders
 * 
 * @param array $file The $_FILES array element
 * @param string $category The item category
 * @param string $existing_file The existing file path (if updating)
 * @return string The new file URL
 * @throws Exception If upload fails
 */
function handleFileUpload($file, $category, $existing_file = '') {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $file['error']);
    }

    // Validate file type and size
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception("Only JPG, PNG, GIF, and WebP images are allowed.");
    }

    if ($file['size'] > $max_size) {
        throw new Exception("File size must be less than 2MB.");
    }

    // Map category names to folder names
    $category_folders = [
        'Traditional Beverages' => 'traditionalBeverages',
        'Snacks & Appetizers' => 'snacksNappetizers',
        'Rice & Noodles' => 'riceNnoodles',
        'Proteins & Sides' => 'proteinsNsides',
        'Fresh & Cold' => 'freshNcold',
        'Desserts' => 'desserts'
    ];

    // Get the folder name for this category
    $folder_name = $category_folders[$category] ?? strtolower(str_replace(' ', '', $category));

    // Create category folder if it doesn't exist
    $category_dir = UPLOAD_BASE_DIR . $folder_name . '/';
    if (!file_exists($category_dir)) {
        if (!mkdir($category_dir, 0755, true)) {
            throw new Exception("Failed to create directory for category.");
        }
    }

    // Keep original filename but ensure it's safe
    $filename = preg_replace('/[^a-z0-9\._-]/i', '_', basename($file['name']));
    $destination = $category_dir . $filename;

    // Check if file already exists and append a number if it does
    $counter = 1;
    $pathinfo = pathinfo($filename);
    while (file_exists($destination)) {
        $filename = $pathinfo['filename'] . '_' . $counter . '.' . $pathinfo['extension'];
        $destination = $category_dir . $filename;
        $counter++;
    }

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to move uploaded file.");
    }

    // Delete old file if it exists
    if (!empty($existing_file) && $existing_file != '/Group3_Database_Project/DB/assets/images/menu-default.png') {
        $old_file = UPLOAD_BASE_DIR . ltrim(str_replace(UPLOAD_BASE_URL, '', $existing_file), '/');
        if (file_exists($old_file) && is_file($old_file)) {
            unlink($old_file);
        }
    }

    // Return path in the new format: menu/"category"/"filename"
    return 'menu/' . $folder_name . '/' . $filename;
}
