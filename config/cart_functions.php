<?php
function addToUserCart($user_id, $product_id) {
    global $pdo;
    try {
        // Check if product exists and is active
        $stmt = $pdo->prepare("SELECT product_id FROM products WHERE product_id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        if (!$stmt->fetch()) {
            return false;
        }
        
        // Check if item already in cart
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update quantity if already in cart
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
        } else {
            // Insert new item
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $product_id]);
        }
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function getUserCart($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT c.*, p.title, p.price, p.image_path FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function getCartItemCount($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function updateCartQuantity($user_id, $product_id, $quantity) {
    global $pdo;
    if ($quantity <= 0) {
        removeFromCart($user_id, $product_id);
    } else {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $user_id, $product_id]);
    }
}

function removeFromCart($user_id, $product_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

function clearUserCart($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return true;
}
?>