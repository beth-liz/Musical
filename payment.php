<?php
require_once 'config/auth.php';
require_once 'config/database.php';
require_once 'config/cart_functions.php';

// Require authentication
requireAuth();

$user_id = $_SESSION['user_id'];

// Get user's cart items and calculate total
$cart_items = getUserCart($user_id);
$cart_total = 0;
$shipping_cost = 9.99;
$tax_rate = 0.08;

foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}

$tax_amount = $cart_total * $tax_rate;
$grand_total = $cart_total + $tax_amount + $shipping_cost;

// If cart is empty, redirect back to cart
if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['razorpay_payment_id'])) {
        // Payment successful - handle the success scenario
        $payment_id = $_POST['razorpay_payment_id'];
        $order_id = $_POST['razorpay_order_id'];
        $signature = $_POST['razorpay_signature'];
        
        // Verify payment signature
        $generated_signature = hash_hmac('sha256', $order_id . "|" . $payment_id, "YOUR_RAZORPAY_KEY_SECRET");
        
        if ($generated_signature == $signature) {
            // Payment verification successful
            // Clear the cart and create order record
            clearUserCart($user_id);
            
            // Redirect to success page
            header('Location: payment_success.php?payment_id=' . $payment_id . '&order_id=' . $order_id);
            exit;
        } else {
            // Payment verification failed
            $error = "Payment verification failed. Please try again.";
        }
    }
}

// Razorpay API credentials (Replace with your actual credentials)
$key_id = "YOUR_RAZORPAY_KEY_ID";
$key_secret = "YOUR_RAZORPAY_KEY_SECRET";

// Create order in Razorpay
require_once 'vendor/autoload.php'; // Make sure to install Razorpay PHP SDK

use Razorpay\Api\Api;

$api = new Api($key_id, $key_secret);

// Create order
$orderData = [
    'receipt'         => 'rcptid_' . time(),
    'amount'          => $grand_total * 100, // Amount in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // Auto capture
];

try {
    $razorpayOrder = $api->order->create($orderData);
    $order_id = $razorpayOrder['id'];
} catch (Exception $e) {
    $error = "Error creating order: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Symphony Musical Instruments</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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

        .auth-links a:hover {
            background: #c53030 !important;
            color: white !important;
            text-decoration: none !important;
            transform: translateY(-2px) !important;
        }

        /* Payment Container */
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .payment-summary {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .payment-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn{
            background:var(--accent); color:#fff; padding:12px 28px;
            border-radius:6px; text-decoration:none; font-weight:600;
            box-shadow: 0 4px 15px rgba(178, 111, 48, 0.3); 
            display:inline-block; border:none; cursor:pointer;
            transition: all 0.2s ease;
            width: 100%;
            font-size: 1.1rem;
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
            padding: 8px 0;
        }
        .summary-total {
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--accent);
        }
        .order-items {
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .payment-method-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .payment-icon-large {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 10px;
        }
        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .success-message {
            background: #c6f6d5;
            color: #276749;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }

        /* Footer */
        footer{
            border-top:1px solid rgba(255, 255, 255, 0.2); text-align:center; padding:35px;
            color: rgba(255, 255, 255, 0.8); font-size:1rem; 
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            margin-top: 50px;
        }

        /* Responsive */
        @media(max-width:1000px){
            .nav-main{position: static; transform: none; width: 100%; justify-content: space-around; margin-top: 10px; order: 2;} 
            .nav-row{flex-direction: column; align-items: flex-start;} 
            .brand{width: 100%; text-align: center; order: 1;} 
            .cart-link{order: 1; position: absolute; right: 20px;}
            .auth-links{order: 1; margin-left: 0; margin-top: 10px; width: 100%; justify-content: center;}
            .payment-container {
                grid-template-columns: 1fr;
            }
        }
        @media(max-width:600px){
            .nav-main{flex-wrap: wrap;} 
            .nav-main a{margin: 5px 10px; font-size: 0.9rem;} 
            .payment-summary, .payment-form {padding: 20px; margin: 10px;}
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
                <a href="mus_home.php #contact" class="nav">Contact</a>
                <?php if(isAdmin()): ?>
                    <a href="add_products.php" class="nav">Add Product</a>
                <?php endif; ?>
            </nav>
            <a href="cart.php" class="cart-link">
                <span class="icon">🛒</span> Cart
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
                <h2>Complete Your Payment</h2>
                
                <?php if(isset($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="payment-container">
                    <div class="payment-summary">
                        <h3 class="summary-title">Order Summary</h3>
                        
                        <div class="order-items">
                            <?php foreach($cart_items as $item): ?>
                                <div class="order-item">
                                    <span><?php echo htmlspecialchars($item['title']); ?> × <?php echo $item['quantity']; ?></span>
                                    <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
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
                            <span>Total Amount:</span>
                            <span>₹<?php echo number_format($grand_total, 2); ?></span>
                        </div>
                    </div>
                    
                    <div class="payment-form">
                        <div class="payment-method-info">
                            <div class="payment-icon-large">
                                <i class="fab fa-cc-paypal"></i>
                            </div>
                            <h3>PayPal Payment</h3>
                            <p>You'll be redirected to Razorpay for secure payment processing</p>
                        </div>
                        
                        <form id="payment-form" method="POST">
                            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                            
                            <button type="button" id="pay-button" class="btn">
                                <i class="fas fa-lock"></i> Pay ₹<?php echo number_format($grand_total, 2); ?>
                            </button>
                        </form>
                        
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="cart.php" class="btn ghost">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                        </div>
                        
                        <div style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: var(--muted);">
                            <p><i class="fas fa-shield-alt"></i> Your payment is secure and encrypted</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>© <span id="year"></span> Symphony Musical Instruments. All rights reserved.</footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('year').textContent = new Date().getFullYear();
            
            const payButton = document.getElementById('pay-button');
            
            payButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                const options = {
                    "key": "<?php echo $key_id; ?>",
                    "amount": "<?php echo $grand_total * 100; ?>",
                    "currency": "INR",
                    "name": "Symphony Musical Instruments",
                    "description": "Order Payment",
                    "image": "https://example.com/your_logo.jpg",
                    "order_id": "<?php echo $order_id; ?>",
                    "handler": function (response){
                        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                        document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                        document.getElementById('razorpay_signature').value = response.razorpay_signature;
                        document.getElementById('payment-form').submit();
                    },
                    "prefill": {
                        "name": "<?php echo htmlspecialchars($_SESSION['name']); ?>",
                        "email": "<?php echo htmlspecialchars($_SESSION['email'] ?? 'customer@example.com'); ?>",
                        "contact": "9999999999"
                    },
                    "notes": {
                        "address": "Symphony Musical Instruments"
                    },
                    "theme": {
                        "color": "#b26f30"
                    }
                };
                
                const rzp = new Razorpay(options);
                rzp.open();
            });
        });
    </script>
</body>
</html>