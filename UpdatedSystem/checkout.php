<?php

require_once 'marketplace/marketplaceFrame.php';
    require 'classes/config.php';
    require 'marketplace/marketPlaceCRUD.php';
    
    
    $marketPlaceFrame = new marketPlaceFrame();
    $marketPlaceFrame->navbar();

$merchant_id = 1227852;
$merchant_secret = "OTYyNzU2MDEyMjY1MDg1NzUxMTMwMDY0OTc5MjMzNTY4NTI3NjU4";
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
<html>
<body>
<!-- <form method="post" action="https://sandbox.payhere.lk/pay/checkout">   
    <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">  
    <input type="hidden" name="return_url" value="http://sample.com/return">
    <input type="hidden" name="cancel_url" value="http://sample.com/cancel">
    <input type="hidden" name="notify_url" value="http://sample.com/notify">  
    </br></br>Item Details</br>
    <input type="text" name="order_id" value="ItemNo12345">
    <input type="text" name="items" value="Door bell wireless">
    <input type="text" name="currency" value="LKR">
    <input type="text" name="amount" value="1000">  
    </br></br>Customer Details</br>
    <input type="text" name="first_name" value="Saman">
    <input type="text" name="last_name" value="Perera">
    <input type="text" name="email" value="samanp@gmail.com">
    <input type="text" name="phone" value="0771234567">
    <input type="text" name="address" value="No.1, Galle Road">
    <input type="text" name="city" value="Colombo">
    <input type="hidden" name="country" value="Sri Lanka">
    <?php  ?>
    <input type="hidden" name="hash" value="<?php echo $hash  ?>">  
    <input type="submit" value="Buy Now">
    
    
    

</form>  -->

<form action="https://sandbox.payhere.lk/pay/checkout" method="post"> <div class="form-group">
                  <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                  <input type="hidden" name="return_url" value="http://sample.com/return">
    <input type="hidden" name="cancel_url" value="http://sample.com/cancel">
    <input type="hidden" name="notify_url" value="http://sample.com/notify">
      <input type="text" name="order_id" value="ItemNo12345">
    <input type="text" name="items" value="Door bell wireless">
    <input type="text" name="currency" value="LKR">
    <input type="text" name="amount" value="1000">
    <input type="text" name="first_name" value="Saman">
    <input type="text" name="last_name" value="Perera">
    <input type="text" name="email" value="samanp@gmail.com">
    <input type="text" name="phone" value="0771234567">
    <input type="text" name="address" value="No.1, Galle Road">
    <input type="text" name="city" value="Colombo">
    <input type="hidden" name="country" value="Sri Lanka">
                    

                  </div>
                  
                  <div class="cart mt-4 align-items-center">
                  <input type="hidden" name="hash" value="<?php echo $hash  ?>">  
                    <button type="submit" value="Buy Now" class="btn btn-danger text-uppercase mr-2 px-4">Confirm Order</button>
                    <i class="fa fa-heart text-muted"></i>
                    <i class="fa fa-share-alt text-muted"></i>
                  </div>
                </form>

</body>
</html>