<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $_SESSION['cart'][] = $data['id'];
    echo json_encode([
        'message' => 'Added to cart',
        'count' => count($_SESSION['cart']),
        'cart' => $_SESSION['cart']
    ]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
