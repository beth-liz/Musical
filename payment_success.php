<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();
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
        /* Add your existing CSS styles here */
        .success-container {
            max-width: 600px;
            margin: 100px auto;
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
    </style>
</head>
<body>
    <!-- Include your header -->
    
    <main>
        <section>
            <div class="container">
                <div class="success-container">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Payment Successful!</h2>
                    <p>Thank you for your purchase. Your order has been confirmed.</p>
                    <p>Payment ID: <?php echo htmlspecialchars($_GET['payment_id'] ?? 'N/A'); ?></p>
                    <p>Order ID: <?php echo htmlspecialchars($_GET['order_id'] ?? 'N/A'); ?></p>
                    
                    <div style="margin-top: 30px;">
                        <a href="mus_home.php" class="btn">Continue Shopping</a>
                        <a href="orders.php" class="btn ghost" style="margin-left: 10px;">View Orders</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Include your footer -->
</body>
</html>