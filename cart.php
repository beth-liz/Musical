<?php
require_once 'config/auth.php';
require_once 'config/database.php';
require_once 'config/cart_functions.php';

// Require authentication
requireAuth();

$user_id = $_SESSION['user_id'];

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $quantity = intval($_POST['quantity']);
        updateCartQuantity($user_id, $product_id, $quantity);
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        removeFromCart($user_id, $product_id);
    } elseif (isset($_POST['clear_cart'])) {
        clearUserCart($user_id);
    } elseif (isset($_POST['checkout'])) {
        // Handle checkout process
        header('Location: checkout.php');
        exit;
    }
}

// Get user's cart items
$cart_items = getUserCart($user_id);
$cart_count = getCartItemCount($user_id);
$cart_total = 0;
$shipping_cost = 9.99;
$tax_rate = 0.08; // 8% tax

foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

$tax_amount = $cart_total * $tax_rate;
$grand_total = $cart_total + $tax_amount + $shipping_cost;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Symphony Musical Instruments</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg: #fbfbfb;
            --card: #ffffff;
            --muted: #6b7280;
            --accent: rgba(178, 111, 48, 0.83);
            --accent-hover: rgb(213, 148, 34);
            --accent-2: #0f172a;
            --radius: 12px;
            --shadow: 0 6px 18px rgba(15,23,42,0.08);
            --maxw: 1200px;
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--accent-2);
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
        }
        h1, h2, h3, .brand {
            font-family: 'Playfair Display', serif;
        }
        section {
            padding: 60px 20px;
        }
        .container{
            max-width:var(--maxw);
            margin:auto;
        }

        /* Header Styling */
        header{
            position:sticky; top:0; z-index:20; 
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-bottom:3px solid var(--accent);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .nav-row{
            max-width:var(--maxw); margin:auto; display:flex; 
            align-items:center; justify-content:space-between; 
            padding:18px 20px;
            position: relative;
        }
        .brand{
            font-weight:700; font-size:1.8rem; text-decoration:none; 
            color: white; 
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-main {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 40px;
        }

        /* Navigation Links - Text color change with line effect */
        .nav-main a {
            color: white !important;
            font-weight:500; 
            padding: 8px 0;
            text-decoration: none !important;
            border: none;
            background: transparent;
            cursor: pointer;
            display: inline-block;
            position: relative;
            transition: color 0.3s ease !important;
        }

        /* Line effect on hover */
        .nav-main a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--accent);
            transition: width 0.3s ease;
        }

        /* Hover effects */
        .nav-main a:hover {
            color: var(--accent) !important;
            background: transparent !important;
            transform: none !important;
        }

        .nav-main a:hover::after {
            width: 100%;
        }

        .nav-main a.active {
            color: var(--accent) !important;
        }

        .nav-main a.active::after {
            width: 100%;
            background-color: var(--accent);
        }

        .cart-link{
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: white; 
            margin-left: auto;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease !important;
        }
        .cart-link:hover{
            background: var(--accent) !important;
            color: white !important;
            transform: translateY(-2px) !important;
        }
        .cart-link .icon {
            font-size: 1.3rem;
            line-height: 1;
        }

        /* Auth links as buttons */
        .auth-links {
            display: flex;
            gap: 15px;
            margin-left: 20px;
            align-items: center;
        }

        .auth-links span {
            color: white;
            font-weight: 500;
            padding: 8px 0;
        }

        .auth-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease !important;
            display: inline-block;
            background: #e53e3e;
        }

        /* Auth link hover effects - button style */
        .auth-links a:hover {
            background: #c53030 !important;
            color: white !important;
            text-decoration: none !important;
            transform: translateY(-2px) !important;
        }

        /* Cart Styling */
        .cart-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            margin: 20px;
        }
        .cart-items-container {
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .cart-summary-container {
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .cart-item{
            display:flex; align-items:center; gap:25px; 
            border-bottom:1px solid #e2e8f0; padding:20px 0;
        }
        .cart-item img{width:100px; height:80px; object-fit:cover; border-radius:8px;}
        .btn{
            background:var(--accent); color:#fff; padding:12px 28px;
            border-radius:6px; text-decoration:none; font-weight:600;
            box-shadow: 0 4px 15px rgba(178, 111, 48, 0.3); 
            display:inline-block; border:none; cursor:pointer;
            transition: all 0.2s ease;
        }
        .btn:hover{
            background:var(--accent-hover); 
            box-shadow: 0 6px 18px rgba(178, 111, 48, 0.4);
        }
        .btn.ghost{
            background:transparent; color:var(--accent); 
            border:2px solid var(--accent);
            box-shadow:none; padding:10px 24px;
        }
        .btn.ghost:hover{
            background:var(--accent); color:#fff;
            box-shadow: 0 4px 12px rgba(178, 111, 48, 0.4);
        }
        h2{text-align:center; font-size:2.2rem; margin-bottom:50px; color: white; font-weight:700; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);}
        .empty-cart {
            text-align: center;
            color: var(--muted);
            padding: 40px 0;
            font-size: 1.1rem;
        }
        .qty-btn, .remove-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
            font-weight: 600;
        }
        .qty-btn:hover { background: var(--accent-hover); }
        .remove-btn {
            background: #e53e3e;
        }
        .remove-btn:hover {
            background: #c53030;
        }
        .qty-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .qty-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
        }
        .item-details {
            flex-grow: 1;
        }
        .item-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .item-price {
            color: var(--muted);
            font-size: 0.95rem;
        }
        .item-total {
            font-weight: 600;
            color: var(--accent);
            margin-top: 5px;
        }
        .summary-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 700;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .summary-total {
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--accent);
        }
        .payment-options {
            margin: 25px 0;
        }
        .payment-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        .payment-methods {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .payment-method {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .payment-method:hover {
            border-color: var(--accent);
            background: rgba(178, 111, 48, 0.05);
        }
        .payment-method.active {
            border-color: var(--accent);
            background: rgba(178, 111, 48, 0.1);
        }
        .payment-icon {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        .checkout-btn {
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            margin-top: 10px;
        }
        .continue-shopping {
            text-align: center;
            margin-top: 20px;
        }
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .cart-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        /* Footer */
        footer{
            border-top:1px solid rgba(255, 255, 255, 0.2); text-align:center; padding:35px;
            color: rgba(255, 255, 255, 0.8); font-size:1rem; 
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            margin-top: 50px;
        }

        /* Responsive adjustments */
        @media(max-width:1000px){
            .nav-main{position: static; transform: none; width: 100%; justify-content: space-around; margin-top: 10px; order: 2;} 
            .nav-row{flex-direction: column; align-items: flex-start;} 
            .brand{width: 100%; text-align: center; order: 1;} 
            .cart-link{order: 1; position: absolute; right: 20px;}
            .auth-links{order: 1; margin-left: 0; margin-top: 10px; width: 100%; justify-content: center;}
            .cart-container {
                grid-template-columns: 1fr;
            }
        }
        @media(max-width:600px){
            .nav-main{flex-wrap: wrap;} 
            .nav-main a{margin: 5px 10px; font-size: 0.9rem;} 
            .cart-items-container, .cart-summary-container {padding: 20px; margin: 10px;}
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .item-actions {
                width: 100%;
                display: flex;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-row">
            <a href="mus_home.php" class="brand">Symphony</a>
            <nav class="nav-main">
                <a href="mus_home.php" class="nav">Home</a>
                <a href="shop.php" class="nav">Shop</a>
                <a href="mus_home.php #about" class="nav">About</a>
                    <a href=" mus_home.php #contact" class="nav">Contact</a>
                <?php if(isAdmin()): ?>
                    <a href="add_products.php" class="nav">Add Product</a>
                <?php endif; ?>
            </nav>
            <a href="cart.php" class="cart-link">
                <span class="icon">🛒</span> Cart (<span id="cart-count"><?php echo $cart_count; ?></span>)
            </a>
            <div class="auth-links">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!
                    <?php if(isAdmin()): ?>
                        <span style="background: var(--accent); padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; margin-left: 5px;">Admin</span>
                    <?php endif; ?>
                </span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <section>
            <div class="container">
                <h2>Your Shopping Cart</h2>
                <div class="cart-container">
                    <div class="cart-items-container">
                        <div class="cart-header">
                            <h3 class="cart-title">Cart Items (<?php echo $cart_count; ?>)</h3>
                            <?php if(!empty($cart_items)): ?>
                                <form method="POST">
                                    <button type="submit" name="clear_cart" class="btn ghost">Clear Cart</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        
                        <div id="cart-items">
                            <?php if(empty($cart_items)): ?>
                                <div class="empty-cart">
                                    <i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 20px; color: #d1d5db;"></i>
                                    <p>Your cart is empty.</p>
                                    <a href="shop.php" class="btn" style="margin-top: 15px;">Start Shopping Now</a>
                                </div>
                            <?php else: ?>
                                <?php foreach($cart_items as $item): 
                                    $item_total = $item['price'] * $item['quantity'];
                                ?>
                                <div class="cart-item">
                                    <img src="<?php echo htmlspecialchars($item['image_path'] ?: 'https://placehold.co/100x80/ff7a59/ffffff?text=Item'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <div class="item-details">
                                        <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                        <div class="item-price">₹<?php echo number_format($item['price'], 2); ?></div>
                                        <div class="item-total">₹<?php echo number_format($item_total, 2); ?></div>
                                    </div>
                                    <div class="item-actions">
                                        <div class="qty-controls">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                                                <button type="submit" name="update_quantity" class="qty-btn minus" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>−</button>
                                            </form>
                                            <span class="qty-display"><?php echo $item['quantity']; ?></span>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                                <button type="submit" name="update_quantity" class="qty-btn plus">+</button>
                                            </form>
                                        </div>
                                        <form method="POST" style="display: inline; margin-top: 10px;">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                            <button type="submit" name="remove_item" class="remove-btn">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if(!empty($cart_items)): ?>
                    <div class="cart-summary-container">
                        <h3 class="summary-title">Order Summary</h3>
                        
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>₹<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>₹<?php echo number_format($shipping_cost, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (<?php echo $tax_rate * 100; ?>%):</span>
                            <span>₹<?php echo number_format($tax_amount, 2); ?></span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span>₹<?php echo number_format($grand_total, 2); ?></span>
                        </div>
                        
                        <div class="payment-options">
                            <div class="payment-title">Payment Method</div>
                            <div class="payment-methods">
                                <div class="payment-method active">
                                    <div class="payment-icon"><i class="fab fa-cc-paypal"></i></div>
                                    <div>PayPal</div>
                                </div>
                                <div class="payment-method">
                                    <div class="payment-icon"><i class="fas fa-credit-card"></i></div>
                                    <div>Card</div>
                                </div>
                                <div class="payment-method">
                                    <div class="payment-icon"><i class="fas fa-university"></i></div>
                                    <div>Bank</div>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST">
                            <button type="submit" name="checkout" class="btn checkout-btn">
                                <i class="fas fa-lock"></i> Proceed to Checkout
                            </button>
                        </form>
                        
                        <div class="continue-shopping">
                            <a href="shop.php" class="btn ghost">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>© <span id="year"></span> Symphony Musical Instruments. All rights reserved.</footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('year').textContent = new Date().getFullYear();
            
            // Payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method');
            paymentMethods.forEach(method => {
                method.addEventListener('click', () => {
                    paymentMethods.forEach(m => m.classList.remove('active'));
                    method.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>