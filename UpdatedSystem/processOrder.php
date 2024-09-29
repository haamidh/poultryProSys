<?php
    require_once 'marketplace/marketplaceFrame.php';
    require 'classes/config.php';
    require 'marketplace/marketPlaceCRUD.php';
    
    
    $marketPlaceFrame = new marketPlaceFrame();
    $marketPlaceFrame->navbar();
    
    $marketPlaceCRUD = new MarketPlaceCRUD();
    $product_id = $marketPlaceCRUD->getProductId();
    $row = $marketPlaceCRUD->viewProduct($product_id);
    $quantity = $_POST["quantity"];
    $total = $quantity * $row['product_price'];

    $merchant_id = 1227852;
    $merchant_secret = "NDE3MzE4NTEzODM0ODM2NDUzMjkyNzI5NzcwMjM5MjA3MDA2MjQzOQ==";
    $amount = 1000;
    $currency = "LKR";

    $order_id = 12345;
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
<!doctype HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
  <link rel="stylesheet" href="marketplace/marketPlaceStyle.css">
  <title>MarketPlace - PoultryPro</title>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <div class="container mt-5 mb-5">
    <div class="row d-flex justify-content-center">
      <div class="col-md-10">
        <div class="card">
          <div class="row">
            <div class="col-md-6">
              <div class="images p-3">
                <div class="text-center p-4"> <img src="<?php echo $row['product_img'] ?>" width="250" /> </div>
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
                  <h6 class="text">Total Price : <?php echo 'Rs. '.$total."/="; ?></h6>
                </div>
                <form action="https://sandbox.payhere.lk/pay/checkout" method="post"> <div class="form-group">
                  <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                    

                  </div>
                  
                  <div class="cart mt-4 align-items-center">
                  <input type="hidden" name="hash" value="<?php echo $hash  ?>">  
                    <button type="submit" class="btn btn-danger text-uppercase mr-2 px-4">Confirm Order</button>
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
