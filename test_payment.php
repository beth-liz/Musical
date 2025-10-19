<?php
session_start();
require_once 'config/database.php';
require_once 'config/cart_functions.php';

echo "<h2>Payment Test</h2>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "❌ Not logged in. Please login first.";
    exit;
}

$user_id = $_SESSION['user_id'];
echo "✅ User ID: " . $user_id . "<br>";

// Test 1: Check current cart
$cart_count = getCartItemCount($user_id);
echo "🛒 Current cart count: " . $cart_count . "<br>";

// Test 2: Test clear cart function
echo "<h3>Testing Cart Clear Function:</h3>";
$result = clearUserCart($user_id);
echo "Clear cart result: " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "<br>";

// Test 3: Check cart after clearing
$cart_count_after = getCartItemCount($user_id);
echo "🛒 Cart count after clearing: " . $cart_count_after . "<br>";

if ($cart_count_after == 0) {
    echo "🎉 Cart clearing is working perfectly!";
} else {
    echo "❌ Cart is not being cleared. There might be a database issue.";
}
?>