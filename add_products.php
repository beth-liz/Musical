<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Require admin authentication instead of regular auth
requireAdmin();
require_once 'config/cart_functions.php';
$cart_count = getCartItemCount($_SESSION['user_id']);
$success_message = "";
$error_message = "";

// Handle product submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    
    // Handle file upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = 'uploads/products/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Please upload only JPG or JPEG images.";
        }
    }
    
    if (empty($error_message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (title, description, price, image_path, stock_quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $price, $image_path, $stock_quantity]);
            $success_message = "Product added successfully!";
        } catch(PDOException $e) {
            $error_message = "Error adding product: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Musical Instruments</title>
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

        /* Form Styling */
        .form-card{
            max-width:650px; margin:auto; 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            padding:40px; border-radius:var(--radius); box-shadow:var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        input,textarea,select{
            width:100%; padding:12px; border:1px solid #e2e8f0;
            border-radius:6px; font-size:1rem;
            transition: border-color 0.2s;
        }
        input:focus,textarea:focus,select:focus{border-color:var(--accent); outline:none; box-shadow: 0 0 0 3px rgba(178, 111, 48, 0.1);}
        form label{display:block; margin:18px 0 8px; font-weight:500; color:var(--accent-2);}
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
        h2{text-align:center; font-size:2.2rem; margin-bottom:50px; color: white; font-weight:700; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);}
        #addResult{text-align:center;margin-top:20px;color:var(--muted);}

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
        }
        @media(max-width:600px){
            .nav-main{flex-wrap: wrap;} 
            .nav-main a{margin: 5px 10px; font-size: 0.9rem;} 
            .form-card{padding: 20px;}
        }
    </style>
</head>
<body>
    <header>
  <div class="nav-row">
    <a href="#" class="brand">Symphony</a>
    <!-- Main Navigation Wrapper (Centered) -->
    <nav class="nav-main">
      <a href="mus_home.php" class="nav ">Home</a>
      <a href="shop.php" class="nav">Shop</a>
      <a href="#about" class="nav">About</a>
      <a href="#contact" class="nav">Contact</a>
      <?php if(isAdmin()): ?>
          <a href="add_products.php" class="nav active">Add Product</a>
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
        <section>
            <div class="container">
                <h2>Add a New Product Listing</h2>
                
                <?php if($success_message): ?>
                    <div style="background: #c6f6d5; color: #22543d; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($error_message): ?>
                    <div style="background: #fed7d7; color: #c53030; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Add Product Form -->
                <div class="form-card" style="max-width: 600px; margin: 0 auto;">
                    <h3 style="margin-top: 0; color: var(--accent-2); text-align: center;">Add New Product</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <label>Product Title</label>
                        <input name="title" required placeholder="Enter product title">
                        
                        <label>Description</label>
                        <textarea name="description" rows="3" placeholder="Product description..."></textarea>
                        
                        <label>Price (₹)</label>
                        <input name="price" type="number" min="0" step="0.01" required placeholder="0.00">
                        
                        <label>Product Image (JPG/JPEG only)</label>
                        <input name="image" type="file" accept=".jpg,.jpeg" style="padding: 8px; border: 2px solid #e2e8f0; border-radius: 6px; background: white;">
                        <small style="color: var(--muted); font-size: 0.8rem;">Only JPG and JPEG files are allowed</small>
                        
                        <label>Stock Quantity</label>
                        <input name="stock_quantity" type="number" min="0" value="0" placeholder="0">
                        
                        <button class="btn" type="submit" style="width: 100%; margin-top: 20px;">Add Product</button>
                    </form>
                </div>
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