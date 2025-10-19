<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/auth.php';
require_once 'config/database.php';
require_once 'config/cart_functions.php';

// Require authentication
requireAuth();

// Get order details from URL parameters
$order_id = $_GET['order_id'] ?? '';
$payment_id = $_GET['payment_id'] ?? '';

// If no order ID, redirect to cart
if (empty($order_id)) {
    header('Location: cart.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

// If order not found, redirect to cart
if (!$order) {
    header('Location: cart.php');
    exit;
}

// ✅ CLEAR THE CART AFTER SUCCESSFUL PAYMENT
clearUserCart($user_id);

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.title, p.image_path 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Symphony Musical Instruments</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --accent: rgba(178, 111, 48, 0.83);
            --accent-hover: rgb(213, 148, 34);
            --muted: #6b7280;
        }
        *{box-sizing:border-box}
        body{
            margin:0; 
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.9) 100%), url('img/one.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            line-height:1.6;
            -webkit-font-smoothing:antialiased;
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        header{
            position:sticky; top:0; z-index:20; 
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-bottom:3px solid var(--accent);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .nav-row{
            max-width:1200px; margin:auto; display:flex; 
            align-items:center; justify-content:space-between; 
            padding:18px 20px;
        }
        .brand{
            font-weight:700; font-size:1.8rem; text-decoration:none; 
            color: white; letter-spacing: 1px;
        }
        .nav-main {
            display: flex;
            gap: 40px;
        }
        .nav-main a {
            color: white !important;
            font-weight:500; 
            text-decoration: none !important;
            transition: color 0.3s ease !important;
        }
        .nav-main a:hover {
            color: var(--accent) !important;
        }
        .cart-link{
            color: white; 
            text-decoration: none;
            font-weight: 600;
        }
        .auth-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .auth-links span {
            color: white;
            font-weight: 500;
        }
        .auth-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            background: #e53e3e;
            transition: all 0.3s ease !important;
        }
        .auth-links a:hover {
            background: #c53030 !important;
        }
        section {
            padding: 60px 20px;
        }
        .container{
            max-width:1200px;
            margin:auto;
        }
        .success-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .success-icon {
            font-size: 4rem;
            color: #38a169;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 2rem;
            margin: 20px 0;
            color: #0f172a;
        }
        .order-details {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .order-items {
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .order-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        .btn{
            background: var(--accent); color:#fff; padding:12px 28px;
            border-radius:6px; text-decoration:none; font-weight:600;
            box-shadow: 0 4px 15px rgba(178, 111, 48, 0.3); 
            display:inline-block; border:none; cursor:pointer;
            transition: all 0.2s ease;
            margin: 10px;
        }
        .btn:hover{
            background: var(--accent-hover); 
            box-shadow: 0 6px 18px rgba(178, 111, 48, 0.4);
        }
        .btn.ghost{
            background:transparent; color:var(--accent); 
            border:2px solid var(--accent);
            box-shadow:none;
        }
        .btn.ghost:hover{
            background:var(--accent); color:#fff;
        }
        footer{
            border-top:1px solid rgba(255, 255, 255, 0.2); 
            text-align:center; padding:35px;
            color: rgba(255, 255, 255, 0.8); 
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-row">
            <a href="mus_home.php" class="brand">Symphony</a>
            <nav class="nav-main">
                <a href="mus_home.php">Home</a>
                <a href="shop.php">Shop</a>
                <a href="mus_home.php#about">About</a>
                <a href="mus_home.php#contact">Contact</a>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="add_products.php">Add Product</a>
                <?php endif; ?>
            </nav>
            <a href="cart.php" class="cart-link">
                <span>🛒 Cart</span>
            </a>
            <div class="auth-links">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <span style="background: var(--accent); padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">Admin</span>
                    <?php endif; ?>
                </span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <section>
            <div class="container">
                <div class="success-container">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Payment Successful!</h2>
                    <p>Thank you for your purchase. Your order has been confirmed.</p>
                    
                    <div class="order-details">
                        <h3 style="text-align: center; margin-bottom: 20px;">Order Details</h3>
                        
                        <div class="detail-row">
                            <span><strong>Order ID:</strong></span>
                            <span>#<?php echo htmlspecialchars($order['order_id']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span><strong>Payment ID:</strong></span>
                            <span><?php echo substr($payment_id, 0, 12) . '...'; ?></span>
                        </div>
                        <div class="detail-row">
                            <span><strong>Order Date:</strong></span>
                            <span><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span><strong>Total Amount:</strong></span>
                            <span style="color: #38a169; font-weight: 600;">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="detail-row">
                            <span><strong>Status:</strong></span>
                            <span style="color: #38a169; font-weight: 600;"><?php echo ucfirst($order['status']); ?></span>
                        </div>
                    </div>

                    <?php if(!empty($order_items)): ?>
                    <div class="order-items">
                        <h4>Order Items</h4>
                        <?php foreach($order_items as $item): ?>
                        <div class="order-item">
                            <div style="display: flex; align-items: center;">
                                <img src="<?php echo htmlspecialchars($item['image_path'] ?: 'https://placehold.co/60x60/ff7a59/ffffff?text=Item'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <div style="text-align: left;">
                                    <div style="font-weight: 600;"><?php echo htmlspecialchars($item['title']); ?></div>
                                    <div style="color: var(--muted); font-size: 0.9rem;">Qty: <?php echo $item['quantity']; ?></div>
                                </div>
                            </div>
                            <div style="font-weight: 600;">
                                ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 30px;">
                        <a href="mus_home.php" class="btn">Continue Shopping</a>
                        <a href="orders.php" class="btn ghost">View All Orders</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>© <span id="year"></span> Symphony Musical Instruments. All rights reserved.</footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('year').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>