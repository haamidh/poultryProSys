<?php
session_start();
require '../classes/config.php';
require 'marketPlaceCRUD.php';

if (!isset($_SESSION['user_id'])) {
    // User is not logged in
    $_SESSION['error_message'] = "Please Login before purchasing";
    // header("Location: login.php");
    // exit();
}

// Check if the user is logged in but their role is not 'customer'
if ($_SESSION['role'] !== 'customer') {
    // If the user is logged in but not a customer, show the 'Login as customer' message
    $_SESSION['error_message'] = "Please Login as a customer before purchasing";
    // header("Location: login.php");
    // exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$amount = $_SESSION['order_details'][0]['total'];
$product_name = $_SESSION['order_details'][0]['item_name'];
$quantity = $_SESSION['order_details'][0]['quantity'];

if (isset($_SESSION['order_details'])) {
    unset($_SESSION['billing_details']);
}

if (!isset($_SESSION['order_details'])) {
    $_SESSION['order_details'] = array();
    $_SESSION['order_details']['created_at'] = time();
}

$_SESSION['billing_details'][] = array(
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    'city' => $city
);

// Set the expiry duration (in seconds)
$expiry_duration = 5*60;  // 5 minutes

// Check if 'order_details' session array exists and has a 'created_at' key
if (isset($_SESSION['billing_details']) && isset($_SESSION['billing_details']['created_at'])) {
    // Calculate the time elapsed since the session array was created
    $time_elapsed = time() - $_SESSION['billing_details']['created_at'];

    // If the time elapsed exceeds the expiry duration, clear the session data
    if ($time_elapsed > $expiry_duration) {
        unset($_SESSION['billing_details']);  // delete the session array
        
    }
}

}

$merchant_id = 1227852;
$merchant_secret = "OTYyNzU2MDEyMjY1MDg1NzUxMTMwMDY0OTc5MjMzNTY4NTI3NjU4";

$currency = "LKR";

$order_id = 1227852;
$hash = strtoupper(
    md5(
        $merchant_id .
            $order_id .
            number_format($amount, 2, '.', '') .
            $currency .
            strtoupper(md5($merchant_secret))
    )
);
?>

<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">

<link rel="stylesheet" href="../header.css">
<title>MarketPlace - PoultryPro</title>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="https://images.unsplash.com/photo-1523350165414-082d792c4bcc?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="" width="72" height="72">
            <h1>Confirm Order</h1>
        </div>
    </div>

    <div class="container">
        <div class="row">

            <form method="post" action="https://sandbox.payhere.lk/pay/checkout">
                <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                <input type="hidden" name="return_url" value="http://localhost/poultryProsys/poultryProSys/UpdatedSystem/marketplace/order.php">
                <input type="hidden" name="cancel_url" value="http://sample.com/cancel">
                <input type="hidden" name="notify_url" value="http://sample.com/notify">
                <input type="hidden" name="currency" value="<?php echo $currency; ?>">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

                <div class="col-md-8 order-1">
                    <h4 class="mb-3">Item Details</h4>
                    <div class="row">
                        <div class="col mb-4">
                            <label for="First name">Item Name</label>
                            <input type="text" class="form-control" name="items" value="<?php echo $product_name ?>" readonly>
                        </div>
                        <div class="col mb-4">
                            <label for="Quantity">Quantity</label>
                            <input type="text" class="form-control" name="quantity" value="<?php echo $quantity; ?>" readonly>
                        </div>
                        <div class="col mb-4">
                            <label for="Last name">Total Amount</label>
                            <input type="text" class="form-control" name="amount" value="<?php echo $amount; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-8 order-1">
                        <h4 class="mb-3">Billing Address</h4>
                        <div class="row">
                            <div class="col mb-4">
                                <label for="First name"> First Name </label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo $first_name ?>" readonly  aria-label="First name">
                            </div>
                            <div class="col mb-4">
                                <label for="Last name"> Last Name </label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo $last_name ?>" readonly  aria-label="Last name">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email">Email</label>
                            <input type="text" name="email" class="form-control" value="<?php echo $email ?>" readonly  aria-label="email">
                        </div>

                        <div class="mb-4">
                            <label for="email">Phone</label>
                            <input type="number" name="phone" class="form-control" value="<?php echo $phone ?>" readonly  aria-label="phone">
                        </div>

                        <div class="mb-4">
                            <label for="Address">Address</label>
                            <input type="text" name="address" class="form-control" value="<?php echo $address ?>" readonly aria-label="Address">
                        </div>

                        <div class="mb-4">
                            <label for="Address2">City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo $city ?>" readonly  aria-label="Address2">
                        </div>
                        <hr class="mb-4">

                        <input type="hidden" name="country" value=""></br>
                        <input type="hidden" name="hash" value="<?php echo $hash; ?>">


                        <div class="d-grid gap-2">

                            <input type="submit" value="Proceed to Checkout" class="btn btn-primary btn-lg"> 

            </form>
            <div class="d-grid gap-2">
                            <a href="../marketplace.php" class="btn btn-danger btn-lg">Cancel Order</a>
                            
                        </div>
        </div>

    </div>
    </div>
    </div>


    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
    <script>
        // Function to display an error message as an alert and redirect to the login page
        function showError(message) {
            if (message) {
                alert(message);
                window.location.href = "login.php"; // Redirect to the login page after clicking OK
            }
        }

        // Wait for the page to fully load before showing the alert
        window.onload = function() {
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "showError('" . $_SESSION['error_message'] . "');";
                unset($_SESSION['error_message']); // Clear the error message after displaying it
            }
            ?>
        };
    </script>
</body>

</html>