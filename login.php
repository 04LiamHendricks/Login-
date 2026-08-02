<?php
// ============================================
// File: login.php - Authentication
// ============================================
session_start();
include("includes/DBConn.php");

// Check if redirected from checkout
$checkout_redirect = isset($_GET['redirect']) && $_GET['redirect'] == 'checkout';
if($checkout_redirect) {
    $_SESSION['redirect_to_checkout'] = true;
}

if(isset($_POST['login'])){
    $u = sanitize($_POST['username']);
    $p = md5($_POST['password']);
    $q = mysqli_query($conn, "SELECT * FROM tblUser WHERE username='$u' AND password='$p'");
    
    if(mysqli_num_rows($q) > 0){ 
        $user = mysqli_fetch_assoc($q);
        
        // Check if approved
        if($user['status'] != 'Approved') {
            $error = "Your account is pending approval. Please wait for admin verification.";
        } else {
            $_SESSION['user'] = $u;
            // Check if admin
            if(isset($user['role']) && $user['role'] == 'Admin') {
                $_SESSION['admin'] = true;
            }
            // Redirect to checkout if needed
            if(isset($_SESSION['redirect_to_checkout'])) {
                unset($_SESSION['redirect_to_checkout']);
                header("Location: checkout.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Pastimes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7fc; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin:0; padding:1rem; }
        .login-card { background: white; padding: 2.5rem; border-radius: 32px; width: 380px; max-width:100%; box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1); }
        .logo-text { font-size: 1.5rem; font-weight:800; margin-bottom:0.2rem; background:linear-gradient(135deg,#1e2b3c,#c25b3a); -webkit-background-clip:text; background-clip:text; color:transparent; }
        h2 { margin-bottom: 0.3rem; font-weight: 700; }
        .subtitle { color: #7a8e9e; margin-bottom: 1.5rem; font-size:0.9rem; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 60px; font-family: inherit; }
        button { background: #1e2f3c; color: white; border: none; padding: 12px; border-radius: 60px; width: 100%; font-weight: bold; cursor: pointer; margin-top: 10px; }
        button:hover { background: #e07c4c; }
        .error { color: #d9534f; margin-bottom: 1rem; padding: 0.8rem; background: #fee2e2; border-radius: 12px; }
        a { color: #e07c4c; text-decoration: none; display: block; text-align: center; margin-top: 1rem; }
        .admin-link { text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #eef2f6; font-size:0.8rem; }
        .admin-link a { color: #7a8e9e; }
        .redirect-msg { background: #dbeafe; padding: 0.8rem; border-radius: 16px; margin-bottom:1rem; color:#1e40af; font-size:0.9rem; text-align:center; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo-text">PASTIMES</div>
    <h2>Welcome back</h2>
    <p class="subtitle">Login to continue shopping</p>
    
    <?php if(isset($error)): ?>
        <div class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($checkout_redirect || isset($_SESSION['redirect_to_checkout'])): ?>
        <div class="redirect-msg"><i class="fas fa-shopping-cart"></i> Please login to complete your order</div>
    <?php endif; ?>
    
    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login"><i class="fas fa-arrow-right"></i> Login</button>
    </form>
    <a href="register.php">No account? Join Pastimes →</a>
    
    <div class="admin-link">
        <a href="admin_login.php"><i class="fas fa-shield-alt"></i> Admin Login</a>
    </div>
</div>
</body>
</html>