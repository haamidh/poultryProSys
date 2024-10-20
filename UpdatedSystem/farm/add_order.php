<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/config.php';
require_once '../classes/Product.php';
require '../classes/Stocks.php';
require_once '../classes/Order.php';
require_once '../classes/OrderDetails.php';

require_once 'Frame.php';
require_once '../classes/checkLogin.php';
require_once '../classes/Order.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farm') {
    header("Location: ../login.php");
    exit();
}

// Retrieve the id from the session
$user_id = $_SESSION["user_id"];

// Check login and fetch farm data
$farm = CheckLogin::checkLoginAndRole($user_id, 'farm');

$frame = new Frame();
$frame->first_part($farm);

$db = new Database();
$con = $db->getConnection();

$product = new Product($con);
$products = $product->read($user_id);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_order'])) {
        $product_id = $_POST['product_id'];
        $farm_id = $user_id;
        $quantity = number_format($_POST['quantity'], 2);
        $unit_price = $_POST['productPriceInput'];
        $total = number_format($_POST['totalAmountInput'], 2);

        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $city = $_POST['city'];

        $order = new Order($con);
        $orderDetails = new OrderDetails($con);
        $order_num = $order->generateOrderNum($con,$user_id);
        $order->setOrder_num($order_num);
        $order->setCus_id(40);
        $order->setProduct_id($product_id);
        $order->setFarm_id($farm_id);
        $order->setQuantity($quantity);
        $order->setUnit_price($unit_price);
        $order->setTotal($total);
        $order->setStatus(1);
        if($order->create()){
            $success_message = "Order Created Successfully.";
        } else {
            $error_message = "Failed to Create Order";
        }

        $orderDetails->setOrder_num($order_num);
        $orderDetails->setFirst_name($first_name);
        $orderDetails->setLast_name($last_name);
        $orderDetails->setEmail($email);
        $orderDetails->setPhone_number($phone);
        $orderDetails->setAddress($address);
        $orderDetails->setCity($city);

        if($orderDetails->create($order_num,$phone)){
            $success_message = "Order Created Successfully.";
        } else {
            $error_message = "Failed to Create Order";
        }


    }

}

?>

<html>

<head>
    <title>Add Orders</title>
</head>

