<?php
session_start();
require '../classes/config.php';
require 'marketPlaceCRUD.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please Login before purchasing";
}

if ($_SESSION['role'] !== 'customer') {
    $_SESSION['error_message'] = "Please Login as a customer before purchasing";
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

    $expiry_duration = 5 * 60; // 5 minutes

    if (isset($_SESSION['billing_details']) && isset($_SESSION['billing_details']['created_at'])) {
        $time_elapsed = time() - $_SESSION['billing_details']['created_at'];
        if ($time_elapsed > $expiry_duration) {
            unset($_SESSION['billing_details']);
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../header.css">
    <link rel="stylesheet" href="../marketplacestyle.css">
    <title>MarketPlace - PoultryPro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 15px;
        }
        .card-body {
            padding: 20px;
        }
        h1, h4 {
            font-weight: 600;
        }
        .btn-success {
            background-color: #28a745;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .btn-danger {
            background-color: #dc3545;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
        }
        .form-control[readonly] {
            background-color: #f8f8f8;
        }
        @media (max-width: 768px) {
            .container {
                margin-top: 30px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="https://images.unsplash.com/photo-1523350165414-082d792c4bcc?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="" width="72" height="72">
            <h1 class="text-uppercase">Confirm Order</h1>
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

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Product Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="item_name">Item Name</label>
                                    <input type="text" class="form-control" name="items" value="<?php echo $product_name ?>" readonly>
                                </div>
                                <div class="col mb-3">
                                    <label for="quantity">Quantity</label>
                                    <input type="text" class="form-control" name="quantity" value="<?php echo $quantity; ?>" readonly>
                                </div>
                                <div class="col mb-3">
                                    <label for="amount">Total Amount</label>
                                    <input type="text" class="form-control" name="amount" value="<?php echo $amount; ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">Billing Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="first_name">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo $first_name ?>" readonly>
                                </div>
                                <div class="col mb-3">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo $last_name ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input type="text" name="email" class="form-control" value="<?php echo $email ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="phone">Phone</label>
                                <input type="number" name="phone" class="form-control" value="<?php echo $phone ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="address">Address</label>
                                <input type="text" name="address" class="form-control" value="<?php echo $address ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="city">City</label>
                                <input type="text" name="city" class="form-control" value="<?php echo $city ?>" readonly>
                            </div>

                            <input type="hidden" name="country" value="">
                            <input type="hidden" name="hash" value="<?php echo $hash; ?>">

                            <div class="d-grid gap-2">
                                <input type="submit" value="Proceed to Checkout" class="btn btn-success btn-lg"> 
                                <a href="../marketplace.php" class="btn btn-danger btn-lg">Cancel Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showError(message) {
            if (message) {
                alert(message);
                window.location.href = "login.php";
            }
        }

        window.onload = function() {
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "showError('" . $_SESSION['error_message'] . "');";
                unset($_SESSION['error_message']);
            }
            ?>
        };
    </script>
</body>
</html>
