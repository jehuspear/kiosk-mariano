<?php
session_start();
include 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = '';
$menuItem = null;
$sizes = array();

// Get menu item details
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get menu item details
    $sql = "SELECT * FROM menuitem WHERE MenuItem_ID = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if(mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $menuItem = mysqli_fetch_assoc($result);
        
        // Get sizes
        $sizeSql = "SELECT * FROM menuitem_sizes WHERE MenuItem_ID = ? ORDER BY 
                    CASE MenuItemSize_SizeName 
                        WHEN 'Uno' THEN 1 
                        WHEN 'Dos' THEN 2 
                        WHEN 'Tres' THEN 3 
                        WHEN 'Quatro' THEN 4 
                        WHEN 'Sinco' THEN 5 
                    END";
        $sizeStmt = mysqli_stmt_init($conn);
        
        if(mysqli_stmt_prepare($sizeStmt, $sizeSql)) {
            mysqli_stmt_bind_param($sizeStmt, "i", $id);
            mysqli_stmt_execute($sizeStmt);
            $sizeResult = mysqli_stmt_get_result($sizeStmt);
            while($size = mysqli_fetch_assoc($sizeResult)) {
                $sizes[$size['MenuItemSize_SizeName']] = $size;
            }
        }
    }
}

if(isset($_POST['submit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $totalStocks = $_POST['totalStocks'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    try {
        // Update menu item
        $sql = "UPDATE menuitem SET MenuItem_Name=?, MenuItem_Description=?, MenuItem_Category=?, MenuItem_TotalStocks=? WHERE MenuItem_ID=?";
        $stmt = mysqli_stmt_init($conn);
        
        if(mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssii", $name, $description, $category, $totalStocks, $id);
            mysqli_stmt_execute($stmt);
        }
        
        // Handle new image if uploaded
        if(!empty($_FILES["image"]["name"])) {
            $targetDir = "Images/menu-items/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            
            // Allow certain file formats
            $allowTypes = array('jpg','png','jpeg');
            if(in_array(strtolower($fileType), $allowTypes)) {
                if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    // Delete old image if exists
                    if(file_exists($menuItem['MenuItem_Image'])) {
                        unlink($menuItem['MenuItem_Image']);
                    }
                    
                    // Update image path in database
                    $sql = "UPDATE menuitem SET MenuItem_Image=? WHERE MenuItem_ID=?";
                    $stmt = mysqli_stmt_init($conn);
                    
                    if(mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "si", $targetFilePath, $id);
                        mysqli_stmt_execute($stmt);
                    }
                }
            }
        }
        
        // Update or insert sizes
        $sizeNames = array('Uno', 'Dos', 'Tres', 'Quatro', 'Sinco');
        foreach($sizeNames as $size) {
            $sizeLower = strtolower($size);
            if (isset($_POST["price_" . $sizeLower], $_POST["temperature_type_" . $sizeLower], $_POST["stock_" . $sizeLower])) {
                $price = $_POST["price_" . $sizeLower];
                $temperatureType = $_POST["temperature_type_" . $sizeLower];
                $stock = $_POST["stock_" . $sizeLower];

                // Validate temperature type
                if (!in_array($temperatureType, ['Hot', 'Iced', 'Normal'])) {
                    throw new Exception('Invalid temperature type');
                }
            
                // Check if size exists
                if(isset($sizes[$size])) {
                    // Update existing size
                    $updateSql = "UPDATE menuitem_sizes SET 
                                MenuItemSize_Price=?, 
                                MenuItemSize_IsHot=?,
                                MenuItemSize_Stock=? 
                                WHERE MenuItemSize_ID=?";
                    $updateStmt = mysqli_stmt_init($conn);
                    
                    if(mysqli_stmt_prepare($updateStmt, $updateSql)) {
                        mysqli_stmt_bind_param($updateStmt, "dsii", 
                            $price, 
                            $temperatureType, 
                            $stock, 
                            $sizes[$size]['MenuItemSize_ID']
                        );
                        mysqli_stmt_execute($updateStmt);
                    }
                } else {
                    // Insert new size
                    $insertSql = "INSERT INTO menuitem_sizes 
                                (MenuItem_ID, MenuItemSize_SizeName, MenuItemSize_Price, MenuItemSize_IsHot, MenuItemSize_Stock) 
                                VALUES (?, ?, ?, ?, ?)";
                    $insertStmt = mysqli_stmt_init($conn);
                    
                    if(mysqli_stmt_prepare($insertStmt, $insertSql)) {
                        mysqli_stmt_bind_param($insertStmt, "isdsi", 
                            $id, 
                            $size, 
                            $price, 
                            $temperatureType, 
                            $stock
                        );
                        mysqli_stmt_execute($insertStmt);
                    }
                }
            }
        }
        
        mysqli_commit($conn);
        $message = '<div class="alert alert-success">Menu item updated successfully!</div>';
        echo "<script>
            setTimeout(function() {
                window.parent.closeModal();
            }, 1500);
        </script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = '<div class="alert alert-danger">Failed to update menu item: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="Css-admin/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="Css-admin/menu-forms.css">
    <link rel="stylesheet" href="Css-admin/menu-item-sizes.css">
</head>
<body>
    <div class="form-container">
        <h2 class="mb-4">Edit Menu Item</h2>
        
        <?php 
        echo $message;
        
        if($menuItem) { 
        ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $menuItem['MenuItem_ID']; ?>">
            
            <div class="form-group">
                <label for="name">Item Name:</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo htmlspecialchars($menuItem['MenuItem_Name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($menuItem['MenuItem_Description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="category">Category:</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php
                    $categories = array('Traditional Coffee', 'Coffee', 'Non-Coffee', 'Mocktail', 'Pastries', 'Snacks');
                    foreach($categories as $category) {
                        $selected = ($category == $menuItem['MenuItem_Category']) ? 'selected' : '';
                        echo "<option value='$category' $selected>$category</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="totalStocks">Total Stocks:</label>
                <input type="number" class="form-control" id="totalStocks" name="totalStocks" 
                       value="<?php echo $menuItem['MenuItem_TotalStocks']; ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label>Current Image:</label><br>
                <img src="<?php echo $menuItem['MenuItem_Image']; ?>" class="current-image" alt="Current Image">
            </div>
            
            <div class="form-group">
                <label for="image">Change Image (optional):</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" 
                       onchange="previewImage(this);">
                <img id="preview" class="preview-image">
            </div>
            
            <!-- Sizes and Prices -->
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Sizes and Prices</h4>
                    <button type="button" class="btn btn-primary btn-sm" onclick="showAddSizeForm()">
                        <i class="fas fa-plus"></i> Add Size
                    </button>
                </div>

                <!-- Add Size Form -->
                <div id="addSizeForm" class="menu-item-size-form">
                    <h5><i class="fas fa-plus-circle"></i> Add New Size</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="newSizeName" class="form-label">Cup Size</label>
                            <input type="text" id="newSizeName" class="form-control" 
                                   placeholder="Enter size name" maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label for="newSizePrice" class="form-label">Price (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" id="newSizePrice" class="form-control" placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="newTemperatureType" class="form-label">Temperature</label>
                            <select id="newTemperatureType" class="form-control temperature-type-select">
                                <option value="">Select Temperature</option>
                                <option value="Hot">Hot</option>
                                <option value="Iced">Iced</option>
                                <option value="Normal">Normal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="newSizeStock" class="form-label">Stock</label>
                            <input type="number" id="newSizeStock" class="form-control" placeholder="0" min="0">
                        </div>
                    </div>
                    <div class="menu-item-size-form-actions">
                        <button type="button" class="save-btn" onclick="createMenuItemSize(<?php echo $menuItem['MenuItem_ID']; ?>)">
                            Save Size
                        </button>
                        <button type="button" class="cancel-btn" onclick="hideAddSizeForm()">
                            Cancel
                        </button>
                    </div>
                </div>

                <table class="menu-item-sizes-table">
                    <thead>
                        <tr>
                            <th style="width: 20%">Size</th>
                            <th style="width: 25%">Price (₱)</th>
                            <th style="width: 25%">Temperature</th>
                            <th style="width: 15%">Stock</th>
                            <th style="width: 15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sizeLabels = array(
                            'Uno' => 'Uno (8oz)',
                            'Dos' => 'Dos (12oz)',
                            'Tres' => 'Tres (12oz)',
                            'Quatro' => 'Quatro (16oz)',
                            'Sinco' => 'Sinco (22oz)'
                        );
                        foreach($sizes as $size): 
                        ?>
                        <tr data-size-id="<?php echo $size['MenuItemSize_ID']; ?>">
                            <td>
                                <input type="text" class="form-control" 
                                       id="size_name_<?php echo $size['MenuItemSize_ID']; ?>" 
                                       value="<?php echo htmlspecialchars($size['MenuItemSize_SizeName']); ?>"
                                       maxlength="50">
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" 
                                           id="price_<?php echo $size['MenuItemSize_ID']; ?>" 
                                           value="<?php echo $size['MenuItemSize_Price']; ?>"
                                           step="0.01" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <select class="form-control temperature-type-select" 
                                            id="temperature_type_<?php echo $size['MenuItemSize_ID']; ?>">
                                        <option value="Hot" <?php echo $size['MenuItemSize_IsHot'] === 'Hot' ? 'selected' : ''; ?>>Hot</option>
                                        <option value="Iced" <?php echo $size['MenuItemSize_IsHot'] === 'Iced' ? 'selected' : ''; ?>>Iced</option>
                                        <option value="Normal" <?php echo $size['MenuItemSize_IsHot'] === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control" 
                                       id="stock_<?php echo $size['MenuItemSize_ID']; ?>" 
                                       value="<?php echo $size['MenuItemSize_Stock']; ?>"
                                       min="0" placeholder="0">
                            </td>
                            <td class="menu-item-size-actions">
                                <button type="button" class="update-btn update-size-btn" 
                                        data-size-id="<?php echo $size['MenuItemSize_ID']; ?>">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button type="button" class="delete-btn delete-size-btn"
                                        data-size-id="<?php echo $size['MenuItemSize_ID']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Item
                </button>
            </div>
        </form>
        
        <?php 
        } else {
            echo '<div class="alert alert-danger">Menu item not found!</div>';
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="Css-admin/bootstrap.bundle.min.js"></script>
    
    <!-- Menu Sizes JS -->
    <script src="Javascript-admin/menu-sizes.js"></script>
    
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
