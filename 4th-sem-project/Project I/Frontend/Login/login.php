<?php
session_start();
$errors = isset($_SESSION['login_errors']) ? $_SESSION['login_errors'] : array();
$old = isset($_SESSION['login_old']) ? $_SESSION['login_old'] : array();
$activeForm = isset($_SESSION['login_active_form']) ? $_SESSION['login_active_form'] : '';
unset($_SESSION['login_errors'], $_SESSION['login_old'], $_SESSION['login_active_form']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farm2Door</title>
    <link rel="stylesheet" href="login.css">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
</head>
<body>
    <!-- Navbar -->
    <header>
        <nav class="navbar">
            <div class="hamburger" id="hamburger">
                <i class="fa fa-bars"></i>
            </div>
            <div class="logo">
                <img src="../../Images/logo.png" alt="Farm2Door Logo" />
            </div>
            <ul class="nav-links">
                <li><a href="../Home/index.html">Home</a></li>
                <li><a href="../Product/product.html">Products</a></li>
                <li><a href="../About Us/aboutus.html">About Us</a></li>
                <li><a href="../Contact Us/contactus.html">Contact Us</a></li>
                <li><a href="login.php" class="active">Login</a></li>
                <li><a href="../Registration/index.html ">Sign Up</a></li>
            </ul>
        </nav>
    </header>

    <!-- Login Section -->
    
  <section class="login-section">
    <span class="close" onclick="closeModal()">&times;</span>
        <div class="login-container">
            <div class="login-header">
                <h1>Welcome to Farm2Door</h1>
                <p>Choose your login type</p>
            </div>
            
            <div class="login-options">
                <div class="login-card farmer-login" onclick="showLoginForm('farmer')">
                    <div class="login-icon">
                        <i class="fa-solid fa-tractor"></i>
                    </div>
                    <h3>Farmer Login</h3>
                    <p>Access your farmer dashboard to manage products and orders</p>
                </div>
                
                <div class="login-card consumer-login" onclick="showLoginForm('consumer')">
                    <div class="login-icon">
                        <i class="fa-solid fa-shopping-cart"></i>
                    </div>
                    <h3>Consumer Login</h3>
                    <p>Access your account to browse and order fresh produce</p>
                </div>

                <div class="login-card admin-login" onclick="showLoginForm('admin')">
                    <div class="login-icon">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h3>Admin Login</h3>
                    <p>Access your account to browse and order fresh produce</p>
                </div>
            </div>

            <!-- Login Forms -->
            <div class="login-forms">
                <div id="farmer-form" class="login-form" style="display: none;">
                    <h2>Farmer Login</h2>
                    <form action="../../Backend/farmer_login.php" method="POST">
                        <div class="form-group">
                            <label for="farmer-email">Email Address</label>
                            <input type="email" id="farmer-email" name="farmer-email" placeholder="Enter your email" required>
                            <p class="error" id="e1"></p>
                        </div>
                        <div class="form-group">
                            <label for="farmer-password">Password</label>
                            <input type="password" name="farmer-password" id="farmer-password" placeholder="Enter your password" required>
                            <p class="error" id="e2"></p>
                        </div>
                        <input type="checkbox" name="f-chk" class="chk" id="f-chk"><label style="color: green; margin-left: 4px;">Remember me</label>
                        <button type="submit" class="login-btn">Login as Farmer</button>
                        <p class="signup-link">Don't have an account? <a href="../Registration/farmer-register.html">Register as Farmer</a></p>
                        <p class="success" id="farmer-success"></p>
                    </form>
                </div>

                <div id="consumer-form" class="login-form" style="display: none;">
                    <h2>Consumer Login</h2>
                    <form action="../../Backend/consumer_login.php"  method="POST">
                        <div class="form-group">
                            <label for="consumer-email">Email Address</label>
                            <input type="email" id="consumer-email" name="consumer-email"  placeholder="Enter your email" value="<?php echo htmlspecialchars($old['consumer-email'] ?? ''); ?>">
                            <p class="error" id="e3"> <?php echo isset($errors['email']) ? $errors['email'] : ''; ?> </p>
                        </div>
                        <div class="form-group">
                            <label for="consumer-password">Password</label>
                            <input type="password" id="consumer-password" name="consumer-password" placeholder="Enter your password">
                            <p class="error" id="e4"> <?php echo isset($errors['password']) ? $errors['password'] : ''; ?> </p>
                        </div>
                        <input type="checkbox" name="c-chk" class="chk" id="c-chk"><label style="color: green; margin-left: 4px;">Remember me</label>
                        <button type="submit" class="login-btn">Login as Consumer</button>
                        <p class="error" style="color:red;"> <?php echo isset($errors['result']) ? $errors['result'] : ''; ?> </p>
                        <p class="signup-link">Don't have an account? <a href="../../Backend/consumer-registration.php">Register as Consumer</a></p>
                         <p class="success" ></p>
                    </form>
                </div>

                <div id="admin-form" class="login-form" style="display: none;">
                    <h2>Admin Login</h2>
                    <form action="../../Backend/admin_login.php" method="POST">
                        <div class="form-group">
                            <label for="admin-email">Email Address</label>
                            <input type="email" id="admin-email" name="admin-email" placeholder="Enter your email" required>
                            <p class="error" id="e5"></p>
                        </div>
                        <div class="form-group">
                            <label for="admin-password">Password</label>
                            <input type="password" id="admin-password"  name="admin-password" placeholder="Enter your password" required>
                            <p class="error" id="e6"></p>
                        </div>
                        <input type="checkbox" name="a-chk" class="chk"  id="a-chk"><label style="color: green; margin-left: 4px;">Remember me</label>
                        <button type="submit" class="login-btn">Login as Admin</button>
                         <p class="success" id="admin-success"></p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Farm2Door. All rights reserved.</p>
        <div class="socials">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
        </div>
    </footer>

    <script src="login.js"></script>
    <script>
      (function() {
        var active = <?php echo json_encode($activeForm); ?>;
        var hasErrors = <?php echo json_encode(!empty($errors)); ?>;
        if (hasErrors || active === 'consumer') {
          if (typeof showLoginForm === 'function') {
            showLoginForm('consumer');
          } else {
            // Fallback if script loads later
            window.addEventListener('load', function(){ showLoginForm('consumer'); });
          }
        }
      })();
    </script>
</body>
</html>
