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
  position: relative; /* Needed for absolute centering */
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
  /* Centering the main navigation items */
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

/* Cart specific styling (Far Right) */
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

/* Responsive adjustments */
@media(max-width:1000px){
  .nav-main{position: static; transform: none; width: 100%; justify-content: space-around; margin-top: 10px; order: 2;} 
  .nav-row{flex-direction: column; align-items: flex-start;} 
  .brand{width: 100%; text-align: center; order: 1;} 
  .cart-link{order: 1; position: absolute; right: 20px;}
  .auth-links{order: 1; margin-left: 0; margin-top: 10px; width: 100%; justify-content: center;}
}
@media(max-width:900px){.hero{flex-direction:column-reverse;text-align:center;}.hero img{max-width:100%;}}
@media(max-width:600px){.nav-main{flex-wrap: wrap;} .nav-main a{margin: 5px 10px; font-size: 0.9rem;} .hero-text h1{font-size: 2.2rem;} .feature-grid{grid-template-columns: 1fr;}}

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
    <?php if(isAdmin()): ?>
        <a href="add_products.php" class="nav">Add Product</a>
    <?php endif; ?>
</nav>
    <!-- Cart Link (Right Corner) -->
    <a href="cart.php" class="cart-link">
    <span class="icon">🛒</span> Cart (<span id="cart-count"><?php echo $cart_count; ?></span>)
</a>
    <div class="auth-links">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</span>
        <a href="logout.php">Logout</a>
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
</main>

<footer>© <span id="year"></span> Musical Instruments. All rights reserved.</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('year').textContent = new Date().getFullYear();
});
</script>
</body>
</html>