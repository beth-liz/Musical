<?php
require_once 'config/auth.php';
require_once 'config/database.php';

// Check if user is already logged in
requireGuest();

$errors = [];
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Validation
    if (empty($name)) {
        $errors["name"] = "Name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors["name"] = "Name can only contain letters and spaces.";
    }
    
    if (empty($email)) {
        $errors["email"] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Please enter a valid email address.";
    }
    
    if (empty($password)) {
        $errors["password"] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors["password"] = "Password must be at least 8 characters long.";
    }
    
    if ($password !== $confirm_password) {
        $errors["confirm_password"] = "Passwords do not match.";
    }
    
    // If no errors, create user account
    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors["email"] = "Email already exists.";
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password]);
                
                $success_message = "Account created successfully! You can now sign in.";
            }
        } catch(PDOException $e) {
            $errors["general"] = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Musical Instruments</title>
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

        /* Auth links as buttons */
        .auth-links {
            display: flex;
            gap: 15px;
            margin-left: 20px;
        }

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

        .main-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-2);
            margin-bottom: 10px;
        }

        .subtitle {
            color: #718096;
            font-size: 1rem;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--accent-2);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            background: white;
            box-shadow: 0 0 0 3px rgba(178, 111, 48, 0.1);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.8rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .success-message {
            background: #c6f6d5;
            color: #22543d;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
            border: 1px solid #9ae6b4;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(178, 111, 48, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 20px;
            color: #a0aec0;
            font-size: 0.9rem;
        }

        .signin-link {
            text-align: center;
            margin-top: 30px;
        }

        .signin-link p {
            color: #718096;
            font-size: 0.9rem;
        }

        .signin-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signin-link a:hover {
            color: var(--accent-hover);
        }

        .password-strength {
            margin-top: 5px;
            font-size: 0.8rem;
        }

        .strength-weak { color: #e53e3e; }
        .strength-medium { color: #dd6b20; }
        .strength-strong { color: #38a169; }

        footer {
            border-top:1px solid rgba(255, 255, 255, 0.2); 
            text-align:center; padding:35px;
            color: rgba(255, 255, 255, 0.8); 
            font-size:1rem; 
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            margin-top: 50px;
        }

        @media(max-width:1000px){
            .nav-main{position: static; transform: none; width: 100%; justify-content: space-around; margin-top: 10px; order: 2;} 
            .nav-row{flex-direction: column; align-items: flex-start;} 
            .brand{width: 100%; text-align: center; order: 1;} 
            .auth-links{order: 1; margin-left: 0; margin-top: 10px; width: 100%; justify-content: center;}
        }
        @media(max-width:600px){
            .nav-main{flex-wrap: wrap;} 
            .nav-main a{margin: 5px 10px; font-size: 0.9rem;} 
            .container{padding: 30px 20px; margin: 10px;}
            .logo{font-size: 2rem;}
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-row">
            <a href="home.php" class="brand">Symphony</a>
            <nav class="nav-main">
                <a href="home.php" class="nav active">Home</a>
                <a href="home.php" class="nav">Shop</a>
            </nav>
            <div class="auth-links">
                <a href="signin.php">Sign In</a>
                <a href="signup.php" class="signup">Sign Up</a>
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="container">
            <div class="header">
                <div class="logo">Symphony</div>
                <p class="subtitle">Create your account</p>
            </div>

            <?php if($success_message): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" autocomplete="name">
                    </div>
                    <?php if(isset($errors['name'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['name']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" autocomplete="email">
                    </div>
                    <?php if(isset($errors['email'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['email']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required oninput="checkPasswordStrength()" autocomplete="new-password">
                    </div>
                    <div id="password-strength" class="password-strength"></div>
                    <?php if(isset($errors['password'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['password']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required oninput="checkPasswordMatch()" autocomplete="new-password">
                    </div>
                    <div id="password-match" class="password-strength"></div>
                    <?php if(isset($errors['confirm_password'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errors['confirm_password']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="signin-link">
                <p>Already have an account? <a href="signin.php">Sign in here</a></p>
            </div>
        </div>
    </div>

    <footer>© <span id="year"></span> Musical Instruments. All rights reserved.</footer>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                return;
            }
            
            let strength = 0;
            let message = '';
            let className = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            if (strength < 2) {
                message = 'Weak password';
                className = 'strength-weak';
            } else if (strength < 4) {
                message = 'Medium strength';
                className = 'strength-medium';
            } else {
                message = 'Strong password';
                className = 'strength-strong';
            }
            
            strengthDiv.innerHTML = `<i class="fas fa-shield-alt"></i> ${message}`;
            strengthDiv.className = `password-strength ${className}`;
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('password-match');
            
            if (confirmPassword.length === 0) {
                matchDiv.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<i class="fas fa-check"></i> Passwords match';
                matchDiv.className = 'password-strength strength-strong';
            } else {
                matchDiv.innerHTML = '<i class="fas fa-times"></i> Passwords do not match';
                matchDiv.className = 'password-strength strength-weak';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('year').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>