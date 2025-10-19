<?php
require_once 'config/auth.php';
require_once 'config/database.php';
require_once 'config/cart_functions.php';

// Require authentication
requireAuth();

// Handle add to cart request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    if (addToUserCart($_SESSION['user_id'], $product_id)) {
        $success_message = "Product added to cart successfully!";
    } else {
        $error_message = "Failed to add product to cart.";
    }
}

// Get all products
try {
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE is_active = 1 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    $products = [];
}

// Get cart count for current user
$cart_count = getCartItemCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Musical Instruments</title>
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

        /* Product Grid - Fixed 4 columns */
        .product-grid{
            display: grid;
            gap: 30px;
            grid-template-columns: repeat(4, 1fr);
        }
        
        .product-card{
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border-radius:var(--radius); 
            overflow:hidden;
            box-shadow:var(--shadow); 
            display:flex; 
            flex-direction:column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: 100%;
        }
        .product-card:hover{
            transform:translateY(-5px); 
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
        }
        .product-card img{
            width:100%; 
            height:220px; 
            object-fit:cover;
        }
        .product-card .pad{
            padding:20px; 
            flex:1; 
            display:flex; 
            flex-direction:column;
        }
        .product-title{
            font-weight:600; 
            margin:0 0 4px; 
            font-size:1.25rem;
        }
        .product-meta{
            color:var(--muted); 
            font-size:1rem; 
            margin-bottom:15px;
        }
        .product-actions{
            margin-top:auto; 
            display:flex; 
            gap:12px; 
            padding-top:10px;
        }
        .btn{
            background:var(--accent); 
            color:#fff; 
            padding:12px 24px;
            border-radius:6px; 
            text-decoration:none; 
            font-weight:600;
            box-shadow: 0 4px 15px rgba(178, 111, 48, 0.3); 
            display:inline-block; 
            border:none; 
            cursor:pointer;
            transition: all 0.2s ease;
            flex: 1;
            font-size: 0.9rem;
            text-align: center;
        }
        .btn:hover{
            background:var(--accent-hover); 
            box-shadow: 0 6px 18px rgba(178, 111, 48, 0.4);
        }
        .btn.ghost{
            background:transparent; 
            color:var(--accent); 
            border:2px solid var(--accent);
            box-shadow:none; 
            padding:10px 20px;
        }
        .btn.ghost:hover{
            background:var(--accent); 
            color:#fff;
            box-shadow: 0 4px 12px rgba(178, 111, 48, 0.4);
        }
        h2{
            text-align:center; 
            font-size:2.2rem; 
            margin-bottom:50px; 
            color: white; 
            font-weight:700; 
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Footer */
        footer{
            border-top:1px solid rgba(255, 255, 255, 0.2); 
            text-align:center; 
            padding:35px;
            color: rgba(255, 255, 255, 0.8); 
            font-size:1rem; 
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            margin-top: 50px;
        }

        /* Responsive adjustments */
        @media(max-width: 1400px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media(max-width: 1000px) {
            .nav-main{
                position: static; 
                transform: none; 
                width: 100%; 
                justify-content: space-around; 
                margin-top: 10px; 
                order: 2;
            } 
            .nav-row{
                flex-direction: column; 
                align-items: flex-start;
            } 
            .brand{
                width: 100%; 
                text-align: center; 
                order: 1;
            } 
            .cart-link{
                order: 1; 
                position: absolute; 
                right: 20px;
            }
            .auth-links{
                order: 1; 
                margin-left: 0; 
                margin-top: 10px; 
                width: 100%; 
                justify-content: center;
            }
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 600px){
            .nav-main{
                flex-wrap: wrap;
            } 
            .nav-main a{
                margin: 5px 10px; 
                font-size: 0.9rem;
            } 
            .product-grid{
                grid-template-columns: 1fr;
            }
            .btn {
                padding: 10px 16px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-row">
            <a href="mus_home.php" class="brand"></i> Symphony</a>
            <nav class="nav-main">
                <a href="mus_home.php" class="nav">Home</a>
                <a href="shop.php" class="nav active">Shop</a>
                <?php if(isAdmin()): ?>
                    <a href="add_products.php" class="nav">Add Product</a>
                <?php endif; ?>
            </nav>
            <a href="cart.php" class="cart-link">
                <span class="icon"><i class="fas fa-shopping-cart"></i></span> Cart (<span id="cart-count"><?php echo $cart_count; ?></span>)
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
                <h2>Our Latest Instruments</h2>
                
                <?php if(isset($success_message)): ?>
                    <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error_message)): ?>
                    <div style="background: #fed7d7; color: #c53030; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div id="productGrid" class="product-grid">
                    <?php if(empty($products)): ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: rgba(255, 255, 255, 0.95); border-radius: 12px; margin: 20px 0;">
                            <div style="font-size: 4rem; margin-bottom: 20px;"><i class="fas fa-music"></i></div>
                            <h3 style="color: var(--accent-2); margin-bottom: 15px;">No Products Yet</h3>
                            <p style="color: var(--muted); margin-bottom: 25px;">Our musical instruments collection will be updated soon!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($products as $product): ?>
                        <div class="product-card">
                            <?php if($product['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/400x220/ff7a59/ffffff?text=Image+Unavailable';">
                            <?php else: ?>
                                <img src="https://placehold.co/400x220/ff7a59/ffffff?text=No+Image" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <?php endif; ?>
                            <div class="pad">
                                <h4 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h4>
                                <div class="product-meta">₹<?php echo number_format($product['price'], 2); ?></div>
                                <?php if($product['description']): ?>
                                    <p style="color: var(--muted); font-size: 0.9rem; margin: 10px 0; flex: 1;"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . (strlen($product['description']) > 80 ? '...' : ''); ?></p>
                                <?php endif; ?>
                                <div class="product-actions">
                                    <form method="POST" style="display: inline; margin: 0;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <button type="submit" name="add_to_cart" class="btn">Add Cart</button>
                                    </form>
                                    <button class="btn ghost" onclick="alert('Viewing details for <?php echo htmlspecialchars($product['title']); ?>...')">Details</button> 
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>&copy; <span id="year"></span> Musical Instruments. All rights reserved.</footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('year').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>