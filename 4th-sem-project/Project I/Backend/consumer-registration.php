<?php
include 'connection.php';
include 'validation.php';


// Main Processing
$errors = array();
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize inputs
    $firstName = trim($_POST['first-name']);
    $lastName = trim($_POST['last-name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);
    $terms = isset($_POST['terms']) ? $_POST['terms'] : '';

    // Validate all fields
    $isFormValid = true;

    $isFormValid = validateName($firstName, $errors, 'firstName') && $isFormValid;
    $isFormValid = validateName($lastName, $errors, 'lastName') && $isFormValid;
    $isFormValid = validateEmail($email, $errors) && $isFormValid;
    $isFormValid = validatePhone($phone, $errors) && $isFormValid;
    $isFormValid = validateAddress($address, $errors) && $isFormValid;
    $isFormValid = validateCity($city, $errors) && $isFormValid;
    $isFormValid = validateProvince($province, $errors) && $isFormValid;
    $isFormValid = validatePassword($password, $errors) && $isFormValid;

    // Validate confirm password
    if (trim($confirmPassword) === "") {
        $errors['confirmPassword'] = "Please confirm your password";
        $isFormValid = false;
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match";
        $isFormValid = false;
    }

    $isFormValid = validateTerms($terms, $errors) && $isFormValid;

    // Check if email already exists
    if ($isFormValid) {
        $checkEmailSql = "SELECT * FROM `customer_registration` WHERE email = ?";
        $checkStmt = mysqli_prepare($conn, $checkEmailSql);
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {
            $errors['email'] = "This email is already registered!";
            $isFormValid = false;
        }
        mysqli_stmt_close($checkStmt);
    }

    // If all validations pass, insert into database
    if ($isFormValid) {

        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Convert terms to boolean (1 for true, 0 for false)
        $termsAccepted = 1;

        // Prepare INSERT query
        $sql = "INSERT INTO `customer_registration` 
                (firstName, lastName, Email, Phone, Address, City, Province, Password, Terms) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Bind parameters (s = string, i = integer)
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssi",
                $firstName,
                $lastName,
                $email,
                $phone,
                $address,
                $city,
                $province,
                $hashedPassword,
                $termsAccepted
            );

            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                $successMessage = "Registration successful! Welcome to Farm2Door!";



                header('Location: ../Frontend/Login/login.php');
                exit();
            } else {
                $errors['database'] = "Registration failed: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            $errors['database'] = "Database error: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Registration - Farm2Door</title>
    <link rel="stylesheet" href="registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
</head>

<body>
    <!-- Navbar -->
    <header>
        <nav class="navbar">
            <div class="hamburger" id="hamburger">
                <i class="fa fa-bars"></i>
            </div>
            <div class="logo">
                <img src="../Images/logo.png" alt="Farm2Door Logo" />
            </div>
            <ul class="nav-links">
                <li><a href="../Home/index.html">Home</a></li>
                <li><a href="../Product/product.html">Products</a></li>
                <li><a href="../About Us/aboutus.html">About Us</a></li>
                <li><a href="../Contact Us/contactus.html">Contact Us</a></li>
                <li><a href="../Login/login.html">Login</a></li>
                <li><a href="../Registration/index.html " class="active">Sign Up</a></li>
            </ul>
        </nav>
    </header>

    <!-- Registration Section -->
    <section class="registration-section">
        <div class="registration-container">
            <div class="registration-header">
                <h1>Join Farm2Door as a Consumer</h1>
                <p>Create your account to start ordering fresh vegetables</p>
            </div>

            <div class="registration-form-container">
                <div class="registration-form">
                    <h2>Consumer Registration</h2>
                    <form id="consumer-registration-form" action="" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="consumer-first-name">First Name</label>
                                <input type="text" id="consumer-first-name" name="first-name"
                                    placeholder="Enter your first name"
                                    value="<?php echo htmlspecialchars($_POST['first-name'] ?? ''); ?>" required>
                                <p class="error" id="first-name-error"> <?php echo $errors['first-name'] ?? ''; ?> </p>
                            </div>
                            <div class="form-group">
                                <label for="consumer-last-name">Last Name</label>
                                <input type="text" id="consumer-last-name" name="last-name"
                                    placeholder="Enter your last name"
                                    value="<?php echo htmlspecialchars($_POST['last-name'] ?? ''); ?>" required>
                                <p class="error" id="last-name-error"> <?php echo $errors['last-name'] ?? ''; ?> </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="consumer-email">Email Address</label>
                            <input type="email" id="consumer-email" name="email" placeholder="Enter your email address"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            <p class="error" id="email-error"> <?php echo $errors['email'] ?? ''; ?> </p>

                        </div>

                        <div class="form-group">
                            <label for="consumer-phone">Phone Number</label>
                            <input type="tel" id="consumer-phone" name="phone" placeholder="Enter your phone number"
                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                            <p class="error" id="phone-error"> <?php echo $errors['phone'] ?? ''; ?> </p>
                        </div>

                        <div class="form-group">
                            <label for="consumer-address">Address</label>
                            <textarea name="address" rows="3"
                                required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>

                            <p class="error" id="address-error"> <?php echo $errors['address'] ?? ''; ?> </p>
                        </div>

                        <div class="form-group">
                            <label for="consumer-city">City</label>
                            <input type="text" id="consumer-city" name="city" placeholder="Enter your city"
                                value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
                            <p class="error" id="city-error"> <?php echo $errors['city'] ?? ''; ?> </p>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="province-state">Province</label>
                                <input type="text" id="consumer-province" name="province"
                                    placeholder="Enter your province"
                                    value="<?php echo htmlspecialchars($_POST['province'] ?? ''); ?>" required>
                                <p class="error" id="province-error"> <?php echo $errors['province'] ?? ''; ?> </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="consumer-password">Password</label>
                            <input type="password" id="consumer-password" name="password"
                                placeholder="Create a strong password"
                                value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>" required>
                            <p class="error" id="password-error"> <?php echo $errors['password'] ?? ''; ?> </p>
                        </div>

                        <div class="form-group">
                            <label for="consumer-confirm-password">Confirm Password</label>
                            <input type="password" id="consumer-confirm-password" name="confirm-password"
                                placeholder="Confirm your password"
                                value="<?php echo htmlspecialchars($_POST['confirm-password'] ?? ''); ?>" required>
                            <p class="error" id="confirm-password-error">
                                <?php echo $errors['confirmPassword'] ?? ''; ?>
                            </p>
                        </div>

                        <div class="form-group checkbox-group">
                            <input type="checkbox" name="terms" required <?php if (!empty($_POST['terms']))
                                echo 'checked'; ?>>

                            <label for="consumer-terms">I agree to the <a href="#" class="terms-link">Terms and
                                    Conditions</a></label>
                            <p class="error" id="terms-error"> <?php echo $errors['terms'] ?? ''; ?> </p>
                        </div>

                        <button type="submit" class="register-btn">Register as Consumer</button>
                        <p class="login-link">Already have an account? <a href="../Login/login.html">Login here</a></p>
                        <p class="success" id="registration-success"> <?php
                        echo $successMessage ?? $errors['database'] ?? '';
                        ?> </p>
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

    <script src="registration.js"></script>
</body>

</html>