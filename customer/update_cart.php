<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = isset($_POST['index']) ? (int)$_POST['index'] : null;
    $change = isset($_POST['change']) ? (int)$_POST['change'] : null;

    if ($index !== null && $change !== null && isset($_SESSION['cart'][$index])) {
        $newQuantity = $_SESSION['cart'][$index]['quantity'] + $change;
        
        if ($newQuantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $newQuantity;
        } else {
            // Remove item if quantity becomes 0 or negative
            array_splice($_SESSION['cart'], $index, 1);
        }

        // Calculate total quantity
        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalQuantity += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'totalQuantity' => $totalQuantity
        ]);
        exit;
    }
}

echo json_encode(['success' => false]);
