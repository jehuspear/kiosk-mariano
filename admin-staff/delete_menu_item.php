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
    
    // First get the image path to delete the file
    $sql = "SELECT MenuItem_Image FROM menuitem WHERE MenuItem_ID = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if(mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            $imagePath = $row['MenuItem_Image'];
            
            // Delete the menu item from database
            $sql = "DELETE FROM menuitem WHERE MenuItem_ID = ?";
            $stmt = mysqli_stmt_init($conn);
            
            if(mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                
                if(mysqli_stmt_execute($stmt)) {
                    // Delete the image file if it exists
                    if(file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    
                    // Redirect back to menu screen
                    header("Location: menuscreen.php");
                    exit();
                }
            }
        }
    }
}

// If something went wrong, redirect back with error
header("Location: menuscreen.php?error=delete_failed");
exit();
?>