<body>
    <main class="col-lg-10 col-md-9 col-sm-8 p-0 vh-100 overflow-auto">
        <div class="container">
            <div class="row my-5 text-center">

                <!-- Add New Order Section -->
                <div class="col-lg-5 col-md-10 col-12 mb-3 px-5">
                    <div class="card shadow">
                        <div class="card-header p-3 text-center" style="background-color: #356854;">
                            <h5 class="card-title text-white mb-0">
                                <strong style="font-size: 24px;">New Order</strong>
                            </h5>
                        </div>
                        <div class="card-body" style="background-color: #F5F5F5;">

                            <!-- Success and Error Messages -->
                            <?php if (isset($success_message)) : ?>
                                <div class="alert alert-success text-center">
                                    <?php echo $success_message; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($error_message)) : ?>
                                <div class="alert alert-danger text-center">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Add Order Form -->
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    <div class="mb-3">
        <label class="form-label">Select Product</label>
        <select name="product_id" id="product_id" class="form-control" required onchange="updateProductDetails()">
            <option value="" disabled selected>-- Select Product --</option>
            <?php foreach ($products as $singleProduct) {
                // Get the stock availability for each product
                $Stocks = new Stocks($con, $user_id);
                $itemStock = $Stocks->getProductAvailableQuantity($singleProduct['product_id']);
            ?>
                <option value="<?= $singleProduct['product_id'] ?>"
                    data-image="../<?= $singleProduct['product_img'] ?>"
                    data-price="<?= $singleProduct['product_price'] ?>"
                    data-stock="<?= $itemStock ?>"
                    data-unit="<?= $singleProduct['unit'] ?>">
                    <?= $singleProduct['product_name'] ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="card-body text-center" style="background-color: white;">
        <img id="productImage" src="" alt="Product Image" style="max-width: 150px; max-height: 150px;" />
    </div>

    <script>
        function updateProductDetails() {
            var selectElement = document.getElementById("product_id");
            var selectedOption = selectElement.options[selectElement.selectedIndex];

            var productImage = selectedOption.getAttribute("data-image");
            var productPrice = parseFloat(selectedOption.getAttribute("data-price"));
            var availableStock = parseInt(selectedOption.getAttribute("data-stock"));
            var stockUnit = selectedOption.getAttribute("data-unit");

            var imageElement = document.getElementById("productImage");
            imageElement.src = productImage ? productImage : "";

            var priceElement = document.getElementById("productPrice");
            priceElement.textContent = productPrice ? "Price: Rs. " + productPrice : "Price not available";

            document.getElementById("productPriceInput").value = productPrice;

            var stockElement = document.getElementById("availableStock");
            stockElement.textContent = availableStock ? "Available Stock: " + availableStock : "Stock not available";

            var stockUnitElement = document.getElementById("stockUnit");
            stockUnitElement.textContent = stockUnit ? "" + stockUnit : "";

            var quantityInput = document.getElementById("quantity");
            if (availableStock && availableStock > 0) {
                quantityInput.max = availableStock;
                quantityInput.value = Math.min(1, availableStock);
                quantityInput.disabled = false;
            } else {
                quantityInput.max = 0;
                quantityInput.value = 0;
                quantityInput.disabled = true;
            }

            quantityInput.addEventListener('input', function () {
                var quantity = parseFloat(this.value) || 0; // Allow decimal numbers
                var totalAmount = quantity * productPrice;
                document.getElementById("totalAmountInput").value = totalAmount.toFixed(2); // Set total amount
            });
        }
    </script>

    <div class="mb-3">
        <p id="productPrice" style="font-weight: bold;"></p>
        <input type="hidden" id="productPriceInput" name="productPriceInput" value="">
    </div>

    <div class="mb-3">
        <p id="availableStock" style="font-weight: bold;"></p>
        <p id="stockUnit" style="font-weight: bold;"></p>
    </div>

    <div class="mb-3 row">
        <div class="col-md-6">
            <label class="form-label">Quantity:</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" max="" placeholder="Select quantity">
        </div>
        <div class="col-md-6">
            <label class="form-label"><strong>Total:</strong></label>
            <input type="number" id="totalAmountInput" class="form-control" name="totalAmountInput" value="" readonly>
        </div>
    </div>

    <!-- New Section for Billing Details -->
    <div class="mb-3">
        <label class="form-label"><strong>Billing Details</strong></label>
        <hr>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">First Name:</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Last Name:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Phone Number:</label>
                <input type="tel" name="phone" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">Address:</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label">City:</label>
                <input type="text" name="city" class="form-control" required>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-success btn-block" name="add_order">Confirm Order</button>
        <button type="reset" class="btn btn-danger btn-block" name="reset_order">Reset Order</button>
    </div>
</form>


                        </div>
                    </div>
                </div>

               

            </div>
        </div>
    </main>

    <!-- Confirmation for Deletion -->
    <script>
        function myFunction(med_id) {
            if (confirm("Are you sure you want to delete this order?")) {
                window.location.href = "delete_medicine.php?med_id=" + med_id;
            }
        }

        function searchMedicine() {
            var input = document.getElementById("searchMedInput");
            var filter = input.value.toUpperCase();
            var table = document.querySelector(".table");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var medName = rows[i].getElementsByTagName("td")[0];
                var description = rows[i].getElementsByTagName("td")[1];

                if (medName || description) {
                    var nameValue = medName.textContent || medName.innerText;
                    var descriptionValue = description.textContent || description.innerText;

                    if (
                        nameValue.toUpperCase().indexOf(filter) > -1 ||
                        descriptionValue.toUpperCase().indexOf(filter) > -1
                    ) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

</html>