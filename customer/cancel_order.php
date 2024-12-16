<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clear the cart
    $_SESSION['cart'] = array();
    
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false]);
