<?php
// clear_cart.php - Endpoint to clear user's cart after successful payment
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/auth.php';
require_once 'config/database.php';
require_once 'config/cart_functions.php';

// Require authentication
requireAuth();

$user_id = $_SESSION['user_id'];

// Clear the cart
clearUserCart($user_id);

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Cart cleared']);
exit;
?>