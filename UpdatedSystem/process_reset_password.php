<?php
session_start();
require 'classes/config.php';
require 'classes/Validation.php';

$database = new Database();
$conn = $database->getConnection();
$errors = false;

if (isset($_POST['token']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
    $token = $_POST['token'];

    // Validate password using the Validation class
    if (!Validation::validatePasswordFieldLog($_POST['password'], $passwordErr)) {
        $errors = true;
        $_SESSION['error_message'] = $passwordErr;
    } else {
        $password = $_POST['password'];
    }
    $confirmPassword = $_POST['confirm_password'];

    // Check if the passwords match
    if ($password !== $confirmPassword) {
        $errors = true;
        $_SESSION['error_message'] = "Passwords do not match.";
    }

    // If no validation errors, proceed
    if (!$errors) {
        // Fetch the token and expiration from the users table
        $stmt = $conn->prepare("SELECT * FROM user WHERE reset_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && $user['token_expires'] >= date("U")) {
            // Token is valid and not expired
            $email = $user['email'];

            // Hash the new password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Update the user's password and clear the token and expiration
            $stmt = $conn->prepare("UPDATE user SET password = :password, reset_token = NULL, token_expires = NULL WHERE email = :email");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            echo "<script>alert('Your password has been successfully reset.'); window.location.href='login.php';</script>";
            exit(); // Stop further execution after redirecting
        } else {
            $_SESSION['error_message'] = "Invalid or expired token. Please try again.";
            header("Location: process_reset_password.php");
            exit();
        }
    } else {
        // If errors exist, redirect to the form page
        header("Location: process_reset_password.php?token=" . urlencode($token));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="footer.css">
        <link rel="stylesheet" href="header.css">
        <style>
            /* Same CSS styles as provided */
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
                z-index: 2;
            }

            .card-header,
            .form-control,
            .btn-primary,
            .alert,
            label,
            input {
                position: relative;
                z-index: 4;
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
                <div class="d-flex justify-content-center">
                    <div class="card col-lg-4 col-md-6 col-sm-8 col-12">
                        <div class="card-header">
                            <h4>Reset Your Password</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['token'])) : ?>
                                <form action="process_reset_password.php" method="POST">
                                    <?php if (isset($_SESSION['error_message'])) : ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php echo $_SESSION['error_message']; ?>
                                            <?php unset($_SESSION['error_message']); // Clear the error message after displaying ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                                    </div>

                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">Reset Password</button>
                                    </div>
                                </form>
                            <?php else : ?>
                                <p class="text-danger">Invalid token.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
