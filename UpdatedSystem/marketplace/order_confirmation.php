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
    $product_name = $_POST['items'];
    $amount = $_POST['amount'];
    $quantity = $_POST['quantity'];
    $product_id = $_POST['product_id'];
    $product_price = $_POST['product_price'];
    $farm_id = $_POST['farm_id'];
    
    if (isset($_SESSION['order_details'])) {
        unset($_SESSION['order_details']);
    }
    
    if (!isset($_SESSION['order_details'])) {
        $_SESSION['order_details'] = array();
        $_SESSION['order_details']['created_at'] = time();
    }
    
    $_SESSION['order_details'][] = array(
        'cus_id' => $_SESSION['user_id'],
        'item_name' => $product_name,
        'farm_id' => $farm_id,
        'product_id' => $product_id,
        'quantity' => $quantity,
        'product_price' => $product_price,
        'total' => $amount
    );

    $expiry_duration = 5 * 60; // 5 minutes

    if (isset($_SESSION['order_details']) && isset($_SESSION['order_details']['created_at'])) {
        $time_elapsed = time() - $_SESSION['order_details']['created_at'];

        if ($time_elapsed > $expiry_duration) {
            unset($_SESSION['order_details']);
            $_SESSION['error_message'] = "Session expired. The order details have been cleared. Please Login again and order";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="marketPlaceStyle.css" rel="stylesheet">
    <link rel="stylesheet" href="../header.css">
    <title>MarketPlace - PoultryPro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container my-5">
        <div class="text-center mb-4">
            <img class="d-block mx-auto mb-4" src="https://images.unsplash.com/photo-1523350165414-082d792c4bcc?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="" width="72" height="72">
            <h1>Checkout Form</h1>
            <p class="lead">Please fill in the details below to complete your order.</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Product Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Item Name:</strong> <?php echo htmlspecialchars($product_name); ?></p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($quantity); ?></p>
                        <p><strong>Total Amount:</strong> <?php echo htmlspecialchars($amount); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Billing Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="checkout.php">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="first_name">First Name</label>
                                    <input type="text" name="first_name" class="form-control" placeholder="First name" aria-label="First name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" placeholder="Last name" aria-label="Last name" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="email">Email (optional)</label>
                                    <input type="email" name="email" class="form-control" placeholder="you@example.com" aria-label="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone">Phone</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="077 123 4567" aria-label="phone" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" class="form-control" placeholder="1234 Main St" aria-label="Address" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="city">City</label>
                                    <input type="text" name="city" class="form-control" placeholder="City" aria-label="City" required>
                                </div>
                            </div>

                            <input type="hidden" name="country" value="">
                            <input type="hidden" name="hash" value="<?php echo htmlspecialchars($hash); ?>">

                            <div class="d-grid gap-2">
                                <input type="submit" value="Proceed with the Order" class="btn btn-success btn-lg">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showError(message) {
            if (message) {
                alert(message);
                window.location.href = "../login.php";
            }
        }

        window.onload = function() {
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "showError('" . addslashes($_SESSION['error_message']) . "');";
                unset($_SESSION['error_message']);
            }
            ?>
        };
    </script>
</body>
</html>
