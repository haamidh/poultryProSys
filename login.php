<?php
session_start();
require 'config.php';
require 'User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// Check when form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    if ($user->login()) {
        // Successful login
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role;

        switch ($user->role) {
            case "admin":
                header("Location: admin.php");
                exit();
            case "farm":
                header("Location: farm_dashboard.php?user_id=" . $user->user_id);
                exit();
            case "customer":
                header("Location: customer.php");
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
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Log in form</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>
    <body>

        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #40826D;">
            <a class="navbar-brand mx-5" href="platformUI.html" style="font-weight: bold">
                <img src="/docs/4.0/assets/brand/bootstrap-solid.svg" width="30" height="30" class="d-inline-block align-top" alt="">
                PoultryPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                <ul class="navbar-nav align-items-center">
                    <div class="justify-content-center mt-2" style="color:white;">You don't have an account?</div>
                    <li class="nav-item align-items-center mx-4" style="background-color:#8A9A5B;">
                        <a class="nav-link" href="register.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
            <div class="row">
                <div class="col-md-4">
                    <div class="card" style="width:450px;">
                        <div class="card-header" style="background-color: #40826D;color: white;">
                            <h4>Log In</h4>
                        </div>
                        <div class="card-body">

                            <?php if (isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $_SESSION['error_message']; ?>
                                </div>
                                <?php unset($_SESSION['error_message']); ?> 
                            <?php endif; ?>

                            <form id="batchForm" action="login.php" method="post">
                                <div class="row">
                                    <label>Email:</label>
                                    <input type="text" class="form-control" name="email" placeholder="abc123@gmail.com" required="">
                                </div>
                                <br>
                                <div class="row">
                                    <label>Password:</label>
                                    <input type="password" class="form-control" name="password" required="">
                                </div>
                                <br>
                                <input type="submit" class="btn btn-primary" name="submit" value="Log In" style="margin-left:100px">
                                <a class='mx-5' href="">Forget Password</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>
