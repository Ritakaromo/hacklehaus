<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['id'] ?? null;

if ($productId) {
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 1;
    } else {
        $_SESSION['cart'][$productId]++;
    }

    echo json_encode([
        'message' => 'Added to cart',
        'cart' => $_SESSION['cart']
    ]);
} else {
    echo json_encode(['error' => 'Invalid product ID']);
}
