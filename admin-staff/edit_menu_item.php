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

// Get menu item details
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM menuitem WHERE MenuItem_ID = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if(mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $menuItem = mysqli_fetch_assoc($result);
    }
}

if(isset($_POST['submit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $totalStocks = $_POST['totalStocks'];
    
    // Check if new image is uploaded
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
            // Upload file to server
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $sql = "UPDATE menuitem SET MenuItem_Name=?, MenuItem_Image=?, MenuItem_Description=?, MenuItem_Category=?, MenuItem_TotalStocks=? WHERE MenuItem_ID=?";
                $stmt = mysqli_stmt_init($conn);
                
                if(mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssssii", $name, $targetFilePath, $description, $category, $totalStocks, $id);
                    
                    if(mysqli_stmt_execute($stmt)) {
                        $message = '<div class="alert alert-success">Menu item updated successfully!</div>';
                        echo "<script>
                            setTimeout(function() {
                                window.parent.closeModal();
                            }, 1500);
                        </script>";
                    } else {
                        $message = '<div class="alert alert-danger">Failed to update menu item!</div>';
                    }
                }
            } else {
                $message = '<div class="alert alert-danger">Failed to upload image!</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Only JPG, JPEG & PNG files are allowed!</div>';
        }
    } else {
        // Update without changing image
        $sql = "UPDATE menuitem SET MenuItem_Name=?, MenuItem_Description=?, MenuItem_Category=?, MenuItem_TotalStocks=? WHERE MenuItem_ID=?";
        $stmt = mysqli_stmt_init($conn);
        
        if(mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssii", $name, $description, $category, $totalStocks, $id);
            
            if(mysqli_stmt_execute($stmt)) {
                $message = '<div class="alert alert-success">Menu item updated successfully!</div>';
                echo "<script>
                    setTimeout(function() {
                        window.parent.closeModal();
                    }, 1500);
                </script>";
            } else {
                $message = '<div class="alert alert-danger">Failed to update menu item!</div>';
            }
        }
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
    
    <style>
        body {
            background-color: #212121;
            color: white;
            padding: 20px;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            background-color: #333;
            border: 1px solid #444;
            color: white;
        }
        
        .form-control:focus {
            background-color: #444;
            border-color: #666;
            color: white;
            box-shadow: none;
        }
        
        .current-image {
            max-width: 200px;
            margin: 10px 0;
            border-radius: 5px;
        }
        
        .preview-image {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 5px;
            display: none;
        }
        
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        
        label {
            color: #ddd;
            margin-bottom: 5px;
        }
        
        .alert {
            margin-bottom: 20px;
        }
    </style>
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
