<?php
session_start();

// Check if 'cart' exists in the session
if (!isset($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'No items in the cart']);
    exit;
}

// Ensure index is set and valid
if (isset($_POST['index']) && isset($_SESSION['cart'][$_POST['index']])) {
    $index = $_POST['index'];

    // Remove the item
    unset($_SESSION['cart'][$index]);

    // Re-index the cart to avoid gaps in the array
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    // If the cart is now empty, return success with empty status
    if (empty($_SESSION['cart'])) {
        echo json_encode(['success' => true, 'empty' => true]);
    } else {
        echo json_encode(['success' => true, 'empty' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
}
?>
