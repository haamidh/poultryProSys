<?php
session_start();
require 'classes/config.php';
require 'classes/User.php';
require 'classes/Validation.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$query = "SELECT city FROM city";
$stmt = $db->prepare($query);
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mobileErr = $emailErr = $textErr = $passwordErr = $roleErr = $cityErr = $addressErr = "";
$errors = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and assign form data
    $user->username = $_POST['username'];
    $user->role = $_POST['role'];
    $user->address = $_POST['address'];
    $user->city = $_POST['city'];
    $user->mobile = $_POST['mobile'];
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    $user->status = 1;

    // Validate fields (same as your previous validation logic)
    // Validate mobile
    if (!Validation::validateMobile($user->mobile, $mobileErr)) {
        $errors = true;
    }

    // Validate email
    if (!Validation::validateEmail($user->email, $emailErr)) {
        $errors = true;
    }

    // Validate name
    if (!Validation::validateTextField($user->username, $textErr)) {
        $errors = true;
    }

    // Validate password
    if (!Validation::validatePasswordField($user->password, $passwordErr)) {
        $errors = true;
    }

    // Validate role
    if (empty($user->role)) {
        $roleErr = "*Please select your role";
        $errors = true;
    }

    // Validate city
    if (empty($user->city)) {
        $cityErr = "*Please select city";
        $errors = true;
    }

    // Validate address
    if (!Validation::validateAddressField($user->address, $addressErr)) {
        $errors = true;
    }

    if (!$errors) {
        if ($user->emailExists()) {
            ?>
            <script type="text/javascript">
                alert("<?php echo "Email already exists. Please login using your email and password."; ?>");
                window.location.href = 'login.php';
            </script>
            <?php
        } else {
            if ($user->register()) {
                $_SESSION['user_id'] = $user->user_id;
                $_SESSION['username'] = $user->username;
                $_SESSION['role'] = $user->role;
                $_SESSION['status'] = $user->status;

                switch ($user->role) {
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
                // Debugging output
                echo "<script>alert('Something went wrong.'); window.location.href = 'register.php';</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up Form</title>
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
        </style>
    </head>

    <body>

        <?php include 'includes/header.php'; ?>

        <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
            <div class="row w-100 mt-5">
                <div class="d-flex justify-content-center">
                    <div class="card col-lg-4 col-md-6 col-sm-8 col-12">
                        <div class="card-header">
                            <h4>Sign Up</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                                <div class="mb-3">
                                    <label>Name:</label>
                                    <input type="text" class="form-control" name="username" placeholder="Business name/Your name" required oninput="validateName(this)">
                                    <small id="nameError" class="text-danger"><?php echo $textErr ?></small>
                                </div>

                                <div class="mb-3">
                                    <label>Role:</label>
                                    <select name="role" class="form-control" required>
                                        <option value="" disabled selected>Select role</option>
                                        <option value="farm">Farm</option>
                                        <option value="customer">Customer</option>
                                    </select>
                                    <small class="text-danger"><?php echo $roleErr ?></small>
                                </div>

                                <div class="mb-3">
                                    <label>Address:</label>
                                    <input type="text" class="form-control" name="address" required>
                                    <small  class="text-danger"><?php echo $addressErr ?></small>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label>City:</label>
                                        <select name="city" class="form-control" required>
                                            <option value="" disabled selected>Select city</option>
                                            <?php foreach ($cities as $city) : ?>
                                                <option value="<?php echo htmlspecialchars($city['city']); ?>">
                                                    <?php echo htmlspecialchars($city['city']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small  class="text-danger"><?php echo $cityErr ?></small>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Mobile:</label>
                                        <input type="text" class="form-control" name="mobile" id="mobileInput" placeholder="0777777777" required oninput="validateMobile()">
                                        <small id="mobileError" class="text-danger"><?php echo $mobileErr ?></small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Email:</label>
                                    <input type="email" class="form-control" name="email" id="emailInput" placeholder="abc123@gmail.com" required oninput="validateEmail()">
                                    <small id="emailError" class="text-danger"><?php echo $emailErr ?></small>
                                </div>

                                <div class="mb-3">
                                    <label>Password:</label>
                                    <input type="password" class="form-control" name="password" placeholder="********" required oninput="validatePassword()">
                                    <small id="passwordError" class="text-danger"><?php echo $passwordErr ?></small>
                                </div>


                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>

                                <div class="text-center mt-3">
                                    <p>Already have an account? <a href="login.php">Login here</a></p>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

        <script src="script.js"></script>
    </body>

</html>
