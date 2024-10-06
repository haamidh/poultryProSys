<?php
require 'classes/config.php';
require 'marketplace/marketPlaceCRUD.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['items'];
    $amount = $_POST['amount'];
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
    
    <link rel="stylesheet" href="header.css">
    <title>MarketPlace - PoultryPro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="py-5 text-center">
            <img class="d-block mx-auto mb-4" src="https://images.unsplash.com/photo-1523350165414-082d792c4bcc?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="" width="72" height="72">
            <h1>Checkout Form</h1>
        </div>
    </div>

    <div class="container">
        <div class="row">

            <form method="post" action="https://sandbox.payhere.lk/pay/checkout">
                <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                <input type="hidden" name="return_url" value="http://sample.com/return">
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
                            <label for="Last name">Amount</label>
                            <input type="text" class="form-control" name="amount" value="<?php echo $amount; ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-8 order-1">
                        <h4 class="mb-3">Billing Address</h4>
                        <div class="row">
                            <div class="col mb-4">
                                <label for="First name"> First Name </label>
                                <input type="text" name="first_name" class="form-control" placeholder="First name" aria-label="First name">
                            </div>
                            <div class="col mb-4">
                                <label for="Last name"> Last Name </label>
                                <input type="text" name="last_name" class="form-control" placeholder="Last name" aria-label="Last name">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email">Email (optional)</label>
                            <input type="text" name="email" class="form-control" placeholder="you@example.com" aria-label="email">
                        </div>

                        <div class="mb-4">
                            <label for="email">Phone</label>
                            <input type="number" name="phone" class="form-control" placeholder="077 123 4567" aria-label="phone">
                        </div>

                        <div class="mb-4">
                            <label for="Address">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="1234 Main St" aria-label="Address">
                        </div>

                        <div class="mb-4">
                            <label for="Address2">City</label>
                            <input type="text" name="city" class="form-control" placeholder="Appartment or suite" aria-label="Address2">
                        </div>
                        <hr class="mb-4">

                        <input type="hidden" name="country" value=""></br>
                        <input type="hidden" name="hash" value="<?php echo $hash; ?>">


                        <div class="d-grid gap-2">

                            <input type="submit" value="Continue to Checkout" value="Buy Now" class="btn btn-primary btn-lg">

                            </form>
                        </div>

                    </div>
                </div>
        </div>


        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>

</html>