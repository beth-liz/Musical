<?php
require_once 'database.php';

function getUserCart($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, p.title, p.price, p.image_path 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function addToUserCart($user_id, $product_id, $quantity = 1) {
    global $pdo;
    
    try {
        // Check if item already exists in cart
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existingItem = $stmt->fetch();
        
        if ($existingItem) {
            // Update quantity if item exists
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $user_id, $product_id]);
        } else {
            // Insert new item
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function updateCartQuantity($user_id, $product_id, $quantity) {
    global $pdo;
    
    try {
        if ($quantity <= 0) {
            // Remove item if quantity is 0 or less
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
        } else {
            // Update quantity
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $user_id, $product_id]);
        }
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function removeFromCart($user_id, $product_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function clearUserCart($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function getCartItemCount($user_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['total'] ?: 0;
    } catch(PDOException $e) {
        return 0;
    }
}
?>