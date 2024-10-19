<?php
require 'classes/config.php';
require 'marketplace/marketPlaceCRUD.php';

$marketPlaceCRUD = new MarketPlaceCRUD();
$product_id = $marketPlaceCRUD->getProductId();
$row = $marketPlaceCRUD->viewProduct($product_id);
$quantity = $_POST["quantity"];
$product_price = $row['product_price'];
$farm_id = $row['farm_id'];
$total = $quantity * $product_price;
?>
<!doctype HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <link rel="stylesheet" href="marketplace/marketPlaceStyle.css">
    <link rel="stylesheet" href="header.css">
    <title>MarketPlace - PoultryPro</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-5 mb-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="images p-3">
                                <div class="image-container text-center p-4">
                                    <img src="<?php echo $row['product_img'] ?>" alt="Product Image" width="250" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="product p-4">
                                <div class="mt-4 mb-3">
                                    <span class="text-uppercase text-muted">PoultryPro</span>
                                    <h3 class="text-uppercase"><?php echo $row['product_name']; ?></h3>
                                    <div class="price d-flex flex-row align-items-center">
                                        <h6>Rs. <?php echo $row['product_price']; ?></h6>
                                    </div>
                                </div>
                                <p class="about"><?php echo $row['description']; ?></p>
                                <div class="sizes mt-5">
                                    <h6 class="text">Total Price : <?php echo 'Rs. ' . $total . "/="; ?></h6>
                                </div>
                                <form action="marketplace/order_confirmation.php" method="post">
                                    <div class="form-group">
                                        <input type="hidden" name="items" value="<?php echo $row['product_name']; ?>">
                                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                                        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <input type="hidden" name="product_price" value="<?php echo $product_price; ?>">
                                        <input type="hidden" name="farm_id" value="<?php echo $farm_id; ?>">
                                    </div>

                                    <div class="cart mt-4 align-items-center">
                                        <input type="hidden" name="hash" value="<?php echo $hash ?>">  
                                        <button type="submit" value="Buy Now" class="btn btn-danger text-uppercase mr-2 px-4">Confirm Order</button>
                                        <i class="fa fa-heart text-muted"></i>
                                        <i class="fa fa-share-alt text-muted"></i>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
