<?php
session_start();
require 'config.php';
require 'User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user->username = $_POST['username'];
    $user->role = $_POST['role'];
    $user->address = $_POST['address'];
    $user->city = $_POST['city'];
    $user->mobile = $_POST['mobile'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    $user->role = $_POST['role'];

    if ($user->emailExists()) {
        ?>
        <script type="text/javascript">
            alert("<?php echo "Email already exists.Please login using mail and password"; ?>");
            window.location.href = 'login.php';
        </script>
        <?php
    } else {
        if ($user->register()) {
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;

            switch ($user->role) {
                case "farm":
                    
                    header("Location: farm_dashboard.php?user_id=" . $_SESSION['user_id']);

                    exit();
                case "customer":
                    header("Location: customer.php");
                    exit();
                default:

                    header("Location: login.php");
                    exit();
            }
        } else {
            ?>
            <script type="text/javascript">
                alert("<?php echo "Something Gone wrong."; ?>");
                window.location.href = 'regiter.php';
            </script>
            <?php
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign in form</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <style>
            .navbar-nav .nav-item a {
                font-weight: bold;
            }
        </style>
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
                    <div class="justify-content-center mt-2" style="color:white;">Already you have an account?</div>
                    <li class="nav-item align-items-center mx-4" style="background-color:#8A9A5B;">
                        <a class="nav-link" href="login.php">Log In</a>
                    </li>


                </ul>
            </div>
        </nav> 

        <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
            <div class="row">
                <div class="col-md-4">
                    <div class="card" style="width:450px;">
                        <div class="card-header" style="background-color: #40826D;color: white;">
                            <h4>Sign Up</h4>
                        </div>
                        <div class="card-body"> 
                            <form id="batchForm" action="" method="post">

                                <div class="row">
                                    <label>Name:</label>
                                    <input type="text" class="form-control" name="username" placeholder="Business name/Your name" required>
                                </div>

                                <div class="row">
                                    <label>Role:</label>
                                    <select name="role" required>
                                        <option value="" disabled selected>Select role</option>
                                        <option value="farm">Farm</option>
                                        <option value="customer">Customer</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <label>Address:</label>
                                    <input type="text" class="form-control" name="address" required>
                                </div>

                                <div class="row">
                                    <label>City:</label>
                                    <select name="city" required>
                                        <option value="" disabled selected>Select city</option>
                                        <option value="hambantota">Hambantota</option>
                                        <option value="matara">Matara</option>
                                        <option value="galle">Galle</option>
                                        <option value="aluthgama">Aluthgama</option>
                                        <option value="colombo">Colombo</option>
                                        <option value="gampaha">Gampaha</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <label>Mobile:</label>
                                    <input type="text" class="form-control" name="mobile" required>
                                </div>

                                <div class="row">
                                    <label>Email:</label>
                                    <input type="email" class="form-control" name="email" placeholder="abc123@gmail.com" required>
                                </div>

                                <div class="row">
                                    <label>Password:</label>
                                    <input type="text" class="form-control" name="password" required>
                                </div>

                                <br>
                                <input type="submit" class="btn btn-primary" name="submit" value="Sign Up" style="margin-left: 150px">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>




