<?php
session_start();
include 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = '';

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $totalStocks = 0; // Will be calculated from sizes
    
    // Handle file upload
    $targetDir = "Images/menu-item/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Validate input
    if(empty($name) || empty($description) || empty($category)) {
        $message = '<div class="alert alert-danger">All fields are required!</div>';
    } else {
        // Allow certain file formats
        $allowTypes = array('jpg','png','jpeg');
        if(in_array(strtolower($fileType), $allowTypes)) {
            // Upload file to server
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Start transaction
                mysqli_begin_transaction($conn);
                try {
                    // Insert into menuitem table
                    $sql = "INSERT INTO menuitem (MenuItem_Name, MenuItem_Image, MenuItem_Description, Category_ID, MenuItem_Category, MenuItem_TotalStocks, MenuItem_TotalSold) VALUES (?, ?, ?, ?, ?, ?, 0)";
                    $stmt = mysqli_stmt_init($conn);
                    
                    if(mysqli_stmt_prepare($stmt, $sql)) {
                        // Get category name for backward compatibility
                        $categoryName = '';
                        $categoryQuery = "SELECT Category_Name FROM category WHERE Category_ID = ?";
                        $categoryStmt = mysqli_stmt_init($conn);
                        if(mysqli_stmt_prepare($categoryStmt, $categoryQuery)) {
                            mysqli_stmt_bind_param($categoryStmt, "i", $category);
                            mysqli_stmt_execute($categoryStmt);
                            $categoryResult = mysqli_stmt_get_result($categoryStmt);
                            if($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                                $categoryName = $categoryRow['Category_Name'];
                            }
                            mysqli_stmt_close($categoryStmt);
                        }
                        
                        mysqli_stmt_bind_param($stmt, "sssisi", $name, $targetFilePath, $description, $category, $categoryName, $totalStocks);
                        mysqli_stmt_execute($stmt);
                        $menuItemId = mysqli_insert_id($conn);
                        
                        // Insert sizes
                        $totalStocks = 0;
                        foreach($_POST['sizes'] as $size) {
                            if (!empty($size['name']) && isset($size['price']) && isset($size['stock'])) {
                                $sizeName = $size['name'];
                                $price = floatval($size['price']);
                                $temperatureType = $size['temperature_type'];
                                $stock = intval($size['stock']);
                                
                                // Validate data
                                if (empty($sizeName) || strlen($sizeName) > 50) {
                                    throw new Exception('Invalid size name');
                                }
                                if ($price <= 0) {
                                    throw new Exception('Invalid price');
                                }
                                if ($stock < 0) {
                                    throw new Exception('Invalid stock quantity');
                                }
                                if (!in_array($temperatureType, ['Hot', 'Iced', 'Normal'])) {
                                    throw new Exception('Invalid temperature type');
                                }
                                
                                $sizeSql = "INSERT INTO menuitem_sizes (
                                    MenuItem_ID, 
                                    MenuItemSize_SizeName, 
                                    MenuItemSize_Price, 
                                    MenuItemSize_IsHot, 
                                    MenuItemSize_Stock,
                                    MenuItemSize_Sold
                                ) VALUES (?, ?, ?, ?, ?, 0)";
                                $sizeStmt = mysqli_stmt_init($conn);
                                
                                if(mysqli_stmt_prepare($sizeStmt, $sizeSql)) {
                                    mysqli_stmt_bind_param($sizeStmt, "isdsi", 
                                        $menuItemId, 
                                        $sizeName, 
                                        $price, 
                                        $temperatureType, 
                                        $stock
                                    );
                                    mysqli_stmt_execute($sizeStmt);
                                    $totalStocks += $stock;
                                }
                            }
                        }
                        
                        // Update total stocks
                        $updateStocksSql = "UPDATE menuitem SET MenuItem_TotalStocks = ? WHERE MenuItem_ID = ?";
                        $updateStocksStmt = mysqli_stmt_init($conn);
                        if(mysqli_stmt_prepare($updateStocksStmt, $updateStocksSql)) {
                            mysqli_stmt_bind_param($updateStocksStmt, "ii", $totalStocks, $menuItemId);
                            mysqli_stmt_execute($updateStocksStmt);
                        }
                        
                        mysqli_commit($conn);
                        $message = '<div class="alert alert-success">Menu item added successfully!</div>';
                        echo "<script>
                            setTimeout(function() {
                                window.parent.closeModal();
                            }, 1500);
                        </script>";
                    }
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $message = '<div class="alert alert-danger">Failed to add menu item: ' . $e->getMessage() . '</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">Failed to upload image!</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Only JPG, JPEG & PNG files are allowed!</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="Css-admin/add-menu-item.css">
</head>
<body>
    <div class="add-menu-form">
        <h2>Add New Menu Item</h2>
        
        <?php echo $message; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Item Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="category">Category:</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php
                    // Fetch categories from database
                    $categorySql = "SELECT Category_ID, Category_Name, Category_Description FROM category ORDER BY Category_ID ASC";
                    $categoryResult = mysqli_query($conn, $categorySql);
                    
                    while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                        echo '<option value="' . $categoryRow['Category_ID'] . '" title="' . htmlspecialchars($categoryRow['Category_Description']) . '">' . 
                             htmlspecialchars($categoryRow['Category_Name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required 
                       onchange="previewImage(this);">
                <div class="image-preview-container">
                    <img id="preview" class="image-preview">
                </div>
            </div>
            
            <!-- Sizes and Prices -->
            <div class="size-management">
                <div class="size-management-header">
                    <h4>Sizes and Prices</h4>
                    <button type="button" class="add-size-btn" onclick="addSizeRow()">
                        <i class="fas fa-plus"></i> Add Size
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="size-table" id="sizesTable">
                        <thead>
                            <tr>
                                <th style="width: 25%">Size Name</th>
                                <th style="width: 25%">Price (₱)</th>
                                <th style="width: 25%">Temperature</th>
                                <th style="width: 15%">Stock</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sizesTableBody">
                            <!-- Size rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="submit-btn">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
        </form>
    </div>

    <!-- Size Row Template (hidden) -->
    <template id="sizeRowTemplate">
        <tr class="size-row">
            <td>
                <input type="text" class="form-control" name="sizes[{index}][name]" 
                       placeholder="Enter size name" maxlength="50" required>
            </td>
            <td>
                <div class="price-input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number" class="form-control" name="sizes[{index}][price]" 
                           step="0.01" min="0" required>
                </div>
            </td>
            <td>
                <select class="form-control temperature-type-select" name="sizes[{index}][temperature_type]" required>
                    <option value="">Select Temperature</option>
                    <option value="Hot">🔥 Hot</option>
                    <option value="Normal">🌡️ Normal</option>
                    <option value="Iced">❄️ Iced</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="sizes[{index}][stock]" 
                       min="0" required>
            </td>
            <td>
                <button type="button" class="remove-size-btn" onclick="removeSizeRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Bootstrap JS -->
    <script src="Css-admin/bootstrap.bundle.min.js"></script>
    
    <!-- Menu Sizes JS -->
    <script src="Javascript-admin/add-menu-sizes.js"></script>
    
    <script>
    function previewImage(input) {
        var preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
