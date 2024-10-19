<!-- forget_password.php -->
<?php
$emailErr = "";
$errors = false;
session_start(); // Ensure session is started for error/success messages
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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
                        <h4>Forgot Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error_message'])) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['error_message']; ?>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php elseif (isset($_SESSION['success_message'])) : ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $_SESSION['success_message']; ?>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>

                        <form id="loginForm" action="send_reset_link.php" method="POST">
                            <div class="mb-3">
                                <label>Enter your email to reset password:</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                                <small id="emailError" class="text-danger"><?php echo $emailErr; ?></small>
                            </div>

                            <div class="d-grid gap-2">
                                <input type="submit" class="btn btn-primary" name="submit" value="Send Reset Link">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Email validation
        document.getElementById('email').addEventListener('input', function() {
            const emailInput = this;
            const emailError = document.getElementById('emailError');
            const emailValue = emailInput.value;
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (emailValue === "") {
                emailError.textContent = "Email is required.";
                emailInput.classList.add('is-invalid');
            } else if (!emailRegex.test(emailValue)) {
                emailError.textContent = "Invalid email format.";
                emailInput.classList.add('is-invalid');
            } else {
                emailError.textContent = "";
                emailInput.classList.remove('is-invalid');
            }
        });
    </script>

</body>

</html>
