<?php
session_start();
require 'classes/config.php';
require 'classes/User.php';
require 'classes/Validation.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$emailErr = $passwordErr = "";
$errors = false;
// Check when form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate password
    if (!Validation::validatePasswordField($_POST['password'], $passwordErr)) {
        $errors = true;
    } else {
        $user->password = $_POST['password'];
    }

    // Validate email
    if (!Validation::validateEmail($_POST['email'], $emailErr)) {
        $errors = true;
    } else {
        $user->email = $_POST['email'];
    }

    if (!$errors) {
        if ($user->login()) {
            // Successful login
            if ($user->status == '0') { // Check if the user is blocked
                $_SESSION['error_message'] = "Your account was temporarily blocked by admin.";
                header("Location: login.php?status=blocked");
                exit();
            }
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;
            $_SESSION['status'] = $user->status;

            switch ($_SESSION['role']) {
                case "admin":
                    header("Location: admin/dashboard.php");
                    exit();
                case "farm":
                    header("Location: farm/dashboard.php");
                    exit();
                case "customer":
                    header("Location: marketplace.php");
                    exit();
                default:

                    header("Location: login.php");
                    exit();
            }
        } else {
            // Invalid email or password
            $_SESSION['error_message'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="footer.css">
        <link rel="stylesheet" href="header.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                background-image: url('images/img6.jpg');
                background-repeat: no-repeat;
                background-size: cover;
                position: relative;
                min-height: 100vh;
            }

            /* Background overlay */
            body::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1;
            }

            .card {
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                background-color: rgba(255, 255, 255, 0.5);
                position: relative;
                z-index: 2; /* Keep the card background behind the overlay */
            }

            .card-header,
            .form-control,
            .btn-primary,
            .alert,
            label,
            input {
                position: relative;
                z-index: 4; /* Bring these elements to the front */
            }

            .card-header {
                background-color: #40826D;
                color: white;
                border-radius: 10px 10px 0 0;
                font-weight: bold;
                text-align: center;
            }

            .form-control {
                background-color: lightcyan;
                border-radius: 5px;
            }

            .btn-primary {
                background-color: #40826D;
                border-color: #40826D;
                width: 100%;
            }

            .btn-primary:hover {
                background-color: #306A53;
                border-color: #306A53;
            }

            .alert {
                text-align: center;
            }

            label {
                color: #333;
            }

            .navbar,
            footer {
                position: relative;
                z-index: 5;
            }

            @media screen and (max-width: 576px) {
                .card-header {
                    font-size: 1.2rem;
                }
            }

        </style>
    </head>

    <body>

        <?php include 'includes/header.php'; ?>

        <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
            <div class="row w-100">
                <div class=" d-flex justify-content-center">
                    <div class="card col-lg-4 col-md-6 col-sm-8 col-12">
                        <div class="card-header">
                            <h4>Log In</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['error_message'])) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $_SESSION['error_message']; ?>
                                </div>
                                <?php unset($_SESSION['error_message']); ?>
                            <?php endif; ?>

                            <form id="loginForm" action="login.php" method="post">
                                <div class="mb-3">
                                    <label>Email:<span style="color: red;"><?php echo $emailErr ?></span></label>
                                    <input type="text" class="form-control" name="email" placeholder="Enter your email" required>
                                </div>
                                <div class="mb-3">
                                    <label>Password:<span style="color: red;"><?php echo $passwordErr ?></span></label>
                                    <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                                </div>
                                <div class="d-grid gap-2">
                                    <input type="submit" class="btn btn-primary" name="submit" value="Log In">
                                </div>
                            </form>

                            <div class="mt-3 text-center">
                                <small>Don't have an account? <a href="register.php" class="text-primary" style="font-weight: bold;">Sign Up</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

    </body>

</html>

