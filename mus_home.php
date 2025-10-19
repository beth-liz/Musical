<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require authentication
requireAuth();
require_once 'config/cart_functions.php';
$cart_count = getCartItemCount($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Musical Instruments - Dashboard</title>
<!-- Cinzel for headings and a classic touch, Inter for body text -->
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ====== GLOBAL CSS & VARIABLES ====== */
:root{
  --bg: #fbfbfb;
  --card: #ffffff;
  --muted: #6b7280;
  --accent:rgba(178, 111, 48, 0.83); /* Vibrant Orange/Red */
  --accent-hover:rgb(213, 148, 34); /* Slightly darker hover */
  --accent-2: #0f172a; /* Dark Charcoal */
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
  font-family: 'Cinzel', serif;
}
section {
  padding: 60px 20px;
}
.container{
  max-width:var(--maxw);
  margin:auto;
}
.page{
  display:none; /* Hide pages by default */
}
.page.active{
  display:block; /* Show active page */
}

/* === ENHANCED HEADER (Cinzel, No Default Underline, Centered) === */
/* === ENHANCED HEADER (Cinzel, No Default Underline, Centered) === */
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
  gap: 20px; /* Added gap for better spacing */
}
.brand{
  font-weight:700; font-size:1.8rem; text-decoration:none; 
  color: white; 
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0; /* Prevent brand from shrinking */
  margin-right: auto; /* Push brand to left */
}
.nav-main {
  display: flex;
  gap: 30px; /* Reduced gap to fit more items */
  flex-wrap: nowrap;
  justify-content: center;
  flex: 1; /* Take available space */
  max-width: 500px; /* Limit width to prevent overflow */
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
  white-space: nowrap; /* Prevent text wrapping */
  font-size: 0.95rem; /* Slightly smaller font */
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

/* Right side container for cart and auth links */
.header-right {
  display: flex;
  align-items: center;
  gap: 15px;
  flex-shrink: 0; /* Prevent shrinking */
  margin-left: auto; /* Push to right */
}

/* Cart specific styling */
.cart-link{
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: white; 
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 6px;
  transition: all 0.3s ease !important;
  white-space: nowrap;
  font-size: 0.95rem;
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
  align-items: center;
  flex-shrink: 0;
}

.auth-links span {
  color: white;
  font-weight: 500;
  padding: 8px 0;
  white-space: nowrap;
  font-size: 0.95rem;
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
  white-space: nowrap;
  font-size: 0.95rem;
}

/* Auth link hover effects - button style */
.auth-links a:hover {
  background: #c53030 !important;
  color: white !important;
  text-decoration: none !important;
  transform: translateY(-2px) !important;
}

/* Responsive adjustments */
@media(max-width:1200px){
  .nav-main {
    gap: 20px;
  }
  .nav-main a {
    font-size: 0.9rem;
  }
}

@media(max-width:1000px){
  .nav-main{
    position: static; 
    transform: none; 
    width: 100%; 
    justify-content: space-around; 
    margin-top: 10px; 
    order: 2;
    max-width: none;
  } 
  .nav-row{
    flex-direction: column; 
    align-items: stretch;
  } 
  .brand{
    width: 100%; 
    text-align: center; 
    order: 1;
    margin-right: 0;
    justify-content: center;
  } 
  .header-right {
    order: 1;
    width: 100%;
    justify-content: center;
    margin-top: 10px;
    margin-left: 0;
  }
}
@media(max-width:600px){
  .nav-main{
    flex-wrap: wrap; 
    gap: 15px;
  } 
  .nav-main a{
    margin: 0; 
    font-size: 0.85rem;
  } 
  .header-right {
    flex-direction: column;
    gap: 10px;
  }
  .auth-links {
    flex-direction: column;
    gap: 10px;
  }
}

/* === GENERAL STYLES (Kept from previous professional design) === */
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
  box-shadow: 0 4px 15px rgba(255, 122, 89, 0.6); 
  display:inline-block; border:none; cursor:pointer;
  transition: all 0.2s ease;
}
.btn:hover{
  background:var(--accent-hover); 
  box-shadow: 0 6px 18px rgba(255, 122, 89, 0.7);
}
.btn.ghost{
  background:transparent; color:var(--accent); 
  border:2px solid var(--accent);
  box-shadow:none; padding:10px 24px;
}
.btn.ghost:hover{
  background:var(--accent); color:#fff;
  box-shadow: 0 4px 12px rgba(255, 122, 89, 0.4);
}
h2{text-align:center; font-size:2.2rem; margin-bottom:50px; color: white; font-weight:700; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);}
.feature-grid{
  display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
  gap:30px;
}
.feature{
  background: rgb(255, 255, 255); 
  backdrop-filter: blur(10px);
  padding:35px; border-radius:var(--radius);
  box-shadow:var(--shadow); text-align:center;
  border-top: 4px solid var(--accent);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.feature:hover{transform:translateY(-8px); box-shadow: 0 12px 25px rgba(15, 23, 42, 0.15);}
.feature .icon{font-size:40px; margin-bottom:15px; color:var(--accent);}
.feature h3{margin-top:0; font-size:1.35rem; color:var(--accent-2); font-weight:600;}
.feature p{color:var(--muted);}

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

/* Forms/Cart */
input,textarea,select{
  width:100%; padding:12px; border:1px solid #e2e8f0;
  border-radius:6px; font-size:1rem;
  transition: border-color 0.2s;
}
input:focus,textarea:focus,select:focus{border-color:var(--accent); outline:none; box-shadow: 0 0 0 3px rgba(255, 122, 89, 0.1);}
form label{display:block; margin:18px 0 8px; font-weight:500; color:var(--accent-2);}
.form-card{
  max-width:650px; margin:auto; 
  background: rgba(255, 255, 255, 0.95); 
  backdrop-filter: blur(10px);
  padding:40px; border-radius:var(--radius); box-shadow:var(--shadow);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.cart-item{
  display:flex; align-items:center; gap:25px; 
  border-bottom:1px solid #e2e8f0; padding:15px 0;
}
.cart-item img{width:90px; height:70px; object-fit:cover; border-radius:8px;}
#cart-total{font-size:1.5rem; margin-top:30px; padding-top:15px; border-top:3px solid var(--accent); color:var(--accent-2);}

/* Footer */
footer{
  border-top:1px solid rgba(255, 255, 255, 0.2); text-align:center; padding:35px;
  color: rgba(255, 255, 255, 0.8); font-size:1rem; 
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  margin-top: 50px;
}

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
@media(max-width:1200px){
  .nav-main {
    gap: 25px;
  }
}

@media(max-width:1000px){
  .nav-main{position: static; transform: none; width: 100%; justify-content: space-around; margin-top: 10px; order: 2;} 
  .nav-row{flex-direction: column; align-items: flex-start;} 
  .brand{width: 100%; text-align: center; order: 1;} 
  .cart-link{order: 1; position: absolute; right: 20px; top: 18px;}
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
@media(max-width:600px){
  .nav-main{flex-wrap: wrap;} 
  .nav-main a{margin: 5px 10px; font-size: 0.9rem;} 
  .hero-text h1{font-size: 2.2rem;} 
  .feature-grid{grid-template-columns: 1fr;} 
  .section-title {font-size: 1.8rem;}
  .cart-link {
    position: static;
    margin-right: 0;
    justify-content: center;
    width: 100%;
    margin-top: 10px;
  }
}
</style>
</head>
<body>

<header>
  <div class="nav-row">
    <a href="#" class="brand">Symphony</a>
    <!-- Main Navigation Wrapper (Centered) -->
    <nav class="nav-main">
      <a href="mus_home.php" class="nav active">Home</a>
      <a href="shop.php" class="nav">Shop</a>
      <a href="#about" class="nav">About</a>
      <a href="#contact" class="nav">Contact</a>
      <?php if(isAdmin()): ?>
          <a href="add_products.php" class="nav">Add Product</a>
      <?php endif; ?>
    </nav>
    <!-- Right side container for cart and auth -->
    <div class="header-right">
      <!-- Cart Link -->
      <a href="cart.php" class="cart-link">
        <span class="icon">🛒</span> Cart (<span id="cart-count"><?php echo $cart_count; ?></span>)
      </a>
      <!-- Auth Links -->
      <div class="auth-links">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
        <a href="logout.php">Logout</a>
      </div>
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
    <h2>Experience the Musical Difference</h2>
    <div class="feature-grid">
      <div class="feature"><div class="icon">🎶</div><h3>Authentic Quality</h3><p>Hand-picked instruments from trusted global manufacturers.</p></div>
      <div class="feature"><div class="icon">📦</div><h3>Reliable Shipping</h3><p>Secure, fast delivery right to your door, worldwide.</p></div>
      <div class="feature"><div class="icon">🤝</div><h3>Expert Support</h3><p>Our team of musicians is here to help you find your sound.</p></div>
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
        <img src="img/12.jpg" alt="Symphony Music Studio">
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
</main>

<footer>© <span id="year"></span> Musical Instruments. All rights reserved.</footer>

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