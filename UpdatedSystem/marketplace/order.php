<?php
session_start();
require_once '../classes/config.php';
require_once '../classes/Order.php';


$database = new Database();
$con = $database->getConnection();

if(!isset($_SESSION['order_details'])){
  header("Location: ../login.php");
}




$order = new Order($con);
$product_id = $_SESSION['order_details'][0]['product_id'];
$quantity = $_SESSION['order_details'][0]['quantity'];
$unit_price = $_SESSION['order_details'][0]['product_price'];
$total_amount = $_SESSION['order_details'][0]['total'];
$status = 1;

      
      $order->setCus_id(23);
      $order->setFarm_id(23);
      $order->setProduct_id($product_id);
      $order->setQuantity($quantity);
      $order->setUnit_price($unit_price);
      $order->setTotal($total_amount);
      $order->setStatus($status);
      
      $order->create();
      

      

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container my-5">
    <div class="row">
      <!-- Left Column -->
      <div class="col-md-6">
        <h1 class="mb-4">Thank you for your purchase!</h1>
        <p>Your order will be processed within 24 hours during working days. We will notify you by email once your order has been shipped.</p>
        
        <!-- Billing Address Section -->
        <div class="border p-3 rounded">
          <h5>Billing address</h5>
          <p><strong>Name:</strong> Jane Smith</p>
          <p><strong>Address:</strong> 456 Oak St #3b, San Francisco, CA 94102, United States</p>
          <p><strong>Phone:</strong> +1 (415) 555-1234</p>
          <p><strong>Email:</strong> jane.smith@email.com</p>
        </div>

        <!-- Track Your Order Button -->
        <a href="#" class="btn btn-success mt-4">Track Your Order</a>
      </div>

      <!-- Right Column (Order Summary) -->
      <div class="col-md-6">
        <div class="border p-4 rounded">
          <h5>Order Summary</h5>
          <div class="d-flex justify-content-between">
            
            <!-- Use the correct session variable 'order_details' -->
            <p><strong>Order Number:</strong> <?php echo str_pad($_SESSION['order_details'][0]['cus_id'], 9, '0', STR_PAD_LEFT); ?></p>
            <p><strong>Payment Method:</strong> Mastercard</p>
          </div>
          <hr>

          <!-- Items List -->
          <div class="d-flex justify-content-between">
            <div>
              <!-- Corrected: Use 'order_details' session variable -->
              <p><strong>Product ID:</strong> <?php echo $_SESSION['order_details'][0]['product_id']; ?></p>
              <p>Quantity: <?php echo $_SESSION['order_details'][0]['quantity']; ?><br>Price per Unit: $<?php echo $_SESSION['order_details'][0]['product_price']; ?></p>
            </div>
            <p class="fw-bold">$<?php echo $_SESSION['order_details'][0]['total']; ?></p>
          </div>

          <hr>
          
          <!-- Price Breakdown -->
          <div class="d-flex justify-content-between">
            <p>Sub Total</p>
            <p>$<?php echo $_SESSION['order_details'][0]['total']; ?></p>
          </div>
          <div class="d-flex justify-content-between">
            <p>Shipping</p>
            <p>$2.00</p>
          </div>
          <div class="d-flex justify-content-between">
            <p>Tax</p>
            <p>$5.00</p>
          </div>
          
          <hr>
          
          <!-- Total Price -->
          <div class="d-flex justify-content-between fw-bold">
            <p>Order Total</p>
            <p>$<?php echo $_SESSION['order_details'][0]['total'] + 2 + 5; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
