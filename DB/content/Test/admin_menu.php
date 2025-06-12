<?php 
require_once 'db_connect.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create new item
        if (isset($_POST['create'])) {
            $category = $_POST['category'];
			$image_url = !empty($_FILES['image']['name']) 
				? handleFileUpload($_FILES['image'], $category, '') 
				: 'menu/images/menu-default.png';  // Changed this line

            $stmt = $pdo->prepare("INSERT INTO menu_items (name, image_url, price, available, category) 
                                 VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $image_url,
                $_POST['price'],
                isset($_POST['available']) ? 1 : 0,
                $category
            ]);
            $_SESSION['message'] = "Item added successfully!";
        }

        // Update item
        if (isset($_POST['update'])) {
            // First get the existing item data
            $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE item_id = ?");
            $stmt->execute([$_POST['item_id']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing) {
                throw new Exception("Item not found in database.");
            }
            
            $category = $_POST['category'];
            
            // Handle file upload if new image was provided
            if (!empty($_FILES['image']['name'])) {
                $image_url = handleFileUpload($_FILES['image'], $category, $existing['image_url']);
            } 
            // Handle category change without new image
			elseif ($category !== $existing['category']) {
				$old_path = UPLOAD_BASE_DIR . ltrim($existing['image_url'], 'menu/');
				
				// Only proceed if the old file exists and isn't the default image
				if (file_exists($old_path) && $existing['image_url'] != 'menu/images/menu-default.png') {
					// Get the filename from the old path
					$filename = basename($existing['image_url']);
					
					// Map category to folder name
					$category_folders = [
						'Traditional Beverages' => 'traditionalBeverages',
						'Snacks & Appetizers' => 'snacksNappetizers',
						'Rice & Noodles' => 'riceNnoodles',
						'Proteins & Sides' => 'proteinsNsides',
						'Fresh & Cold' => 'freshNcold',
						'Desserts' => 'desserts'
					];
					$folder_name = $category_folders[$category] ?? strtolower(str_replace(' ', '', $category));
					$new_dir = UPLOAD_BASE_DIR . $folder_name . '/';
					
					// Create new directory if it doesn't exist
					if (!file_exists($new_dir)) {
						mkdir($new_dir, 0755, true);
					}
					
					// New full path
					$new_path = $new_dir . $filename;
					
					// Move the file
					if (rename($old_path, $new_path)) {
						$image_url = 'menu/' . $folder_name . '/' . $filename;
					} else {
						// If move fails, keep the old path
						$image_url = $existing['image_url'];
						$_SESSION['error'] = "Could not move file to new category directory, but other changes were saved.";
					}
				} else {
					// File doesn't exist or is default image - keep current URL
					$image_url = $existing['image_url'];
				}
			}

            // Get values from form or use existing values if not provided
            $name = !empty($_POST['name']) ? $_POST['name'] : $existing['name'];
            $price = !empty($_POST['price']) ? $_POST['price'] : $existing['price'];
            $available = isset($_POST['available']) ? 1 : 0;
            $popularity_score = $existing['popularity_score']; // Keep existing score unless you want to change it

            // Update the item
            $stmt = $pdo->prepare("UPDATE menu_items SET 
                                  name = ?, 
                                  image_url = ?, 
                                  price = ?, 
                                  available = ?, 
                                  category = ?,
                                  popularity_score = ?
                                  WHERE item_id = ?");
            
            $stmt->execute([
                $name,
                $image_url,
                $price,
                $available,
                $category,
                $popularity_score,
                $_POST['item_id']
            ]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "Item updated successfully!";
            } else {
                $_SESSION['error'] = "No changes were made to the item.";
            }
        }
        
        // Delete item
        if (isset($_POST['delete'])) {
            // First get image URL to delete the file
            $stmt = $pdo->prepare("SELECT image_url FROM menu_items WHERE item_id = ?");
            $stmt->execute([$_POST['item_id']]);
            $image_url = $stmt->fetchColumn();

            // Delete the record
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE item_id = ?");
            $stmt->execute([$_POST['item_id']]);

			// Delete the image file if it's not the default
			if ($image_url && strpos($image_url, 'menu-default.png') === false) {
				$file_path = UPLOAD_BASE_DIR . ltrim($image_url, 'menu/');
				if (file_exists($file_path)) {
					unlink($file_path);
				}
			}

            $_SESSION['message'] = "Item deleted successfully!";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
    }
    
    header("Location: admin_menu.php");
    exit;
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
            <form method="POST" action="admin_menu.php" enctype="multipart/form-data" id="addForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Item Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Item Image</label>
                        <div class="file-upload btn btn-outline-secondary w-100">
                            <span id="file-upload-label">Choose file...</span>
                            <input type="file" class="file-upload-input" id="image" name="image" accept="image/*">
                        </div>
                        <div class="mt-2">
                            <img id="image-preview" src="/Group3_Database_Project/DB/assets/images/menu-default.png" 
                                 alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="price" class="form-label">Price (RM) *</label>
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
                        <label for="category" class="form-label">Category *</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
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
						// Change this line to handle the new path format
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
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_name" class="form-label">Item Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Item Image</label>
                                <div class="file-upload btn btn-outline-secondary w-100">
                                    <span id="edit-file-upload-label">Choose file...</span>
                                    <input type="file" class="file-upload-input" id="edit_image" name="image" accept="image/*">
                                </div>
                                <div class="mt-2">
                                    <img id="edit_image_preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                                    <input type="hidden" id="edit_current_image" name="current_image">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="edit_price" class="form-label">Price (RM) *</label>
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
                                <label for="edit_category" class="form-label">Category *</label>
                                <select class="form-select" id="edit_category" name="category" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat ?>"><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
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
        // Function to load data into edit form
        function loadEditForm(itemId, currentImagePath) {
            fetch('get_item.php?id=' + itemId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(item => {
                    if (!item) {
                        throw new Error('Item data is empty');
                    }
                    
                    // Populate form fields
                    document.getElementById('edit_item_id').value = item.item_id;
                    document.getElementById('edit_name').value = item.name;
                    document.getElementById('edit_price').value = item.price;
                    document.getElementById('edit_available').checked = item.available == 1;
                    document.getElementById('edit_category').value = item.category;
                    document.getElementById('edit_current_image').value = item.image_url;
                    
                    // Show current image preview
                    const preview = document.getElementById('edit_image_preview');
                    if (currentImagePath) {
                        preview.src = currentImagePath;
                        preview.style.display = 'block';
                        document.getElementById('edit-file-upload-label').textContent = 'Change image...';
                    } else {
                        preview.src = '/Group3_Database_Project/DB/assets/images/menu-default.png';
                        preview.style.display = 'block';
                        document.getElementById('edit-file-upload-label').textContent = 'Choose file...';
                    }
                })
                .catch(error => {
                    console.error('Error loading edit form:', error);
                    alert('Error loading item data. Please try again.');
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

        // Form validation
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const name = document.getElementById('edit_name').value.trim();
            const price = document.getElementById('edit_price').value.trim();
            const category = document.getElementById('edit_category').value;
            
            if (!name || !price || !category) {
                e.preventDefault();
                alert('Please fill in all required fields (marked with *)');
                return false;
            }
            
            if (isNaN(price) || parseFloat(price) <= 0) {
                e.preventDefault();
                alert('Please enter a valid price (greater than 0)');
                return false;
            }
            
            return true;
        });

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