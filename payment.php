<?php
require_once 'config/auth.php';
require_once 'config/database.php';
require_once 'config/cart_functions.php';

// Require authentication
requireAuth();

$user_id = $_SESSION['user_id'];

// Get cart data from POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_total = floatval($_POST['cart_total']);
    $cart_items = json_decode($_POST['cart_items'], true);
} else {
    // Redirect if accessed directly
    header('Location: cart.php');
    exit;
}

// If cart is empty, redirect back to cart
if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Calculate order details
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_cost = 9.99;
$tax_rate = 0.08;
$tax_amount = $subtotal * $tax_rate;
$grand_total = $subtotal + $tax_amount + $shipping_cost;

// Razorpay API credentials
$key_id = "rzp_test_iZLI83hLdG7JqU";
$key_secret = "YOUR_RAZORPAY_KEY_SECRET";

// Create order in Razorpay
require_once 'vendor/autoload.php';

use Razorpay\Api\Api;

$api = new Api($key_id, $key_secret);

// Create order
$orderData = [
    'receipt'         => 'order_' . time(),
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
                <a href=" mus_home.php #contact" class="nav">Contact</a>
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
                <div class="payment-container">
                    <div class="payment-summary">
                        <h3 class="summary-title">Order Summary</h3>
                        
                        <div class="order-items">
                            <?php foreach($cart_items as $item): ?>
                            <div class="order-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <div style="font-size: 0.9rem; color: var(--muted);">
                                        Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?>
                                    </div>
                                </div>
                                <div>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>₹<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>₹<?php echo number_format($shipping_cost, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (8%):</span>
                            <span>₹<?php echo number_format($tax_amount, 2); ?></span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Total Amount:</span>
                            <span>₹<?php echo number_format($grand_total, 2); ?></span>
                        </div>
                    </div>
                    
                    <div class="payment-form">
                        <h3 class="summary-title">Payment Details</h3>
                        
                        <div class="payment-method-info">
                            <div class="payment-icon-large">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h4>Razorpay Payment Gateway</h4>
                            <p>Secure payment powered by Razorpay</p>
                        </div>
                        
                        <?php if(isset($error)): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo $error; ?>
                            </div>
                            <a href="cart.php" class="btn ghost">Back to Cart</a>
                        <?php else: ?>
                            <form id="payment-form" method="POST" action="payment_success.php">
                                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                <input type="hidden" name="cart_total" value="<?php echo $grand_total; ?>">
                                <input type="hidden" name="cart_items" value='<?php echo json_encode($cart_items); ?>'>
                                
                                <button type="button" class="btn" id="rzp-button">
                                    <i class="fas fa-lock"></i> Pay Now - ₹<?php echo number_format($grand_total, 2); ?>
                                </button>
                                
                                <a href="cart.php" class="btn ghost" style="margin-top: 15px;">
                                    <i class="fas fa-arrow-left"></i> Back to Cart
                                </a>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>© <span id="year"></span> Symphony Musical Instruments. All rights reserved.</footer>

    <?php if(!isset($error)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('year').textContent = new Date().getFullYear();
            
            var options = {
                "key": "<?php echo $key_id; ?>",
                "amount": "<?php echo $grand_total * 100; ?>",
                "currency": "INR",
                "name": "Symphony Musical Instruments",
                "description": "Order Payment",
                "image": "img/logo3.png",
                "order_id": "<?php echo $order_id; ?>",
                "handler": function (response) {
                    // Create a form to submit payment details
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'payment_success.php';

                    // Add all necessary fields
                    var formData = {
                        'razorpay_payment_id': response.razorpay_payment_id,
                        'razorpay_order_id': response.razorpay_order_id,
                        'razorpay_signature': response.razorpay_signature,
                        'order_id': '<?php echo $order_id; ?>',
                        'cart_total': '<?php echo $grand_total; ?>',
                        'cart_items': '<?php echo addslashes(json_encode($cart_items)); ?>'
                    };

                    // Add all fields to form
                    for (var key in formData) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = formData[key];
                        form.appendChild(input);
                    }

                    document.body.appendChild(form);
                    form.submit();
                },
                "prefill": {
                    "name": "<?php echo htmlspecialchars($_SESSION['name']); ?>",
                    "email": "<?php echo htmlspecialchars($_SESSION['email']); ?>"
                },
                "theme": {
                    "color": "#b26f30"
                }
            };
            
            var rzp = new Razorpay(options);
            document.getElementById('rzp-button').onclick = function(e) {
                rzp.open();
                e.preventDefault();
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>