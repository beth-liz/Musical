<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Redirect if already logged in
requireGuest();

// Get all products for display (public view)
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musical Instruments - Home</title>
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
            font-weight:700; font-size:1.8rem; 
            color: white; 
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none !important;
        }
        .brand:hover {
            text-decoration: none !important;
            color: white !important;
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
        
        .auth-links {
            display: flex;
            gap: 15px;
            margin-left: 20px;
        }
        
        /* Auth links as buttons (original style) */
        .auth-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease !important;
            display: inline-block;
        }
        
        /* Auth link hover effects - button style */
        .auth-links a:hover {
            background: var(--accent) !important;
            color: white !important;
            text-decoration: none !important;
            transform: translateY(-2px) !important;
        }
        
        .auth-links a.signup {
            background: var(--accent);
            color: white;
        }
        
        .auth-links a.signup:hover {
            background: var(--accent-hover) !important;
            color: white !important;
            transform: translateY(-2px) !important;
        }

        /* Remove line effect from auth links */
        .auth-links a::after {
            display: none;
        }

        /* Hero Section */
        .hero{
            display:flex; flex-wrap:wrap; align-items:center; 
            justify-content:space-between; 
            padding:90px 20px; 
            gap:50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 200px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hero-text{flex:1 1 380px;}
        .hero-text h1{
            margin:0 0 16px; font-size:3rem; font-weight:700;
            line-height:1.2; color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        .hero-text p{
            color: rgba(255, 255, 255, 0.9); font-size:1.2rem; margin-bottom:30px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        .hero img{
            max-width:480px; height: auto; border-radius:var(--radius);
            box-shadow: 0 10px 30px rgba(255, 122, 89, 0.3);
            border: 5px solid var(--card);
            object-fit: cover;
        }
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
            transform: translateY(-2px);
        }

        /* Product Grid */
        .product-grid{
            display:grid; gap:30px;
            grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
        }
        .product-card{
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border-radius:var(--radius); overflow:hidden;
            box-shadow:var(--shadow); display:flex; flex-direction:column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .product-card:hover{transform:translateY(-5px); box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);}
        .product-card img{width:100%; height:220px; object-fit:cover;}
        .product-card .pad{padding:20px; flex:1; display:flex; flex-direction:column;}
        .product-title{font-weight:600; margin:0 0 4px; font-size:1.25rem;}
        .product-meta{color:var(--muted); font-size:1rem; margin-bottom:15px;}
        .product-actions{margin-top:auto; display:flex; gap:12px; padding-top:10px;}
        .btn.ghost{
            background:transparent; color:var(--accent); 
            border:2px solid var(--accent);
            box-shadow:none; padding:10px 24px;
        }
        .btn.ghost:hover{
            background:var(--accent); color:#fff;
            box-shadow: 0 4px 12px rgba(178, 111, 48, 0.4);
            transform: translateY(-2px);
        }
        h2{text-align:center; font-size:2.2rem; margin-bottom:50px; color: white; font-weight:700; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);}

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
            .auth-links{order: 1; position: absolute; right: 20px;}
        }
        @media(max-width:600px){
            .nav-main{flex-wrap: wrap;} 
            .nav-main a{margin: 5px 10px; font-size: 0.9rem;} 
            .product-grid{grid-template-columns: 1fr;}
            .hero{margin: 20px;}
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-row">
            <a href="home.php" class="brand">Symphony </a>
            <nav class="nav-main">
                <a href="home.php" class="nav active">Home</a>
                <a href="shop.php" class="nav">Shop</a>
            </nav>
            <div class="auth-links">
                <a href="signin.php">Sign In</a>
                <a href="signup.php" class="signup">Sign Up</a>
            </div>
        </div>
    </header>

    <main>
        <div class="hero container">
            <div class="hero-text">
                <h1>Find your perfect instrument. Play with passion.</h1>
                <p>Explore our curated collection of premium guitars, keyboards, drums, and accessories. Quality and sound you can trust.</p>
                <a href="shop.php" class="btn">Explore The Shop</a>
            </div>
            <img src="img/six.jpg" alt="Musical instruments showcase" onerror="this.onerror=null;this.src='https://placehold.co/700x560/ff7a59/ffffff?text=Musical+Instruments';">
        </div>

        <section class="container" style="padding-top: 0;">
            <h2>Our Latest Instruments</h2>
            <div class="product-grid">
                <?php if(empty($products)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: rgba(255, 255, 255, 0.95); border-radius: 12px; margin: 20px 0;">
                        <div style="font-size: 4rem; margin-bottom: 20px;">🎵</div>
                        <h3 style="color: var(--accent-2); margin-bottom: 15px;">No Products Yet</h3>
                        <p style="color: var(--muted); margin-bottom: 25px;">Our musical instruments collection will be updated soon!</p>
                    </div>
                <?php else: ?>
                    <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <?php if($product['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/300x220/ff7a59/ffffff?text=Image+Unavailable';">
                        <?php else: ?>
                            <img src="https://placehold.co/300x220/ff7a59/ffffff?text=No+Image" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php endif; ?>
                        <div class="pad">
                            <h4 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h4>
                            <div class="product-meta">₹<?php echo number_format($product['price'], 2); ?></div>
                            <?php if($product['description']): ?>
                                <p style="color: var(--muted); font-size: 0.9rem; margin: 10px 0;"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : ''); ?></p>
                            <?php endif; ?>
                            <div class="product-actions">
                                <a href="signin.php" class="btn">Add to Cart</a>
                               
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>© <span id="year"></span> Musical Instruments. All rights reserved.</footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('year').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>
