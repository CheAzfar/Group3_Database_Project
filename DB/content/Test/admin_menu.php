<?php
require_once 'db_connect.php';
session_start();
include($_SERVER['DOCUMENT_ROOT']."/Group3_Database_Project/DB/content/pages/connection.php");

// Function to handle file upload with custom naming and directory structure
function handleCustomFileUpload($file, $category, $itemName, $oldImageUrl = '') {
    // Category to folder mapping
    $category_folders = [
        'Traditional Beverages' => 'traditionalBeverages',
        'Snacks & Appetizers' => 'snacksNappetizers',
        'Rice & Noodles' => 'riceNnoodles',
        'Proteins & Sides' => 'proteinsNsides',
        'Fresh & Cold' => 'freshNcold',
        'Desserts' => 'desserts'
    ];
    
    $folder_name = $category_folders[$category] ?? strtolower(str_replace(' ', '', $category));
    
    // Base directory for uploads - Now includes /menu/ subdirectory
    $base_dir = $_SERVER['DOCUMENT_ROOT'] . '/Group3_Database_Project/DB/assets/menu/';
    $upload_dir = $base_dir . $folder_name . '/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Clean the item name for filename (remove special characters, spaces, etc.)
    $clean_name = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($itemName));
    $filename = $clean_name . '.png';
    $file_path = $upload_dir . $filename;
    
    // Delete old image if exists and different from new path
    if ($oldImageUrl && $oldImageUrl !== 'menu/' . $folder_name . '/' . $filename) {
        $old_file_path = $_SERVER['DOCUMENT_ROOT'] . '/Group3_Database_Project/DB/assets/' . $oldImageUrl;
        if (file_exists($old_file_path) && strpos($oldImageUrl, 'menu-default.png') === false) {
            unlink($old_file_path);
        }
    }
    
    // Handle different image types and convert to PNG
    $temp_file = $file['tmp_name'];
    $file_type = mime_content_type($temp_file);
    
    switch ($file_type) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($temp_file);
            break;
        case 'image/png':
            $source = imagecreatefrompng($temp_file);
            break;
        case 'image/gif':
            $source = imagecreatefromgif($temp_file);
            break;
        case 'image/webp':
            $source = imagecreatefromwebp($temp_file);
            break;
        default:
            throw new Exception("Unsupported image type. Please upload JPEG, PNG, GIF, or WebP images.");
    }
    
    if (!$source) {
        throw new Exception("Failed to process the uploaded image.");
    }
    
    // Save as PNG
    if (imagepng($source, $file_path)) {
        imagedestroy($source);
        return 'menu/' . $folder_name . '/' . $filename;
    } else {
        imagedestroy($source);
        throw new Exception("Failed to save the image file.");
    }
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle DELETE operation
    if (isset($_POST['delete']) && isset($_POST['item_id'])) {
        $item_id = $_POST['item_id'];
        
        try {
            // First get the item to delete its image file
            $stmt = $pdo->prepare("SELECT image_url FROM menu_items WHERE item_id = ?");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($item && !empty($item['image_url'])) {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/Group3_Database_Project/DB/assets/' . $item['image_url'];
                if (file_exists($image_path) && strpos($item['image_url'], 'menu-default.png') === false) {
                    unlink($image_path);
                }
            }
            
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE item_id = ?");
            if ($stmt->execute([$item_id])) {
                $_SESSION['message'] = "Menu item deleted successfully.";
            } else {
                $_SESSION['error'] = "Error deleting menu item.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
        }
        
        // Redirect to prevent form resubmission
        header("Location: admin_menu.php");
        exit();
    }
    
    // Handle CREATE and UPDATE operations
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $available = isset($_POST['available']) ? 1 : 0;
    $popularity_score = $_POST['popularity_score'] ?? 0;
    $image_url = null;

    // Image upload handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        try {
            $old_image_url = $_POST['current_image'] ?? '';
            $image_url = handleCustomFileUpload($_FILES['image'], $category, $name, $old_image_url);
        } catch (Exception $e) {
            $_SESSION['error'] = "Image upload error: " . $e->getMessage();
            header("Location: admin_menu.php");
            exit();
        }
    }

    // Handle UPDATE operation
    if (isset($_POST['update']) && isset($_POST['item_id'])) {
        $item_id = $_POST['item_id'];

        try {
            if ($image_url !== null) {
                // Update including image
                $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, category = ?, available = ?, popularity_score = ?, image_url = ? WHERE item_id = ?");
                $stmt->execute([$name, $price, $category, $available, $popularity_score, $image_url, $item_id]);
            } else {
                // Update without changing image
                $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, category = ?, available = ?, popularity_score = ? WHERE item_id = ?");
                $stmt->execute([$name, $price, $category, $available, $popularity_score, $item_id]);
            }

            $_SESSION['message'] = "Menu item updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating menu item: " . $e->getMessage();
        }

    // Handle CREATE operation
    } elseif (isset($_POST['create'])) {
        try {
            if ($image_url === null) {
                $image_url = ""; // Store empty string if no image
            }

            $stmt = $pdo->prepare("INSERT INTO menu_items (name, price, category, available, popularity_score, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $category, $available, $popularity_score, $image_url]);

            $_SESSION['message'] = "New menu item added successfully.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error adding menu item: " . $e->getMessage();
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: admin_menu.php");
    exit();
}

// Sorting parameters
$sort = $_GET['sort'] ?? 'item_id';
$order = $_GET['order'] ?? 'asc';
$valid_sorts = ['item_id', 'name', 'price', 'available', 'category', 'popularity_score'];
$valid_orders = ['asc', 'desc'];

// Validate sort and order
if (!in_array($sort, $valid_sorts)) $sort = 'item_id';
if (!in_array($order, $valid_orders)) $order = 'asc';

// Fetch all menu items with sorting
try {
    $stmt = $pdo->prepare("SELECT * FROM menu_items ORDER BY $sort $order");
    $stmt->execute();
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching menu items: " . $e->getMessage());
}

// Categories for dropdown
$categories = [
    'Rice & Noodles',
    'Proteins & Sides',
    'Snacks & Appetizers',
    'Traditional Beverages',
    'Fresh & Cold',
    'Desserts'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menu Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 20px; }
        .container { background-color: white; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); padding: 25px; }
        .table-responsive { margin-top: 20px; }
        .img-thumbnail { max-width: 80px; max-height: 80px; }
        .form-section { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .badge-available { background-color: #28a745; }
        .badge-unavailable { background-color: #dc3545; }
        .action-buttons .btn { margin-right: 5px; margin-bottom: 5px; }
        .sortable { cursor: pointer; position: relative; }
        .sortable:hover { background-color: #f1f1f1; }
        .sortable::after {
            content: '';
            display: inline-block;
            margin-left: 5px;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
        }
        .sortable.asc::after {
            border-bottom: 5px solid #000;
        }
        .sortable.desc::after {
            border-top: 5px solid #000;
        }
        .file-upload { position: relative; overflow: hidden; display: inline-block; }
        .file-upload-input { position: absolute; left: 0; top: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4 text-center">Menu Items Management</h1>
        
        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Add New Item Form -->
        <div class="form-section">
            <h2 class="mb-4"><i class="fas fa-plus-circle"></i> Add New Menu Item</h2>
            <form method="POST" action="admin_menu.php" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="form-text">The image will be saved as: [clean_name].png</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Item Image</label>
                        <div class="file-upload btn btn-outline-secondary w-100">
                            <span id="file-upload-label">Choose file...</span>
                            <input type="file" class="file-upload-input" id="image" name="image" accept="image/*">
                        </div>
                        <div class="form-text">Accepts JPEG, PNG, GIF, WebP. Will be converted to PNG.</div>
                        <div class="mt-2">
                            <img id="image-preview" src="/Group3_Database_Project/DB/assets/images/menu-default.png" 
                                 alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="price" class="form-label">Price (RM)</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="available" class="form-label">Availability</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="available" name="available" value="1" checked>
                            <label class="form-check-label" for="available">Available</label>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text" id="category-path">Select category to see folder path</div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="popularity_score" class="form-label">Popularity Score</label>
                        <input type="number" class="form-control" id="popularity_score" name="popularity_score" min="0" value="0" required>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" name="create" class="btn btn-primary">
                            <i class="fas fa-save"></i> Add Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Menu Items Table -->
        <h2 class="mb-4"><i class="fas fa-utensils"></i> Current Menu Items</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th class="sortable <?= $sort === 'item_id' ? $order : '' ?>" 
                            onclick="sortTable('item_id')">ID</th>
                        <th class="sortable <?= $sort === 'name' ? $order : '' ?>" 
                            onclick="sortTable('name')">Name</th>
                        <th>Image</th>
                        <th class="sortable <?= $sort === 'price' ? $order : '' ?>" 
                            onclick="sortTable('price')">Price</th>
                        <th class="sortable <?= $sort === 'available' ? $order : '' ?>" 
                            onclick="sortTable('available')">Available</th>
                        <th class="sortable <?= $sort === 'category' ? $order : '' ?>" 
                            onclick="sortTable('category')">Category</th>
                        <th class="sortable <?= $sort === 'popularity_score' ? $order : '' ?>" 
                            onclick="sortTable('popularity_score')">Popularity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menu_items as $item): 
                        $imagePath = !empty($item['image_url'])
                            ? '/Group3_Database_Project/DB/assets/' . $item['image_url']
                            : '/Group3_Database_Project/DB/assets/images/menu-default.png';
                    ?>
                    <tr>
                        <td><?= $item['item_id'] ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>
                            <img src="<?= $imagePath ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>" 
                                 class="img-thumbnail"
                                 onerror="this.src='/Group3_Database_Project/DB/assets/images/menu-default.png'">
                        </td>
                        <td>RM <?= number_format($item['price'], 2) ?></td>
                        <td>
                            <span class="badge rounded-pill <?= $item['available'] ? 'badge-available' : 'badge-unavailable' ?>">
                                <?= $item['available'] ? 'Yes' : 'No' ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= $item['popularity_score'] ?></td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" 
                                    onclick="loadEditForm(<?= $item['item_id'] ?>, '<?= $imagePath ?>')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" action="admin_menu.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="admin_menu.php" id="editForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="item_id" id="edit_item_id">
                        <input type="hidden" name="current_image" id="edit_current_image">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_name" class="form-label">Item Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="form-text">Changes will update the filename</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Item Image</label>
                                <div class="file-upload btn btn-outline-secondary w-100">
                                    <span id="edit-file-upload-label">Choose file...</span>
                                    <input type="file" class="file-upload-input" id="edit_image" name="image" accept="image/*">
                                </div>
                                <div class="form-text">Leave empty to keep current image</div>
                                <div class="mt-2">
                                    <img id="edit_image_preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="edit_price" class="form-label">Price (RM)</label>
                                <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="edit_available" class="form-label">Availability</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="edit_available" name="available" value="1">
                                    <label class="form-check-label" for="edit_available">Available</label>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_category" name="category" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>"><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text" id="edit-category-path">Category changes will move the file</div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="edit_popularity_score" class="form-label">Popularity Score</label>
                                <input type="number" class="form-control" id="edit_popularity_score" name="popularity_score" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Category to folder mapping for display
        const categoryFolders = {
            'Traditional Beverages': 'traditionalBeverages',
            'Snacks & Appetizers': 'snacksNappetizers',
            'Rice & Noodles': 'riceNnoodles',
            'Proteins & Sides': 'proteinsNsides',
            'Fresh & Cold': 'freshNcold',
            'Desserts': 'desserts'
        };
        
        // Function to clean name for filename preview
        function cleanName(name) {
            return name.replace(/[^a-zA-Z0-9]/g, '').toLowerCase();
        }
        
        // Update category path display
        function updateCategoryPath(selectElement, pathElement) {
            const category = selectElement.value;
            const nameInput = selectElement.closest('form').querySelector('input[name="name"]');
            const name = nameInput ? nameInput.value : '';
            
            if (category && categoryFolders[category]) {
                const cleanedName = cleanName(name);
                const path = cleanedName ? 
                    `assets/menu/${categoryFolders[category]}/${cleanedName}.png` : 
                    `assets/menu/${categoryFolders[category]}/[item_name].png`;
                pathElement.textContent = `Path: ${path}`;
            } else {
                pathElement.textContent = 'Select category to see folder path';
            }
        }
        
        // Add event listeners for add form
        document.getElementById('category').addEventListener('change', function() {
            updateCategoryPath(this, document.getElementById('category-path'));
        });
        
        document.getElementById('name').addEventListener('input', function() {
            updateCategoryPath(document.getElementById('category'), document.getElementById('category-path'));
        });
        
        // Add event listeners for edit form
        document.getElementById('edit_category').addEventListener('change', function() {
            updateCategoryPath(this, document.getElementById('edit-category-path'));
        });
        
        document.getElementById('edit_name').addEventListener('input', function() {
            updateCategoryPath(document.getElementById('edit_category'), document.getElementById('edit-category-path'));
        });

        // Function to load data into edit form
        function loadEditForm(itemId, currentImagePath) {
            fetch('get_item.php?id=' + itemId)
                .then(response => response.json())
                .then(item => {
                    // Populate form fields
                    document.getElementById('edit_item_id').value = item.item_id;
                    document.getElementById('edit_name').value = item.name;
                    document.getElementById('edit_price').value = item.price;
                    document.getElementById('edit_available').checked = item.available == 1;
                    document.getElementById('edit_category').value = item.category;
                    document.getElementById('edit_popularity_score').value = item.popularity_score;
                    document.getElementById('edit_current_image').value = item.image_url;
                    
                    // Update category path
                    updateCategoryPath(document.getElementById('edit_category'), document.getElementById('edit-category-path'));
                    
                    // Show current image preview
                    const preview = document.getElementById('edit_image_preview');
                    if (currentImagePath) {
                        preview.src = currentImagePath;
                        preview.style.display = 'block';
                        document.getElementById('edit-file-upload-label').textContent = 'Change image...';
                    } else {
                        preview.style.display = 'none';
                        document.getElementById('edit-file-upload-label').textContent = 'Choose file...';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Fallback: populate from the current row data
                    alert('Could not load item details. Please try again.');
                });
        }

        // File upload handling for add form
        document.getElementById('image').addEventListener('change', function(e) {
            const label = document.getElementById('file-upload-label');
            const preview = document.getElementById('image-preview');
            
            if (this.files && this.files[0]) {
                label.textContent = this.files[0].name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                label.textContent = 'Choose file...';
                preview.src = '/Group3_Database_Project/DB/assets/images/menu-default.png';
            }
        });

        // File upload handling for edit form
        document.getElementById('edit_image').addEventListener('change', function(e) {
            const label = document.getElementById('edit-file-upload-label');
            const preview = document.getElementById('edit_image_preview');
            
            if (this.files && this.files[0]) {
                label.textContent = this.files[0].name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                label.textContent = 'Change image...';
                // Keep the current preview image
            }
        });

        // Sorting functionality
        function sortTable(column) {
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentOrder = url.searchParams.get('order');
            
            let newOrder = 'asc';
            if (currentSort === column) {
                newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            }
            
            url.searchParams.set('sort', column);
            url.searchParams.set('order', newOrder);
            window.location.href = url.toString();
        }

        // Initialize sort indicators
        document.addEventListener('DOMContentLoaded', function() {
            const sortableHeaders = document.querySelectorAll('.sortable');
            sortableHeaders.forEach(header => {
                if (header.classList.contains('asc') || header.classList.contains('desc')) {
                    header.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>