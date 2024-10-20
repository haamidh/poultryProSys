<?php
require 'classes/config.php';
require 'marketplace/marketPlaceCRUD.php';
require 'classes/Stocks.php';
require 'classes/Product.php';

$database = new Database();
$con = $database->getConnection();
$farm_id = isset($_GET["farm_id"]) ? $_GET["farm_id"] : '';
$marketPlaceCRUD = new MarketPlaceCRUD();
$product_id = $marketPlaceCRUD->getProductId();
$row = $marketPlaceCRUD->viewProduct($product_id);
$Stocks = new Stocks($con, $farm_id);
$itemStock = $Stocks->getProductAvailableQuantity($product_id);

if ($itemStock > 0) {
    $product_quantity = $product_qty_num = $itemStock;
} else {
    $product_quantity = 'OUT OF STOCK';
    $product_qty_num = 0;
}

if (!empty($row)) { // Check if product found
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
                background-color: #f7f7f7;
                font-family: 'Poppins', sans-serif;
            }

            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
                background-color: #fff;
            }

            .images img {
                width: 100%;
                max-height: 400px;
                object-fit: cover;
                border-radius: 15px;
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
                margin-top: 10px;
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

            .quantity-input {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                font-size: 1.1rem;
            }

            .quantity-btn {
                cursor: pointer;
                font-size: 1.5rem;
                padding: 5px 15px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background-color: #f1f1f1;
                transition: background-color 0.3s ease;
            }

            .quantity-btn:hover {
                background-color: #ddd;
            }

            #quantity {
                width: 60px;
                text-align: center;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .btn-custom {
                background-color: #007bff;
                color: #fff;
                font-size: 1.1rem;
                font-weight: 600;
                border-radius: 50px;
                padding: 10px 40px;
                border: none;
                transition: background-color 0.3s ease, transform 0.3s ease;
                box-shadow: 0px 8px 15px rgba(0, 123, 255, 0.2);
            }

            .btn-custom:hover {
                background-color: #0056b3;
                transform: translateY(-2px);
            }

            .btn-custom:focus {
                outline: none;
                box-shadow: 0px 0px 10px rgba(0, 123, 255, 0.4);
            }

            .cart {
                margin-top: 30px;
            }

            .product-description {
                max-width: 700px;
                margin: 0 auto;
            }

            .container {
                padding-top: 50px;
            }

            @media (max-width: 768px) {
                .images img {
                    max-height: 250px;
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
                            <div class="col-md-6 d-flex align-items-center justify-content-center">
                                <div class="images p-3">
                                    <div class="image-container text-center p-4">
                                        <img src="<?php echo !empty($row['product_img']) ? $row['product_img'] : 'default_image.jpg'; ?>" alt="Product Image">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex flex-column justify-content-center align-items-center text-center">
                                <div class="product p-4">
                                    <div class="mt-4 mb-3">
                                        <span class="text-uppercase text-muted">PoultryPro</span>
                                        <h3 class="text-uppercase"><?php echo $row['product_name']; ?></h3>
                                        <div class="price">
                                            <h6>Rs. <?php echo $row['product_price']; ?></h6>
                                        </div>
                                    </div>
                                    <p class="about"><?php echo $row['description']; ?></p>
                                    <div class="sizes mt-5">
                                        <h6 class="text">Available Stock: <?php echo $product_quantity; ?> 
                                            <?php if ($product_qty_num > 0) { ?>
                                                <?php echo $row['unit']; ?>s
                                        </h6>
                                    </div>

                                    <!-- Quantity Input -->
                                    <form action="processOrder.php?product_id=<?php echo $product_id; ?>" method="post">
                                        <div class="row">
                                            <div class="col-md-12 d-flex justify-content-center">
                                                <div class="form-group quantity-input">
                                                    <label for="quantity">Quantity:</label>
                                                    <div class="quantity-btn" onclick="decreaseQty()">-</div>
                                                    <input type="number" min="1" max="<?php echo $product_qty_num; ?>" class="form-control" id="quantity" name="quantity" required value="1">
                                                    <div class="quantity-btn" onclick="increaseQty()">+</div>
                                                    <input type="hidden" name="farm_id" value="<?php echo $row['farm_id']; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Buy Button -->
                                        <div class="cart mt-4 align-items-center">
                                            <button type="submit" class="btn btn-custom text-uppercase mr-2 px-4">Buy</button>
                                        </div>
                                    </form>
                                <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function increaseQty() {
                var qty = document.getElementById('quantity');
                if (qty.value < <?php echo $product_qty_num; ?>) {
                    qty.value++;
                }
            }

            function decreaseQty() {
                var qty = document.getElementById('quantity');
                if (qty.value > 1) {
                    qty.value--;
                }
            }
        </script>
    </body>

    </html>
<?php
} else {
    echo 'Product not found.';
}
?>
