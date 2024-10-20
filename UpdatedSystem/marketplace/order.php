<?php
session_start();
require_once '../classes/config.php';
require_once '../classes/Order.php';
require_once '../classes/OrderDetails.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
  header("Location: ../login.php");
  exit();
}

// Ensure 'order_details' are set in the session
if (!isset($_SESSION['order_details'])) {
  header("Location: ../marketplace.php");
  exit();
}

$database = new Database();
$con = $database->getConnection();

$order = new Order($con);
$order_details = new OrderDetails($con);
$farm_id = $_SESSION['order_details'][0]['farm_id'];
$order_num = $order->generateOrderNum($con, $farm_id);



// Check if the order has already been placed
if (!isset($_SESSION['order_created'])) {
  // Extract order details from session
  $product_id = $_SESSION['order_details'][0]['product_id'];
  $farm_id = $_SESSION['order_details'][0]['farm_id'];
  $quantity = $_SESSION['order_details'][0]['quantity'];
  $unit_price = $_SESSION['order_details'][0]['product_price'];
  $total_amount = $_SESSION['order_details'][0]['total'];

  

  $status = 1;

  $first_name = $_SESSION['billing_details'][0]['first_name'];
  $last_name = $_SESSION['billing_details'][0]['last_name'];
  $email = $_SESSION['billing_details'][0]['email'];
  $phone = $_SESSION['billing_details'][0]['phone'];
  $address = $_SESSION['billing_details'][0]['address'];
  $city = $_SESSION['billing_details'][0]['city'];

  // Set order details
  $order_num = $order->generateOrderNum($con, $farm_id);
  $order->setCus_id($_SESSION['user_id']); // This should probably come from the session
  $order->setFarm_id($farm_id); // This should also come from session data or form
  $order->setProduct_id($product_id);
  $order->setQuantity($quantity);
  $order->setUnit_price($unit_price);
  $order->setTotal($total_amount);
  $order->setStatus($status);

  
  $order_details->setOrder_num($order_num);
  $order->setOrder_num($order_num);
  $order_details->setFirst_name($first_name);
  $order_details->setLast_name($last_name);
  $order_details->setEmail($email);
  $order_details->setPhone_number($phone);
  $order_details->setAddress($address);
  $order_details->setCity($city);

  $service_fee = $total_amount * 5/100;
  $order_details->create($order_num,$phone);
  $order->addOrderPayments($service_fee, $order_num);

  // Create the order in the database
  $order->create();

  // Mark the order as created in the session
  $_SESSION['order_created'] = true;

  // Redirect to order confirmation page to prevent duplicate order creation
  header("Location: order.php");
  exit();
}

$first_name = $_SESSION['billing_details'][0]['first_name'];
  $last_name = $_SESSION['billing_details'][0]['last_name'];
  $email = $_SESSION['billing_details'][0]['email'];
  $phone = $_SESSION['billing_details'][0]['phone'];
  $address = $_SESSION['billing_details'][0]['address'];
  $city = $_SESSION['billing_details'][0]['city'];



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
        <p>Your order will be processed within 24 hours during working days.</p>

        <!-- Billing Address Section -->
        <div class="border p-3 rounded">
          <h5>Billing address</h5>
          <p><strong>Name:</strong> <?php echo $first_name.' '.$last_name; ?></p>
          <p><strong>Address:</strong><?php echo $address.' '.$city; ?></p>
          <p><strong>Phone:</strong><?php echo $phone; ?></p>
          <p><strong>Email:</strong> <?php echo $email; ?></p>
          
        </div>

        <!-- Track Your Order Button -->
        <a href="#" class="btn btn-success mt-4">View My Orders</a>
      </div>

      <!-- Right Column (Order Summary) -->
      <div class="col-md-6">
        <div class="border p-4 rounded">
          <h5>Order Summary</h5>
          <div class="d-flex justify-content-between">

            <!-- There is no 'cus_id' in 'order_details', perhaps replace it with farm_id or add it explicitly -->
            <p><strong>Order Number:</strong> <?php echo $order_num; // You need to update this with actual order number 
                                              ?></p>
            
          </div>
          <hr>

          <!-- Items List -->
          <div class="d-flex justify-content-between">
            <div>
              <!-- Corrected: Use 'order_details' session variable -->
              <p><strong>Product :</strong> <?php echo $_SESSION['order_details'][0]['item_name']; ?></p>
              <p><strong>Quantity:</strong> <?php echo $_SESSION['order_details'][0]['quantity']; ?></p>
              <p><strong>Unit Price:</strong> LKR <?php echo $_SESSION['order_details'][0]['product_price']; ?></p>
            </div>
            <p class="fw-bold">Total : LKR <?php echo $_SESSION['order_details'][0]['total']; ?></p>
          </div>

          <hr>

          <!-- Price Breakdown -->
          <div class="d-flex justify-content-between">
            <p>Sub Total</p>
            <p>LKR <?php echo $_SESSION['order_details'][0]['total']; ?></p>
          </div>
          <div class="d-flex justify-content-between">
            <p>Shipping</p>
            <p>FREE</p>
          </div>
          

          <hr>

          <!-- Total Price -->
          <div class="d-flex justify-content-between fw-bold">
            <p>Order Total</p>
            <p>LKR <?php echo $_SESSION['order_details'][0]['total']; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php
unset($_SESSION['order_details']);
unset($_SESSION['order_created']);
unset($_SESSION['billing_details']);

?>