<?php
session_start();
include 'connection.php';

$errors = array();
$old = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['consumer-email']) ? trim($_POST['consumer-email']) : '';
    $password = isset($_POST['consumer-password']) ? trim($_POST['consumer-password']) : '';

    $old['consumer-email'] = $email;

    if ($email === '') {
        $errors['email'] = "Email cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    }

    if ($password === '') {
        $errors['password'] = "Password cannot be empty!";
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM `customer_registration` WHERE Email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            $errors['result'] = "Database error: failed to prepare statement.";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['Password'])) {
                    $_SESSION['consumer_email'] = $user['Email'];

                    if (!empty($_POST['c-chk'])) {
                        $_SESSION['remember_email'] = $email;
                    } else {
                        unset($_SESSION['remember_email']);
                    }

                    mysqli_close($conn);
                    ?>
                    <script>
                        alert("Logged In successfully");
                    </script>
                    <?php
                    header('Location: ../Frontend/Home/index.html');
                    exit();
                } else {
                    $errors['result'] = "Invalid email or password!";
                }
            } else {
                $errors['result'] = "Invalid email or password!";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        $_SESSION['login_old'] = $old;
        $_SESSION['login_active_form'] = 'consumer';
        mysqli_close($conn);
        header('Location: ../Frontend/Login/login.php');
        exit();
    }
}

mysqli_close($conn);
header('Location: ../Frontend/Login/login.php');
exit();
?>