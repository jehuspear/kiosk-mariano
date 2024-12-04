<?php
session_start();
include 'database_admin.php';

// Check if user is not logged in
if(!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Get the image path before deleting
        $sql = "SELECT MenuItem_Image FROM menuitem WHERE MenuItem_ID = ?";
        $stmt = mysqli_stmt_init($conn);
        
        if(mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if($row = mysqli_fetch_assoc($result)) {
                $imagePath = $row['MenuItem_Image'];
                
                // Delete sizes first (foreign key constraint)
                $sizeSql = "DELETE FROM menuitem_sizes WHERE MenuItem_ID = ?";
                $sizeStmt = mysqli_stmt_init($conn);
                
                if(mysqli_stmt_prepare($sizeStmt, $sizeSql)) {
                    mysqli_stmt_bind_param($sizeStmt, "i", $id);
                    mysqli_stmt_execute($sizeStmt);
                }
                
                // Then delete the menu item
                $menuSql = "DELETE FROM menuitem WHERE MenuItem_ID = ?";
                $menuStmt = mysqli_stmt_init($conn);
                
                if(mysqli_stmt_prepare($menuStmt, $menuSql)) {
                    mysqli_stmt_bind_param($menuStmt, "i", $id);
                    mysqli_stmt_execute($menuStmt);
                    
                    // Delete the image file if it exists
                    if(file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    
                    mysqli_commit($conn);
                    
                    // Redirect back to menu screen
                    header("Location: menuscreen.php");
                    exit();
                }
            }
        }
        
        // If we get here, something went wrong
        throw new Exception("Failed to delete menu item");
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        
        // Set error message in session and redirect
        $_SESSION['error_message'] = "Error deleting menu item: " . $e->getMessage();
        header("Location: menuscreen.php");
        exit();
    }
} else {
    // No ID provided, redirect back
    header("Location: menuscreen.php");
    exit();
}
?>
