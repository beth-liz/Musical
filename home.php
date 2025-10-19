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
        <title>Symphony - Premium Musical Instruments</title>
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
                padding: 80px 20px;
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
                margin: 80px auto;
                border: 1px solid rgba(255, 255, 255, 0.2);
                max-width: 1200px;
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

            /* New Sections */
            .section-title {
                text-align: center;
                font-size: 2.2rem;
                margin-bottom: 50px;
                color: white;
                font-weight: 700;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            }
            
            .features {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 30px;
                margin-top: 50px;
            }
            
            .feature-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: var(--radius);
                padding: 30px;
                text-align: center;
                box-shadow: var(--shadow);
                transition: transform 0.3s ease;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .feature-card:hover {
                transform: translateY(-5px);
            }
            
            .feature-icon {
                font-size: 2.5rem;
                margin-bottom: 20px;
                color: var(--accent);
            }
            
            .feature-card h3 {
                margin-bottom: 15px;
                color: var(--accent-2);
            }
            
            .feature-card p {
                color: var(--muted);
            }
            
            .categories {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
                margin-top: 40px;
            }
            
            .category-card {
                position: relative;
                border-radius: var(--radius);
                overflow: hidden;
                height: 250px;
                box-shadow: var(--shadow);
                transition: transform 0.3s ease;
            }
            
            .category-card:hover {
                transform: translateY(-5px);
            }
            
            .category-card img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }
            
            .category-card:hover img {
                transform: scale(1.05);
            }
            
            .category-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
                padding: 20px;
                color: white;
            }
            
            .category-overlay h3 {
                margin: 0;
                font-size: 1.5rem;
            }
            
            .testimonials {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                margin-top: 50px;
            }
            
            .testimonial-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: var(--radius);
                padding: 30px;
                box-shadow: var(--shadow);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .testimonial-text {
                font-style: italic;
                margin-bottom: 20px;
                color: var(--accent-2);
            }
            
            .testimonial-author {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            
            .author-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background-color: var(--accent);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
            }
            
            .author-info h4 {
                margin: 0;
                color: var(--accent-2);
            }
            
            .author-info p {
                margin: 0;
                color: var(--muted);
                font-size: 0.9rem;
            }
            
            .newsletter {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                padding: 60px 40px;
                text-align: center;
                border: 1px solid rgba(255, 255, 255, 0.2);
                margin-top: 50px;
            }
            
            .newsletter h3 {
                color: white;
                margin-bottom: 20px;
                font-size: 1.8rem;
            }
            
            .newsletter p {
                color: rgba(255, 255, 255, 0.9);
                margin-bottom: 30px;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .newsletter-form {
                display: flex;
                max-width: 500px;
                margin: 0 auto;
                gap: 10px;
            }
            
            .newsletter-form input {
                flex: 1;
                padding: 12px 16px;
                border-radius: 6px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                background: rgba(255, 255, 255, 0.9);
                font-size: 1rem;
            }
            
            .newsletter-form button {
                background: var(--accent);
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 6px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            
            .newsletter-form button:hover {
                background: var(--accent-hover);
            }

            /* Footer */
            footer{
                border-top:1px solid rgba(255, 255, 255, 0.2); 
                padding: 50px 20px 30px;
                color: rgba(255, 255, 255, 0.8); 
                font-size:1rem; 
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(10px);
                margin-top: 50px;
            }
            
            .footer-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 40px;
                max-width: var(--maxw);
                margin: 0 auto;
            }
            
            .footer-column h4 {
                color: white;
                margin-bottom: 20px;
                font-size: 1.2rem;
            }
            
            .footer-column ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .footer-column ul li {
                margin-bottom: 10px;
            }
            
            .footer-column ul li a {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .footer-column ul li a:hover {
                color: var(--accent);
            }
            
            .social-links {
                display: flex;
                gap: 15px;
                margin-top: 20px;
            }
            
            .social-links a {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                color: white;
                text-decoration: none;
                transition: background 0.3s ease;
            }
            
            .social-links a:hover {
                background: var(--accent);
            }
            
            .copyright {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }
            /* About Section */
    .about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
    margin-top: 40px;
    }

    .about-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.7;
    }

    .about-text p {
    margin-bottom: 20px;
    }

    .about-image {
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    }

    .about-image img {
    width: 100%;
    height: auto;
    display: block;
    }

    /* Contact Section */
    .contact-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    margin-top: 40px;
    }

    .contact-info {
    color: rgba(255, 255, 255, 0.9);
    }

    .contact-info h3 {
    color: white;
    margin-bottom: 20px;
    font-size: 1.5rem;
    }

    .contact-info p {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    }

    .contact-info i {
    color: var(--accent);
    width: 20px;
    }

    .contact-form {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: var(--radius);
    padding: 30px;
    box-shadow: var(--shadow);
    border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .contact-form .form-group {
    margin-bottom: 20px;
    }

    .contact-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--accent-2);
    }

    .contact-form input,
    .contact-form textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
    border-color: var(--accent);
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 122, 89, 0.1);
    }

    .contact-form textarea {
    height: 150px;
    resize: vertical;
    }

    /* Responsive adjustments */
    @media(max-width:1000px){
    .nav-main{position: static; transform: none; width: 100%; justify-content: space-around; margin-top: 10px; order: 2;} 
    .nav-row{flex-direction: column; align-items: flex-start;} 
    .brand{width: 100%; text-align: center; order: 1;} 
    .cart-link{order: 1; position: absolute; right: 20px;}
    .auth-links{order: 1; margin-left: 0; margin-top: 10px; width: 100%; justify-content: center;}
    .newsletter-form {
        flex-direction: column;
    }
    .about-content,
    .contact-content {
        grid-template-columns: 1fr;
    }
    }
    @media(max-width:900px){.hero{flex-direction:column-reverse;text-align:center;}.hero img{max-width:100%;}}
    @media(max-width:600px){.nav-main{flex-wrap: wrap;} .nav-main a{margin: 5px 10px; font-size: 0.9rem;} .hero-text h1{font-size: 2.2rem;} .feature-grid{grid-template-columns: 1fr;} .section-title {font-size: 1.8rem;}}


            /* Responsive adjustments */
            @media(max-width:1000px){
                .nav-main{position: static; transform: none; width: 100%; justify-content: space-around; margin-top: 10px; order: 2;} 
                .nav-row{flex-direction: column; align-items: flex-start;} 
                .brand{width: 100%; text-align: center; order: 1;} 
                .auth-links{order: 1; position: absolute; right: 20px;}
                .newsletter-form {
                    flex-direction: column;
                }
            }
            @media(max-width:600px){
                .nav-main{flex-wrap: wrap;} 
                .nav-main a{margin: 5px 10px; font-size: 0.9rem;} 
                .product-grid{grid-template-columns: 1fr;}
                .hero{margin: 20px;}
                .section-title {
                    font-size: 1.8rem;
                }
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
                    <a href="#categories" class="nav">Categories</a>
                    <a href="#about" class="nav">About</a>
                    <a href="#contact" class="nav">Contact</a>
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

            <!-- Featured Products Section -->
            <section class="container">
                <h2 class="section-title">Our Latest Instruments</h2>
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
                                    <a href="signin.php" class="btn ghost">View Details</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div style="text-align: center; margin-top: 40px;">
                    <a href="shop.php" class="btn">View All Products</a>
                </div>
            </section>

            <!-- About Section -->
    <section class="container" id="about">
        <h2 class="section-title">About Symphony</h2>
        <div class="about-content">
        <div class="about-text">
            <p>Founded in 2010, Symphony has been at the forefront of providing premium musical instruments to musicians of all levels. Our passion for music drives us to curate the finest collection of guitars, keyboards, drums, and accessories.</p>
            <p>We believe that every musician deserves access to high-quality instruments that inspire creativity and enhance performance. Our team consists of experienced musicians who understand the nuances of sound and craftsmanship.</p>
            <p>At Symphony, we're not just selling instruments - we're helping musicians find their voice and express their artistry through the power of music.</p>
            <a href="shop.php" class="btn" style="margin-top: 20px;">Discover Our Collection</a>
        </div>
        <div class="about-image">
            <img src=img/one.jpg alt="Symphony Music Studio">
        </div>
        </div>
    </section>

            <!-- Features Section -->
            <section class="container">
                <h2 class="section-title">Why Choose Symphony</h2>
                <div class="features">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Quality Guarantee</h3>
                        <p>All our instruments come with a comprehensive warranty and quality assurance.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h3>Fast Shipping</h3>
                        <p>Free shipping on orders over ₹5000. Delivery within 3-5 business days.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Expert Support</h3>
                        <p>Our team of musicians is here to help you choose the perfect instrument.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <h3>Easy Returns</h3>
                        <p>Not satisfied? Return within 30 days for a full refund or exchange.</p>
                    </div>
                </div>
            </section>

            <!-- Categories Section -->
            <section class="container" id="categories">
                <h2 class="section-title">Shop By Category</h2>
                <div class="categories">
                    <div class="category-card">
                        <img src=img/three.jpg alt="Guitars">
                        <div class="category-overlay">
                            <h3>Guitars</h3>
                        </div>
                    </div>
                    <div class="category-card">
                        <img src=img/one.jpg alt="Keyboards">
                        <div class="category-overlay">
                            <h3>Keyboards</h3>
                        </div>
                    </div>
                    <div class="category-card">
                        <img src=img/five.jpg alt="Drums">
                        <div class="category-overlay">
                            <h3>Drums</h3>
                        </div>
                    </div>
                    <div class="category-card">
                        <img src=img/six.jpg alt="Accessories">
                        <div class="category-overlay">
                            <h3>Accessories</h3>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
    <section class="container" id="contact">
        <h2 class="section-title">Get In Touch</h2>
        <div class="contact-content">
        <div class="contact-info">
            <h3>Contact Information</h3>
            <p><i class="fas fa-map-marker-alt"></i> 123 Music Street, Harmony Square, Mumbai, India</p>
            <p><i class="fas fa-phone"></i> +91 98765 43210</p>
            <p><i class="fas fa-envelope"></i> info@symphony.com</p>
            <p><i class="fas fa-clock"></i> Monday - Saturday: 10:00 AM - 8:00 PM</p>
            <p><i class="fas fa-clock"></i> Sunday: 12:00 PM - 6:00 PM</p>
            
            <h3 style="margin-top: 30px;">Visit Our Store</h3>
            <p>Experience our instruments firsthand at our flagship store in Mumbai. Our expert staff will help you find the perfect instrument for your needs.</p>
        </div>
        <div class="contact-form">
            <h3 style="color: var(--accent-2); margin-bottom: 20px;">Send Us a Message</h3>
            <form id="contactForm">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Send Message</button>
            </form>
        </div>
        </div>
    </section>

            <!-- Testimonials Section -->
            <section class="container">
                <h2 class="section-title">What Our Customers Say</h2>
                <div class="testimonials">
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "The quality of the guitar I purchased exceeded my expectations. The sound is rich and the craftsmanship is exceptional. Highly recommended!"
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">RK</div>
                            <div class="author-info">
                                <h4>Rahul Kumar</h4>
                                <p>Professional Musician</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "As a beginner, I was overwhelmed with choices. The Symphony team helped me find the perfect keyboard for my needs. Great service!"
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">PS</div>
                            <div class="author-info">
                                <h4>Priya Sharma</h4>
                                <p>Music Student</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-text">
                            "Fast delivery and excellent packaging. The drum set arrived in perfect condition. Will definitely shop here again for my musical needs."
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">AM</div>
                            <div class="author-info">
                                <h4>Anil Mehta</h4>
                                <p>Band Director</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Newsletter Section -->
            <section class="container">
                <div class="newsletter">
                    <h3>Stay in Tune with Our Latest</h3>
                    <p>Subscribe to our newsletter for exclusive deals, new arrivals, and musical inspiration delivered to your inbox.</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Your email address" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </section>
        </main>

        <footer>
            <div class="footer-content">
                <div class="footer-column">
                    <h4>Symphony</h4>
                    <p style="color: rgba(255, 255, 255, 0.8);">Your trusted partner for premium musical instruments since 2010.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h4>Shop</h4>
                    <ul>
                        <li><a href="#">Guitars</a></li>
                        <li><a href="#">Keyboards</a></li>
                        <li><a href="#">Drums</a></li>
                        <li><a href="#">Accessories</a></li>
                        <li><a href="#">Sheet Music</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Returns & Exchanges</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Repair Services</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Contact</h4>
                    <ul>
                        <li><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> 123 Music Street, Mumbai</li>
                        <li><i class="fas fa-phone" style="margin-right: 8px;"></i> +91 98765 43210</li>
                        <li><i class="fas fa-envelope" style="margin-right: 8px;"></i> info@symphony.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                © <span id="year"></span> Symphony Musical Instruments. All rights reserved.
            </div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('year').textContent = new Date().getFullYear();
                
                // Smooth scrolling for anchor links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href');
                        if(targetId === '#') return;
                        
                        const targetElement = document.querySelector(targetId);
                        if(targetElement) {
                            window.scrollTo({
                                top: targetElement.offsetTop - 100,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
                / Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if(targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if(targetElement) {
            window.scrollTo({
            top: targetElement.offsetTop - 100,
            behavior: 'smooth'
            });
        }
        });
    });
    
    // Contact form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Thank you for your message! We will get back to you soon.');
        this.reset();
    });
    });
        
        </script>
    </body>
    </html>