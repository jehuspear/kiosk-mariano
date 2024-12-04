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
    $totalStocks = $_POST['totalStocks'];
    
    // Handle file upload
    $targetDir = "Images/menu-items/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Validate input
    if(empty($name) || empty($description) || empty($category) || empty($totalStocks)) {
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
                    $sql = "INSERT INTO menuitem (MenuItem_Name, MenuItem_Image, MenuItem_Description, MenuItem_Category, MenuItem_TotalStocks, MenuItem_TotalSold) VALUES (?, ?, ?, ?, ?, 0)";
                    $stmt = mysqli_stmt_init($conn);
                    
                    if(mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ssssi", $name, $targetFilePath, $description, $category, $totalStocks);
                        mysqli_stmt_execute($stmt);
                        $menuItemId = mysqli_insert_id($conn);
                        
                        // Insert sizes
                        $sizes = array('Uno', 'Dos', 'Tres', 'Quatro', 'Sinco');
                        $sizeSql = "INSERT INTO menuitem_sizes (MenuItem_ID, MenuItemSize_Size, MenuItemSize_Price, MenuItemSize_IsHot, MenuItemSize_Stock) VALUES (?, ?, ?, ?, ?)";
                        $sizeStmt = mysqli_stmt_init($conn);
                        
                        if(mysqli_stmt_prepare($sizeStmt, $sizeSql)) {
                            foreach($sizes as $size) {
                                $sizeLower = strtolower($size);
                                $price = $_POST["price_" . $sizeLower];
                                $isHot = isset($_POST["is_hot_" . $sizeLower]) ? 1 : 0;
                                $stock = $_POST["stock_" . $sizeLower];
                                
                                mysqli_stmt_bind_param($sizeStmt, "isdii", $menuItemId, $size, $price, $isHot, $stock);
                                mysqli_stmt_execute($sizeStmt);
                            }
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
    <link rel="stylesheet" href="Css-admin/menu-forms.css">
</head>
<body>
    <div class="form-container">
        <h2 class="mb-4">Add New Menu Item</h2>
        
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
                    <option value="Traditional Coffee">Traditional Coffee</option>
                    <option value="Coffee">Coffee</option>
                    <option value="Non-Coffee">Non-Coffee</option>
                    <option value="Mocktail">Mocktail</option>
                    <option value="Pastries">Pastries</option>
                    <option value="Snacks">Snacks</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="totalStocks">Total Stocks:</label>
                <input type="number" class="form-control" id="totalStocks" name="totalStocks" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required 
                       onchange="previewImage(this);">
                <img id="preview" class="preview-image">
            </div>
            
            <!-- Sizes and Prices -->
            <div class="form-group">
                <h4>Sizes and Prices</h4>
                <table class="sizes-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Price (₱)</th>
                            <th>Type</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sizes = array(
                            'uno' => 'Uno (8oz)',
                            'dos' => 'Dos (12oz)',
                            'tres' => 'Tres (12oz)',
                            'quatro' => 'Quatro (16oz)',
                            'sinco' => 'Sinco (22oz)'
                        );
                        foreach($sizes as $key => $label): ?>
                        <tr>
                            <td class="size-label"><?php echo $label; ?></td>
                            <td>
                                <input type="number" class="form-control" 
                                       name="price_<?php echo $key; ?>" 
                                       step="0.01" min="0" >
                            </td>
                            <td>
                                <div class="hot-cold-toggle">
                                    <label class="hot-label">
                                        <input type="checkbox" name="is_hot_<?php echo $key; ?>">
                                        <i class="fas fa-mug-hot"></i> HOT
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control" 
                                       name="stock_<?php echo $key; ?>" 
                                       min="0" >
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="Css-admin/bootstrap.bundle.min.js"></script>
    
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
