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
                    CASE MenuItemSize_Size 
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
                $sizes[$size['MenuItemSize_Size']] = $size;
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
        
        // Update sizes
        $sizeSql = "UPDATE menuitem_sizes SET MenuItemSize_Price=?, MenuItemSize_IsHot=?, MenuItemSize_Stock=? 
                    WHERE MenuItem_ID=? AND MenuItemSize_Size=?";
        $sizeStmt = mysqli_stmt_init($conn);
        
        if(mysqli_stmt_prepare($sizeStmt, $sizeSql)) {
            $sizeNames = array('Uno', 'Dos', 'Tres', 'Quatro', 'Sinco');
            foreach($sizeNames as $size) {
                $sizeLower = strtolower($size);
                $price = $_POST["price_" . $sizeLower];
                $isHot = isset($_POST["is_hot_" . $sizeLower]) ? 1 : 0;
                $stock = $_POST["stock_" . $sizeLower];
                
                mysqli_stmt_bind_param($sizeStmt, "diisi", $price, $isHot, $stock, $id, $size);
                mysqli_stmt_execute($sizeStmt);
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
                        $sizeLabels = array(
                            'Uno' => 'Uno (8oz)',
                            'Dos' => 'Dos (12oz)',
                            'Tres' => 'Tres (12oz)',
                            'Quatro' => 'Quatro (16oz)',
                            'Sinco' => 'Sinco (22oz)'
                        );
                        foreach($sizeLabels as $size => $label): 
                            $sizeData = isset($sizes[$size]) ? $sizes[$size] : null;
                            $sizeLower = strtolower($size);
                        ?>
                        <tr>
                            <td class="size-label"><?php echo $label; ?></td>
                            <td>
                                <input type="number" class="form-control" 
                                       name="price_<?php echo $sizeLower; ?>" 
                                       value="<?php echo $sizeData ? $sizeData['MenuItemSize_Price'] : ''; ?>"
                                       step="0.01" min="0" required>
                            </td>
                            <td>
                                <div class="hot-cold-toggle">
                                    <label class="hot-label">
                                        <input type="checkbox" name="is_hot_<?php echo $sizeLower; ?>"
                                               <?php echo ($sizeData && $sizeData['MenuItemSize_IsHot']) ? 'checked' : ''; ?>>
                                        <i class="fas fa-mug-hot"></i> HOT
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control" 
                                       name="stock_<?php echo $sizeLower; ?>" 
                                       value="<?php echo $sizeData ? $sizeData['MenuItemSize_Stock'] : ''; ?>"
                                       min="0" required>
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
