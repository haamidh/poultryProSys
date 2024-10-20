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
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin-top: 30px;
        }
        .images img {
            width: 100%;
            max-width: 300px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
        }
        .product h3 {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
        }
        .product h6 {
            font-size: 1.5rem;
            color: #007bff;
            font-weight: 500;
        }
        .about {
            font-size: 1rem;
            color: #666;
            margin-top: 10px;
        }
        .sizes h6 {
            font-size: 1.1rem;
            color: #28a745;
            font-weight: 600;
        }
        .cart .btn-danger {
            background-color: #dc3545;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 40px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0px 8px 15px rgba(220, 53, 69, 0.2);
        }
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .fa-heart, .fa-share-alt {
            cursor: pointer;
            margin-left: 20px;
            transition: color 0.3s ease;
        }
        .fa-heart:hover {
            color: #e74c3c;
        }
        .fa-share-alt:hover {
            color: #007bff;
        }
        .cart {
            margin-top: 30px;
        }
        @media (max-width: 768px) {
            .images img {
                max-width: 200px;
            }
            .product h3 {
                font-size: 1.6rem;
            }
            .product h6 {
                font-size: 1.2rem;
            }
            .sizes h6 {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-5 mb-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="row">
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="images p-3">
                                <div class="image-container text-center p-4">
                                    <img src="<?php echo $row['product_img'] ?>" alt="Product Image" />
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
